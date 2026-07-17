<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'brand_name',
        'tagline',
        'business_description',
        'mission',
        'vision',
        'primary_market',
        'target_audience',
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
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'preferred_words'   => 'array',
            'restricted_words'  => 'array',
            'competitor_names'  => 'array',
            'approved_claims'   => 'array',
            'restricted_claims' => 'array',
        ];
    }

    /**
     * Get the organization that owns this brand profile.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
