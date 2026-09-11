<x-layouts.app
    title="Editar quirófano"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('operating-rooms.show', $operatingRoom) }}"
            variant="secondary"
        >
            Ver quirófano
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Gestión de registro"
            title="Editar quirófano"
            description="Actualiza la información y el estado operativo del quirófano."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                <p class="text-sm font-semibold text-alert">
                    No se pudo actualizar el quirófano.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>
            </div>
        @endif

        <x-ui.card>

            <form
                method="POST"
                action="{{ route('operating-rooms.update', $operatingRoom) }}"
                class="space-y-8"
            >
                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4">
                    <span class="font-mono text-xs text-muted">
                        Quirófano #{{ $operatingRoom->id }}
                    </span>

                    <x-ui.badge
                        :status="$operatingRoom->state"
                    />
                </div>

                @include('operating-rooms.partials.form', ['hideStateField' => true])

                <div class="flex flex-col gap-4 border-t border-hairline pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <button
                        type="submit"
                        form="operating-room-state-form"
                        class="text-left text-sm font-semibold {{ $operatingRoom->state === 'inactive' ? 'text-teal hover:text-teal-dark' : 'text-alert hover:opacity-80' }}"
                    >
                        {{ $operatingRoom->state === 'inactive' ? 'Habilitar quirófano' : 'Deshabilitar quirófano' }}
                    </button>

                    <div class="flex gap-3">
                        <x-ui.button href="{{ route('operating-rooms.show', $operatingRoom) }}" variant="secondary">Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                    </div>

                </div>
            </form>

            <form
                id="operating-room-state-form"
                method="POST"
                action="{{ $operatingRoom->state === 'inactive' ? route('operating-rooms.activate', $operatingRoom) : route('operating-rooms.deactivate', $operatingRoom) }}"
                data-confirm-title="{{ $operatingRoom->state === 'inactive' ? 'Habilitar quirófano' : 'Deshabilitar quirófano' }}"
                data-confirm-message="{{ $operatingRoom->state === 'inactive' ? 'El quirófano volverá a estar disponible para nuevas operaciones.' : 'El quirófano dejará de estar disponible para nuevas operaciones, pero conservará su historial.' }} ¿Deseas continuar?"
                data-confirm-label="{{ $operatingRoom->state === 'inactive' ? 'Habilitar' : 'Deshabilitar' }}"
            >
                @csrf
                @method('PATCH')
            </form>

        </x-ui.card>

    </div>
</x-layouts.app>
