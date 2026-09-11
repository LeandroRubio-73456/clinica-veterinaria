<x-layouts.app title="Detalle de usuario" max-width="max-w-6xl">
    <x-slot:actions>
        <x-ui.button href="{{ route('users.edit', $user) }}" variant="primary">Editar usuario</x-ui.button>
    </x-slot:actions>

    <div class="space-y-8">
        <x-ui.section-header eyebrow="Cuenta del personal" title="{{ $user->name }}" description="Información de acceso y rol asignado en el sistema." />

        @if (session('success'))
            <div class="rounded-md border border-teal/20 bg-teal-light px-4 py-3 text-sm text-teal-dark" role="status">{{ session('success') }}</div>
        @endif

        <div class="grid gap-6 md:grid-cols-2">
            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Información de cuenta</x-ui.eyebrow>
                <dl class="mt-5 space-y-4">
                    <div class="border-b border-hairline pb-4"><dt class="text-sm text-muted">Nombre</dt><dd class="mt-1 font-medium text-ink">{{ $user->name }}</dd></div>
                    <div class="border-b border-hairline pb-4"><dt class="text-sm text-muted">Correo electrónico</dt><dd class="mt-1 text-ink">{{ $user->email }}</dd></div>
                    <div><dt class="text-sm text-muted">Registrado el</dt><dd class="mt-1 font-mono text-sm text-ink">{{ $user->created_at?->format('d/m/Y H:i') ?? '—' }}</dd></div>
                </dl>
            </x-ui.card>

            <x-ui.card>
                <x-ui.eyebrow variant="kicker">Accesos</x-ui.eyebrow>
                @php($roleLabels = ['admin' => 'Administrador', 'administrativo' => 'Administrativo', 'veterinario' => 'Veterinario'])
                <dl class="mt-5 space-y-4">
                    <div class="border-b border-hairline pb-4"><dt class="text-sm text-muted">Rol asignado</dt><dd class="mt-1 font-semibold text-teal-dark">{{ $roleLabels[$user->role] ?? 'Sin rol' }}</dd></div>
                    @if ($user->role === 'veterinario')
                        <div class="border-b border-hairline pb-4">
                            <dt class="text-sm text-muted">Perfil veterinario asociado</dt>
                            <dd class="mt-1 font-medium text-ink">
                                @if ($user->veterinarian)
                                    {{ $user->veterinarian->first_name }} {{ $user->veterinarian->last_name }}
                                @else
                                    <span class="text-alert">Sin asociar</span>
                                @endif
                            </dd>
                            <p class="mt-1 text-xs text-muted">Este perfil determina la agenda y las cirugías que puede gestionar.</p>
                        </div>
                    @endif
                    <div><dt class="text-sm text-muted">Descripción</dt><dd class="mt-1 text-sm leading-6 text-muted">
                        @switch($user->role)
                            @case('admin') Gestiona usuarios, catálogos, personal y todos los módulos. @break
                            @case('administrativo') Gestiona dueños, mascotas y programación de cirugías. @break
                            @case('veterinario') Consulta su agenda y gestiona el ciclo de sus cirugías. @break
                            @default El usuario aún no tiene un rol válido asignado.
                        @endswitch
                    </dd></div>
                </dl>
            </x-ui.card>
        </div>

        <div><x-ui.button href="{{ route('users.index') }}" variant="secondary">Volver a usuarios</x-ui.button></div>
    </div>
</x-layouts.app>
