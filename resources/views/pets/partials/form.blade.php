<div class="space-y-8">

    <div>
        <div class="flex items-center justify-between gap-4">
            <x-ui.eyebrow variant="kicker">
                Identificación
            </x-ui.eyebrow>
        </div>

        <div class="mt-5 grid gap-5 md:grid-cols-2">

            <x-ui.input
                label="Nombre"
                name="name"
                type="text"
                placeholder="Nombre de la mascota"
                required
                :value="$pet->name ?? ''"
                :error="$errors->first('name')"
            />

            <x-ui.select
                label="Dueño"
                name="owner_id"
                :options="$owners"
                :value="$pet->owner_id ?? ''"
                placeholder="Seleccionar dueño..."
                required
                :error="$errors->first('owner_id')"
            />

        </div>
    </div>

    <div class="border-t border-hairline pt-8">
        <x-ui.eyebrow variant="kicker">
            Características
        </x-ui.eyebrow>

        <div class="mt-5 grid gap-5 md:grid-cols-2">

            <x-ui.select
                label="Especie"
                name="species_id"
                :options="$speciesOptions"
                :value="$pet->species_id ?? ''"
                placeholder="Seleccionar especie..."
                required
                :error="$errors->first('species_id')"
            />

            <x-ui.input
                label="Raza"
                name="breed"
                type="text"
                placeholder="Raza de la mascota"
                :value="$pet->breed ?? ''"
                :error="$errors->first('breed')"
            />

            <x-ui.input
                label="Edad"
                name="age"
                type="number"
                min="0"
                placeholder="Edad en meses"
                :value="$pet->age ?? ''"
                :error="$errors->first('age')"
            />

            <x-ui.input
                label="Peso"
                name="weight"
                type="number"
                step="0.01"
                min="0"
                placeholder="Peso en kg"
                :value="$pet->weight ?? ''"
                :error="$errors->first('weight')"
            />

            <x-ui.select
                label="Sexo"
                name="gender"
                :options="$genderOptions"
                :value="$pet->gender ?? ''"
                placeholder="Seleccionar sexo..."
                :error="$errors->first('gender')"
            />
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
                :value="$pet->state ?? 'active'"
                placeholder="Seleccionar estado..."
                :error="$errors->first('state')"
            />
        </div>
    </div>
    @endif
</div>
