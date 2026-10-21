<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePurchaseOrderRequest;
use App\Http\Requests\UpdatePurchaseOrderStatusRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseOrder;
use App\Services\PurchaseOrderService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(private PurchaseOrderService $orders) {}

    public function index(Request $request)
    {
        return PurchaseOrderResource::collection(
            PurchaseOrder::query()
                ->with('supplier:id,name')
                ->latest('created_at')
                ->paginate(max(1, min((int) $request->query('per_page', 15), 100)))
        );
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        return new PurchaseOrderResource($purchaseOrder->load(['supplier', 'items.product']));
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        $order = $this->orders->create($request->validated(), $request->user()->id);

        return (new PurchaseOrderResource($order))->response()->setStatusCode(201);
    }

    public function updateStatus(UpdatePurchaseOrderStatusRequest $request, PurchaseOrder $purchaseOrder)
    {
        $order = $this->orders->transition($purchaseOrder, $request->targetStatus());

        return new PurchaseOrderResource($order);
    }
}