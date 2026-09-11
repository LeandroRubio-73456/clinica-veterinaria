<div class="space-y-8">

    <div>
        <x-ui.eyebrow variant="kicker">
            Información del procedimiento
        </x-ui.eyebrow>

        <div class="mt-5 space-y-5">

            <x-ui.input
                label="Nombre"
                name="name"
                type="text"
                placeholder="Ej. Esterilización"
                required
                :value="$surgeryType->name ?? ''"
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
                    placeholder="Describe brevemente el procedimiento..."
                    class="mt-2 block w-full rounded-md border border-hairline bg-surface px-4 py-3 text-sm text-ink outline-none placeholder:text-muted focus:border-teal focus:ring-2 focus:ring-teal/10"
                >{{ old('description', $surgeryType->description ?? '') }}</textarea>

                @error('description')
                    <p class="mt-2 text-sm text-alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>
    </div>

    <div class="border-t border-hairline pt-8">

        <x-ui.eyebrow variant="kicker">
            Programación
        </x-ui.eyebrow>

        <div class="mt-5 max-w-sm">

            <x-ui.input
                label="Duración estimada"
                name="estimated_duration"
                type="number"
                min="1"
                placeholder="Ej. 60"
                required
                :value="$surgeryType->estimated_duration ?? ''"
                :error="$errors->first('estimated_duration')"
            />

            <p class="mt-2 text-xs text-muted">
                Duración estimada del procedimiento en minutos.
            </p>

        </div>
    </div>
    @if (!isset($hideStateField) || !$hideStateField)
    <div class="border-t border-hairline pt-8">
        <x-ui.eyebrow variant="kicker">
            Estado
        </x-ui.eyebrow>

        <div class="mt-5 grid gap-5 md:grid-cols-2">

            <x-ui.select
                label="Estado"
                name="state"
                :options="$stateOptions"
                :value="$surgeryType->state ?? 'active'"
                placeholder="Seleccionar estado..."
                :error="$errors->first('state')"
            />
        </div>
    </div>
    @endif
</div>
