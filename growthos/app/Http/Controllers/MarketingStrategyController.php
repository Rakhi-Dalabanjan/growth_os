<?php

namespace App\Http\Controllers;

use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use App\Services\MarketingStrategyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class MarketingStrategyController extends Controller
{
    protected MarketingStrategyService $strategyService;

    public function __construct(MarketingStrategyService $strategyService)
    {
        $this->strategyService = $strategyService;
    }

    /**
     * Display the marketing strategy profile page.
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
                ->with('warning', 'Please create a Brand Profile before accessing Marketing Strategy.');
        }

        $brandIntelligence = $organization->brandIntelligence;

        // 3. Ensure brand intelligence has been generated
        if (!$brandIntelligence) {
            return redirect()->route('brand-intelligence')
                ->with('warning', 'Please run Brand Intelligence analysis first before generating a Marketing Strategy.');
        }

        $marketingStrategy = $organization->marketingStrategy;

        // 4. Authorize if strategy exists
        if ($marketingStrategy) {
            $this->authorize('view', $marketingStrategy);
        }

        return view('marketing-strategy.show', compact('brandProfile', 'brandIntelligence', 'marketingStrategy'));
    }

    /**
     * Run or regenerate the marketing strategy analysis.
     */
    public function generate(Request $request): RedirectResponse
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

        $brandIntelligence = $organization->brandIntelligence;

        // 3. Ensure brand intelligence exists
        if (!$brandIntelligence) {
            return redirect()->route('brand-intelligence')
                ->with('warning', 'Please generate your Brand Intelligence profile before creating a Marketing Strategy.');
        }

        // 4. Authorize if strategy already exists
        $marketingStrategy = $organization->marketingStrategy;
        if ($marketingStrategy) {
            $this->authorize('update', $marketingStrategy);
        }

        try {
            // 5. Generate strategy via service
            $this->strategyService->generate($brandIntelligence);

            return redirect()->route('marketing-strategy')
                ->with('success', 'Marketing Strategy generated successfully!');
        } catch (Exception $e) {
            return redirect()->route('marketing-strategy')
                ->with('error', 'Failed to generate Marketing Strategy: ' . $e->getMessage());
        }
    }
}
