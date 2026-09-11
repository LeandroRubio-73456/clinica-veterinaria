<x-layouts.app
    title="Especies"
    max-width="max-w-7xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('species.create') }}"
            variant="primary"
        >
            Registrar especie
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Catálogo de mascotas"
            title="Especies"
            description="Administra las especies disponibles para el registro de mascotas."
        />

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
                            Catálogo de especies
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            {{ $species->total() }} registros encontrados.
                        </p>
                    </div>

                    <form
                        method="GET"
                        action="{{ route('species.index') }}"
                        class="flex flex-wrap items-end gap-2 sm:w-auto"
                    >
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar especie..."
                              class="w-full rounded-md border border-hairline bg-surface px-4 py-2.5 text-sm text-ink outline-none placeholder:text-muted focus:border-teal focus:ring-2 focus:ring-teal/10 sm:w-64"
                          >

                          <label for="species-state-filter" class="sr-only">Estado</label>
                          <select id="species-state-filter" name="state" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
                              <option value="">Todos los estados</option>
                              @foreach ($stateOptions as $value => $label)
                                  <option value="{{ $value }}" @selected(request('state') === $value)>{{ $label }}</option>
                              @endforeach
                          </select>

                        <button
                            type="submit"
                              class="rounded-md border border-hairline bg-base px-4 py-2.5 text-sm font-semibold text-ink hover:bg-teal-light"
                        >
                              Filtrar
                        </button>
                        @if (request()->hasAny(['search', 'state']))
                            <a href="{{ route('species.index') }}" class="rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm font-medium text-muted hover:bg-base hover:text-ink">Limpiar filtros</a>
                        @endif
                    </form>

                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full min-w-[750px] text-left">

                    <thead>
                        <tr class="border-b border-hairline bg-base/60">

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Especie
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Mascotas
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Estado
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Registro
                            </th>

                            <th class="px-6 py-3 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                Acciones
                            </th>

                        </tr>
                    </thead>

                    <tbody class="divide-y divide-hairline">

                        @forelse ($species as $specie)

                            <tr class="hover:bg-base/40">

                                <td class="px-6 py-4">

                                    <a
                                        href="{{ route('species.show', $specie) }}"
                                        class="font-semibold text-ink hover:text-teal"
                                    >
                                        {{ $specie->name }}
                                    </a>

                                    <p class="mt-1 max-w-md truncate text-sm text-muted">
                                        {{ $specie->description ?: 'Sin descripción' }}
                                    </p>

                                </td>

                                <td class="px-6 py-4">

                                    <span class="font-mono text-sm font-medium text-ink">
                                        {{ $specie->pets_count ?? $specie->pets->count() }}
                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <x-ui.badge
                                        :status="$specie->state"
                                    />

                                </td>

                                <td class="px-6 py-4">

                                    <span class="font-mono text-xs text-muted">
                                        {{ $specie->created_at?->format('d/m/Y') ?? '—' }}
                                    </span>

                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-end gap-4">

                                        <a
                                            href="{{ route('species.show', $specie) }}"
                                            class="text-sm font-semibold text-teal hover:text-teal-dark"
                                        >
                                            Ver
                                        </a>

                                        <a
                                            href="{{ route('species.edit', $specie) }}"
                                            class="text-sm font-semibold text-ink hover:text-teal"
                                        >
                                            Editar
                                        </a>

                                        @if ($specie->state === 'inactive')
                                            <form method="POST" action="{{ route('species.activate', $specie) }}" class="inline" data-confirm-title="Habilitar especie" data-confirm-message="La especie volverá a estar disponible en los formularios. ¿Deseas continuar?" data-confirm-label="Habilitar">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button type="submit" variant="secondary">Habilitar</x-ui.button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('species.deactivate', $specie) }}" class="inline" data-confirm-title="Deshabilitar especie" data-confirm-message="La especie dejará de estar disponible para nuevos registros, pero conservará su historial. ¿Deseas continuar?" data-confirm-label="Deshabilitar">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button type="submit" variant="warning">Deshabilitar</x-ui.button>
                                            </form>
                                        @endif

                                        @if ($specie->pets_count === 0)
                                            <form method="POST" action="{{ route('species.destroy', $specie) }}" class="inline"
                                                data-confirm-title="Eliminar especie" data-confirm-message="Esta acción eliminará el catálogo de forma permanente. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente">
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
                                <td
                                    colspan="5"
                                    class="px-6 py-12 text-center"
                                >

                                    <p class="text-sm font-medium text-ink">
                                        No hay especies registradas.
                                    </p>

                                    <p class="mt-1 text-sm text-muted">
                                        Registra una especie para utilizarla en el registro de mascotas.
                                    </p>

                                    <div class="mt-5">

                                        <x-ui.button
                                            href="{{ route('species.create') }}"
                                            variant="primary"
                                        >
                                            Registrar especie
                                        </x-ui.button>

                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($species->hasPages())
                <div class="border-t border-hairline px-6 py-4">
                    {{ $species->links() }}
                </div>
            @endif

        </x-ui.card>

    </div>
</x-layouts.app>
