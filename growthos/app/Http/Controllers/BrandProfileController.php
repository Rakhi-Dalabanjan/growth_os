<?php

namespace App\Http\Controllers;

use App\Models\BrandProfile;
use App\Http\Requests\StoreBrandProfileRequest;
use App\Http\Requests\UpdateBrandProfileRequest;
use App\Services\BrandProfileCompletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BrandProfileController extends Controller
{
    protected BrandProfileCompletionService $completionService;

    public function __construct(BrandProfileCompletionService $completionService)
    {
        $this->completionService = $completionService;
    }

    /**
     * Display or redirect to the organization's brand profile.
     */
    public function index(): RedirectResponse
    {
        $user = Auth::user();

        // Ensure user belongs to an organization
        if (!$user->hasOrganization()) {
            return redirect()->route('organization.create')
                ->with('error', 'Please set up your organization first.');
        }

        $brandProfile = $user->organization->brandProfile;

        if ($brandProfile) {
            return redirect()->route('brand-profile.show', $brandProfile);
        }

        return redirect()->route('brand-profile.create');
    }

    /**
     * Show the form for creating a new brand profile.
     */
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user->hasOrganization()) {
            return redirect()->route('organization.create')
                ->with('error', 'Please set up your organization first.');
        }

        $brandProfile = $user->organization->brandProfile;

        if ($brandProfile) {
            return redirect()->route('brand-profile.show', $brandProfile);
        }

        return view('brand-profile.create');
    }

    /**
     * Store a newly created brand profile in storage.
     */
    public function store(StoreBrandProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user->hasOrganization()) {
            return redirect()->route('organization.create')
                ->with('error', 'Please set up your organization first.');
        }

        // Prevent duplicate profiles
        if ($user->organization->brandProfile) {
            return redirect()->route('brand-profile.show', $user->organization->brandProfile)
                ->with('error', 'Brand profile already exists.');
        }

        $validated = $request->validated();
        
        // Map comma-separated string inputs to arrays
        $validated['preferred_words']   = $this->parseCommaSeparated($request->input('preferred_words'));
        $validated['restricted_words']  = $this->parseCommaSeparated($request->input('restricted_words'));
        $validated['competitor_names']  = $this->parseCommaSeparated($request->input('competitor_names'));
        $validated['approved_claims']   = $this->parseCommaSeparated($request->input('approved_claims'));
        $validated['restricted_claims'] = $this->parseCommaSeparated($request->input('restricted_claims'));

        $validated['organization_id'] = $user->organization_id;
        $validated['status'] = $validated['status'] ?? 'active';

        $brandProfile = BrandProfile::create($validated);

        return redirect()->route('brand-profile.show', $brandProfile)
            ->with('success', 'Brand Profile created successfully!');
    }

    /**
     * Display the specified brand profile.
     */
    public function show(BrandProfile $brandProfile): View|RedirectResponse
    {
        $this->authorize('view', $brandProfile);

        $completion = $this->completionService->calculate($brandProfile);

        return view('brand-profile.show', compact('brandProfile', 'completion'));
    }

    /**
     * Show the form for editing the specified brand profile.
     */
    public function edit(BrandProfile $brandProfile): View|RedirectResponse
    {
        $this->authorize('update', $brandProfile);

        return view('brand-profile.edit', compact('brandProfile'));
    }

    /**
     * Update the specified brand profile in storage.
     */
    public function update(UpdateBrandProfileRequest $request, BrandProfile $brandProfile): RedirectResponse
    {
        $this->authorize('update', $brandProfile);

        $validated = $request->validated();

        // Map comma-separated string inputs to arrays
        $validated['preferred_words']   = $this->parseCommaSeparated($request->input('preferred_words'));
        $validated['restricted_words']  = $this->parseCommaSeparated($request->input('restricted_words'));
        $validated['competitor_names']  = $this->parseCommaSeparated($request->input('competitor_names'));
        $validated['approved_claims']   = $this->parseCommaSeparated($request->input('approved_claims'));
        $validated['restricted_claims'] = $this->parseCommaSeparated($request->input('restricted_claims'));

        $brandProfile->update($validated);

        return redirect()->route('brand-profile.show', $brandProfile)
            ->with('success', 'Brand Profile updated successfully!');
    }

    /**
     * Helper to split comma-separated input into trimmed array values.
     */
    private function parseCommaSeparated(?string $input): array
    {
        if (is_null($input) || trim($input) === '') {
            return [];
        }

        return array_values(array_filter(
            array_map('trim', explode(',', $input)),
            fn($val) => $val !== ''
        ));
    }
}
