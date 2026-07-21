<x-app-layout>
    <x-slot name="title">Content Calendar</x-slot>
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Content Calendar</li>
        </ol>
    </nav>

    <!-- Header & Quick Actions -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1" style="color: #0f172a; font-size: 1.8rem; letter-spacing: -0.5px;">AI Content Calendar</h1>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">
                Plan, optimize, and schedule your social media posts.
            </p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <!-- View toggler -->
            <div class="btn-group me-2" role="group" id="view-toggler" style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; padding: 2px; background: #f1f5f9;">
                <button type="button" class="btn btn-sm btn-light border-0 py-1.5 px-3 active-view" data-view="grid" style="border-radius: 6px; font-weight: 500; font-size: 0.85rem;">
                    <i class="bi bi-grid-3x3-gap me-1"></i> Grid
                </button>
                <button type="button" class="btn btn-sm btn-light border-0 py-1.5 px-3" data-view="table" style="border-radius: 6px; font-weight: 500; font-size: 0.85rem;">
                    <i class="bi bi-table me-1"></i> Table
                </button>
                <button type="button" class="btn btn-sm btn-light border-0 py-1.5 px-3" data-view="list" style="border-radius: 6px; font-weight: 500; font-size: 0.85rem;">
                    <i class="bi bi-list-task me-1"></i> List
                </button>
            </div>

            <!-- Manual post button -->
            <button class="btn btn-outline-primary btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#createEntryModal" style="border-radius: 8px; font-size: 0.85rem;">
                <i class="bi bi-plus-lg me-1"></i> Add Entry
            </button>

            <!-- Generate / Regenerate trigger -->
            @if($strategy)
                <button class="btn btn-primary btn-sm px-3 py-2 fw-semibold" data-bs-toggle="modal" data-bs-target="#generateCalendarModal" style="border-radius: 8px; font-size: 0.85rem; background-color: #2563eb; border-color: #2563eb;">
                    <i class="bi bi-magic me-1"></i> {{ count($entries) > 0 ? 'Regenerate Month' : 'Generate Month' }}
                </button>
            @endif
        </div>
    </div>

    <!-- Strategy Validation Check -->
    @if(!$strategy)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4 p-4" role="alert" style="border-radius: 12px; background-color: #fffbeb; border-left: 5px solid #d97706 !important;">
            <i class="bi bi-exclamation-triangle-fill text-warning me-3" style="font-size: 1.8rem;"></i>
            <div>
                <h4 class="alert-heading fw-bold mb-1" style="color: #78350f; font-size: 1.1rem;">Missing Marketing Strategy</h4>
                <p class="mb-0 text-muted" style="font-size: 0.88rem;">
                    GrowthOS requires an active Marketing Strategy before generating your content calendar. Please go to the 
                    <a href="{{ route('marketing-strategy') }}" class="fw-semibold text-decoration-underline" style="color: #78350f;">Marketing Strategy section</a> 
                    and run the analysis first.
                </p>
            </div>
        </div>
    @endif

    <!-- Search & Filters Toolbar -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; border: 1px solid #e2e8f0;">
        <div class="card-body p-3">
            <form action="{{ route('content-calendar') }}" method="GET" id="filter-form" class="row g-2 align-items-center">
                <!-- Month & Year navigation -->
                <div class="col-12 col-md-3 col-lg-2">
                    <div class="d-flex align-items-center gap-1">
                        <select name="month" class="form-select form-select-sm fw-semibold" style="border-radius: 8px;" onchange="this.form.submit()">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>
                                    {{ date("F", mktime(0, 0, 0, $m, 1)) }}
                                </option>
                            @endfor
                        </select>
                        <select name="year" class="form-select form-select-sm fw-semibold" style="border-radius: 8px;" onchange="this.form.submit()">
                            @for($y = 2024; $y <= 2030; $y++)
                                <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>
                                    {{ $y }}
                                </option>
                            @endfor
                        </select>
                    </div>
                </div>

                <!-- Custom Filters -->
                <div class="col-6 col-md-2 col-lg-1.5">
                    <select name="platform" class="form-select form-select-sm text-capitalize" style="border-radius: 8px;" onchange="this.form.submit()">
                        <option value="">Platform</option>
                        @foreach($platforms as $p)
                            <option value="{{ $p }}" {{ request('platform') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-1.5">
                    <select name="content_pillar" class="form-select form-select-sm text-capitalize" style="border-radius: 8px;" onchange="this.form.submit()">
                        <option value="">Pillar</option>
                        @foreach($pillars as $p)
                            <option value="{{ $p }}" {{ request('content_pillar') === $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-1.5">
                    <select name="campaign_name" class="form-select form-select-sm text-capitalize" style="border-radius: 8px;" onchange="this.form.submit()">
                        <option value="">Campaign</option>
                        @foreach($campaigns as $c)
                            <option value="{{ $c }}" {{ request('campaign_name') === $c ? 'selected' : '' }}>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-1.5">
                    <select name="goal" class="form-select form-select-sm text-capitalize" style="border-radius: 8px;" onchange="this.form.submit()">
                        <option value="">Goal</option>
                        @foreach($goals as $g)
                            <option value="{{ $g }}" {{ request('goal') === $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-6 col-md-2 col-lg-1.5">
                    <select name="status" class="form-select form-select-sm" style="border-radius: 8px;" onchange="this.form.submit()">
                        <option value="">Status</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Text Search -->
                <div class="col-12 col-md-3 col-lg-2 ms-auto">
                    <div class="input-group input-group-sm">
                        <input type="text" name="search" class="form-control" placeholder="Search entries..." value="{{ request('search') }}" style="border-radius: 8px 0 0 8px;">
                        <button type="submit" class="btn btn-primary" style="border-radius: 0 8px 8px 0; background-color: #2563eb;"><i class="bi bi-search"></i></button>
                    </div>
                </div>

                <!-- Clear filters -->
                @if(request()->anyFilled(['platform', 'content_pillar', 'campaign_name', 'goal', 'status', 'search']))
                    <div class="col-auto">
                        <a href="{{ route('content-calendar', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="btn btn-sm btn-link text-decoration-none text-danger fw-semibold" style="font-size: 0.82rem; padding: 0;">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Alert Messaging -->
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show p-3 mb-4" role="alert" style="border-radius: 8px; border-left: 4px solid #16a34a !important; font-size: 0.88rem;">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning border-0 shadow-sm alert-dismissible fade show p-3 mb-4" role="alert" style="border-radius: 8px; border-left: 4px solid #d97706 !important; font-size: 0.88rem;">
            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm alert-dismissible fade show p-3 mb-4" role="alert" style="border-radius: 8px; border-left: 4px solid #dc2626 !important; font-size: 0.88rem;">
            <i class="bi bi-x-circle-fill me-2 text-danger"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Empty State -->
    @if(count($entries) === 0)
        <div class="card border-0 shadow-sm text-center py-5 mb-4" style="border-radius: 12px;">
            <div class="card-body py-5">
                <div style="width: 72px; height: 72px; background: #f1f5f9; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                    <i class="bi bi-calendar-x text-secondary" style="font-size: 2.2rem;"></i>
                </div>
                <h3 class="fw-bold mb-2" style="color: #0f172a; font-size: 1.4rem;">No Calendar Entries Found</h3>
                <p class="text-muted mx-auto mb-4" style="max-width: 420px; font-size: 0.9rem;">
                    We couldn't find any post entries for this month. 
                    @if($strategy)
                        Generate a complete monthly schedule based on your brand strategy!
                    @else
                        Create your Brand Profile and Marketing Strategy first, then generate a calendar.
                    @endif
                </p>
                @if($strategy)
                    <button class="btn btn-primary btn-sm px-4 py-2.5 fw-semibold" data-bs-toggle="modal" data-bs-target="#generateCalendarModal" style="border-radius: 8px; background-color: #2563eb;">
                        <i class="bi bi-magic me-1"></i> Generate Calendar
                    </button>
                @endif
            </div>
        </div>
    @else

    <!-- Bulk Action Toolbar -->
    <div id="bulk-toolbar" class="d-none bg-dark text-white p-3 mb-3 d-flex align-items-center justify-content-between shadow" style="border-radius: 10px; animation: slideIn 0.2s ease-out;">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary" id="checked-count" style="font-size: 0.85rem;">0</span>
            <span style="font-size: 0.88rem; font-weight: 500;">items selected</span>
        </div>
        <form action="{{ route('content-calendar.bulk') }}" method="POST" id="bulk-action-form" class="d-flex align-items-center gap-2">
            @csrf
            <div id="bulk-inputs"></div>
            <select name="action" class="form-select form-select-sm bg-dark text-white border-secondary" style="border-radius: 6px; font-size: 0.82rem; width: 180px;" required>
                <option value="">Choose bulk action...</option>
                <option value="status_Draft">Set status: Draft</option>
                <option value="status_Approved">Set status: Approved</option>
                <option value="status_Rejected">Set status: Rejected</option>
                <option value="status_Scheduled">Set status: Scheduled</option>
                <option value="status_Published">Set status: Published</option>
                <option value="delete">Delete selected entries</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary fw-semibold px-3" style="border-radius: 6px; background-color: #2563eb; font-size: 0.82rem;">
                Apply Action
            </button>
        </form>
    </div>

    <!-- VIEW 1: MONTHLY GRID VIEW -->
    <div class="view-panel" id="calendar-grid-view">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
            <div class="card-body p-0">
                <!-- Grid Days of Week Headers -->
                <div class="row g-0 text-center fw-bold bg-light py-3 border-bottom border-secondary-subtle" style="font-size: 0.82rem; color: #475569; letter-spacing: 0.5px;">
                    <div class="col" style="width: 14.28%;">MON</div>
                    <div class="col" style="width: 14.28%;">TUE</div>
                    <div class="col" style="width: 14.28%;">WED</div>
                    <div class="col" style="width: 14.28%;">THU</div>
                    <div class="col" style="width: 14.28%;">FRI</div>
                    <div class="col" style="width: 14.28%;">SAT</div>
                    <div class="col" style="width: 14.28%;">SUN</div>
                </div>

                <!-- Calendar grid builder -->
                @php
                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $selectedMonth, $selectedYear);
                    $firstDayOfWeek = (int) date('N', mktime(0, 0, 0, $selectedMonth, 1, $selectedYear));
                    $startPadding = $firstDayOfWeek - 1; 
                    $totalCells = $daysInMonth + $startPadding;
                    $weeksCount = ceil($totalCells / 7);
                @endphp

                <div class="calendar-grid-container">
                    @for($w = 0; $w < $weeksCount; $w++)
                        <div class="row g-0 border-bottom">
                            @for($d = 1; $d <= 7; $d++)
                                @php
                                    $cellIndex = ($w * 7) + $d;
                                    $dayNum = $cellIndex - $startPadding;
                                    $isValidDay = ($dayNum > 0 && $dayNum <= $daysInMonth);
                                    $dayStr = $isValidDay ? sprintf("%04d-%02d-%02d", $selectedYear, $selectedMonth, $dayNum) : '';
                                    
                                    $dayEntries = $isValidDay ? $entries->filter(function($entry) use ($dayStr) {
                                        return $entry->planned_date === $dayStr;
                                    }) : collect();
                                @endphp

                                <div class="col col-cell p-2 d-flex flex-column justify-content-between border-end {{ $isValidDay ? 'bg-white' : 'bg-light-subtle' }}" style="width: 14.28%; min-height: 120px; border-color: #f1f5f9;">
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold {{ $isValidDay ? 'text-dark' : 'text-muted opacity-25' }}" style="font-size: 0.9rem;">
                                                {{ $isValidDay ? $dayNum : '' }}
                                            </span>
                                            @if($isValidDay && date('Y-m-d') === $dayStr)
                                                <span class="badge rounded-pill bg-danger" style="font-size: 0.65rem;">TODAY</span>
                                            @endif
                                        </div>

                                        <!-- Entries list for day -->
                                        @foreach($dayEntries as $entry)
                                            <div class="post-grid-card p-1.5 mb-1.5 shadow-xs border" style="border-radius: 6px; font-size: 0.72rem; cursor: pointer; border-left: 3px solid {{ $entry->platform === 'LinkedIn' ? '#0077b5' : ($entry->platform === 'Twitter' ? '#1da1f2' : '#e1306c') }} !important; background: #fafafa;" 
                                                 onclick="showViewModal({{ json_encode($entry) }})">
                                                <div class="d-flex justify-content-between mb-0.5">
                                                    <span class="badge text-capitalize bg-light text-dark text-xxs font-monospace">{{ $entry->platform }}</span>
                                                    <span class="badge text-xxs bg-{{ $entry->status === 'Approved' ? 'success' : ($entry->status === 'Draft' ? 'secondary' : ($entry->status === 'Scheduled' ? 'primary' : 'warning')) }}">{{ $entry->status }}</span>
                                                </div>
                                                <div class="fw-semibold text-truncate">{{ $entry->title }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endfor
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- VIEW 2: TABLE VIEW -->
    <div class="view-panel d-none" id="calendar-table-view">
        <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden; border: 1px solid #e2e8f0;">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0" style="font-size: 0.85rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3" style="width: 40px;">
                                <input type="checkbox" id="check-all" class="form-check-input">
                            </th>
                            <th class="py-3">Date</th>
                            <th class="py-3">Platform</th>
                            <th class="py-3">Title</th>
                            <th class="py-3">Content Pillar</th>
                            <th class="py-3">Campaign</th>
                            <th class="py-3">Priority</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($entries as $entry)
                            <tr id="row-{{ $entry->id }}">
                                <td class="px-4 py-3">
                                    <input type="checkbox" class="form-check-input bulk-checkbox" value="{{ $entry->id }}" onchange="updateBulkToolbar()">
                                </td>
                                <td class="fw-semibold">{{ date('M j, Y', strtotime($entry->planned_date)) }}</td>
                                <td>
                                    <span class="badge bg-light text-dark font-monospace text-capitalize">{{ $entry->platform }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark" style="max-width: 260px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="{{ $entry->title }}">{{ $entry->title }}</div>
                                    <small class="text-muted d-block text-truncate" style="max-width: 260px;">{{ $entry->topic }}</small>
                                </td>
                                <td>{{ $entry->content_pillar }}</td>
                                <td>{{ $entry->campaign_name }}</td>
                                <td>
                                    <span class="badge text-xxs bg-{{ $entry->priority === 'High' ? 'danger-subtle text-danger' : ($entry->priority === 'Medium' ? 'warning-subtle text-warning-emphasis' : 'secondary-subtle text-secondary') }}">
                                        {{ $entry->priority }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-{{ $entry->status === 'Approved' ? 'success' : ($entry->status === 'Draft' ? 'secondary' : ($entry->status === 'Scheduled' ? 'primary' : 'warning')) }}">
                                        {{ $entry->status }}
                                    </span>
                                </td>
                                <td class="text-end px-4">
                                    <div class="d-flex justify-content-end gap-1.5">
                                        <button class="btn btn-xs btn-outline-secondary" onclick="showViewModal({{ json_encode($entry) }})" title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button class="btn btn-xs btn-outline-primary" onclick="showEditModal({{ json_encode($entry) }})" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('content-calendar.duplicate', $entry->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-outline-info" title="Duplicate">
                                                <i class="bi bi-copy"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('content-calendar.destroy', $entry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- VIEW 3: LIST VIEW -->
    <div class="view-panel d-none" id="calendar-list-view">
        <div class="d-flex flex-column gap-3">
            @php
                $groupedEntries = $entries->groupBy('planned_date');
            @endphp

            @foreach($groupedEntries as $date => $dayPosts)
                <div class="card border-0 shadow-sm" style="border-radius: 12px; border: 1px solid #e2e8f0;">
                    <div class="card-header bg-light-subtle border-bottom d-flex justify-content-between align-items-center py-3 px-4">
                        <h5 class="fw-bold mb-0 text-primary" style="font-size: 1.05rem;">
                            <i class="bi bi-calendar-check me-2"></i> {{ date('l, M j, Y', strtotime($date)) }}
                        </h5>
                        <span class="badge bg-secondary rounded-pill font-monospace" style="font-size: 0.72rem;">{{ count($dayPosts) }} {{ Str::plural('Post', count($dayPosts)) }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($dayPosts as $entry)
                                <div class="list-group-item p-4 d-flex justify-content-between align-items-start align-items-lg-center flex-column flex-lg-row gap-3">
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Platform graphic dot -->
                                        <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #f1f5f9; flex-shrink: 0;">
                                            <i class="bi bi-{{ strtolower($entry->platform) === 'instagram' ? 'instagram text-danger' : (strtolower($entry->platform) === 'linkedin' ? 'linkedin text-primary' : 'twitter text-info') }}" style="font-size: 1.2rem;"></i>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                                <h6 class="fw-bold mb-0" style="color:#0f172a; font-size: 0.95rem;">{{ $entry->title }}</h6>
                                                <span class="badge text-capitalize bg-light text-dark font-monospace" style="font-size: 0.68rem;">{{ $entry->platform }}</span>
                                                <span class="badge text-xxs bg-{{ $entry->status === 'Approved' ? 'success' : ($entry->status === 'Draft' ? 'secondary' : ($entry->status === 'Scheduled' ? 'primary' : 'warning')) }}">{{ $entry->status }}</span>
                                            </div>
                                            <p class="mb-2 text-muted" style="font-size: 0.82rem;">{{ $entry->topic }}</p>
                                            
                                            <div class="d-flex align-items-center gap-3 flex-wrap" style="font-size: 0.78rem; color: #64748b;">
                                                <span><i class="bi bi-tag-fill me-1"></i> Pillar: <strong>{{ $entry->content_pillar }}</strong></span>
                                                <span><i class="bi bi-bullseye me-1"></i> Goal: <strong>{{ $entry->goal }}</strong></span>
                                                <span><i class="bi bi-briefcase me-1"></i> Campaign: <strong>{{ $entry->campaign_name }}</strong></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 w-100 w-lg-auto justify-content-end border-top pt-3 pt-lg-0 border-lg-0 mt-2 mt-lg-0" style="border-color: #f1f5f9;">
                                        <button class="btn btn-xs btn-outline-secondary" onclick="showViewModal({{ json_encode($entry) }})"><i class="bi bi-eye me-1"></i> View</button>
                                        <button class="btn btn-xs btn-outline-primary" onclick="showEditModal({{ json_encode($entry) }})"><i class="bi bi-pencil me-1"></i> Edit</button>
                                        <form action="{{ route('content-calendar.duplicate', $entry->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-xs btn-outline-info"><i class="bi bi-copy me-1"></i> Clone</button>
                                        </form>
                                        <form action="{{ route('content-calendar.destroy', $entry->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this post?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-xs btn-outline-danger"><i class="bi bi-trash me-1"></i> Delete</button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    @endif
</div>

<!-- ==============================================
     MODAL COMPONENT: GENERATE CALENDAR
     ============================================== -->
@if($strategy)
<div class="modal fade" id="generateCalendarModal" tabindex="-1" aria-labelledby="generateCalendarModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('content-calendar.generate') }}" method="POST">
            @csrf
            <div class="modal-content" style="border-radius: 12px; border: 0;">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="generateCalendarModalLabel" style="color: #0f172a;"><i class="bi bi-magic me-2 text-primary"></i> Generate AI Calendar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if(count($entries) > 0)
                        <div class="alert alert-warning border-0 p-3 mb-3" style="border-radius: 8px; background-color: #fffbeb; font-size: 0.85rem; border-left: 4px solid #d97706 !important;">
                            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>
                            <strong>Regeneration Warning!</strong> Generating a new calendar for this month will permanently delete and replace the <strong>{{ count($entries) }}</strong> existing entries for {{ date("F", mktime(0, 0, 0, $selectedMonth, 1)) }} {{ $selectedYear }}. This action cannot be undone.
                        </div>
                    @endif
                    <p class="text-muted" style="font-size: 0.88rem;">
                        Select the target month and year. Our AI Content Engine will distribute posts organically, matching your marketing strategy goals.
                    </p>
                    <div class="row g-2 mt-2">
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Month</label>
                            <select name="month" class="form-select" style="border-radius: 8px;" required>
                                @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ $selectedMonth === $m ? 'selected' : '' }}>
                                        {{ date("F", mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Year</label>
                            <select name="year" class="form-select" style="border-radius: 8px;" required>
                                @for($y = 2024; $y <= 2030; $y++)
                                    <option value="{{ $y }}" {{ $selectedYear === $y ? 'selected' : '' }}>
                                        {{ $y }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold" style="background-color: #2563eb; border-color: #2563eb;">
                        {{ count($entries) > 0 ? 'Confirm & Replace' : 'Generate' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

<!-- ==============================================
     MODAL COMPONENT: CREATE CALENDAR ENTRY
     ============================================== -->
<div class="modal fade" id="createEntryModal" tabindex="-1" aria-labelledby="createEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('content-calendar.store') }}" method="POST">
            @csrf
            <div class="modal-content" style="border-radius: 12px; border: 0;">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="createEntryModalLabel" style="color: #0f172a;"><i class="bi bi-calendar-plus me-2 text-primary"></i> Add Calendar Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Working Title</label>
                            <input type="text" name="title" class="form-control" placeholder="e.g. Save 10 Hours per week" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Topic / Description</label>
                            <textarea name="topic" class="form-control" rows="3" placeholder="Write a short summary of this post..." style="border-radius: 8px;" required></textarea>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Platform</label>
                            <select name="platform" class="form-select text-capitalize" style="border-radius: 8px;" required>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Twitter">Twitter</option>
                                <option value="Instagram">Instagram</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Content Pillar</label>
                            <input type="text" name="content_pillar" class="form-control" placeholder="e.g. Productivity" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Campaign Name</label>
                            <input type="text" name="campaign_name" class="form-control" placeholder="e.g. Launch Campaign" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Post Goal</label>
                            <input type="text" name="goal" class="form-control" placeholder="e.g. Lead Generation" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Content Type</label>
                            <input type="text" name="content_type" class="form-control" placeholder="e.g. Educational" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Post Format</label>
                            <input type="text" name="post_format" class="form-control" placeholder="e.g. Text, Reel, Carousel" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Planned Date</label>
                            <input type="date" name="planned_date" class="form-control" value="{{ sprintf('%04d-%02d-01', $selectedYear, $selectedMonth) }}" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Planned Time</label>
                            <input type="time" name="planned_time" class="form-control" value="09:00" style="border-radius: 8px;">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Priority</label>
                            <select name="priority" class="form-select" style="border-radius: 8px;" required>
                                <option value="Low">Low</option>
                                <option value="Medium" selected>Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Status</label>
                            <select name="status" class="form-select" style="border-radius: 8px;" required>
                                <option value="Draft" selected>Draft</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Scheduled">Scheduled</option>
                                <option value="Published">Published</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Notes / Suggested CTA</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="Add custom notes or calls-to-action..." style="border-radius: 8px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold" style="background-color: #2563eb; border-color: #2563eb;">Add Post</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL COMPONENT: EDIT CALENDAR ENTRY
     ============================================== -->
<div class="modal fade" id="editEntryModal" tabindex="-1" aria-labelledby="editEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form action="" method="POST" id="edit-form">
            @csrf
            @method('PUT')
            <div class="modal-content" style="border-radius: 12px; border: 0;">
                <div class="modal-header border-bottom">
                    <h5 class="modal-title fw-bold" id="editEntryModalLabel" style="color: #0f172a;"><i class="bi bi-pencil-square me-2 text-primary"></i> Edit Calendar Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Working Title</label>
                            <input type="text" name="title" id="edit-title" class="form-control" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Topic / Description</label>
                            <textarea name="topic" id="edit-topic" class="form-control" rows="3" style="border-radius: 8px;" required></textarea>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Platform</label>
                            <select name="platform" id="edit-platform" class="form-select text-capitalize" style="border-radius: 8px;" required>
                                <option value="LinkedIn">LinkedIn</option>
                                <option value="Twitter">Twitter</option>
                                <option value="Instagram">Instagram</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Content Pillar</label>
                            <input type="text" name="content_pillar" id="edit-content_pillar" class="form-control" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Campaign Name</label>
                            <input type="text" name="campaign_name" id="edit-campaign_name" class="form-control" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Post Goal</label>
                            <input type="text" name="goal" id="edit-goal" class="form-control" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Content Type</label>
                            <input type="text" name="content_type" id="edit-content_type" class="form-control" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Post Format</label>
                            <input type="text" name="post_format" id="edit-post_format" class="form-control" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Planned Date</label>
                            <input type="date" name="planned_date" id="edit-planned_date" class="form-control" style="border-radius: 8px;" required>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Planned Time</label>
                            <input type="time" name="planned_time" id="edit-planned_time" class="form-control" style="border-radius: 8px;">
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Priority</label>
                            <select name="priority" id="edit-priority" class="form-select" style="border-radius: 8px;" required>
                                <option value="Low">Low</option>
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Status</label>
                            <select name="status" id="edit-status" class="form-select" style="border-radius: 8px;" required>
                                <option value="Draft">Draft</option>
                                <option value="Approved">Approved</option>
                                <option value="Rejected">Rejected</option>
                                <option value="Scheduled">Scheduled</option>
                                <option value="Published">Published</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold" style="font-size: 0.78rem;">Notes / Suggested CTA</label>
                            <textarea name="notes" id="edit-notes" class="form-control" rows="2" style="border-radius: 8px;"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top p-3 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-sm btn-primary px-4 fw-semibold" style="background-color: #2563eb; border-color: #2563eb;">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ==============================================
     MODAL COMPONENT: DETAILED VIEW MODAL
     ============================================== -->
<div class="modal fade" id="viewEntryModal" tabindex="-1" aria-labelledby="viewEntryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: 0;">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold" id="viewEntryModalLabel" style="color: #0f172a;"><i class="bi bi-card-text me-2 text-primary"></i> Calendar Entry Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge bg-light text-dark font-monospace text-capitalize" id="view-platform" style="font-size: 0.78rem;">Platform</span>
                    <span class="badge text-xxs" id="view-priority">Priority</span>
                    <span class="badge rounded-pill" id="view-status">Status</span>
                </div>
                <h5 class="fw-bold mb-2" id="view-title" style="color: #0f172a; font-size: 1.15rem;">Title</h5>
                <p class="mb-4 text-muted" id="view-topic" style="font-size: 0.88rem; line-height: 1.5;">Topic Description...</p>

                <div class="row g-3 border-top pt-3" style="font-size: 0.82rem; color: #475569;">
                    <div class="col-6">
                        <i class="bi bi-calendar3 me-1"></i> Date: <strong id="view-date" class="text-dark">Date</strong>
                    </div>
                    <div class="col-6">
                        <i class="bi bi-clock me-1"></i> Time: <strong id="view-time" class="text-dark">Time</strong>
                    </div>
                    <div class="col-6">
                        <i class="bi bi-tag me-1"></i> Pillar: <strong id="view-pillar" class="text-dark">Pillar</strong>
                    </div>
                    <div class="col-6">
                        <i class="bi bi-bullseye me-1"></i> Goal: <strong id="view-goal" class="text-dark">Goal</strong>
                    </div>
                    <div class="col-6">
                        <i class="bi bi-briefcase me-1"></i> Campaign: <strong id="view-campaign" class="text-dark">Campaign</strong>
                    </div>
                    <div class="col-6">
                        <i class="bi bi-gear me-1"></i> Format: <strong id="view-format" class="text-dark">Format</strong>
                    </div>
                </div>

                <div class="border-top pt-3 mt-3" id="view-notes-container" style="font-size: 0.82rem;">
                    <div class="fw-semibold text-secondary mb-1">Notes / CTA Guidelines:</div>
                    <div class="p-3 bg-light rounded text-muted font-monospace" id="view-notes" style="font-size: 0.78rem; white-space: pre-wrap;">Notes...</div>
                </div>

                <div class="border-top pt-3 mt-3 text-muted text-xxs font-monospace">
                    Generated by: <span id="view-provider">provider</span> (<span id="view-model">model</span>)
                </div>
            </div>
            <div class="modal-footer border-top p-3 d-flex justify-content-end gap-1.5">
                <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-sm btn-primary" id="view-btn-edit" onclick="dismissAndEdit()"><i class="bi bi-pencil me-1"></i> Edit</button>
            </div>
        </div>
    </div>
</div>

<script>
    // Selected View tracking & LocalStorage save
    document.addEventListener('DOMContentLoaded', function() {
        const toggler = document.getElementById('view-toggler');
        const viewButtons = toggler.querySelectorAll('button');
        const panels = document.querySelectorAll('.view-panel');

        // Check cache
        const cachedView = localStorage.getItem('growthos_calendar_view') || 'grid';
        setView(cachedView);

        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const selectedView = this.getAttribute('data-view');
                setView(selectedView);
                localStorage.setItem('growthos_calendar_view', selectedView);
            });
        });

        function setView(viewName) {
            viewButtons.forEach(b => {
                if (b.getAttribute('data-view') === viewName) {
                    b.classList.add('btn-primary', 'text-white');
                    b.classList.remove('btn-light');
                } else {
                    b.classList.remove('btn-primary', 'text-white');
                    b.classList.add('btn-light');
                }
            });

            panels.forEach(p => {
                if (p.getAttribute('id') === `calendar-${viewName}-view`) {
                    p.classList.remove('d-none');
                } else {
                    p.classList.add('d-none');
                }
            });
        }

        // Table check-all handler
        const checkAll = document.getElementById('check-all');
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const checkboxes = document.querySelectorAll('.bulk-checkbox');
                checkboxes.forEach(c => {
                    c.checked = this.checked;
                });
                updateBulkToolbar();
            });
        }
    });

    // Populate Detailed View Modal
    let activeViewEntry = null;
    function showViewModal(entry) {
        activeViewEntry = entry;
        document.getElementById('view-title').innerText = entry.title;
        document.getElementById('view-topic').innerText = entry.topic;
        document.getElementById('view-platform').innerText = entry.platform;
        document.getElementById('view-date').innerText = entry.planned_date;
        document.getElementById('view-time').innerText = entry.planned_time || '09:00:00';
        document.getElementById('view-pillar').innerText = entry.content_pillar;
        document.getElementById('view-goal').innerText = entry.goal;
        document.getElementById('view-campaign').innerText = entry.campaign_name;
        document.getElementById('view-format').innerText = `${entry.content_type} / ${entry.post_format}`;
        
        // Priority
        const priorityBadge = document.getElementById('view-priority');
        priorityBadge.innerText = entry.priority;
        priorityBadge.className = "badge text-xxs ";
        if (entry.priority === 'High') priorityBadge.classList.add('bg-danger-subtle', 'text-danger');
        else if (entry.priority === 'Medium') priorityBadge.classList.add('bg-warning-subtle', 'text-warning-emphasis');
        else priorityBadge.classList.add('bg-secondary-subtle', 'text-secondary');

        // Status
        const statusBadge = document.getElementById('view-status');
        statusBadge.innerText = entry.status;
        statusBadge.className = "badge rounded-pill ";
        if (entry.status === 'Approved') statusBadge.classList.add('bg-success');
        else if (entry.status === 'Draft') statusBadge.classList.add('bg-secondary');
        else if (entry.status === 'Scheduled') statusBadge.classList.add('bg-primary');
        else statusBadge.classList.add('bg-warning');

        // Notes
        const notesContainer = document.getElementById('view-notes-container');
        if (entry.notes) {
            notesContainer.classList.remove('d-none');
            document.getElementById('view-notes').innerText = entry.notes;
        } else {
            notesContainer.classList.add('d-none');
        }

        // Provider info
        document.getElementById('view-provider').innerText = entry.provider || 'unknown';
        document.getElementById('view-model').innerText = entry.model || 'unknown';

        const modal = new bootstrap.Modal(document.getElementById('viewEntryModal'));
        modal.show();
    }

    function dismissAndEdit() {
        const viewModal = bootstrap.Modal.getInstance(document.getElementById('viewEntryModal'));
        if (viewModal) viewModal.hide();
        if (activeViewEntry) {
            setTimeout(() => {
                showEditModal(activeViewEntry);
            }, 300);
        }
    }

    // Populate Edit Modal
    function showEditModal(entry) {
        document.getElementById('edit-title').value = entry.title;
        document.getElementById('edit-topic').value = entry.topic;
        document.getElementById('edit-platform').value = entry.platform;
        document.getElementById('edit-content_pillar').value = entry.content_pillar;
        document.getElementById('edit-campaign_name').value = entry.campaign_name;
        document.getElementById('edit-goal').value = entry.goal;
        document.getElementById('edit-content_type').value = entry.content_type;
        document.getElementById('edit-post_format').value = entry.post_format;
        document.getElementById('edit-planned_date').value = entry.planned_date;
        document.getElementById('edit-planned_time').value = entry.planned_time ? entry.planned_time.substring(0, 5) : '09:00';
        document.getElementById('edit-priority').value = entry.priority;
        document.getElementById('edit-status').value = entry.status;
        document.getElementById('edit-notes').value = entry.notes || '';

        // Form action url
        document.getElementById('edit-form').action = `/content-calendar/${entry.id}`;

        const modal = new bootstrap.Modal(document.getElementById('editEntryModal'));
        modal.show();
    }

    // Bulk Select toolbar manager
    function updateBulkToolbar() {
        const checkboxes = document.querySelectorAll('.bulk-checkbox:checked');
        const toolbar = document.getElementById('bulk-toolbar');
        const countBadge = document.getElementById('checked-count');
        const inputsContainer = document.getElementById('bulk-inputs');

        if (checkboxes.length > 0) {
            toolbar.classList.remove('d-none');
            countBadge.innerText = checkboxes.length;

            // Generate hidden inputs inside bulk form
            let html = '';
            checkboxes.forEach(c => {
                html += `<input type="hidden" name="ids[]" value="${c.value}">`;
            });
            inputsContainer.innerHTML = html;
        } else {
            toolbar.classList.add('d-none');
            inputsContainer.innerHTML = '';
        }
    }
</script>

<style>
    .col-cell {
        transition: background-color 0.15s ease;
    }
    .col-cell:hover {
        background-color: #f8fafc !important;
    }
    .post-grid-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .post-grid-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
    }
    .text-xxs {
        font-size: 0.62rem;
        padding: 0.18em 0.5em;
    }
    .text-xxs.font-monospace {
        font-size: 0.6rem;
    }
    .btn-xs {
        padding: 0.25rem 0.5rem;
        font-size: 0.72rem;
        border-radius: 6px;
    }
    @keyframes slideIn {
        from { transform: translateY(15px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
</style>
</x-app-layout>
