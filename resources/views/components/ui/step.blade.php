@props([
    'number', // ej: '01' — usar SOLO cuando el contenido es realmente secuencial
    'title',
])

<div {{ $attributes->merge(['class' => 'bg-surface p-6']) }}>
    <p class="font-mono text-2xl font-semibold text-teal/30 mb-3">{{ $number }}</p>
    <p class="text-sm font-semibold text-ink mb-1">{{ $title }}</p>
    <p class="text-xs text-muted leading-relaxed">{{ $slot }}</p>
</div>

{{--
Uso (envolver el grupo en un contenedor con divisores, ver welcome.blade.php):
<x-ui.step number="01" title="Registro">
    Se registra al dueño y la mascota si son nuevos en el sistema.
</x-ui.step>
--}}
