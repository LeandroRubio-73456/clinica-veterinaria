@props([
    'title' => 'Panel',
    'maxWidth' => 'max-w-7xl',
])

@php
    $isActive = fn(string $pattern) => request()->is($pattern);
    $user = auth()->user();
    $initials = $user
        ? collect(explode(' ', trim($user->name ?? '')))
            ->filter()
            ->map(fn($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->join('')
        : 'IN';
@endphp

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — Clínica Veterinaria del Municipio</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-simple.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-base text-ink antialiased font-body" x-data="{ sidebarOpen: false, catalogsOpen: false, notificationsOpen: false, confirmOpen: false, confirmForm: null, confirmTitle: '', confirmMessage: '', confirmLabel: 'Confirmar' }" @confirm-action.window="confirmForm = $event.detail.form; confirmTitle = $event.detail.title; confirmMessage = $event.detail.message; confirmLabel = $event.detail.label || 'Confirmar'; confirmOpen = true; $nextTick(() => $refs.confirmCancel.focus())" @keydown.escape.window="confirmOpen = false">
    <a href="#contenido-principal"
        class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-surface focus:px-4 focus:py-3 focus:text-ink focus:shadow-lg">
        Saltar al contenido principal
    </a>

    <div class="flex min-h-screen">
        <aside id="sidebar" aria-label="Navegación principal"
            class="fixed inset-y-0 left-0 z-40 flex w-64 flex-col border-r border-hairline bg-surface transform transition-transform lg:static lg:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            <div class="flex h-16 items-center gap-3 border-b border-hairline px-5">
                <a href="{{ url('/dashboard') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-simple.png') }}"
                        alt="" class="h-9 w-9 shrink-0 object-contain" aria-hidden="true">
                    <p class="font-display text-sm font-semibold leading-tight text-ink">Clínica<br>Veterinaria</p>
                </a>
                <button type="button"
                    class="ml-auto rounded p-2 text-muted hover:bg-base focus:outline-none focus:ring-2 focus:ring-teal lg:hidden"
                    @click="sidebarOpen = false" aria-label="Cerrar menú">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <nav class="flex-1 space-y-5 overflow-y-auto px-3 py-5 text-sm font-medium"
                aria-label="Secciones del sistema">
                <div class="space-y-1">
                    <p class="px-3 pb-2 font-mono text-[10px] uppercase tracking-wider text-muted">Operación</p>

                    @foreach ([[route('dashboard'), 'Dashboard', 'dashboard', null], [route('surgeries.index'), 'Cirugías', 'surgeries*', null], [route('owners.index'), 'Dueños', 'owners*', ['admin', 'administrativo']], [route('pets.index'), 'Mascotas', 'pets*', ['admin', 'administrativo']], [route('veterinarians.index'), 'Veterinarios', 'veterinarians*', 'admin'], [route('operating-rooms.index'), 'Quirófanos', 'operating-rooms*', 'admin']] as [$href, $label, $pattern, $roles])
                        @if ($roles === 'admin' && !$user?->isAdmin())
                            @continue
                        @elseif (is_array($roles) && !in_array($user?->role, $roles, true))
                            @continue
                        @endif

                        @php($active = $isActive($pattern))

                        <a href="{{ $href }}" @click="sidebarOpen = false" @class([
                            'flex items-center gap-3 rounded-md px-3 py-2.5 transition-colors focus:outline-none focus:ring-2 focus:ring-teal',
                            'bg-teal-light text-teal-dark' => $active,
                            'text-muted hover:bg-base hover:text-ink' => !$active,
                        ])
                            @if ($active) aria-current="page" @endif>
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
                @if (auth()->user()->canManageCatalogs())
                    <div class="space-y-1">
                        <button type="button"
                            class="flex w-full items-center justify-between rounded-md px-3 pb-2 text-left font-mono text-[10px] uppercase tracking-wider text-muted focus:outline-none focus:ring-2 focus:ring-teal"
                            @click="catalogsOpen = !catalogsOpen" :aria-expanded="catalogsOpen.toString()"
                            aria-controls="catalogs-menu">
                            <span>Catálogos</span>
                            <svg class="h-3.5 w-3.5 transition-transform" :class="catalogsOpen ? 'rotate-180' : ''"
                                viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd"
                                    d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.51a.75.75 0 0 1-1.08 0l-4.25-4.51a.75.75 0 0 1 .02-1.06Z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="catalogs-menu" x-show="catalogsOpen" x-cloak class="space-y-1 pl-2">
                            <a href="/surgery-types"
                                class="block rounded-md px-3 py-2 text-muted hover:bg-base hover:text-ink focus:outline-none focus:ring-2 focus:ring-teal">Tipos
                                de cirugía</a>
                            <a href="/species"
                                class="block rounded-md px-3 py-2 text-muted hover:bg-base hover:text-ink focus:outline-none focus:ring-2 focus:ring-teal">Especies</a>
                            <a href="/specialties"
                                class="block rounded-md px-3 py-2 text-muted hover:bg-base hover:text-ink focus:outline-none focus:ring-2 focus:ring-teal">Especialidades</a>
                        </div>
                    </div>
                @endif
                @if (auth()->user()->canViewReports())
                    <div class="space-y-1 border-t border-hairline pt-5">
                        <p class="px-3 pb-2 font-mono text-[10px] uppercase tracking-wider text-muted">Administración
                        </p>
                        <a href="/reports"
                            class="flex items-center gap-3 rounded-md px-3 py-2.5 text-muted hover:bg-base hover:text-ink focus:outline-none focus:ring-2 focus:ring-teal">Reportes</a>
                        @can('viewAny', App\Models\User::class)
                            <a href="/users"
                                class="flex items-center gap-3 rounded-md px-3 py-2.5 text-muted hover:bg-base hover:text-ink focus:outline-none focus:ring-2 focus:ring-teal">Usuarios</a>
                        @endcan
                        @can('viewAny', App\Models\User::class)
                            <a href="{{ route('roles.index') }}"
                                class="flex items-center gap-3 rounded-md px-3 py-2.5 text-muted hover:bg-base hover:text-ink focus:outline-none focus:ring-2 focus:ring-teal">Roles y permisos</a>
                        @endcan
                    </div>
                @endif
            </nav>

            <div class="border-t border-hairline px-3 py-4">
                <div class="flex items-center gap-3 px-3 py-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-full bg-teal-light font-mono text-xs font-semibold uppercase text-teal-dark"
                        aria-hidden="true">{{ $initials }}</div>
                    <div class="min-w-0 leading-tight">
                        <p class="truncate text-sm font-medium text-ink">{{ $user?->name ?? 'Usuario' }}</p>
                        <p class="truncate font-mono text-[11px] uppercase text-muted">
                            {{ $user?->role ?? 'Sesión activa' }}</p>
                    </div>
                </div>
                <a class="w-full rounded-md px-3 py-2 text-left text-sm text-muted transition-colors hover:text-teal focus:outline-none focus:ring-2 focus:ring-teal"
                    href="{{ route('profile.edit') }}">
                    Mi perfil
                </a>
                @if (Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                        @csrf
                        <button type="submit"
                            class="w-full rounded-md px-3 py-2 text-left text-sm text-muted transition-colors hover:text-alert focus:outline-none focus:ring-2 focus:ring-teal">Cerrar
                            sesión</button>
                    </form>
                @endif
            </div>
        </aside>

        <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-ink/40 lg:hidden"
            aria-hidden="true"></div>

        <div class="min-w-0 flex-1">
            <header class="flex h-16 items-center justify-between border-b border-hairline bg-surface px-6 lg:px-8">
                <div class="flex items-center gap-4">
                    <button type="button"
                        class="rounded p-2 text-muted hover:bg-base focus:outline-none focus:ring-2 focus:ring-teal lg:hidden"
                        @click="sidebarOpen = true" aria-controls="sidebar" :aria-expanded="sidebarOpen.toString()"
                        aria-label="Abrir menú">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 12h18M3 6h18M3 18h18" />
                        </svg>
                    </button>
                    <h1 class="font-display text-lg font-semibold text-ink">{{ $title }}</h1>
                </div>
                <div class="flex items-center gap-4">
                    <div class="relative" @click.outside="notificationsOpen = false">
                        <button type="button" class="relative rounded-md p-2 text-muted hover:bg-base hover:text-ink focus:outline-none focus:ring-2 focus:ring-teal" @click="notificationsOpen = !notificationsOpen" :aria-expanded="notificationsOpen.toString()" aria-controls="notifications-menu" aria-label="Notificaciones">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4" /></svg>
                            <span id="notification-count" @class(['absolute -right-0.5 -top-0.5 h-2.5 w-2.5 rounded-full bg-alert' => ($notifications ?? collect())->isNotEmpty(), 'hidden' => ($notifications ?? collect())->isEmpty()]) aria-hidden="true"></span>
                        </button>
                        <div id="notifications-menu" x-show="notificationsOpen" x-cloak class="absolute right-0 z-50 mt-2 w-80 overflow-hidden rounded-md border border-hairline bg-surface shadow-xl" role="dialog" aria-label="Notificaciones recientes">
                            <div class="flex items-center justify-between border-b border-hairline px-4 py-3"><span class="text-sm font-semibold text-ink">Notificaciones</span><span id="notification-pending" class="text-xs text-muted">{{ ($notifications ?? collect())->count() }} pendientes</span></div>
                            <div id="notification-items" class="max-h-80 overflow-y-auto">
                                @forelse (($notifications ?? collect())->take(5) as $notification)
                                    <a href="{{ $notification['url'] }}" class="block border-b border-hairline px-4 py-3 hover:bg-base focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal"><span class="block text-sm font-semibold {{ $notification['priority'] === 'high' ? 'text-alert' : 'text-ink' }}">{{ $notification['title'] }}</span><span class="mt-1 block text-xs leading-5 text-muted">{{ $notification['message'] }}</span></a>
                                @empty
                                    <p class="px-4 py-6 text-center text-sm text-muted">No hay notificaciones pendientes.</p>
                                @endforelse
                            </div>
                            <a href="{{ route('notifications.index') }}" class="block border-t border-hairline px-4 py-3 text-center text-sm font-semibold text-teal hover:bg-base">Ver todas</a>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">{{ $actions ?? '' }}</div>
                </div>
            </header>
            <main id="contenido-principal" tabindex="-1"
                class="{{ $maxWidth }} mx-auto px-6 py-10 outline-none lg:px-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    <div x-show="confirmOpen" x-cloak class="fixed inset-0 z-[60] flex items-center justify-center bg-ink/50 px-6" role="presentation">
        <div class="w-full max-w-md rounded-lg border border-hairline bg-surface p-6 shadow-2xl" role="alertdialog" aria-modal="true" aria-labelledby="confirm-title" aria-describedby="confirm-message" @click.stop>
            <h2 id="confirm-title" class="font-display text-xl font-semibold text-ink" x-text="confirmTitle"></h2>
            <p id="confirm-message" class="mt-3 text-sm leading-6 text-muted" x-text="confirmMessage"></p>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" x-ref="confirmCancel" @click="confirmOpen = false" class="rounded-md border border-hairline px-4 py-2.5 text-sm font-semibold text-ink hover:bg-base focus:outline-none focus:ring-2 focus:ring-teal">Cancelar</button>
                <button type="button" @click="confirmOpen = false; if (confirmForm) { confirmForm.dataset.confirmed = 'true'; confirmForm.requestSubmit(); }" class="rounded-md bg-alert px-4 py-2.5 text-sm font-semibold text-white hover:opacity-90 focus:outline-none focus:ring-2 focus:ring-alert" x-text="confirmLabel"></button>
            </div>
        </div>
    </div>
</body>

</html>
