<x-layouts.app title="Notificaciones" max-width="max-w-4xl">
    <div class="space-y-8">
        <x-ui.section-header
            eyebrow="Seguimiento operativo"
            title="Notificaciones"
            description="Cirugías próximas y acciones pendientes relacionadas con tu agenda."
        />

        <x-ui.card padding="0">
            @forelse ($notifications as $notification)
                <a href="{{ $notification['url'] }}" class="flex gap-4 border-b border-hairline px-6 py-5 last:border-0 hover:bg-base/50 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-teal">
                    <span class="mt-1 flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $notification['priority'] === 'high' ? 'bg-alert/10 text-alert' : 'bg-teal-light text-teal-dark' }}" aria-hidden="true">
                        @if ($notification['type'] === 'in_progress')
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 6v6l4 2" /><circle cx="12" cy="12" r="9" /></svg>
                        @else
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M7 3v4M17 3v4M4 9h16M6 5h12a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z" /></svg>
                        @endif
                    </span>
                    <span class="min-w-0">
                        <span class="block font-semibold text-ink">{{ $notification['title'] }}</span>
                        <span class="mt-1 block text-sm leading-6 text-muted">{{ $notification['message'] }}</span>
                    </span>
                </a>
            @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-sm font-semibold text-ink">No tienes notificaciones pendientes.</p>
                    <p class="mt-1 text-sm text-muted">Aquí aparecerán las cirugías próximas y las que requieran seguimiento.</p>
                </div>
            @endforelse
        </x-ui.card>
    </div>
</x-layouts.app>
