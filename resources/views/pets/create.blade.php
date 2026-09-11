<x-layouts.app
    title="Registrar mascota"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('pets.index') }}"
            variant="secondary"
        >
            Volver a mascotas
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Nuevo registro"
            title="Registrar mascota"
            description="Registra los datos básicos de la mascota y asígnala a su dueño."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                <p class="text-sm font-semibold text-alert">
                    No se pudo guardar la mascota.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>
            </div>
        @endif

        <x-ui.card>
            <form
                method="POST"
                action="{{ route('pets.store') }}"
                class="space-y-8"
            >
                @csrf

                @include('pets.partials.form')

                <div class="flex flex-col-reverse gap-3 border-t border-hairline pt-6 sm:flex-row sm:justify-end">
                    <x-ui.button
                        href="{{ route('pets.index') }}"
                        variant="secondary"
                    >
                        Cancelar
                    </x-ui.button>

                    <x-ui.button
                        type="submit"
                        variant="primary"
                    >
                        Guardar mascota
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>