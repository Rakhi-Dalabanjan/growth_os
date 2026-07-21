<?php

namespace App\Policies;

use App\Models\ContentCalendar;
use App\Models\User;

class ContentCalendarPolicy
{
    /**
     * Determine whether the user can view the content calendar entry.
     */
    public function view(User $user, ContentCalendar $contentCalendar): bool
    {
        return $user->organization_id !== null && $user->organization_id === $contentCalendar->organization_id;
    }

    /**
     * Determine whether the user can update the content calendar entry.
     */
    public function update(User $user, ContentCalendar $contentCalendar): bool
    {
        return $user->organization_id !== null && $user->organization_id === $contentCalendar->organization_id;
    }

    /**
     * Determine whether the user can delete the content calendar entry.
     */
    public function delete(User $user, ContentCalendar $contentCalendar): bool
    {
        return $user->organization_id !== null && $user->organization_id === $contentCalendar->organization_id;
    }
}
