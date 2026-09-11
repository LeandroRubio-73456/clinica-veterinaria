<x-layouts.app
    title="Editar tipo de cirugía"
    max-width="max-w-5xl"
>
    <x-slot:actions>

        <div class="flex gap-3">

            <x-ui.button
                href="{{ route('surgery-types.show', $surgeryType) }}"
                variant="secondary"
            >
                Ver tipo de cirugía
            </x-ui.button>

        </div>

    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Gestión de catálogo"
            title="Editar tipo de cirugía"
            description="Actualiza la información y duración estimada del procedimiento."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">

                <p class="text-sm font-semibold text-alert">
                    No se pudo actualizar el tipo de cirugía.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>

            </div>
        @endif

        <x-ui.card>

            <form
                method="POST"
                action="{{ route('surgery-types.update', $surgeryType) }}"
                class="space-y-8"
            >

                @csrf
                @method('PUT')

                <div class="flex items-center justify-between gap-4">

                    <span class="font-mono text-xs text-muted">
                        Tipo de cirugía #{{ $surgeryType->id }}
                    </span>

                    <span class="font-mono text-xs text-muted">
                        {{ $surgeryType->surgeries_count ?? $surgeryType->surgeries->count() }}
                        cirugías asociadas
                    </span>

                </div>

                @include('surgery-types.partials.form', ['hideStateField' => true])

                <div class="flex flex-col gap-4 border-t border-hairline pt-6 sm:flex-row sm:items-center sm:justify-between">

                    <button type="submit" form="surgery-type-state-form"
                        class="text-left text-sm font-semibold {{ $surgeryType->state === 'inactive' ? 'text-teal hover:text-teal-dark' : 'text-alert hover:opacity-80' }}">
                        {{ $surgeryType->state === 'inactive' ? 'Habilitar tipo de cirugía' : 'Deshabilitar tipo de cirugía' }}
                    </button>

                    <div class="flex gap-3">
                        <x-ui.button href="{{ route('surgery-types.show', $surgeryType) }}" variant="secondary">Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                    </div>

                </div>

            </form>

            <form id="surgery-type-state-form" method="POST"
                action="{{ $surgeryType->state === 'inactive' ? route('surgery-types.activate', $surgeryType) : route('surgery-types.deactivate', $surgeryType) }}">
                @csrf
                @method('PATCH')
            </form>

        </x-ui.card>

    </div>
</x-layouts.app>
