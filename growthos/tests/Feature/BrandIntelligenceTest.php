<?php

namespace Tests\Feature;

use App\Models\BrandProfile;
use App\Models\BrandIntelligence;
use App\Models\Organization;
use App\Models\User;
use App\Services\BrandIntelligenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BrandIntelligenceTest extends TestCase
{
    use RefreshDatabase;

    private function createValidBrandProfile(Organization $org): BrandProfile
    {
        return BrandProfile::create([
            'organization_id'      => $org->id,
            'brand_name'           => 'Acme Tech',
            'business_description' => 'We make high quality software and tools.',
            'target_audience'      => 'Developers, software engineers, tech managers.',
            'mission'              => 'Innovate computing.',
            'vision'               => 'A seamless workspace.',
            'primary_market'       => 'B2B',
            'brand_tone'           => 'Professional',
            'language'             => 'English',
            'preferred_words'      => ['performant', 'reliable'],
            'restricted_words'     => ['cheap'],
            'competitor_names'     => ['Competitor A'],
            'primary_cta'          => 'Start Trial',
        ]);
    }

    private function getMockAISuccessResponse(): array
    {
        return [
            'success' => true,
            'message' => 'Brand intelligence analysis completed successfully.',
            'provider' => 'gemini',
            'execution_time' => 1.45,
            'data' => [
                'intelligence' => [
                    'summary'                       => 'Acme Tech is a B2B tech brand focused on high-quality software tools.',
                    'brand_personality'             => ['Reliable', 'Innovative', 'Professional'],
                    'brand_voice'                   => ['Clear', 'Direct', 'Informative'],
                    'ideal_customer'                => [
                        'demographics' => 'Software engineers & managers aged 25-50.',
                        'behaviors'    => 'Using Git, automating deployments.',
                        'pains'        => 'Fragmented workflows and slow build times.'
                    ],
                    'customer_problems'             => ['Slow builds', 'Workflow friction'],
                    'customer_goals'                => ['Faster deployment', 'Better team cohesion'],
                    'marketing_objectives'          => ['Increase organic signups by 20%'],
                    'competitor_summary'            => 'Competitor A lacks the native integrations Acme Tech provides.',
                    'recommended_content_pillars'   => ['DevOps Automation', 'SaaS Security'],
                    'recommended_posting_frequency' => '3 times per week on LinkedIn',
                    'recommended_cta'               => ['Download free whitepaper', 'Start trial now'],
                    'recommended_hashtags'          => ['#DevOps', '#SaaS', '#Tech'],
                    'strengths'                     => ['Native integrations', 'Fast execution'],
                    'weaknesses'                    => ['New platform brand awareness'],
                    'opportunities'                 => ['Growing DevOps market'],
                    'risks'                         => ['Intensifying platform competition'],
                    'confidence_score'              => 90,
                ],
                'model' => 'gemini-2.0-flash',
            ]
        ];
    }

    /**
     * Guest cannot access brand intelligence routes.
     */
    public function test_guests_cannot_access_brand_intelligence(): void
    {
        $this->get('/brand-intelligence')->assertRedirect('/login');
        $this->post('/brand-intelligence/analyze')->assertRedirect('/login');
    }

    /**
     * User without organization is redirected to setup organization.
     */
    public function test_user_without_org_redirected_to_org_create(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/brand-intelligence');
        $response->assertRedirect('/organization/create');
    }

    /**
     * User with organization but without profile is redirected to create brand profile.
     */
    public function test_user_without_profile_redirected_to_profile_create(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this->actingAs($user)->get('/brand-intelligence');
        $response->assertRedirect('/brand-profile/create');
        $response->assertSessionHas('warning');
    }

    /**
     * Check validation of Brand Profile before analysis starts (missing required fields).
     */
    public function test_analysis_validation_warning_if_fields_missing(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        
        // Brand Profile missing business description & target audience
        $profile = BrandProfile::create([
            'organization_id' => $org->id,
            'brand_name'      => 'Acme Tech',
        ]);

        $response = $this->actingAs($user)->post('/brand-intelligence/analyze');
        
        $response->assertRedirect('/brand-intelligence');
        $response->assertSessionHas('warning');
        $this->assertDatabaseMissing('brand_intelligence', ['organization_id' => $org->id]);
    }

    /**
     * Test successful analysis execution, JSON validation, and saving.
     */
    public function test_successful_brand_analysis_and_store(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $profile = $this->createValidBrandProfile($org);

        Http::fake([
            '*/ai/brand/analyze' => Http::response($this->getMockAISuccessResponse(), 200)
        ]);

        $response = $this->actingAs($user)->post('/brand-intelligence/analyze');

        $response->assertRedirect('/brand-intelligence');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('brand_intelligence', [
            'organization_id'  => $org->id,
            'brand_profile_id' => $profile->id,
            'confidence_score' => 90,
            'provider'         => 'gemini',
            'model'            => 'gemini-2.0-flash',
        ]);

        $intelligence = BrandIntelligence::where('organization_id', $org->id)->first();
        $this->assertEquals('Acme Tech is a B2B tech brand focused on high-quality software tools.', $intelligence->summary);
        $this->assertEquals(['Reliable', 'Innovative', 'Professional'], $intelligence->brand_personality);
        $this->assertEquals(['DevOps Automation', 'SaaS Security'], $intelligence->recommended_content_pillars);
        $this->assertEquals('3 times per week on LinkedIn', $intelligence->recommended_posting_frequency);
    }

    /**
     * Subsequent generation overwrites existing record (keeping only the newest version).
     */
    public function test_subsequent_analysis_overwrites_existing(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $profile = $this->createValidBrandProfile($org);

        // Pre-create brand intelligence record
        $oldIntel = BrandIntelligence::create([
            'organization_id'  => $org->id,
            'brand_profile_id' => $profile->id,
            'summary'          => 'Old Summary',
        ]);

        Http::fake([
            '*/ai/brand/analyze' => Http::response($this->getMockAISuccessResponse(), 200)
        ]);

        $this->actingAs($user)->post('/brand-intelligence/analyze');

        // Confirm only 1 record exists
        $this->assertEquals(1, BrandIntelligence::where('organization_id', $org->id)->count());
        $newIntel = BrandIntelligence::where('organization_id', $org->id)->first();
        $this->assertNotEquals('Old Summary', $newIntel->summary);
    }

    /**
     * Tenant Isolation: User cannot view another organization's brand intelligence.
     */
    public function test_tenant_isolation_cannot_view_other_brand_intelligence(): void
    {
        $org1 = Organization::create(['name' => 'Org One']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);

        $org2 = Organization::create(['name' => 'Org Two']);
        $profile2 = $this->createValidBrandProfile($org2);
        
        $intel2 = BrandIntelligence::create([
            'organization_id'  => $org2->id,
            'brand_profile_id' => $profile2->id,
            'summary'          => 'Secret Org Two summary',
        ]);

        // Attempting to access Org Two's page (redirected/forbidden check in policy)
        // Wait, the show route does not accept id (it resolves the auth user's intelligence),
        // but if someone tries to fetch the show view or view route, it only shows their own.
        // If we test accessing a hypothetical custom show route, or policy directly:
        $policy = new \App\Policies\BrandIntelligencePolicy();
        $this->assertTrue($policy->view($user1, BrandIntelligence::create(['organization_id' => $org1->id, 'brand_profile_id' => 1])));
        $this->assertFalse($policy->view($user1, $intel2));
    }

    /**
     * Tenant Isolation: User cannot trigger regeneration for another organization's brand profile.
     */
    public function test_tenant_isolation_cannot_regenerate_other_brand_intelligence(): void
    {
        $org1 = Organization::create(['name' => 'Org One']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);
        $profile1 = $this->createValidBrandProfile($org1);

        $org2 = Organization::create(['name' => 'Org Two']);
        $user2 = User::factory()->create(['organization_id' => $org2->id]);
        $profile2 = $this->createValidBrandProfile($org2);

        Http::fake([
            '*/ai/brand/analyze' => Http::response($this->getMockAISuccessResponse(), 200)
        ]);

        // Act as user 1, analyze. This must analyze profile1, not profile2.
        $this->actingAs($user1)->post('/brand-intelligence/analyze');
        
        $this->assertDatabaseHas('brand_intelligence', ['organization_id' => $org1->id]);
        $this->assertDatabaseMissing('brand_intelligence', ['organization_id' => $org2->id]);
    }

    /**
     * Dashboard displays correct status depending on generation state.
     */
    public function test_dashboard_displays_correct_brand_intelligence_status(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $profile = $this->createValidBrandProfile($org);

        // Scenario 1: Not Generated
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertSee('Not Generated');

        // Scenario 2: Generated
        BrandIntelligence::create([
            'organization_id'  => $org->id,
            'brand_profile_id' => $profile->id,
            'summary'          => 'Generated Summary info',
            'confidence_score' => 88,
            'generated_at'     => now(),
        ]);

        $user->refresh();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertSee('Generated');
        $response->assertSee('88%');
    }

    /**
     * Gateway Failure returns nice warning/error display alerts.
     */
    public function test_gateway_failure_displays_bootstrap_alert(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $profile = $this->createValidBrandProfile($org);

        // Fake provider offline or timeout connection error
        Http::fake([
            '*/ai/brand/analyze' => function() {
                throw new \Illuminate\Http\Client\ConnectionException("Connection refused");
            }
        ]);

        $response = $this->actingAs($user)->post('/brand-intelligence/analyze');
        $response->assertRedirect('/brand-intelligence');
        $response->assertSessionHas('error');
    }

    /**
     * Invalid AI Response (JSON format mismatch or missing fields) handled gracefully.
     */
    public function test_invalid_ai_response_displays_bootstrap_alert(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $profile = $this->createValidBrandProfile($org);

        // Fake response missing required fields (e.g. missing 'summary')
        $badResponse = $this->getMockAISuccessResponse();
        unset($badResponse['data']['intelligence']['summary']);

        Http::fake([
            '*/ai/brand/analyze' => Http::response($badResponse, 200)
        ]);

        $response = $this->actingAs($user)->post('/brand-intelligence/analyze');
        $response->assertRedirect('/brand-intelligence');
        $response->assertSessionHas('error');
    }
}
