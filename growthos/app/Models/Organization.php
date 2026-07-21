<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Organization extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'website',
        'industry',
        'business_email',
        'phone',
        'country',
        'timezone',
        'logo',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the users that belong to this organization.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Get the brand profile associated with this organization.
     */
    public function brandProfile(): HasOne
    {
        return $this->hasOne(BrandProfile::class);
    }

    /**
     * Get the brand intelligence associated with this organization.
     */
    public function brandIntelligence(): HasOne
    {
        return $this->hasOne(BrandIntelligence::class);
    }

    /**
     * Get the active marketing strategy associated with this organization.
     */
    public function marketingStrategy(): HasOne
    {
        return $this->hasOne(MarketingStrategy::class);
    }

    /**
     * Get the social accounts connected to this organization.
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    /**
     * Get the content calendar posts associated with this organization.
     */
    public function contentCalendars(): HasMany
    {
        return $this->hasMany(ContentCalendar::class);
    }

    /**
     * Get the content captions associated with this organization.
     */
    public function contentCaptions(): HasMany
    {
        return $this->hasMany(ContentCaption::class);
    }

    /**
     * Get the logo URL accessor.
     */
    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return Storage::disk('public')->url($this->logo);
        }
        return null;
    }

    /**
     * Check if organization is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
