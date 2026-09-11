<?php

namespace App\Policies;

use App\Models\Pet;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PetPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->canManagePeople();
    }

    public function view(User $user, Pet $pet): bool
    {
        return $user->canManagePeople();
    }

    public function create(User $user): bool
    {
        return $user->canManagePeople();
    }

    public function update(User $user, Pet $pet): bool
    {
        return $user->canManagePeople();
    }

    public function delete(User $user, Pet $pet): bool
    {
        return $user->canManagePeople();
    }
}
