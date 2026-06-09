<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['text' => 'Masuk']));

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

foreach (array_filter((['text' => 'Masuk']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<button type="submit" <?php echo e($attributes->merge(['class' => 'w-full flex items-center justify-center bg-bottle text-white font-semibold py-3 rounded-xl shadow-sm hover:bg-bottleHover active:scale-[0.99] transition'])); ?>>
    <?php echo e($text); ?>

</button>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/auth/submit-button.blade.php ENDPATH**/ ?>