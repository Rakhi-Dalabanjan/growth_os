<x-app-layout>
    <x-slot name="title">Brand Profile</x-slot>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active">Brand Profile</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Main Panel: Brand Details -->
        <div class="col-12 col-lg-8">
            
            <!-- SECTION 1: Brand Identity -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:32px;height:32px;background:#eff6ff;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <i class="bi bi-tag-fill text-primary"></i>
                        </div>
                        <span class="fw-semibold" style="font-size:0.95rem;">Brand Identity</span>
                    </div>
                    <a href="{{ route('brand-profile.edit', $brandProfile) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> Edit
                    </a>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="text-muted-sm uppercase font-semibold text-xs tracking-wider" style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Brand Name</div>
                            <h5 class="fw-bold mb-0 text-dark" style="font-size:1.1rem;margin-top:0.25rem;">{{ $brandProfile->brand_name }}</h5>
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Tagline</div>
                            <p class="mb-0 text-dark fw-medium" style="font-size:0.9rem;margin-top:0.25rem;">{{ $brandProfile->tagline ?: '—' }}</p>
                        </div>
                        <div class="col-12">
                            <hr class="my-3" style="border-color:#e2e8f0;">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Business Description</div>
                            <div style="font-size:0.875rem;line-height:1.6;color:#334155;">{!! nl2br(e($brandProfile->business_description)) ?: '<span class="text-muted">No description provided</span>' !!}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: Business & Market -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#f0fdf4;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-briefcase-fill text-success"></i>
                    </div>
                    <span class="fw-semibold" style="font-size:0.95rem;">Business & Market</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.3rem;">Mission</div>
                            <div style="font-size:0.875rem;line-height:1.6;color:#334155;">{!! nl2br(e($brandProfile->mission)) ?: '<span class="text-muted">—</span>' !!}</div>
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.3rem;">Vision</div>
                            <div style="font-size:0.875rem;line-height:1.6;color:#334155;">{!! nl2br(e($brandProfile->vision)) ?: '<span class="text-muted">—</span>' !!}</div>
                        </div>
                        <div class="col-12">
                            <hr class="my-2" style="border-color:#e2e8f0;">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Primary Market</div>
                            <p class="mb-0 text-dark fw-medium mt-1" style="font-size:0.9rem;">{{ $brandProfile->primary_market ?: '—' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 3: Target Audience -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#fef3c7;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-people-fill text-warning"></i>
                    </div>
                    <span class="fw-semibold" style="font-size:0.95rem;">Target Audience</span>
                </div>
                <div class="card-body p-4">
                    <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Audience Demographics & Psychographics</div>
                    <div style="font-size:0.875rem;line-height:1.6;color:#334155;">{!! nl2br(e($brandProfile->target_audience)) ?: '<span class="text-muted">No audience information specified</span>' !!}</div>
                </div>
            </div>

            <!-- SECTION 4: Brand Voice -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#fdf4ff;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-chat-quote-fill" style="color:#a855f7;"></i>
                    </div>
                    <span class="fw-semibold" style="font-size:0.95rem;">Brand Voice</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3 col-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Brand Tone</div>
                            <div class="fw-semibold text-dark mt-1" style="font-size:0.9rem;">{{ $brandProfile->brand_tone ?: '—' }}</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Formality</div>
                            <div class="fw-semibold text-dark mt-1" style="font-size:0.9rem;">{{ $brandProfile->formality ?: '—' }}</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Language</div>
                            <div class="fw-semibold text-dark mt-1" style="font-size:0.9rem;">{{ $brandProfile->language ?: '—' }}</div>
                        </div>
                        <div class="col-md-3 col-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Emoji Style</div>
                            <div class="fw-semibold text-dark mt-1" style="font-size:0.9rem;">{{ $brandProfile->emoji_style ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 5: Visual Style -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#ecfdf5;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-palette-fill" style="color:#0d9488;"></i>
                    </div>
                    <span class="fw-semibold" style="font-size:0.95rem;">Visual Style & Identity</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4 align-items-center">
                        <div class="col-md-6 border-end-md">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.5rem;">Color Palette</div>
                            <div class="d-flex flex-column gap-2">
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:32px;height:32px;border-radius:50%;background-color:{{ $brandProfile->primary_color ?: '#2563eb' }};border:1px solid #cbd5e1;"></div>
                                    <div>
                                        <div style="font-size:0.75rem;font-weight:600;color:#64748b;">Primary</div>
                                        <code style="font-size:0.8rem;color:#1e293b;">{{ $brandProfile->primary_color ?: '—' }}</code>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:32px;height:32px;border-radius:50%;background-color:{{ $brandProfile->secondary_color ?: '#64748b' }};border:1px solid #cbd5e1;"></div>
                                    <div>
                                        <div style="font-size:0.75rem;font-weight:600;color:#64748b;">Secondary</div>
                                        <code style="font-size:0.8rem;color:#1e293b;">{{ $brandProfile->secondary_color ?: '—' }}</code>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <div style="width:32px;height:32px;border-radius:50%;background-color:{{ $brandProfile->accent_color ?: '#f59e0b' }};border:1px solid #cbd5e1;"></div>
                                    <div>
                                        <div style="font-size:0.75rem;font-weight:600;color:#64748b;">Accent</div>
                                        <code style="font-size:0.8rem;color:#1e293b;">{{ $brandProfile->accent_color ?: '—' }}</code>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 ps-md-4">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Typography</div>
                            <div class="mb-3">
                                <div style="font-size:0.75rem;color:#64748b;font-weight:500;">Primary Font</div>
                                <div class="fw-semibold text-dark fs-5 mt-1" style="font-family: {{ $brandProfile->primary_font ?: 'inherit' }}; font-size:1.05rem;">
                                    {{ $brandProfile->primary_font ?: 'System Default' }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size:0.75rem;color:#64748b;font-weight:500;">Secondary Font</div>
                                <div class="fw-semibold text-dark fs-5 mt-1" style="font-family: {{ $brandProfile->secondary_font ?: 'inherit' }}; font-size:1.05rem;">
                                    {{ $brandProfile->secondary_font ?: 'System Default' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 6: Marketing Parameters -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#fef2f2;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-megaphone-fill text-danger"></i>
                    </div>
                    <span class="fw-semibold" style="font-size:0.95rem;">Marketing Parameters</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Primary Call to Action (CTA)</div>
                            <div class="fw-semibold text-dark mt-1" style="font-size:0.9rem;">{{ $brandProfile->primary_cta ?: '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;">Secondary Call to Action (CTA)</div>
                            <div class="fw-semibold text-dark mt-1" style="font-size:0.9rem;">{{ $brandProfile->secondary_cta ?: '—' }}</div>
                        </div>
                    </div>
                    <hr class="my-3" style="border-color:#e2e8f0;">
                    
                    <div class="mb-3">
                        <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Preferred Words & Phrases</div>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($brandProfile->preferred_words ?? [] as $word)
                                <span class="badge bg-success" style="font-size:0.75rem;padding:0.35rem 0.65rem;">{{ $word }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="mb-3">
                        <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Restricted Words / Jargon to Avoid</div>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($brandProfile->restricted_words ?? [] as $word)
                                <span class="badge bg-danger" style="font-size:0.75rem;padding:0.35rem 0.65rem;">{{ $word }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Competitors</div>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($brandProfile->competitor_names ?? [] as $word)
                                <span class="badge bg-secondary" style="font-size:0.75rem;padding:0.35rem 0.65rem;">{{ $word }}</span>
                            @empty
                                <span class="text-muted">—</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- SECTION 7: Compliance -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex align-items-center gap-2">
                    <div style="width:32px;height:32px;background:#f8fafc;border-radius:6px;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-shield-check text-secondary"></i>
                    </div>
                    <span class="fw-semibold" style="font-size:0.95rem;">Compliance & Legal</span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Approved Claims</div>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($brandProfile->approved_claims ?? [] as $claim)
                                    <span class="badge bg-light text-dark border" style="font-size:0.75rem;padding:0.35rem 0.65rem;">{{ $claim }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Restricted / Forbidden Claims</div>
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($brandProfile->restricted_claims ?? [] as $claim)
                                    <span class="badge bg-light text-danger border border-danger" style="font-size:0.75rem;padding:0.35rem 0.65rem;">{{ $claim }}</span>
                                @empty
                                    <span class="text-muted">—</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <hr class="my-3" style="border-color:#e2e8f0;">
                    <div>
                        <div style="font-size:0.72rem;color:#94a3b8;text-transform:uppercase;letter-spacing:0.8px;margin-bottom:0.4rem;">Legal Disclaimer</div>
                        <div style="font-size:0.85rem;line-height:1.6;color:#64748b;background:#f8fafc;padding:1rem;border-radius:8px;border:1px solid #e2e8f0;">
                            {!! nl2br(e($brandProfile->legal_disclaimer)) ?: '<span class="text-muted">No disclaimer set</span>' !!}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side: Completion Score Card -->
        <div class="col-12 col-lg-4">
            <div class="card shadow-sm sticky-lg-top" style="top:80px; z-index:1000;">
                <div class="card-header fw-semibold" style="font-size:0.85rem;">
                    <i class="bi bi-bar-chart-fill me-2 text-primary"></i>Profile Completion
                </div>
                <div class="card-body p-4 text-center">
                    <div class="d-inline-flex align-items-center justify-content-center position-relative mb-3" style="width:120px; height:120px;">
                        <!-- Circular progress simulated -->
                        <div style="position:absolute; width:100%; height:100%; border:8px solid #f1f5f9; border-radius:50%;"></div>
                        <div class="d-flex flex-column align-items-center justify-content-center">
                            <span class="fw-bold fs-2 mb-0" style="color:#1e293b;">{{ $completion['percentage'] }}%</span>
                            <span style="font-size:0.7rem;color:#94a3b8;text-transform:uppercase;font-weight:600;letter-spacing:0.5px;">Setup</span>
                        </div>
                    </div>
                    
                    <h5 class="fw-bold mb-1">{{ $completion['status'] }}</h5>
                    <p class="text-muted-sm mb-3">Filled {{ $completion['filled'] }} of {{ $completion['total'] }} brand metrics</p>
                    
                    <div class="progress mb-4" style="height:8px; border-radius:10px;">
                        <div class="progress-bar bg-{{ $completion['color'] }}" 
                             role="progressbar" 
                             style="width: {{ $completion['percentage'] }}%; border-radius:10px;" 
                             aria-valuenow="{{ $completion['percentage'] }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100"></div>
                    </div>

                    <a href="{{ route('brand-profile.edit', $brandProfile) }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-pencil-fill me-1"></i> Edit Brand Profile
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
