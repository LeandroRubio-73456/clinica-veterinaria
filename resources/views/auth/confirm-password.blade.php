<x-layouts.guest
    title="Confirmar contraseña"
    :show-nav="false"
    :show-footer="false"
>
    <main class="min-h-screen bg-base">
        <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-6 py-12 lg:px-8">
            <x-ui.card padding="7" class="w-full max-w-md">
                <div class="mb-8">
                    <x-ui.eyebrow variant="kicker">
                        Confirmación de seguridad
                    </x-ui.eyebrow>

                    <h1 class="mt-3 font-display text-3xl font-semibold tracking-tight text-ink">
                        Confirma tu contraseña
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-muted">
                        Esta es un área protegida. Confirma tu contraseña
                        antes de continuar.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                        <p class="text-sm font-semibold text-alert">
                            La contraseña no es válida.
                        </p>

                        <p class="mt-1 text-sm text-muted">
                            Ingresa nuevamente tu contraseña para continuar.
                        </p>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('password.confirm') }}"
                    class="space-y-5"
                >
                    @csrf

                    <x-ui.input
                        label="Contraseña"
                        name="password"
                        type="password"
                        placeholder="Ingresa tu contraseña actual"
                        autocomplete="current-password"
                        required
                        :error="$errors->first('password')"
                    />

                    <x-ui.button
                        type="submit"
                        variant="primary"
                        class="w-full justify-center"
                    >
                        Confirmar contraseña
                    </x-ui.button>
                </form>
            </x-ui.card>
        </div>
    </main>
</x-layouts.guest>