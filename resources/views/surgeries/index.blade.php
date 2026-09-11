<x-layouts.app title="Cirugías" max-width="max-w-7xl">
    <x-slot:actions>
        @can('create', App\Models\Surgery::class)
            <x-ui.button href="{{ route('surgeries.create') }}" variant="primary">
                Programar cirugía
            </x-ui.button>
        @endcan
        <x-ui.button href="{{ route('surgeries.calendar') }}" variant="secondary">
            Ver agenda
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Gestión quirúrgica" title="Cirugías"
            description="Consulta y administra las cirugías programadas y realizadas en la clínica." />

        @if (session('success'))
            <div class="rounded-md border border-success/20 bg-success/5 px-4 py-3 text-sm text-success">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3 text-sm text-alert">
                {{ session('error') }}
            </div>
        @endif

        <x-ui.card padding="0">

            <div class="border-b border-hairline px-6 py-5">

                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                    <div>
                        <h2 class="font-display text-xl font-semibold text-ink">
                            Registro de cirugías
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            {{ $surgeries->total() }} registros encontrados.
                        </p>
                    </div>

                    <form method="GET" action="{{ route('surgeries.index') }}" class="w-full space-y-5">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
                            <div class="flex-1">
                                <label for="surgeries-search" class="block text-sm font-medium text-ink">Buscar</label>
                                <input id="surgeries-search" type="search" name="search" value="{{ request('search') }}"
                                    placeholder="Nombre de mascota, dueño, tipo de cirugía..."
                                    class="mt-1.5 w-full rounded-md border border-hairline bg-surface px-4 py-2.5 text-sm text-ink outline-none focus:border-teal focus:ring-2 focus:ring-teal/10">
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="rounded-md bg-teal px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-teal-dark focus:outline-none focus:ring-2 focus:ring-teal focus:ring-offset-2">Buscar cirugías</button>
                                @if (request()->hasAny(['search', 'veterinarian_id', 'operating_room_id', 'state', 'from', 'to']))
                                    <a href="{{ route('surgeries.index') }}" class="rounded-md border border-hairline bg-surface px-4 py-2.5 text-sm font-medium text-muted hover:bg-base hover:text-ink focus:outline-none focus:ring-2 focus:ring-teal">Limpiar filtros</a>
                                @endif
                            </div>
                        </div>

                        <div class="grid gap-3 sm:grid-cols-3">
                            <div>
                                <label for="surgeries-veterinarian-filter" class="block text-sm font-medium text-ink">Veterinario</label>
                                <select id="surgeries-veterinarian-filter" name="veterinarian_id" class="mt-1.5 w-full rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                                    <option value="">Todos</option>
                                    @foreach ($veterinarianOptions as $value => $label)
                                        <option value="{{ $value }}" @selected((string) request('veterinarian_id') === (string) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="surgeries-room-filter" class="block text-sm font-medium text-ink">Quirófano</label>
                                <select id="surgeries-room-filter" name="operating_room_id" class="mt-1.5 w-full rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                                    <option value="">Todos</option>
                                    @foreach ($operatingRoomOptions as $value => $label)
                                        <option value="{{ $value }}" @selected((string) request('operating_room_id') === (string) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="surgeries-state-filter" class="block text-sm font-medium text-ink">Estado</label>
                                <select id="surgeries-state-filter" name="state" class="mt-1.5 w-full rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                                    <option value="">Todos</option>
                                    @foreach ($stateOptions as $value => $label)
                                        <option value="{{ $value }}" @selected(request('state') === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <fieldset class="rounded-md border border-hairline bg-base/40 p-4">
                            <legend class="px-1 text-sm font-semibold text-ink">Período</legend>
                            <p id="surgeries-date-filter-help" class="mb-3 text-xs leading-5 text-muted">Filtra las cirugías por rango de fecha.</p>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <div>
                                    <label for="surgeries-from-filter" class="block text-sm font-medium text-ink">Desde</label>
                                    <input id="surgeries-from-filter" type="date" name="from" value="{{ request('from') }}" aria-describedby="surgeries-date-filter-help" class="mt-1.5 w-full rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                                </div>
                                <div>
                                    <label for="surgeries-to-filter" class="block text-sm font-medium text-ink">Hasta</label>
                                    <input id="surgeries-to-filter" type="date" name="to" value="{{ request('to') }}" aria-describedby="surgeries-date-filter-help" class="mt-1.5 w-full rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                                </div>
                            </div>
                        </fieldset>
                    </form>

                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full min-w-[1050px] text-left">

                    <thead>

                        <tr class="border-b border-hairline bg-base/60">

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Fecha / hora
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Mascota
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Cirujano
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Tipo
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Quirófano
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Estado
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Motivo / observación
                            </th>

                            <th class="px-6 py-3 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                Acciones
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-hairline">

                        @forelse ($surgeries as $surgery)
                            <tr class="hover:bg-base/40">

                                <td class="px-6 py-4">

                                    <div class="font-mono text-sm font-medium text-ink">
                                        {{ $surgery->scheduled_date ?? '—' }}
                                    </div>

                                    <div class="mt-1 font-mono text-xs text-muted">
                                        {{ $surgery->start_time ?? '—' }}
                                        —
                                        {{ $surgery->end_time ?? '—' }}
                                    </div>

                                </td>

                                <td class="px-6 py-4">

                                    @if ($surgery->pet)
                                        <a href="{{ route('pets.show', $surgery->pet) }}"
                                            class="font-semibold text-ink hover:text-teal">
                                            {{ $surgery->pet->name }}
                                        </a>
                                        <p class="mt-1 text-xs text-muted">{{ $surgery->pet->owner?->first_name }} {{ $surgery->pet->owner?->last_name }}</p>
                                    @else
                                        <span class="text-sm text-muted">—</span>
                                    @endif

                                </td>

                                <td class="px-6 py-4">

                                    @if ($surgery->veterinarian)
                                        <a href="{{ route('veterinarians.show', $surgery->veterinarian) }}"
                                            class="text-sm font-medium text-ink hover:text-teal">
                                            {{ $surgery->veterinarian->first_name }}
                                            {{ $surgery->veterinarian->last_name }}
                                        </a>
                                    @else
                                        <span class="text-sm text-muted">—</span>
                                    @endif

                                </td>

                                <td class="px-6 py-4">

                                    <span class="text-sm text-ink">
                                        {{ $surgery->surgeryType?->name ?? '—' }}
                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <span class="text-sm text-ink">
                                        {{ $surgery->operatingRoom?->name ?? '—' }}
                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <x-ui.badge :status="$surgery->state" />

                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    @if (in_array($surgery->state, ['cancelled', 'no_show']))
                                        {{ $surgery->notes ?: 'Motivo no registrado' }}
                                    @else
                                        <span aria-hidden="true">—</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-end gap-4">

                                        <a href="{{ route('surgeries.show', $surgery) }}"
                                            class="text-sm font-semibold text-teal hover:text-teal-dark">
                                            Ver
                                        </a>
                                        @can('update', $surgery)
                                            @if ($surgery->state === 'scheduled')
                                                <a href="{{ route('surgeries.edit', $surgery) }}"
                                                    class="text-sm font-semibold text-ink hover:text-teal">
                                                    Editar
                                                </a>
                                            @endif
                                        @endcan
                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-12 text-center">

                                    <p class="text-sm font-medium text-ink">
                                        No hay cirugías registradas.
                                    </p>

                                    <p class="mt-1 text-sm text-muted">
                                        Programa una cirugía para comenzar a gestionar la agenda quirúrgica.
                                    </p>

                                    <div class="mt-5">
                                        @can('create', App\Models\Surgery::class)
                                            <x-ui.button href="{{ route('surgeries.create') }}" variant="primary">
                                                Programar cirugía
                                            </x-ui.button>
                                        @endcan

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($surgeries->hasPages())
                <div class="border-t border-hairline px-6 py-4">
                    {{ $surgeries->links() }}
                </div>
            @endif

        </x-ui.card>

    </div>
</x-layouts.app>
