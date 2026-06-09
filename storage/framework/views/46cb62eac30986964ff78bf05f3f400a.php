<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'autocomplete' => null,
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
    'label',
    'name',
    'type' => 'text',
    'value' => null,
    'required' => false,
    'autocomplete' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div>
    <label for="<?php echo e($name); ?>" class="block text-sm font-medium text-gray-700 mb-1.5"><?php echo e($label); ?></label>
    <input
        type="<?php echo e($type); ?>"
        name="<?php echo e($name); ?>"
        id="<?php echo e($name); ?>"
        value="<?php echo e($value); ?>"
        <?php if($required): ?> required <?php endif; ?>
        <?php if($autocomplete): ?> autocomplete="<?php echo e($autocomplete); ?>" <?php endif; ?>
        <?php echo e($attributes->merge(['class' => 'w-full border border-gray-200 rounded-xl px-4 py-2.5 text-gray-900 placeholder:text-gray-400 focus:ring-2 focus:ring-bottle/40 focus:border-bottle outline-none transition shadow-sm'])); ?>

    />
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/auth/input.blade.php ENDPATH**/ ?>