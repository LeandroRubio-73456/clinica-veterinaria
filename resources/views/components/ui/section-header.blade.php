@props([
    'eyebrow',
    'title',
    'description' => null,
    'maxWidth' => 'max-w-2xl',
])

<div {{ $attributes->merge(['class' => "{$maxWidth} mb-14"]) }}>
    <x-ui.eyebrow class="mb-3">{{ $eyebrow }}</x-ui.eyebrow>
    <h2 class="font-display text-3xl font-semibold text-ink mb-4">{{ $title }}</h2>
    @if ($description)
        <p class="text-muted leading-relaxed">{{ $description }}</p>
    @endif
</div>

{{--
Uso:
<x-ui.section-header
    eyebrow="El problema"
    title="La gestión manual satura la agenda quirúrgica"
    description="Sin un sistema que valide horarios..." />
--}}
