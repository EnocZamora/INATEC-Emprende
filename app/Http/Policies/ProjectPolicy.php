<?php

declare(strict_types=1);

namespace App\Http\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Guests may browse the public catalog.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Published projects are public; drafts and suspended ones stay private.
     */
    public function view(?User $user, Project $project): bool
    {
        if ($project->isPublished()) {
            return true;
        }

        if ($user === null) {
            return false;
        }

        return $user->isAdmin() || $user->id === $project->user_id;
    }

    /**
     * Any authenticated student or administrator may publish a project.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Strict IDOR guard: only the owner or an administrator may update.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->isAdmin() || $user->id === $project->user_id;
    }

    /**
     * Strict IDOR guard: only the owner or an administrator may delete.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->isAdmin() || $user->id === $project->user_id;
    }

    /**
     * Restoring soft-deleted projects is an administrative action.
     */
    public function restore(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }

    /**
     * Permanent deletion is an administrative action.
     */
    public function forceDelete(User $user, Project $project): bool
    {
        return $user->isAdmin();
    }
}
