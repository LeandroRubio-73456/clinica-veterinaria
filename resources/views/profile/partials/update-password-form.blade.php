<section aria-labelledby="update-password-heading">
    <div>
        <x-ui.eyebrow variant="kicker">Seguridad</x-ui.eyebrow>
        <h2 id="update-password-heading" class="mt-2 font-display text-xl font-semibold text-ink">
            Cambiar contraseña
        </h2>
        <p class="mt-1 text-sm text-muted">
            Utiliza una contraseña larga y exclusiva para tu cuenta.
        </p>
    </div>

    <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-5">
        @csrf
        @method('PUT')

        <x-ui.input
            label="Contraseña actual"
            name="current_password"
            type="password"
            autocomplete="current-password"
            required
            :error="$errors->getBag('updatePassword')->first('current_password')"
        />

        <div class="grid gap-5 md:grid-cols-2">
            <x-ui.input
                label="Nueva contraseña"
                name="password"
                type="password"
                autocomplete="new-password"
                required
                :error="$errors->getBag('updatePassword')->first('password')"
            />

            <x-ui.input
                label="Confirmar nueva contraseña"
                name="password_confirmation"
                type="password"
                autocomplete="new-password"
                required
                :error="$errors->getBag('updatePassword')->first('password_confirmation')"
            />
        </div>

        <div class="flex justify-end border-t border-hairline pt-5">
            <x-ui.button type="submit" variant="primary">
                Actualizar contraseña
            </x-ui.button>
        </div>
    </form>
</section>
