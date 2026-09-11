<?php

namespace App\Policies;

use App\Models\Owner;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OwnerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canManagePeople();
    }

    public function view(User $user, Owner $owner): bool
    {
        return $user->canManagePeople();
    }

    public function create(User $user): bool
    {
        return $user->canManagePeople();
    }

    public function update(User $user, Owner $owner): bool
    {
        return $user->canManagePeople();
    }

    public function delete(User $user, Owner $owner): bool
    {
        return $user->canManagePeople();
    }
}
