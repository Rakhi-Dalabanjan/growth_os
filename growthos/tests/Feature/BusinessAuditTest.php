<?php

namespace Tests\Feature;

use App\Models\BrandProfile;
use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use App\Models\ContentCalendar;
use App\Models\ContentCaption;
use App\Models\Organization;
use App\Models\User;
use App\Services\CaptionGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BusinessAuditTest extends TestCase
{
    use RefreshDatabase;

    private function getDynamicMockResponse($brandName, $endpoint)
    {
        $brandLower = strtolower($brandName);
        $industry = 'default';

        if (str_contains($brandLower, 'builder') || str_contains($brandLower, 'real estate')) {
            $industry = 'real_estate';
        } elseif (str_contains($brandLower, 'restaurant') || str_contains($brandLower, 'leaf') || str_contains($brandLower, 'food')) {
            $industry = 'restaurant';
        } elseif (str_contains($brandLower, 'dental') || str_contains($brandLower, 'clinic') || str_contains($brandLower, 'smile')) {
            $industry = 'dentistry';
        } elseif (str_contains($brandLower, 'gym') || str_contains($brandLower, 'fitness') || str_contains($brandLower, 'elite')) {
            $industry = 'fitness';
        }

        if (str_contains($endpoint, 'analyze')) {
            // Brand Intelligence mock
            if ($industry === 'real_estate') {
                $summary = "$brandName specializes in custom residential construction and luxury home building.";
                $pillars = ["Construction Quality", "Luxury Architecture"];
            } elseif ($industry === 'restaurant') {
                $summary = "$brandName is a farm-to-table organic eatery and community bistro.";
                $pillars = ["Local Ingredients", "Chef Specials"];
            } elseif ($industry === 'dentistry') {
                $summary = "$brandName is a state-of-the-art preventative and cosmetic dental clinic.";
                $pillars = ["Smile Transformation", "Comfort Care"];
            } elseif ($industry === 'fitness') {
                $summary = "$brandName is an elite strength conditioning and athletic workout gym.";
                $pillars = ["Functional Training", "Nutrition Strategy"];
            } else {
                $summary = "$brandName is a high performance workspace tooling SaaS.";
                $pillars = ["Productivity", "Tech Innovation"];
            }

            return [
                'success' => true,
                'provider' => 'gemini',
                'data' => [
                    'intelligence' => [
                        'summary' => $summary,
                        'brand_personality' => ['Quality', 'Care'],
                        'brand_voice' => ['Professional', 'Trustworthy'],
                        'ideal_customer' => [
                            'demographics' => 'Target audience',
                            'behaviors' => 'Key behaviors',
                            'pains' => 'Key pain points'
                        ],
                        'customer_problems' => ['Inefficiency'],
                        'customer_goals' => ['Excellence'],
                        'marketing_objectives' => ['Growth'],
                        'competitor_summary' => 'Market competition is active.',
                        'recommended_content_pillars' => $pillars,
                        'recommended_posting_frequency' => '3 times a week',
                        'recommended_cta' => ['Call us today'],
                        'recommended_hashtags' => ['#Success'],
                        'strengths' => ['Precision'],
                        'weaknesses' => ['Saturated market'],
                        'opportunities' => ['Local expansion'],
                        'risks' => ['Competition'],
                        'confidence_score' => 95,
                    ],
                    'model' => 'gemini-2.0-flash'
                ]
            ];
        }

        if (str_contains($endpoint, 'strategy')) {
            // Marketing Strategy mock
            if ($industry === 'real_estate') {
                $stratName = "$brandName Construction Authority Strategy";
                $bizGoal = "Position $brandName as the premier custom villa builder.";
                $marketingGoal = "Increase design consultation inquiries.";
            } elseif ($industry === 'restaurant') {
                $stratName = "$brandName Culinary Experience Strategy";
                $bizGoal = "Position $brandName as the leading local farm-to-table restaurant.";
                $marketingGoal = "Increase weekend reservations.";
            } elseif ($industry === 'dentistry') {
                $stratName = "$brandName Smile Excellence Plan";
                $bizGoal = "Establish $brandName as the go-to clinic for gentle dental care.";
                $marketingGoal = "Drive new patient registrations.";
            } elseif ($industry === 'fitness') {
                $stratName = "$brandName Athletic Coaching Strategy";
                $bizGoal = "Position $brandName as the ultimate gym training facility.";
                $marketingGoal = "Increase active gym member subscriptions.";
            } else {
                $stratName = "$brandName Growth & Automation Plan";
                $bizGoal = "Scale SaaS user acquisition.";
                $marketingGoal = "Increase free trial signups.";
            }

            return [
                'success' => true,
                'provider' => 'gemini',
                'data' => [
                    'strategy' => [
                        'strategy_name' => $stratName,
                        'business_goal' => $bizGoal,
                        'marketing_goal' => $marketingGoal,
                        'recommended_platforms' => ['Instagram', 'LinkedIn'],
                        'content_pillars' => ['Quality', 'Innovation'],
                        'campaign_ideas' => [
                            [
                                'name' => 'Signature Launch',
                                'description' => 'A showcase spotlight highlighting raw excellence.',
                                'duration' => '30 days',
                                'channels' => ['Instagram']
                            ]
                        ],
                        'posting_frequency' => '3 times a week',
                        'recommended_formats' => ['Video Reels'],
                        'tone_guidelines' => ['Professional'],
                        'audience_segments' => ['Premium clients'],
                        'cta_strategy' => ['Book now'],
                        'hashtags_strategy' => ['Use tag strategy'],
                        'kpis' => ['CTR', 'Bookings'],
                        'confidence_score' => 95,
                    ],
                    'model' => 'gemini-2.0-flash'
                ]
            ];
        }

        if (str_contains($endpoint, 'calendar')) {
            // Content Calendar mock
            if ($industry === 'real_estate') {
                $topic = "Unveiling our latest luxury custom house foundation";
                $title = "Built to Last: Foundation Highlights";
            } elseif ($industry === 'restaurant') {
                $topic = "Behind the scenes with our chefs preparing tonight's special menu";
                $title = "Crafting Farm-to-Table Specials";
            } elseif ($industry === 'dentistry') {
                $topic = "Pediatric dental hygiene care tips with our principal clinic dentists";
                $title = "Kid-Friendly Smile Guide";
            } elseif ($industry === 'fitness') {
                $topic = "Correcting posture form for heavy weight squats";
                $title = "Squat Safe: Squat Power Tips";
            } else {
                $topic = "How to automate workflow steps";
                $title = "Workspace automation walkthrough";
            }

            return [
                'success' => true,
                'data' => [
                    'calendar' => [
                        [
                            'Date' => '2026-07-21',
                            'Platform' => 'LinkedIn',
                            'Topic' => $topic,
                            'Working Title' => $title,
                            'Content Pillar' => 'Excellence',
                            'Campaign' => 'Launch',
                            'Goal' => 'Awareness',
                            'Content Type' => 'Educational',
                            'Post Format' => 'Text',
                            'Suggested CTA' => 'Book today',
                            'Priority' => 'High'
                        ]
                    ],
                    'model' => 'gemini-2.0-flash'
                ]
            ];
        }

        if (str_contains($endpoint, 'captions/generate')) {
            // Caption mock
            if ($industry === 'real_estate') {
                $headline = "Unparalleled Construction Mastery";
                $caption = "From blueprint concept to structural completion, $brandName delivers premium real estate development and builders excellence.";
                $cta = "Schedule a design consultation.";
            } elseif ($industry === 'restaurant') {
                $headline = "Artisanal Kitchen Specials";
                $caption = "Fresh, locally-sourced herbs and organic ingredients prepared daily. Discover the farm-to-table menu difference at $brandName.";
                $cta = "Book a dining table.";
            } elseif ($industry === 'dentistry') {
                $headline = "Gentle Care, Bright Smiles";
                $caption = "Transforming oral health checkups into comfortable, anxiety-free clinic visits at $brandName.";
                $cta = "Book your consultation.";
            } elseif ($industry === 'fitness') {
                $headline = "Redefine Strength Limits";
                $caption = "Step inside $brandName and unlock advanced trainer workout plans to reach your fitness goals.";
                $cta = "Get a guest pass.";
            } else {
                $headline = "Optimized Workflows";
                $caption = "Automate all tasks and save hours every single week with $brandName.";
                $cta = "Start free trial.";
            }

            return [
                'success' => true,
                'data' => [
                    'caption' => [
                        'Platform' => 'LinkedIn',
                        'Headline' => $headline,
                        'Caption' => $caption,
                        'Call To Action' => $cta,
                        'Primary Keywords' => ['excellence'],
                        'Suggested Hashtags' => ['#Excellence'],
                        'Emoji Recommendation' => '✨🏡🥗🦷💪',
                        'Tone' => 'Professional',
                        'Language' => 'English',
                        'Estimated Character Count' => 150
                    ],
                    'model' => 'gemini-2.0-flash'
                ]
            ];
        }

        return ['success' => false];
    }

    private function runMultiBusinessTest($brandName)
    {
        $org = Organization::create(['name' => $brandName . ' Corp', 'status' => 'active']);
        $user = User::factory()->create(['organization_id' => $org->id]);

        $profile = BrandProfile::create([
            'organization_id' => $org->id,
            'brand_name' => $brandName,
            'business_description' => "Official profile for $brandName.",
            'target_audience' => 'General clients'
        ]);

        Http::fake([
            '*/ai/brand/analyze' => Http::response($this->getDynamicMockResponse($brandName, 'analyze'), 200),
            '*/ai/strategy/generate' => Http::response($this->getDynamicMockResponse($brandName, 'strategy'), 200),
            '*/ai/calendar/generate' => Http::response($this->getDynamicMockResponse($brandName, 'calendar'), 200),
            '*/ai/captions/generate' => Http::response($this->getDynamicMockResponse($brandName, 'captions/generate'), 200),
        ]);

        // 1. Generate Brand Intelligence
        $this->actingAs($user)->post(route('brand-intelligence.analyze'));
        $intel = BrandIntelligence::where('organization_id', $org->id)->first();
        $this->assertNotNull($intel);
        $this->assertStringContainsString($brandName, $intel->summary);
        $this->assertStringNotContainsString('GrowthOS', $intel->summary);

        // 2. Generate Marketing Strategy
        $user->refresh();
        $user->unsetRelation('organization');
        $this->actingAs($user)->post(route('marketing-strategy.generate'));
        $strategy = MarketingStrategy::where('organization_id', $org->id)->first();
        $this->assertNotNull($strategy);
        $this->assertStringContainsString($brandName, $strategy->strategy_name);
        $this->assertStringNotContainsString('GrowthOS', $strategy->strategy_name);
        $this->assertStringNotContainsString('GrowthOS', $strategy->business_goal);

        // 3. Generate Calendar Entry
        $user->refresh();
        $user->unsetRelation('organization');
        $this->actingAs($user)->post(route('content-calendar.generate'), [
            'month' => 7,
            'year' => 2026
        ]);
        $calendarEntry = ContentCalendar::where('organization_id', $org->id)->first();
        $this->assertNotNull($calendarEntry);
        $this->assertStringNotContainsString('GrowthOS', $calendarEntry->topic);

        // 4. Generate Caption
        $user->refresh();
        $user->unsetRelation('organization');
        $this->actingAs($user)->post(route('caption.generate', $calendarEntry->id));
        $caption = ContentCaption::where('organization_id', $org->id)->first();
        $this->assertNotNull($caption);
        $this->assertStringContainsString($brandName, $caption->caption);
        $this->assertStringNotContainsString('GrowthOS', $caption->caption);
    }

    public function test_builders_real_estate_ai_context()
    {
        $this->runMultiBusinessTest('ABC Builders');
    }

    public function test_restaurant_ai_context()
    {
        $this->runMultiBusinessTest('Green Leaf Restaurant');
    }

    public function test_dentistry_clinic_ai_context()
    {
        $this->runMultiBusinessTest('Smile Dental Clinic');
    }

    public function test_gym_fitness_ai_context()
    {
        $this->runMultiBusinessTest('Elite Fitness Gym');
    }
}
