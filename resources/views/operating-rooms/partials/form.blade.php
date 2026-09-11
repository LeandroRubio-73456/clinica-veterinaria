<div class="space-y-8">

    <div>
        <x-ui.eyebrow variant="kicker">
            Información del quirófano
        </x-ui.eyebrow>

        <div class="mt-5 grid gap-5 md:grid-cols-2">

            <x-ui.input
                label="Nombre"
                name="name"
                type="text"
                placeholder="Ej. Quirófano A"
                required
                :value="$operatingRoom->name ?? ''"
                :error="$errors->first('name')"
            />

            <x-ui.input
                label="Tipo"
                name="type"
                type="text"
                placeholder="Ej. general, especializado"
                :value="$operatingRoom->type ?? ''"
                :error="$errors->first('type')"
            />

        </div>
    </div>

    @if (!isset($hideStateField) || !$hideStateField)
    <div class="border-t border-hairline pt-8">

        <x-ui.eyebrow variant="kicker">
            Disponibilidad
        </x-ui.eyebrow>

        <div class="mt-5 max-w-md">

            <x-ui.select
                label="Estado"
                name="state"
                :options="$stateOptions"
                :value="$operatingRoom->state ?? 'available'"
                placeholder="Seleccionar estado..."
                required
                :error="$errors->first('state')"
            />

        </div>
    </div>
    @endif

</div>
