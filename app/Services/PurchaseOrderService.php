<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Exceptions\InvalidStatusTransitionException;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Support\OrderCalculator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderService
{
    /**
     * @param  array{supplier_id:int, items:array<int, array{product_id:int, quantity:int, unit_price:float|int|string}>}  $data
     */
    public function create(array $data, ?int $userId = null): PurchaseOrder
    {
        return DB::transaction(function () use ($data, $userId) {
            $lines = collect($data['items']);

            $order = PurchaseOrder::create([
                'po_number' => 'PO-'.now()->format('Ymd').'-'.strtoupper(Str::random(6)),
                'supplier_id' => $data['supplier_id'],
                'created_by' => $userId,
                'status' => PurchaseOrderStatus::Draft,
                'total_amount' => OrderCalculator::grandTotal($lines),
                'ordered_at' => now(),
            ]);

            $order->items()->createMany($lines->map(fn (array $line) => [
                'product_id' => $line['product_id'],
                'quantity' => $line['quantity'],
                'unit_price' => $line['unit_price'],
                'subtotal' => OrderCalculator::lineSubtotal((int) $line['quantity'], $line['unit_price']),
            ])->all());

            return $order->load(['supplier', 'items.product']);
        });
    }

    /**
     * Moves an order along its lifecycle. When the target is RECEIVED, stock is
     * increased in the SAME transaction.
     *
     * Race safety:
     *  - the order row is locked (SELECT ... FOR UPDATE), so two concurrent
     *    "receive" calls serialise and the second sees RECEIVED and is rejected
     *    -> stock can never be added twice;
     *  - product rows are locked in id order (no deadlocks between orders that
     *    share products) and incremented with an atomic SQL expression.
     */
    public function transition(PurchaseOrder $order, PurchaseOrderStatus $target): PurchaseOrder
    {
        return DB::transaction(function () use ($order, $target) {
            $locked = PurchaseOrder::query()->whereKey($order->getKey())->lockForUpdate()->firstOrFail();

            if (! $locked->status->canTransitionTo($target)) {
                throw $locked->status->isFinal()
                    ? InvalidStatusTransitionException::immutable($locked->status)
                    : InvalidStatusTransitionException::between($locked->status, $target);
            }

            if ($target === PurchaseOrderStatus::Received) {
                $this->receiveStock($locked);
            }

            $locked->status = $target;
            match ($target) {
                PurchaseOrderStatus::Approved => $locked->approved_at = now(),
                PurchaseOrderStatus::Received => $locked->received_at = now(),
                PurchaseOrderStatus::Cancelled => $locked->cancelled_at = now(),
                default => null,
            };
            $locked->save();

            return $locked->load(['supplier', 'items.product']);
        });
    }

    private function receiveStock(PurchaseOrder $order): void
    {
        $items = $order->items()->get(['product_id', 'quantity']);

        $products = Product::query()
            ->whereIn('id', $items->pluck('product_id'))
            ->orderBy('id')
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        foreach ($items->sortBy('product_id') as $item) {
            $products[$item->product_id]->increaseStock($item->quantity);
        }
    }
}