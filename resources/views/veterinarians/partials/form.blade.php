<div class="space-y-8">

    <div>
        <x-ui.eyebrow variant="kicker">
            Información profesional
        </x-ui.eyebrow>

        <div class="mt-5 grid gap-5 md:grid-cols-2">

            <x-ui.input
                label="Cédula"
                name="cedula"
                type="text"
                inputmode="numeric"
                maxlength="10"
                placeholder="0102030405"
                required
                :value="$veterinarian->cedula ?? ''"
                :error="$errors->first('cedula')"
            />

            <x-ui.input
                label="Nombres"
                name="first_name"
                type="text"
                placeholder="Nombres del veterinario"
                autocomplete="first_name"
                required
                :value="$veterinarian->first_name ?? ''"
                :error="$errors->first('first_name')"
            />

            <x-ui.input
                label="Apellidos"
                name="last_name"
                type="text"
                placeholder="Apellidos del veterinario"
                autocomplete="last_name"
                required
                :value="$veterinarian->last_name ?? ''"
                :error="$errors->first('last_name')"
            />

            <x-ui.select
                label="Especialidad"
                name="specialty_id"
                :options="$specialtyOptions"
                :value="$veterinarian->specialty_id ?? ''"
                placeholder="Seleccionar especialidad..."
                required
                :error="$errors->first('specialty_id')"
            />

        </div>
    </div>

    <div class="border-t border-hairline pt-8">

        <x-ui.eyebrow variant="kicker">
            Información de contacto
        </x-ui.eyebrow>

        <div class="mt-5 grid gap-5 md:grid-cols-2">

            <x-ui.input
                label="Correo electrónico"
                name="email"
                type="email"
                placeholder="veterinario@clinica.local"
                autocomplete="email"
                :value="$veterinarian->email ?? ''"
                :error="$errors->first('email')"
            />

            <x-ui.input
                label="Teléfono"
                name="phone"
                type="tel"
                placeholder="0999999999"
                autocomplete="tel"
                :value="$veterinarian->phone ?? ''"
                :error="$errors->first('phone')"
            />

        </div>
    </div>
    
    @if (!isset($hideStateField) || !$hideStateField)
    <div class="border-t border-hairline pt-8">

        <x-ui.eyebrow variant="kicker">
            Estado
        </x-ui.eyebrow>

        <div class="mt-5 max-w-md">

            <x-ui.select
                label="Estado del veterinario"
                name="state"
                :options="$stateOptions"
                :value="$veterinarian->state ?? 'active'"
                placeholder="Seleccionar estado..."
                required
                :error="$errors->first('state')"
            />

        </div>
    </div>
    @endif

</div>
