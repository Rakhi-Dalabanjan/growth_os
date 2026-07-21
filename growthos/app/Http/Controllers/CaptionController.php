<?php

namespace App\Http\Controllers;

use App\Models\ContentCalendar;
use App\Models\ContentCaption;
use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use App\Services\CaptionGenerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class CaptionController extends Controller
{
    protected CaptionGenerationService $service;

    public function __construct(CaptionGenerationService $service)
    {
        $this->service = $service;
    }

    /**
     * Render the Caption Studio dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->route('organization.create')
                ->with('warning', 'Please create an organization first.');
        }

        // Check if dependencies exist
        $brandIntelligence = BrandIntelligence::where('organization_id', $organization->id)->first();
        $strategy = MarketingStrategy::where('organization_id', $organization->id)->first();

        // Query captions with search/filters
        $query = ContentCaption::where('organization_id', $organization->id)
            ->with('contentCalendar');

        if ($request->filled('platform')) {
            $query->where('platform', $request->platform);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('tone')) {
            $query->where('tone', $request->tone);
        }

        // Relational filters
        if ($request->filled('month') || $request->filled('pillar') || $request->filled('campaign') || $request->filled('goal') || $request->filled('search')) {
            $query->where(function($q) use ($request, $organization) {
                $q->whereHas('contentCalendar', function($sq) use ($request) {
                    if ($request->filled('month')) {
                        $sq->where('month', $request->month);
                    }
                    if ($request->filled('pillar')) {
                        $sq->where('content_pillar', $request->pillar);
                    }
                    if ($request->filled('campaign')) {
                        $sq->where('campaign_name', $request->campaign);
                    }
                    if ($request->filled('goal')) {
                        $sq->where('goal', $request->goal);
                    }
                    if ($request->filled('search')) {
                        $search = $request->search;
                        $sq->where(function($ssq) use ($search) {
                            $ssq->where('title', 'like', "%{$search}%")
                               ->orWhere('topic', 'like', "%{$search}%");
                        });
                    }
                });

                if ($request->filled('search')) {
                    $search = $request->search;
                    $q->orWhere('caption', 'like', "%{$search}%")
                      ->orWhere('headline', 'like', "%{$search}%")
                      ->orWhere('cta', 'like', "%{$search}%");
                }
            });
        }

        $captions = $query->latest()->get();

        // Get calendar entries that do not have captions yet (for generation)
        $pendingCalendarEntries = ContentCalendar::where('organization_id', $organization->id)
            ->whereDoesntHave('caption')
            ->get();

        // Distinct filter options
        $distinctQuery = ContentCaption::where('organization_id', $organization->id);
        $platforms = (clone $distinctQuery)->distinct()->pluck('platform');
        $tones = (clone $distinctQuery)->distinct()->whereNotNull('tone')->pluck('tone');

        $calendarQuery = ContentCalendar::where('organization_id', $organization->id);
        $pillars = (clone $calendarQuery)->distinct()->pluck('content_pillar');
        $campaigns = (clone $calendarQuery)->distinct()->pluck('campaign_name');
        $goals = (clone $calendarQuery)->distinct()->pluck('goal');
        $months = (clone $calendarQuery)->distinct()->pluck('month');

        return view('caption.index', compact(
            'captions',
            'pendingCalendarEntries',
            'brandIntelligence',
            'strategy',
            'platforms',
            'tones',
            'pillars',
            'campaigns',
            'goals',
            'months'
        ));
    }

    /**
     * Generate caption for a content calendar entry.
     */
    public function generate(Request $request, int $calendarId)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $entry = ContentCalendar::where('organization_id', $organization->id)
            ->where('id', $calendarId)
            ->firstOrFail();

        try {
            $this->service->generate($entry, $request->input('tone'), $request->input('language'));

            return redirect()->route('caption.index')
                ->with('success', 'Caption generated successfully!');
        } catch (Exception $e) {
            Log::error("Failed to generate caption: " . $e->getMessage());
            return redirect()->back()->with('error', "Failed to generate caption: " . $e->getMessage());
        }
    }

    /**
     * Regenerate an existing caption.
     */
    public function regenerate(Request $request, int $id)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $caption = ContentCaption::where('organization_id', $organization->id)
            ->where('id', $id)
            ->firstOrFail();

        $entry = $caption->contentCalendar;

        try {
            $this->service->generate($entry, $request->input('tone'), $request->input('language'));

            return redirect()->route('caption.index')
                ->with('success', 'Caption regenerated successfully!');
        } catch (Exception $e) {
            Log::error("Failed to regenerate caption: " . $e->getMessage());
            return redirect()->back()->with('error', "Failed to regenerate caption: " . $e->getMessage());
        }
    }

    /**
     * Update an edited caption manually.
     */
    public function update(Request $request, int $id)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $caption = ContentCaption::where('organization_id', $organization->id)
            ->where('id', $id)
            ->firstOrFail();

        $request->validate([
            'headline' => 'nullable|string|max:255',
            'caption'  => 'required|string',
            'cta'      => 'nullable|string|max:255',
            'hashtags' => 'nullable|string',
            'tone'     => 'nullable|string|max:255',
            'language' => 'nullable|string|max:255',
        ]);

        // Parse hashtags input (e.g. from comma separated or space separated)
        $hashtagsArray = [];
        if ($request->filled('hashtags')) {
            $tags = preg_split('/[\s,]+/', $request->hashtags);
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if ($tag !== '') {
                    if (strpos($tag, '#') !== 0) {
                        $tag = '#' . $tag;
                    }
                    $hashtagsArray[] = $tag;
                }
            }
        }

        $caption->update([
            'headline'        => $request->headline,
            'caption'         => $request->caption,
            'cta'             => $request->cta,
            'hashtags'        => $hashtagsArray,
            'tone'            => $request->tone,
            'language'        => $request->language,
            'word_count'      => str_word_count($request->caption),
            'character_count' => strlen($request->caption),
        ]);

        return redirect()->route('caption.index')
            ->with('success', 'Caption updated successfully.');
    }

    /**
     * Duplicate an existing caption.
     */
    public function duplicate(Request $request, int $id)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $caption = ContentCaption::where('organization_id', $organization->id)
            ->where('id', $id)
            ->firstOrFail();

        $newCaption = $caption->replicate();
        $newCaption->headline = $caption->headline ? ($caption->headline . ' (Copy)') : 'Copy';
        $newCaption->status = 'Draft';
        $newCaption->created_at = now();
        $newCaption->updated_at = now();
        $newCaption->save();

        return redirect()->route('caption.index')
            ->with('success', 'Caption duplicated successfully.');
    }

    /**
     * Delete a caption.
     */
    public function destroy(Request $request, int $id)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $caption = ContentCaption::where('organization_id', $organization->id)
            ->where('id', $id)
            ->firstOrFail();

        $caption->delete();

        return redirect()->route('caption.index')
            ->with('success', 'Caption deleted successfully.');
    }

    /**
     * Approve a caption.
     */
    public function approve(Request $request, int $id)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $caption = ContentCaption::where('organization_id', $organization->id)
            ->where('id', $id)
            ->firstOrFail();

        $caption->update(['status' => 'Approved']);

        return redirect()->route('caption.index')
            ->with('success', 'Caption approved successfully.');
    }

    /**
     * Reject a caption.
     */
    public function reject(Request $request, int $id)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $caption = ContentCaption::where('organization_id', $organization->id)
            ->where('id', $id)
            ->firstOrFail();

        $caption->update(['status' => 'Rejected']);

        return redirect()->route('caption.index')
            ->with('success', 'Caption rejected successfully.');
    }

    /**
     * Bulk actions for captions and generation.
     */
    public function bulk(Request $request)
    {
        $user = $request->user();
        $organization = $user->organization;

        if (!$organization) {
            return redirect()->back()->with('error', 'Organization not found.');
        }

        $action = $request->input('action');
        $ids = $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->back()->with('warning', 'No entries selected.');
        }

        try {
            if ($action === 'generate') {
                // $ids are Content Calendar entry IDs
                $entries = ContentCalendar::where('organization_id', $organization->id)
                    ->whereIn('id', $ids)
                    ->get();

                $successCount = 0;
                foreach ($entries as $entry) {
                    $this->service->generate($entry);
                    $successCount++;
                }

                return redirect()->route('caption.index')
                    ->with('success', "Successfully generated {$successCount} caption(s)!");

            } elseif ($action === 'approve') {
                // $ids are Caption IDs
                $count = ContentCaption::where('organization_id', $organization->id)
                    ->whereIn('id', $ids)
                    ->update(['status' => 'Approved']);

                return redirect()->route('caption.index')
                    ->with('success', "Successfully approved {$count} caption(s)!");

            } elseif ($action === 'delete') {
                // $ids are Caption IDs
                $count = ContentCaption::where('organization_id', $organization->id)
                    ->whereIn('id', $ids)
                    ->delete();

                return redirect()->route('caption.index')
                    ->with('success', "Successfully deleted {$count} caption(s)!");
            }

            return redirect()->back()->with('error', 'Invalid bulk action.');
        } catch (Exception $e) {
            Log::error("Bulk action {$action} failed: " . $e->getMessage());
            return redirect()->back()->with('error', "Bulk action failed: " . $e->getMessage());
        }
    }
}
