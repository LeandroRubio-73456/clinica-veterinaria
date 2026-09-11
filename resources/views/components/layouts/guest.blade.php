@props([
    'title' => 'Clínica Veterinaria del Municipio',
    'showNav' => true,
    'showFooter' => true,
])

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-simple.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-base text-ink antialiased font-body">

    <x-layout.navbar :show-nav="$showNav" />

    <main>
        {{ $slot }}
    </main>

    @if ($showFooter)
        <x-layout.footer />
    @endif

</body>
</html>

{{--
Uso en resources/views/welcome.blade.php:

<x-layouts.guest title="Clínica Veterinaria del Municipio — Gestión de Cirugías">
    <section>...</section>
</x-layouts.guest>

Para login/register (sin nav de secciones ancla):
<x-layouts.guest title="Iniciar sesión" :show-nav="false">
    ...formulario...
</x-layouts.guest>
--}}
