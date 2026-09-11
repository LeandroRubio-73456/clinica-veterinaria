<section aria-labelledby="delete-account-heading">
    <div>
        <x-ui.eyebrow variant="kicker">Zona sensible</x-ui.eyebrow>
        <h2 id="delete-account-heading" class="mt-2 font-display text-xl font-semibold text-ink">
            Eliminar cuenta
        </h2>
        <p class="mt-1 text-sm text-muted">
            Esta acción cerrará tu sesión y eliminará permanentemente tu cuenta.
        </p>
    </div>

    <form
        method="POST"
        action="{{ route('profile.destroy') }}"
        class="mt-6 space-y-5"
        data-confirm-title="Eliminar mi cuenta" data-confirm-message="Esta acción eliminará permanentemente tu cuenta y cerrará tu sesión. No se puede deshacer. ¿Deseas continuar?" data-confirm-label="Eliminar mi cuenta"
    >
        @csrf
        @method('DELETE')

        <x-ui.input
            label="Confirma tu contraseña para continuar"
            name="password"
            type="password"
            autocomplete="current-password"
            required
            :error="$errors->getBag('userDeletion')->first('password')"
        />

        <div class="flex justify-end border-t border-hairline pt-5">
            <x-ui.button type="submit" variant="danger">
                Eliminar mi cuenta
            </x-ui.button>
        </div>
    </form>
</section>
