@props([
    'label',
    'value',
    'valueClass' => 'text-ink', // usar 'text-success' / 'text-alert' para resaltar el dato
])

<div>
    <dt class="font-mono text-[11px] text-muted uppercase tracking-wide mb-1">{{ $label }}</dt>
    <dd {{ $attributes->merge(['class' => "font-display text-2xl font-semibold {$valueClass}"]) }}>
        {{ $value }}
    </dd>
</div>

{{--
Uso (envolver el grupo en un <dl>):
<dl class="grid grid-cols-3 gap-6">
    <x-ui.stat label="Espera actual" value="{{ $stats->avg_wait ?? '—' }}" />
    <x-ui.stat label="Cirugías / día" value="{{ $stats->daily_surgeries ?? '—' }}" />
    <x-ui.stat label="Cruces de horario" value="0" value-class="text-success" />
</dl>
--}}
