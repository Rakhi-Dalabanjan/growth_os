<?php

namespace App\Services;

use App\Models\BrandProfile;
use App\Models\BrandIntelligence;
use Illuminate\Support\Facades\Log;
use Exception;

class BrandIntelligenceService extends AIService
{
    /**
     * Analyze a brand profile using FastAPI AI Gateway.
     *
     * @param BrandProfile $profile
     * @return BrandIntelligence
     * @throws Exception
     */
    public function analyze(BrandProfile $profile): BrandIntelligence
    {
        // 1. Validate the brand profile
        $this->validateProfile($profile);

        // 2. Prepare payload
        $payload = [
            'brand_name'           => $profile->brand_name,
            'business_description' => $profile->business_description,
            'mission'              => $profile->mission,
            'vision'               => $profile->vision,
            'target_audience'      => $profile->target_audience,
            'primary_market'       => $profile->primary_market,
            'brand_tone'           => $profile->brand_tone,
            'languages'            => $profile->language ? [$profile->language] : [],
            'preferred_words'      => $profile->preferred_words ?? [],
            'restricted_words'     => $profile->restricted_words ?? [],
            'competitors'          => $profile->competitor_names ?? [],
            'cta'                  => trim(($profile->primary_cta ?? '') . ' ' . ($profile->secondary_cta ?? '')),
            'brand_colors'         => array_filter([$profile->primary_color, $profile->secondary_color, $profile->accent_color]),
            'brand_fonts'          => array_filter([$profile->primary_font, $profile->secondary_font]),
        ];

        // 3. Make HTTP request to FastAPI Gateway
        $start = microtime(true);
        $response = $this->request('POST', '/ai/brand/analyze', $payload);
        $executionTime = round(microtime(true) - $start, 2);

        // 4. Handle connection and request level errors
        if (!$response['success']) {
            throw new Exception($response['error'] ?? 'Connection to AI Service failed or timed out.');
        }

        $resData = $response['data'] ?? [];
        if (empty($resData['success'])) {
            throw new Exception($resData['message'] ?? 'AI Provider failed to generate response.');
        }

        $gatewayData = $resData['data'] ?? [];
        $aiData = $gatewayData['intelligence'] ?? null;
        $model = $gatewayData['model'] ?? 'unknown';
        $provider = $resData['provider'] ?? 'gateway';

        // 5. JSON parsing and structure validation
        if (empty($aiData)) {
            throw new Exception("AI Provider returned an invalid or empty response structure.");
        }

        // Validate that critical fields in AI output are present
        $requiredResponseFields = [
            'summary',
            'brand_personality',
            'ideal_customer',
            'marketing_objectives',
            'strengths',
            'weaknesses',
            'opportunities',
            'confidence_score'
        ];

        $missingResponseFields = [];
        foreach ($requiredResponseFields as $field) {
            if (!isset($aiData[$field]) || is_null($aiData[$field])) {
                $missingResponseFields[] = $field;
            }
        }

        if (!empty($missingResponseFields)) {
            Log::warning("AI Brand Intelligence response has missing fields: " . implode(', ', $missingResponseFields), [
                'raw_response' => $aiData
            ]);
            throw new Exception("AI Response validation failed: Missing fields (" . implode(', ', $missingResponseFields) . ").");
        }

        // 6. Save or update record in database
        $brandIntelligence = BrandIntelligence::updateOrCreate(
            ['organization_id' => $profile->organization_id],
            [
                'brand_profile_id'             => $profile->id,
                'summary'                      => $aiData['summary'] ?? null,
                'brand_personality'            => $aiData['brand_personality'] ?? null,
                'brand_voice'                  => $aiData['brand_voice'] ?? ($aiData['recommended_tone'] ?? null),
                'ideal_customer'               => $aiData['ideal_customer'] ?? null,
                'customer_problems'            => $aiData['customer_problems'] ?? null,
                'customer_goals'               => $aiData['customer_goals'] ?? null,
                'marketing_objectives'         => $aiData['marketing_objectives'] ?? null,
                'competitor_summary'           => $aiData['competitor_summary'] ?? null,
                'recommended_content_pillars'  => $aiData['recommended_content_pillars'] ?? ($aiData['content_pillars'] ?? null),
                'recommended_posting_frequency' => $aiData['recommended_posting_frequency'] ?? null,
                'recommended_cta'              => $aiData['recommended_cta'] ?? ($aiData['recommended_cta'] ?? null),
                'recommended_hashtags'         => $aiData['recommended_hashtags'] ?? null,
                'strengths'                    => $aiData['strengths'] ?? null,
                'weaknesses'                   => $aiData['weaknesses'] ?? null,
                'opportunities'                => $aiData['opportunities'] ?? null,
                'risks'                        => $aiData['risks'] ?? null,
                'confidence_score'             => $aiData['confidence_score'] ?? null,
                'provider'                     => $provider,
                'model'                        => $model,
                'execution_time'               => $executionTime,
                'generated_at'                 => now(),
            ]
        );

        return $brandIntelligence;
    }

    /**
     * Check if the brand profile is complete enough for AI analysis.
     *
     * @param BrandProfile $profile
     * @throws Exception
     */
    public function validateProfile(BrandProfile $profile): void
    {
        $missing = [];
        
        if (empty($profile->brand_name)) {
            $missing[] = 'Brand Name';
        }
        if (empty($profile->business_description)) {
            $missing[] = 'Business Description';
        }
        if (empty($profile->target_audience)) {
            $missing[] = 'Target Audience';
        }

        if (!empty($missing)) {
            throw new Exception("Your Brand Profile is incomplete. Please fill out the following required fields to run AI analysis: " . implode(', ', $missing) . ".");
        }
    }
}
