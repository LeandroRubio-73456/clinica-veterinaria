<x-layouts.app
    title="Editar veterinario"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('veterinarians.show', $veterinarian) }}"
            variant="secondary"
        >
            Ver veterinario
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Gestión de registro"
            title="Editar veterinario"
            description="Actualiza los datos profesionales y el estado del veterinario."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                <p class="text-sm font-semibold text-alert">
                    No se pudo actualizar el veterinario.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>
            </div>
        @endif

        <x-ui.card>

            <form
                method="POST"
                action="{{ route('veterinarians.update', $veterinarian) }}"
                class="space-y-8"
            >

                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4">
                    <span class="font-mono text-xs text-muted">
                        Veterinario #{{ $veterinarian->id }}
                    </span>

                    <x-ui.badge
                        :status="$veterinarian->state"
                    />
                </div>

                @include('veterinarians.partials.form', ['hideStateField' => true])

                <div class="flex flex-col gap-4 border-t border-hairline pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <button
                        type="submit"
                        form="veterinarian-state-form"
                        class="text-left text-sm font-semibold {{ $veterinarian->state === 'inactive' ? 'text-teal hover:text-teal-dark' : 'text-alert hover:opacity-80' }}"
                    >
                        {{ $veterinarian->state === 'inactive' ? 'Habilitar veterinario' : 'Deshabilitar veterinario' }}
                    </button>

                    <div class="flex gap-3">

                        <x-ui.button
                            href="{{ route('veterinarians.show', $veterinarian) }}"
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
                id="veterinarian-state-form"
                method="POST"
                action="{{ $veterinarian->state === 'inactive' ? route('veterinarians.activate', $veterinarian) : route('veterinarians.deactivate', $veterinarian) }}"
                data-confirm-title="{{ $veterinarian->state === 'inactive' ? 'Habilitar veterinario' : 'Deshabilitar veterinario' }}"
                data-confirm-message="{{ $veterinarian->state === 'inactive' ? 'El perfil volverá a estar disponible para nuevas asignaciones.' : 'El perfil dejará de estar disponible para nuevas asignaciones, pero conservará su historial.' }} ¿Deseas continuar?"
                data-confirm-label="{{ $veterinarian->state === 'inactive' ? 'Habilitar' : 'Deshabilitar' }}"
            >
                @csrf
                @method('PATCH')
            </form>

        </x-ui.card>

    </div>
</x-layouts.app>
