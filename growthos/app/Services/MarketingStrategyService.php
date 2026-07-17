<?php

namespace App\Services;

use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use Illuminate\Support\Facades\Log;
use Exception;

class MarketingStrategyService extends AIService
{
    /**
     * Generate a complete marketing strategy from Brand Intelligence.
     *
     * @param BrandIntelligence $brandIntelligence
     * @return MarketingStrategy
     * @throws Exception
     */
    public function generate(BrandIntelligence $brandIntelligence): MarketingStrategy
    {
        // 1. Prepare request inputs from brand intelligence profile
        $payload = [
            'summary'                       => $brandIntelligence->summary,
            'brand_personality'            => $brandIntelligence->brand_personality ?? [],
            'brand_voice'                  => $brandIntelligence->brand_voice ?? [],
            'ideal_customer'               => $brandIntelligence->ideal_customer ?? [],
            'customer_problems'            => $brandIntelligence->customer_problems ?? [],
            'customer_goals'               => $brandIntelligence->customer_goals ?? [],
            'marketing_objectives'         => $brandIntelligence->marketing_objectives ?? [],
            'competitor_summary'           => $brandIntelligence->competitor_summary,
            'recommended_content_pillars'  => $brandIntelligence->recommended_content_pillars ?? [],
            'recommended_posting_frequency' => $brandIntelligence->recommended_posting_frequency,
            'recommended_cta'              => $brandIntelligence->recommended_cta ?? [],
            'recommended_hashtags'         => $brandIntelligence->recommended_hashtags ?? [],
            'strengths'                    => $brandIntelligence->strengths ?? [],
            'weaknesses'                   => $brandIntelligence->weaknesses ?? [],
            'opportunities'                => $brandIntelligence->opportunities ?? [],
            'risks'                        => $brandIntelligence->risks ?? [],
        ];

        // 2. Perform FastAPI POST request
        $start = microtime(true);
        $response = $this->request('POST', '/ai/strategy/generate', $payload);
        $executionTime = round(microtime(true) - $start, 2);

        // 3. Handle connection level errors
        if (!$response['success']) {
            throw new Exception($response['error'] ?? 'Connection to AI Service failed or timed out.');
        }

        $resData = $response['data'] ?? [];
        if (empty($resData['success'])) {
            throw new Exception($resData['message'] ?? 'AI Provider failed to generate social media strategy.');
        }

        $gatewayData = $resData['data'] ?? [];
        $strategyData = $gatewayData['strategy'] ?? null;
        $model = $gatewayData['model'] ?? 'unknown';
        $provider = $resData['provider'] ?? 'gateway';

        // 4. Validate output JSON structure
        if (empty($strategyData)) {
            throw new Exception("AI Provider returned an invalid or empty strategy response.");
        }

        $requiredStrategyFields = [
            'strategy_name',
            'business_goal',
            'marketing_goal',
            'recommended_platforms',
            'content_pillars',
            'campaign_ideas',
            'posting_frequency',
            'recommended_formats',
            'tone_guidelines',
            'cta_strategy',
            'hashtags_strategy',
            'kpis',
            'confidence_score'
        ];

        $missingFields = [];
        foreach ($requiredStrategyFields as $field) {
            if (!isset($strategyData[$field]) || is_null($strategyData[$field])) {
                $missingFields[] = $field;
            }
        }

        if (!empty($missingFields)) {
            Log::warning("AI Marketing Strategy response has missing fields: " . implode(', ', $missingFields), [
                'raw_response' => $strategyData
            ]);
            throw new Exception("AI Response validation failed: Missing strategy fields (" . implode(', ', $missingFields) . ").");
        }

        // 5. Store / Update organization strategy (keeping only one active version)
        $marketingStrategy = MarketingStrategy::updateOrCreate(
            ['organization_id' => $brandIntelligence->organization_id],
            [
                'brand_intelligence_id'  => $brandIntelligence->id,
                'strategy_name'          => $strategyData['strategy_name'] ?? null,
                'business_goal'          => $strategyData['business_goal'] ?? null,
                'marketing_goal'         => $strategyData['marketing_goal'] ?? null,
                'recommended_platforms'  => $strategyData['recommended_platforms'] ?? null,
                'content_pillars'        => $strategyData['content_pillars'] ?? null,
                'campaign_ideas'         => $strategyData['campaign_ideas'] ?? null,
                'posting_frequency'      => $strategyData['posting_frequency'] ?? null,
                'recommended_formats'    => $strategyData['recommended_formats'] ?? null,
                'tone_guidelines'        => $strategyData['tone_guidelines'] ?? null,
                'audience_segments'      => $strategyData['audience_segments'] ?? ($brandIntelligence->ideal_customer ? [$brandIntelligence->ideal_customer['demographics'] ?? 'Target Segment'] : []),
                'hashtags_strategy'      => $strategyData['hashtags_strategy'] ?? null,
                'cta_strategy'           => $strategyData['cta_strategy'] ?? null,
                'kpis'                   => $strategyData['kpis'] ?? null,
                'growth_recommendations' => $strategyData['growth_recommendations'] ?? null,
                'risk_considerations'    => $strategyData['risk_considerations'] ?? null,
                'confidence_score'       => $strategyData['confidence_score'] ?? null,
                'provider'               => $provider,
                'model'                  => $model,
                'execution_time'         => $executionTime,
                'generated_at'           => now(),
            ]
        );

        return $marketingStrategy;
    }
}
