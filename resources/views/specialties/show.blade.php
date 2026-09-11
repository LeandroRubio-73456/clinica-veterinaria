<x-layouts.app
    title="Detalle de especialidad"
    max-width="max-w-6xl"
>
    <x-slot:actions>

        <div class="flex gap-3">

            <x-ui.button
                href="{{ route('specialties.edit', $specialty) }}"
                variant="secondary"
            >
                Editar
            </x-ui.button>

            <x-ui.button
                href="{{ route('specialties.create') }}"
                variant="primary"
            >
                Registrar especialidad
            </x-ui.button>

        </div>

    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Catálogo veterinario"
            :title="$specialty->name"
            description="Información de la especialidad y veterinarios asociados."
        />

        <div class="grid gap-6 lg:grid-cols-[0.85fr_1.5fr]">

            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Información principal
                </x-ui.eyebrow>

                <div class="mt-2 flex items-start justify-between gap-4">

                    <h2 class="font-display text-2xl font-semibold text-ink">
                        {{ $specialty->name }}
                    </h2>

                    <x-ui.badge :status="$specialty->state" />

                </div>

                <div class="mt-8 rounded-lg bg-ink p-6">

                    <p class="font-mono text-[11px] uppercase tracking-widest text-white/50">
                        Veterinarios asociados
                    </p>

                    <div class="mt-3 flex items-baseline gap-2">

                        <span class="font-display text-4xl font-semibold text-white">
                            {{ $specialty->veterinarians_count ?? $specialty->veterinarians->count() }}
                        </span>

                        <span class="text-sm text-white/60">
                            veterinarios
                        </span>

                    </div>

                </div>

                <dl class="mt-8 space-y-5">

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Identificador
                        </dt>

                        <dd class="mt-2 font-mono text-sm font-medium text-ink">
                            #{{ $specialty->id }}
                        </dd>

                    </div>

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Estado
                        </dt>

                        <dd class="mt-2">
                            <x-ui.badge :status="$specialty->state" />
                        </dd>

                    </div>

                    <div>

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Registrada
                        </dt>

                        <dd class="mt-2 font-mono text-sm text-ink">
                            {{ $specialty->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </dd>

                    </div>

                </dl>

                <div class="mt-8 border-t border-hairline pt-6">

                    <x-ui.button
                        href="{{ route('specialties.edit', $specialty) }}"
                        variant="primary"
                        class="w-full justify-center"
                    >
                        Editar información
                    </x-ui.button>

                </div>

            </x-ui.card>

            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Descripción
                </x-ui.eyebrow>

                <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                    Sobre esta especialidad
                </h2>

                <div class="mt-5">

                    @if ($specialty->description)

                        <p class="whitespace-pre-line text-sm leading-7 text-muted">
                            {{ $specialty->description }}
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
                        Veterinarios asociados
                    </x-ui.eyebrow>

                    <p class="mt-2 text-sm text-muted">
                        Veterinarios que tienen asignada esta especialidad.
                    </p>

                    <div class="mt-6 overflow-x-auto">

                        <table class="w-full min-w-[700px] text-left">

                            <thead>
                                <tr class="border-b border-hairline">

                                    <th class="pb-3 pr-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Veterinario
                                    </th>

                                    <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Correo
                                    </th>

                                    <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Teléfono
                                    </th>

                                    <th class="pb-3 pl-4 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Estado
                                    </th>

                                </tr>
                            </thead>

                            <tbody class="divide-y divide-hairline">

                                @forelse ($specialty->veterinarians as $veterinarian)

                                    <tr>

                                        <td class="py-4 pr-4">

                                            <a
                                                href="{{ route('veterinarians.show', $veterinarian) }}"
                                                class="text-sm font-semibold text-ink hover:text-teal"
                                            >
                                                {{ $veterinarian->first_name }}
                                                {{ $veterinarian->last_name }}
                                            </a>

                                        </td>

                                        <td class="px-4 py-4 text-sm text-muted">
                                            {{ $veterinarian->email ?: '—' }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-muted">
                                            {{ $veterinarian->phone ?: '—' }}
                                        </td>

                                        <td class="py-4 pl-4 text-right">

                                            <x-ui.badge
                                                :status="$veterinarian->state"
                                            />

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="4"
                                            class="py-10 text-center"
                                        >

                                            <p class="text-sm font-medium text-ink">
                                                No hay veterinarios asociados.
                                            </p>

                                            <p class="mt-1 text-sm text-muted">
                                                Los veterinarios asignados a esta especialidad aparecerán aquí.
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
                        #{{ $specialty->id }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Registro creado
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $specialty->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Última actualización
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $specialty->updated_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

            </div>

        </x-ui.card>

    </div>
</x-layouts.app>