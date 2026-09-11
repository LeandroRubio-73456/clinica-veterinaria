@php
    $roleOptions = $roleOptions ?? ['admin' => 'Administrador', 'administrativo' => 'Administrativo', 'veterinario' => 'Veterinario'];
@endphp

<div
    class="space-y-8"
    x-data="{ role: @js(old('role', $user->roleProfile?->slug ?? $user->role ?? 'administrativo')) }"
>
    <div>
        <x-ui.eyebrow variant="kicker">Datos de acceso</x-ui.eyebrow>
        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <x-ui.input label="Nombre completo" name="name" type="text" placeholder="Nombre del usuario" autocomplete="name" required :value="$user->name ?? ''" :error="$errors->first('name')" />
            <x-ui.input label="Correo electrónico" name="email" type="email" placeholder="correo@clinica.local" autocomplete="email" required :value="$user->email ?? ''" :error="$errors->first('email')" />
        </div>
    </div>

    <div class="border-t border-hairline pt-8">
        <x-ui.eyebrow variant="kicker">Permisos del sistema</x-ui.eyebrow>
        <div class="mt-5">
        <x-ui.select label="Rol" name="role" :options="$roleOptions" :value="$user->roleProfile?->slug ?? $user->role ?? 'administrativo'" placeholder="Selecciona un rol" required x-model="role" :error="$errors->first('role')" />
        </div>
        <p class="mt-2 text-sm text-muted">El rol determina los módulos y acciones disponibles para esta cuenta.</p>
    </div>

    <div
        class="border-t border-hairline pt-8"
        x-show="role === 'veterinario'"
        x-cloak
    >
        <x-ui.eyebrow variant="kicker">Perfil profesional</x-ui.eyebrow>
        <div class="mt-5">
            <x-ui.select
                label="Veterinario asociado"
                name="veterinarian_id"
                :options="$veterinarianOptions"
                :value="$user->veterinarian?->id"
                placeholder="Selecciona el perfil del veterinario"
                x-bind:required="role === 'veterinario'"
                :error="$errors->first('veterinarian_id')"
            />
        </div>
        <p class="mt-2 text-sm text-muted">
            Esta asociación determina la agenda y las cirugías que podrá consultar y operar este usuario.
        </p>
    </div>

    <div class="border-t border-hairline pt-8">
        <x-ui.eyebrow variant="kicker">Contraseña</x-ui.eyebrow>
        <p class="mt-1 text-sm text-muted">{{ $user->exists ? 'Déjala vacía para conservar la contraseña actual.' : 'Debe tener al menos 8 caracteres.' }}</p>
        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <x-ui.input label="Contraseña" name="password" type="password" autocomplete="new-password" :required="!$user->exists" :error="$errors->first('password')" />
            <x-ui.input label="Confirmar contraseña" name="password_confirmation" type="password" autocomplete="new-password" :required="!$user->exists" :error="$errors->first('password_confirmation')" />
        </div>
    </div>
</div>
