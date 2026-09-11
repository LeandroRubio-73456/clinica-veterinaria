<x-layouts.portal title="Mi información">
    <div class="mx-auto max-w-3xl space-y-8">
        <x-ui.section-header eyebrow="Cuenta personal" title="Mi información" description="Actualiza tus datos de contacto y la contraseña de acceso." />
        @if(session('status'))<div class="rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark" role="status">La información se actualizó correctamente.</div>@endif
        <x-ui.card><form method="POST" action="{{ route('portal.profile.update') }}" class="space-y-5">@csrf @method('PATCH')
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-ink">Cédula (usuario de acceso)</label>
                    <p class="mt-1 rounded-md border border-hairline bg-base px-4 py-2.5 text-sm text-muted">{{ $owner?->cedula ?? 'No registrada' }}</p>
                </div>
                <x-ui.input label="Nombre mostrado" name="name" :value="old('name',$user->name)" required /><x-ui.input label="Correo electrónico" name="email" type="email" :value="old('email',$user->email)" required /><x-ui.input label="Nombres" name="first_name" :value="old('first_name',$owner?->first_name)" required /><x-ui.input label="Apellidos" name="last_name" :value="old('last_name',$owner?->last_name)" required /><x-ui.input label="Teléfono" name="phone" :value="old('phone',$owner?->phone)" required /><x-ui.input label="Dirección" name="address" :value="old('address',$owner?->address)" required /></div>
            <div class="flex justify-end border-t border-hairline pt-5"><x-ui.button type="submit" variant="primary">Guardar información</x-ui.button></div>
        </form></x-ui.card>
        <x-ui.card><form method="POST" action="{{ route('portal.password.update') }}" class="space-y-5">@csrf @method('PATCH')<x-ui.input label="Contraseña actual" name="current_password" type="password" required /><div class="grid gap-5 md:grid-cols-2"><x-ui.input label="Nueva contraseña" name="password" type="password" required /><x-ui.input label="Confirmar contraseña" name="password_confirmation" type="password" required /></div><div class="flex justify-end"><x-ui.button type="submit" variant="secondary">Cambiar contraseña</x-ui.button></div></form></x-ui.card>
    </div>
</x-layouts.portal>
