<x-layouts.app title="Detalle de mascota" max-width="max-w-6xl">
    <x-slot:actions>
        <div class="flex flex-wrap gap-3">
            <x-ui.button href="{{ route('pets.edit', $pet) }}" variant="secondary">
                Editar mascota
            </x-ui.button>

            @if ($pet->state === 'active')
                <x-ui.button href="{{ route('surgeries.create', ['pet_id' => $pet->id]) }}" variant="primary">
                    Programar cirugía
                </x-ui.button>
            @endif

            <x-ui.button href="{{ route('pets.create') }}" variant="primary">
                Registrar mascota
            </x-ui.button>
        </div>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Ficha de mascota" :title="$pet->name"
            description="Información básica de identificación y propietario." />

        <div class="grid gap-6 lg:grid-cols-[1fr_1.1fr]">

            {{-- Datos de la mascota --}}
            <x-ui.card>

                <div class="flex items-start justify-between gap-4">
                    <div>
                        <x-ui.eyebrow variant="kicker">
                            Información de mascota
                        </x-ui.eyebrow>

                        <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                            {{ $pet->name }}
                        </h2>
                    </div>

                    <span class="font-mono text-xs text-muted">
                        #{{ $pet->id }}
                    </span>
                </div>

                <dl class="mt-8 space-y-5">

                    <div class="border-b border-hairline pb-5">
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Especie
                        </dt>

                        <dd class="mt-2 text-sm font-medium text-ink">
                            {{ $pet->species?->name ?: 'No registrada' }}
                        </dd>
                    </div>

                    <div class="border-b border-hairline pb-5">
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Raza
                        </dt>

                        <dd class="mt-2 text-sm font-medium text-ink">
                            {{ $pet->breed ?: 'No registrada' }}
                        </dd>
                    </div>

                    <div class="grid grid-cols-2 gap-5 border-b border-hairline pb-5">

                        <div>
                            <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                                Edad
                            </dt>

                            <dd class="mt-2 font-mono text-sm font-medium text-ink">
                                {{ $pet->age !== null ? $pet->age . ' meses' : '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                                Peso
                            </dt>

                            <dd class="mt-2 font-mono text-sm font-medium text-ink">
                                {{ $pet->weight !== null ? number_format($pet->weight, 2) . ' kg' : '—' }}
                            </dd>
                        </div>

                    </div>

                    <div class="border-b border-hairline pb-5">
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Sexo
                        </dt>

                        <dd class="mt-2 text-sm font-medium text-ink">
                            @switch($pet->gender)
                                @case('male')
                                    Macho
                                @break

                                @case('female')
                                    Hembra
                                @break

                                @default
                                    No registrado
                            @endswitch
                        </dd>
                    </div>

                    <div>

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Estado
                        </dt>

                        <dd class="mt-2">
                            <x-ui.badge :status="$pet->state" />
                        </dd>

                    </div>

                </dl>

                <div class="mt-8 border-t border-hairline pt-6">
                    <x-ui.button href="{{ route('pets.edit', $pet) }}" variant="primary" class="w-full justify-center">
                        Editar información
                    </x-ui.button>
                </div>
            </x-ui.card>

            {{-- Dueño --}}
            <x-ui.card>

                <div>
                    <x-ui.eyebrow variant="kicker">
                        Propietario
                    </x-ui.eyebrow>

                    <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                        Dueño de la mascota
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        Información del propietario asociado al registro.
                    </p>
                </div>

                @if ($pet->owner)
                    <div class="mt-6 rounded-lg border border-hairline bg-base p-6">

                        <p class="font-display text-lg font-semibold text-ink">
                            {{ $pet->owner->first_name }}
                            {{ $pet->owner->last_name }}
                        </p>

                        <dl class="mt-5 space-y-4">

                            <div>
                                <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Teléfono
                                </dt>

                                <dd class="mt-1 text-sm text-ink">
                                    {{ $pet->owner->phone ?: 'No registrado' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Correo
                                </dt>

                                <dd class="mt-1 break-all text-sm text-ink">
                                    {{ $pet->owner->email ?: 'No registrado' }}
                                </dd>
                            </div>

                            <div>
                                <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Dirección
                                </dt>

                                <dd class="mt-1 text-sm text-ink">
                                    {{ $pet->owner->address ?: 'No registrada' }}
                                </dd>
                            </div>

                        </dl>

                        <div class="mt-6">
                            <x-ui.button href="{{ route('owners.show', $pet->owner) }}" variant="secondary"
                                class="w-full justify-center">
                                Ver dueño
                            </x-ui.button>
                        </div>
                    </div>
                @else
                    <div class="mt-6 rounded-md border border-alert/20 bg-alert/5 px-4 py-3">
                        <p class="text-sm font-semibold text-alert">
                            Dueño no disponible.
                        </p>

                        <p class="mt-1 text-sm text-muted">
                            La relación con el propietario no está disponible para este registro.
                        </p>
                    </div>
                @endif
            </x-ui.card>
        </div>

        {{-- Cirugías --}}
        <x-ui.card>

            <div class="flex items-start justify-between gap-4">
                <div>
                    <x-ui.eyebrow variant="kicker">
                        Historial de cirugías
                    </x-ui.eyebrow>

                    <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                        Cirugías de la mascota
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        Procedimientos quirúrgicos registrados para esta mascota.
                    </p>
                </div>

                <span class="font-mono text-sm text-ink">
                    {{ $pet->surgeries_count ?? $pet->surgeries->count() }}
                </span>
            </div>

            <div class="mt-6 overflow-x-auto">
                <table class="w-full min-w-[760px] text-left">
                    <thead>
                        <tr class="border-b border-hairline">
                            <th class="pb-3 pr-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Fecha
                            </th>

                            <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Cirugía
                            </th>

                            <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Veterinario
                            </th>

                            <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                Quirófano
                            </th>

                            <th class="pb-3 pl-4 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                Estado
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-hairline">
                        @forelse ($pet->surgeries as $surgery)
                            <tr>
                                <td class="py-4 pr-4">
                                    <p class="font-mono text-sm font-medium text-ink">
                                        {{ $surgery->scheduled_date ?? '—' }}
                                    </p>

                                    <p class="mt-1 font-mono text-xs text-muted">
                                        {{ $surgery->start_time ?? '--:--' }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 text-sm text-muted">
                                    {{ $surgery->surgeryType?->name ?? '—' }}
                                </td>

                                <td class="px-4 py-4 text-sm text-muted">
                                    {{ $surgery->veterinarian?->first_name . ' ' . $surgery->veterinarian?->last_name ?? '—' }}
                                </td>

                                <td class="px-4 py-4 text-sm text-muted">
                                    {{ $surgery->operatingRoom?->name ?? '—' }}
                                </td>

                                <td class="py-4 pl-4 text-right">
                                    <x-ui.badge :status="$surgery->state" />
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-10 text-center">
                                    <p class="text-sm font-medium text-ink">
                                        No hay cirugías registradas.
                                    </p>

                                    <p class="mt-1 text-sm text-muted">
                                        Las cirugías de esta mascota aparecerán aquí cuando sean programadas.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </x-ui.card>

        {{-- Metadatos --}}
        <x-ui.card>
            <div class="grid gap-5 md:grid-cols-3">

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        ID
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        #{{ $pet->id }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Registro creado
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $pet->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Última actualización
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $pet->updated_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

            </div>
        </x-ui.card>

    </div>
</x-layouts.app>
