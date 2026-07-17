<?php

namespace App\Policies;

use App\Models\BrandProfile;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BrandProfilePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BrandProfile $brandProfile): bool
    {
        return $user->organization_id !== null && $user->organization_id === $brandProfile->organization_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Must belong to an organization to have a brand profile
        return $user->organization_id !== null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BrandProfile $brandProfile): bool
    {
        return $user->organization_id !== null && $user->organization_id === $brandProfile->organization_id;
    }
}
