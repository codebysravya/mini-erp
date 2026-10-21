@extends('layouts.app')
@section('title', 'New purchase order')

@section('content')
<h1 class="h3 mb-3">New purchase order</h1>

@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <strong>Fix these before saving:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('purchase-orders.store') }}" id="po-form" novalidate>
    @csrf
    <div class="panel p-3 mb-3">
        <label for="supplier_id" class="form-label">Supplier</label>
        <select id="supplier_id" name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
            <option value="">Choose a supplier</option>
            @foreach ($suppliers as $supplier)
                <option value="{{ $supplier->id }}" @selected(old('supplier_id') == $supplier->id)>{{ $supplier->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="panel mb-3">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h2 class="h5 mb-0">Line items</h2>
            <button type="button" class="btn btn-outline-success btn-sm" id="add-row">Add item</button>
        </div>
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                <tr><th style="min-width:240px">Product</th><th style="width:130px">Quantity</th><th style="width:160px">Unit price</th><th class="num" style="width:140px">Subtotal</th><th style="width:60px"></th></tr>
                </thead>
                <tbody id="rows"></tbody>
                <tfoot>
                <tr><td colspan="3" class="num fw-semibold">Total</td><td class="num fw-bold" id="grand-total">0.00</td><td></td></tr>
                </tfoot>
            </table>
        </div>
    </div>

    <button class="btn btn-success">Save as draft</button>
    <a href="{{ route('purchase-orders.index') }}" class="btn btn-link">Cancel</a>
</form>

<template id="row-template">
    <tr class="line">
        <td>
            <select class="form-select product" required>
                <option value="">Choose a product</option>
                @foreach ($products as $p)
                    <option value="{{ $p->id }}" data-cost="{{ $p->unit_cost }}">{{ $p->sku }} · {{ $p->name }}</option>
                @endforeach
            </select>
        </td>
        <td><input type="number" min="1" step="1" class="form-control qty" value="1" required></td>
        <td><input type="number" min="0" step="0.01" class="form-control price" value="0.00" required></td>
        <td class="num subtotal">0.00</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger remove" aria-label="Remove line">&times;</button></td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
(function () {
    const rows = document.getElementById('rows');
    const tpl = document.getElementById('row-template');
    const totalEl = document.getElementById('grand-total');
    const old = @json(old('items', []));
    let index = 0;

    const cents = (v) => Math.round((parseFloat(v) || 0) * 100);
    const fmt = (c) => (c / 100).toFixed(2);

    function recalc() {
        let total = 0;
        rows.querySelectorAll('tr.line').forEach((tr) => {
            const q = Math.max(0, parseInt(tr.querySelector('.qty').value, 10) || 0);
            const sub = q * cents(tr.querySelector('.price').value);
            tr.querySelector('.subtotal').textContent = fmt(sub);
            total += sub;
        });
        totalEl.textContent = fmt(total);
    }

    function addRow(data) {
        const tr = tpl.content.firstElementChild.cloneNode(true);
        const i = index++;
        const product = tr.querySelector('.product'), qty = tr.querySelector('.qty'), price = tr.querySelector('.price');
        product.name = `items[${i}][product_id]`;
        qty.name = `items[${i}][quantity]`;
        price.name = `items[${i}][unit_price]`;
        if (data) { product.value = data.product_id ?? ''; qty.value = data.quantity ?? 1; price.value = data.unit_price ?? '0.00'; }

        product.addEventListener('change', () => {
            const cost = product.selectedOptions[0]?.dataset.cost;
            if (cost) price.value = parseFloat(cost).toFixed(2);   // prefill with catalogue cost, still editable
            recalc();
        });
        [qty, price].forEach((el) => el.addEventListener('input', recalc));
        tr.querySelector('.remove').addEventListener('click', () => {
            if (rows.querySelectorAll('tr.line').length > 1) { tr.remove(); recalc(); }
        });
        rows.appendChild(tr);
        recalc();
    }

    document.getElementById('add-row').addEventListener('click', () => addRow());
    (Array.isArray(old) ? old : Object.values(old)).forEach(addRow);
    if (!rows.children.length) addRow();
})();
</script>
@endpush