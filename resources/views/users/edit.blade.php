<x-layouts.app title="Editar usuario" max-width="max-w-6xl">
    <x-slot:actions>
        <x-ui.button href="{{ route('users.show', $user) }}" variant="secondary">Ver usuario</x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">
        <x-ui.section-header eyebrow="Gestión de cuenta" title="Editar usuario" description="Actualiza los datos y el nivel de acceso de la cuenta." />
        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3" role="alert"><p class="text-sm font-semibold text-alert">No se pudo actualizar el usuario.</p><p class="mt-1 text-sm text-muted">Revisa los campos indicados e inténtalo nuevamente.</p></div>
        @endif
        <x-ui.card>
            <form method="POST" action="{{ route('users.update', $user) }}" class="space-y-8">
                @csrf
                @method('PUT')
                @include('users.partials.form')
                <div class="flex flex-col-reverse gap-3 border-t border-hairline pt-6 sm:flex-row sm:items-center sm:justify-between">
                    @if (auth()->id() !== $user->id)
                        <button type="submit" form="delete-user-form" class="text-left text-sm font-semibold text-alert hover:opacity-80">Eliminar usuario</button>
                    @else
                        <span class="text-sm text-muted">Esta es tu cuenta actual.</span>
                    @endif
                    <div class="flex gap-3">
                        <x-ui.button href="{{ route('users.show', $user) }}" variant="secondary">Cancelar</x-ui.button>
                        <x-ui.button type="submit" variant="primary">Guardar cambios</x-ui.button>
                    </div>
                </div>
            </form>
            @if (auth()->id() !== $user->id)
                <form id="delete-user-form" method="POST" action="{{ route('users.destroy', $user) }}" data-confirm-title="Eliminar usuario" data-confirm-message="Esta acción eliminará la cuenta de acceso de forma permanente. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente">@csrf @method('DELETE')</form>
            @endif
        </x-ui.card>
    </div>
</x-layouts.app>
