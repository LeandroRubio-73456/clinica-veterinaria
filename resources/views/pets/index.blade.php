<x-layouts.app title="Mascotas" max-width="max-w-7xl">
    <x-slot:actions>
        <x-ui.button href="{{ route('pets.create') }}" variant="primary">
            Registrar mascota
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Gestión de mascotas" title="Mascotas"
            description="Registra y consulta las mascotas asociadas a los dueños de la clínica." />

        @if (session('success'))
            <div class="rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark">
                {{ session('success') }}
            </div>
        @endif

        @if (session('info'))
            <div class="rounded-md border border-hairline bg-base px-4 py-3 text-sm text-muted" role="status">
                {{ session('info') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3 text-sm text-alert" role="alert">
                {{ session('error') }}
            </div>
        @endif

        <x-ui.card padding="0">
            <div class="border-b border-hairline px-6 py-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-ink">
                            Registro de mascotas
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            {{ $pets->total() }} registros encontrados.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('pets.index') }}" class="flex flex-wrap items-end gap-2 sm:w-auto">
                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Buscar mascota o dueño..."
                            class="w-full rounded-md border border-hairline bg-surface px-4 py-2.5 text-sm text-ink outline-none focus:border-teal focus:ring-2 focus:ring-teal/10 sm:w-64">

                        <label for="pets-species-filter" class="sr-only">Especie</label>
                        <select id="pets-species-filter" name="species_id" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                            <option value="">Todas las especies</option>
                            @foreach ($speciesOptions as $value => $label)
                                <option value="{{ $value }}" @selected((string) request('species_id') === (string) $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <label for="pets-state-filter" class="sr-only">Estado</label>
                        <select id="pets-state-filter" name="state" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                            <option value="">Todos los estados</option>
                            @foreach ($stateOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('state') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="rounded-md border border-hairline bg-base px-4 py-2.5 text-sm font-semibold text-ink hover:bg-teal-light">
                            Filtrar
                        </button>
                        @if (request()->hasAny(['search', 'species_id', 'state']))
                            <a href="{{ route('pets.index') }}" class="rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm font-medium text-muted hover:bg-base hover:text-ink">Limpiar filtros</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-left">
                    <thead>
                        <tr class="border-b border-hairline bg-base/60">
                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Mascota
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Especie
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Raza
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Edad
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Peso
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Dueño
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
                        @forelse ($pets as $pet)
                            <tr class="hover:bg-base/40">
                                <td class="px-6 py-4">
                                    <a href="{{ route('pets.show', $pet) }}"
                                        class="font-semibold text-ink hover:text-teal">
                                        {{ $pet->name }}
                                    </a>

                                    <p class="mt-1 font-mono text-xs text-muted">
                                        #{{ $pet->id }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    {{ $pet->species?->name ?: 'No registrada' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    {{ $pet->breed ?: 'No registrada' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm text-ink">
                                        {{ $pet->age !== null ? $pet->age . ' meses' : '—' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm text-ink">
                                        {{ $pet->weight !== null ? number_format($pet->weight, 2) . ' kg' : '—' }}
                                    </span>
                                </td>
                                
                                <td class="px-6 py-4">
                                    @if ($pet->owner)
                                        <a href="{{ route('owners.show', $pet->owner) }}"
                                            class="text-sm font-semibold text-teal hover:text-teal-dark">
                                            {{ $pet->owner->first_name }}
                                            {{ $pet->owner->last_name }}
                                        </a>
                                    @else
                                        <span class="text-sm text-muted">
                                            No disponible
                                        </span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4">
                                    <x-ui.badge
                                        :status="$pet->state"
                                    />
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4">
                                        <a href="{{ route('pets.show', $pet) }}"
                                            class="text-sm font-semibold text-teal hover:text-teal-dark">
                                            Ver
                                        </a>

                                        <a href="{{ route('pets.edit', $pet) }}"
                                            class="text-sm font-semibold text-ink hover:text-teal">
                                            Editar
                                        </a>

                                        @if ($pet->state === 'inactive')
                                            <form method="POST" action="{{ route('pets.activate', $pet) }}" class="inline" data-confirm-title="Habilitar mascota" data-confirm-message="La mascota volverá a estar disponible para nuevas operaciones. ¿Deseas continuar?" data-confirm-label="Habilitar">
                                                @csrf
                                                @method('PATCH')

                                                <x-ui.button type="submit" variant="secondary">
                                                    Habilitar
                                                </x-ui.button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('pets.deactivate', $pet) }}" class="inline" data-confirm-title="Deshabilitar mascota" data-confirm-message="La mascota dejará de estar disponible para nuevas operaciones, pero conservará su historial. ¿Deseas continuar?" data-confirm-label="Deshabilitar">
                                                @csrf
                                                @method('PATCH')

                                                <x-ui.button type="submit" variant="warning">
                                                    Deshabilitar
                                                </x-ui.button>
                                            </form>
                                        @endif

                                        @if ($pet->surgeries_count === 0)
                                            <form method="POST" action="{{ route('pets.destroy', $pet) }}" class="inline" data-confirm-title="Eliminar mascota" data-confirm-message="Esta acción eliminará el registro de forma permanente. Solo debe usarse si no tiene historial asociado. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente">
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
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <p class="text-sm font-medium text-ink">
                                        No hay mascotas registradas.
                                    </p>

                                    <p class="mt-1 text-sm text-muted">
                                        Registra la primera mascota para asociarla con su dueño.
                                    </p>

                                    <div class="mt-5">
                                        <x-ui.button href="{{ route('pets.create') }}" variant="primary">
                                            Registrar mascota
                                        </x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pets->hasPages())
                <div class="border-t border-hairline px-6 py-4">
                    {{ $pets->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
