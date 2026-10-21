<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Product */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'unit_cost' => (float) $this->unit_cost,
            'stock_quantity' => $this->stock_quantity,
            'is_low_stock' => $this->isLowStock(),
        ];
    }
}