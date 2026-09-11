<div class="space-y-8">

    <div>

        <x-ui.eyebrow variant="kicker">
            Información de la especie
        </x-ui.eyebrow>

        <div class="mt-5 space-y-5">

            <x-ui.input
                label="Nombre"
                name="name"
                type="text"
                placeholder="Ej. Perro"
                required
                :value="$species->name ?? ''"
                :error="$errors->first('name')"
            />

            <div>

                <label
                    for="description"
                    class="block text-sm font-medium text-ink"
                >
                    Descripción
                </label>

                <textarea
                    id="description"
                    name="description"
                    rows="5"
                    placeholder="Describe la especie..."
                    class="mt-2 block w-full rounded-md border border-hairline bg-surface px-4 py-3 text-sm text-ink outline-none placeholder:text-muted focus:border-teal focus:ring-2 focus:ring-teal/10"
                >{{ old('description', $species->description ?? '') }}</textarea>

                @error('description')
                    <p class="mt-2 text-sm text-alert">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

    </div>

    @if (!isset($hideStateField) || !$hideStateField)
    <div class="border-t border-hairline pt-8">

        <x-ui.eyebrow variant="kicker">
            Estado del catálogo
        </x-ui.eyebrow>

        <div class="mt-5 max-w-sm">

            <x-ui.select
                label="Estado"
                name="state"
                :options="$stateOptions"
                :value="$species->state ?? 'active'"
                placeholder="Seleccionar estado..."
                required
                :error="$errors->first('state')"
            />

            <p class="mt-2 text-xs text-muted">
                Las especies inactivas no deberían aparecer como opción para nuevos registros de mascotas.
            </p>

        </div>

    </div>
    @endif

</div>
