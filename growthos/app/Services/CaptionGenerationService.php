<?php

namespace App\Services;

use App\Models\ContentCalendar;
use App\Models\ContentCaption;
use App\Models\BrandIntelligence;
use App\Models\MarketingStrategy;
use Illuminate\Support\Facades\Log;
use Exception;

class CaptionGenerationService extends AIService
{
    /**
     * Generate a caption for a content calendar entry.
     *
     * @param ContentCalendar $contentCalendar
     * @param string|null $tone
     * @param string|null $language
     * @return ContentCaption
     * @throws Exception
     */
    public function generate(ContentCalendar $contentCalendar, ?string $tone = null, ?string $language = null): ContentCaption
    {
        $organizationId = $contentCalendar->organization_id;

        // Load Brand Intelligence
        $brandIntelligence = BrandIntelligence::where('organization_id', $organizationId)->first();
        if (!$brandIntelligence) {
            throw new Exception("Brand Intelligence is required before generating a caption.");
        }

        // Load Marketing Strategy
        $marketingStrategy = MarketingStrategy::where('organization_id', $organizationId)->first();
        if (!$marketingStrategy) {
            throw new Exception("Marketing Strategy is required before generating a caption.");
        }

        // 1. Prepare request payload
        $payload = [
            'brand_intelligence' => $brandIntelligence->toArray(),
            'marketing_strategy' => $marketingStrategy->toArray(),
            'content_calendar'   => $contentCalendar->toArray(),
            'tone'               => $tone,
            'language'           => $language,
        ];

        // 2. Perform FastAPI POST request
        $start = microtime(true);
        $response = $this->request('POST', '/ai/captions/generate', $payload);
        $executionTime = round(microtime(true) - $start, 2);

        // 3. Handle connection level errors
        if (!$response['success']) {
            throw new Exception($response['error'] ?? 'Connection to AI Service failed or timed out.');
        }

        $resData = $response['data'] ?? [];
        if (empty($resData['success'])) {
            throw new Exception($resData['message'] ?? 'AI Provider failed to generate caption.');
        }

        $gatewayData = $resData['data'] ?? [];
        $captionData = $gatewayData['caption'] ?? null;
        $model = $gatewayData['model'] ?? 'unknown';
        $provider = $resData['provider'] ?? 'gateway';

        // 4. Validate output JSON structure
        if (empty($captionData)) {
            throw new Exception("AI Provider returned an invalid or empty caption response.");
        }

        $requiredFields = [
            'Platform',
            'Headline',
            'Caption',
            'Call To Action',
            'Primary Keywords',
            'Suggested Hashtags',
            'Emoji Recommendation',
            'Tone',
            'Language',
            'Estimated Character Count'
        ];

        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (!isset($captionData[$field])) {
                $missingFields[] = $field;
            }
        }
        if (!empty($missingFields)) {
            Log::warning("AI Caption response is missing fields: " . implode(', ', $missingFields), [
                'raw_caption' => $captionData
            ]);
            throw new Exception("AI Response validation failed: Missing fields (" . implode(', ', $missingFields) . ").");
        }

        // 5. Store caption details
        // Delete existing captions for this content calendar entry to avoid duplicates on regeneration.
        ContentCaption::where('content_calendar_id', $contentCalendar->id)->delete();

        // Calculate counts
        $wordsCount = str_word_count($captionData['Caption']);
        $characterCount = strlen($captionData['Caption']);

        $caption = ContentCaption::create([
            'organization_id'     => $organizationId,
            'content_calendar_id' => $contentCalendar->id,
            'platform'            => $captionData['Platform'],
            'headline'            => $captionData['Headline'],
            'caption'             => $captionData['Caption'],
            'hashtags'            => $captionData['Suggested Hashtags'],
            'cta'                 => $captionData['Call To Action'],
            'keywords'            => $captionData['Primary Keywords'],
            'emoji_style'         => $captionData['Emoji Recommendation'],
            'tone'                => $captionData['Tone'],
            'language'            => $captionData['Language'],
            'word_count'          => $wordsCount,
            'character_count'     => $characterCount,
            'status'              => 'Draft',
            'provider'            => $provider,
            'model'               => $model,
            'generated_at'        => now(),
        ]);

        // Log details (Never log prompts or API keys)
        Log::info("AI Caption generated successfully.", [
            'organization_id' => $organizationId,
            'provider'        => $provider,
            'model'           => $model,
            'execution_time'  => $executionTime,
            'platform'        => $captionData['Platform'],
        ]);

        return $caption;
    }
}
