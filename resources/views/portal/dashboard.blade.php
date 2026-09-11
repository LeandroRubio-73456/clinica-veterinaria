<x-layouts.portal title="Mis recordatorios">
    <div class="space-y-8">
        <x-ui.section-header eyebrow="Área del propietario" title="Mis recordatorios" description="Consulta las cirugías programadas para tus mascotas. Esta información es privada y solo corresponde a tu cuenta." />

        <x-ui.card padding="0">
            <div class="border-b border-hairline px-6 py-5"><h2 class="font-display text-xl font-semibold">Próximas cirugías</h2><p class="mt-1 text-sm text-muted">Solo se muestran procedimientos asignados a tus mascotas.</p></div>
            <div class="divide-y divide-hairline">
                @forelse($surgeries as $surgery)
                    <a href="{{ route('portal.surgery', $surgery) }}" class="block px-6 py-5 transition hover:bg-base focus:outline-none focus-visible:ring-2 focus-visible:ring-teal"><div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold">{{ $surgery->pet?->name }} — {{ $surgery->surgeryType?->name }}</p><p class="mt-1 text-sm text-muted">{{ $surgery->scheduled_date }} · {{ $surgery->start_time }} a {{ $surgery->end_time }} · {{ $surgery->veterinarian?->first_name }} {{ $surgery->veterinarian?->last_name }}</p></div><x-ui.badge :status="$surgery->state" /></div></a>
                @empty <p class="px-6 py-10 text-center text-sm text-muted">No tienes cirugías próximas registradas.</p> @endforelse
            </div>
        </x-ui.card>

        @if(($notifications ?? collect())->isNotEmpty())
            <x-ui.card><h2 class="font-display text-xl font-semibold">Avisos importantes</h2><div class="mt-4 space-y-3">@foreach($notifications->take(5) as $notification)<a href="{{ $notification['url'] }}" class="block rounded-md border border-hairline p-4 hover:bg-base"><p class="font-semibold text-ink">{{ $notification['title'] }}</p><p class="mt-1 text-sm text-muted">{{ $notification['message'] }}</p></a>@endforeach</div></x-ui.card>
        @endif

        <x-ui.card>
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-sm text-muted">Mascotas registradas</p><p class="mt-1 font-mono text-2xl font-semibold">{{ $pets->count() }}</p></div><a href="{{ route('portal.profile') }}" class="text-sm font-semibold text-teal hover:text-teal-dark">Actualizar mi información</a></div>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                @forelse($pets as $pet)
                    <div class="rounded-md border border-hairline p-4"><p class="font-semibold text-ink">{{ $pet->name }}</p><p class="mt-1 text-sm text-muted">{{ $pet->species?->name ?? 'Especie no especificada' }} · {{ $pet->breed ?: 'Raza no especificada' }}</p><p class="mt-1 text-xs text-muted">{{ $pet->age }} meses · {{ $pet->weight }} kg · {{ $pet->gender === 'female' ? 'Hembra' : 'Macho' }}</p></div>
                @empty
                    <p class="text-sm text-muted">No tienes mascotas registradas.</p>
                @endforelse
            </div>
        </x-ui.card>

        <x-ui.card padding="0">
            <div class="border-b border-hairline px-6 py-5"><h2 class="font-display text-xl font-semibold">Historial de cirugías</h2><p class="mt-1 text-sm text-muted">Consulta el seguimiento de los procedimientos anteriores y sus estados finales.</p></div>
            <div class="divide-y divide-hairline">
                @forelse($surgeryHistory as $surgery)
                    <a href="{{ route('portal.surgery', $surgery) }}" class="block px-6 py-5 transition hover:bg-base focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-teal"><div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><p class="font-semibold">{{ $surgery->pet?->name }} — {{ $surgery->surgeryType?->name }}</p><p class="mt-1 text-sm text-muted">{{ $surgery->scheduled_date }} · {{ $surgery->start_time }} a {{ $surgery->end_time }}</p>@if(in_array($surgery->state, ['cancelled', 'no_show'], true) && $surgery->notes)<p class="mt-2 text-sm text-muted"><span class="font-semibold text-ink">Motivo:</span> {{ $surgery->notes }}</p>@endif</div><x-ui.badge :status="$surgery->state" /></div></a>
                @empty
                    <p class="px-6 py-10 text-center text-sm text-muted">Aún no tienes cirugías anteriores registradas.</p>
                @endforelse
            </div>
        </x-ui.card>
    </div>
</x-layouts.portal>
