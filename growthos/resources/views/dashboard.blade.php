<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- Page Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="font-size:1.4rem;color:#1e293b;">
                Welcome back, {{ auth()->user()->name }} 👋
            </h2>
            <p class="mb-0" style="color:#64748b;font-size:0.875rem;">
                Here's an overview of your GrowthOS workspace.
            </p>
        </div>
        <div class="d-none d-md-block">
            <span class="badge" style="background:#eff6ff;color:#2563eb;font-size:0.78rem;border-radius:8px;padding:0.5rem 0.9rem;">
                <i class="bi bi-calendar3 me-1"></i>
                {{ now()->format('l, F j, Y') }}
            </span>
        </div>
    </div>

    <!-- ── Stat Cards Row 1 ── -->
    <div class="row g-3 mb-4">

        <!-- Organization Card -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card h-100" style="border-radius:12px;">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start justify-content-between">
                        <div>
                            <p class="mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;">Organization</p>
                            @if($organization)
                                <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.1rem;">{{ $organization->name }}</h4>
                                <span class="badge {{ $organization->status === 'active' ? 'bg-success' : 'bg-secondary' }}" style="font-size:0.72rem;">
                                    {{ ucfirst($organization->status) }}
                                </span>
                            @else
                                <h4 class="fw-bold mb-1" style="color:#94a3b8;font-size:1rem;">Not Set Up</h4>
                                <a href="{{ route('organization.create') }}" class="btn btn-sm btn-primary mt-1" style="font-size:0.78rem;">
                                    <i class="bi bi-plus-lg me-1"></i> Setup
                                </a>
                            @endif
                        </div>
                        <div style="width:46px;height:46px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-building" style="color:#2563eb;font-size:1.3rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand Profile Card -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card h-100" style="border-radius:12px;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;">Brand Profile</p>
                            @if($brandProfile)
                                <h4 class="fw-bold mb-1" style="color:#1e293b;font-size:1.1rem;">{{ $brandProfile->brand_name }}</h4>
                                <div style="font-size:0.8rem;color:#64748b;" class="mb-1">
                                    <strong>Market:</strong> {{ $brandProfile->primary_market ?: '—' }}<br>
                                    <strong>Tone:</strong> {{ $brandProfile->brand_tone ?: '—' }}<br>
                                    <strong>Lang:</strong> {{ $brandProfile->language ?: '—' }}
                                </div>
                            @else
                                <h4 class="fw-bold mb-1" style="color:#94a3b8;font-size:1rem;">Not Configured</h4>
                            @endif
                        </div>
                        <div style="width:46px;height:46px;background:#fef3c7;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-person-badge" style="color:#d97706;font-size:1.3rem;"></i>
                        </div>
                    </div>
                    
                    @if($brandProfile)
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.75rem;color:#64748b;">
                                <span>Completion: <strong>{{ $brandCompletion['percentage'] }}%</strong></span>
                                <span class="badge bg-{{ $brandCompletion['color'] }}">{{ $brandCompletion['status'] }}</span>
                            </div>
                            <div class="progress mb-3" style="height:6px;border-radius:10px;">
                                <div class="progress-bar bg-{{ $brandCompletion['color'] }}" style="width:{{ $brandCompletion['percentage'] }}%;border-radius:10px;"></div>
                            </div>
                            <a href="{{ route('brand-profile.edit', $brandProfile) }}" class="btn btn-sm btn-outline-primary w-100" style="font-size:0.78rem;">
                                <i class="bi bi-pencil-fill me-1"></i> Edit Brand Profile
                            </a>
                        </div>
                    @else
                        <div class="mt-auto">
                            <a href="{{ route('brand-profile.create') }}" class="btn btn-sm btn-primary w-100" style="font-size:0.78rem;">
                                <i class="bi bi-plus-lg me-1"></i> Create Brand Profile
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Social Accounts Card -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card h-100" style="border-radius:12px;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;">Social Accounts</p>
                            @if($socialAccountsCount > 0)
                                <h4 class="fw-bold mb-1" style="color:#16a34a;font-size:1.1rem;">Connected</h4>
                                <div style="font-size:0.8rem;color:#64748b;" class="mb-1">
                                    <strong>Active Connections:</strong> {{ $socialAccountsCount }} {{ Str::plural('Account', $socialAccountsCount) }}
                                </div>
                            @else
                                <h4 class="fw-bold mb-1" style="color:#64748b;font-size:1.1rem;">Not Connected</h4>
                                <div style="font-size:0.8rem;color:#94a3b8;" class="mb-1">
                                    No active social integrations.
                                </div>
                            @endif
                        </div>
                        <div style="width:46px;height:46px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-share" style="color:#2563eb;font-size:1.3rem;"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <a href="{{ route('social-accounts') }}" class="btn btn-sm btn-outline-primary w-100" style="font-size:0.78rem;">
                            <i class="bi bi-gear-fill me-1"></i> Manage Connections
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Brand Intelligence Card -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card h-100" style="border-radius:12px;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;">Brand Intelligence</p>
                            @if($brandIntelligence)
                                <h4 class="fw-bold mb-1" style="color:#16a34a;font-size:1.1rem;">Generated</h4>
                                <div style="font-size:0.8rem;color:#64748b;" class="mb-1">
                                    <strong>Last Run:</strong> {{ $brandIntelligence->generated_at->format('M j, Y H:i') }}<br>
                                    <strong>Model:</strong> {{ $brandIntelligence->model }}<br>
                                    <strong>Score:</strong> {{ $brandIntelligence->confidence_score }}%
                                </div>
                            @else
                                <h4 class="fw-bold mb-1" style="color:#e2e8f0;background:#64748b;padding:0.1rem 0.5rem;border-radius:6px;font-size:0.8rem;display:inline-block;">Not Generated</h4>
                                <div style="font-size:0.8rem;color:#94a3b8;" class="mt-2 mb-1">
                                    Analyze your brand to get insights.
                                </div>
                            @endif
                        </div>
                        <div style="width:46px;height:46px;background:#f5f3ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-brain" style="color:#7c3aed;font-size:1.3rem;"></i>
                        </div>
                    </div>
                    
                    @if($brandIntelligence)
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.75rem;color:#64748b;">
                                <span>Confidence: <strong>{{ $brandIntelligence->confidence_score }}%</strong></span>
                            </div>
                            <div class="progress mb-3" style="height:6px;border-radius:10px;">
                                <div class="progress-bar bg-success" style="width:{{ $brandIntelligence->confidence_score }}%;border-radius:10px;"></div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('brand-intelligence') }}" class="btn btn-sm btn-outline-primary flex-grow-1" style="font-size:0.78rem;">
                                    <i class="bi bi-eye-fill"></i> View
                                </a>
                                <form action="{{ route('brand-intelligence.analyze') }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="font-size:0.78rem;">
                                        <i class="bi bi-arrow-clockwise"></i> Analyze
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="mt-auto">
                            @if($brandProfile)
                                <form action="{{ route('brand-intelligence.analyze') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="font-size:0.78rem;">
                                        <i class="bi bi-magic"></i> Analyze Brand
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('brand-profile.create') }}" class="btn btn-sm btn-outline-secondary w-100" style="font-size:0.78rem;">
                                    Create Brand Profile first
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Marketing Strategy Card -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card h-100" style="border-radius:12px;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;">Marketing Strategy</p>
                            @if($marketingStrategy)
                                <h4 class="fw-bold mb-1" style="color:#2563eb;font-size:1.1rem;">Generated</h4>
                                <div style="font-size:0.8rem;color:#64748b;" class="mb-1">
                                    <strong>Last Run:</strong> {{ $marketingStrategy->generated_at->format('M j, Y H:i') }}<br>
                                    <strong>Model:</strong> {{ $marketingStrategy->model }}<br>
                                    <strong>Score:</strong> {{ $marketingStrategy->confidence_score }}%
                                </div>
                            @else
                                <h4 class="fw-bold mb-1" style="color:#e2e8f0;background:#64748b;padding:0.1rem 0.5rem;border-radius:6px;font-size:0.8rem;display:inline-block;">Not Generated</h4>
                                <div style="font-size:0.8rem;color:#94a3b8;" class="mt-2 mb-1">
                                    Generate strategy based on Brand Intelligence.
                                </div>
                            @endif
                        </div>
                        <div style="width:46px;height:46px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-rocket-takeoff" style="color:#2563eb;font-size:1.3rem;"></i>
                        </div>
                    </div>
                    
                    @if($marketingStrategy)
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.75rem;color:#64748b;">
                                <span>Confidence: <strong>{{ $marketingStrategy->confidence_score }}%</strong></span>
                            </div>
                            <div class="progress mb-3" style="height:6px;border-radius:10px;">
                                <div class="progress-bar bg-primary" style="width:{{ $marketingStrategy->confidence_score }}%;border-radius:10px;"></div>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('marketing-strategy') }}" class="btn btn-sm btn-outline-primary flex-grow-1" style="font-size:0.78rem;">
                                    <i class="bi bi-eye-fill"></i> View
                                </a>
                                <form action="{{ route('marketing-strategy.generate') }}" method="POST" class="flex-grow-1">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="font-size:0.78rem;">
                                        <i class="bi bi-arrow-clockwise"></i> Analyze
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="mt-auto">
                            @if($brandIntelligence)
                                <form action="{{ route('marketing-strategy.generate') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary w-100" style="font-size:0.78rem;">
                                        <i class="bi bi-magic"></i> Generate Strategy
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('brand-intelligence') }}" class="btn btn-sm btn-outline-secondary w-100" style="font-size:0.78rem;">
                                    Analyze Brand first
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- AI Service Card -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card h-100" style="border-radius:12px;">
                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <p class="mb-1" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;">AI Service</p>
                            @if(($aiStatus['status'] ?? 'offline') === 'online')
                                <h4 class="fw-bold mb-1" style="color:#16a34a;font-size:1.1rem;">Online</h4>
                                <div style="font-size:0.8rem;color:#64748b;" class="mb-1">
                                    <strong>Version:</strong> {{ $aiStatus['version'] ?? '1.0.0' }}<br>
                                    <strong>Latency:</strong> {{ $aiStatus['latency'] ?? '0' }} ms<br>
                                    <strong>Uptime:</strong> {{ number_format(($aiStatus['uptime'] ?? 0), 1) }}s
                                </div>
                            @elseif(($aiStatus['status'] ?? 'offline') === 'error')
                                <h4 class="fw-bold mb-1" style="color:#d97706;font-size:1.1rem;">Error</h4>
                                <div style="font-size:0.8rem;color:#94a3b8;" class="mb-1">
                                    Service configuration error.
                                </div>
                            @else
                                <h4 class="fw-bold mb-1" style="color:#dc2626;font-size:1.1rem;">Offline</h4>
                                <div style="font-size:0.8rem;color:#94a3b8;" class="mb-1">
                                    Service connection offline.
                                </div>
                            @endif
                        </div>
                        <div style="width:46px;height:46px;background:#f5f3ff;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-cpu" style="color:#7c3aed;font-size:1.3rem;"></i>
                        </div>
                    </div>
                    <div class="mt-auto">
                        <div class="mb-3">
                            @if(($aiStatus['status'] ?? 'offline') === 'online')
                                <span class="badge bg-success" style="font-size:0.72rem;">Connected</span>
                            @elseif(($aiStatus['status'] ?? 'offline') === 'error')
                                <span class="badge bg-warning" style="font-size:0.72rem;">Response Fault</span>
                            @else
                                <span class="badge bg-danger" style="font-size:0.72rem;">Disconnected</span>
                            @endif
                        </div>
                        <a href="{{ route('ai-service.index') }}" class="btn btn-sm btn-outline-primary w-100" style="font-size:0.78rem;">
                            <i class="bi bi-terminal me-1"></i> Test Connection
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Card -->
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card stat-card h-100" style="border-radius:12px;">
                <div class="card-body p-4">
                    <p class="mb-2" style="font-size:0.78rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;">Quick Actions</p>
                    <div class="d-flex flex-column gap-2">
                        @if(!$organization)
                            <a href="{{ route('organization.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-2">
                                <i class="bi bi-building"></i> Setup Organization
                            </a>
                        @else
                            <a href="{{ route('organization.show', $organization) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
                                <i class="bi bi-building"></i> View Organization
                            </a>
                        @endif
                        <a href="{{ route('settings.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
                            <i class="bi bi-gear"></i> Account Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Recent Activity ── -->
    <div class="card" style="border-radius:12px;">
        <div class="card-header d-flex align-items-center justify-content-between">
            <span class="fw-semibold" style="font-size:0.9rem;">
                <i class="bi bi-activity me-2 text-primary"></i>Recent Activity
            </span>
            <span class="badge" style="background:#eff6ff;color:#2563eb;font-size:0.72rem;">Last 30 days</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="font-size:0.875rem;">
                    <thead>
                        <tr style="background:#f8fafc;">
                            <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Event</th>
                            <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Details</th>
                            <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Date</th>
                            <th class="px-4 py-3" style="font-weight:600;font-size:0.78rem;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;border-bottom:1px solid #e2e8f0;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:32px;height:32px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                        <i class="bi bi-person-plus" style="color:#2563eb;font-size:0.85rem;"></i>
                                    </div>
                                    Account Created
                                </div>
                            </td>
                            <td class="px-4 py-3" style="color:#64748b;">New user registration</td>
                            <td class="px-4 py-3" style="color:#64748b;">{{ auth()->user()->created_at->diffForHumans() }}</td>
                            <td class="px-4 py-3">
                                <span class="badge" style="background:#f0fdf4;color:#16a34a;font-size:0.72rem;">Completed</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-4 py-3" colspan="4" style="color:#94a3b8;text-align:center;padding:2rem;">
                                <i class="bi bi-inbox" style="font-size:1.5rem;display:block;margin-bottom:0.5rem;color:#cbd5e1;"></i>
                                No more activity yet. Start by setting up your organization!
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
