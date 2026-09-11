@props([
    'name',              // ej: nombre del quirófano o de la mascota en cirugía
    'meta' => null,      // ej: "Esterilización · Dra. Pérez"
    'status' => 'available', // available | in_progress | occupied | maintenance
])

@php
$dotColor = [
    'available'   => 'bg-success',
    'occupied'    => 'bg-alert',
    'in_progress' => 'bg-amber',
    'maintenance' => 'bg-muted',
][$status] ?? 'bg-muted';
@endphp

<div {{ $attributes->merge(['class' => 'flex items-center justify-between px-5 py-4']) }}>
    <div class="flex items-center gap-3">
        <span class="w-2 h-2 rounded-full {{ $dotColor }}"></span>
        <div>
            <p class="text-white text-sm font-medium">{{ $name }}</p>
            @if ($meta)
                <p class="font-mono text-white/40 text-xs">{{ $meta }}</p>
            @else
                <p class="font-mono text-white/40 text-xs">Sin cirugía asignada</p>
            @endif
        </div>
    </div>
    <x-ui.badge :status="$status" />
</div>
