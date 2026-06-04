@props([
    'src' => null,
    'fallback' => null,
    'alt' => '',
    'type' => 'package',
    'wrapperClass' => '',
    'imgClass' => 'w-full h-full object-cover',
])

@php
    $placeholder = \App\Support\ImageHelper::placeholderUrl($type);
    $resolved = \App\Support\ImageHelper::url($src, $fallback) ?? $placeholder;
@endphp

<div {{ $attributes->merge(['class' => trim('overflow-hidden '.$wrapperClass)]) }}>
    <img
        src="{{ $resolved }}"
        alt="{{ $alt }}"
        class="{{ $imgClass }}"
        loading="lazy"
        decoding="async"
        referrerpolicy="no-referrer-when-downgrade"
        data-placeholder="{{ $placeholder }}"
        onerror="this.onerror=null;this.src=this.dataset.placeholder||'{{ $placeholder }}'"
    >
</div>
