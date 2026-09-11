<x-layouts.app title="Programar cirugía" max-width="max-w-6xl">
    <x-slot:actions>

        <x-ui.button href="{{ route('surgeries.index') }}" variant="secondary">
            Volver a cirugías
        </x-ui.button>

    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Programación quirúrgica" title="Programar cirugía"
            description="Registra una nueva cirugía y asigna los recursos necesarios para su realización." />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">

                <p class="text-sm font-semibold text-alert">
                    No se pudo programar la cirugía.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados y verifica la disponibilidad del veterinario y del quirófano.
                </p>

            </div>
        @endif

        <x-ui.card>

            <form id="surgery-create-form" method="POST" action="{{ route('surgeries.store') }}" class="space-y-8">

                @csrf

                @include('surgeries.partials.form', [
                    'showStateField' => false,
                    'showScheduler' => true,
                    'formId' => 'surgery-create-form',
                ])

                <div class="flex flex-col-reverse gap-3 border-t border-hairline pt-6 sm:flex-row sm:justify-end">

                    <x-ui.button href="{{ route('surgeries.index') }}" variant="secondary">
                        Cancelar
                    </x-ui.button>

                    <x-ui.button type="submit" variant="primary">
                        Programar cirugía
                    </x-ui.button>

                </div>

            </form>

        </x-ui.card>

    </div>
</x-layouts.app>
