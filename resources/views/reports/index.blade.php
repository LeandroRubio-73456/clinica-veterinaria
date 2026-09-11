<x-layouts.app title="Reportes" max-width="max-w-7xl">
    <div class="space-y-8">
        <x-ui.section-header
            eyebrow="Eficiencia operativa"
            title="Reportes"
            description="Analiza la actividad quirúrgica de la clínica dentro de un período seleccionado."
        />

        @if ($errors->any())
            <div class="rounded-md border border-alert/20 bg-alert/5 px-4 py-3" role="alert">
                <p class="text-sm font-semibold text-alert">No se pudo consultar el reporte.</p>
                <p class="mt-1 text-sm text-muted">Revisa el rango de fechas seleccionado.</p>
            </div>
        @endif

        <x-ui.card>
            <div class="flex flex-col gap-4 border-b border-hairline pb-5 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-ink">Período del análisis</p>
                    <p class="mt-1 text-sm text-muted">Usa un acceso rápido o define un rango personalizado.</p>
                </div>
                <nav class="flex flex-wrap gap-2" aria-label="Períodos rápidos">
                    @foreach (['day' => 'Hoy', 'week' => 'Esta semana', 'month' => 'Este mes'] as $value => $label)
                        <a href="{{ route('reports.index', ['period' => $value]) }}" @class([
                            'rounded-md border px-3 py-2 text-sm font-semibold transition',
                            'border-teal bg-teal text-white' => $period === $value,
                            'border-hairline bg-surface text-muted hover:bg-base hover:text-ink' => $period !== $value,
                        ])>{{ $label }}</a>
                    @endforeach
                </nav>
            </div>
            <form method="GET" action="{{ route('reports.index') }}" class="grid gap-5 md:grid-cols-[1fr_1fr_auto_auto] md:items-end">
                <input type="hidden" name="period" value="custom">
                <x-ui.input
                    label="Desde"
                    name="from"
                    type="date"
                    :value="$from->toDateString()"
                    required
                    :error="$errors->first('from')"
                />
                <x-ui.input
                    label="Hasta"
                    name="to"
                    type="date"
                    :value="$to->toDateString()"
                    required
                    :error="$errors->first('to')"
                />
                <x-ui.button type="submit" variant="primary">Actualizar reporte</x-ui.button>
                @if (request()->hasAny(['from', 'to']))
                    <x-ui.button href="{{ route('reports.index') }}" variant="secondary">Limpiar filtros</x-ui.button>
                @endif
            </form>
            <div class="mt-5 flex flex-wrap items-center gap-3 border-t border-hairline pt-5">
                <span class="text-sm font-semibold text-ink">Exportar período:</span>
                <a href="{{ route('reports.export.excel', request()->query()) }}" class="rounded-md border border-hairline bg-surface px-3 py-2 text-sm font-semibold text-ink hover:bg-base">Excel (CSV)</a>
                <a href="{{ route('reports.export.pdf', request()->query()) }}" target="_blank" rel="noopener" class="rounded-md border border-hairline bg-surface px-3 py-2 text-sm font-semibold text-ink hover:bg-base">PDF / imprimir</a>
                <span class="text-xs text-muted">Las exportaciones respetan el período seleccionado.</span>
            </div>
        </x-ui.card>

        <section aria-labelledby="report-metrics-heading">
            <h2 id="report-metrics-heading" class="sr-only">Indicadores operativos del período</h2>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-6">
                <x-ui.stat label="Cirugías registradas" :value="$totalSurgeries" value-class="text-ink" />
                <x-ui.stat label="Completadas" :value="$completedSurgeries" value-class="text-success" />
                <x-ui.stat label="Canceladas / no presentadas" :value="$cancelledSurgeries + $noShowSurgeries" value-class="text-alert" />
                <x-ui.stat label="Tasa de finalización" :value="$completionRate . '%'" value-class="text-teal" />
                <x-ui.stat label="Inicio a tiempo" :value="($onTimeStartRate ?? '—') . '%'" value-class="text-teal" />
                <x-ui.stat label="Ocupación de quirófanos" :value="$occupancyRate . '%'" value-class="text-teal" />
            </div>
        </section>

        <x-ui.card>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <x-ui.eyebrow variant="kicker">Control de agenda</x-ui.eyebrow>
                    <h2 class="mt-2 font-display text-xl font-semibold text-ink">Indicadores operativos</h2>
                    <p class="mt-1 text-sm text-muted">Mide la coordinación de recursos y las oportunidades de mejora detectadas durante la programación.</p>
                </div>
                <p class="text-xs text-muted">No representa una línea base histórica de la clínica.</p>
            </div>
            <dl class="mt-6 grid gap-4 sm:grid-cols-3">
                <div class="rounded-md border border-hairline bg-base/40 p-4">
                    <dt class="text-xs text-muted">Intentos rechazados</dt>
                    <dd class="mt-2 font-mono text-2xl font-semibold text-alert">{{ $schedulingConflictCount }}</dd>
                    <p class="mt-1 text-xs text-muted">La agenda evitó guardar un cruce.</p>
                </div>
                <div class="rounded-md border border-hairline bg-base/40 p-4">
                    <dt class="text-xs text-muted">Conflictos de veterinario</dt>
                    <dd class="mt-2 font-mono text-2xl font-semibold text-ink">{{ $veterinarianConflictCount }}</dd>
                    <p class="mt-1 text-xs text-muted">Intentos con el mismo recurso ocupado.</p>
                </div>
                <div class="rounded-md border border-hairline bg-base/40 p-4">
                    <dt class="text-xs text-muted">Conflictos de quirófano</dt>
                    <dd class="mt-2 font-mono text-2xl font-semibold text-ink">{{ $operatingRoomConflictCount }}</dd>
                    <p class="mt-1 text-xs text-muted">Intentos con horario no disponible.</p>
                </div>
            </dl>
            <p class="mt-5 text-xs leading-5 text-muted">La referencia externa de 50 mascotas por día pertenece al planteamiento del problema; no se utiliza aquí como resultado medido ni como línea base real de la clínica.</p>
        </x-ui.card>

        <x-ui.card>
            <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <x-ui.eyebrow variant="kicker">Evaluación de mejora</x-ui.eyebrow>
                    <h2 class="mt-2 font-display text-xl font-semibold text-ink">Comparación con el período anterior</h2>
                </div>
                <p class="text-xs text-muted">{{ $previousFrom->format('d/m/Y') }} — {{ $previousTo->format('d/m/Y') }}</p>
            </div>
            <dl class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-6">
                @foreach ([
                    ['Cirugías registradas', 'total'],
                    ['Tasa de finalización', 'completion'],
                    ['Tasa de cancelación', 'cancellation'],
                    ['Tasa de no presentación', 'no_show'],
                    ['Ocupación de quirófanos', 'occupancy'],
                    ['Intentos rechazados', 'conflicts'],
                ] as [$label, $key])
                    @php($isImprovement = in_array($key, ['cancellation', 'no_show', 'conflicts'], true) ? $comparison[$key] <= 0 : $comparison[$key] >= 0)
                    <div class="rounded-md border border-hairline bg-base/40 p-4">
                        <dt class="text-xs text-muted">{{ $label }}</dt>
                        <dd class="mt-2 font-mono text-lg font-semibold {{ $comparison[$key] === null ? 'text-muted' : ($isImprovement ? 'text-teal-dark' : 'text-alert') }}">
                            {{ $comparison[$key] === null ? 'Sin base' : (($comparison[$key] > 0 ? '+' : '') . $comparison[$key] . '%') }}
                        </dd>
                        <p class="mt-1 text-xs text-muted">variación relativa</p>
                    </div>
                @endforeach
            </dl>
            <p class="mt-5 text-xs leading-5 text-muted">Una mejora no se interpreta igual en todos los indicadores: una mayor finalización y ocupación puede ser positiva; una menor cancelación y no presentación también. La comparación sirve como evidencia del comportamiento del sistema, no como una afirmación causal por sí sola.</p>
        </x-ui.card>

        <section class="grid gap-6 lg:grid-cols-3" aria-label="Resumen operativo">
            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Estados</x-ui.eyebrow>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">Distribución de cirugías</h2>
                <dl class="mt-6 space-y-4">
                    @foreach ([
                        ['Programadas', $scheduledSurgeries, 'scheduled'],
                        ['En curso', $inProgressSurgeries, 'in_progress'],
                        ['Completadas', $completedSurgeries, 'completed'],
                        ['Canceladas', $cancelledSurgeries, 'cancelled'],
                        ['No presentadas', $noShowSurgeries, 'no_show'],
                    ] as [$label, $value, $status])
                        <div class="flex items-center justify-between border-b border-hairline pb-3 last:border-0 last:pb-0">
                            <dt class="flex items-center gap-3 text-sm text-muted"><x-ui.badge :status="$status" /> {{ $label }}</dt>
                            <dd class="font-mono text-sm font-semibold text-ink">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </x-ui.card>

            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Duración</x-ui.eyebrow>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">Tiempo quirúrgico</h2>
                <p class="mt-1 text-sm text-muted">Calculado únicamente con cirugías completadas que tienen hora real de inicio y finalización.</p>
                <div class="mt-8 flex items-end gap-3">
                    <span class="font-mono text-4xl font-semibold text-ink">{{ $averageDuration ?? '—' }}</span>
                    <span class="pb-1 text-sm text-muted">{{ $averageDuration ? 'minutos promedio' : 'sin datos reales' }}</span>
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Espera administrativa</x-ui.eyebrow>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">Tiempo promedio desde registro hasta programación</h2>
                <p class="mt-1 text-sm text-muted">Desde el registro de la cirugía hasta la fecha y hora programadas.</p>
                <div class="mt-8 flex items-end gap-3">
                    <span class="font-mono text-3xl font-semibold text-ink">{{ $averageSchedulingLeadTimeLabel }}</span>
                    <span class="pb-1 text-sm text-muted">promedio</span>
                </div>
                <p class="mt-3 text-xs text-muted">Calculado con {{ $schedulingLeadTimesCount }} {{ $schedulingLeadTimesCount === 1 ? 'registro' : 'registros' }} del período.</p>
            </x-ui.card>
        </section>

        <section aria-labelledby="occupancy-heading">
            <x-ui.card>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <x-ui.eyebrow variant="kicker">Capacidad instalada</x-ui.eyebrow>
                        <h2 id="occupancy-heading" class="mt-2 font-display text-xl font-semibold text-ink">Ocupación de quirófanos</h2>
                        <p class="mt-1 text-sm text-muted">Minutos reales en cirugía ÷ minutos operativos planificados del período.</p>
                    </div>
                    <p class="text-sm text-muted">Horario: <span class="font-semibold text-ink">{{ $operatingStart }}–{{ $operatingEnd }}</span> · {{ $operatingDaysInPeriod }} días operativos</p>
                </div>

                <div class="mt-6 overflow-x-auto">
                    <table class="w-full min-w-[720px] text-left">
                        <caption class="sr-only">Porcentaje de ocupación de cada quirófano</caption>
                        <thead>
                            <tr class="border-b border-hairline">
                                <th scope="col" class="px-4 pb-3 pl-0 font-mono text-[11px] uppercase tracking-widest text-muted">Quirófano</th>
                                <th scope="col" class="px-4 pb-3 font-mono text-[11px] uppercase tracking-widest text-muted">Minutos ocupados</th>
                                <th scope="col" class="px-4 pb-3 font-mono text-[11px] uppercase tracking-widest text-muted">Minutos disponibles</th>
                                <th scope="col" class="px-4 pb-3 font-mono text-[11px] uppercase tracking-widest text-muted">Utilización</th>
                                <th scope="col" class="px-4 pb-3 pr-0 font-mono text-[11px] uppercase tracking-widest text-muted">Cirugías</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-hairline">
                            @forelse ($roomSummary as $row)
                                <tr>
                                    <th scope="row" class="px-4 py-4 pl-0 text-sm font-semibold text-ink">{{ $row['name'] }}</th>
                                    <td class="px-4 py-4 font-mono text-sm text-ink">{{ number_format($row['occupied_minutes']) }} min</td>
                                    <td class="px-4 py-4 font-mono text-sm text-muted">{{ number_format($row['available_minutes']) }} min</td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-2 w-24 overflow-hidden rounded-full bg-hairline" aria-hidden="true"><div class="h-full rounded-full bg-teal" style="width: {{ min(100, $row['occupancy_rate']) }}%"></div></div>
                                            <span class="font-mono text-sm font-semibold text-ink">{{ number_format($row['occupancy_rate'], 1) }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 pr-0 font-mono text-sm text-muted">{{ $row['total'] }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="py-8 text-center text-sm text-muted">No hay quirófanos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <p class="mt-5 text-xs leading-5 text-muted">El cálculo usa únicamente cirugías en curso o completadas con hora real de inicio. Las cirugías programadas, canceladas y no presentadas no cuentan como minutos ocupados.</p>
            </x-ui.card>
        </section>

        <section aria-labelledby="daily-summary-heading">
            <x-ui.card>
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <x-ui.eyebrow variant="kicker">Seguimiento</x-ui.eyebrow>
                        <h2 id="daily-summary-heading" class="mt-2 font-display text-xl font-semibold text-ink">Resumen diario</h2>
                    </div>
                    <p class="font-mono text-xs text-muted">{{ $from->format('d/m/Y') }} — {{ $to->format('d/m/Y') }}</p>
                </div>
                <div class="mt-6 overflow-x-auto">
                    <table class="w-full min-w-[620px] text-left">
                        <caption class="sr-only">Resumen diario de cirugías</caption>
                        <thead><tr class="border-b border-hairline">
                            <th scope="col" class="px-4 pb-3 pl-0 font-mono text-[11px] uppercase tracking-widest text-muted">Fecha</th>
                            <th scope="col" class="px-4 pb-3 font-mono text-[11px] uppercase tracking-widest text-muted">Total</th>
                            <th scope="col" class="px-4 pb-3 pr-0 font-mono text-[11px] uppercase tracking-widest text-muted">Estados registrados</th>
                        </tr></thead>
                        <tbody class="divide-y divide-hairline">
                            @forelse ($dailySummary as $row)
                                <tr>
                                    <td class="px-4 py-4 pl-0 font-mono text-sm text-ink">{{ $row['date']->format('d/m/Y') }}</td>
                                    <td class="px-4 py-4 font-mono text-sm text-ink">{{ $row['total'] }}</td>
                                    <td class="px-4 py-4 pr-0"><div class="flex flex-wrap gap-2">@foreach($row['states'] as $state => $stateData)<a href="{{ route('surgeries.index', ['from' => $row['date']->toDateString(), 'to' => $row['date']->toDateString(), 'state' => $state]) }}" class="inline-flex items-center gap-1 rounded-md border border-hairline px-2.5 py-1.5 text-xs font-semibold text-teal hover:bg-teal-light">{{ ['scheduled'=>'Programadas','in_progress'=>'En curso','completed'=>'Completadas','cancelled'=>'Canceladas','no_show'=>'No presentadas'][$state] ?? ucfirst($state) }}: {{ $stateData['count'] }}</a>@endforeach</div></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="py-8 text-center text-sm text-muted">No hay cirugías en el período seleccionado.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
        </section>

        <section class="grid gap-6 lg:grid-cols-2" aria-label="Uso de recursos">
            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Quirófanos</x-ui.eyebrow>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">Uso por quirófano</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($roomSummary as $row)
                        <div class="flex items-center justify-between border-b border-hairline pb-3 last:border-0 last:pb-0"><span class="text-sm text-ink">{{ $row['name'] }}</span><span class="font-mono text-sm text-muted">{{ $row['occupancy_rate'] }}% · {{ $row['completed'] }}/{{ $row['total'] }} completadas</span></div>
                    @empty
                        <p class="text-sm text-muted">No hay datos de quirófanos para este período.</p>
                    @endforelse
                </div>
            </x-ui.card>

            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Personal</x-ui.eyebrow>
                <h2 class="mt-2 font-display text-xl font-semibold text-ink">Cirugías por veterinario</h2>
                <div class="mt-6 space-y-4">
                    @forelse ($veterinarianSummary as $row)
                        <div class="flex items-center justify-between border-b border-hairline pb-3 last:border-0 last:pb-0"><span class="text-sm text-ink">{{ $row['name'] }}</span><span class="font-mono text-sm text-muted">{{ $row['completed'] }}/{{ $row['total'] }} completadas</span></div>
                    @empty
                        <p class="text-sm text-muted">No hay datos de veterinarios para este período.</p>
                    @endforelse
                </div>
            </x-ui.card>
        </section>
    </div>
</x-layouts.app>
