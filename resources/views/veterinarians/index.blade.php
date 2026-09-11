<x-layouts.app title="Veterinarios" max-width="max-w-7xl">
    <x-slot:actions>
        <x-ui.button href="{{ route('veterinarians.create') }}" variant="primary">
            Registrar veterinario
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Gestión de personal" title="Veterinarios"
            description="Administra los veterinarios que participan en la programación y ejecución de cirugías." />

        @if (session('success'))
            <div class="rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-md border border-hairline bg-base px-4 py-3 text-sm text-muted" role="status">{{ session('info') }}</div>
        @endif
        @if (session('error'))
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3 text-sm text-alert" role="alert">{{ session('error') }}</div>
        @endif

        <x-ui.card padding="0">

            <div class="border-b border-hairline px-6 py-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                    <div>
                        <h2 class="font-display text-xl font-semibold text-ink">
                            Registro de veterinarios
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            {{ $veterinarians->total() }} registros encontrados.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('veterinarians.index') }}" class="flex flex-wrap items-end gap-2 sm:w-auto">
                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Buscar por nombre, cédula o correo..."
                            class="w-full rounded-md border border-hairline bg-surface px-4 py-2.5 text-sm text-ink outline-none focus:border-teal focus:ring-2 focus:ring-teal/10 sm:w-64">

                        <label for="vets-specialty-filter" class="sr-only">Especialidad</label>
                        <select id="vets-specialty-filter" name="specialty_id" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                            <option value="">Todas las especialidades</option>
                            @foreach ($specialtyOptions as $value => $label)
                                <option value="{{ $value }}" @selected((string) request('specialty_id') === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <label for="vets-state-filter" class="sr-only">Estado</label>
                        <select id="vets-state-filter" name="state" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                            <option value="">Todos los estados</option>
                            @foreach ($stateOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('state') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="rounded-md border border-hairline bg-base px-4 py-2.5 text-sm font-semibold text-ink hover:bg-teal-light">
                            Filtrar
                        </button>
                        @if (request()->hasAny(['search', 'specialty_id', 'state']))
                            <a href="{{ route('veterinarians.index') }}" class="rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm font-medium text-muted hover:bg-base hover:text-ink">Limpiar filtros</a>
                        @endif
                    </form>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[1000px] text-left">

                    <thead>
                        <tr class="border-b border-hairline bg-base/60">

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Veterinario
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Cédula
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Especialidad
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Correo
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Teléfono
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Estado
                            </th>

                            <th class="px-6 py-3 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                Acciones
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-hairline">

                        @forelse ($veterinarians as $veterinarian)
                            <tr class="hover:bg-base/40">

                                <td class="px-6 py-4">
                                    <a href="{{ route('veterinarians.show', $veterinarian) }}"
                                        class="font-semibold text-ink hover:text-teal">
                                        {{ $veterinarian->first_name }} {{ $veterinarian->last_name }}
                                    </a>

                                    <p class="mt-1 font-mono text-xs text-muted">
                                        #{{ $veterinarian->id }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 font-mono text-sm text-ink">
                                    {{ $veterinarian->cedula ?: 'No registrada' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    {{ $veterinarian->specialty?->name ?: 'No registrada' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    {{ $veterinarian->email ?: 'No registrado' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    {{ $veterinarian->phone ?: 'No registrado' }}
                                </td>

                                <td class="px-6 py-4">
                                    <x-ui.badge :status="$veterinarian->state" />
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4">

                                        <a href="{{ route('veterinarians.show', $veterinarian) }}"
                                            class="text-sm font-semibold text-teal hover:text-teal-dark">
                                            Ver
                                        </a>

                                        <a href="{{ route('veterinarians.edit', $veterinarian) }}"
                                            class="text-sm font-semibold text-ink hover:text-teal">
                                            Editar
                                        </a>

                                        @if ($veterinarian->state === 'inactive')
                                            <form method="POST" action="{{ route('veterinarians.activate', $veterinarian) }}" class="inline" data-confirm-title="Habilitar veterinario" data-confirm-message="El perfil volverá a estar disponible para nuevas asignaciones. ¿Deseas continuar?" data-confirm-label="Habilitar">
                                                @csrf
                                                @method('PATCH')

                                                <x-ui.button type="submit" variant="secondary">
                                                    Habilitar
                                                </x-ui.button>
                                            </form>
                                        @else
                                                <form method="POST" action="{{ route('veterinarians.deactivate', $veterinarian) }}" class="inline" data-confirm-title="Deshabilitar veterinario" data-confirm-message="El perfil dejará de estar disponible para nuevas asignaciones, pero conservará su historial. ¿Deseas continuar?" data-confirm-label="Deshabilitar">
                                                @csrf
                                                @method('PATCH')

                                                <x-ui.button type="submit" variant="warning">
                                                    Deshabilitar
                                                </x-ui.button>
                                            </form>
                                        @endif

                                        @if ($veterinarian->surgeries_count === 0)
                                            <form method="POST" action="{{ route('veterinarians.destroy', $veterinarian) }}" class="inline"
                                                data-confirm-title="Eliminar veterinario" data-confirm-message="Esta acción eliminará el registro de forma permanente. Solo debe usarse si no tiene cirugías asociadas. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente">
                                                @csrf
                                                @method('DELETE')
                                                <x-ui.button type="submit" variant="danger">Eliminar</x-ui.button>
                                            </form>
                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-sm font-medium text-ink">
                                        No hay veterinarios registrados.
                                    </p>

                                    <p class="mt-1 text-sm text-muted">
                                        Registra el primer veterinario para comenzar a asignarlo a cirugías.
                                    </p>

                                    <div class="mt-5">
                                        <x-ui.button href="{{ route('veterinarians.create') }}" variant="primary">
                                            Registrar veterinario
                                        </x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            @if ($veterinarians->hasPages())
                <div class="border-t border-hairline px-6 py-4">
                    {{ $veterinarians->links() }}
                </div>
            @endif

        </x-ui.card>

    </div>
</x-layouts.app>
