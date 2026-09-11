<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Permite continuar únicamente si el usuario autenticado posee uno de los
     * roles declarados en la ruta.
     */
    public function handle(
        Request $request,
        Closure $next,
        ...$roles
    ): Response {
        $user = $request->user();

        $routeName = $request->route()?->getName() ?? '';
        $permission = match (true) {
            str_starts_with($routeName, 'roles.') => 'roles.manage',
            str_starts_with($routeName, 'users.') => 'users.manage',
            str_starts_with($routeName, 'reports.') => 'reports.view',
            str_starts_with($routeName, 'operating-rooms.') || str_starts_with($routeName, 'species.') || str_starts_with($routeName, 'specialties.') || str_starts_with($routeName, 'surgery-types.') => 'catalogs.manage',
            str_starts_with($routeName, 'owners.') || str_starts_with($routeName, 'pets.') => 'people.manage',
            str_starts_with($routeName, 'surgeries.start') || str_starts_with($routeName, 'surgeries.complete') => 'surgeries.operate',
            in_array($routeName, ['surgeries.index', 'surgeries.calendar', 'surgeries.calendar.events', 'surgeries.show'], true) => 'surgeries.view',
            str_starts_with($routeName, 'surgeries.') => 'surgeries.manage',
            default => null,
        };

        if (!$user || (!collect($roles)->contains(fn ($role) => $user->hasRole($role)) && (!$permission || !$user->hasPermission($permission)))) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}
