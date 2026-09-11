@props(['title' => 'Mi portal'])
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} — Clínica Veterinaria</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-simple.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base text-ink antialiased font-body">
    <a href="#portal-main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-surface focus:px-4 focus:py-3">Saltar al contenido principal</a>
    <header class="border-b border-hairline bg-surface">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4 lg:px-8">
            <a href="{{ route('portal.dashboard') }}" class="flex items-center gap-3 font-display text-lg font-semibold">
                <img src="{{ asset('images/logo-simple.png') }}" alt="" class="h-9 w-9 object-contain" aria-hidden="true">
                <span>Mi portal veterinario</span>
            </a>
            <div class="flex items-center gap-4 text-sm">
                <a href="{{ route('portal.profile') }}" class="text-muted hover:text-teal">Mi información</a>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="font-semibold text-alert hover:opacity-80">Cerrar sesión</button></form>
            </div>
        </div>
    </header>
    <main id="portal-main" tabindex="-1" class="mx-auto max-w-6xl px-6 py-10 outline-none lg:px-8">{{ $slot }}</main>
</body>
</html>
