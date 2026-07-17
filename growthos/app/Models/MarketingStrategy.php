<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingStrategy extends Model
{
    use HasFactory;

    protected $table = 'marketing_strategies';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'brand_intelligence_id',
        'strategy_name',
        'business_goal',
        'marketing_goal',
        'recommended_platforms',
        'content_pillars',
        'campaign_ideas',
        'posting_frequency',
        'recommended_formats',
        'tone_guidelines',
        'audience_segments',
        'hashtags_strategy',
        'cta_strategy',
        'kpis',
        'growth_recommendations',
        'risk_considerations',
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
            'recommended_platforms'  => 'array',
            'content_pillars'        => 'array',
            'campaign_ideas'         => 'array',
            'recommended_formats'    => 'array',
            'tone_guidelines'        => 'array',
            'audience_segments'      => 'array',
            'hashtags_strategy'      => 'array',
            'cta_strategy'           => 'array',
            'kpis'                   => 'array',
            'growth_recommendations' => 'array',
            'risk_considerations'    => 'array',
            'generated_at'           => 'datetime',
            'execution_time'         => 'float',
            'confidence_score'       => 'integer',
        ];
    }

    /**
     * Get the organization that owns this marketing strategy.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the brand intelligence profile this strategy is based on.
     */
    public function brandIntelligence(): BelongsTo
    {
        return $this->belongsTo(BrandIntelligence::class);
    }
}
