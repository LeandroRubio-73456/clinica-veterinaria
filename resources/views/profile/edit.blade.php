<x-layouts.app title="Mi perfil" max-width="max-w-6xl">
    <x-slot:actions>
        <x-ui.button href="{{ route('dashboard') }}" variant="secondary">
            Volver al dashboard
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">
        <x-ui.section-header
            eyebrow="Cuenta personal"
            title="Mi perfil"
            description="Actualiza tus datos personales y administra la seguridad de tu cuenta."
        />

        @if (session('status') === 'profile-updated')
            <div class="rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark" role="status">
                La información del perfil se actualizó correctamente.
            </div>
        @endif

        @if (session('status') === 'password-updated')
            <div class="rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark" role="status">
                La contraseña se actualizó correctamente.
            </div>
        @endif

        <div class="space-y-6">
            <x-ui.card>
                @include('profile.partials.update-profile-information-form')
            </x-ui.card>

            <x-ui.card>
                @include('profile.partials.update-password-form')
            </x-ui.card>

            <x-ui.card>
                @include('profile.partials.delete-user-form')
            </x-ui.card>
        </div>
    </div>
</x-layouts.app>
