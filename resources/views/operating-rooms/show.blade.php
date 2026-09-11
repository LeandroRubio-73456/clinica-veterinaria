<x-layouts.app
    title="Detalle del quirófano"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <div class="flex gap-3">

            <x-ui.button
                href="{{ route('operating-rooms.edit', $operatingRoom) }}"
                variant="secondary"
            >
                Editar
            </x-ui.button>

            <x-ui.button
                href="{{ route('operating-rooms.create') }}"
                variant="primary"
            >
                Registrar quirófano
            </x-ui.button>

        </div>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Infraestructura quirúrgica"
            :title="$operatingRoom->name"
            :description="$operatingRoom->type ?: 'Tipo no registrado'"
        />

        <div class="grid gap-6 lg:grid-cols-[0.9fr_1.4fr]">

            {{-- Estado --}}
            <x-ui.card>

                <div>
                    <x-ui.eyebrow variant="kicker">
                        Estado actual
                    </x-ui.eyebrow>

                    <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                        {{ $operatingRoom->name }}
                    </h2>
                </div>

                <div class="mt-8 rounded-lg bg-ink p-6">

                    <p class="font-mono text-[11px] uppercase tracking-widest text-white/50">
                        Estado operativo
                    </p>

                    <div class="mt-4">
                        <x-ui.badge
                            :status="$operatingRoom->state"
                        />
                    </div>

                    <p class="mt-5 text-sm leading-6 text-white/70">
                        @switch($operatingRoom->state)

                            @case('available')
                                El quirófano está disponible para programación.
                                @break

                            @case('occupied')
                                El quirófano se encuentra ocupado.
                                @break

                            @case('maintenance')
                                El quirófano está fuera de servicio por mantenimiento.
                                @break

                            @default
                                Estado no disponible.
                        @endswitch
                    </p>

                </div>

                <dl class="mt-8 space-y-5">

                    <div class="border-b border-hairline pb-5">
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Tipo
                        </dt>

                        <dd class="mt-2 text-sm font-medium text-ink">
                            {{ $operatingRoom->type ?: 'No registrado' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Identificador
                        </dt>

                        <dd class="mt-2 font-mono text-sm font-medium text-ink">
                            #{{ $operatingRoom->id }}
                        </dd>
                    </div>

                </dl>

                <div class="mt-8 border-t border-hairline pt-6">

                    <x-ui.button
                        href="{{ route('operating-rooms.edit', $operatingRoom) }}"
                        variant="primary"
                        class="w-full justify-center"
                    >
                        Editar información
                    </x-ui.button>

                </div>

            </x-ui.card>

            {{-- Cirugías --}}
            <x-ui.card>

                <div class="flex items-start justify-between gap-4">

                    <div>
                        <x-ui.eyebrow variant="kicker">
                            Actividad
                        </x-ui.eyebrow>

                        <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                            Cirugías programadas
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            Cirugías asociadas a este quirófano.
                        </p>
                    </div>

                    <span class="font-mono text-sm font-medium text-ink">
                        {{ $operatingRoom->surgeries_count ?? $operatingRoom->surgeries->count() }}
                    </span>

                </div>

                <div class="mt-6 overflow-x-auto">

                    <table class="w-full min-w-[780px] text-left">

                        <thead>
                            <tr class="border-b border-hairline">

                                <th class="pb-3 pr-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Fecha
                                </th>

                                <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Horario
                                </th>

                                <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Mascota
                                </th>

                                <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Veterinario
                                </th>

                                <th class="pb-3 pl-4 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Estado
                                </th>

                            </tr>
                        </thead>

                        <tbody class="divide-y divide-hairline">

                            @forelse ($operatingRoom->surgeries as $surgery)

                                <tr>

                                    <td class="py-4 pr-4">

                                        <p class="font-mono text-sm font-medium text-ink">
                                            {{ $surgery->scheduled_date ?? '—' }}
                                        </p>

                                    </td>

                                    <td class="px-4 py-4">

                                        <p class="font-mono text-sm text-ink">
                                            {{ $surgery->start_time ?? '--:--' }}
                                            —
                                            {{ $surgery->end_time ?? '--:--' }}
                                        </p>

                                    </td>

                                    <td class="px-4 py-4 text-sm text-muted">
                                        {{ $surgery->pet?->name ?? '—' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm text-muted">
                                        {{ $surgery->veterinarian?->first_name . ' ' . $surgery->veterinarian?->last_name ?? '—' }}
                                    </td>

                                    <td class="py-4 pl-4 text-right">
                                        <x-ui.badge
                                            :status="$surgery->state"
                                        />
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="5"
                                        class="py-10 text-center"
                                    >
                                        <p class="text-sm font-medium text-ink">
                                            No hay cirugías asociadas.
                                        </p>

                                        <p class="mt-1 text-sm text-muted">
                                            Las cirugías de este quirófano aparecerán aquí cuando sean programadas.
                                        </p>
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </x-ui.card>

        </div>

        <x-ui.card>
            <div class="grid gap-5 md:grid-cols-3">

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        ID
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        #{{ $operatingRoom->id }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Registro creado
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $operatingRoom->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Última actualización
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $operatingRoom->updated_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

            </div>
        </x-ui.card>

    </div>
</x-layouts.app>