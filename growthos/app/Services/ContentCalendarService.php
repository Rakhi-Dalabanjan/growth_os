<?php

namespace App\Services;

use App\Models\MarketingStrategy;
use App\Models\ContentCalendar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;

class ContentCalendarService extends AIService
{
    /**
     * Generate a content calendar for a selected month and year.
     *
     * @param MarketingStrategy $marketingStrategy
     * @param int $month
     * @param int $year
     * @return \Illuminate\Database\Eloquent\Collection
     * @throws Exception
     */
    public function generate(MarketingStrategy $marketingStrategy, int $month, int $year)
    {
        // 1. Prepare request inputs from marketing strategy
        $payload = [
            'strategy_name'     => $marketingStrategy->strategy_name ?? 'Growth Strategy',
            'business_goal'     => $marketingStrategy->business_goal ?? 'Grow Brand',
            'marketing_goal'    => $marketingStrategy->marketing_goal ?? 'Reach Target Customers',
            'posting_frequency' => $marketingStrategy->posting_frequency ?? '3 posts per week',
            'platforms'         => $marketingStrategy->recommended_platforms ?? [],
            'pillars'           => $marketingStrategy->content_pillars ?? [],
            'campaigns'         => $marketingStrategy->campaign_ideas ?? [],
            'month'             => $month,
            'year'              => $year,
        ];

        // 2. Perform FastAPI POST request
        $start = microtime(true);
        $response = $this->request('POST', '/ai/calendar/generate', $payload);
        $executionTime = round(microtime(true) - $start, 2);

        // 3. Handle connection level errors
        if (!$response['success']) {
            throw new Exception($response['error'] ?? 'Connection to AI Service failed or timed out.');
        }

        $resData = $response['data'] ?? [];
        if (empty($resData['success'])) {
            throw new Exception($resData['message'] ?? 'AI Provider failed to generate content calendar.');
        }

        $gatewayData = $resData['data'] ?? [];
        $calendarItems = $gatewayData['calendar'] ?? null;
        $model = $gatewayData['model'] ?? 'unknown';
        $provider = $resData['provider'] ?? 'gateway';

        // 4. Validate output JSON structure
        if (empty($calendarItems) || !is_array($calendarItems)) {
            throw new Exception("AI Provider returned an invalid or empty calendar response.");
        }

        // Required fields inside each post item
        $requiredItemFields = [
            'Date',
            'Platform',
            'Topic',
            'Working Title',
            'Content Pillar',
            'Campaign',
            'Goal',
            'Content Type',
            'Post Format',
            'Priority'
        ];

        foreach ($calendarItems as $index => $item) {
            $missingFields = [];
            foreach ($requiredItemFields as $field) {
                if (!isset($item[$field]) || is_null($item[$field])) {
                    $missingFields[] = $field;
                }
            }
            if (!empty($missingFields)) {
                Log::warning("AI Content Calendar item at index {$index} is missing fields: " . implode(', ', $missingFields), [
                    'raw_item' => $item
                ]);
                throw new Exception("AI Response validation failed: Missing calendar fields at item {$index} (" . implode(', ', $missingFields) . ").");
            }
        }

        // 5. Store / Update entries in transaction
        return DB::transaction(function () use ($calendarItems, $marketingStrategy, $month, $year, $provider, $model, $executionTime) {
            // Delete existing records for this organization, month, and year (replace selected month only)
            ContentCalendar::where('organization_id', $marketingStrategy->organization_id)
                ->where('month', $month)
                ->where('year', $year)
                ->delete();

            $createdEntries = [];
            $generatedAt = now();

            foreach ($calendarItems as $item) {
                // Ensure date matches selected month/year, or fall back/validate
                $dateStr = $item['Date'];
                
                // Format suggested CTA inside notes if present
                $notes = "";
                if (!empty($item['Suggested CTA'])) {
                    $notes = "Suggested CTA: " . $item['Suggested CTA'];
                }

                $createdEntries[] = ContentCalendar::create([
                    'organization_id'       => $marketingStrategy->organization_id,
                    'marketing_strategy_id' => $marketingStrategy->id,
                    'month'                 => $month,
                    'year'                  => $year,
                    'platform'              => $item['Platform'],
                    'title'                 => $item['Working Title'],
                    'topic'                 => $item['Topic'],
                    'content_pillar'        => $item['Content Pillar'],
                    'campaign_name'         => $item['Campaign'],
                    'goal'                  => $item['Goal'],
                    'content_type'          => $item['Content Type'],
                    'post_format'           => $item['Post Format'],
                    'status'                => 'Draft', // Default status is Draft
                    'planned_date'          => $dateStr,
                    'planned_time'          => '09:00:00', // Default planned time
                    'priority'              => $item['Priority'] ?? 'Medium',
                    'notes'                 => $notes,
                    'provider'              => $provider,
                    'model'                 => $model,
                    'generated_at'          => $generatedAt,
                ]);
            }

            // Log details
            Log::info("AI Content Calendar generated successfully.", [
                'organization_id' => $marketingStrategy->organization_id,
                'provider'        => $provider,
                'model'           => $model,
                'execution_time'  => $executionTime,
                'month'           => $month,
                'year'            => $year,
                'posts_count'     => count($createdEntries)
            ]);

            return collect($createdEntries);
        });
    }
}
