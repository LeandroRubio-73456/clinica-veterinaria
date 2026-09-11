<x-layouts.app
    title="Editar cirugía"
    max-width="max-w-6xl"
>
    <x-slot:actions>

        <x-ui.button
            href="{{ route('surgeries.show', $surgery) }}"
            variant="secondary"
        >
            Ver cirugía
        </x-ui.button>

    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Programación quirúrgica"
            title="Editar cirugía"
            description="Actualiza la programación y los recursos asignados a esta cirugía."
        />

        <x-ui.card>

            <div class="mb-8 flex items-center justify-between gap-4 border-b border-hairline pb-6">

                <div>

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Cirugía
                    </p>

                    <p class="mt-1 font-mono text-sm font-medium text-ink">
                        #{{ $surgery->id }}
                    </p>

                </div>

                <x-ui.badge :status="$surgery->state" />

            </div>

            @if ($errors->any())

                <div class="mb-8 rounded-md border border-alert/20 bg-alert/5 px-4 py-3">

                    <p class="text-sm font-semibold text-alert">
                        No se pudo actualizar la cirugía.
                    </p>

                    <p class="mt-1 text-sm text-muted">
                        Revisa los campos indicados y verifica nuevamente la disponibilidad.
                    </p>

                </div>

            @endif

            <form
                id="surgery-edit-form"
                method="POST"
                action="{{ route('surgeries.update', $surgery) }}"
                class="space-y-8"
            >

                @csrf
                @method('PUT')

                @include('surgeries.partials.form', [
                    'showStateField' => false,
                    'showScheduler' => true,
                    'formId' => 'surgery-edit-form',
                ])

                <div class="flex flex-col-reverse gap-3 border-t border-hairline pt-6 sm:flex-row sm:justify-end">

                    <x-ui.button
                        href="{{ route('surgeries.show', $surgery) }}"
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

            </form>

        </x-ui.card>

    </div>
</x-layouts.app>
