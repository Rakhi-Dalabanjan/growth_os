<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GrowthOS — AI Social Media Operating System">

    <title>{{ $title ?? 'Welcome' }} — {{ config('app.name', 'GrowthOS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Vite Assets -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            padding: 2rem;
            text-align: center;
        }

        .auth-logo {
            width: 48px;
            height: 48px;
            background: rgba(255,255,255,0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.75rem;
        }

        .auth-brand {
            font-size: 1.5rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        .auth-tagline {
            font-size: 0.78rem;
            color: rgba(255,255,255,0.7);
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        .auth-body {
            padding: 2rem;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border-color: #e2e8f0;
            font-size: 0.875rem;
            padding: 0.6rem 0.875rem;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #374151;
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
        }
        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        .auth-link {
            color: #2563eb;
            font-size: 0.85rem;
            text-decoration: none;
            font-weight: 500;
        }
        .auth-link:hover {
            color: #1d4ed8;
            text-decoration: underline;
        }

        .invalid-feedback {
            font-size: 0.78rem;
        }
    </style>
</head>
<body>
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="bi bi-broadcast text-white" style="font-size:1.4rem;"></i>
            </div>
            <div class="auth-brand">GrowthOS</div>
            <div class="auth-tagline">AI Social Media OS</div>
        </div>
        <div class="auth-body">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
