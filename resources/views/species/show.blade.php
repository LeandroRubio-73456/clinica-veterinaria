<x-layouts.app
    title="Detalle de especie"
    max-width="max-w-6xl"
>
    <x-slot:actions>

        <div class="flex gap-3">

            <x-ui.button
                href="{{ route('species.edit', $species) }}"
                variant="secondary"
            >
                Editar
            </x-ui.button>

            <x-ui.button
                href="{{ route('species.create') }}"
                variant="primary"
            >
                Registrar especie
            </x-ui.button>

        </div>

    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Catálogo de mascotas"
            :title="$species->name"
            description="Información de la especie y mascotas asociadas."
        />

        <div class="grid gap-6 lg:grid-cols-[0.85fr_1.5fr]">

            {{-- Información --}}
            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Información principal
                </x-ui.eyebrow>

                <div class="mt-2 flex items-start justify-between gap-4">

                    <h2 class="font-display text-2xl font-semibold text-ink">
                        {{ $species->name }}
                    </h2>

                    <x-ui.badge
                        :status="$species->state"
                    />

                </div>

                <div class="mt-8 rounded-lg bg-ink p-6">

                    <p class="font-mono text-[11px] uppercase tracking-widest text-white/50">
                        Mascotas registradas
                    </p>

                    <div class="mt-3 flex items-baseline gap-2">

                        <span class="font-display text-4xl font-semibold text-white">
                            {{ $species->pets_count ?? $species->pets->count() }}
                        </span>

                        <span class="text-sm text-white/60">
                            mascotas
                        </span>

                    </div>

                </div>

                <dl class="mt-8 space-y-5">

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Identificador
                        </dt>

                        <dd class="mt-2 font-mono text-sm font-medium text-ink">
                            #{{ $species->id }}
                        </dd>

                    </div>

                    <div class="border-b border-hairline pb-5">

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Estado
                        </dt>

                        <dd class="mt-2">
                            <x-ui.badge
                                :status="$species->state"
                            />
                        </dd>

                    </div>

                    <div>

                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Registrada
                        </dt>

                        <dd class="mt-2 font-mono text-sm text-ink">
                            {{ $species->created_at?->format('d/m/Y H:i') ?? '—' }}
                        </dd>

                    </div>

                </dl>

                <div class="mt-8 border-t border-hairline pt-6">

                    <x-ui.button
                        href="{{ route('species.edit', $species) }}"
                        variant="primary"
                        class="w-full justify-center"
                    >
                        Editar información
                    </x-ui.button>

                </div>

            </x-ui.card>

            {{-- Descripción + mascotas --}}
            <x-ui.card>

                <x-ui.eyebrow variant="kicker">
                    Descripción
                </x-ui.eyebrow>

                <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                    Sobre {{ $species->name }}
                </h2>

                <div class="mt-5">

                    @if ($species->description)

                        <p class="whitespace-pre-line text-sm leading-7 text-muted">
                            {{ $species->description }}
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
                        Mascotas asociadas
                    </x-ui.eyebrow>

                    <p class="mt-2 text-sm text-muted">
                        Mascotas registradas con esta especie.
                    </p>

                    <div class="mt-6 overflow-x-auto">

                        <table class="w-full min-w-[600px] text-left">

                            <thead>
                                <tr class="border-b border-hairline">

                                    <th class="pb-3 pr-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Mascota
                                    </th>

                                    <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Raza
                                    </th>

                                    <th class="pb-3 px-4 font-mono text-[11px] uppercase tracking-widest text-muted">
                                        Género
                                    </th>

                                    <th class="pb-3 pl-4 text-right font-mono text-[11px] uppercase tracking-widest text-muted">
                                        ID
                                    </th>

                                </tr>
                            </thead>

                            <tbody class="divide-y divide-hairline">

                                @forelse ($species->pets as $pet)

                                    <tr>

                                        <td class="py-4 pr-4">

                                            <a
                                                href="{{ route('pets.show', $pet) }}"
                                                class="text-sm font-semibold text-ink hover:text-teal"
                                            >
                                                {{ $pet->name }}
                                            </a>

                                        </td>

                                        <td class="px-4 py-4 text-sm text-muted">
                                            {{ $pet->breed ?: 'No registrada' }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-muted">
                                            {{ $pet->gender === 'male' ? 'Macho' : ($pet->gender === 'female' ? 'Hembra' : '—') }}
                                        </td>

                                        <td class="py-4 pl-4 text-right font-mono text-xs text-muted">
                                            #{{ $pet->id }}
                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td
                                            colspan="4"
                                            class="py-10 text-center"
                                        >

                                            <p class="text-sm font-medium text-ink">
                                                No hay mascotas asociadas.
                                            </p>

                                            <p class="mt-1 text-sm text-muted">
                                                Las mascotas registradas con esta especie aparecerán aquí.
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
                        #{{ $species->id }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Registro creado
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $species->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Última actualización
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $species->updated_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

            </div>

        </x-ui.card>

    </div>
</x-layouts.app>