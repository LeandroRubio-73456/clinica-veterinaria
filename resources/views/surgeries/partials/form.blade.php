<div class="space-y-8">

    {{-- Información de la cirugía --}}
    <div>

        <x-ui.eyebrow variant="kicker">
            Información de la cirugía
        </x-ui.eyebrow>

        <div
            x-data="{
                ownerId: '',
                petId: '',
                pets: {},
                init() {
                    this.pets = JSON.parse(this.$el.dataset.pets || '{}');
                    this.petId = this.$el.dataset.initialPetId || '';

                    if (this.petId) {
                        this.ownerId = Object.keys(this.pets).find((ownerId) =>
                            this.pets[ownerId].some((pet) => String(pet.id) === String(this.petId))
                        ) ?? '';
                    }
                },
                get filteredPets() {
                    return this.pets[this.ownerId] ?? [];
                },
                onOwnerChange() {
                    if (!this.filteredPets.some((pet) => String(pet.id) === String(this.petId))) {
                        this.petId = '';
                    }
                },
            }"
            data-pets='@json($petsByOwner ?? [])'
            data-initial-pet-id="{{ old('pet_id', $surgery->pet_id ?? '') }}"
            class="mt-5 grid gap-5 md:grid-cols-2"
        >

            <div class="space-y-2">
                <label for="owner_id" class="block text-sm font-semibold text-ink">
                    Dueño
                </label>

                <select
                    id="owner_id"
                    x-model="ownerId"
                    @change="onOwnerChange()"
                    class="w-full rounded-md border border-hairline bg-surface px-4 py-3 text-sm font-body text-ink outline-none transition focus:border-teal focus:ring-2 focus:ring-teal/10"
                >
                    <option value="">Seleccionar dueño...</option>
                    @foreach ($ownerOptions ?? [] as $ownerId => $ownerLabel)
                        <option value="{{ $ownerId }}">{{ $ownerLabel }}</option>
                    @endforeach
                </select>

                <p class="text-xs text-muted">
                    Busca al dueño por su cédula o nombre para filtrar sus mascotas.
                </p>
            </div>

            <div class="space-y-2">
                <label for="pet_id" class="block text-sm font-semibold text-ink">
                    Mascota
                    <span class="text-alert" aria-hidden="true">*</span>
                </label>

                <select
                    id="pet_id"
                    name="pet_id"
                    :disabled="!ownerId"
                    required
                    @change="petId = $event.target.value"
                    class="w-full rounded-md border border-hairline bg-surface px-4 py-3 text-sm font-body text-ink outline-none transition focus:border-teal focus:ring-2 focus:ring-teal/10 disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <option value="" :selected="!petId">Seleccionar mascota...</option>
                    <template x-for="pet in filteredPets" :key="pet.id">
                        <option :value="pet.id" :selected="String(pet.id) === String(petId)" x-text="pet.label"></option>
                    </template>
                </select>

                <p class="text-xs text-muted" x-show="!ownerId">
                    Selecciona primero un dueño para ver sus mascotas.
                </p>

                @error('pet_id')
                    <p class="text-sm text-alert">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

        <div class="mt-5 max-w-sm">

            <x-ui.select
                label="Tipo de cirugía"
                name="surgery_type_id"
                :options="$surgeryTypeOptions"
                :value="old('surgery_type_id', $surgery->surgery_type_id ?? '')"
                placeholder="Seleccionar tipo de cirugía..."
                required
                :error="$errors->first('surgery_type_id')"
            />

        </div>

    </div>


    {{-- Recursos --}}
    <div class="border-t border-hairline pt-8">

        <x-ui.eyebrow variant="kicker">
            Recursos asignados
        </x-ui.eyebrow>

        <div class="mt-5 grid gap-5 md:grid-cols-2">

            <x-ui.select
                label="Veterinario responsable"
                name="veterinarian_id"
                :options="$veterinarianOptions"
                :value="old('veterinarian_id', $surgery->veterinarian_id ?? '')"
                placeholder="Seleccionar veterinario..."
                required
                :error="$errors->first('veterinarian_id')"
            />

            <x-ui.select
                label="Quirófano"
                name="operating_room_id"
                :options="$operatingRoomOptions"
                :value="old('operating_room_id', $surgery->operating_room_id ?? '')"
                placeholder="Seleccionar quirófano..."
                required
                :error="$errors->first('operating_room_id')"
            />

        </div>

    </div>


    {{-- Programación --}}
    <div class="border-t border-hairline pt-8">

        <x-ui.eyebrow variant="kicker">
            Programación
        </x-ui.eyebrow>

        <div class="mt-5 grid gap-5 md:grid-cols-3">

            <x-ui.input
                label="Fecha programada"
                name="scheduled_date"
                type="date"
                :value="old('scheduled_date', isset($surgery->scheduled_date) ? $surgery->scheduled_date : '')"
                required
                :error="$errors->first('scheduled_date')"
            />

            <x-ui.input
                label="Hora de inicio"
                name="start_time"
                type="time"
                :value="old('start_time', isset($surgery->start_time) ? $surgery->start_time : '')"
                required
                :error="$errors->first('start_time')"
            />

            <x-ui.input
                label="Hora de finalización"
                name="end_time"
                type="time"
                :value="old('end_time', isset($surgery->end_time) ? $surgery->end_time : '')"
                required
                :error="$errors->first('end_time')"
            />

        </div>

        <p class="mt-3 text-xs text-muted">
            La duración estimada del tipo de cirugía debe utilizarse como referencia para definir el horario.
        </p>

    </div>


    @if (!isset($showScheduler) || $showScheduler)
        <div class="border-t border-hairline pt-8">
            <x-ui.eyebrow variant="kicker">
                Disponibilidad visual
            </x-ui.eyebrow>

            <p class="mt-1 text-sm text-muted">
                Selecciona el veterinario, el quirófano y la fecha para consultar los horarios ocupados. Haz clic o arrastra sobre un espacio libre para completar la programación.
            </p>

            <div
                id="surgery-scheduler"
                data-events-url="{{ route('surgeries.calendar.events') }}"
                data-form-id="{{ $formId ?? 'surgery-create-form' }}"
                data-current-surgery-id="{{ $surgery->exists ? $surgery->id : '' }}"
                data-durations='@json($surgeryTypeDurations ?? [])'
                class="mt-5 min-h-[620px] rounded-lg border border-hairline bg-surface p-3 sm:p-5"
            ></div>

            <p id="surgery-scheduler-message" class="mt-3 text-sm text-muted" aria-live="polite">
                Selecciona recursos para consultar la disponibilidad.
            </p>
        </div>
    @endif

    {{-- Estado --}}
    @if (isset($showStateField) && $showStateField)

        <div class="border-t border-hairline pt-8">

            <x-ui.eyebrow variant="kicker">
                Estado
            </x-ui.eyebrow>

            <div class="mt-5 max-w-sm">

                <x-ui.select
                    label="Estado de la cirugía"
                    name="state"
                    :options="$stateOptions"
                    :value="old('state', $surgery->state ?? 'scheduled')"
                    required
                    :error="$errors->first('state')"
                />

            </div>

        </div>

    @endif


    {{-- Observaciones --}}
    <div class="border-t border-hairline pt-8">

        <x-ui.eyebrow variant="kicker">
            Observaciones
        </x-ui.eyebrow>

        <div class="mt-5">

            <label
                for="notes"
                class="block text-sm font-medium text-ink"
            >
                Notas
            </label>

            <textarea
                id="notes"
                name="notes"
                rows="5"
                placeholder="Observaciones relacionadas con la cirugía..."
                class="mt-2 block w-full rounded-md border border-hairline bg-surface px-4 py-3 text-sm text-ink outline-none placeholder:text-muted focus:border-teal focus:ring-2 focus:ring-teal/10"
            >{{ old('notes', $surgery->notes ?? '') }}</textarea>

            @error('notes')
                <p class="mt-2 text-sm text-alert">
                    {{ $message }}
                </p>
            @enderror

        </div>

    </div>

</div>
