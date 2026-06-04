@props([
    'src',
    'alt' => '',
    'class' => '',
])

<img
    src="{{ $src }}"
    alt="{{ $alt }}"
    loading="lazy"
    decoding="async"
    {{ $attributes->merge(['class' => trim($class)]) }}
/>
