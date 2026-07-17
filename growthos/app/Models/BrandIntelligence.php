<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandIntelligence extends Model
{
    use HasFactory;

    protected $table = 'brand_intelligence';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'brand_profile_id',
        'summary',
        'brand_personality',
        'brand_voice',
        'ideal_customer',
        'customer_problems',
        'customer_goals',
        'marketing_objectives',
        'competitor_summary',
        'recommended_content_pillars',
        'recommended_posting_frequency',
        'recommended_cta',
        'recommended_hashtags',
        'strengths',
        'weaknesses',
        'opportunities',
        'risks',
        'confidence_score',
        'provider',
        'model',
        'execution_time',
        'generated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'brand_personality'           => 'array',
            'brand_voice'                 => 'array',
            'ideal_customer'              => 'array',
            'customer_problems'           => 'array',
            'customer_goals'              => 'array',
            'marketing_objectives'        => 'array',
            'recommended_content_pillars' => 'array',
            'recommended_cta'             => 'array',
            'recommended_hashtags'        => 'array',
            'strengths'                   => 'array',
            'weaknesses'                  => 'array',
            'opportunities'               => 'array',
            'risks'                       => 'array',
            'generated_at'                => 'datetime',
            'execution_time'              => 'float',
            'confidence_score'            => 'integer',
        ];
    }

    /**
     * Get the organization that owns this brand intelligence.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the brand profile this intelligence is based on.
     */
    public function brandProfile(): BelongsTo
    {
        return $this->belongsTo(BrandProfile::class);
    }
}
