
@props([
    'type' => 'button',
    'variant' => 'primary',
])

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'rounded-lg px-4 py-2 font-semibold transition',
        'bg-green-800 text-white hover:bg-green-900'
            => $variant === 'primary',
        'border border-gray-300 bg-white text-gray-700'
            => $variant === 'secondary',
    ]) }}
>
    {{ $slot }}
</button>
