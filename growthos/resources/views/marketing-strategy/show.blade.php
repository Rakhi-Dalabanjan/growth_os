<x-app-layout>
    <x-slot name="title">Marketing Strategy</x-slot>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h2 class="fw-bold mb-1" style="font-size:1.4rem;color:#1e293b;">
                AI Marketing Strategy Engine
            </h2>
            <p class="mb-0" style="color:#64748b;font-size:0.875rem;">
                Strategic roadmap, recommended formats, campaign blueprints, and performance indicators generated for {{ $brandProfile->brand_name }}.
            </p>
        </div>
        <div>
            <form action="{{ route('marketing-strategy.generate') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary d-flex align-items-center gap-2 shadow-sm px-4 py-2" style="border-radius:10px; font-weight:600;">
                    <i class="bi bi-rocket-takeoff"></i>
                    {{ $marketingStrategy ? 'Regenerate Strategy' : 'Generate Strategy' }}
                </button>
            </form>
        </div>
    </div>

    @if(!$marketingStrategy)
        <!-- Empty Strategy State -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius:16px; background:linear-gradient(135deg, #ffffff, #f8fafc);">
            <div class="card-body p-5 text-center">
                <div class="mx-auto mb-4 d-flex align-items-center justify-content-center" style="width:80px; height:80px; background:#eff6ff; border-radius:50%;">
                    <i class="bi bi-rocket-takeoff text-primary" style="font-size:2.5rem;"></i>
                </div>
                <h3 class="fw-bold mb-2" style="color:#0f172a;">Formulate Your Strategic Roadmaps</h3>
                <p class="text-muted mx-auto mb-4" style="max-width: 600px; font-size:0.95rem;">
                    Deploy the Social Media Marketing Strategy Engine. By combining your brand profile parameters and brand intelligence, our AI develops custom posting frequencies, campaign structures, platform selections, copy instructions, and KPIs to grow your digital footprint.
                </p>
                <div class="alert alert-info border-0 mx-auto text-start d-inline-flex align-items-center gap-2 mb-4" style="max-width: 500px; border-radius:10px; font-size:0.85rem;">
                    <i class="bi bi-info-circle-fill flex-shrink-0 text-info"></i>
                    <div>
                        Requires: <strong>Brand Intelligence</strong> profile to be generated. Click below to initiate.
                    </div>
                </div>
                <div>
                    <form action="{{ route('marketing-strategy.generate') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg shadow-sm px-5" style="border-radius:12px; font-weight:600; font-size:1rem;">
                            <i class="bi bi-magic me-2"></i> Start Strategy Formulation
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @else
        <!-- Grid Content -->
        <div class="row g-4">
            
            <!-- Left Column: Confidence Score & Channels -->
            <div class="col-12 col-lg-4">
                <div class="row g-4">
                    
                    <!-- Confidence Score Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius:16px;">
                            <div class="card-body p-4 text-center">
                                <p class="mb-1" style="font-size:0.8rem; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:#94a3b8;">
                                    Strategy Confidence Score
                                </p>
                                <div class="my-3 position-relative d-inline-block">
                                    <h1 class="fw-extrabold text-primary mb-0" style="font-size:3.5rem;">
                                        {{ $marketingStrategy->confidence_score }}%
                                    </h1>
                                </div>
                                <div class="progress mb-3" style="height:8px; border-radius:10px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $marketingStrategy->confidence_score }}%; border-radius:10px;" aria-valuenow="{{ $marketingStrategy->confidence_score }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="mb-0 text-muted" style="font-size:0.82rem;">
                                    Based on the depth of the computed Brand Intelligence inputs and competitor parameters.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Recommended Platforms -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius:16px;">
                            <div class="card-header border-0 pb-0 bg-transparent">
                                <span class="fw-semibold text-muted" style="font-size:0.85rem;">
                                    <i class="bi bi-share me-1 text-primary"></i> Primary Channels
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="d-flex flex-wrap gap-2">
                                    @if(is_array($marketingStrategy->recommended_platforms))
                                        @foreach($marketingStrategy->recommended_platforms as $platform)
                                            <span class="badge" style="background:#eff6ff; color:#2563eb; font-size:0.85rem; padding:0.6rem 0.9rem; border-radius:10px; font-weight:600;">
                                                <i class="bi bi-{{ strtolower($platform) === 'twitter' ? 'twitter-x' : (strtolower($platform) === 'linkedin' ? 'linkedin' : (strtolower($platform) === 'instagram' ? 'instagram' : 'globe')) }} me-1"></i>
                                                {{ $platform }}
                                            </span>
                                        @endforeach
                                    @else
                                        <p class="text-muted mb-0">N/A</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Posting Frequency & Content Formats -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius:16px;">
                            <div class="card-header border-0 pb-0 bg-transparent">
                                <span class="fw-semibold text-muted" style="font-size:0.85rem;">
                                    <i class="bi bi-clock-history me-1 text-warning"></i> Publishing Logistics
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <div class="mb-4">
                                    <h6 class="fw-bold mb-2 text-dark" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Recommended Frequency</h6>
                                    <p class="text-muted" style="font-size:0.9rem; line-height: 1.5;">
                                        {{ $marketingStrategy->posting_frequency }}
                                    </p>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-2 text-dark" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Recommended Formats</h6>
                                    <div class="d-flex flex-wrap gap-1">
                                        @if(is_array($marketingStrategy->recommended_formats))
                                            @foreach($marketingStrategy->recommended_formats as $format)
                                                <span class="badge bg-light text-dark font-monospace" style="font-size:0.75rem; padding: 0.4rem 0.6rem; border-radius:6px; border: 1px solid #cbd5e1;">
                                                    {{ $format }}
                                                </span>
                                            @endforeach
                                        @else
                                            <p class="text-muted mb-0">N/A</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Metadata Card -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm" style="border-radius:16px;">
                            <div class="card-header border-0 pb-0 bg-transparent">
                                <span class="fw-semibold text-muted" style="font-size:0.85rem;">
                                    <i class="bi bi-info-circle me-1"></i> Strategy Metadata
                                </span>
                            </div>
                            <div class="card-body p-4">
                                <ul class="list-unstyled mb-0 d-flex flex-column gap-3" style="font-size:0.875rem;">
                                    <li class="d-flex justify-content-between">
                                        <span class="text-muted">AI Provider:</span>
                                        <span class="fw-semibold badge bg-light text-dark" style="font-size:0.8rem;">
                                            {{ ucfirst($marketingStrategy->provider) }}
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="text-muted">Model:</span>
                                        <span class="fw-semibold" style="color:#334155;">
                                            {{ $marketingStrategy->model }}
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="text-muted">Execution Time:</span>
                                        <span class="fw-semibold" style="color:#334155;">
                                            {{ number_format($marketingStrategy->execution_time, 2) }} seconds
                                        </span>
                                    </li>
                                    <li class="d-flex justify-content-between">
                                        <span class="text-muted">Last Generated:</span>
                                        <span class="fw-semibold text-end" style="color:#334155; font-size:0.8rem;">
                                            {{ $marketingStrategy->generated_at->format('M j, Y H:i:s') }}<br>
                                            <small class="text-muted font-monospace">({{ $marketingStrategy->generated_at->diffForHumans() }})</small>
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- Right Column: Goals, Pillars, Campaigns, KPIs -->
            <div class="col-12 col-lg-8">
                <div class="d-flex flex-column gap-4">

                    <!-- Strategy Name & Goals Card -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-body p-4">
                            <h4 class="fw-extrabold mb-3 text-dark" style="font-size:1.25rem;">
                                {{ $marketingStrategy->strategy_name ?: 'Social Media Strategy' }}
                            </h4>
                            
                            <div class="row g-3 mt-2">
                                <div class="col-12 col-md-6">
                                    <div class="p-3 bg-light rounded-3 h-100 border-start border-primary border-4">
                                        <h6 class="fw-bold text-primary mb-1" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Business Objective</h6>
                                        <p class="mb-0 text-muted" style="font-size:0.875rem; line-height: 1.5;">
                                            {{ $marketingStrategy->business_goal }}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="p-3 bg-light rounded-3 h-100 border-start border-info border-4">
                                        <h6 class="fw-bold text-info mb-1" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;">Marketing Goal</h6>
                                        <p class="mb-0 text-muted" style="font-size:0.875rem; line-height: 1.5;">
                                            {{ $marketingStrategy->marketing_goal }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Target Segments & Tone Guidelines -->
                    <div class="row g-4">
                        <!-- Audience segments -->
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header border-0 pb-0 bg-transparent">
                                    <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:0.95rem;">
                                        <i class="bi bi-people me-2 text-info"></i> Target Audience Segments
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    @if(is_array($marketingStrategy->audience_segments))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.875rem;">
                                            @foreach($marketingStrategy->audience_segments as $segment)
                                                <li class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-person-check text-info mt-1 flex-shrink-0"></i>
                                                    <span class="text-muted">{{ $segment }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">N/A</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Tone guidelines -->
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header border-0 pb-0 bg-transparent">
                                    <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:0.95rem;">
                                        <i class="bi bi-input-cursor-text me-2 text-warning"></i> Tone & Voice Guidelines
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    @if(is_array($marketingStrategy->tone_guidelines))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.875rem;">
                                            @foreach($marketingStrategy->tone_guidelines as $guideline)
                                                <li class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-check-circle-fill text-success mt-1 flex-shrink-0"></i>
                                                    <span class="text-muted">{{ $guideline }}</span>
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

                    <!-- Content Pillars Card -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-columns me-2 text-primary"></i> Strategic Content Pillars
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-3">
                                @if(is_array($marketingStrategy->content_pillars))
                                    @foreach($marketingStrategy->content_pillars as $idx => $pillar)
                                        <div class="col-12 col-md-4">
                                            <div class="p-3 bg-light rounded-3 h-100 text-center">
                                                <div class="d-inline-flex align-items-center justify-content-center mb-2" style="width:40px; height:40px; background:#eff6ff; border-radius:50%;">
                                                    <strong class="text-primary">0{{ $idx + 1 }}</strong>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1" style="font-size:0.9rem;">{{ $pillar }}</h6>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">N/A</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Blueprint Ideas (Timeline format) -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-calendar3-range me-2 text-primary"></i> Recommended Campaign Blueprints
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if(is_array($marketingStrategy->campaign_ideas))
                                <div class="position-relative ps-4" style="border-left: 2px solid #e2e8f0;">
                                    @foreach($marketingStrategy->campaign_ideas as $idx => $campaign)
                                        <div class="mb-4 position-relative">
                                            <!-- Timeline bullet -->
                                            <div class="position-absolute" style="left: -33px; top: 0; width: 16px; height: 16px; border-radius: 50%; background: #2563eb; border: 4px solid #fff; box-shadow: 0 0 0 2px #eff6ff;"></div>
                                            
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                                <h6 class="fw-bold mb-0 text-dark" style="font-size:0.95rem;">
                                                    {{ $campaign['name'] ?? 'Campaign ' . ($idx + 1) }}
                                                </h6>
                                                @if(isset($campaign['duration']))
                                                    <span class="badge bg-light text-primary border" style="font-size:0.7rem;">
                                                        <i class="bi bi-clock me-1"></i>{{ $campaign['duration'] }}
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <p class="text-muted mb-2" style="font-size:0.875rem; line-height: 1.5;">
                                                {{ $campaign['description'] ?? 'No description provided.' }}
                                            </p>
                                            
                                            @if(isset($campaign['channels']) && is_array($campaign['channels']))
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($campaign['channels'] as $chan)
                                                        <span class="badge bg-light text-secondary border" style="font-size:0.7rem;">
                                                            {{ $chan }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">N/A</p>
                            @endif
                        </div>
                    </div>

                    <!-- CTAs & Hashtags Strategy -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-signpost-split me-2 text-primary"></i> Strategic Execution Rules
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <h6 class="fw-bold mb-2 text-dark" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;"><i class="bi bi-box-arrow-up-right text-primary me-2"></i> CTA Strategy</h6>
                                    @if(is_array($marketingStrategy->cta_strategy))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.875rem;">
                                            @foreach($marketingStrategy->cta_strategy as $ctaRule)
                                                <li class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-arrow-right-short text-primary mt-1 flex-shrink-0"></i>
                                                    <span class="text-muted">{{ $ctaRule }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">{{ $marketingStrategy->cta_strategy }}</p>
                                    @endif
                                </div>
                                <div class="col-12 col-md-6">
                                    <h6 class="fw-bold mb-2 text-dark" style="font-size:0.85rem; text-transform:uppercase; letter-spacing:0.5px;"><i class="bi bi-hash text-warning me-1"></i> Hashtag Rules</h6>
                                    @if(is_array($marketingStrategy->hashtags_strategy))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.875rem;">
                                            @foreach($marketingStrategy->hashtags_strategy as $hashRule)
                                                <li class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-arrow-right-short text-warning mt-1 flex-shrink-0"></i>
                                                    <span class="text-muted">{{ $hashRule }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">{{ $marketingStrategy->hashtags_strategy }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KPIs & Measurement -->
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-header border-0 pb-0 bg-transparent">
                            <h5 class="fw-bold mb-0" style="color:#0f172a; font-size:1.05rem;">
                                <i class="bi bi-bar-chart me-2 text-success"></i> Key Performance Indicators (KPIs)
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            @if(is_array($marketingStrategy->kpis))
                                <div class="row g-2">
                                    @foreach($marketingStrategy->kpis as $kpi)
                                        <div class="col-12 col-md-6">
                                            <div class="p-3 border rounded-3 bg-light d-flex align-items-center gap-3">
                                                <i class="bi bi-check2-square text-success" style="font-size:1.2rem;"></i>
                                                <span class="text-dark fw-medium" style="font-size:0.875rem;">{{ $kpi }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted mb-0">N/A</p>
                            @endif
                        </div>
                    </div>

                    <!-- SWOT & Growth recommendations -->
                    <div class="row g-4">
                        
                        <!-- Growth recommendations -->
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header border-0 pb-0 bg-transparent">
                                    <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:0.95rem;">
                                        <i class="bi bi-graph-up-arrow me-2 text-success"></i> Growth Recommendations
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    @if(is_array($marketingStrategy->growth_recommendations))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.875rem;">
                                            @foreach($marketingStrategy->growth_recommendations as $rec)
                                                <li class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-lightning-fill text-warning mt-1 flex-shrink-0"></i>
                                                    <span class="text-muted">{{ $rec }}</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted mb-0">N/A</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Risk considerations -->
                        <div class="col-12 col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header border-0 pb-0 bg-transparent">
                                    <h6 class="fw-bold mb-0" style="color:#0f172a; font-size:0.95rem;">
                                        <i class="bi bi-shield-exclamation me-2 text-danger"></i> Risk Considerations
                                    </h6>
                                </div>
                                <div class="card-body p-4">
                                    @if(is_array($marketingStrategy->risk_considerations))
                                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2" style="font-size:0.875rem;">
                                            @foreach($marketingStrategy->risk_considerations as $risk)
                                                <li class="d-flex align-items-start gap-2">
                                                    <i class="bi bi-exclamation-octagon text-danger mt-1 flex-shrink-0"></i>
                                                    <span class="text-muted">{{ $risk }}</span>
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

                </div>
            </div>
            
        </div>
    @endif
</x-app-layout>
