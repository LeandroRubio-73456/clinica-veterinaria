<x-layouts.app title="Usuarios" max-width="max-w-7xl">
    <x-slot:actions>
        <x-ui.button href="{{ route('users.create') }}" variant="primary">
            Crear usuario
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">
        <x-ui.section-header
            eyebrow="Administración del sistema"
            title="Usuarios"
            description="Gestiona las cuentas del personal y asigna el nivel de acceso correspondiente."
        />

        @if (session('success'))
            <div class="rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark" role="status">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3 text-sm text-alert" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <x-ui.card padding="0">
            <div class="border-b border-hairline px-6 py-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-ink">Personal registrado</h2>
                        <p class="mt-1 text-sm text-muted">{{ $users->total() }} usuarios encontrados.</p>
                    </div>

                    <form method="GET" action="{{ route('users.index') }}" class="flex flex-wrap items-end gap-2">
                        <label for="users-search-filter" class="sr-only">Buscar usuario</label>
                        <input id="users-search-filter" type="search" name="search" value="{{ request('search') }}" placeholder="Nombre o correo..." class="rounded-md border border-hairline bg-surface px-4 py-2.5 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                        <label for="users-role-filter" class="sr-only">Rol</label>
                        <select id="users-role-filter" name="role" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                            <option value="">Todos los roles</option>
                            @foreach ($roleOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-md border border-hairline bg-base px-4 py-2.5 text-sm font-semibold text-ink hover:bg-teal-light">Filtrar</button>
                        @if (request()->hasAny(['search', 'role']))
                            <a href="{{ route('users.index') }}" class="rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm font-medium text-muted hover:bg-base hover:text-ink">Limpiar filtros</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left">
                    <caption class="sr-only">Usuarios registrados en el sistema</caption>
                    <thead>
                        <tr class="border-b border-hairline bg-base/60">
                            <th scope="col" class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">Usuario</th>
                            <th scope="col" class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">Correo</th>
                            <th scope="col" class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">Rol</th>
                            <th scope="col" class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">Perfil profesional</th>
                            <th scope="col" class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">Registro</th>
                            <th scope="col" class="px-6 py-3 text-right font-mono text-[11px] uppercase tracking-widest text-muted">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-hairline">
                        @forelse ($users as $user)
                            <tr class="hover:bg-base/40">
                                <td class="px-6 py-4">
                                    <a href="{{ route('users.show', $user) }}" class="font-semibold text-ink hover:text-teal">{{ $user->name }}</a>
                                    <p class="mt-1 font-mono text-xs text-muted">#{{ $user->id }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-muted">{{ $user->email }}</td>
                                <td class="px-6 py-4"><span class="text-sm font-medium text-ink">{{ $user->roleProfile?->name ?? ucfirst($user->role ?? 'Sin rol') }}</span></td>
                                <td class="px-6 py-4 text-sm text-muted">
                                    @if ($user->isVeterinarian())
                                        @if ($user->veterinarian)
                                            {{ $user->veterinarian->first_name }} {{ $user->veterinarian->last_name }}
                                        @else
                                            <span class="font-medium text-alert">Sin asociar</span>
                                        @endif
                                    @else
                                        <span aria-hidden="true">—</span>
                                        <span class="sr-only">No aplica</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-mono text-xs text-muted">{{ $user->created_at?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4">
                                        <a href="{{ route('users.show', $user) }}" class="text-sm font-semibold text-teal hover:text-teal-dark">Ver</a>
                                        <a href="{{ route('users.edit', $user) }}" class="text-sm font-semibold text-ink hover:text-teal">Editar</a>
                                        @if (auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('users.destroy', $user) }}" data-confirm-title="Eliminar usuario" data-confirm-message="Esta acción eliminará la cuenta de acceso de forma permanente. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-sm font-semibold text-alert hover:opacity-80">Eliminar</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <p class="text-sm font-medium text-ink">No hay usuarios registrados.</p>
                                    <p class="mt-1 text-sm text-muted">Crea la primera cuenta del personal autorizado.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-hairline px-6 py-4">{{ $users->links() }}</div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
