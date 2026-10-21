@extends('layouts.app')
@section('title', 'Inventory')

@section('content')
<h1 class="h3 mb-3">Inventory</h1>
<div class="panel">
    <div class="table-responsive">
        <table class="table mb-0 align-middle">
            <thead class="table-light">
            <tr><th>SKU</th><th>Name</th><th class="num">In stock</th><th class="num">Unit cost</th><th>Flag</th></tr>
            </thead>
            <tbody>
            @foreach ($products as $product)
                <tr class="{{ $product->isLowStock() ? 'row-low' : '' }}">
                    <td>{{ $product->sku }}</td>
                    <td>{{ $product->name }}</td>
                    <td class="num fw-semibold">{{ number_format($product->stock_quantity) }}</td>
                    <td class="num">{{ number_format($product->unit_cost, 2) }}</td>
                    <td>
                        @if ($product->isLowStock())
                            <span class="badge text-bg-warning">Low stock</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $products->links('pagination::bootstrap-5') }}</div>
@endsection