@php
    $user = auth()->user();
@endphp
<x-layouts.app title="Dashboard" max-width="max-w-7xl">
    <x-slot:actions>
        @can('create', App\Models\Surgery::class)
            <x-ui.button href="{{ route('surgeries.create') }}" variant="primary">
                Programar cirugía
            </x-ui.button>
        @endcan
        <x-ui.button href="{{ route('surgeries.calendar') }}" variant="secondary">
            Ver agenda
        </x-ui.button>
    </x-slot:actions>

        <div class="space-y-8" data-live-dashboard data-live-url="{{ route('dashboard.live', ['period' => $period ?? 'day']) }}">

        {{-- Encabezado --}}
        <x-ui.section-header eyebrow="Resumen operativo" title="Dashboard"
            description="Indicadores para supervisar la operación y tomar decisiones sobre los recursos de la clínica." />

        <p class="text-xl text-muted">Bienvenido, <span class="font-semibold text-ink">{{ $user->name }}</span>. Aquí puedes consultar el estado actual de la operación.</p>

        <nav aria-label="Período del dashboard" class="flex w-fit flex-wrap gap-2 rounded-md border border-hairline bg-surface p-2">
            @foreach(['day' => 'Diario', 'week' => 'Semanal', 'month' => 'Mensual'] as $value => $label)
                <a href="{{ route('dashboard', ['period' => $value]) }}" @class(['rounded-md px-4 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-teal', 'bg-teal text-white' => ($period ?? 'day') === $value, 'text-muted hover:bg-base hover:text-ink' => ($period ?? 'day') !== $value])>{{ $label }}</a>
            @endforeach
        </nav>

        {{-- Métricas principales --}}
        <section aria-label="Resumen operativo" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <x-ui.stat id="dashboard-total" :label="'Cirugías ' . strtolower($periodLabel ?? 'diarias')" :value="$todaySurgeriesCount ?? 0" value-class="text-ink" />

            <x-ui.stat id="dashboard-scheduled" label="Programadas" :value="$scheduledSurgeriesCount ?? 0" value-class="text-teal" />

            <x-ui.stat id="dashboard-in-progress" label="En curso" :value="$inProgressSurgeriesCount ?? 0" value-class="text-amber" />

            <x-ui.stat id="dashboard-completed" label="Completadas" :value="$completedSurgeriesCount ?? 0" value-class="text-success" />
        </section>

        {{-- Indicadores para la toma de decisiones --}}
        <section aria-labelledby="decision-summary-heading" class="grid gap-6 lg:grid-cols-2">
            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <x-ui.eyebrow variant="kicker">Decisiones</x-ui.eyebrow>
                        <h2 id="decision-summary-heading" class="mt-2 font-display text-xl font-semibold text-ink">
                            Eficiencia del período
                        </h2>
                        <p class="mt-1 text-sm text-muted">Señales clave para detectar oportunidades y riesgos operativos.</p>
                    </div>
                    @if ($user->canViewReports())
                        <a href="{{ route('reports.index', ['period' => $period ?? 'day']) }}" class="shrink-0 text-sm font-semibold text-teal hover:text-teal-dark">
                            Ver detalle
                        </a>
                    @endif
                </div>

                <dl class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-md border border-hairline bg-base p-4">
                        <dt class="text-sm text-muted">Tasa de finalización</dt>
                        <dd id="dashboard-completion-rate" class="mt-2 font-mono text-2xl font-semibold text-success">{{ number_format($completionRate ?? 0, 1) }}%</dd>
                    </div>
                    <div class="rounded-md border border-hairline bg-base p-4">
                        <dt class="text-sm text-muted">Inicio a tiempo</dt>
                        <dd class="mt-2 font-mono text-2xl font-semibold text-teal">{{ $onTimeStartRate !== null ? number_format($onTimeStartRate, 1) . '%' : '—' }}</dd>
                    </div>
                    <div class="rounded-md border border-hairline bg-base p-4">
                        <dt class="text-sm text-muted">Canceladas / no presentadas</dt>
                        <dd class="mt-2 font-mono text-2xl font-semibold text-alert">{{ ($cancelledSurgeriesCount ?? 0) + ($noShowSurgeriesCount ?? 0) }}</dd>
                        <p class="mt-1 text-xs text-muted">{{ $cancelledSurgeriesCount ?? 0 }} canceladas · {{ $noShowSurgeriesCount ?? 0 }} no presentadas</p>
                    </div>
                    <div class="rounded-md border border-hairline bg-base p-4">
                        <dt class="text-sm text-muted">Conflictos detectados</dt>
                        <dd class="mt-2 font-mono text-2xl font-semibold text-amber">{{ $schedulingConflictCount ?? 0 }}</dd>
                        <p class="mt-1 text-xs text-muted">Agenda · veterinarios · quirófanos</p>
                    </div>
                </dl>
            </x-ui.card>

            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Capacidad</x-ui.eyebrow>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">Uso de quirófanos</h2>
                <p class="mt-1 text-sm text-muted">Ocupación calculada con el tiempo operativo disponible.</p>

                <div class="mt-5 flex items-end justify-between gap-4 border-b border-hairline pb-4">
                    <span class="text-sm text-muted">Ocupación global</span>
                    <span id="dashboard-occupancy-rate" class="font-mono text-3xl font-semibold text-teal">{{ number_format($occupancyRate ?? 0, 1) }}%</span>
                </div>
                <div class="mt-2 divide-y divide-hairline">
                    @forelse (($roomSummary ?? collect())->sortByDesc('occupancy_rate')->take(4) as $room)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="truncate text-sm text-ink">{{ $room['name'] }}</span>
                            <span class="shrink-0 font-mono text-sm font-semibold text-muted">{{ number_format($room['occupancy_rate'], 1) }}%</span>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-muted">No hay datos de ocupación para este período.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </section>

        <section aria-label="Carga por veterinario y alertas" class="grid gap-6 lg:grid-cols-2">
            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Distribución de trabajo</x-ui.eyebrow>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">Cirugías por veterinario</h2>
                <p class="mt-1 text-sm text-muted">Ayuda a revisar carga de trabajo y resultados del período.</p>
                <div class="mt-5 divide-y divide-hairline">
                    @forelse (($veterinarianSummary ?? collect())->sortByDesc('total')->take(5) as $veterinarian)
                        <div class="flex items-center justify-between gap-4 py-3">
                            <span class="truncate text-sm text-ink">{{ $veterinarian['name'] }}</span>
                            <span class="shrink-0 text-right font-mono text-sm text-muted">{{ $veterinarian['total'] }} total · {{ $veterinarian['completed'] }} completas</span>
                        </div>
                    @empty
                        <p class="py-4 text-sm text-muted">No hay cirugías asignadas en este período.</p>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Seguimiento</x-ui.eyebrow>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">Alertas operativas</h2>
                <p class="mt-1 text-sm text-muted">Aspectos que pueden requerir una acción administrativa.</p>
                <dl class="mt-5 divide-y divide-hairline">
                    <div class="flex items-center justify-between gap-4 py-3"><dt class="text-sm text-muted">Tiempo promedio de cirugía</dt><dd class="font-mono text-sm font-semibold text-ink">{{ $averageDuration !== null ? $averageDuration . ' min' : '—' }}</dd></div>
                    <div class="flex items-center justify-between gap-4 py-3"><dt class="text-sm text-muted">Tiempo medio de programación</dt><dd class="font-mono text-sm font-semibold text-ink">{{ $averageSchedulingLeadTimeLabel ?? '—' }}</dd></div>
                    <div class="flex items-center justify-between gap-4 py-3"><dt class="text-sm text-muted">Conflictos de veterinario</dt><dd class="font-mono text-sm font-semibold text-ink">{{ $veterinarianConflictCount ?? 0 }}</dd></div>
                    <div class="flex items-center justify-between gap-4 py-3"><dt class="text-sm text-muted">Conflictos de quirófano</dt><dd class="font-mono text-sm font-semibold text-ink">{{ $operatingRoomConflictCount ?? 0 }}</dd></div>
                </dl>
            </x-ui.card>
        </section>

        {{-- Estado en vivo --}}
        <section>
            <x-ui.status-panel title="Estado de quirófanos" :timestamp="$roomsTimestamp ?? now()->format('H:i')"
                footer-note="Disponibilidad actual de los quirófanos registrados.">
                @forelse ($operatingRooms ?? [] as $room)
                    <x-ui.status-row :name="$room->name" :meta="$room->type ?? 'Quirófano general'" :status="$room->state" />
                @empty
                    <div class="px-5 py-6 text-sm text-white/60">
                        No hay quirófanos registrados para mostrar.
                    </div>
                @endforelse
            </x-ui.status-panel>
        </section>

        {{-- Agenda + resumen --}}
        <section class="grid gap-6 lg:grid-cols-[1.5fr_1fr]">

            {{-- Próximas cirugías --}}
            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <x-ui.eyebrow variant="kicker">
                            Agenda
                        </x-ui.eyebrow>

                        <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                            Próximas cirugías
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            Cirugías programadas para las próximas horas.
                        </p>
                    </div>

                    <a href="{{ route('surgeries.calendar') }}"
                        class="text-sm font-semibold text-teal hover:text-teal-dark">
                        Ver agenda
                    </a>
                </div>

                <div class="mt-6 divide-y divide-hairline">
                    @forelse ($upcomingSurgeries ?? [] as $surgery)
                        <a href="{{ route('surgeries.show', $surgery) }}"
                            class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0 hover:bg-base/60">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-ink">
                                    {{ $surgery->pet?->name ?? 'Mascota no disponible' }}
                                </p>

                                <p class="mt-1 text-sm text-muted">
                                    {{ $surgery->surgeryType?->name ?? 'Tipo de cirugía no disponible' }}
                                    <span class="text-hairline">·</span>
                                    {{ $surgery->operatingRoom?->name ?? 'Quirófano no asignado' }}
                                </p>

                                <p class="mt-1 text-xs text-muted">
                                    {{ trim(($surgery->veterinarian?->first_name ?? '') . ' ' . ($surgery->veterinarian?->last_name ?? '')) ?:
                                        'Veterinario no asignado' }}
                                </p>
                            </div>

                            <div class="shrink-0 text-right">
                                <p class="font-mono text-sm font-medium text-ink">
                                    {{ $surgery->start_time ?? '--:--' }}
                                </p>

                                <div class="mt-2">
                                    <x-ui.badge :status="$surgery->state" />
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="py-8 text-center">
                            <p class="text-sm font-medium text-ink">
                                No hay cirugías próximas.
                            </p>

                            <p class="mt-1 text-sm text-muted">
                                La agenda aparecerá aquí cuando existan cirugías programadas.
                            </p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>

            {{-- Resumen del día --}}
            <x-ui.card>
                <div>
                    <x-ui.eyebrow variant="kicker">
                        Resumen
                    </x-ui.eyebrow>

                    <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                        Actividad {{ strtolower($periodLabel ?? 'diaria') }}
                    </h2>

                    <p class="mt-1 text-sm text-muted">
                        Estado general del período seleccionado.
                    </p>
                </div>

                <div class="mt-6 space-y-4">

                    <div class="flex items-center justify-between border-b border-hairline pb-4">
                        <span class="text-sm text-muted">
                            Capacidad programada
                        </span>

                        <span class="font-mono text-sm font-semibold text-ink">
                            {{ $todaySurgeriesCount ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-hairline pb-4">
                        <span class="text-sm text-muted">
                            Cirugías pendientes
                        </span>

                        <span class="font-mono text-sm font-semibold text-teal">
                            {{ $pendingSurgeriesCount ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between border-b border-hairline pb-4">
                        <span class="text-sm text-muted">
                            Cirugías completadas
                        </span>

                        <span class="font-mono text-sm font-semibold text-success">
                            {{ $completedSurgeriesCount ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-sm text-muted">
                            Cirugías canceladas
                        </span>

                        <span class="font-mono text-sm font-semibold text-alert">
                            {{ $cancelledSurgeriesCount ?? 0 }}
                        </span>
                    </div>

                </div>
            </x-ui.card>
        </section>

        {{-- Cirugías del día --}}
        <section>
            <x-ui.card>
                <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <x-ui.eyebrow variant="kicker">
                            Resumen {{ $periodLabel ?? 'diario' }}
                        </x-ui.eyebrow>

                        <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                            Cirugías programadas
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                                            Seguimiento de las cirugías previstas para el período seleccionado.
                        </p>
                    </div>

                    <span class="font-mono text-xs uppercase tracking-widest text-muted">
                        {{ ($periodStart ?? now())->format('d/m/Y') }} — {{ ($periodEnd ?? now())->format('d/m/Y') }}
                    </span>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full min-w-[760px] text-left">
                        <thead>
                            <tr class="border-b border-hairline">
                                <th class="pb-3 pr-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Hora
                                </th>

                                <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Mascota
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

                                <th
                                    class="pb-3 pl-4 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Estado
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-hairline">
                            @forelse ($todaySurgeries ?? [] as $surgery)
                                <tr class="group">
                                    <td class="py-4 pr-4 font-mono text-sm font-medium text-ink">
                                        <a href="{{ route('surgeries.show', $surgery) }}" class="rounded text-teal hover:text-teal-dark focus:outline-none focus:ring-2 focus:ring-teal" aria-label="Ver cirugía de {{ $surgery->pet?->name ?? 'mascota' }}">
                                            {{ $surgery->start_time ?? '--:--' }}
                                        </a>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="font-medium text-ink">
                                            <a href="{{ route('surgeries.show', $surgery) }}" class="rounded hover:text-teal-dark focus:outline-none focus:ring-2 focus:ring-teal">
                                                {{ $surgery->pet?->name ?? '—' }}
                                            </a>
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-sm text-muted">
                                            <a href="{{ route('surgeries.show', $surgery) }}" class="rounded hover:text-teal-dark focus:outline-none focus:ring-2 focus:ring-teal">
                                                {{ $surgery->surgeryType?->name ?? '—' }}
                                            </a>
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-sm text-muted">
                                            {{ trim(($surgery->veterinarian?->first_name ?? '') . ' ' . ($surgery->veterinarian?->last_name ?? '')) ?:
                                                'Veterinario no asignado' }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <p class="text-sm text-muted">
                                            {{ $surgery->operatingRoom?->name ?? '—' }}
                                        </p>
                                    </td>

                                    <td class="py-4 pl-4 text-right">
                                        <x-ui.badge :status="$surgery->state" />
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-10 text-center">
                                        <p class="text-sm font-medium text-ink">
                                            No hay cirugías programadas para este período.
                                        </p>

                                        <p class="mt-1 text-sm text-muted">
                                            Programa una cirugía para comenzar a utilizar la agenda.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </section>

        {{-- Acciones rápidas --}}
        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @can('create', App\Models\Surgery::class)
                <x-ui.card interactive>
                    <a href="{{ route('surgeries.create') }}" class="block">
                        <x-ui.icon-box>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M12 5v14M5 12h14" />
                            </svg>
                        </x-ui.icon-box>

                        <h3 class="mt-4 font-display text-lg font-semibold text-ink">
                            Programar cirugía
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-muted">
                            Registrar una nueva cirugía verificando disponibilidad.
                        </p>
                    </a>
                </x-ui.card>
            @endcan
            <x-ui.card interactive>
                <a href="{{ route('surgeries.calendar') }}" class="block">
                    <x-ui.icon-box>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2">
                            <rect x="3" y="4" width="18" height="17" rx="2" />
                            <path d="M8 2v4M16 2v4M3 10h18" />
                        </svg>
                    </x-ui.icon-box>

                    <h3 class="mt-4 font-display text-lg font-semibold text-ink">
                        Consultar agenda
                    </h3>

                    <p class="mt-1 text-sm leading-6 text-muted">
                        Revisar las cirugías programadas y sus horarios.
                    </p>
                </a>
            </x-ui.card>
            @if ($user->canViewReports())
                <x-ui.card interactive>
                    <a href="/reports" class="block">
                        <x-ui.icon-box>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2">
                                <path d="M4 19V5M4 19h16" />
                                <path d="M8 15v-3M12 15V8M16 15V5" />
                            </svg>
                        </x-ui.icon-box>

                        <h3 class="mt-4 font-display text-lg font-semibold text-ink">
                            Ver reportes
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-muted">
                            Consultar indicadores básicos de eficiencia operativa.
                        </p>
                    </a>
                </x-ui.card>
            @endif
        </section>
    </div>
</x-layouts.app>
