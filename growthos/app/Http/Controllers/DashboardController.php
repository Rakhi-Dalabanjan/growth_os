<?php

namespace App\Http\Controllers;

use App\Services\BrandProfileCompletionService;
use App\Services\AIService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected BrandProfileCompletionService $completionService;
    protected AIService $aiService;

    public function __construct(BrandProfileCompletionService $completionService, AIService $aiService)
    {
        $this->completionService = $completionService;
        $this->aiService = $aiService;
    }

    /**
     * Show the dashboard.
     */
    public function index(): View
    {
        $user         = auth()->user();
        $organization = $user->organization;
        $brandProfile = $organization ? $organization->brandProfile : null;
        
        $brandCompletion = $brandProfile 
            ? $this->completionService->calculate($brandProfile)
            : [
                'percentage' => 0,
                'status' => 'Not Started',
                'color' => 'secondary',
            ];

        $socialAccountsCount = $organization
            ? $organization->socialAccounts()->where('status', 'connected')->count()
            : 0;

        $brandIntelligence = $organization ? $organization->brandIntelligence : null;
        $marketingStrategy = $organization ? $organization->marketingStrategy : null;

        // Content Calendar metrics
        $hasCalendar = false;
        $postsPlannedCount = 0;
        $postsApprovedCount = 0;
        $postsScheduledCount = 0;

        if ($organization) {
            $hasCalendar = $organization->contentCalendars()->exists();
            $postsPlannedCount = $organization->contentCalendars()->where('status', 'Draft')->count();
            $postsApprovedCount = $organization->contentCalendars()->where('status', 'Approved')->count();
            $postsScheduledCount = $organization->contentCalendars()->where('status', 'Scheduled')->count();
        }

        $aiStatus = $this->aiService->getStatusForDashboard();

        return view('dashboard', compact(
            'user', 
            'organization', 
            'brandProfile', 
            'brandCompletion',
            'socialAccountsCount',
            'aiStatus',
            'brandIntelligence',
            'marketingStrategy',
            'hasCalendar',
            'postsPlannedCount',
            'postsApprovedCount',
            'postsScheduledCount'
        ));
    }
}
