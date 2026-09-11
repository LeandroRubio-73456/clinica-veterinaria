<?php

namespace App\Policies;

use App\Models\OperatingRoom;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OperatingRoomPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'administrativo',
            'veterinario',
        ], true);
    }

    public function view(User $user, $model): bool
    {
        return in_array($user->role, [
            'admin',
            'administrativo',
            'veterinario',
        ], true);
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, $model): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, $model): bool
    {
        return $user->isAdmin();
    }
}
