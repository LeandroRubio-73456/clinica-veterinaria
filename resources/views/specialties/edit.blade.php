<x-layouts.app
    title="Editar especialidad"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('specialties.show', $specialty) }}"
            variant="secondary"
        >
            Ver especialidad
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Gestión de catálogo"
            title="Editar especialidad"
            description="Actualiza la información y disponibilidad de la especialidad."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                <p class="text-sm font-semibold text-alert">
                    No se pudo actualizar la especialidad.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>
            </div>
        @endif

        <x-ui.card>

            <form
                method="POST"
                action="{{ route('specialties.update', $specialty) }}"
                class="space-y-8"
            >

                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4">

                    <span class="font-mono text-xs text-muted">
                        Especialidad #{{ $specialty->id }}
                    </span>

                    <x-ui.badge :status="$specialty->state" />

                </div>

                @include('specialties.partials.form', ['hideStateField' => true])

                <div class="flex flex-col gap-4 border-t border-hairline pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <button type="submit" form="specialty-state-form"
                        class="text-left text-sm font-semibold {{ $specialty->state === 'inactive' ? 'text-teal hover:text-teal-dark' : 'text-alert hover:opacity-80' }}">
                        {{ $specialty->state === 'inactive' ? 'Habilitar especialidad' : 'Deshabilitar especialidad' }}
                    </button>

                    <div class="flex gap-3">
                        <x-ui.button href="{{ route('specialties.show', $specialty) }}" variant="secondary">Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                    </div>

                </div>

            </form>

            <form id="specialty-state-form" method="POST"
                action="{{ $specialty->state === 'inactive' ? route('specialties.activate', $specialty) : route('specialties.deactivate', $specialty) }}"
                data-confirm-title="{{ $specialty->state === 'inactive' ? 'Habilitar especialidad' : 'Deshabilitar especialidad' }}"
                data-confirm-message="{{ $specialty->state === 'inactive' ? 'La especialidad volverá a estar disponible en los formularios.' : 'La especialidad dejará de estar disponible para nuevos registros, pero conservará su historial.' }} ¿Deseas continuar?">
                @csrf
                @method('PATCH')
            </form>

        </x-ui.card>

    </div>
</x-layouts.app>
