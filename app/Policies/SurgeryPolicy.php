<?php

namespace App\Policies;

use App\Models\Surgery;
use App\Models\User;

class SurgeryPolicy
{
    public function viewAny(User $user): bool
    {
        return in_array($user->role, [
            'admin',
            'administrativo',
            'veterinario',
        ], true) && (!$user->isVeterinarian() || $user->veterinarian?->state === 'active');
    }

    public function view(User $user, Surgery $surgery): bool
    {
        if ($user->isVeterinarian()) {
            return $user->veterinarian?->state === 'active'
                && $user->veterinarian->id === $surgery->veterinarian_id;
        }

        return in_array($user->role, [
            'admin',
            'administrativo',
            'veterinario',
        ], true);
    }

    public function create(User $user): bool
    {
        return $user->canScheduleSurgeries();
    }

    public function update(User $user, Surgery $surgery): bool
    {
        return $user->canScheduleSurgeries()
            ;
    }

    public function delete(User $user, Surgery $surgery): bool
    {
        return $user->canScheduleSurgeries()
            && $surgery->state === 'scheduled';
    }

    public function start(User $user, Surgery $surgery): bool
    {
        return $user->canOperateSurgeries()
            && (!$user->isVeterinarian() || ($user->veterinarian?->state === 'active'
                && $user->veterinarian->id === $surgery->veterinarian_id));
    }

    public function complete(User $user, Surgery $surgery): bool
    {
        return $user->canOperateSurgeries()
            && (!$user->isVeterinarian() || ($user->veterinarian?->state === 'active'
                && $user->veterinarian->id === $surgery->veterinarian_id))
            ;
    }

    public function cancel(User $user, Surgery $surgery): bool
    {
        return $user->canScheduleSurgeries()
            ;
    }

    public function noShow(User $user, Surgery $surgery): bool
    {
        return $user->canScheduleSurgeries()
            ;
    }

    public function updateNotes(User $user, Surgery $surgery): bool
    {
        return $user->canOperateSurgeries()
            && (!$user->isVeterinarian() || ($user->veterinarian?->state === 'active'
                && $user->veterinarian->id === $surgery->veterinarian_id))
            && $surgery->state === 'in_progress';
    }
}
