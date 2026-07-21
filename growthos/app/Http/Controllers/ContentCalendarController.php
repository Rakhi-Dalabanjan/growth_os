<?php

namespace App\Http\Controllers;

use App\Models\ContentCalendar;
use App\Models\MarketingStrategy;
use App\Services\ContentCalendarService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Exception;

class ContentCalendarController extends Controller
{
    /**
     * Render the content calendar page with filters and grid/table/list views.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->route('organization.create')
                ->with('warning', 'Please create an organization first.');
        }

        // Get filter inputs or set defaults
        $selectedMonth = (int) $request->input('month', now()->month);
        $selectedYear = (int) $request->input('year', now()->year);

        // Fetch strategy to check availability
        $strategy = MarketingStrategy::where('organization_id', $organization->id)->first();

        // Build filterable query
        $query = ContentCalendar::where('organization_id', $organization->id)
            ->where('month', $selectedMonth)
            ->where('year', $selectedYear);

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        if ($request->filled('content_pillar')) {
            $query->where('content_pillar', $request->content_pillar);
        }
        if ($request->filled('campaign_name')) {
            $query->where('campaign_name', $request->campaign_name);
        }
        if ($request->filled('goal')) {
            $query->where('goal', $request->goal);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('topic', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderBy('planned_date')->orderBy('planned_time')->get();

        // Distinct filter options for dropdowns (scoped to organization and current month/year selection)
        $distinctQuery = ContentCalendar::where('organization_id', $organization->id);
        $platforms = (clone $distinctQuery)->distinct()->pluck('platform');
        $pillars = (clone $distinctQuery)->distinct()->pluck('content_pillar');
        $campaigns = (clone $distinctQuery)->distinct()->pluck('campaign_name');
        $goals = (clone $distinctQuery)->distinct()->pluck('goal');
        $statuses = ['Draft', 'Approved', 'Rejected', 'Scheduled', 'Published'];

        return view('content-calendar.index', compact(
            'entries',
            'strategy',
            'selectedMonth',
            'selectedYear',
            'platforms',
            'pillars',
            'campaigns',
            'goals',
            'statuses'
        ));
    }

    /**
     * Trigger AI calendar generation for the specified month and year.
     */
    public function generate(Request $request, ContentCalendarService $service)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year'  => 'required|integer|min:2024|max:2035',
        ]);

        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $strategy = MarketingStrategy::where('organization_id', $organization->id)->first();
        if (!$strategy) {
            return redirect()->back()->with('warning', 'An active Marketing Strategy is required before generating a content calendar. Please generate a strategy first.');
        }

        try {
            $month = (int) $request->month;
            $year = (int) $request->year;

            $service->generate($strategy, $month, $year);

            $monthName = date("F", mktime(0, 0, 0, $month, 1));
            return redirect()->route('content-calendar', ['month' => $month, 'year' => $year])
                ->with('success', "AI Content Calendar for {$monthName} {$year} generated successfully!");
        } catch (Exception $e) {
            Log::error("Failed to generate Content Calendar: " . $e->getMessage());
            return redirect()->back()->with('error', "Failed to generate Content Calendar: " . $e->getMessage());
        }
    }

    /**
     * Store a manually created content calendar entry.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|string|max:255',
            'topic'          => 'required|string',
            'platform'       => 'required|string',
            'content_pillar' => 'required|string',
            'campaign_name'  => 'required|string',
            'goal'           => 'required|string',
            'content_type'   => 'required|string',
            'post_format'    => 'required|string',
            'planned_date'   => 'required|date',
            'planned_time'   => 'nullable|string',
            'priority'       => 'required|string|in:Low,Medium,High',
            'status'         => 'required|string|in:Draft,Approved,Rejected,Scheduled,Published',
            'notes'          => 'nullable|string',
        ]);

        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $strategy = MarketingStrategy::where('organization_id', $organization->id)->first();
        if (!$strategy) {
            return redirect()->back()->with('error', 'An active Marketing Strategy is required.');
        }

        // Determine month and year from selected date
        $time = strtotime($request->planned_date);
        $month = (int) date('n', $time);
        $year = (int) date('Y', $time);

        ContentCalendar::create([
            'organization_id'       => $organization->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => $month,
            'year'                  => $year,
            'platform'              => $request->platform,
            'title'                 => $request->title,
            'topic'                 => $request->topic,
            'content_pillar'        => $request->content_pillar,
            'campaign_name'         => $request->campaign_name,
            'goal'                  => $request->goal,
            'content_type'          => $request->content_type,
            'post_format'           => $request->post_format,
            'status'                => $request->status,
            'planned_date'          => $request->planned_date,
            'planned_time'          => $request->planned_time ?: '09:00:00',
            'priority'              => $request->priority,
            'notes'                 => $request->notes,
            'provider'              => 'manual',
            'model'                 => 'manual',
            'generated_at'          => now(),
        ]);

        return redirect()->back()->with('success', 'Calendar entry added successfully!');
    }

    /**
     * Update an existing content calendar entry.
     */
    public function update(Request $request, $id)
    {
        $entry = ContentCalendar::findOrFail($id);
        Gate::authorize('update', $entry);

        $request->validate([
            'title'          => 'required|string|max:255',
            'topic'          => 'required|string',
            'platform'       => 'required|string',
            'content_pillar' => 'required|string',
            'campaign_name'  => 'required|string',
            'goal'           => 'required|string',
            'content_type'   => 'required|string',
            'post_format'    => 'required|string',
            'planned_date'   => 'required|date',
            'planned_time'   => 'nullable|string',
            'priority'       => 'required|string|in:Low,Medium,High',
            'status'         => 'required|string|in:Draft,Approved,Rejected,Scheduled,Published',
            'notes'          => 'nullable|string',
        ]);

        // Recalculate month/year in case planned_date changes
        $time = strtotime($request->planned_date);
        $month = (int) date('n', $time);
        $year = (int) date('Y', $time);

        $entry->update([
            'month'          => $month,
            'year'           => $year,
            'platform'       => $request->platform,
            'title'          => $request->title,
            'topic'          => $request->topic,
            'content_pillar' => $request->content_pillar,
            'campaign_name'  => $request->campaign_name,
            'goal'           => $request->goal,
            'content_type'   => $request->content_type,
            'post_format'    => $request->post_format,
            'status'         => $request->status,
            'planned_date'   => $request->planned_date,
            'planned_time'   => $request->planned_time ?: '09:00:00',
            'priority'       => $request->priority,
            'notes'          => $request->notes,
        ]);

        return redirect()->back()->with('success', 'Calendar entry updated successfully!');
    }

    /**
     * Delete a content calendar entry.
     */
    public function destroy($id)
    {
        $entry = ContentCalendar::findOrFail($id);
        Gate::authorize('delete', $entry);

        $entry->delete();

        return redirect()->back()->with('success', 'Calendar entry deleted successfully!');
    }

    /**
     * Duplicate an existing calendar entry.
     */
    public function duplicate($id)
    {
        $entry = ContentCalendar::findOrFail($id);
        Gate::authorize('view', $entry);

        $clone = $entry->replicate();
        $clone->title = $entry->title . ' (Copy)';
        $clone->status = 'Draft'; // Duplicated entries reset to Draft
        $clone->save();

        return redirect()->back()->with('success', 'Calendar entry duplicated successfully!');
    }

    /**
     * Handle bulk status updates or deletions.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids'    => 'required|array',
            'ids.*'  => 'integer|exists:content_calendars,id',
            'action' => 'required|string|in:delete,status_Draft,status_Approved,status_Rejected,status_Scheduled,status_Published',
        ]);

        $ids = $request->ids;
        $action = $request->action;

        if ($action === 'delete') {
            $deletedCount = 0;
            foreach ($ids as $id) {
                $entry = ContentCalendar::findOrFail($id);
                if (Gate::allows('delete', $entry)) {
                    $entry->delete();
                    $deletedCount++;
                }
            }
            return redirect()->back()->with('success', "Successfully deleted {$deletedCount} selected posts.");
        }

        if (str_starts_with($action, 'status_')) {
            $status = substr($action, 7);
            $updatedCount = 0;
            foreach ($ids as $id) {
                $entry = ContentCalendar::findOrFail($id);
                if (Gate::allows('update', $entry)) {
                    $entry->update(['status' => $status]);
                    $updatedCount++;
                }
            }
            return redirect()->back()->with('success', "Successfully updated status to '{$status}' for {$updatedCount} selected posts.");
        }

        return redirect()->back()->with('error', 'Invalid bulk action.');
    }
}
