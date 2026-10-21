<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        return ProductResource::collection(
            Product::active()->orderBy('name')->paginate($this->perPage($request))
        );
    }

    public function lowStock(Request $request)
    {
        return ProductResource::collection(
            Product::active()->lowStock()->orderBy('stock_quantity')->paginate($this->perPage($request))
        )->additional(['threshold' => Product::lowStockThreshold()]);
    }

    private function perPage(Request $request): int
    {
        return max(1, min((int) $request->query('per_page', 15), 100));
    }
}