<x-layouts.app title="Crear usuario" max-width="max-w-6xl">
    <x-slot:actions>
        <x-ui.button href="{{ route('users.index') }}" variant="secondary">Volver a usuarios</x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">
        <x-ui.section-header eyebrow="Nuevo registro" title="Crear usuario" description="Registra una cuenta para un integrante del personal de la clínica." />
        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3" role="alert"><p class="text-sm font-semibold text-alert">No se pudo crear el usuario.</p><p class="mt-1 text-sm text-muted">Revisa los campos indicados e inténtalo nuevamente.</p></div>
        @endif
        <x-ui.card>
            <form method="POST" action="{{ route('users.store') }}" class="space-y-8">
                @csrf
                @include('users.partials.form')
                <div class="flex flex-col-reverse gap-3 border-t border-hairline pt-6 sm:flex-row sm:justify-end">
                    <x-ui.button href="{{ route('users.index') }}" variant="secondary">Cancelar</x-ui.button>
                    <x-ui.button type="submit" variant="primary">Crear usuario</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-layouts.app>
