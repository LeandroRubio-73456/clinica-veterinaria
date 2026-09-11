<x-layouts.app
    title="Dueños"
    max-width="max-w-7xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('owners.create') }}"
            variant="primary"
        >
            Registrar dueño
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Gestión de propietarios"
            title="Dueños"
            description="Registra y consulta los propietarios asociados a las mascotas de la clínica."
        />

        @if (session('success'))
            <div class="rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark">
                {{ session('success') }}
            </div>
        @endif

        <x-ui.card padding="0">
            <div class="border-b border-hairline px-6 py-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="font-display text-xl font-semibold text-ink">
                            Registro de dueños
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            {{ $owners->total() }} registros encontrados.
                        </p>
                    </div>

                    <form
                        method="GET"
                        action="{{ route('owners.index') }}"
                        class="flex w-full sm:w-auto"
                    >
                        <input
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Buscar por nombre, cédula o correo..."
                            class="w-full rounded-l-md border border-hairline bg-surface px-4 py-2.5 text-sm text-ink outline-none focus:border-teal focus:ring-2 focus:ring-teal/10 sm:w-72"
                        >

                        <button
                            type="submit"
                            class="rounded-r-md border border-l-0 border-hairline bg-base px-4 py-2.5 text-sm font-semibold text-ink hover:bg-teal-light"
                        >
                            Buscar
                        </button>
                        @if (request()->has('search'))
                            <a href="{{ route('owners.index') }}" class="ml-2 rounded-md border border-hairline bg-surface px-3 py-2.5 text-sm font-medium text-muted hover:bg-base hover:text-ink">Limpiar filtros</a>
                        @endif
                    </form>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[950px] text-left">
                    <thead>
                        <tr class="border-b border-hairline bg-base/60">
                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Nombre
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Cédula
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Teléfono
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Correo
                            </th>

                            <th class="px-6 py-3 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Mascotas
                            </th>

                            <th class="px-6 py-3 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                Acciones
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-hairline">
                        @forelse ($owners as $owner)
                            <tr class="hover:bg-base/40">
                                <td class="px-6 py-4">
                                    <a
                                        href="{{ route('owners.show', $owner) }}"
                                        class="font-semibold text-ink hover:text-teal"
                                    >
                                        {{ $owner->first_name }} {{ $owner->last_name }}
                                    </a>

                                    <p class="mt-1 font-mono text-xs text-muted">
                                        #{{ $owner->id }}
                                    </p>
                                </td>

                                <td class="px-6 py-4 font-mono text-sm text-ink">
                                    {{ $owner->cedula ?: 'No registrada' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    {{ $owner->phone ?: 'No registrado' }}
                                </td>

                                <td class="px-6 py-4 text-sm text-muted">
                                    {{ $owner->email ?: 'No registrado' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="font-mono text-sm font-medium text-ink">
                                        {{ $owner->pets_count ?? $owner->pets->count() }}
                                    </span>
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-4">
                                        <a
                                            href="{{ route('owners.show', $owner) }}"
                                            class="text-sm font-semibold text-teal hover:text-teal-dark"
                                        >
                                            Ver
                                        </a>

                                        <a
                                            href="{{ route('owners.edit', $owner) }}"
                                            class="text-sm font-semibold text-ink hover:text-teal"
                                        >
                                            Editar
                                        </a>

                                        <form
                                            method="POST"
                                            action="{{ route('owners.destroy', $owner) }}"
                                            data-confirm-title="Eliminar dueño" data-confirm-message="Esta acción eliminará el registro de forma permanente. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="text-sm font-semibold text-alert hover:opacity-80"
                                            >
                                                Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td
                                    colspan="6"
                                    class="px-6 py-12 text-center"
                                >
                                    <p class="text-sm font-medium text-ink">
                                        No hay dueños registrados.
                                    </p>

                                    <p class="mt-1 text-sm text-muted">
                                        Registra el primer dueño para comenzar a gestionar mascotas y cirugías.
                                    </p>

                                    <div class="mt-5">
                                        <x-ui.button
                                            href="{{ route('owners.create') }}"
                                            variant="primary"
                                        >
                                            Registrar dueño
                                        </x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($owners->hasPages())
                <div class="border-t border-hairline px-6 py-4">
                    {{ $owners->links() }}
                </div>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
