@extends('layouts.app')
@section('title', $order->po_number)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
    <div>
        <h1 class="h3 mb-1">{{ $order->po_number }}
            <span class="badge text-bg-{{ $order->status->badge() }} fs-6 align-middle">{{ $order->status->value }}</span>
        </h1>
        <div class="text-muted">{{ $order->supplier->name }} · ordered {{ $order->ordered_at->format('d M Y') }}</div>
    </div>
    <div class="d-flex gap-2">
        @foreach ($order->status->allowedTransitions() as $next)
            <form method="POST" action="{{ route('purchase-orders.status', $order) }}"
                  @if ($next === \App\Enums\PurchaseOrderStatus::Cancelled) onsubmit="return confirm('Cancel this order? This cannot be undone.')" @endif>
                @csrf @method('PATCH')
                <input type="hidden" name="status" value="{{ $next->value }}">
                <button class="btn btn-{{ $next === \App\Enums\PurchaseOrderStatus::Cancelled ? 'outline-danger' : 'success' }}">
                    {{ $next->actionLabel() }}
                </button>
            </form>
        @endforeach
    </div>
</div>

@if ($order->status->isFinal())
    <div class="alert alert-secondary">This order is {{ strtolower($order->status->value) }} and can no longer be changed.</div>
@endif

<div class="panel">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
            <tr><th>SKU</th><th>Product</th><th class="num">Quantity</th><th class="num">Unit price</th><th class="num">Subtotal</th></tr>
            </thead>
            <tbody>
            @foreach ($order->items as $item)
                <tr>
                    <td>{{ $item->product->sku }}</td>
                    <td>{{ $item->product->name }}</td>
                    <td class="num">{{ number_format($item->quantity) }}</td>
                    <td class="num">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="num">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr><td colspan="4" class="num fw-semibold">Total</td><td class="num fw-bold">{{ number_format($order->total_amount, 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
</div>
<a href="{{ route('purchase-orders.index') }}" class="btn btn-link mt-2">Back to orders</a>
@endsection