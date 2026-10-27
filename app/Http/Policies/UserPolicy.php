<?php

declare(strict_types=1);

namespace App\Http\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Only administrators may list the user directory.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Administrators see every account; students only see themselves.
     */
    public function view(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->id === $target->id;
    }

    /**
     * Account creation is an administrative action.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Administrators manage any account; students manage only their own.
     */
    public function update(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->id === $target->id;
    }

    /**
     * Students can only edit their own profile data.
     */
    public function updateProfile(User $user, User $target): bool
    {
        return $user->isAdmin() || $user->id === $target->id;
    }

    /**
     * Role promotion/demotion is strictly administrative and never self-assigned.
     */
    public function updateRole(User $user, User $target): bool
    {
        return $user->isAdmin() && $user->id !== $target->id;
    }

    /**
     * Administrators may delete accounts, but never their own.
     */
    public function delete(User $user, User $target): bool
    {
        return $user->isAdmin() && $user->id !== $target->id;
    }
}
