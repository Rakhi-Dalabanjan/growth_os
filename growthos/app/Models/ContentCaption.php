<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentCaption extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'content_captions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'organization_id',
        'content_calendar_id',
        'platform',
        'headline',
        'caption',
        'hashtags',
        'cta',
        'keywords',
        'emoji_style',
        'tone',
        'language',
        'word_count',
        'character_count',
        'status',
        'provider',
        'model',
        'generated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'hashtags' => 'array',
        'keywords' => 'array',
        'generated_at' => 'datetime',
    ];

    /**
     * Get the organization that owns this caption.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the content calendar entry associated with this caption.
     */
    public function contentCalendar(): BelongsTo
    {
        return $this->belongsTo(ContentCalendar::class);
    }
}
