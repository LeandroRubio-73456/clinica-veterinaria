<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;

#[Fillable(['name', 'username', 'email', 'password', 'role', 'role_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Determina si el usuario puede administrar catálogos y usuarios.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isAdministrative(): bool
    {
        return $this->hasRole('administrativo');
    }

    public function isVeterinarian(): bool
    {
        return $this->hasRole('veterinario');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('propietario');
    }

    public function roleProfile()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function owner()
    {
        return $this->hasOne(Owner::class);
    }

    public function hasRole(string $slug): bool
    {
        return $this->roleProfile?->slug === $slug || $this->role === $slug;
    }

    public function hasPermission(string $slug): bool
    {
        return $this->isAdmin() || $this->roleProfile?->permissions()->where('slug', $slug)->exists();
    }

    public function veterinarian()
    {
        return $this->hasOne(Veterinarian::class);
    }

    public function canManagePeople(): bool
    {
        return $this->isAdmin() || $this->isAdministrative();
    }

    public function canManageCatalogs(): bool
    {
        return $this->isAdmin();
    }

    /**
     * Determina si el usuario puede gestionar información operativa.
     */
    public function canScheduleSurgeries(): bool
    {
        return $this->isAdmin() || $this->isAdministrative();
    }

    /**
     * Determina si el usuario puede iniciar y finalizar cirugías.
     */
    public function canOperateSurgeries(): bool
    {
        return $this->isAdmin() || $this->isVeterinarian();
    }

    public function canViewReports(): bool
    {
        return $this->isAdmin() || $this->isAdministrative();
    }
}
