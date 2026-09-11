<x-layouts.guest
    title="Verificar correo electrónico"
    :show-nav="false"
    :show-footer="false"
>
    <main class="min-h-screen bg-base">
        <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-6 py-12 lg:px-8">
            <x-ui.card padding="7" class="w-full max-w-md">
                <div class="text-center">
                    <x-ui.eyebrow variant="kicker">
                        Verificación de cuenta
                    </x-ui.eyebrow>

                    <div class="mx-auto mt-5 flex h-12 w-12 items-center justify-center rounded-md bg-teal-light">
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-6 w-6 text-teal-dark"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="M3 7l9 6 9-6" />
                            <rect x="3" y="5" width="18" height="14" rx="2" />
                        </svg>
                    </div>

                    <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight text-ink">
                        Verifica tu correo
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-muted">
                        Antes de continuar, verifica tu dirección de correo
                        electrónico mediante el enlace que enviamos.
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="mt-6 rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-left text-sm text-teal-dark">
                            Se ha enviado un nuevo enlace de verificación a tu correo.
                        </div>
                    @endif

                    <div class="mt-8">
                        <form
                            method="POST"
                            action="{{ route('verification.send') }}"
                        >
                            @csrf

                            <x-ui.button
                                type="submit"
                                variant="primary"
                                class="w-full justify-center"
                            >
                                Reenviar enlace
                            </x-ui.button>
                        </form>
                    </div>

                    <div class="mt-6 border-t border-hairline pt-6">
                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="text-sm font-semibold text-muted hover:text-ink"
                            >
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </main>
</x-layouts.guest>