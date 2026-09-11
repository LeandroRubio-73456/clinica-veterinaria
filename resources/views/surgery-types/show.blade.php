<x-layouts.app title="Detalle del tipo de cirugía" max-width="max-w-6xl">
    <x-slot:actions>

        <div class="flex gap-3">

            <x-ui.button href="{{ route('surgery-types.edit', $surgeryType) }}" variant="secondary">
                Editar
            </x-ui.button>

            <x-ui.button href="{{ route('surgery-types.create') }}" variant="primary">
                Registrar tipo
            </x-ui.button>

        </div>

    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Catálogo quirúrgico" :title="$surgeryType->name"
            description="Información del procedimiento y su duración estimada." />

        <div class="grid gap-6 lg:grid-cols-[0.85fr_1.5fr]">

            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Información principal
                </x-ui.eyebrow>

                <h2 class="mt-2 font-display text-2xl font-semibold text-ink">
                    {{ $surgeryType->name }}
                </h2>

                <div class="mt-8 rounded-lg bg-ink p-6">

                    <p class="font-mono text-[11px] uppercase tracking-widest text-white/50">
                        Duración estimada
                    </p>

                    <div class="mt-3 flex items-baseline gap-2">

                        <span class="font-display text-4xl font-semibold text-white">
                            {{ $surgeryType->estimated_duration }}
                        </span>

                        <span class="text-sm text-white/60">
                            minutos
                        </span>

                    </div>

                    <p class="mt-4 text-sm leading-6 text-white/70">
                        Esta duración puede utilizarse como referencia para la programación de cirugías.
                    </p>

                </div>

                <dl class="mt-8 space-y-5">

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Identificador
                        </dt>

                        <dd class="mt-2 font-mono text-sm font-medium text-ink">
                            #{{ $surgeryType->id }}
                        </dd>

                    </div>

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Cirugías asociadas
                        </dt>

                        <dd class="mt-2 font-mono text-sm font-medium text-ink">
                            {{ $surgeryType->surgeries_count ?? $surgeryType->surgeries->count() }}
                        </dd>

                    </div>

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Estado
                        </dt>

                        <dd class="mt-2">
                            <x-ui.badge :status="$surgeryType->state" />
                        </dd>

                    </div>

                    <div>

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Registrado
                        </dt>

                        <dd class="mt-2 font-mono text-sm text-ink">
                            {{ $surgeryType->created_at ?? '—' }}
                        </dd>

                    </div>

                </dl>

                <div class="mt-8 border-t border-hairline pt-6">

                    <x-ui.button href="{{ route('surgery-types.edit', $surgeryType) }}" variant="primary"
                        class="w-full justify-center">
                        Editar información
                    </x-ui.button>

                </div>

            </x-ui.card>

            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Descripción
                </x-ui.eyebrow>

                <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                    Sobre este procedimiento
                </h2>

                <div class="mt-6">

                    @if ($surgeryType->description)
                        <p class="whitespace-pre-line text-sm leading-7 text-muted">
                            {{ $surgeryType->description }}
                        </p>
                    @else
                        <div class="rounded-md border border-hairline bg-base/50 px-5 py-6">

                            <p class="text-sm font-medium text-ink">
                                No hay una descripción registrada.
                            </p>

                            <p class="mt-1 text-sm text-muted">
                                Puedes agregar una descripción desde la opción de edición.
                            </p>

                        </div>
                    @endif

                </div>

                <div class="mt-8 border-t border-hairline pt-8">

                    <x-ui.eyebrow variant="kicker">
                        Cirugías asociadas
                    </x-ui.eyebrow>

                    <p class="mt-2 text-sm text-muted">
                        Este procedimiento está relacionado con
                        <strong class="font-semibold text-ink">
                            {{ $surgeryType->surgeries_count ?? $surgeryType->surgeries->count() }}
                        </strong>
                        cirugías.
                    </p>

                    <div class="mt-6 overflow-x-auto">

                        <table class="w-full min-w-[650px] text-left">

                            <thead>
                                <tr class="border-b border-hairline">

                                    <th class="pb-3 pr-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Fecha
                                    </th>

                                    <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Mascota
                                    </th>

                                    <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Veterinario
                                    </th>

                                    <th
                                        class="pb-3 pl-4 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Estado
                                    </th>

                                </tr>
                            </thead>

                            <tbody class="divide-y divide-hairline">

                                @forelse ($surgeryType->surgeries as $surgery)
                                    <tr>

                                        <td class="py-4 pr-4 font-mono text-sm text-ink">
                                            {{ $surgery->scheduled_date ?? '—' }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-muted">
                                            {{ $surgery->pet?->name ?? '—' }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-muted">
                                            {{ $surgery->veterinarian?->first_name ?? '—' }} {{ $surgery->veterinarian?->last_name ?? '—' }}
                                        </td>

                                        <td class="py-4 pl-4 text-right">

                                            @if ($surgery->state)
                                                <x-ui.badge :status="$surgery->state" />
                                            @else
                                                <span class="text-sm text-muted">
                                                    —
                                                </span>
                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="4" class="py-10 text-center">

                                            <p class="text-sm font-medium text-ink">
                                                No hay cirugías asociadas.
                                            </p>

                                            <p class="mt-1 text-sm text-muted">
                                                Las cirugías que utilicen este tipo de procedimiento aparecerán aquí.
                                            </p>

                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>

                    </div>

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
                        #{{ $surgeryType->id }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Registro creado
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $surgeryType->created_at ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Última actualización
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $surgeryType->updated_at ?? '—' }}
                    </p>
                </div>

            </div>

        </x-ui.card>

    </div>
</x-layouts.app>
