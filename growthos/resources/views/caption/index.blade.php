<x-app-layout>
    <x-slot name="title">Caption Studio</x-slot>

    <div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Caption Studio</li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.8rem; letter-spacing: -0.5px;">AI Caption Studio</h1>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">
                    Generate, refine, and approve platform-specific copy tailored to your brand voice.
                </p>
            </div>
            
            <div class="d-flex align-items-center gap-2">
                @if($brandIntelligence && $strategy && count($pendingCalendarEntries) > 0)
                    <button class="btn btn-primary btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#bulkGenerateModal" style="border-radius: 8px; font-size: 0.85rem; background-color: #2563eb; border-color: #2563eb;">
                        <i class="bi bi-lightning-charge me-1"></i> Bulk Generate
                    </button>
                @endif
            </div>
        </div>

        <!-- System Alerts & Warnings -->
        @if(!$brandIntelligence || !$strategy)
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 p-4" role="alert" style="border-radius: 12px; background-color: #fffbeb; border-left: 5px solid #d97706 !important;">
                <i class="bi bi-exclamation-triangle-fill text-warning me-3" style="font-size: 1.8rem;"></i>
                <div>
                    <h4 class="alert-heading fw-bold mb-1" style="color: #78350f; font-size: 1.1rem;">Setup Requirements Missing</h4>
                    <p class="mb-0 text-muted" style="font-size: 0.88rem;">
                        Before you can generate captions, you must complete the setup of your brand assets:
                        <ul class="mb-0 mt-2 pl-4">
                            @if(!$brandIntelligence)
                                <li><strong>Brand Intelligence:</strong> Please complete the <a href="{{ route('brand-intelligence') }}" class="fw-semibold text-decoration-underline text-warning-emphasis">Brand Intelligence analysis</a>.</li>
                            @endif
                            @if(!$strategy)
                                <li><strong>Marketing Strategy:</strong> Please generate a <a href="{{ route('marketing-strategy') }}" class="fw-semibold text-decoration-underline text-warning-emphasis">Marketing Strategy</a> first.</li>
                            @endif
                        </ul>
                    </p>
                </div>
            </div>
        @elseif(count($captions) === 0 && count($pendingCalendarEntries) === 0)
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4 p-4" role="alert" style="border-radius: 12px; background-color: #f0f9ff; border-left: 5px solid #0284c7 !important;">
                <i class="bi bi-calendar-event text-info me-3" style="font-size: 1.8rem;"></i>
                <div>
                    <h4 class="alert-heading fw-bold mb-1" style="color: #0c4a6e; font-size: 1.1rem;">No Content Calendar Entries</h4>
                    <p class="mb-0 text-muted" style="font-size: 0.88rem;">
                        You have not generated or created any entries in your Content Calendar yet. Go to the 
                        <a href="{{ route('content-calendar') }}" class="fw-semibold text-decoration-underline" style="color: #0c4a6e;">Content Calendar</a> 
                        and generate posts first before using the Caption Studio.
                    </p>
                </div>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px; background-color: #f0fdf4; color: #15803d; border-left: 5px solid #16a34a !important;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px; background-color: #fef2f2; color: #b91c1c; border-left: 5px solid #dc2626 !important;">
                <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 10px; background-color: #fffbeb; color: #b45309; border-left: 5px solid #d97706 !important;">
                <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Filter Toolbar -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-body p-3">
                <form action="{{ route('caption.index') }}" method="GET" class="row g-2 align-items-center">
                    <div class="col-6 col-md-2">
                        <select name="platform" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                            <option value="">Platform</option>
                            @foreach($platforms as $p)
                                <option value="{{ $p }}" {{ request('platform') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="pillar" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                            <option value="">Content Pillar</option>
                            @foreach($pillars as $p)
                                <option value="{{ $p }}" {{ request('pillar') === $p ? 'selected' : '' }}>{{ $p }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-2">
                        <select name="campaign" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                            <option value="">Campaign</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c }}" {{ request('campaign') === $c ? 'selected' : '' }}>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-1.5">
                        <select name="goal" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                            <option value="">Goal</option>
                            @foreach($goals as $g)
                                <option value="{{ $g }}" {{ request('goal') === $g ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-md-1.5">
                        <select name="status" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                            <option value="">Status</option>
                            <option value="Draft" {{ request('status') === 'Draft' ? 'selected' : '' }}>Draft</option>
                            <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                            <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div class="col-6 col-md-1">
                        <select name="month" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                            <option value="">Month</option>
                            @foreach($months as $m)
                                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>
                                    {{ date("F", mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-md-2 ms-auto">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search" class="form-control" placeholder="Search captions..." value="{{ request('search') }}" style="border-radius: 8px 0 0 8px;">
                            <button type="submit" class="btn btn-primary" style="border-radius: 0 8px 8px 0; background-color: #2563eb;"><i class="bi bi-search"></i></button>
                        </div>
                    </div>

                    @if(request()->anyFilled(['platform', 'pillar', 'campaign', 'goal', 'status', 'month', 'search']))
                        <div class="col-auto">
                            <a href="{{ route('caption.index') }}" class="btn btn-sm btn-link text-decoration-none text-danger fw-semibold" style="font-size: 0.82rem; padding: 0;">
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Left Side: Generated Captions Studio Grid -->
            <div class="col-lg-8">
                <form id="bulk-captions-form" action="{{ route('caption.bulk') }}" method="POST">
                    @csrf
                    <!-- Bulk Action Controls -->
                    <div class="d-flex align-items-center justify-content-between mb-3 bg-white p-3 shadow-sm" style="border-radius: 12px; display: none !important;" id="bulk-actions-toolbar">
                        <div class="d-flex align-items-center gap-2">
                            <input type="checkbox" class="form-check-input" id="select-all-captions" style="cursor: pointer;">
                            <span class="fw-semibold text-dark" id="selected-count" style="font-size: 0.88rem;">0 selected</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-outline-success fw-semibold" style="border-radius: 6px;">
                                <i class="bi bi-check2-all me-1"></i> Approve
                            </button>
                            <button type="submit" name="action" value="delete" class="btn btn-sm btn-outline-danger fw-semibold" style="border-radius: 6px;" onclick="return confirm('Are you sure you want to delete all selected captions?')">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </div>
                    </div>

                    @if(count($captions) > 0)
                        <div class="row g-3">
                            @foreach($captions as $caption)
                                <div class="col-12">
                                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border: 1px solid #e2e8f0; transition: transform 0.2s;">
                                        <!-- Card Header -->
                                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 pb-0 px-4">
                                            <div class="d-flex align-items-center gap-2">
                                                <input type="checkbox" name="ids[]" value="{{ $caption->id }}" class="form-check-input caption-checkbox" style="cursor: pointer;">
                                                
                                                <span class="badge text-capitalize px-2.5 py-1.5 fw-semibold" style="font-size: 0.75rem; 
                                                    background-color: 
                                                        @if($caption->platform === 'LinkedIn') #e0f2fe; color: #0369a1;
                                                        @elseif($caption->platform === 'Instagram') #fdf2f8; color: #be185d;
                                                        @elseif($caption->platform === 'X (Twitter)' || $caption->platform === 'Twitter') #f1f5f9; color: #0f172a;
                                                        @elseif($caption->platform === 'Facebook') #dbeafe; color: #1d4ed8;
                                                        @elseif($caption->platform === 'YouTube') #fee2e2; color: #b91c1c;
                                                        @else #f3f4f6; color: #374151;
                                                        @endif
                                                ">
                                                    @if($caption->platform === 'LinkedIn')<i class="bi bi-linkedin me-1"></i>
                                                    @elseif($caption->platform === 'Instagram')<i class="bi bi-instagram me-1"></i>
                                                    @elseif($caption->platform === 'X (Twitter)' || $caption->platform === 'Twitter')<i class="bi bi-twitter-x me-1"></i>
                                                    @elseif($caption->platform === 'Facebook')<i class="bi bi-facebook me-1"></i>
                                                    @elseif($caption->platform === 'YouTube')<i class="bi bi-youtube me-1"></i>
                                                    @endif
                                                    {{ $caption->platform }}
                                                </span>

                                                <span class="badge px-2.5 py-1.5 fw-semibold" style="font-size: 0.75rem;
                                                    background-color:
                                                        @if($caption->status === 'Approved') #dcfce7; color: #15803d;
                                                        @elseif($caption->status === 'Rejected') #fee2e2; color: #b91c1c;
                                                        @else #fef9c3; color: #854d0e;
                                                        @endif
                                                ">
                                                    {{ $caption->status }}
                                                </span>
                                            </div>

                                            <div class="dropdown">
                                                <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 8px;">
                                                    <li>
                                                        <button type="button" class="dropdown-item py-2" data-bs-toggle="modal" data-bs-target="#editCaptionModal{{ $caption->id }}">
                                                            <i class="bi bi-pencil-square me-2 text-muted"></i> Edit Caption
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('caption.duplicate', $caption->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item py-2">
                                                                <i class="bi bi-files me-2 text-muted"></i> Duplicate
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('caption.approve', $caption->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item py-2 text-success">
                                                                <i class="bi bi-check-lg me-2"></i> Approve
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('caption.reject', $caption->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item py-2 text-warning">
                                                                <i class="bi bi-x-lg me-2"></i> Reject
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('caption.destroy', $caption->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this caption?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item py-2 text-danger">
                                                                <i class="bi bi-trash3 me-2"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <!-- Card Body -->
                                        <div class="card-body px-4 py-3">
                                            @if($caption->headline)
                                                <h5 class="fw-bold text-dark mb-2" style="font-size: 1rem;">{{ $caption->headline }}</h5>
                                            @endif
                                            
                                            <p class="text-secondary mb-3 copyable-text" style="font-size: 0.9rem; white-space: pre-line; line-height: 1.5;">{{ $caption->caption }}</p>
                                            
                                            @if($caption->cta)
                                                <div class="mb-3 p-2 bg-light rounded" style="font-size: 0.85rem; border-left: 3px solid #2563eb;">
                                                    <strong>CTA:</strong> {{ $caption->cta }}
                                                </div>
                                            @endif

                                            @if($caption->hashtags && count($caption->hashtags) > 0)
                                                <div class="d-flex flex-wrap gap-1.5 mb-3">
                                                    @foreach($caption->hashtags as $tag)
                                                        <span class="text-primary" style="font-size: 0.85rem; font-weight: 500;">{{ $tag }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            @if($caption->keywords && count($caption->keywords) > 0)
                                                <div class="mb-3">
                                                    <span class="text-muted" style="font-size: 0.8rem;">Keywords:</span>
                                                    @foreach($caption->keywords as $kw)
                                                        <span class="badge bg-light text-secondary border px-2 py-1 ms-1" style="font-size: 0.72rem; border-radius: 4px;">{{ $kw }}</span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Card Footer -->
                                        <div class="card-footer bg-transparent border-0 px-4 pb-3 pt-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                            <div class="d-flex align-items-center gap-3 text-muted" style="font-size: 0.78rem;">
                                                <span><i class="bi bi-chat-text me-1"></i> {{ $caption->word_count }} words</span>
                                                <span><i class="bi bi-hash me-1"></i> {{ $caption->character_count }} chars</span>
                                                @if($caption->tone)
                                                    <span><i class="bi bi-chat-dots me-1"></i> Tone: {{ $caption->tone }}</span>
                                                @endif
                                                @if($caption->emoji_style)
                                                    <span>Emoji: {{ $caption->emoji_style }}</span>
                                                @endif
                                            </div>

                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-secondary px-2.5 py-1 copy-btn" style="border-radius: 6px; font-size: 0.78rem;">
                                                    <i class="bi bi-clipboard me-1"></i> Copy
                                                </button>

                                                @if($brandIntelligence && $strategy)
                                                    <button type="button" class="btn btn-sm btn-outline-primary px-2.5 py-1" style="border-radius: 6px; font-size: 0.78rem;" data-bs-toggle="modal" data-bs-target="#regenerateConfirmModal{{ $caption->id }}">
                                                        <i class="bi bi-arrow-clockwise me-1"></i> Regenerate
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Meta info footer banner -->
                                        <div class="card-footer bg-light border-0 py-2 px-4 text-muted" style="font-size: 0.72rem; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px;">
                                            Generated via <strong class="text-uppercase">{{ $caption->provider ?? 'Gateway' }}</strong> ({{ $caption->model ?? 'unknown' }}) on {{ $caption->generated_at ? $caption->generated_at->format('M d, Y H:i') : $caption->created_at->format('M d, Y H:i') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Regenerate Confirmation Modal -->
                                <div class="modal fade" id="regenerateConfirmModal{{ $caption->id }}" data-bs-backdrop="static" tabindex="-1" aria-labelledby="regenerateConfirmLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold" id="regenerateConfirmLabel">Confirm Caption Regeneration</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body py-3">
                                                <div class="alert alert-warning border-0 mb-3" style="border-radius: 10px; background-color: #fffbeb;">
                                                    <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                                                    Warning: Regenerating will completely overwrite the existing copy for this post. Any manual changes you have made will be lost.
                                                </div>
                                                <p class="text-muted mb-0">Are you sure you want to proceed with regenerating this caption?</p>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light btn-sm px-3 py-2 fw-semibold" style="border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                                                <form action="{{ route('caption.regenerate', $caption->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-primary btn-sm px-3 py-2 fw-semibold" style="border-radius: 8px; background-color: #2563eb;">Regenerate</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Edit Caption Modal -->
                                <div class="modal fade" id="editCaptionModal{{ $caption->id }}" tabindex="-1" aria-labelledby="editCaptionLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                                            <form action="{{ route('caption.update', $caption->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header border-0 pb-0 px-4 pt-4">
                                                    <h5 class="modal-title fw-bold" id="editCaptionLabel">Edit Caption Details</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body px-4 py-3">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold" style="font-size: 0.85rem;">Headline</label>
                                                        <input type="text" name="headline" class="form-control" style="border-radius: 8px;" value="{{ $caption->headline }}">
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold" style="font-size: 0.85rem;">Caption / Body Copy</label>
                                                        <textarea name="caption" class="form-control" rows="8" style="border-radius: 8px; font-size: 0.9rem;" required>{{ $caption->caption }}</textarea>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Call To Action (CTA)</label>
                                                            <input type="text" name="cta" class="form-control" style="border-radius: 8px;" value="{{ $caption->cta }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Hashtags (Space/Comma separated)</label>
                                                            <input type="text" name="hashtags" class="form-control" style="border-radius: 8px;" value="{{ $caption->hashtags ? implode(', ', $caption->hashtags) : '' }}">
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Tone</label>
                                                            <input type="text" name="tone" class="form-control" style="border-radius: 8px;" value="{{ $caption->tone }}">
                                                        </div>
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label fw-semibold" style="font-size: 0.85rem;">Language</label>
                                                            <input type="text" name="language" class="form-control" style="border-radius: 8px;" value="{{ $caption->language }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 px-4 pb-4 pt-0">
                                                    <button type="button" class="btn btn-light btn-sm px-3 py-2 fw-semibold" style="border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary btn-sm px-3 py-2 fw-semibold" style="border-radius: 8px; background-color: #2563eb;">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="card border-0 shadow-sm py-5" style="border-radius: 12px;">
                            <div class="card-body text-center">
                                <i class="bi bi-chat-left-dots text-muted" style="font-size: 3rem;"></i>
                                <h5 class="fw-bold mt-3 text-secondary">No Captions Found</h5>
                                <p class="text-muted" style="font-size: 0.9rem;">No captions match your active filters or none have been generated yet.</p>
                            </div>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Right Side: Pending Generation List -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <div class="card border-0 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div class="card-header bg-transparent border-0 px-4 pt-4 pb-2">
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.1rem;"><i class="bi bi-lightning-charge text-primary me-2"></i>Pending Captions</h5>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;">Content calendar posts waiting for copy generation.</p>
                    </div>

                    <div class="card-body px-4 py-2">
                        @if($brandIntelligence && $strategy)
                            @if(count($pendingCalendarEntries) > 0)
                                <div class="list-group list-group-flush">
                                    @foreach($pendingCalendarEntries as $entry)
                                        <div class="list-group-item px-0 py-3 border-bottom d-flex justify-content-between align-items-start gap-2">
                                            <div style="flex: 1;">
                                                <div class="d-flex align-items-center gap-1.5 mb-1.5">
                                                    <span class="badge text-capitalize px-2 py-0.5" style="font-size: 0.65rem;
                                                        background-color: 
                                                            @if($entry->platform === 'LinkedIn') #e0f2fe; color: #0369a1;
                                                            @elseif($entry->platform === 'Instagram') #fdf2f8; color: #be185d;
                                                            @elseif($entry->platform === 'X (Twitter)' || $entry->platform === 'Twitter') #f1f5f9; color: #0f172a;
                                                            @elseif($entry->platform === 'Facebook') #dbeafe; color: #1d4ed8;
                                                            @elseif($entry->platform === 'YouTube') #fee2e2; color: #b91c1c;
                                                            @else #f3f4f6; color: #374151;
                                                            @endif
                                                    ">
                                                        {{ $entry->platform }}
                                                    </span>
                                                    <small class="text-muted" style="font-size: 0.72rem;">{{ $entry->planned_date }}</small>
                                                </div>
                                                <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">{{ $entry->title }}</h6>
                                                <p class="text-muted mb-0" style="font-size: 0.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                                    Topic: {{ $entry->topic }}
                                                </p>
                                            </div>

                                            <form action="{{ route('caption.generate', $entry->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-outline-primary py-1 px-2" style="border-radius: 6px; font-size: 0.75rem;">
                                                    Generate
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-check2-circle text-success" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2" style="font-size: 0.85rem;">All calendar posts have generated captions!</p>
                                </div>
                            @endif
                        @else
                            <div class="alert alert-warning py-2 mb-0" style="font-size: 0.82rem; border-radius: 8px;">
                                <i class="bi bi-lock me-1"></i> Complete setup requirements to unlock generation.
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Generate Modal -->
    <div class="modal fade" id="bulkGenerateModal" tabindex="-1" aria-labelledby="bulkGenerateLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 16px;">
                <form action="{{ route('caption.bulk') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="generate">
                    <div class="modal-header border-0 pb-0 px-4 pt-4">
                        <h5 class="modal-title fw-bold" id="bulkGenerateLabel">Bulk Generate Captions</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body px-4 py-3">
                        <p class="text-muted" style="font-size: 0.88rem;">Select the content calendar posts you want to generate captions for in bulk:</p>
                        <div class="border rounded bg-light p-2" style="max-height: 250px; overflow-y: auto;">
                            @foreach($pendingCalendarEntries as $entry)
                                <div class="form-check py-1.5 border-bottom last-border-0">
                                    <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $entry->id }}" id="pendingCc{{ $entry->id }}" checked style="cursor: pointer;">
                                    <label class="form-check-label" for="pendingCc{{ $entry->id }}" style="cursor: pointer; font-size: 0.82rem;">
                                        <strong>[{{ $entry->platform }}]</strong> {{ $entry->title }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4 pt-0">
                        <button type="button" class="btn btn-light btn-sm px-3 py-2 fw-semibold" style="border-radius: 8px;" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary btn-sm px-3 py-2 fw-semibold" style="border-radius: 8px; background-color: #2563eb;">Generate Selected</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts to support copy/bulk checklist functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Copy Caption to Clipboard
            const copyButtons = document.querySelectorAll('.copy-btn');
            copyButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const card = this.closest('.card');
                    const textEl = card.querySelector('.copyable-text');
                    const ctaEl = card.querySelector('.bg-light');
                    let copyText = textEl.innerText;
                    
                    if (ctaEl) {
                        copyText += "\n\n" + ctaEl.innerText;
                    }

                    navigator.clipboard.writeText(copyText).then(() => {
                        const originalHtml = this.innerHTML;
                        this.innerHTML = '<i class="bi bi-check-lg text-success"></i> Copied!';
                        setTimeout(() => {
                            this.innerHTML = originalHtml;
                        }, 2000);
                    }).catch(err => {
                        console.error('Failed to copy: ', err);
                    });
                });
            });

            // Bulk Checkbox interactions
            const selectAllCheckbox = document.getElementById('select-all-captions');
            const checkboxes = document.querySelectorAll('.caption-checkbox');
            const bulkToolbar = document.getElementById('bulk-actions-toolbar');
            const selectedCountLabel = document.getElementById('selected-count');

            function updateToolbar() {
                const checked = document.querySelectorAll('.caption-checkbox:checked');
                selectedCountLabel.innerText = `${checked.length} selected`;
                
                if (checked.length > 0) {
                    bulkToolbar.style.setProperty('display', 'flex', 'important');
                } else {
                    bulkToolbar.style.setProperty('display', 'none', 'important');
                }
            }

            if (selectAllCheckbox) {
                selectAllCheckbox.addEventListener('change', function () {
                    checkboxes.forEach(cb => {
                        cb.checked = this.checked;
                    });
                    updateToolbar();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    if (!this.checked && selectAllCheckbox) {
                        selectAllCheckbox.checked = false;
                    }
                    updateToolbar();
                });
            });
        });
    </script>
</x-app-layout>
