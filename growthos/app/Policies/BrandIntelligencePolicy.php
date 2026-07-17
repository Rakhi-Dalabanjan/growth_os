<?php

namespace App\Policies;

use App\Models\BrandIntelligence;
use App\Models\User;

class BrandIntelligencePolicy
{
    /**
     * Determine whether the user can view the brand intelligence.
     */
    public function view(User $user, BrandIntelligence $brandIntelligence): bool
    {
        return $user->organization_id !== null && $user->organization_id === $brandIntelligence->organization_id;
    }

    /**
     * Determine whether the user can update/regenerate the brand intelligence.
     */
    public function update(User $user, BrandIntelligence $brandIntelligence): bool
    {
        return $user->organization_id !== null && $user->organization_id === $brandIntelligence->organization_id;
    }
}
