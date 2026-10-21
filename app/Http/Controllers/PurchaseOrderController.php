<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderStatusRequest;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;

class PurchaseOrderController extends Controller
{
    public function __construct(private PurchaseOrderService $orders) {}

    public function index()
    {
        return view('purchase-orders.index', [
            'orders' => PurchaseOrder::query()
                ->with('supplier:id,name')
                ->withCount('items')
                ->latest('created_at')
                ->paginate(15),
        ]);
    }

    public function create()
    {
        return view('purchase-orders.create', [
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'products' => Product::active()->orderBy('name')->get(['id', 'sku', 'name', 'unit_cost']),
        ]);
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        $order = $this->orders->create($request->validated(), $request->user()->id);

        return redirect()->route('purchase-orders.show', $order)
            ->with('success', "Order {$order->po_number} saved as draft.");
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        return view('purchase-orders.show', [
            'order' => $purchaseOrder->load(['supplier', 'items.product']),
        ]);
    }

    public function updateStatus(UpdatePurchaseOrderStatusRequest $request, PurchaseOrder $purchaseOrder)
    {
        $order = $this->orders->transition($purchaseOrder, $request->targetStatus());

        return redirect()->route('purchase-orders.show', $order)
            ->with('success', "Order is now {$order->status->value}.");
    }
}