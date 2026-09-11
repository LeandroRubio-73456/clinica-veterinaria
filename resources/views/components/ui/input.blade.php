@props([
    'label',
    'name',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'autocomplete' => null,
    'required' => false,
    'error' => null,
])

<div class="space-y-2">
    <label
        for="{{ $name }}"
        class="block text-sm font-semibold text-ink"
    >
        {{ $label }}
        @if ($required)
            <span class="text-alert" aria-hidden="true">*</span>
        @endif
    </label>

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        @if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
        @required($required)
        {{ $attributes->merge([
            'class' => 'w-full rounded-md border bg-surface px-4 py-3 text-sm font-body text-ink
                        placeholder:text-muted/70 outline-none transition
                        border-hairline focus:border-teal focus:ring-2 focus:ring-teal/10',
        ]) }}
    >

    @if ($error)
        <p class="text-sm text-alert">
            {{ $error }}
        </p>
    @endif
</div>