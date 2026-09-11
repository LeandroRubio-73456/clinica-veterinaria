<x-layouts.guest title="Clínica Veterinaria del Municipio">
    <section class="relative overflow-hidden border-b border-hairline bg-surface">
        <div class="mx-auto grid max-w-7xl items-center gap-10 px-6 py-14 lg:grid-cols-[1.05fr_.95fr] lg:gap-16 lg:px-8 lg:py-20">
            <div>
                <x-ui.eyebrow variant="pill" class="mb-5">Cuidado veterinario municipal</x-ui.eyebrow>
                <h1 class="max-w-2xl font-display text-4xl font-semibold leading-[1.08] tracking-tight text-ink sm:text-6xl">Atención organizada para el bienestar de tu mascota.</h1>
                <p class="mt-6 max-w-xl text-lg leading-relaxed text-muted">En la Clínica Veterinaria del Municipio coordinamos cada cirugía con un equipo profesional, espacios preparados y la información necesaria para acompañarte durante el proceso.</p>
                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <x-ui.button href="{{ route('login') }}">Ya tengo una cuenta <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M12 5l7 7-7 7"/></svg></x-ui.button>
                </div>
                <p class="mt-4 text-sm text-muted">El personal de la clínica crea tu cuenta al registrarte como propietario para que puedas consultar los recordatorios y las cirugías de tus mascotas.</p>
            </div>
            <x-ui.card padding="p-7" class="relative overflow-hidden bg-teal-light/40">
                <div class="absolute -right-14 -top-14 h-40 w-40 rounded-full bg-teal/10"></div>
                <img src="{{ asset('images/logo.png') }}" alt="Clínica Veterinaria del Municipio" class="relative mx-auto h-auto w-56 object-contain sm:w-64">
                <div class="relative mt-7 border-t border-teal/15 pt-6">
                    <p class="font-mono text-xs uppercase tracking-widest text-teal-dark">Una atención más clara</p>
                    <p class="mt-3 font-display text-2xl font-semibold text-ink">Tu información, siempre a mano.</p>
                    <p class="mt-3 text-sm leading-relaxed text-muted">Desde tu cuenta puedes revisar la fecha, el procedimiento y el estado de las cirugías asignadas a tus mascotas.</p>
                </div>
            </x-ui.card>
        </div>
    </section>

    <section id="servicios" class="mx-auto max-w-7xl border-b border-hairline px-6 py-16 lg:px-8 lg:py-20">
        <x-ui.section-header eyebrow="Nuestra atención" title="Información útil para cuidar mejor a tu mascota" description="La clínica mantiene una agenda organizada para que cada procedimiento tenga un equipo y un espacio preparados." />
        <div class="grid gap-6 sm:grid-cols-3">
            <x-ui.card><x-ui.icon-box class="mb-4"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B5F67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/></svg></x-ui.icon-box><h2 class="font-display font-semibold text-ink">Acompañamiento profesional</h2><p class="mt-2 text-sm leading-relaxed text-muted">El personal registra y coordina cada procedimiento para dar seguimiento a tu mascota.</p></x-ui.card>
            <x-ui.card><x-ui.icon-box class="mb-4"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B5F67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></x-ui.icon-box><h2 class="font-display font-semibold text-ink">Citas coordinadas</h2><p class="mt-2 text-sm leading-relaxed text-muted">La agenda ayuda a evitar cruces y a mantener disponible el espacio necesario para cada cirugía.</p></x-ui.card>
            <x-ui.card><x-ui.icon-box class="mb-4"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#0B5F67" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16v16H4z"/><path d="M8 12h8M8 8h8M8 16h5"/></svg></x-ui.icon-box><h2 class="font-display font-semibold text-ink">Avisos oportunos</h2><p class="mt-2 text-sm leading-relaxed text-muted">Recibe recordatorios y actualizaciones importantes en tu cuenta y en tu correo electrónico.</p></x-ui.card>
        </div>
    </section>

    <section id="informacion" class="bg-ink text-white">
        <div class="mx-auto grid max-w-7xl gap-10 px-6 py-16 lg:grid-cols-[1fr_auto] lg:items-center lg:px-8 lg:py-20">
            <div><p class="font-mono text-xs uppercase tracking-widest text-teal-light">La clínica en cifras</p><h2 class="mt-3 max-w-2xl font-display text-3xl font-semibold sm:text-4xl">Una agenda preparada para atenderte.</h2><p class="mt-4 max-w-2xl leading-relaxed text-white/70">Estos datos se actualizan desde la agenda actual de la clínica y muestran la capacidad operativa registrada.</p></div>
            <dl class="grid grid-cols-3 gap-6 rounded-xl border border-white/10 bg-white/5 p-6 sm:gap-10 sm:p-8"><div><dt class="text-xs text-white/60">Quirófanos visibles</dt><dd class="mt-2 font-display text-3xl font-semibold text-white">{{ $operatingRoomCount }}</dd></div><div><dt class="text-xs text-white/60">Procedimientos disponibles</dt><dd class="mt-2 font-display text-3xl font-semibold text-white">{{ $activeSurgeryTypeCount }}</dd></div><div><dt class="text-xs text-white/60">Cirugías este mes</dt><dd class="mt-2 font-display text-3xl font-semibold text-teal-light">{{ $monthlySurgeryCount }}</dd></div></dl>
        </div>
    </section>

    <section id="registro" class="mx-auto max-w-7xl px-6 py-16 text-center lg:px-8 lg:py-20">
        <x-ui.eyebrow class="mb-3">Para propietarios</x-ui.eyebrow>
        <h2 class="font-display text-3xl font-semibold text-ink sm:text-4xl">Consulta la información de tu mascota cuando la necesites.</h2>
        <p class="mx-auto mt-4 max-w-2xl leading-relaxed text-muted">Al registrar a tu mascota, el personal de la clínica crea tu cuenta y te entrega tus credenciales de acceso.</p>
        <div class="mt-7"><x-ui.button href="{{ route('login') }}">Iniciar sesión</x-ui.button></div>
    </section>
</x-layouts.guest>
