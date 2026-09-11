<x-layouts.app title="Detalle del veterinario" max-width="max-w-6xl">
    <x-slot:actions>
        <div class="flex gap-3">

            <x-ui.button href="{{ route('veterinarians.edit', $veterinarian) }}" variant="secondary">
                Editar
            </x-ui.button>

            <x-ui.button href="{{ route('veterinarians.create') }}" variant="primary">
                Registrar veterinario
            </x-ui.button>

        </div>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header eyebrow="Ficha profesional" :title="$veterinarian->first_name . ' ' . $veterinarian->last_name" :description="$veterinarian->specialty?->name ?? 'Especialidad no registrada'" />

        <div class="grid gap-6 lg:grid-cols-[1fr_1.2fr]">

            {{-- Información --}}
            <x-ui.card>

                <div class="flex items-start justify-between gap-4">

                    <div>
                        <x-ui.eyebrow variant="kicker">
                            Información del veterinario
                        </x-ui.eyebrow>

                        <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                            {{ $veterinarian->first_name }} {{ $veterinarian->last_name }}
                        </h2>
                    </div>

                    <x-ui.badge :status="$veterinarian->state" />

                </div>

                <dl class="mt-8 space-y-5">

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Cédula
                        </dt>

                        <dd class="mt-2 font-mono text-sm font-medium text-ink">
                            {{ $veterinarian->cedula ?: 'No registrada' }}
                        </dd>

                    </div>

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Especialidad
                        </dt>

                        <dd class="mt-2 text-sm font-medium text-ink">
                            {{ $veterinarian->specialty?->name ?: 'No registrada' }}
                        </dd>

                    </div>

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Correo electrónico
                        </dt>

                        <dd class="mt-2 break-all text-sm font-medium text-ink">
                            {{ $veterinarian->email ?: 'No registrado' }}
                        </dd>

                    </div>

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Teléfono
                        </dt>

                        <dd class="mt-2 text-sm font-medium text-ink">
                            {{ $veterinarian->phone ?: 'No registrado' }}
                        </dd>

                    </div>

                    <div>

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Estado
                        </dt>

                        <dd class="mt-2">
                            <x-ui.badge :status="$veterinarian->state" />
                        </dd>

                    </div>

                </dl>

                <div class="mt-8 border-t border-hairline pt-6">

                    <x-ui.button href="{{ route('veterinarians.edit', $veterinarian) }}" variant="primary"
                        class="w-full justify-center">
                        Editar información
                    </x-ui.button>

                </div>

            </x-ui.card>

            {{-- Cirugías --}}
            <x-ui.card>

                <div class="flex items-start justify-between gap-4">

                    <div>
                        <x-ui.eyebrow variant="kicker">
                            Actividad quirúrgica
                        </x-ui.eyebrow>

                        <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                            Cirugías asignadas
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            Cirugías en las que este veterinario figura como cirujano principal.
                        </p>
                    </div>

                    <span class="font-mono text-sm font-medium text-ink">
                        {{ $veterinarian->surgeries_count ?? $veterinarian->surgeries->count() }}
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
                                    Mascota
                                </th>

                                <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                    Cirugía
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

                            @forelse ($veterinarian->surgeries as $surgery)
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
                                        {{ $surgery->pet?->name ?? '—' }}
                                    </td>

                                    <td class="px-4 py-4 text-sm text-muted">
                                        {{ $surgery->surgeryType?->name ?? '—' }}
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
                                            No hay cirugías asignadas.
                                        </p>

                                        <p class="mt-1 text-sm text-muted">
                                            Las cirugías de este veterinario aparecerán aquí cuando sean programadas.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </x-ui.card>

        </div>

        {{-- Metadatos --}}
        <x-ui.card>

            <div class="grid gap-5 md:grid-cols-3">

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        ID
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        #{{ $veterinarian->id }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Registro creado
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $veterinarian->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Última actualización
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $veterinarian->updated_at?->format('d/m/Y H  :i') ?? '—' }}
                    </p>
                </div>

            </div>

        </x-ui.card>

    </div>
</x-layouts.app>
