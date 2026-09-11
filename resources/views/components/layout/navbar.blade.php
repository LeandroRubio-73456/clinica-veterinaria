@props([
    'showNav' => true,
])

<header class="sticky top-0 z-50 border-b border-hairline bg-surface/95 backdrop-blur">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-6 lg:px-8">
        <a href="{{ url('/') }}" class="flex shrink-0 items-center gap-3" aria-label="Clínica Veterinaria del Municipio, inicio">
            <img src="{{ asset('images/logo-simple.png') }}" alt="" class="h-9 w-9 object-contain" aria-hidden="true">
            <div class="hidden leading-tight sm:block">
                <p class="font-display text-sm font-semibold text-ink">Clínica Veterinaria</p>
                <p class="font-mono text-[11px] tracking-wide text-muted">DEL MUNICIPIO — QUITO</p>
            </div>
        </a>

        @if ($showNav)
            <nav class="hidden items-center gap-7 font-medium text-sm text-muted md:flex" aria-label="Navegación principal">
                <a href="#servicios" class="transition-colors hover:text-ink">Nuestra atención</a>
                <a href="#informacion" class="transition-colors hover:text-ink">La clínica en cifras</a>
                <a href="#registro" class="transition-colors hover:text-ink">Propietarios</a>
            </nav>
        @endif

        <div class="flex items-center gap-2 sm:gap-3">
            @auth
                <x-ui.button href="{{ route('dashboard') }}" class="!px-3 !py-2 sm:!px-4 sm:!py-2.5">Ir a mi área</x-ui.button>
            @else
                <x-ui.button href="{{ route('login') }}" class="!px-3 !py-2 sm:!px-4 sm:!py-2.5">Iniciar sesión</x-ui.button>
            @endauth
        </div>
    </div>
</header>
