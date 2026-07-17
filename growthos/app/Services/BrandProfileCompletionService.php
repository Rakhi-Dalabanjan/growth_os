<?php

namespace App\Services;

use App\Models\BrandProfile;

class BrandProfileCompletionService
{
    /**
     * The fields to evaluate for completion.
     */
    protected array $fields = [
        'brand_name',
        'tagline',
        'business_description',
        'mission',
        'vision',
        'target_audience',
        'primary_market',
        'brand_tone',
        'formality',
        'language',
        'emoji_style',
        'primary_color',
        'secondary_color',
        'accent_color',
        'primary_font',
        'secondary_font',
        'primary_cta',
        'secondary_cta',
        'preferred_words',
        'restricted_words',
        'competitor_names',
        'approved_claims',
        'restricted_claims',
        'legal_disclaimer',
    ];

    /**
     * Calculate the completion status and score.
     */
    public function calculate(?BrandProfile $profile): array
    {
        if (!$profile) {
            return [
                'percentage' => 0,
                'status' => 'Not Started',
                'color' => 'secondary',
            ];
        }

        $totalFields = count($this->fields);
        $filledFields = 0;

        foreach ($this->fields as $field) {
            $value = $profile->{$field};

            if (is_array($value)) {
                // If it is an array, check if it's not empty and contains non-empty strings
                $filtered = array_filter($value, fn($item) => !is_null($item) && trim((string)$item) !== '');
                if (count($filtered) > 0) {
                    $filledFields++;
                }
            } else {
                // Otherwise check if string is not null and not empty
                if (!is_null($value) && trim((string)$value) !== '') {
                    $filledFields++;
                }
            }
        }

        $percentage = (int) round(($filledFields / $totalFields) * 100);

        // Determine label and Bootstrap color
        if ($percentage === 0) {
            $status = 'Not Started';
            $color = 'danger';
        } elseif ($percentage <= 25) {
            $status = 'Basic';
            $color = 'warning';
        } elseif ($percentage <= 50) {
            $status = 'Good';
            $color = 'info';
        } elseif ($percentage <= 75) {
            $status = 'Almost Ready';
            $color = 'primary';
        } else {
            $status = $percentage === 100 ? 'Complete' : 'Almost Ready';
            $color = $percentage === 100 ? 'success' : 'primary';
        }

        return [
            'percentage' => $percentage,
            'status' => $status,
            'color' => $color,
            'filled' => $filledFields,
            'total' => $totalFields,
        ];
    }
}
