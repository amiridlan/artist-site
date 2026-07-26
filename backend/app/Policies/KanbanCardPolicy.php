<?php

namespace App\Policies;

use App\Models\KanbanCard;
use App\Models\User;

class KanbanCardPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('manage-kanban');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, KanbanCard $kanbanCard): bool
    {
        return $user->can('manage-kanban');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage-kanban');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, KanbanCard $kanbanCard): bool
    {
        return $user->can('manage-kanban');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, KanbanCard $kanbanCard): bool
    {
        return $user->can('manage-kanban');
    }

    /**
     * Determine whether the user can confirm a card (move to confirmed stage).
     * This requires checking if they can create the corresponding event type.
     */
    public function confirm(User $user, KanbanCard $kanbanCard): bool
    {
        // Must have manage-kanban permission
        if (!$user->can('manage-kanban')) {
            return false;
        }

        // Must have permission to create the event type
        return $user->can("create-{$kanbanCard->type}");
    }
}
