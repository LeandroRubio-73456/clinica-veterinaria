@props([
    'status' => 'available', // available | in_progress | occupied | maintenance | neutral
])

@php
// Mapea directamente los ENUM de operating_rooms.state y surgeries.state
// available/completed -> success | in_progress -> amber | occupied/cancelled/no_show -> alert
$map = [
    'active'   => ['classes' => 'text-success bg-success/10', 'label' => 'Activo'],
    'available'   => ['classes' => 'text-success bg-success/10', 'label' => 'Disponible'],
    'completed'   => ['classes' => 'text-success bg-success/10', 'label' => 'Completada'],
    'scheduled'   => ['classes' => 'text-teal bg-teal/10', 'label' => 'Programada'],
    'in_progress' => ['classes' => 'text-amber bg-amber/10', 'label' => 'En curso'],
    'occupied'    => ['classes' => 'text-alert bg-alert/10', 'label' => 'Ocupado'],
    'cancelled'   => ['classes' => 'text-alert bg-alert/10', 'label' => 'Cancelada'],
    'inactive'   => ['classes' => 'text-alert bg-alert/10', 'label' => 'Inactivo'],
    'no_show'     => ['classes' => 'text-alert bg-alert/10', 'label' => 'No se presentó'],
    'maintenance' => ['classes' => 'text-muted bg-hairline', 'label' => 'Mantenimiento'],
    'neutral'     => ['classes' => 'text-muted bg-hairline', 'label' => 'N/A'],
];

$config = $map[$status] ?? $map['neutral'];
@endphp

<span {{ $attributes->merge(['class' => "font-mono text-[11px] font-medium px-2.5 py-1 rounded {$config['classes']}"]) }}>
    {{ $slot->isEmpty() ? strtoupper($config['label']) : $slot }}
</span>

{{--
Uso:
<x-ui.badge status="available" />
<x-ui.badge status="in_progress" />
<x-ui.badge status="{{ $surgery->state }}" /> {{-- usa directamente el enum de la BD --}}