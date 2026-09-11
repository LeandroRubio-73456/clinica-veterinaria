<x-layouts.app
    title="Editar mascota"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <div class="flex gap-3">
            <x-ui.button
                href="{{ route('pets.show', $pet) }}"
                variant="secondary"
            >
                Ver mascota
            </x-ui.button>
        </div>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Gestión de registro"
            title="Editar mascota"
            description="Actualiza los datos básicos y la información del propietario."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                <p class="text-sm font-semibold text-alert">
                    No se pudo actualizar la mascota.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>
            </div>
        @endif

        <x-ui.card>

            <form
                method="POST"
                action="{{ route('pets.update', $pet) }}"
                class="space-y-8"
            >
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4">
                    <span class="font-mono text-xs text-muted">
                        Mascota #{{ $pet->id }}
                    </span>

                    <a
                        href="{{ route('owners.show', $pet->owner) }}"
                        class="text-sm font-semibold text-teal hover:text-teal-dark"
                    >
                        Ver dueño
                    </a>
                </div>

                @include('pets.partials.form', ['hideStateField' => true])

                <div class="flex flex-col gap-4 border-t border-hairline pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <button
                        type="submit"
                        form="pet-state-form"
                        class="text-left text-sm font-semibold {{ $pet->state === 'inactive' ? 'text-teal hover:text-teal-dark' : 'text-alert hover:opacity-80' }}"
                    >
                        {{ $pet->state === 'inactive' ? 'Habilitar mascota' : 'Deshabilitar mascota' }}
                    </button>
                    <div class="flex gap-3">
                        <x-ui.button
                            href="{{ route('pets.show', $pet) }}"
                            variant="secondary"
                        >
                            Cancelar
                        </x-ui.button>

                        <x-ui.button
                            type="submit"
                            variant="primary"
                        >
                            Guardar cambios
                        </x-ui.button>
                    </div>
                </div>
            </form>
            
            <form
                id="pet-state-form"
                method="POST"
                action="{{ $pet->state === 'inactive' ? route('pets.activate', $pet) : route('pets.deactivate', $pet) }}"
                data-confirm-title="{{ $pet->state === 'inactive' ? 'Habilitar mascota' : 'Deshabilitar mascota' }}"
                data-confirm-message="{{ $pet->state === 'inactive' ? 'La mascota volverá a estar disponible para nuevas operaciones.' : 'La mascota dejará de estar disponible para nuevas operaciones, pero conservará su historial.' }} ¿Deseas continuar?"
                data-confirm-label="{{ $pet->state === 'inactive' ? 'Habilitar' : 'Deshabilitar' }}"
            >
                @csrf
                @method('PATCH')
            </form>

        </x-ui.card>
    </div>
</x-layouts.app>
