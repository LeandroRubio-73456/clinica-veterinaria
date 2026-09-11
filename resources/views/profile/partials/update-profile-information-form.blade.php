<section aria-labelledby="profile-information-heading">
    <div>
        <x-ui.eyebrow variant="kicker">Información personal</x-ui.eyebrow>
        <h2 id="profile-information-heading" class="mt-2 font-display text-xl font-semibold text-ink">
            Datos de la cuenta
        </h2>
        <p class="mt-1 text-sm text-muted">
            Actualiza el nombre y el correo electrónico utilizados para iniciar sesión.
        </p>
    </div>

    <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')

        <div class="grid gap-5 md:grid-cols-2">
            <x-ui.input
                label="Nombre completo"
                name="name"
                type="text"
                autocomplete="name"
                required
                :value="$user->name"
                :error="$errors->getBag('updateProfileInformation')->first('name')"
            />

            <x-ui.input
                label="Correo electrónico"
                name="email"
                type="email"
                autocomplete="username"
                required
                :value="$user->email"
                :error="$errors->getBag('updateProfileInformation')->first('email')"
            />
        </div>

        <div class="flex justify-end border-t border-hairline pt-5">
            <x-ui.button type="submit" variant="primary">
                Guardar cambios
            </x-ui.button>
        </div>
    </form>

    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
        <div class="mt-5 rounded-md border border-amber/20 bg-amber/10 px-4 py-3 text-sm text-ink">
            <p>Tu correo electrónico aún no está verificado.</p>

            <form method="POST" action="{{ route('verification.send') }}" class="mt-2">
                @csrf
                <button type="submit" class="font-semibold text-teal hover:text-teal-dark">
                    Reenviar correo de verificación
                </button>
            </form>

            @if (session('status') === 'verification-link-sent')
                <p class="mt-2 text-sm text-success">Se envió un nuevo enlace de verificación.</p>
            @endif
        </div>
    @endif
</section>
