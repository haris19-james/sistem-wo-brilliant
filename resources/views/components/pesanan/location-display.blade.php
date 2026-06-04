@props([
    'pesanan',
    'label' => 'Lokasi',
    'layout' => 'stack', // stack | inline
    'showMissingHint' => false,
])

@php
    $href = $pesanan->google_maps_href;
    $hasMaps = filled($href);
@endphp

@if($layout === 'inline')
<div {{ $attributes->merge(['class' => '']) }}>
    <dt class="text-gray-500">{{ $label }}</dt>
    <dd class="font-semibold text-gray-900 mt-0.5">
        @include('components.pesanan.partials.location-maps-link', ['pesanan' => $pesanan, 'href' => $href, 'hasMaps' => $hasMaps, 'showMissingHint' => $showMissingHint])
    </dd>
</div>
@else
<div {{ $attributes->merge(['class' => '']) }}>
    @if($label)
    <p class="text-gray-500 text-sm">{{ $label }}</p>
    @endif
    <div class="mt-0.5">
        @include('components.pesanan.partials.location-maps-link', ['pesanan' => $pesanan, 'href' => $href, 'hasMaps' => $hasMaps, 'showMissingHint' => $showMissingHint])
    </div>
</div>
@endif

@once
@push('scripts')
<script>
(function () {
    function openMapsWithLoading(url) {
        if (!url) return;
        if (typeof window.showLoading === 'function') {
            window.showLoading('Membuka rute lokasi di Google Maps...');
        }
        setTimeout(function () {
            window.open(url, '_blank', 'noopener,noreferrer');
            if (typeof window.hideLoading === 'function') {
                window.hideLoading();
            }
        }, 380);
    }

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('[data-open-google-maps]');
        if (!trigger) return;
        e.preventDefault();
        openMapsWithLoading(trigger.getAttribute('data-maps-url') || trigger.getAttribute('href'));
    });
})();
</script>
@endpush
@endonce
