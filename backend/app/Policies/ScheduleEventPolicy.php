<?php

namespace App\Policies;

use App\Models\ScheduleEvent;
use App\Models\User;

class ScheduleEventPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view schedules (cross-visibility)
        // Artists will be filtered to see only their own in the controller
        return $user->can('view-all-schedules') || $user->isArtist();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ScheduleEvent $scheduleEvent): bool
    {
        // Super Admin and staff can view all
        if ($user->can('view-all-schedules')) {
            return true;
        }

        // Artists can only view events they're assigned to
        if ($user->isArtist() && $user->member_id) {
            return $scheduleEvent->members()->where('members.id', $user->member_id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, string $type): bool
    {
        // Check type-specific create permission
        return $user->can("create-{$type}");
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ScheduleEvent $scheduleEvent): bool
    {
        // Super Admin can update anything
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Only the creator can edit their own events
        return $scheduleEvent->created_by === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ScheduleEvent $scheduleEvent): bool
    {
        // Super Admin can delete anything
        if ($user->hasRole('Super Admin')) {
            return true;
        }

        // Only the creator can delete their own events
        return $scheduleEvent->created_by === $user->id;
    }

    /**
     * Determine whether the user can override conflicts.
     */
    public function overrideConflicts(User $user): bool
    {
        return $user->can('override-conflicts');
    }
}
