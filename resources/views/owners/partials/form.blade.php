<div class="space-y-8">

    <div>
        <x-ui.eyebrow variant="kicker">
            Información personal
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
                :value="$owner->cedula ?? ''"
                :error="$errors->first('cedula')"
            />

            <x-ui.input
                label="Nombres"
                name="first_name"
                type="text"
                placeholder="Nombres del dueño"
                autocomplete="given-name"
                required
                :value="$owner->first_name ?? ''"
                :error="$errors->first('first_name')"
            />

            <x-ui.input
                label="Apellidos"
                name="last_name"
                type="text"
                placeholder="Apellidos del dueño"
                autocomplete="family-name"
                required
                :value="$owner->last_name ?? ''"
                :error="$errors->first('last_name')"
            />
        </div>
    </div>

    <div class="border-t border-hairline pt-8">
        <x-ui.eyebrow variant="kicker">
            Información de contacto
        </x-ui.eyebrow>

        <div class="mt-5 grid gap-5 md:grid-cols-2">
            <x-ui.input
                label="Teléfono"
                name="phone"
                type="tel"
                placeholder="0999999999"
                autocomplete="tel"
                :value="$owner->phone ?? ''"
                :error="$errors->first('phone')"
            />

            <x-ui.input
                label="Correo electrónico"
                name="email"
                type="email"
                placeholder="correo@ejemplo.com"
                autocomplete="email"
                :value="$owner->email ?? ''"
                :error="$errors->first('email')"
            />

            <div class="md:col-span-2">
                <x-ui.input
                    label="Dirección"
                    name="address"
                    type="text"
                    placeholder="Dirección del dueño"
                    autocomplete="street-address"
                    :value="$owner->address ?? ''"
                    :error="$errors->first('address')"
                />
            </div>
        </div>
    </div>

</div>