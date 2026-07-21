<?php

namespace Tests\Feature;

use App\Models\BrandProfile;
use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use App\Models\ContentCalendar;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ContentCalendarTest extends TestCase
{
    use RefreshDatabase;

    private function createValidMarketingStrategy(Organization $org): MarketingStrategy
    {
        $profile = BrandProfile::create([
            'organization_id'      => $org->id,
            'brand_name'           => 'Acme Tech',
            'business_description' => 'We build DevOps automation tools.',
            'target_audience'      => 'Software developers.',
        ]);

        $intel = BrandIntelligence::create([
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

        return MarketingStrategy::create([
            'organization_id'        => $org->id,
            'brand_intelligence_id' => $intel->id,
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
            'posting_frequency'      => '3 posts per week',
            'recommended_formats'    => ['LinkedIn Text', 'Twitter Threads'],
            'tone_guidelines'        => ['Helpful', 'Clear'],
            'audience_segments'      => ['Developers'],
            'hashtags_strategy'      => ['#DevOps', '#Automation'],
            'cta_strategy'           => ['Start free trial'],
            'kpis'                   => ['Signups', 'Clicks'],
            'confidence_score'       => 95,
            'provider'               => 'gemini',
            'model'                  => 'gemini-2.0-flash',
            'execution_time'         => 1.5,
            'generated_at'           => now(),
        ]);
    }

    private function getMockCalendarSuccessResponse(): array
    {
        return [
            'success' => true,
            'message' => 'Content calendar generation completed successfully.',
            'provider' => 'gemini',
            'execution_time' => 1.5,
            'data' => [
                'calendar' => [
                    [
                        'Date'          => '2026-07-02',
                        'Platform'      => 'LinkedIn',
                        'Topic'         => 'Automated content workflows',
                        'Working Title' => 'Save 10+ Hours/Week with Smart Automation',
                        'Content Pillar' => 'AI & Productivity Tips',
                        'Campaign'      => 'Launch Campaign',
                        'Goal'          => 'Lead Generation',
                        'Content Type'  => 'Educational',
                        'Post Format'   => 'Text',
                        'Suggested CTA' => 'Start your free trial today',
                        'Priority'      => 'High'
                    ],
                    [
                        'Date'          => '2026-07-05',
                        'Platform'      => 'Twitter',
                        'Topic'         => 'Productivity hacks',
                        'Working Title' => 'Let AI orchestrate your calendar.',
                        'Content Pillar' => 'Growth Case Studies',
                        'Campaign'      => 'Launch Campaign',
                        'Goal'          => 'Brand Awareness',
                        'Content Type'  => 'Entertainment',
                        'Post Format'   => 'Thread',
                        'Suggested CTA' => 'Join the waitlist',
                        'Priority'      => 'Medium'
                    ]
                ],
                'model' => 'gemini-2.0-flash'
            ]
        ];
    }

    /**
     * Guest cannot access content calendar routes.
     */
    public function test_guests_cannot_access_content_calendar(): void
    {
        $this->get('/content-calendar')->assertRedirect('/login');
        $this->post('/content-calendar/generate')->assertRedirect('/login');
        $this->post('/content-calendar/store')->assertRedirect('/login');
    }

    /**
     * Users without organization are redirected.
     */
    public function test_user_without_org_redirected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/content-calendar')->assertRedirect('/organization/create');
    }

    /**
     * Generation fails with warning if marketing strategy is missing.
     */
    public function test_generation_warning_if_strategy_missing(): void
    {
        $org = Organization::create(['name' => 'Acme Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this->actingAs($user)->post('/content-calendar/generate', [
            'month' => 7,
            'year'  => 2026
        ]);

        $response->assertSessionHas('warning');
        $this->assertDatabaseEmpty('content_calendars');
    }

    /**
     * Successful monthly content calendar generation.
     */
    public function test_successful_calendar_generation(): void
    {
        $org = Organization::create(['name' => 'Acme Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);

        Http::fake([
            '*/ai/calendar/generate' => Http::response($this->getMockCalendarSuccessResponse(), 200),
        ]);

        $response = $this->actingAs($user)->post('/content-calendar/generate', [
            'month' => 7,
            'year'  => 2026
        ]);

        $response->assertRedirect('/content-calendar?month=7&year=2026');
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('content_calendars', 2);
        $this->assertDatabaseHas('content_calendars', [
            'organization_id' => $org->id,
            'month'           => 7,
            'year'            => 2026,
            'platform'        => 'LinkedIn',
            'title'           => 'Save 10+ Hours/Week with Smart Automation',
            'status'          => 'Draft',
        ]);
    }

    /**
     * Subsequent generation replaces only selected month.
     */
    public function test_subsequent_generation_overwrites_selected_month_only(): void
    {
        $org = Organization::create(['name' => 'Acme Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);

        // Pre-create a post for August
        ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 8,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'August Post',
            'topic'                 => 'August Topic',
            'content_pillar'        => 'Tips',
            'campaign_name'         => 'Campaign',
            'goal'                  => 'Awareness',
            'content_type'          => 'Educational',
            'post_format'           => 'Text',
            'status'                => 'Draft',
            'planned_date'          => '2026-08-01',
        ]);

        // Pre-create a post for July (which should get deleted during July regeneration)
        ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'Twitter',
            'title'                 => 'Old July Post',
            'topic'                 => 'Old Topic',
            'content_pillar'        => 'Tips',
            'campaign_name'         => 'Campaign',
            'goal'                  => 'Awareness',
            'content_type'          => 'Educational',
            'post_format'           => 'Text',
            'status'                => 'Draft',
            'planned_date'          => '2026-07-01',
        ]);

        Http::fake([
            '*/ai/calendar/generate' => Http::response($this->getMockCalendarSuccessResponse(), 200),
        ]);

        $response = $this->actingAs($user)->post('/content-calendar/generate', [
            'month' => 7,
            'year'  => 2026
        ]);

        // Check July entries overwritten but August entry remains
        $this->assertDatabaseMissing('content_calendars', ['title' => 'Old July Post']);
        $this->assertDatabaseHas('content_calendars', ['title' => 'August Post']);
        $this->assertDatabaseHas('content_calendars', ['title' => 'Save 10+ Hours/Week with Smart Automation']);
        $this->assertDatabaseCount('content_calendars', 3); // 2 new July posts + 1 existing August post
    }

    /**
     * Tenant isolation for calendar views and CRUD actions.
     */
    public function test_tenant_isolation(): void
    {
        $orgA = Organization::create(['name' => 'Org A']);
        $userA = User::factory()->create(['organization_id' => $orgA->id]);
        $strategyA = $this->createValidMarketingStrategy($orgA);

        $orgB = Organization::create(['name' => 'Org B']);
        $userB = User::factory()->create(['organization_id' => $orgB->id]);
        $strategyB = $this->createValidMarketingStrategy($orgB);

        $postB = ContentCalendar::create([
            'organization_id'       => $orgB->id,
            'marketing_strategy_id' => $strategyB->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Secret Post B',
            'topic'                 => 'Topic B',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'Campaign',
            'goal'                  => 'Goal',
            'content_type'          => 'Educational',
            'post_format'           => 'Text',
            'status'                => 'Draft',
            'planned_date'          => '2026-07-02',
        ]);

        // User A cannot edit Post B
        $this->actingAs($userA)->put("/content-calendar/{$postB->id}", [
            'title'          => 'Hacked Title',
            'topic'          => 'Hacked Topic',
            'platform'       => 'LinkedIn',
            'content_pillar' => 'Pillar',
            'campaign_name'  => 'Campaign',
            'goal'           => 'Goal',
            'content_type'   => 'Educational',
            'post_format'    => 'Text',
            'planned_date'   => '2026-07-02',
            'priority'       => 'Medium',
            'status'         => 'Draft',
        ])->assertStatus(403);

        // User A cannot delete Post B
        $this->actingAs($userA)->delete("/content-calendar/{$postB->id}")
            ->assertStatus(403);
    }

    /**
     * CRUD: edit calendar entry.
     */
    public function test_edit_calendar_entry(): void
    {
        $org = Organization::create(['name' => 'Acme Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);

        $post = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Original Title',
            'topic'                 => 'Original Topic',
            'content_pillar'        => 'Tips',
            'campaign_name'         => 'Campaign',
            'goal'                  => 'Awareness',
            'content_type'          => 'Educational',
            'post_format'           => 'Text',
            'status'                => 'Draft',
            'planned_date'          => '2026-07-02',
        ]);

        $response = $this->actingAs($user)->put("/content-calendar/{$post->id}", [
            'title'          => 'Updated Title',
            'topic'          => 'Updated Topic',
            'platform'       => 'Twitter',
            'content_pillar' => 'Updates',
            'campaign_name'  => 'Summer Sale',
            'goal'           => 'Conversion',
            'content_type'   => 'Promotional',
            'post_format'    => 'Image',
            'planned_date'   => '2026-07-03',
            'planned_time'   => '14:30',
            'priority'       => 'High',
            'status'         => 'Approved',
            'notes'          => 'Updated custom notes',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('content_calendars', [
            'id'    => $post->id,
            'title' => 'Updated Title',
            'status'=> 'Approved',
            'month' => 7,
            'year'  => 2026,
        ]);
    }

    /**
     * Bulk Action: Delete and Status changes.
     */
    public function test_bulk_actions(): void
    {
        $org = Organization::create(['name' => 'Acme Org']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);

        $post1 = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Post 1',
            'topic'                 => 'Topic',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'Campaign',
            'goal'                  => 'Goal',
            'content_type'          => 'Educational',
            'post_format'           => 'Text',
            'status'                => 'Draft',
            'planned_date'          => '2026-07-02',
        ]);

        $post2 = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'Twitter',
            'title'                 => 'Post 2',
            'topic'                 => 'Topic',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'Campaign',
            'goal'                  => 'Goal',
            'content_type'          => 'Educational',
            'post_format'           => 'Text',
            'status'                => 'Draft',
            'planned_date'          => '2026-07-05',
        ]);

        // Bulk update status to Approved
        $this->actingAs($user)->post('/content-calendar/bulk', [
            'ids'    => [$post1->id, $post2->id],
            'action' => 'status_Approved',
        ])->assertRedirect();

        $this->assertDatabaseHas('content_calendars', ['id' => $post1->id, 'status' => 'Approved']);
        $this->assertDatabaseHas('content_calendars', ['id' => $post2->id, 'status' => 'Approved']);

        // Bulk delete
        $this->actingAs($user)->post('/content-calendar/bulk', [
            'ids'    => [$post1->id, $post2->id],
            'action' => 'delete',
        ])->assertRedirect();

        $this->assertDatabaseMissing('content_calendars', ['id' => $post1->id]);
        $this->assertDatabaseMissing('content_calendars', ['id' => $post2->id]);
    }
}
