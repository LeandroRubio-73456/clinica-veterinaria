@props([
    'variant' => 'kicker', // kicker (texto plano sobre un título) | pill (badge con punto, ej. hero)
])

@if ($variant === 'pill')
    <div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2 bg-teal-light text-teal-dark text-xs font-mono font-medium px-3 py-1.5 rounded-full']) }}>
        <span class="w-1.5 h-1.5 rounded-full bg-teal"></span>
        {{ $slot }}
    </div>
@else
    <p {{ $attributes->merge(['class' => 'font-mono text-xs text-teal tracking-widest uppercase']) }}>
        {{ $slot }}
    </p>
@endif

{{--
Uso:
<x-ui.eyebrow variant="pill">SISTEMA DE USO INTERNO</x-ui.eyebrow>
<x-ui.eyebrow>El problema</x-ui.eyebrow> {{-- variant kicker por defecto --}}
