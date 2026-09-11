<x-layouts.guest
    title="Restablecer contraseña"
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
                        Nueva contraseña
                    </h1>

                    <p class="mt-3 text-sm leading-6 text-muted">
                        Define una nueva contraseña para continuar utilizando
                        tu cuenta del sistema.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                        <p class="text-sm font-semibold text-alert">
                            No se pudo actualizar la contraseña.
                        </p>

                        <p class="mt-1 text-sm text-muted">
                            Revisa los datos ingresados e inténtalo nuevamente.
                        </p>
                    </div>
                @endif

                <form
                    method="POST"
                    action="{{ route('password.store') }}"
                    class="space-y-5"
                >
                    @csrf

                    <input
                        type="hidden"
                        name="token"
                        value="{{ $request->route('token') }}"
                    >

                    <x-ui.input
                        label="Correo electrónico"
                        name="email"
                        type="email"
                        placeholder="personal@clinica.local"
                        autocomplete="email"
                        required
                        :value="$request->email"
                        :error="$errors->first('email')"
                    />

                    <x-ui.input
                        label="Nueva contraseña"
                        name="password"
                        type="password"
                        placeholder="Ingresa tu nueva contraseña"
                        autocomplete="new-password"
                        required
                        :error="$errors->first('password')"
                    />

                    <x-ui.input
                        label="Confirmar contraseña"
                        name="password_confirmation"
                        type="password"
                        placeholder="Repite tu nueva contraseña"
                        autocomplete="new-password"
                        required
                        :error="$errors->first('password_confirmation')"
                    />

                    <x-ui.button
                        type="submit"
                        variant="primary"
                        class="w-full justify-center"
                    >
                        Restablecer contraseña
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