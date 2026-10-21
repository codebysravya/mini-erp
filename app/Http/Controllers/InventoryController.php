<?php

namespace App\Http\Controllers;

use App\Models\Product;

class InventoryController extends Controller
{
    public function index()
    {
        return view('inventory.index', [
            'products' => Product::query()->orderBy('name')->paginate(15),
            'threshold' => Product::lowStockThreshold(),
        ]);
    }
}