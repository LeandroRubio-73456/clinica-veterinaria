<x-layouts.app title="Detalle de cirugía" max-width="max-w-6xl">
    <x-slot:actions>

        <div class="flex flex-wrap items-center gap-3">

            @if ($surgery->state === 'scheduled')
                @can('update', $surgery)
                    <x-ui.button href="{{ route('surgeries.edit', $surgery) }}" variant="secondary">
                        Editar
                    </x-ui.button>
                @endcan
                @can('start', $surgery)
                    <form method="POST" action="{{ route('surgeries.start', $surgery) }}" class="inline" data-confirm-title="Iniciar cirugía" data-confirm-message="La cirugía pasará a estado en curso y el quirófano quedará ocupado. ¿Deseas continuar?" data-confirm-label="Iniciar cirugía">
                        @csrf

                        <x-ui.button type="submit" variant="primary">
                            Iniciar cirugía
                        </x-ui.button>
                    </form>
                @endcan
            @elseif ($surgery->state === 'in_progress')
                @can('complete', $surgery)
                    <form method="POST" action="{{ route('surgeries.complete', $surgery) }}" class="inline" data-confirm-title="Finalizar cirugía" data-confirm-message="Se registrará la hora de finalización y el quirófano volverá a estar disponible. ¿Deseas continuar?" data-confirm-label="Finalizar cirugía">
                        @csrf

                        <x-ui.button type="submit" variant="primary">
                            Finalizar cirugía
                        </x-ui.button>
                    </form>
                @endcan
            @endif

            @if ($surgery->state === 'scheduled')
                @can('cancel', $surgery)
                    <form method="POST" action="{{ route('surgeries.cancel', $surgery) }}" class="inline border-x px-2" data-confirm-title="Cancelar cirugía" data-confirm-message="La cirugía se marcará como cancelada y liberará el quirófano. Esta acción no agenda una nueva fecha. ¿Deseas continuar?" data-confirm-label="Cancelar cirugía">
                        @csrf
                        <input name="notes" required maxlength="1000" placeholder="Motivo de cancelación" aria-label="Motivo de cancelación" class="w-48 rounded-md border border-hairline px-2 text-xs">

                        <x-ui.button type="submit" variant="danger">
                            Cancelar cirugía
                        </x-ui.button>
                    </form>
                @endcan

                @can('noShow', $surgery)
                    <form method="POST" action="{{ route('surgeries.no-show', $surgery) }}" class="inline border-x px-2" data-confirm-title="Marcar como no presentada" data-confirm-message="La cirugía se marcará como no presentada y el quirófano quedará disponible. ¿Deseas continuar?" data-confirm-label="Marcar no presentada">
                        @csrf
                        <input name="notes" required maxlength="1000" placeholder="Motivo de no presentación" aria-label="Motivo de no presentación" class="w-48 rounded-md border border-hairline px-2 text-xs">

                        <x-ui.button type="submit" variant="warning">
                            No se presentó
                        </x-ui.button>
                    </form>
                @endcan
            @endif

            <x-ui.button href="{{ route('surgeries.index') }}" variant="secondary">
                Volver
            </x-ui.button>

        </div>

    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Gestión quirúrgica" title="Detalle de cirugía"
            description="Consulta la información, programación y estado actual de la cirugía." />

        {{-- Estado y programación --}}
        <x-ui.card>

            <div class="flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">

                <div>

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Cirugía #{{ $surgery->id }}
                    </p>

                    <h2 class="mt-2 font-display text-2xl font-semibold text-ink">
                        {{ $surgery->surgeryType?->name ?? 'Cirugía' }}
                    </h2>

                    <p class="mt-2 text-sm text-muted">
                        {{ $surgery->pet?->name ?? 'Mascota no disponible' }}
                    </p>

                </div>

                <x-ui.badge :status="$surgery->state" />

            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-3">

                <div class="rounded-lg bg-base p-5">

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Fecha
                    </p>

                    <p class="mt-2 font-mono text-xl font-medium text-ink">
                        {{ $surgery->scheduled_date ?? '—' }}
                    </p>

                </div>

                <div class="rounded-lg bg-base p-5">

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Horario programado
                    </p>

                    <p class="mt-2 font-mono text-xl font-medium text-ink">
                        {{ $surgery->start_time ?? '—' }}
                        —
                        {{ $surgery->end_time ?? '—' }}
                    </p>

                </div>

                <div class="rounded-lg bg-base p-5">

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Duración estimada
                    </p>

                    <p class="mt-2 font-mono text-xl font-medium text-ink">
                        @if ($surgery->surgeryType?->estimated_duration)
                            {{ $surgery->surgeryType->estimated_duration }} min
                        @else
                            —
                        @endif
                    </p>

                </div>

            </div>

        </x-ui.card>


        {{-- Información relacionada --}}
        <div class="grid gap-6 lg:grid-cols-3">

            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Mascota
                </x-ui.eyebrow>

                <div class="mt-4">

                    @if ($surgery->pet)
                        <a href="{{ route('pets.show', $surgery->pet) }}"
                            class="font-display text-xl font-semibold text-ink hover:text-teal">
                            {{ $surgery->pet->name }}
                        </a>
                    @else
                        <p class="text-sm text-muted">
                            Mascota no disponible.
                        </p>
                    @endif

                </div>

            </x-ui.card>


            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Veterinario responsable
                </x-ui.eyebrow>

                <div class="mt-4">

                    @if ($surgery->veterinarian)
                        <a href="{{ route('veterinarians.show', $surgery->veterinarian) }}"
                            class="font-display text-xl font-semibold text-ink hover:text-teal">
                            {{ $surgery->veterinarian->first_name }} {{ $surgery->veterinarian->last_name }}
                        </a>
                    @else
                        <p class="text-sm text-muted">
                            Veterinario no disponible.
                        </p>
                    @endif

                </div>

            </x-ui.card>


            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Quirófano
                </x-ui.eyebrow>

                <div class="mt-4">

                    @if ($surgery->operatingRoom)
                        <p class="font-display text-xl font-semibold text-ink">
                            {{ $surgery->operatingRoom->name }}
                        </p>
                    @else
                        <p class="text-sm text-muted">
                            Quirófano no disponible.
                        </p>
                    @endif

                </div>

            </x-ui.card>

        </div>


        {{-- Tiempos reales --}}
        <x-ui.card>

            <x-ui.eyebrow variant="kicker">
                Ejecución de la cirugía
            </x-ui.eyebrow>

            <div class="mt-6 grid gap-6 md:grid-cols-2">

                <div>

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Inicio real
                    </p>

                    <p class="mt-2 font-mono text-xl font-medium text-ink">
                        {{ $surgery->actual_start_time ?? 'Pendiente' }}
                    </p>

                </div>

                <div>

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Finalización real
                    </p>

                    <p class="mt-2 font-mono text-xl font-medium text-ink">
                        {{ $surgery->actual_end_time ?? 'Pendiente' }}
                    </p>

                </div>

            </div>

            @if ($surgery->state === 'scheduled')
                <div class="mt-8 border-t border-hairline pt-6">

                    <p class="text-sm text-muted">
                        La cirugía aún no ha iniciado. Las acciones para iniciar y finalizar la cirugía se integrarán
                        con el flujo operativo correspondiente.
                    </p>

                </div>
            @elseif ($surgery->state === 'in_progress')
                <div class="mt-8 rounded-md border border-amber/20 bg-amber/5 px-4 py-3">

                    <p class="text-sm font-semibold text-amber">
                        Cirugía en curso
                    </p>

                    <p class="mt-1 text-sm text-muted">
                        La cirugía tiene registrado su inicio real y permanece en ejecución.
                    </p>

                </div>
            @elseif ($surgery->state === 'completed')
                <div class="mt-8 rounded-md border border-success/20 bg-success/5 px-4 py-3">

                    <p class="text-sm font-semibold text-success">
                        Cirugía completada
                    </p>

                    <p class="mt-1 text-sm text-muted">
                        La cirugía tiene registrados sus tiempos reales de ejecución.
                    </p>

                </div>
            @endif

        </x-ui.card>


        {{-- Observaciones --}}
        <x-ui.card>

            <x-ui.eyebrow variant="kicker">
                Observaciones
            </x-ui.eyebrow>

            <div class="mt-5">

                @if ($surgery->notes)
                    <p class="whitespace-pre-line text-sm leading-7 text-muted">
                        {{ $surgery->notes }}
                    </p>
                @else
                    <p class="text-sm text-muted">
                        No hay observaciones registradas.
                    </p>
                @endif

            </div>

        </x-ui.card>


        {{-- Metadatos --}}
        <x-ui.card>

            <div class="grid gap-5 md:grid-cols-3">

                <div>

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Identificador
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        #{{ $surgery->id }}
                    </p>

                </div>

                <div>

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Registrada
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $surgery->created_at ?? '—' }}
                    </p>

                </div>

                <div>

                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Última actualización
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $surgery->updated_at ?? '—' }}
                    </p>

                </div>

            </div>

        </x-ui.card>

    </div>
</x-layouts.app>
