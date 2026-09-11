<x-layouts.app title="Quirófanos" max-width="max-w-7xl">
    <x-slot:actions>
        <x-ui.button href="{{ route('operating-rooms.create') }}" variant="primary">
            Registrar quirófano
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Infraestructura quirúrgica" title="Quirófanos"
            description="Administra los quirófanos disponibles para la programación de cirugías." />

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
                            Registro de quirófanos
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            {{ $operatingRooms->total() }} registros encontrados.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('operating-rooms.index') }}" class="flex flex-wrap items-end gap-2 sm:w-auto">
                        <input type="search" name="search" value="{{ request('search') }}"
                            placeholder="Buscar quirófano..."
                            class="w-full rounded-md border border-hairline bg-surface px-4 py-2.5 text-sm text-ink outline-none focus:border-teal focus:ring-2 focus:ring-teal/10 sm:w-56">

                        <label for="rooms-type-filter" class="sr-only">Tipo</label>
                        <select id="rooms-type-filter" name="type" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                            <option value="">Todos los tipos</option>
                            @foreach ($typeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('type') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <label for="rooms-state-filter" class="sr-only">Estado</label>
                        <select id="rooms-state-filter" name="state" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                            <option value="">Todos los estados</option>
                            @foreach ($stateOptions as $value => $label)
                                <option value="{{ $value }}" @selected(request('state') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit"
                            class="rounded-md border border-hairline bg-base px-4 py-2.5 text-sm font-semibold text-ink hover:bg-teal-light">
                            Filtrar
                        </button>
                        @if (request()->hasAny(['search', 'type', 'state']))
                            <a href="{{ route('operating-rooms.index') }}" class="rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm font-medium text-muted hover:bg-base hover:text-ink">Limpiar filtros</a>
                        @endif
                    </form>

                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[850px] text-left">

                    <thead>
                        <tr class="border-b border-hairline bg-base/60">

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Quirófano
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Tipo
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Cirugías
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

                        @forelse ($operatingRooms as $operatingRoom)
                            <tr class="hover:bg-base/40">

                                <td class="px-6 py-4">
                                    <a href="{{ route('operating-rooms.show', $operatingRoom) }}"
                                        class="font-semibold text-ink hover:text-teal">
                                        {{ $operatingRoom->name }}
                                    </a>

                                    <p class="mt-1 font-mono text-xs text-muted">
                                        #{{ $operatingRoom->id }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    {{ $operatingRoom->type ?: 'No registrado' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm font-medium text-ink">
                                        {{ $operatingRoom->surgeries_count ?? $operatingRoom->surgeries->count() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <x-ui.badge :status="$operatingRoom->state" />
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4">

                                        <a href="{{ route('operating-rooms.show', $operatingRoom) }}"
                                            class="text-sm font-semibold text-teal hover:text-teal-dark">
                                            Ver
                                        </a>

                                        <a href="{{ route('operating-rooms.edit', $operatingRoom) }}"
                                            class="text-sm font-semibold text-ink hover:text-teal">
                                            Editar
                                        </a>

                                        @if ($operatingRoom->state === 'inactive')
                                            <form method="POST" action="{{ route('operating-rooms.activate', $operatingRoom) }}" class="inline" data-confirm-title="Habilitar quirófano" data-confirm-message="El quirófano volverá a estar disponible para nuevas operaciones. ¿Deseas continuar?" data-confirm-label="Habilitar">
                                                @csrf
                                                @method('PATCH')

                                                <x-ui.button type="submit" variant="secondary">
                                                    Habilitar
                                                </x-ui.button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('operating-rooms.deactivate', $operatingRoom) }}" class="inline" data-confirm-title="Deshabilitar quirófano" data-confirm-message="El quirófano dejará de estar disponible para nuevas operaciones, pero conservará su historial. ¿Deseas continuar?" data-confirm-label="Deshabilitar">
                                                @csrf
                                                @method('PATCH')

                                                <x-ui.button type="submit" variant="warning">
                                                    Deshabilitar
                                                </x-ui.button>
                                            </form>
                                        @endif

                                        @if ($operatingRoom->surgeries_count === 0)
                                            <form method="POST" action="{{ route('operating-rooms.destroy', $operatingRoom) }}" class="inline"
                                                data-confirm-title="Eliminar quirófano" data-confirm-message="Esta acción eliminará el registro de forma permanente. Solo debe usarse si no tiene cirugías asociadas. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente">
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
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-sm font-medium text-ink">
                                        No hay quirófanos registrados.
                                    </p>

                                    <p class="mt-1 text-sm text-muted">
                                        Registra el primer quirófano para comenzar a gestionar su disponibilidad.
                                    </p>

                                    <div class="mt-5">
                                        <x-ui.button href="{{ route('operating-rooms.create') }}" variant="primary">
                                            Registrar quirófano
                                        </x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            @if ($operatingRooms->hasPages())
                <div class="border-t border-hairline px-6 py-4">
                    {{ $operatingRooms->links() }}
                </div>
            @endif

        </x-ui.card>
    </div>
</x-layouts.app>
