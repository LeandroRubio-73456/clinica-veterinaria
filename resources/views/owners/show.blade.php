<x-layouts.app
    title="Detalle del dueño"
    max-width="max-w-6xl"
>
    <x-slot:actions>
        <div class="flex gap-3">
            <x-ui.button
                href="{{ route('owners.edit', $owner) }}"
                variant="secondary"
            >
                Editar
            </x-ui.button>

            <x-ui.button
                href="{{ route('owners.create') }}"
                variant="primary"
            >
                Registrar dueño
            </x-ui.button>
        </div>
    </x-slot:actions>

    <div class="space-y-8">

        <x-ui.section-header
            eyebrow="Ficha del propietario"
            :title="$owner->first_name . ' ' . $owner->last_name"
            description="Información de contacto y mascotas asociadas."
        />

        <div class="grid gap-6 lg:grid-cols-[1fr_1.2fr]">

            {{-- Información --}}
            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <x-ui.eyebrow variant="kicker">
                            Información del dueño
                        </x-ui.eyebrow>

                        <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                            {{ $owner->first_name }} {{ $owner->last_name }}
                        </h2>
                    </div>

                    <span class="font-mono text-xs text-muted">
                        #{{ $owner->id }}
                    </span>
                </div>

                <dl class="mt-8 space-y-5">

                    <div class="border-b border-hairline pb-5">
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Cédula
                        </dt>

                        <dd class="mt-2 font-mono text-sm font-medium text-ink">
                            {{ $owner->cedula ?: 'No registrada' }}
                        </dd>
                    </div>

                    <div class="border-b border-hairline pb-5">
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Teléfono
                        </dt>

                        <dd class="mt-2 text-sm font-medium text-ink">
                            {{ $owner->phone ?: 'No registrado' }}
                        </dd>
                    </div>

                    <div class="border-b border-hairline pb-5">
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Correo electrónico
                        </dt>

                        <dd class="mt-2 break-all text-sm font-medium text-ink">
                            {{ $owner->email ?: 'No registrado' }}
                        </dd>
                    </div>

                    <div>
                        <dt class="font-mono text-[11px] uppercase tracking-widest text-muted">
                            Dirección
                        </dt>

                        <dd class="mt-2 text-sm font-medium text-ink">
                            {{ $owner->address ?: 'No registrada' }}
                        </dd>
                    </div>

                </dl>

                <div class="mt-8 border-t border-hairline pt-6">
                    <x-ui.button
                        href="{{ route('owners.edit', $owner) }}"
                        variant="primary"
                        class="w-full justify-center"
                    >
                        Editar información
                    </x-ui.button>
                </div>
            </x-ui.card>

            {{-- Mascotas --}}
            <x-ui.card>
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <x-ui.eyebrow variant="kicker">
                            Relación
                        </x-ui.eyebrow>

                        <h2 class="mt-2 font-display text-xl font-semibold text-ink">
                            Mascotas
                        </h2>

                        <p class="mt-1 text-sm text-muted">
                            Mascotas registradas a nombre de este dueño.
                        </p>
                    </div>

                    <span class="font-mono text-sm font-medium text-ink">
                        {{ $owner->pets->count() }}
                    </span>
                </div>

                <div class="mt-5">
                    <x-ui.button
                        href="{{ route('pets.create', ['owner_id' => $owner->id]) }}"
                        variant="primary"
                        class="w-full justify-center"
                    >
                        Agregar mascota
                    </x-ui.button>
                </div>

                <div class="mt-6 divide-y divide-hairline">
                    @forelse ($owner->pets as $pet)
                        <div class="flex items-center justify-between gap-4 py-4 first:pt-0 last:pb-0">
                            <div>
                                <p class="font-semibold text-ink">
                                    {{ $pet->name }}
                                </p>

                                <p class="mt-1 text-sm text-muted">
                                    {{ $pet->species?->name ?: 'Especie no registrada' }}

                                    @if ($pet->breed)
                                        <span class="text-hairline">·</span>
                                        {{ $pet->breed }}
                                    @endif
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="font-mono text-xs text-muted">
                                    ID #{{ $pet->id }}
                                </p>

                                <a
                                    href="{{ route('pets.show', $pet) }}"
                                    class="mt-1 block text-sm font-semibold text-teal hover:text-teal-dark"
                                >
                                    Ver mascota
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <p class="text-sm font-medium text-ink">
                                Este dueño no tiene mascotas registradas.
                            </p>

                            <p class="mt-1 text-sm text-muted">
                                Puedes registrar una mascota desde el módulo correspondiente.
                            </p>
                        </div>
                    @endforelse
                </div>
            </x-ui.card>
        </div>

        {{-- Información de auditoría --}}
        <x-ui.card>
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Registro creado
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $owner->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>

                <div>
                    <p class="font-mono text-[11px] uppercase tracking-widest text-muted">
                        Última actualización
                    </p>

                    <p class="mt-2 font-mono text-sm text-ink">
                        {{ $owner->updated_at?->format('d/m/Y H:i') ?? '—' }}
                    </p>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-layouts.app>
