@props([
    'label',
    'name',
    'options' => [],
    'value' => '',
    'placeholder' => 'Seleccionar...',
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

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @required($required)
        {{ $attributes->merge([
            'class' => 'w-full rounded-md border border-hairline bg-surface px-4 py-3 text-sm font-body text-ink
                        outline-none transition
                        focus:border-teal focus:ring-2 focus:ring-teal/10',
        ]) }}
    >
        <option value="">
            {{ $placeholder }}
        </option>

        @foreach ($options as $optionValue => $optionLabel)
            <option
                value="{{ $optionValue }}"
                @selected((string) old($name, $value) === (string) $optionValue)
            >
                {{ $optionLabel }}
            </option>
        @endforeach
    </select>

    @if ($error)
        <p class="text-sm text-alert">
            {{ $error }}
        </p>
    @endif
</div>