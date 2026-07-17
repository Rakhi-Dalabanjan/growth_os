<x-app-layout>
    <x-slot name="title">Organization</x-slot>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Organization</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Left: Details card -->
        <div class="col-12 col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:36px;height:36px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-building" style="color:#2563eb;"></i>
                        </div>
                        <div>
                            <div class="fw-semibold" style="font-size:0.95rem;">{{ $organization->name }}</div>
                            <div style="font-size:0.78rem;color:#94a3b8;">Organization Details</div>
                        </div>
                    </div>
                    <a href="{{ route('organization.edit', $organization) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Company Name</div>
                                <div style="font-weight:500;color:#1e293b;">{{ $organization->name }}</div>
                            </div>
                            <div class="mb-3">
                                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Industry</div>
                                <div style="font-weight:500;color:#1e293b;">{{ $organization->industry ?: '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Website</div>
                                @if($organization->website)
                                    <a href="{{ $organization->website }}" target="_blank" class="text-primary text-decoration-none" style="font-size:0.875rem;">
                                        {{ $organization->website }} <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.75rem;"></i>
                                    </a>
                                @else
                                    <div style="color:#94a3b8;">—</div>
                                @endif
                            </div>
                            <div>
                                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Status</div>
                                <span class="badge {{ $organization->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($organization->status) }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Business Email</div>
                                <div style="font-weight:500;color:#1e293b;">{{ $organization->business_email ?: '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Phone</div>
                                <div style="font-weight:500;color:#1e293b;">{{ $organization->phone ?: '—' }}</div>
                            </div>
                            <div class="mb-3">
                                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Country</div>
                                <div style="font-weight:500;color:#1e293b;">{{ $organization->country ?: '—' }}</div>
                            </div>
                            <div>
                                <div style="font-size:0.75rem;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:#94a3b8;margin-bottom:0.3rem;">Timezone</div>
                                <div style="font-weight:500;color:#1e293b;">{{ $organization->timezone ?: '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="background:#f8fafc;border-top:1px solid #e2e8f0;">
                    <div class="d-flex align-items-center gap-2" style="font-size:0.78rem;color:#94a3b8;">
                        <i class="bi bi-clock"></i>
                        Created {{ $organization->created_at->diffForHumans() }} &middot;
                        Last updated {{ $organization->updated_at->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Logo & Quick Info -->
        <div class="col-12 col-lg-4 mb-4">
            <div class="card mb-3">
                <div class="card-header fw-semibold" style="font-size:0.85rem;">
                    <i class="bi bi-image me-2 text-muted"></i>Company Logo
                </div>
                <div class="card-body p-4 text-center">
                    @if($organization->logo)
                        <img src="{{ Storage::disk('public')->url($organization->logo) }}"
                             alt="{{ $organization->name }} logo"
                             class="img-fluid rounded-xl"
                             style="max-height:120px;object-fit:contain;">
                    @else
                        <div style="width:80px;height:80px;background:linear-gradient(135deg,#2563eb,#7c3aed);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 0.75rem;">
                            <span style="font-size:2rem;font-weight:700;color:#fff;">
                                {{ strtoupper(substr($organization->name, 0, 1)) }}
                            </span>
                        </div>
                        <p style="font-size:0.8rem;color:#94a3b8;" class="mb-2">No logo uploaded</p>
                        <a href="{{ route('organization.edit', $organization) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-upload me-1"></i> Upload Logo
                        </a>
                    @endif
                </div>
            </div>

            <div class="card">
                <div class="card-header fw-semibold" style="font-size:0.85rem;">
                    <i class="bi bi-lightning me-2 text-warning"></i>Integrations
                </div>
                <div class="card-body p-3">
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background:#f8fafc;">
                            <div class="d-flex align-items-center gap-2" style="font-size:0.85rem;">
                                <i class="bi bi-facebook" style="color:#1877f2;"></i> Facebook
                            </div>
                            <span class="badge bg-secondary" style="font-size:0.65rem;">Coming Soon</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background:#f8fafc;">
                            <div class="d-flex align-items-center gap-2" style="font-size:0.85rem;">
                                <i class="bi bi-instagram" style="color:#e1306c;"></i> Instagram
                            </div>
                            <span class="badge bg-secondary" style="font-size:0.65rem;">Coming Soon</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background:#f8fafc;">
                            <div class="d-flex align-items-center gap-2" style="font-size:0.85rem;">
                                <i class="bi bi-linkedin" style="color:#0a66c2;"></i> LinkedIn
                            </div>
                            <span class="badge bg-secondary" style="font-size:0.65rem;">Coming Soon</span>
                        </div>
                        <div class="d-flex align-items-center justify-content-between p-2 rounded" style="background:#f8fafc;">
                            <div class="d-flex align-items-center gap-2" style="font-size:0.85rem;">
                                <i class="bi bi-youtube" style="color:#ff0000;"></i> YouTube
                            </div>
                            <span class="badge bg-secondary" style="font-size:0.65rem;">Coming Soon</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
