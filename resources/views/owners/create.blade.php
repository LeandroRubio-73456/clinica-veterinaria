<x-layouts.app
    title="Registrar dueño"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('owners.index') }}"
            variant="secondary"
        >
            Volver a dueños
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">
        <x-ui.section-header
            eyebrow="Nuevo registro"
            title="Registrar dueño"
            description="Ingresa los datos del propietario de la mascota."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                <p class="text-sm font-semibold text-alert">
                    No se pudo guardar el registro.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>
            </div>
        @endif

        <x-ui.card>
            <form
                method="POST"
                action="{{ $owner->exists
                    ? route('owners.update', $owner)
                    : route('owners.store') }}"
                class="space-y-8"
            >
                @csrf

                @if ($owner->exists)
                    @method('PUT')
                @endif

                @include('owners.partials.form')

                <div class="flex flex-col-reverse gap-3 border-t border-hairline pt-6 sm:flex-row sm:justify-end">
                    <x-ui.button
                        href="{{ $owner->exists
                            ? route('owners.show', $owner)
                            : route('owners.index') }}"
                        variant="secondary"
                    >
                        Cancelar
                    </x-ui.button>

                    <x-ui.button
                        type="submit"
                        variant="primary"
                    >
                        {{ $owner->exists ? 'Guardar cambios' : 'Guardar dueño' }}
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>