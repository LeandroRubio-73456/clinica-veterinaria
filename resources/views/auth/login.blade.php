<x-layouts.guest
    title="Iniciar sesión"
    :show-nav="false"
    :show-footer="false"
>
    <main class="min-h-screen bg-base">
        <div class="mx-auto flex min-h-screen max-w-6xl items-center px-6 py-12 lg:px-8">
            <div class="grid w-full overflow-hidden rounded-lg border border-hairline bg-surface lg:grid-cols-[0.9fr_1.1fr]">

                {{-- Contexto del sistema --}}
                <section class="hidden bg-ink p-10 text-white lg:flex lg:flex-col lg:justify-between">
                    <div>
                        <p class="font-mono text-xs uppercase tracking-widest text-white/60">
                            Clínica Veterinaria del Municipio
                        </p>

                        <h1 class="mt-5 max-w-md font-display text-4xl font-semibold tracking-tight leading-[1.08]">
                            Gestión interna de cirugías y quirófanos
                        </h1>

                        <p class="mt-5 max-w-md text-sm leading-6 text-white/70">
                            Accede al sistema para consultar agendas, gestionar cirugías
                            y coordinar la disponibilidad de quirófanos.
                        </p>
                    </div>

                    <div class="border-t border-white/10 pt-6">
                        <p class="font-mono text-[11px] uppercase tracking-widest text-white/50">
                            Acceso exclusivo
                        </p>

                        <p class="mt-2 text-sm text-white/70">
                            Personal administrativo, veterinarios y administradores.
                        </p>
                    </div>
                </section>

                {{-- Formulario --}}
                <section class="p-6 sm:p-10 lg:p-12">
                    <div class="mx-auto max-w-md">
                        <div class="mb-8">
                            <x-ui.eyebrow variant="kicker">
                                Acceso al sistema
                            </x-ui.eyebrow>

                            <h2 class="mt-3 font-display text-3xl font-semibold tracking-tight text-ink">
                                Iniciar sesión
                            </h2>

                            <p class="mt-3 text-sm leading-6 text-muted">
                                Ingresa tus credenciales para acceder al sistema interno.
                            </p>
                        </div>

                        @if (session('status'))
                            <div class="mb-6 rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark">
                                {{ session('status') }}
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="mb-6 rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                                <p class="text-sm font-semibold text-alert">
                                    No se pudo iniciar sesión.
                                </p>

                                <p class="mt-1 text-sm text-muted">
                                    Verifica las credenciales ingresadas e inténtalo nuevamente.
                                </p>
                            </div>
                        @endif

                        <form
                            method="POST"
                            action="{{ route('login') }}"
                            class="space-y-5"
                        >
                            @csrf

                            <x-ui.input
                                label="Usuario o cédula"
                                name="identifier"
                                type="text"
                                placeholder="Ingresa tu cédula o usuario"
                                autocomplete="username"
                                required
                                :error="$errors->first('identifier')"
                            />

                            <x-ui.input
                                label="Contraseña"
                                name="password"
                                type="password"
                                placeholder="Ingresa tu contraseña"
                                autocomplete="current-password"
                                required
                                :error="$errors->first('password')"
                            />

                            <div class="flex items-center justify-between gap-4 pt-1">
                                <label class="flex items-center gap-2 text-sm text-muted">
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        class="h-4 w-4 rounded border-hairline text-teal focus:ring-teal/20"
                                    >

                                    <span>Recordarme</span>
                                </label>

                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-sm font-semibold text-teal hover:text-teal-dark"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            <x-ui.button
                                type="submit"
                                variant="primary"
                                class="w-full justify-center"
                            >
                                Iniciar sesión
                            </x-ui.button>
                        </form>
                        <p class="mt-8 border-t border-hairline pt-6 text-center text-sm text-muted">
                            Si eres propietario, solicita tu acceso al personal de la clínica.
                        </p>
                    </div>
                </section>
            </div>
        </div>
    </main>
</x-layouts.guest>
