<x-layouts.app
    title="Especialidades"
    max-width="max-w-7xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('specialties.create') }}"
            variant="primary"
        >
            Registrar especialidad
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Catálogo veterinario"
            title="Especialidades"
            description="Administra las especialidades veterinarias disponibles para el personal médico."
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
                            Catálogo de especialidades
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            {{ $specialties->total() }} registros encontrados.
                        </p>
                    </div>

                    <form
                        method="GET"
                        action="{{ route('specialties.index') }}"
                        class="flex flex-wrap items-end gap-2 sm:w-auto"
                    >
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar especialidad..."
                              class="w-full rounded-md border border-hairline bg-surface px-4 py-2.5 text-sm text-ink outline-none placeholder:text-muted focus:border-teal focus:ring-2 focus:ring-teal/10 sm:w-64"
                          >

                          <label for="specialties-state-filter" class="sr-only">Estado</label>
                          <select id="specialties-state-filter" name="state" class="rounded-md border border-hairline bg-surface px-3 py-2.5 pe-8 text-sm text-ink focus:border-teal focus:ring-2 focus:ring-teal/10">
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
                            <a href="{{ route('specialties.index') }}" class="rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm font-medium text-muted hover:bg-base hover:text-ink">Limpiar filtros</a>
                        @endif
                    </form>

                </div>

            </div>

            <div class="overflow-x-auto">

                <table class="w-full min-w-[750px] text-left">

                    <thead>
                        <tr class="border-b border-hairline bg-base/60">

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Especialidad
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Veterinarios
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

                        @forelse ($specialties as $specialty)

                            <tr class="hover:bg-base/40">

                                <td class="px-6 py-4">

                                    <a
                                        href="{{ route('specialties.show', $specialty) }}"
                                        class="font-semibold text-ink hover:text-teal"
                                    >
                                        {{ $specialty->name }}
                                    </a>

                                    <p class="mt-1 max-w-md truncate text-sm text-muted">
                                        {{ $specialty->description ?: 'Sin descripción' }}
                                    </p>

                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm font-medium text-ink">
                                        {{ $specialty->veterinarians_count ?? $specialty->veterinarians->count() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <x-ui.badge :status="$specialty->state" />
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs text-muted">
                                        {{ $specialty->created_at?->format('d/m/Y') ?? '—' }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-end gap-4">

                                        <a
                                            href="{{ route('specialties.show', $specialty) }}"
                                            class="text-sm font-semibold text-teal hover:text-teal-dark"
                                        >
                                            Ver
                                        </a>

                                        <a
                                            href="{{ route('specialties.edit', $specialty) }}"
                                            class="text-sm font-semibold text-ink hover:text-teal"
                                        >
                                            Editar
                                        </a>

                                        @if ($specialty->state === 'inactive')
                                            <form method="POST" action="{{ route('specialties.activate', $specialty) }}" class="inline" data-confirm-title="Habilitar especialidad" data-confirm-message="La especialidad volverá a estar disponible en los formularios. ¿Deseas continuar?" data-confirm-label="Habilitar">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button type="submit" variant="secondary">Habilitar</x-ui.button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('specialties.deactivate', $specialty) }}" class="inline" data-confirm-title="Deshabilitar especialidad" data-confirm-message="La especialidad dejará de estar disponible para nuevos registros, pero conservará su historial. ¿Deseas continuar?" data-confirm-label="Deshabilitar">
                                                @csrf
                                                @method('PATCH')
                                                <x-ui.button type="submit" variant="warning">Deshabilitar</x-ui.button>
                                            </form>
                                        @endif

                                        @if ($specialty->veterinarians_count === 0)
                                            <form method="POST" action="{{ route('specialties.destroy', $specialty) }}" class="inline"
                                                data-confirm-title="Eliminar especialidad" data-confirm-message="Esta acción eliminará el catálogo de forma permanente. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente">
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
                                        No hay especialidades registradas.
                                    </p>

                                    <p class="mt-1 text-sm text-muted">
                                        Registra una especialidad para asignarla posteriormente a los veterinarios.
                                    </p>

                                    <div class="mt-5">
                                        <x-ui.button
                                            href="{{ route('specialties.create') }}"
                                            variant="primary"
                                        >
                                            Registrar especialidad
                                        </x-ui.button>
                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            @if ($specialties->hasPages())
                <div class="border-t border-hairline px-6 py-4">
                    {{ $specialties->links() }}
                </div>
            @endif

        </x-ui.card>

    </div>
</x-layouts.app>
