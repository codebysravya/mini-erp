<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'name', 'unit_cost', 'stock_quantity', 'is_active'];

    protected $casts = [
        'unit_cost' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_active' => 'boolean',
    ];

    public static function lowStockThreshold(): int
    {
        return (int) config('inventory.low_stock_threshold', 10);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('stock_quantity', '<', self::lowStockThreshold());
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity < self::lowStockThreshold();
    }

    /**
     * Atomic increment (UPDATE ... SET stock_quantity = stock_quantity + n),
     * so concurrent receipts never overwrite each other.
     */
    public function increaseStock(int $quantity): void
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Stock increment must be a positive integer.');
        }

        $this->newQuery()->whereKey($this->getKey())->increment('stock_quantity', $quantity);

        $this->stock_quantity += $quantity;
        $this->syncOriginalAttribute('stock_quantity');
    }
}