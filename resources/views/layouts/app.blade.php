<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Stockroom') · Stockroom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f4f5f2; color: #1f2a24; font-family: "Segoe UI", system-ui, -apple-system, sans-serif; }
        .topbar { background: #1f3a2f; }
        .topbar .navbar-brand { font-weight: 700; letter-spacing: .2px; }
        .topbar .nav-link { color: #cfe0d6; }
        .topbar .nav-link.active, .topbar .nav-link:hover { color: #fff; }
        .metric { background: #fff; border: 1px solid #dde2da; border-radius: 6px; padding: 1.1rem 1.25rem; height: 100%; }
        .metric .label { color: #5d6b62; font-size: .9rem; }
        .metric .value { font-size: 1.9rem; font-weight: 700; line-height: 1.2; }
        .metric.warn { border-left: 4px solid #c2410c; }
        .panel { background: #fff; border: 1px solid #dde2da; border-radius: 6px; }
        .table > :not(caption) > * > * { padding: .7rem .9rem; }
        .row-low { background: #fff4ec; }
        .num { text-align: right; font-variant-numeric: tabular-nums; }
        :focus-visible { outline: 3px solid #2f7d5b; outline-offset: 2px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-md topbar navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Stockroom</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('purchase-orders.*') ? 'active' : '' }}" href="{{ route('purchase-orders.index') }}">Purchase orders</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('inventory.*') ? 'active' : '' }}" href="{{ route('inventory.index') }}">Inventory</a></li>
            </ul>
            <form method="POST" action="{{ route('logout') }}" class="d-flex align-items-center gap-3">
                @csrf
                <span class="text-white-50 small">{{ auth()->user()->name }}</span>
                <button class="btn btn-sm btn-outline-light">Sign out</button>
            </form>
        </div>
    </div>
</nav>

<main class="container py-4">
    @if (session('success'))
        <div class="alert alert-success" role="status">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>