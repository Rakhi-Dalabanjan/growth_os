<?php

namespace App\Http\Controllers;

use App\Models\BrandProfile;
use App\Models\BrandIntelligence;
use App\Services\BrandIntelligenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class BrandIntelligenceController extends Controller
{
    protected BrandIntelligenceService $intelligenceService;

    public function __construct(BrandIntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    /**
     * Display the brand intelligence dashboard/profile.
     */
    public function show(): View|RedirectResponse
    {
        $user = Auth::user();

        // 1. Ensure user belongs to an organization
        if (!$user->hasOrganization()) {
            return redirect()->route('organization.create')
                ->with('error', 'Please set up your organization first.');
        }

        $organization = $user->organization;
        $brandProfile = $organization->brandProfile;

        // 2. Ensure brand profile exists
        if (!$brandProfile) {
            return redirect()->route('brand-profile.create')
                ->with('warning', 'Please create a Brand Profile before accessing Brand Intelligence.');
        }

        $brandIntelligence = $organization->brandIntelligence;

        // 3. Authorize if record exists
        if ($brandIntelligence) {
            $this->authorize('view', $brandIntelligence);
        }

        return view('brand-intelligence.show', compact('brandProfile', 'brandIntelligence'));
    }

    /**
     * Run or regenerate the brand intelligence analysis.
     */
    public function analyze(Request $request): RedirectResponse
    {
        $user = Auth::user();

        // 1. Ensure organization
        if (!$user->hasOrganization()) {
            return redirect()->route('organization.create')
                ->with('error', 'Please set up your organization first.');
        }

        $organization = $user->organization;
        $brandProfile = $organization->brandProfile;

        // 2. Ensure brand profile
        if (!$brandProfile) {
            return redirect()->route('brand-profile.create')
                ->with('error', 'Please create a Brand Profile first.');
        }

        // 3. Authorize if intelligence already exists
        $brandIntelligence = $organization->brandIntelligence;
        if ($brandIntelligence) {
            $this->authorize('update', $brandIntelligence);
        }

        try {
            // 4. Perform analysis
            $this->intelligenceService->analyze($brandProfile);

            return redirect()->route('brand-intelligence')
                ->with('success', 'Brand Intelligence analysis completed successfully!');
        } catch (Exception $e) {
            // Check if it's a validation error (missing required fields)
            if (str_contains($e->getMessage(), 'incomplete') || str_contains($e->getMessage(), 'missing required fields')) {
                return redirect()->route('brand-intelligence')
                    ->with('warning', $e->getMessage());
            }

            // General connection/AI failure
            return redirect()->route('brand-intelligence')
                ->with('error', 'Failed to generate Brand Intelligence: ' . $e->getMessage());
        }
    }
}
