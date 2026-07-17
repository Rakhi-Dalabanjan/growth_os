<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialAccount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organization_id',
        'platform',
        'platform_user_id',
        'page_id',
        'page_name',
        'instagram_business_id',
        'account_name',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'permissions',
        'connected_at',
        'status',
        'last_sync',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'access_token' => 'encrypted',
            'refresh_token' => 'encrypted',
            'permissions' => 'array',
            'token_expires_at' => 'datetime',
            'connected_at' => 'datetime',
            'last_sync' => 'datetime',
        ];
    }

    /**
     * Get the organization that owns this social account.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
