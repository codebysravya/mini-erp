<?php

namespace App\Http\Controllers;

use App\Enums\PurchaseOrderStatus;
use App\Models\Product;
use App\Models\PurchaseOrder;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // One aggregate query for both product metrics (served by the is_active, stock_quantity index).
        $stock = Product::query()
            ->where('is_active', true)
            ->selectRaw('COUNT(*) as total, COALESCE(SUM(CASE WHEN stock_quantity < ? THEN 1 ELSE 0 END), 0) as low', [Product::lowStockThreshold()])
            ->first();

        $expenditure = PurchaseOrder::query()
            ->where('status', PurchaseOrderStatus::Received->value)
            ->sum('total_amount');

        $latestOrders = PurchaseOrder::query()
            ->with('supplier:id,name')   // eager load: no N+1
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('dashboard', [
            'totalProducts' => (int) $stock->total,
            'lowStockCount' => (int) $stock->low,
            'expenditure' => $expenditure,
            'latestOrders' => $latestOrders,
        ]);
    }
}