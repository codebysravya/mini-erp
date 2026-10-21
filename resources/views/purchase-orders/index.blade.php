@extends('layouts.app')
@section('title', 'Purchase orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Purchase orders</h1>
    <a href="{{ route('purchase-orders.create') }}" class="btn btn-success">New purchase order</a>
</div>
<div class="panel">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
            <tr><th>PO number</th><th>Supplier</th><th>Date</th><th class="num">Lines</th><th>Status</th><th class="num">Total</th></tr>
            </thead>
            <tbody>
            @forelse ($orders as $order)
                <tr>
                    <td><a href="{{ route('purchase-orders.show', $order) }}">{{ $order->po_number }}</a></td>
                    <td>{{ $order->supplier->name }}</td>
                    <td>{{ $order->ordered_at->format('d M Y') }}</td>
                    <td class="num">{{ $order->items_count }}</td>
                    <td><span class="badge text-bg-{{ $order->status->badge() }}">{{ $order->status->value }}</span></td>
                    <td class="num">{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">No purchase orders yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $orders->links('pagination::bootstrap-5') }}</div>
@endsection