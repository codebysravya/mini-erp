@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Dashboard</h1>
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-success">New purchase order</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="metric">
            <div class="label">Active products</div>
            <div class="value">{{ number_format($totalProducts) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric {{ $lowStockCount > 0 ? 'warn' : '' }}">
            <div class="label">Low stock (under {{ \App\Models\Product::lowStockThreshold() }} units)</div>
            <div class="value">{{ number_format($lowStockCount) }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="metric">
            <div class="label">Spent on received orders</div>
            <div class="value">{{ number_format($expenditure, 2) }}</div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
        <h2 class="h5 mb-0">Latest purchase orders</h2>
        <a href="{{ route('purchase-orders.index') }}" class="small">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
            <tr><th>PO number</th><th>Supplier</th><th>Status</th><th class="num">Total</th></tr>
            </thead>
            <tbody>
            @forelse ($latestOrders as $order)
                <tr>
                    <td><a href="{{ route('purchase-orders.show', $order) }}">{{ $order->po_number }}</a></td>
                    <td>{{ $order->supplier->name }}</td>
                    <td><span class="badge text-bg-{{ $order->status->badge() }}">{{ $order->status->value }}</span></td>
                    <td class="num">{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">No purchase orders yet. Create the first one to start restocking.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection