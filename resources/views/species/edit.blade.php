<x-layouts.app
    title="Editar especie"
    max-width="max-w-5xl"
>
    <x-slot:actions>

        <x-ui.button
            href="{{ route('species.show', $species) }}"
            variant="secondary"
        >
            Ver especie
        </x-ui.button>

    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Gestión de catálogo"
            title="Editar especie"
            description="Actualiza la información y disponibilidad de la especie."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">

                <p class="text-sm font-semibold text-alert">
                    No se pudo actualizar la especie.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>

            </div>
        @endif

        <x-ui.card>

            <form
                method="POST"
                action="{{ route('species.update', $species) }}"
                class="space-y-8"
            >

                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4">

                    <span class="font-mono text-xs text-muted">
                        Especie #{{ $species->id }}
                    </span>

                    <x-ui.badge
                        :status="$species->state"
                    />

                </div>

                @include('species.partials.form', ['hideStateField' => true])

                <div class="flex flex-col gap-4 border-t border-hairline pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <button type="submit" form="species-state-form"
                        class="text-left text-sm font-semibold {{ $species->state === 'inactive' ? 'text-teal hover:text-teal-dark' : 'text-alert hover:opacity-80' }}">
                        {{ $species->state === 'inactive' ? 'Habilitar especie' : 'Deshabilitar especie' }}
                    </button>

                    <div class="flex gap-3">
                        <x-ui.button href="{{ route('species.show', $species) }}" variant="secondary">Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                    </div>

                </div>

            </form>

            <form id="species-state-form" method="POST"
                action="{{ $species->state === 'inactive' ? route('species.activate', $species) : route('species.deactivate', $species) }}"
                data-confirm-title="{{ $species->state === 'inactive' ? 'Habilitar especie' : 'Deshabilitar especie' }}"
                data-confirm-message="{{ $species->state === 'inactive' ? 'La especie volverá a estar disponible en los formularios.' : 'La especie dejará de estar disponible para nuevos registros, pero conservará su historial.' }} ¿Deseas continuar?">
                @csrf
                @method('PATCH')
            </form>

        </x-ui.card>

    </div>
</x-layouts.app>
