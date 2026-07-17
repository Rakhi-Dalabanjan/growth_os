<?php

namespace App\Policies;

use App\Models\MarketingStrategy;
use App\Models\User;

class MarketingStrategyPolicy
{
    /**
     * Determine whether the user can view the marketing strategy.
     */
    public function view(User $user, MarketingStrategy $marketingStrategy): bool
    {
        return $user->organization_id !== null && $user->organization_id === $marketingStrategy->organization_id;
    }

    /**
     * Determine whether the user can update/regenerate the marketing strategy.
     */
    public function update(User $user, MarketingStrategy $marketingStrategy): bool
    {
        return $user->organization_id !== null && $user->organization_id === $marketingStrategy->organization_id;
    }
}
