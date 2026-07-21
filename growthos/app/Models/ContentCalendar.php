<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentCalendar extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'content_calendars';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'marketing_strategy_id',
        'month',
        'year',
        'platform',
        'title',
        'topic',
        'content_pillar',
        'campaign_name',
        'goal',
        'content_type',
        'post_format',
        'status',
        'planned_date',
        'planned_time',
        'priority',
        'notes',
        'provider',
        'model',
        'generated_at',
    ];

    /**
     * Get the organization that owns this content calendar entry.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the marketing strategy associated with this entry.
     */
    public function marketingStrategy(): BelongsTo
    {
        return $this->belongsTo(MarketingStrategy::class);
    }

    /**
     * Get the caption associated with this content calendar entry.
     */
    public function caption(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ContentCaption::class, 'content_calendar_id');
    }
}
