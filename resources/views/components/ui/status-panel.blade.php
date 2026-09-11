@props([
    'title' => 'Estado de quirófanos',
    'timestamp' => null,  // ej: now()->format('H:i — d/m/Y'); si null, se muestra placeholder
    'footerNote' => null, // ej: "Total de quirófanos activos: { $count }"
])

<div {{ $attributes->merge(['class' => 'bg-ink rounded-xl shadow-xl overflow-hidden border border-ink/10']) }}>
    <div class="flex items-center justify-between px-5 py-4 border-b border-white/10">
        <p class="font-mono text-white text-xs tracking-widest uppercase">{{ $title }}</p>
        <p class="font-mono text-white/50 text-xs">{{ $timestamp ?? '[hh:mm — dd/mm/aaaa]' }}</p>
    </div>

    <div class="divide-y divide-white/10">
        {{ $slot }}
    </div>

    @if ($footerNote)
        <div class="px-5 py-3 bg-white/5">
            <p class="font-mono text-white/40 text-[11px]">{{ $footerNote }}</p>
        </div>
    @endif
</div>

{{--
Uso — ver también x-ui.status-row:
<x-ui.status-panel timestamp="{{ now()->format('H:i — d/m/Y') }}"
                    footer-note="Total de quirófanos activos: {{ $rooms->count() }}">
    @foreach ($rooms as $room)
        <x-ui.status-row
            :name="$room->name"
            :meta="$room->currentSurgery?->surgeryType->name . ' · ' . $room->currentSurgery?->veterinarian->name"
            :status="$room->state" />
    @endforeach
</x-ui.status-panel>
--}}
