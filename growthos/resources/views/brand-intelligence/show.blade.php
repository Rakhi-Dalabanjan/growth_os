<x-app-layout>
    <x-slot name="title">Brand Intelligence</x-slot>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="font-size:1.4rem;color:#1e293b;">
                Brand Intelligence Engine
            </h2>
            <p class="mb-0" style="color:#64748b;font-size:0.875rem;">
                AI-generated brand persona, audience segmentation, positioning analysis, and recommendations for {{ $brandProfile->brand_name }}.
            </p>
        </div>
        <div>
            <form action="{{ route('brand-intelligence.analyze') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-4 py-2" style="border-radius:10px; font-weight:600;">
                    <i class="bi bi-cpu"></i>
                    {{ $brandIntelligence ? 'Regenerate Analysis' : 'Run Brand Analysis' }}
                </button>
            </form>
        </div>
    </div>

    @if(!$brandIntelligence)
        <!-- Empty / Call To Action State -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:16px; background:linear-gradient(135deg, #ffffff, #f8fafc);">
            <div class="card-body p-5 text-center">
                <div class="mx-auto mb-4 d-flex align-items-center justify-content-center" style="width:80px; height:80px; background:#f5f3ff; border-radius:50%;">
                    <i class="bi bi-brain-half text-purple" style="font-size:2.5rem; color:#7c3aed;"></i>
                </div>
                <h3 class="fw-bold mb-2" style="color:#0f172a;">Teach AI Everything About Your Business</h3>
                <p class="text-muted mx-auto mb-4" style="max-width: 600px; font-size:0.95rem;">
                    Run the Brand Intelligence Engine to analyze your brand profile. This allows our AI system to deeply understand your products, target audience, brand voice, competitors, and marketing objectives to produce high-performing strategies.
                </p>
                <div class="alert alert-info border-0 mx-auto text-start d-inline-flex align-items-center gap-2 mb-4" style="max-width: 500px; border-radius:10px; font-size:0.85rem;">
                    <i class="bi bi-info-circle-fill flex-shrink-0 text-info"></i>
                    <div>
                        Requires: <strong>Brand Name</strong>, <strong>Business Description</strong>, and <strong>Target Audience</strong> to be defined in your Brand Profile.
                    </div>
                </div>
                <div>
                    <form action="{{ route('brand-intelligence.analyze') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5" style="border-radius:12px; font-weight:600; font-size:1rem;">
                            <i class="bi bi-magic me-2"></i> Start Brand Analysis
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- Grid Content -->
        <div class="row g-4">
            
            <!-- Left Column: Confidence Score & Core Insights -->
            <div class="col-12 col-lg-4">
                <div class="row g-4">
                    
                    <!-- Confidence Score Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius:16px; background:#ffffff;">
                            <div class="card-body p-4 text-center">
                                <p class="mb-1" style="font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8;">
                                    Analysis Confidence Score
                                </p>
                                <div class="my-3 position-relative d-inline-block">
                                    <h1 class="fw-extrabold text-primary mb-0" style="font-size:3.5rem;">
                                        {{ $brandIntelligence->confidence_score }}%
                                    </h1>
                                </div>
                                <div class="progress mb-3" style="height:8px; border-radius:10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $brandIntelligence->confidence_score }}%; border-radius:10px;" aria-valuenow="{{ $brandIntelligence->confidence_score }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mb-0 text-muted" style="font-size:0.82rem;">
                                    Calculated based on the depth and details provided in the active Brand Profile inputs.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- AI Metadata Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius:16px; background:#ffffff;">
                            <div class="card-header border-0 pb-0" style="background:#ffffff;">
                                <span class="fw-semibold text-muted" style="font-size:0.85rem;">
                                    <i class="bi bi-info-circle me-1"></i> Engine Metadata
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-3" style="font-size:0.875rem;">
                                    <li class="d-flex justify-content-between">
                                        <span class="text-muted">AI Provider:</span>
                                        <span class="fw-semibold badge bg-light text-dark" style="font-size:0.8rem;">
                                            {{ ucfirst($brandIntelligence->provider) }}
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="text-muted">Model:</span>
                                        <span class="fw-semibold" style="color:#334155;">
                                            {{ $brandIntelligence->model }}
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="text-muted">Execution Time:</span>
                                        <span class="fw-semibold" style="color:#334155;">
                                            {{ number_format($brandIntelligence->execution_time, 2) }} seconds
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="text-muted">Last Generated:</span>
                                        <span class="fw-semibold text-end" style="color:#334155; font-size:0.8rem;">
                                            {{ $brandIntelligence->generated_at->format('M j, Y H:i:s') }}<br>
                                            <small class="text-muted font-monospace">({{ $brandIntelligence->generated_at->diffForHumans() }})</small>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Brand Personality Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius:16px; background:#ffffff;">
                            <div class="card-header border-0 pb-0" style="background:#ffffff;">
                                <span class="fw-semibold" style="font-size:0.95rem; color:#0f172a;">
                                    <i class="bi bi-emoji-smile me-2 text-primary"></i> Brand Personality
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex flex-wrap gap-2">
                                    @if(is_array($brandIntelligence->brand_personality))
                                        @foreach($brandIntelligence->brand_personality as $trait)
                                            <span class="badge" style="background:#eff6ff; color:#2563eb; font-size:0.8rem; padding: 0.5rem 0.8rem; border-radius:8px; font-weight:500;">
                                                # {{ $trait }}
                                            </span>
                                        @endforeach
                                    @else
                                        <span class="text-muted">No specific traits returned.</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Tone / Voice Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius:16px; background:#ffffff;">
                            <div class="card-header border-0 pb-0" style="background:#ffffff;">
                                <span class="fw-semibold" style="font-size:0.95rem; color:#0f172a;">
                                    <i class="bi bi-megaphone me-2 text-warning"></i> Recommended Tone & Voice
                                </span>
                            </div>
                            <div class="card-body p-4">
                                @if(is_array($brandIntelligence->brand_voice))
                                    <ul class="list-group list-group-flush mb-0" style="font-size:0.875rem;">
                                        @foreach($brandIntelligence->brand_voice as $voiceRule)
                                            <li class="list-group-item px-0 border-0 d-flex gap-2">
                                                <i class="bi bi-check-circle-fill text-success flex-shrink-0" style="margin-top:2px;"></i>
                                                <span>{{ $voiceRule }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mb-0">{{ $brandIntelligence->brand_voice ?: 'N/A' }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Column: SWOT, Target Customer, Marketing Goals, Recommendations -->
            <div class="col-12 col-lg-8">
                <div class="d-flex flex-column gap-4">

                    <!-- Executive Summary Card -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-card-text me-2 text-primary"></i> Executive Summary
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-0" style="line-height:1.6; color:#334155; font-size:0.92rem;">
                                {{ $brandIntelligence->summary }}
                            </p>
                        </div>
                    </div>

                    <!-- Ideal Customer Card -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-people me-2 text-info"></i> Target Audience & Ideal Customer
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if(is_array($brandIntelligence->ideal_customer))
                                <div class="row g-3">
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 bg-light rounded-3 h-100">
                                            <h6 class="fw-bold text-dark mb-1" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Demographics</h6>
                                            <p class="mb-0 text-muted" style="font-size:0.875rem;">
                                                {{ $brandIntelligence->ideal_customer['demographics'] ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <div class="p-3 bg-light rounded-3 h-100">
                                            <h6 class="fw-bold text-dark mb-1" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Key Behaviors</h6>
                                            <p class="mb-0 text-muted" style="font-size:0.875rem;">
                                                {{ $brandIntelligence->ideal_customer['behaviors'] ?? 'N/A' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if(isset($brandIntelligence->ideal_customer['pains']))
                                        <div class="col-12">
                                            <div class="p-3 bg-light rounded-3">
                                                <h6 class="fw-bold text-danger mb-1" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Pain Points</h6>
                                                <p class="mb-0 text-muted" style="font-size:0.875rem;">
                                                    {{ $brandIntelligence->ideal_customer['pains'] }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ $brandIntelligence->ideal_customer }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Problems & Goals -->
                    <div class="row g-4">
                        <!-- Problems we solve -->
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header border-0 pb-0 bg-transparent">
                                    <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:0.95rem;">
                                        <i class="bi bi-patch-question me-2 text-danger"></i> Customer Problems
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    @if(is_array($brandIntelligence->customer_problems))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.875rem;">
                                            @foreach($brandIntelligence->customer_problems as $prob)
                                                <li class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-x-circle text-danger mt-1 flex-shrink-0"></i>
                                                    <span class="text-muted">{{ $prob }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">N/A</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Customer Goals -->
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header border-0 pb-0 bg-transparent">
                                    <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:0.95rem;">
                                        <i class="bi bi-compass me-2 text-success"></i> Customer Goals
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    @if(is_array($brandIntelligence->customer_goals))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.875rem;">
                                            @foreach($brandIntelligence->customer_goals as $goal)
                                                <li class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-check-circle text-success mt-1 flex-shrink-0"></i>
                                                    <span class="text-muted">{{ $goal }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">N/A</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SWOT Matrix Card -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-diagram-3 me-2 text-primary"></i> SWOT Analysis
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                
                                <!-- Strengths -->
                                <div class="col-12 col-md-6">
                                    <div class="p-3 rounded-3" style="background:#f0fdf4; border-left:4px solid #16a34a;">
                                        <h6 class="fw-bold text-success mb-2" style="font-size:0.85rem;"><i class="bi bi-shield-plus me-1"></i> Strengths</h6>
                                        @if(is_array($brandIntelligence->strengths))
                                            <ul class="ps-3 mb-0" style="font-size:0.82rem; color:#1e293b;">
                                                @foreach($brandIntelligence->strengths as $str)
                                                    <li class="mb-1">{{ $str }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted mb-0">{{ $brandIntelligence->strengths }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Weaknesses -->
                                <div class="col-12 col-md-6">
                                    <div class="p-3 rounded-3" style="background:#fef2f2; border-left:4px solid #dc2626;">
                                        <h6 class="fw-bold text-danger mb-2" style="font-size:0.85rem;"><i class="bi bi-shield-slash me-1"></i> Weaknesses</h6>
                                        @if(is_array($brandIntelligence->weaknesses))
                                            <ul class="ps-3 mb-0" style="font-size:0.82rem; color:#1e293b;">
                                                @foreach($brandIntelligence->weaknesses as $wk)
                                                    <li class="mb-1">{{ $wk }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted mb-0">{{ $brandIntelligence->weaknesses }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Opportunities -->
                                <div class="col-12 col-md-6">
                                    <div class="p-3 rounded-3" style="background:#eff6ff; border-left:4px solid #2563eb;">
                                        <h6 class="fw-bold text-primary mb-2" style="font-size:0.85rem;"><i class="bi bi-lightbulb me-1"></i> Opportunities</h6>
                                        @if(is_array($brandIntelligence->opportunities))
                                            <ul class="ps-3 mb-0" style="font-size:0.82rem; color:#1e293b;">
                                                @foreach($brandIntelligence->opportunities as $opp)
                                                    <li class="mb-1">{{ $opp }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted mb-0">{{ $brandIntelligence->opportunities }}</p>
                                        @endif
                                    </div>
                                </div>

                                <!-- Risks -->
                                <div class="col-12 col-md-6">
                                    <div class="p-3 rounded-3" style="background:#fffbeb; border-left:4px solid #d97706;">
                                        <h6 class="fw-bold text-warning mb-2" style="font-size:0.85rem;"><i class="bi bi-exclamation-triangle me-1"></i> Risks</h6>
                                        @if(is_array($brandIntelligence->risks))
                                            <ul class="ps-3 mb-0" style="font-size:0.82rem; color:#1e293b;">
                                                @foreach($brandIntelligence->risks as $rsk)
                                                    <li class="mb-1">{{ $rsk }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <p class="text-muted mb-0">{{ $brandIntelligence->risks }}</p>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- Marketing Goals & Strategy Recommendations -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-rocket-takeoff me-2 text-violet" style="color:#7c3aed;"></i> Strategy & Marketing Recommendations
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            
                            <!-- Marketing Objectives -->
                            <div class="mb-4">
                                <h6 class="fw-semibold text-dark mb-2" style="font-size:0.9rem;">Marketing Objectives:</h6>
                                @if(is_array($brandIntelligence->marketing_objectives))
                                    <ul class="list-group list-group-flush mb-0" style="font-size:0.875rem;">
                                        @foreach($brandIntelligence->marketing_objectives as $obj)
                                            <li class="list-group-item px-0 border-0 d-flex gap-2">
                                                <i class="bi bi-bullseye text-danger flex-shrink-0" style="margin-top:2px;"></i>
                                                <span class="text-muted">{{ $obj }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted">{{ $brandIntelligence->marketing_objectives }}</p>
                                @endif
                            </div>

                            <!-- Content Pillars -->
                            <div class="mb-4">
                                <h6 class="fw-semibold text-dark mb-2" style="font-size:0.9rem;">Recommended Content Pillars:</h6>
                                <div class="row g-2">
                                    @if(is_array($brandIntelligence->recommended_content_pillars))
                                        @foreach($brandIntelligence->recommended_content_pillars as $pillar)
                                            <div class="col-12 col-sm-6">
                                                <div class="p-2 border rounded-3 bg-light d-flex align-items-center gap-2" style="font-size:0.85rem;">
                                                    <i class="bi bi-bookmark-star-fill text-primary"></i>
                                                    <span class="text-dark fw-medium">{{ $pillar }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    @else
                                        <p class="text-muted">{{ $brandIntelligence->recommended_content_pillars }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Recommended Post Frequency -->
                            <div class="mb-4 p-3 bg-light rounded-3 d-flex align-items-center gap-3">
                                <div style="width:40px; height:40px; background:#e0f2fe; border-radius:8px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    <i class="bi bi-calendar-event-fill text-info"></i>
                                </div>
                                <div>
                                    <h6 class="fw-semibold mb-0" style="font-size:0.85rem; color:#0369a1;">Recommended Posting Frequency:</h6>
                                    <p class="mb-0 text-muted" style="font-size:0.85rem;">
                                        {{ $brandIntelligence->recommended_posting_frequency ?: 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            <!-- CTAs & Hashtags -->
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <h6 class="fw-semibold text-dark mb-2" style="font-size:0.9rem;">Recommended Calls-To-Action (CTAs):</h6>
                                    @if(is_array($brandIntelligence->recommended_cta))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.82rem;">
                                            @foreach($brandIntelligence->recommended_cta as $ctaVal)
                                                <li class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-box-arrow-up-right text-primary"></i>
                                                    <code class="text-dark" style="font-size:0.8rem; background:#f1f5f9; padding:0.2rem 0.4rem; border-radius:4px;">"{{ $ctaVal }}"</code>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted">{{ $brandIntelligence->recommended_cta }}</p>
                                    @endif
                                </div>
                                <div class="col-12 col-md-6">
                                    <h6 class="fw-semibold text-dark mb-2" style="font-size:0.9rem;">Recommended Hashtags:</h6>
                                    <div class="d-flex flex-wrap gap-2">
                                        @if(is_array($brandIntelligence->recommended_hashtags))
                                            @foreach($brandIntelligence->recommended_hashtags as $tag)
                                                <span class="badge bg-light text-primary font-monospace" style="font-size:0.8rem; padding: 0.4rem 0.6rem; border-radius:6px; border: 1px solid #cbd5e1;">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        @else
                                            <p class="text-muted">{{ $brandIntelligence->recommended_hashtags }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Competitor Summary Card -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-eye me-2 text-danger"></i> Competitor Analysis & Positioning
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <p class="mb-0" style="line-height:1.6; color:#334155; font-size:0.92rem;">
                                {{ $brandIntelligence->competitor_summary ?: 'No competitor details analyzed yet.' }}
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            
        </div>
    @endif
</x-app-layout>
