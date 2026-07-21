<?php

namespace Tests\Feature;

use App\Models\BrandProfile;
use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use App\Models\ContentCalendar;
use App\Models\ContentCaption;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CaptionTest extends TestCase
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
            'brand_tone'                   => 'Inspirational',
            'languages'                    => ['English'],
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
            'generated_at'                 => now(),
        ]);

        return MarketingStrategy::create([
            'organization_id'        => $org->id,
            'brand_intelligence_id' => $intel->id,
            'strategy_name'          => 'Acme Tech Q3 Market Authority Plan',
            'business_goal'          => 'Establish Acme as the fastest DevOps integration tool.',
            'marketing_goal'         => 'Generate product trials.',
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

    private function getMockCaptionSuccessResponse(): array
    {
        return [
            'success' => true,
            'message' => 'Caption generated successfully.',
            'provider' => 'gemini',
            'execution_time' => 0.8,
            'data' => [
                'caption' => [
                    'Platform' => 'LinkedIn',
                    'Headline' => 'Save 10+ Hours/Week with DevOps Automation',
                    'Caption' => 'Deploy containerized apps automatically in under 60 seconds. Learn how Acme Tech makes CI/CD seamless.',
                    'Call To Action' => 'Start your free trial today.',
                    'Primary Keywords' => ['DevOps', 'Automation', 'CI/CD'],
                    'Suggested Hashtags' => ['#DevOps', '#Automation', '#AcmeTech'],
                    'Emoji Recommendation' => '🚀⚡️🛠',
                    'Tone' => 'Inspirational',
                    'Language' => 'English',
                    'Estimated Character Count' => 125,
                ],
                'model' => 'gemini-2.0-flash',
            ]
        ];
    }

    public function test_guests_cannot_access_caption_studio()
    {
        $response = $this->get(route('caption.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_without_org_redirected()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get(route('caption.index'));
        $response->assertRedirect(route('organization.create'));
    }

    public function test_generation_warning_if_brand_assets_missing()
    {
        $org = Organization::create(['name' => 'Acme Inc', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $response = $this->actingAs($user)->get(route('caption.index'));
        $response->assertStatus(200);
        $response->assertSee('Setup Requirements Missing');
    }

    public function test_successful_caption_generation()
    {
        $org = Organization::create(['name' => 'Acme Inc', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);

        $entry = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Cool DevOps post',
            'topic'                 => 'How to set up automated deployments',
            'content_pillar'        => 'CI/CD Workflows',
            'campaign_name'         => 'Speed Run Challenge',
            'goal'                  => 'Lead Generation',
            'content_type'          => 'Educational',
            'post_format'           => 'Text',
            'planned_date'          => '2026-07-21',
        ]);

        Http::fake([
            '*/ai/captions/generate' => Http::response($this->getMockCaptionSuccessResponse(), 200),
        ]);

        $response = $this->actingAs($user)->post(route('caption.generate', $entry->id));
        
        $response->assertRedirect(route('caption.index'));
        $this->assertDatabaseHas('content_captions', [
            'organization_id' => $org->id,
            'content_calendar_id' => $entry->id,
            'headline' => 'Save 10+ Hours/Week with DevOps Automation',
            'platform' => 'LinkedIn',
            'status' => 'Draft',
        ]);
    }

    public function test_edit_caption()
    {
        $org = Organization::create(['name' => 'Acme Inc', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);
        
        $entry = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Post',
            'topic'                 => 'Topic',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        $caption = ContentCaption::create([
            'organization_id'     => $org->id,
            'content_calendar_id' => $entry->id,
            'platform'            => 'LinkedIn',
            'headline'            => 'Old Headline',
            'caption'             => 'Old Caption',
            'status'              => 'Draft',
        ]);

        $response = $this->actingAs($user)->put(route('caption.update', $caption->id), [
            'headline' => 'New Headline',
            'caption'  => 'New Caption Content',
            'cta'      => 'Click here',
            'hashtags' => '#Acme, #GrowthOS',
        ]);

        $response->assertRedirect(route('caption.index'));
        $this->assertDatabaseHas('content_captions', [
            'id'       => $caption->id,
            'headline' => 'New Headline',
            'caption'  => 'New Caption Content',
            'cta'      => 'Click here',
        ]);
    }

    public function test_delete_caption()
    {
        $org = Organization::create(['name' => 'Acme Inc', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);
        
        $entry = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Post',
            'topic'                 => 'Topic',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        $caption = ContentCaption::create([
            'organization_id'     => $org->id,
            'content_calendar_id' => $entry->id,
            'platform'            => 'LinkedIn',
            'headline'            => 'Headline',
            'caption'             => 'Caption',
        ]);

        $response = $this->actingAs($user)->delete(route('caption.destroy', $caption->id));
        
        $response->assertRedirect(route('caption.index'));
        $this->assertDatabaseMissing('content_captions', ['id' => $caption->id]);
    }

    public function test_approve_and_reject_caption()
    {
        $org = Organization::create(['name' => 'Acme Inc', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);
        
        $entry = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Post',
            'topic'                 => 'Topic',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        $caption = ContentCaption::create([
            'organization_id'     => $org->id,
            'content_calendar_id' => $entry->id,
            'platform'            => 'LinkedIn',
            'headline'            => 'Headline',
            'caption'             => 'Caption',
            'status'              => 'Draft',
        ]);

        // Approve
        $response = $this->actingAs($user)->post(route('caption.approve', $caption->id));
        $response->assertRedirect(route('caption.index'));
        $this->assertDatabaseHas('content_captions', ['id' => $caption->id, 'status' => 'Approved']);

        // Reject
        $response = $this->actingAs($user)->post(route('caption.reject', $caption->id));
        $response->assertRedirect(route('caption.index'));
        $this->assertDatabaseHas('content_captions', ['id' => $caption->id, 'status' => 'Rejected']);
    }

    public function test_tenant_isolation()
    {
        $org1 = Organization::create(['name' => 'Org One', 'status' => 'active']);
        $user1 = User::factory()->create(['organization_id' => $org1->id]);
        
        $org2 = Organization::create(['name' => 'Org Two', 'status' => 'active']);
        $user2 = User::factory()->create(['organization_id' => $org2->id]);
        
        $strategy = $this->createValidMarketingStrategy($org1);
        $entry = ContentCalendar::create([
            'organization_id'       => $org1->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Post',
            'topic'                 => 'Topic',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        $caption = ContentCaption::create([
            'organization_id'     => $org1->id,
            'content_calendar_id' => $entry->id,
            'platform'            => 'LinkedIn',
            'headline'            => 'Headline',
            'caption'             => 'Caption',
            'status'              => 'Draft',
        ]);

        // User 2 should not be able to view, edit, or delete caption of Org 1
        $response = $this->actingAs($user2)->get(route('caption.index'));
        $response->assertDontSee('Headline');

        $response = $this->actingAs($user2)->put(route('caption.update', $caption->id), [
            'caption' => 'Hacked Caption',
        ]);
        $response->assertStatus(404);

        $response = $this->actingAs($user2)->delete(route('caption.destroy', $caption->id));
        $response->assertStatus(404);
    }

    public function test_dashboard_displays_correct_counts()
    {
        $org = Organization::create(['name' => 'Acme Inc', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);

        $entry1 = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Post 1',
            'topic'                 => 'Topic 1',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        $entry2 = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'Twitter',
            'title'                 => 'Post 2',
            'topic'                 => 'Topic 2',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        // Generate one caption with Approved status
        ContentCaption::create([
            'organization_id'     => $org->id,
            'content_calendar_id' => $entry1->id,
            'platform'            => 'LinkedIn',
            'headline'            => 'Headline 1',
            'caption'             => 'Caption 1',
            'status'              => 'Approved',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Generated:</strong> 1', false);
        $response->assertSee('Pending:</strong> 1', false);
        $response->assertSee('Approved:</strong> 1', false);
    }

    public function test_gateway_failure_displays_alert()
    {
        $org = Organization::create(['name' => 'Acme Inc', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);

        $entry = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Post',
            'topic'                 => 'Topic',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        Http::fake([
            '*/ai/captions/generate' => Http::response(null, 502),
        ]);

        $response = $this->actingAs($user)->post(route('caption.generate', $entry->id));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_bulk_operations()
    {
        $org = Organization::create(['name' => 'Acme Inc', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);
        $strategy = $this->createValidMarketingStrategy($org);

        $entry1 = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'LinkedIn',
            'title'                 => 'Post 1',
            'topic'                 => 'Topic 1',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        $entry2 = ContentCalendar::create([
            'organization_id'       => $org->id,
            'marketing_strategy_id' => $strategy->id,
            'month'                 => 7,
            'year'                  => 2026,
            'platform'              => 'Twitter',
            'title'                 => 'Post 2',
            'topic'                 => 'Topic 2',
            'content_pillar'        => 'Pillar',
            'campaign_name'         => 'C',
            'goal'                  => 'G',
            'content_type'          => 'T',
            'post_format'           => 'F',
            'planned_date'          => '2026-07-21',
        ]);

        Http::fake([
            '*/ai/captions/generate' => Http::response($this->getMockCaptionSuccessResponse(), 200),
        ]);

        // Bulk Generate
        $response = $this->actingAs($user)->post(route('caption.bulk'), [
            'action' => 'generate',
            'ids' => [$entry1->id, $entry2->id]
        ]);
        $response->assertRedirect(route('caption.index'));
        $this->assertEquals(2, ContentCaption::where('organization_id', $org->id)->count());

        $captions = ContentCaption::where('organization_id', $org->id)->pluck('id')->toArray();

        // Bulk Approve
        $response = $this->actingAs($user)->post(route('caption.bulk'), [
            'action' => 'approve',
            'ids' => $captions
        ]);
        $response->assertRedirect(route('caption.index'));
        $this->assertEquals(2, ContentCaption::where('organization_id', $org->id)->where('status', 'Approved')->count());

        // Bulk Delete
        $response = $this->actingAs($user)->post(route('caption.bulk'), [
            'action' => 'delete',
            'ids' => $captions
        ]);
        $response->assertRedirect(route('caption.index'));
        $this->assertEquals(0, ContentCaption::where('organization_id', $org->id)->count());
    }
}
