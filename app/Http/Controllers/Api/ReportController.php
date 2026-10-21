<?php

namespace App\Http\Controllers\Api;

use App\Enums\PurchaseOrderStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function supplierSpend()
    {
        // Single grouped query, no per-supplier lookups. Uses the
        // (status, supplier_id, total_amount) index on purchase_orders.
        $rows = DB::table('purchase_orders as po')
            ->join('suppliers as s', 's.id', '=', 'po.supplier_id')
            ->where('po.status', PurchaseOrderStatus::Received->value)
            ->groupBy('s.id', 's.name')
            ->orderByDesc('total_spend')
            ->selectRaw('s.id as supplier_id, s.name as supplier_name, COUNT(po.id) as orders_count, SUM(po.total_amount) as total_spend')
            ->get()
            ->map(fn ($r) => [
                'supplier_id' => (int) $r->supplier_id,
                'supplier_name' => $r->supplier_name,
                'orders_count' => (int) $r->orders_count,
                'total_spend' => (float) $r->total_spend,
            ]);

        return response()->json(['data' => $rows]);
    }
}