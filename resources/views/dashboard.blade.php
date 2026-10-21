@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<style>
    .dash {
        --d-ink: #1c2530;
        --d-muted: #6b7684;
        --d-line: #e3e7ec;
        --d-surface: #ffffff;
        --d-soft: #f4f6f8;
        --d-green: #0f766e;
        --d-green-soft: #e3f3f1;
        --d-amber: #b45309;
        --d-amber-soft: #fdf1dc;
        color: var(--d-ink);
    }

    /* Header */
    .dash-head { display: flex; flex-wrap: wrap; gap: 1rem; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
    .dash-head h1 { font-size: 1.65rem; font-weight: 700; letter-spacing: -.02em; margin: 0; }
    .dash-head p { margin: .25rem 0 0; color: var(--d-muted); font-size: .95rem; }
    .dash-cta {
        display: inline-flex; align-items: center; gap: .5rem;
        background: var(--d-green); color: #fff; font-weight: 600;
        padding: .6rem 1.1rem; border-radius: .6rem; text-decoration: none;
        transition: background-color .15s ease;
    }
    .dash-cta:hover { background: #0b5e57; color: #fff; }
    .dash-cta:focus-visible, .dash-link:focus-visible, .dash-po:focus-visible { outline: 3px solid #7cc4bc; outline-offset: 2px; }
    .dash-cta svg { width: 18px; height: 18px; }

    /* Stat strip: one panel, three sections */
    .dash-stats { display: grid; grid-template-columns: repeat(3, 1fr); background: var(--d-surface); border: 1px solid var(--d-line); border-radius: .9rem; overflow: hidden; margin-bottom: 1.5rem; }
    .dash-stat { display: flex; gap: 1rem; align-items: flex-start; padding: 1.35rem 1.5rem; position: relative; }
    .dash-stat + .dash-stat { border-left: 1px solid var(--d-line); }
    .dash-stat-icon { flex: 0 0 auto; width: 44px; height: 44px; border-radius: .7rem; display: grid; place-items: center; background: var(--d-green-soft); color: var(--d-green); }
    .dash-stat-icon svg { width: 22px; height: 22px; }
    .dash-stat-label { color: var(--d-muted); font-size: .9rem; margin-bottom: .15rem; }
    .dash-stat-value { font-size: 2rem; font-weight: 700; line-height: 1.15; letter-spacing: -.02em; font-variant-numeric: tabular-nums; }
    .dash-stat-note { font-size: .82rem; margin-top: .35rem; color: var(--d-muted); }

    /* Low stock state */
    .dash-stat.is-warn { background: var(--d-amber-soft); }
    .dash-stat.is-warn::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: var(--d-amber); }
    .dash-stat.is-warn .dash-stat-icon { background: #fff; color: var(--d-amber); }
    .dash-stat.is-warn .dash-stat-value, .dash-stat.is-warn .dash-stat-note { color: var(--d-amber); }
    .dash-stat.is-warn .dash-stat-note { font-weight: 600; }

    /* Orders panel */
    .dash-panel { background: var(--d-surface); border: 1px solid var(--d-line); border-radius: .9rem; overflow: hidden; }
    .dash-panel-head { display: flex; justify-content: space-between; align-items: center; padding: 1.1rem 1.5rem; border-bottom: 1px solid var(--d-line); }
    .dash-panel-head h2 { font-size: 1.1rem; font-weight: 700; margin: 0; }
    .dash-link { color: var(--d-green); font-weight: 600; font-size: .9rem; text-decoration: none; }
    .dash-link:hover { text-decoration: underline; }

    .dash-table { width: 100%; margin: 0; border-collapse: collapse; }
    .dash-table th { background: var(--d-soft); color: var(--d-muted); font-weight: 600; font-size: .85rem; padding: .7rem 1.5rem; text-align: left; white-space: nowrap; }
    .dash-table td { padding: .9rem 1.5rem; border-top: 1px solid var(--d-line); vertical-align: middle; }
    .dash-table tbody tr:hover { background: #fafbfc; }
    .dash-table .num { text-align: right; font-variant-numeric: tabular-nums; font-weight: 600; }
    .dash-po { font-weight: 700; color: var(--d-ink); text-decoration: none; font-variant-numeric: tabular-nums; }
    .dash-po:hover { color: var(--d-green); text-decoration: underline; }

    .dash-supplier { display: flex; align-items: center; gap: .65rem; }
    .dash-avatar { width: 32px; height: 32px; border-radius: 50%; background: var(--d-green-soft); color: var(--d-green); font-weight: 700; font-size: .85rem; display: grid; place-items: center; flex: 0 0 auto; }

    .dash-status { font-weight: 600; padding: .38em .8em; }

    /* Empty state */
    .dash-empty { text-align: center; padding: 3rem 1.5rem; }
    .dash-empty-icon { width: 56px; height: 56px; margin: 0 auto 1rem; border-radius: 50%; background: var(--d-soft); color: var(--d-muted); display: grid; place-items: center; }
    .dash-empty-icon svg { width: 28px; height: 28px; }
    .dash-empty h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: .35rem; }
    .dash-empty p { color: var(--d-muted); margin-bottom: 1.1rem; }

    /* Mobile */
    @media (max-width: 767.98px) {
        .dash-stats { grid-template-columns: 1fr; }
        .dash-stat + .dash-stat { border-left: 0; border-top: 1px solid var(--d-line); }
        .dash-table th, .dash-table td, .dash-panel-head { padding-left: 1rem; padding-right: 1rem; }
    }
</style>

<div class="dash">

    {{-- Header --}}
    <div class="dash-head">
        <div>
            <h1>Dashboard</h1>
            <p>{{ now()->format('l, d F Y') }}. Here is where your stock and purchasing stand today.</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="dash-cta">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
            New purchase order
        </a>
    </div>

    {{-- Key numbers --}}
    <div class="dash-stats">
        <div class="dash-stat">
            <div class="dash-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>
            </div>
            <div>
                <div class="dash-stat-label">Active products</div>
                <div class="dash-stat-value">{{ number_format($totalProducts) }}</div>
                <div class="dash-stat-note">Items currently on sale</div>
            </div>
        </div>

        <div class="dash-stat {{ $lowStockCount > 0 ? 'is-warn' : '' }}">
            <div class="dash-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l10 18H2L12 3z"/><path d="M12 10v5M12 18v.01"/></svg>
            </div>
            <div>
                <div class="dash-stat-label">Low stock (under {{ \App\Models\Product::lowStockThreshold() }} units)</div>
                <div class="dash-stat-value">{{ number_format($lowStockCount) }}</div>
                <div class="dash-stat-note">
                    {{ $lowStockCount > 0 ? 'Needs restocking. Create a purchase order.' : 'Every product is well stocked' }}
                </div>
            </div>
        </div>

        <div class="dash-stat">
            <div class="dash-stat-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="13" rx="2"/><path d="M2 10h20"/><path d="M6 15h4"/></svg>
            </div>
            <div>
                <div class="dash-stat-label">Spent on received orders</div>
                <div class="dash-stat-value">{{ number_format($expenditure, 2) }}</div>
                <div class="dash-stat-note">Only orders marked as received</div>
            </div>
        </div>
    </div>

    {{-- Latest purchase orders --}}
    <div class="dash-panel">
        <div class="dash-panel-head">
            <h2>Latest purchase orders</h2>
            <a href="{{ route('purchase-orders.index') }}" class="dash-link">View all orders</a>
        </div>

        @if ($latestOrders->isNotEmpty())
            <div class="table-responsive">
                <table class="dash-table">
                    <thead>
                    <tr>
                        <th>PO number</th>
                        <th>Supplier</th>
                        <th>Status</th>
                        <th class="num">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($latestOrders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('purchase-orders.show', $order) }}" class="dash-po">{{ $order->po_number }}</a>
                            </td>
                            <td>
                                <div class="dash-supplier">
                                    <span class="dash-avatar">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($order->supplier->name, 0, 1)) }}</span>
                                    <span>{{ $order->supplier->name }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill dash-status text-bg-{{ $order->status->badge() }}">
                                    {{ ucwords(str_replace('_', ' ', $order->status->value)) }}
                                </span>
                            </td>
                            <td class="num">{{ number_format($order->total_amount, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="dash-empty">
                <div class="dash-empty-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6"/><path d="M9 13h8M9 17h5"/></svg>
                </div>
                <h3>No purchase orders yet</h3>
                <p>Create your first order to start restocking.</p>
                <a href="{{ route('purchase-orders.create') }}" class="dash-cta">New purchase order</a>
            </div>
        @endif
    </div>

</div>
@endsection
