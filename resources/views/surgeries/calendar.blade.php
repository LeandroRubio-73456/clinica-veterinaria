<x-layouts.app    
    title="Calendario de cirugías"
    max-width="max-w-7xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('surgeries.create') }}"
            variant="primary"
        >
            Nueva cirugía
        </x-ui.button>

        <x-ui.button
            href="{{ route('surgeries.index') }}"
            variant="secondary"
        >
            Ver cirugías
        </x-ui.button>
    </x-slot>
    <x-ui.section-header
        eyebrow="Cirugías"
        title="Agenda quirúrgica"
        description="Consulta las cirugías programadas y su distribución en el tiempo."
    />
    <div class="space-y-6">

        <x-ui.card>

            <div
                id="surgery-calendar"
                data-events-url="{{ route('surgeries.calendar.events') }}"
                class="min-h-[650px]"
            ></div>

        </x-ui.card>

    </div>

</x-layouts.app>
