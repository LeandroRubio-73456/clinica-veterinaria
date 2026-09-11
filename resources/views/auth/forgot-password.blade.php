<x-layouts.guest
    title="Recuperar contraseña"
    :show-nav="false"
    :show-footer="false"
>
    <main class="min-h-screen bg-base">
        <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-6 py-12 lg:px-8">
            <section class="w-full max-w-md rounded-lg border border-hairline bg-surface p-6 sm:p-10">
                <div class="mb-8">
                    <x-ui.eyebrow variant="kicker">
                        Recuperación de acceso
                    </x-ui.eyebrow>

                    <h1 class="mt-3 font-display text-3xl font-semibold tracking-tight text-ink">
                        Recuperar contraseña
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-muted">
                        Ingresa tu correo electrónico y te enviaremos un enlace
                        para restablecer tu contraseña.
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
                            No se pudo procesar la solicitud.
                        </p>

                        <p class="mt-1 text-sm text-muted">
                            Verifica el correo ingresado e inténtalo nuevamente.
                        </p>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('password.email') }}"
                    class="space-y-5"
                >
                    @csrf

                    <x-ui.input
                        label="Correo electrónico"
                        name="email"
                        type="email"
                        placeholder="personal@clinica.local"
                        autocomplete="email"
                        required
                        :error="$errors->first('email')"
                    />

                    <x-ui.button
                        type="submit"
                        variant="primary"
                        class="w-full justify-center"
                    >
                        Enviar enlace
                    </x-ui.button>
                </form>

                <div class="mt-6 text-center">
                    <a
                        href="{{ route('login') }}"
                        class="text-sm font-semibold text-teal hover:text-teal-dark"
                    >
                        Volver a iniciar sesión
                    </a>
                </div>
            </section>
        </div>
    </main>
</x-layouts.guest>