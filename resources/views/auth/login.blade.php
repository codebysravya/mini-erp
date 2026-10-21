<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · Stockroom</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body{background:#f4f5f2;font-family:"Segoe UI",system-ui,sans-serif}.card{max-width:400px;border-color:#dde2da}</style>
</head>
<body class="d-flex align-items-center min-vh-100">
<div class="card mx-auto w-100 shadow-sm">
    <div class="card-body p-4">
        <h1 class="h4 mb-1">Stockroom</h1>
        <p class="text-muted mb-4">Sign in to manage inventory and purchase orders.</p>
        <form method="POST" action="{{ url('/login') }}">
            @csrf
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       class="form-control @error('email') is-invalid @enderror">
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" required class="form-control">
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Keep me signed in</label>
            </div>
            <button class="btn btn-success w-100">Sign in</button>
        </form>
    </div>
</div>
</body>
</html>