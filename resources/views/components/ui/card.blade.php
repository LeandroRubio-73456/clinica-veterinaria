@props([
    'interactive' => false, // añade hover de borde teal, para cards clickeables
    'padding' => 'p-6',     // usar 'p-7' para cards tipo "rol" con más jerarquía
])

@php
$classes = "bg-surface border border-hairline rounded-lg {$padding}";
if ($interactive) {
    $classes .= ' hover:border-teal/40 transition-colors';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>

{{--
Uso:
<x-ui.card interactive padding="p-7">
    ...contenido de la tarjeta...
</x-ui.card>
--}}
