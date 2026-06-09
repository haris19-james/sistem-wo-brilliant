<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'pesanan',
    'label' => 'Lokasi',
    'layout' => 'stack', // stack | inline
    'showMissingHint' => false,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'pesanan',
    'label' => 'Lokasi',
    'layout' => 'stack', // stack | inline
    'showMissingHint' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $href = $pesanan->google_maps_href;
    $hasMaps = filled($href);
?>

<?php if($layout === 'inline'): ?>
<div <?php echo e($attributes->merge(['class' => ''])); ?>>
    <dt class="text-gray-500"><?php echo e($label); ?></dt>
    <dd class="font-semibold text-gray-900 mt-0.5">
        <?php echo $__env->make('components.pesanan.partials.location-maps-link', ['pesanan' => $pesanan, 'href' => $href, 'hasMaps' => $hasMaps, 'showMissingHint' => $showMissingHint], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </dd>
</div>
<?php else: ?>
<div <?php echo e($attributes->merge(['class' => ''])); ?>>
    <?php if($label): ?>
    <p class="text-gray-500 text-sm"><?php echo e($label); ?></p>
    <?php endif; ?>
    <div class="mt-0.5">
        <?php echo $__env->make('components.pesanan.partials.location-maps-link', ['pesanan' => $pesanan, 'href' => $href, 'hasMaps' => $hasMaps, 'showMissingHint' => $showMissingHint], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
</div>
<?php endif; ?>

<?php if (! $__env->hasRenderedOnce('097a2121-3042-4a2f-833b-143e80283791')): $__env->markAsRenderedOnce('097a2121-3042-4a2f-833b-143e80283791'); ?>
<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/pesanan/location-display.blade.php ENDPATH**/ ?>