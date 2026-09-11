<x-layouts.app
    title="Editar dueño"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <x-ui.button
            href="{{ route('owners.show', $owner) }}"
            variant="secondary"
        >
            Ver dueño
        </x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">
        <x-ui.section-header
            eyebrow="Gestión de registro"
            title="Editar dueño"
            description="Actualiza la información del propietario."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                <p class="text-sm font-semibold text-alert">
                    No se pudo actualizar el registro.
                </p>

                <p class="mt-1 text-sm text-muted">
                    Revisa los campos indicados e inténtalo nuevamente.
                </p>
            </div>
        @endif

        <x-ui.card>
            <form
                method="POST"
                action="{{ $owner->exists
                    ? route('owners.update', $owner)
                    : route('owners.store') }}"
                class="space-y-8"
            >
                @csrf

                @if ($owner->exists)
                    @method('PUT')
                @endif

                @include('owners.partials.form')

                <div class="flex flex-col-reverse gap-3 border-t border-hairline pt-6 sm:flex-row sm:items-center sm:justify-between">
                    <button
                        type="button"
                        class="text-left text-sm font-semibold text-alert hover:opacity-80"
                        onclick="document.getElementById('delete-owner-form').submit();"
                    >
                        Eliminar dueño
                    </button>
                    <div class="flex gap-3">
                        <x-ui.button
                            href="{{ route('owners.show', $owner) }}"
                            variant="secondary"
                        >
                            Cancelar
                        </x-ui.button>

                        <x-ui.button
                            type="submit"
                            variant="primary"
                        >
                            Guardar cambios
                        </x-ui.button>
                    </div>
                </div>
            </form>

            <form
                id="delete-owner-form"
                method="POST"
                action="{{ route('owners.destroy', $owner) }}"
                    data-confirm-title="Eliminar dueño" data-confirm-message="Esta acción eliminará el registro de forma permanente. ¿Deseas continuar?" data-confirm-label="Eliminar definitivamente"
            >
                @csrf
                @method('DELETE')
            </form>

        </x-ui.card>
    </div>
</x-layouts.app>
