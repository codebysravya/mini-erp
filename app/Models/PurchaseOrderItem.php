<?php

namespace App\Models;

use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id', 'product_id', 'quantity', 'unit_price', 'subtotal'];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        // Line items freeze together with their parent order.
        $guard = function (PurchaseOrderItem $item) {
            $status = $item->purchaseOrder->status;
            if ($status->isFinal()) {
                throw InvalidStatusTransitionException::immutable($status);
            }
        };

        static::updating($guard);
        static::deleting($guard);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}