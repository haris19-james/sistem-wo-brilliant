<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'src' => null,
    'fallback' => null,
    'alt' => '',
    'type' => 'package',
    'wrapperClass' => '',
    'imgClass' => 'w-full h-full object-cover',
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
    'src' => null,
    'fallback' => null,
    'alt' => '',
    'type' => 'package',
    'wrapperClass' => '',
    'imgClass' => 'w-full h-full object-cover',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $placeholder = \App\Support\ImageHelper::placeholderUrl($type);
    $resolved = \App\Support\ImageHelper::url($src, $fallback) ?? $placeholder;
?>

<div <?php echo e($attributes->merge(['class' => trim('overflow-hidden '.$wrapperClass)])); ?>>
    <img
        src="<?php echo e($resolved); ?>"
        alt="<?php echo e($alt); ?>"
        class="<?php echo e($imgClass); ?>"
        loading="lazy"
        decoding="async"
        referrerpolicy="no-referrer-when-downgrade"
        data-placeholder="<?php echo e($placeholder); ?>"
        onerror="this.onerror=null;this.src=this.dataset.placeholder||'<?php echo e($placeholder); ?>'"
    >
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/media-image.blade.php ENDPATH**/ ?>