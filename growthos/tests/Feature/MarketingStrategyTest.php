<?php

namespace Tests\Feature;

use App\Models\BrandProfile;
use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MarketingStrategyTest extends TestCase
{
    use RefreshDatabase;

    private function createValidBrandIntelligence(Organization $org): BrandIntelligence
    {
        $profile = BrandProfile::create([
            'organization_id'      => $org->id,
            'brand_name'           => 'Acme Tech',
            'business_description' => 'We build DevOps automation tools.',
            'target_audience'      => 'Software developers.',
        ]);

        return BrandIntelligence::create([
            'organization_id'               => $org->id,
            'brand_profile_id'             => $profile->id,
            'summary'                      => 'Acme is an automated DevOps tools SaaS.',
            'brand_personality'            => ['Innovative', 'Reliable'],
            'brand_voice'                  => ['Clear', 'Helpful'],
            'ideal_customer'               => [
                'demographics' => 'Developers 25-45.',
                'behaviors'    => 'Code in Go/Python.',
                'pains'        => 'Slow deployment cycles.'
            ],
            'customer_problems'            => ['Fragmented deployment pipelines'],
            'customer_goals'               => ['Instant deployment workflows'],
            'marketing_objectives'         => ['Increase signups'],
            'competitor_summary'           => 'Competitor A lacks automation.',
            'recommended_content_pillars'  => ['Continuous Integration', 'Agile DevOps'],
            'recommended_posting_frequency' => '3 times a week',
            'recommended_cta'              => ['Start trial'],
            'recommended_hashtags'         => ['#DevOps', '#Agile'],
            'strengths'                    => ['Automated triggers'],
            'weaknesses'                   => ['New brand'],
            'opportunities'                => ['Market expanding'],
            'risks'                        => ['API changes'],
            'confidence_score'             => 95,
        ]);
    }

    private function getMockStrategySuccessResponse(): array
    {
        return [
            'success' => true,
            'message' => 'Marketing strategy generation completed successfully.',
            'provider' => 'gemini',
            'execution_time' => 2.15,
            'data' => [
                'strategy' => [
                    'strategy_name'          => 'Acme Tech Q3 Market Authority Plan',
                    'business_goal'          => 'Establish Acme as the fastest DevOps integration tool in the market.',
                    'marketing_goal'         => 'Generate 5,000 product trials via LinkedIn and Twitter content marketing.',
                    'recommended_platforms'  => ['LinkedIn', 'Twitter'],
                    'content_pillars'        => ['CI/CD Workflows', 'Developer Productivity'],
                    'campaign_ideas'         => [
                        [
                            'name'        => 'Speed Run Challenge',
                            'description' => 'Showcase deploying a containerized app in under 60 seconds.',
                            'duration'    => '30 days',
                            'channels'    => ['Twitter']
                        ]
                    ],
                    'posting_frequency'      => 'Daily posts on Twitter, 3 times a week on LinkedIn.',
                    'recommended_formats'    => ['Text Threads', 'Short video reels'],
                    'tone_guidelines'        => ['Helpful', 'Precise', 'Technically deep'],
                    'audience_segments'      => ['DevOps Engineers', 'Indie Hackers'],
                    'cta_strategy'           => ['Include trial links in post signatures'],
                    'hashtags_strategy'      => ['Use #BuildInPublic and #DevOps tags'],
                    'kpis'                   => ['Trial registrations', 'Twitter follower growth'],
                    'growth_recommendations' => ['Interact with developer threads daily'],
                    'risk_considerations'    => ['Avoid controversial tech debates'],
                    'confidence_score'       => 92,
                ],
                'model' => 'gemini-2.0-flash',
            ]
        ];
    }

    /**
     * Guest cannot access marketing strategy routes.
     */
    public function test_guests_cannot_access_marketing_strategy(): void
    {
        $this->get('/marketing-strategy')->assertRedirect('/login');
        $this->post('/marketing-strategy/generate')->assertRedirect('/login');
    }

    /**
     * User without organization is redirected to setup organization.
     */
    public function test_user_without_org_redirected_to_org_create(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/marketing-strategy');
        $response->assertRedirect('/organization/create');
    }

    /**
     * User with organization but without brand intelligence is redirected with warning alert.
     */
    public function test_user_without_brand_intelligence_redirected_with_warning(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        
        // Brand Profile exists, but Brand Intelligence does not.
        BrandProfile::create([
            'organization_id' => $org->id,
            'brand_name'      => 'Acme Tech',
        ]);

        $response = $this->actingAs($user)->get('/marketing-strategy');
        $response->assertRedirect('/brand-intelligence');
        $response->assertSessionHas('warning');
    }

    /**
     * Test successful strategy generation, JSON validation, and saving.
     */
    public function test_successful_strategy_generation_and_store(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $intel = $this->createValidBrandIntelligence($org);

        Http::fake([
            '*/ai/strategy/generate' => Http::response($this->getMockStrategySuccessResponse(), 200)
        ]);

        $response = $this->actingAs($user)->post('/marketing-strategy/generate');

        $response->assertRedirect('/marketing-strategy');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('marketing_strategies', [
            'organization_id'       => $org->id,
            'brand_intelligence_id' => $intel->id,
            'strategy_name'         => 'Acme Tech Q3 Market Authority Plan',
            'confidence_score'      => 92,
            'provider'              => 'gemini',
            'model'                 => 'gemini-2.0-flash',
        ]);

        $strategy = MarketingStrategy::where('organization_id', $org->id)->first();
        $this->assertEquals(['LinkedIn', 'Twitter'], $strategy->recommended_platforms);
        $this->assertEquals(['CI/CD Workflows', 'Developer Productivity'], $strategy->content_pillars);
        $this->assertEquals('Daily posts on Twitter, 3 times a week on LinkedIn.', $strategy->posting_frequency);
    }

    /**
     * Subsequent strategy generation overwrites previous strategy (keeping only the newest version).
     */
    public function test_subsequent_generation_overwrites_existing_strategy(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $intel = $this->createValidBrandIntelligence($org);

        // Pre-create marketing strategy record
        MarketingStrategy::create([
            'organization_id'       => $org->id,
            'brand_intelligence_id' => $intel->id,
            'strategy_name'         => 'Old Strategy Title',
        ]);

        Http::fake([
            '*/ai/strategy/generate' => Http::response($this->getMockStrategySuccessResponse(), 200)
        ]);

        $this->actingAs($user)->post('/marketing-strategy/generate');

        // Verify only 1 record exists
        $this->assertEquals(1, MarketingStrategy::where('organization_id', $org->id)->count());
        
        $newStrategy = MarketingStrategy::where('organization_id', $org->id)->first();
        $this->assertNotEquals('Old Strategy Title', $newStrategy->strategy_name);
    }

    /**
     * Tenant Isolation: User cannot view another organization's marketing strategy.
     */
    public function test_tenant_isolation_cannot_view_other_marketing_strategy(): void
    {
        $org1 = Organization::create(['name' => 'Org One']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);

        $org2 = Organization::create(['name' => 'Org Two']);
        $intel2 = $this->createValidBrandIntelligence($org2);
        
        $strategy2 = MarketingStrategy::create([
            'organization_id'       => $org2->id,
            'brand_intelligence_id' => $intel2->id,
            'strategy_name'         => 'Secret strategy Org Two',
        ]);

        $policy = new \App\Policies\MarketingStrategyPolicy();
        $this->assertTrue($policy->view($user1, MarketingStrategy::create(['organization_id' => $org1->id, 'brand_intelligence_id' => 1])));
        $this->assertFalse($policy->view($user1, $strategy2));
    }

    /**
     * Tenant Isolation: User cannot trigger generation for another organization's brand intelligence.
     */
    public function test_tenant_isolation_cannot_regenerate_other_strategy(): void
    {
        $org1 = Organization::create(['name' => 'Org One']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);
        $intel1 = $this->createValidBrandIntelligence($org1);

        $org2 = Organization::create(['name' => 'Org Two']);
        $user2 = User::factory()->create(['organization_id' => $org2->id]);
        $intel2 = $this->createValidBrandIntelligence($org2);

        Http::fake([
            '*/ai/strategy/generate' => Http::response($this->getMockStrategySuccessResponse(), 200)
        ]);

        // Trigger acting as User 1, this should build strategy for Org One, not Org Two.
        $this->actingAs($user1)->post('/marketing-strategy/generate');

        $this->assertDatabaseHas('marketing_strategies', ['organization_id' => $org1->id]);
        $this->assertDatabaseMissing('marketing_strategies', ['organization_id' => $org2->id]);
    }

    /**
     * Dashboard widget displays correct status.
     */
    public function test_dashboard_displays_correct_marketing_strategy_status(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $intel = $this->createValidBrandIntelligence($org);

        // Scenario 1: Not Generated
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertSee('Not Generated');

        // Scenario 2: Generated
        MarketingStrategy::create([
            'organization_id'       => $org->id,
            'brand_intelligence_id' => $intel->id,
            'strategy_name'         => 'Acme Tech Q3 Market Authority Plan',
            'confidence_score'      => 92,
            'generated_at'          => now(),
        ]);

        $user->refresh();
        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertSee('Generated');
        $response->assertSee('92%');
    }

    /**
     * Gateway Failure returns proper error messages.
     */
    public function test_gateway_failure_displays_bootstrap_alert(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $intel = $this->createValidBrandIntelligence($org);

        Http::fake([
            '*/ai/strategy/generate' => function() {
                throw new \Illuminate\Http\Client\ConnectionException("Connection timed out");
            }
        ]);

        $response = $this->actingAs($user)->post('/marketing-strategy/generate');
        $response->assertRedirect('/marketing-strategy');
        $response->assertSessionHas('error');
    }

    /**
     * Invalid AI Response (missing fields) handled gracefully.
     */
    public function test_invalid_ai_response_displays_bootstrap_alert(): void
    {
        $org = Organization::create(['name' => 'Acme Corp']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $intel = $this->createValidBrandIntelligence($org);

        $badResponse = $this->getMockStrategySuccessResponse();
        // Delete strategy_name from output JSON
        unset($badResponse['data']['strategy']['strategy_name']);

        Http::fake([
            '*/ai/strategy/generate' => Http::response($badResponse, 200)
        ]);

        $response = $this->actingAs($user)->post('/marketing-strategy/generate');
        $response->assertRedirect('/marketing-strategy');
        $response->assertSessionHas('error');
    }
}
