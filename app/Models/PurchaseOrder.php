<?php

namespace App\Models;

use App\Enums\PurchaseOrderStatus;
use App\Exceptions\InvalidStatusTransitionException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number', 'supplier_id', 'created_by', 'status', 'total_amount',
        'ordered_at', 'approved_at', 'received_at', 'cancelled_at',
    ];

    protected $casts = [
        'status' => PurchaseOrderStatus::class,
        'total_amount' => 'decimal:2',
        'ordered_at' => 'datetime',
        'approved_at' => 'datetime',
        'received_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Model-level guard: a RECEIVED / CANCELLED order can never be saved again.
        static::updating(function (PurchaseOrder $order) {
            $original = $order->getOriginal('status');
            $original = $original instanceof PurchaseOrderStatus
                ? $original
                : PurchaseOrderStatus::tryFrom((string) $original);

            if ($original?->isFinal()) {
                throw InvalidStatusTransitionException::immutable($original);
            }
        });

        static::deleting(function (PurchaseOrder $order) {
            if ($order->status->isFinal()) {
                throw InvalidStatusTransitionException::immutable($order->status);
            }
        });
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}