{{-- Contenedor cuadrado para íconos SVG (feather-style, stroke-width 2) --}}
<div {{ $attributes->merge(['class' => 'w-10 h-10 rounded-md bg-teal-light flex items-center justify-center']) }}>
    {{ $slot }}
</div>

{{--
Uso:
<x-ui.icon-box>
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B5F67" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 11l3 3L22 4"/>
    </svg>
</x-ui.icon-box>
--}}
