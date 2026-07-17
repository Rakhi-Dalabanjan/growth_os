<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="GrowthOS — AI Social Media Operating System. Manage your social presence with intelligence.">

    <title>{{ $title ?? 'Dashboard' }} — {{ config('app.name', 'GrowthOS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Vite Assets -->
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])

    <style>
        :root {
            --sidebar-width: 260px;
            --topbar-height: 60px;
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --sidebar-bg: #0f172a;
            --sidebar-text: #94a3b8;
            --sidebar-hover: #1e293b;
            --sidebar-active: #2563eb;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f1f5f9;
            color: #1e293b;
        }

        /* ── Sidebar ── */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        #sidebar .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid #1e293b;
            text-decoration: none;
        }

        #sidebar .sidebar-brand .brand-name {
            font-size: 1.3rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.5px;
        }

        #sidebar .sidebar-brand .brand-tagline {
            font-size: 0.7rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        #sidebar .nav-section-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 1.25rem 1.5rem 0.5rem;
        }

        #sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 1.5rem;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.2s ease;
        }

        #sidebar .nav-link:hover {
            background: var(--sidebar-hover);
            color: #e2e8f0;
            border-left-color: #334155;
        }

        #sidebar .nav-link.active {
            background: rgba(37, 99, 235, 0.15);
            color: #60a5fa;
            border-left-color: var(--sidebar-active);
        }

        #sidebar .nav-link .nav-icon {
            font-size: 1rem;
            width: 1.2rem;
            text-align: center;
            flex-shrink: 0;
        }

        #sidebar .nav-link .nav-badge {
            margin-left: auto;
        }

        #sidebar .sidebar-footer {
            margin-top: auto;
            border-top: 1px solid #1e293b;
            padding: 1rem 1.5rem;
        }

        /* ── Main Content ── */
        #main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin 0.3s ease;
        }

        /* ── Topbar ── */
        #topbar {
            height: var(--topbar-height);
            background: #ffffff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 1020;
            gap: 1rem;
        }

        #topbar .page-title {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }

        #topbar .topbar-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        #topbar .user-avatar {
            width: 34px;
            height: 34px;
            background: var(--primary);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* ── Page Content ── */
        .page-content {
            flex: 1;
            padding: 1.75rem;
        }

        /* ── Footer ── */
        #app-footer {
            background: #ffffff;
            border-top: 1px solid #e2e8f0;
            padding: 0.75rem 1.75rem;
            font-size: 0.78rem;
            color: #94a3b8;
        }

        /* ── Sidebar Toggle (mobile) ── */
        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.show {
                transform: translateX(0);
            }
            #main-content {
                margin-left: 0;
            }
            .sidebar-overlay {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1029;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }

        /* ── Cards ── */
        .stat-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        /* ── Alerts ── */
        .alert {
            border-radius: 10px;
            border: none;
        }

        /* ── Buttons ── */
        .btn {
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
        }

        /* ── Forms ── */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #e2e8f0;
            font-size: 0.875rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #374151;
        }

        /* ── Card ── */
        .card {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            font-weight: 600;
            padding: 1rem 1.25rem;
        }

        /* ── Breadcrumb ── */
        .breadcrumb {
            font-size: 0.8rem;
        }

        /* ── Badge ── */
        .badge {
            border-radius: 6px;
            font-weight: 500;
        }
    </style>

    {{ $styles ?? '' }}
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ══ SIDEBAR ══ -->
<nav id="sidebar">
    <!-- Brand -->
    <a class="sidebar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
        <div style="width:36px;height:36px;background:linear-gradient(135deg,#2563eb,#7c3aed);border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
            <i class="bi bi-broadcast text-white" style="font-size:1.1rem;"></i>
        </div>
        <div>
            <div class="brand-name">GrowthOS</div>
            <div class="brand-tagline">AI Social OS</div>
        </div>
    </a>

    <!-- Main Navigation -->
    <div class="mt-2">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill nav-icon"></i>
            Dashboard
        </a>

        <a href="{{ route('organization.index') }}"
           class="nav-link {{ request()->routeIs('organization.*') ? 'active' : '' }}">
            <i class="bi bi-building nav-icon"></i>
            Organization
        </a>

        <a href="{{ route('brand-profile') }}"
           class="nav-link {{ request()->routeIs('brand-profile') || request()->routeIs('brand-profile.*') ? 'active' : '' }}">
            <i class="bi bi-person-badge nav-icon"></i>
            Brand Profile
        </a>

        <a href="{{ route('brand-intelligence') }}"
           class="nav-link {{ request()->routeIs('brand-intelligence') || request()->routeIs('brand-intelligence.*') ? 'active' : '' }}">
            <i class="bi bi-brain nav-icon"></i>
            Brand Intelligence
        </a>

        <a href="{{ route('marketing-strategy') }}"
           class="nav-link {{ request()->routeIs('marketing-strategy') || request()->routeIs('marketing-strategy.*') ? 'active' : '' }}">
            <i class="bi bi-rocket-takeoff nav-icon"></i>
            Marketing Strategy
        </a>

        <a href="{{ route('social-accounts') }}"
           class="nav-link {{ request()->routeIs('social-accounts') ? 'active' : '' }}">
            <i class="bi bi-share nav-icon"></i>
            Social Accounts
        </a>

        <div class="nav-section-label">Content</div>

        <a href="{{ route('content-calendar') }}"
           class="nav-link {{ request()->routeIs('content-calendar') ? 'active' : '' }}">
            <i class="bi bi-calendar3 nav-icon"></i>
            Content Calendar
            <span class="nav-badge badge bg-secondary" style="font-size:0.6rem;">Soon</span>
        </a>

        <a href="{{ route('ai-studio') }}"
           class="nav-link {{ request()->routeIs('ai-studio') ? 'active' : '' }}">
            <i class="bi bi-stars nav-icon"></i>
            AI Studio
            <span class="nav-badge badge bg-secondary" style="font-size:0.6rem;">Soon</span>
        </a>

        <a href="{{ route('ai-gateway.index') }}"
           class="nav-link {{ request()->routeIs('ai-gateway.*') ? 'active' : '' }}">
            <i class="bi bi-shield-check nav-icon"></i>
            AI Gateway
        </a>

        <a href="{{ route('assets') }}"
           class="nav-link {{ request()->routeIs('assets') ? 'active' : '' }}">
            <i class="bi bi-folder2-open nav-icon"></i>
            Assets
            <span class="nav-badge badge bg-secondary" style="font-size:0.6rem;">Soon</span>
        </a>

        <div class="nav-section-label">Insights</div>

        <a href="{{ route('analytics') }}"
           class="nav-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line nav-icon"></i>
            Analytics
            <span class="nav-badge badge bg-secondary" style="font-size:0.6rem;">Soon</span>
        </a>

        <div class="nav-section-label">Account</div>

        <a href="{{ route('ai-service.index') }}"
           class="nav-link {{ request()->routeIs('ai-service.*') ? 'active' : '' }}">
            <i class="bi bi-cpu nav-icon"></i>
            AI Service
        </a>

        <a href="{{ route('settings.index') }}"
           class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear nav-icon"></i>
            Settings
        </a>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="user-avatar" style="width:32px;height:32px;font-size:0.75rem;background:#1e40af;">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div style="overflow:hidden;">
                <div style="font-size:0.8rem;font-weight:600;color:#e2e8f0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth()->user()->name }}
                </div>
                <div style="font-size:0.7rem;color:#64748b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                    {{ auth()->user()->email }}
                </div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-sm w-100 d-flex align-items-center gap-2"
                    style="background:#1e293b;color:#94a3b8;border:1px solid #334155;">
                <i class="bi bi-box-arrow-left"></i>
                Logout
            </button>
        </form>
    </div>
</nav>

<!-- ══ MAIN CONTENT ══ -->
<div id="main-content">

    <!-- Topbar -->
    <div id="topbar">
        <button class="btn btn-sm d-lg-none" id="sidebarToggle"
                style="background:none;border:none;color:#64748b;font-size:1.2rem;padding:0.25rem;">
            <i class="bi bi-list"></i>
        </button>

        <h1 class="page-title d-none d-lg-block">{{ $title ?? 'Dashboard' }}</h1>

        <div class="topbar-right">
            <!-- Notification bell placeholder -->
            <button class="btn btn-sm position-relative" style="background:none;border:none;color:#64748b;">
                <i class="bi bi-bell" style="font-size:1.1rem;"></i>
            </button>

            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-sm d-flex align-items-center gap-2 dropdown-toggle"
                        style="background:none;border:none;padding:0;"
                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <span class="d-none d-md-inline" style="font-size:0.875rem;font-weight:500;color:#374151;">
                        {{ auth()->user()->name }}
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="border-radius:10px;border-color:#e2e8f0;min-width:180px;">
                    <li><h6 class="dropdown-header" style="font-size:0.75rem;">{{ auth()->user()->email }}</h6></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2" href="{{ route('settings.index') }}">
                            <i class="bi bi-gear" style="color:#64748b;"></i> Settings
                        </a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2">
                                <i class="bi bi-box-arrow-left" style="color:#64748b;"></i> Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <main class="page-content">

        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                {{ session('success') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                {{ session('warning') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i>
                {{ session('error') }}
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer id="app-footer">
        <div class="d-flex align-items-center justify-content-between">
            <span>&copy; {{ date('Y') }} <strong>GrowthOS</strong> — AI Social Media Operating System</span>
            <span>v1.0.0 &middot; Day 1</span>
        </div>
    </footer>

</div>

<script>
    // Sidebar Toggle for Mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
        });
    }
</script>

{{ $scripts ?? '' }}
</body>
</html>
