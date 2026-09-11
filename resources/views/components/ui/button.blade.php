@props([
    'variant' => 'primary', // primary | secondary
    'href' => null,         // si viene, renderiza <a>, si no, <button>
    'type' => 'button',
])

@php
$base = 'inline-flex items-center gap-2 text-sm font-semibold rounded-md transition-colors';

$variants = [
    'primary'   => 'bg-teal hover:bg-teal-dark text-white px-5 py-3',
    'secondary' => 'text-ink hover:text-teal px-0 py-0',
    'warning'   => 'text-amber hover:opacity-80 px-0 py-0',
    'danger'    => 'text-alert hover:opacity-80 px-0 py-0',
];

$classes = $base . ' ' . ($variants[$variant] ?? $variants['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif

{{--
Uso:
<x-ui.button href="{{ route('login') }}">Iniciar sesión</x-ui.button>
<x-ui.button variant="secondary" href="#flujo">Ver cómo funciona</x-ui.button>
<x-ui.button type="submit">Guardar</x-ui.button>
--}}
