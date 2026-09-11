<x-layouts.portal title="Detalle de cirugía">
    <div class="space-y-8">
        <x-ui.section-header eyebrow="Área del propietario" title="Detalle de la cirugía" description="Consulta la información de la cirugía asignada a tu mascota." />

        <x-ui.card>
            <div class="flex flex-col gap-4 border-b border-hairline pb-6 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <p class="text-sm text-muted">Mascota</p>
                    <h2 class="mt-1 font-display text-2xl font-semibold">{{ $surgery->pet?->name ?? 'Sin nombre' }}</h2>
                    <p class="mt-1 text-sm text-muted">{{ $surgery->pet?->species?->name ?? 'Especie no especificada' }}</p>
                </div>
                <x-ui.badge :status="$surgery->state" />
            </div>

            <dl class="mt-6 grid gap-5 sm:grid-cols-2">
                <div><dt class="text-sm text-muted">Procedimiento</dt><dd class="mt-1 font-semibold">{{ $surgery->surgeryType?->name ?? 'No especificado' }}</dd></div>
                <div><dt class="text-sm text-muted">Fecha</dt><dd class="mt-1 font-semibold">{{ $surgery->scheduled_date }}</dd></div>
                <div><dt class="text-sm text-muted">Horario</dt><dd class="mt-1 font-semibold">{{ $surgery->start_time }} a {{ $surgery->end_time }}</dd></div>
                <div><dt class="text-sm text-muted">Veterinario</dt><dd class="mt-1 font-semibold">{{ trim(($surgery->veterinarian?->first_name ?? '') . ' ' . ($surgery->veterinarian?->last_name ?? '')) ?: 'No especificado' }}</dd></div>
                <div><dt class="text-sm text-muted">Quirófano</dt><dd class="mt-1 font-semibold">{{ $surgery->operatingRoom?->name ?? 'No especificado' }}</dd></div>
            </dl>

            @if($surgery->notes)
                <div class="mt-6 rounded-md bg-base p-4"><p class="text-sm font-semibold">Notas</p><p class="mt-1 text-sm text-muted">{{ $surgery->notes }}</p></div>
            @endif

            <div class="mt-6"><a href="{{ route('portal.dashboard') }}" class="text-sm font-semibold text-teal hover:text-teal-dark">← Volver a mis recordatorios</a></div>
        </x-ui.card>
    </div>
</x-layouts.portal>
