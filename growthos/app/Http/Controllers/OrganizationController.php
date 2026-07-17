<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Http\Requests\StoreOrganizationRequest;
use App\Http\Requests\UpdateOrganizationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OrganizationController extends Controller
{
    /**
     * Show the organization index.
     * Redirect to create if user has no org, to show if they do.
     */
    public function index(): RedirectResponse|View
    {
        $user = Auth::user();

        if (! $user->hasOrganization()) {
            return redirect()->route('organization.create');
        }

        return redirect()->route('organization.show', $user->organization_id);
    }

    /**
     * Show the create organization form.
     */
    public function create(): View|RedirectResponse
    {
        $user = Auth::user();

        // If user already has an org, redirect to it
        if ($user->hasOrganization()) {
            return redirect()->route('organization.show', $user->organization_id);
        }

        $timezones  = $this->getTimezones();
        $industries = $this->getIndustries();
        $countries  = $this->getCountries();

        return view('organization.create', compact('timezones', 'industries', 'countries'));
    }

    /**
     * Store a new organization.
     */
    public function store(StoreOrganizationRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $validated['status'] = $validated['status'] ?? 'active';

        $organization = Organization::create($validated);

        // Attach organization to the user
        $user = Auth::user();
        $user->organization_id = $organization->id;
        $user->save();

        return redirect()
            ->route('organization.show', $organization)
            ->with('success', 'Organization created successfully!');
    }

    /**
     * Display the organization.
     */
    public function show(Organization $organization): View
    {
        $this->authorize('view', $organization);

        return view('organization.show', compact('organization'));
    }

    /**
     * Show the edit organization form.
     */
    public function edit(Organization $organization): View
    {
        $this->authorize('update', $organization);

        $timezones  = $this->getTimezones();
        $industries = $this->getIndustries();
        $countries  = $this->getCountries();

        return view('organization.edit', compact('organization', 'timezones', 'industries', 'countries'));
    }

    /**
     * Update the organization.
     */
    public function update(UpdateOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $this->authorize('update', $organization);

        $validated = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($organization->logo) {
                Storage::disk('public')->delete($organization->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $organization->update($validated);

        return redirect()
            ->route('organization.show', $organization)
            ->with('success', 'Organization updated successfully!');
    }

    /**
     * Get list of timezones.
     */
    private function getTimezones(): array
    {
        return \DateTimeZone::listIdentifiers();
    }

    /**
     * Get list of industries.
     */
    private function getIndustries(): array
    {
        return [
            'Technology',
            'Healthcare',
            'Finance & Banking',
            'Education',
            'Retail & E-commerce',
            'Manufacturing',
            'Real Estate',
            'Media & Entertainment',
            'Food & Beverage',
            'Transportation & Logistics',
            'Non-Profit',
            'Government',
            'Legal',
            'Marketing & Advertising',
            'Consulting',
            'Construction',
            'Agriculture',
            'Energy & Utilities',
            'Tourism & Hospitality',
            'Other',
        ];
    }

    /**
     * Get list of countries.
     */
    private function getCountries(): array
    {
        return [
            'Afghanistan', 'Albania', 'Algeria', 'Andorra', 'Angola',
            'Argentina', 'Armenia', 'Australia', 'Austria', 'Azerbaijan',
            'Bahamas', 'Bahrain', 'Bangladesh', 'Belarus', 'Belgium',
            'Belize', 'Benin', 'Bhutan', 'Bolivia', 'Bosnia and Herzegovina',
            'Botswana', 'Brazil', 'Brunei', 'Bulgaria', 'Burkina Faso',
            'Burundi', 'Cambodia', 'Cameroon', 'Canada', 'Chad',
            'Chile', 'China', 'Colombia', 'Congo', 'Costa Rica',
            'Croatia', 'Cuba', 'Cyprus', 'Czech Republic', 'Denmark',
            'Dominican Republic', 'Ecuador', 'Egypt', 'El Salvador', 'Estonia',
            'Ethiopia', 'Finland', 'France', 'Georgia', 'Germany',
            'Ghana', 'Greece', 'Guatemala', 'Haiti', 'Honduras',
            'Hungary', 'Iceland', 'India', 'Indonesia', 'Iran',
            'Iraq', 'Ireland', 'Israel', 'Italy', 'Jamaica',
            'Japan', 'Jordan', 'Kazakhstan', 'Kenya', 'Kosovo',
            'Kuwait', 'Kyrgyzstan', 'Laos', 'Latvia', 'Lebanon',
            'Libya', 'Liechtenstein', 'Lithuania', 'Luxembourg', 'Madagascar',
            'Malaysia', 'Maldives', 'Mali', 'Malta', 'Mexico',
            'Moldova', 'Monaco', 'Mongolia', 'Montenegro', 'Morocco',
            'Mozambique', 'Myanmar', 'Namibia', 'Nepal', 'Netherlands',
            'New Zealand', 'Nicaragua', 'Nigeria', 'North Korea', 'North Macedonia',
            'Norway', 'Oman', 'Pakistan', 'Palestine', 'Panama',
            'Paraguay', 'Peru', 'Philippines', 'Poland', 'Portugal',
            'Qatar', 'Romania', 'Russia', 'Rwanda', 'Saudi Arabia',
            'Senegal', 'Serbia', 'Singapore', 'Slovakia', 'Slovenia',
            'Somalia', 'South Africa', 'South Korea', 'Spain', 'Sri Lanka',
            'Sudan', 'Sweden', 'Switzerland', 'Syria', 'Taiwan',
            'Tajikistan', 'Tanzania', 'Thailand', 'Tunisia', 'Turkey',
            'Turkmenistan', 'Uganda', 'Ukraine', 'United Arab Emirates',
            'United Kingdom', 'United States', 'Uruguay', 'Uzbekistan',
            'Venezuela', 'Vietnam', 'Yemen', 'Zambia', 'Zimbabwe',
        ];
    }
}
