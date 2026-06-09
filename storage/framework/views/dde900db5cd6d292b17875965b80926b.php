<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => 'total_biaya',
    'label' => 'Total biaya',
    'value' => '',
    'required' => true,
    'placeholder' => '0',
    'id' => null,
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
    'name' => 'total_biaya',
    'label' => 'Total biaya',
    'value' => '',
    'required' => true,
    'placeholder' => '0',
    'id' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $rawValue = $value !== '' && $value !== null
        ? (string) (int) round(\App\Support\MoneyParser::toFloat($value))
        : '';
    $displayValue = $rawValue !== ''
        ? \App\Support\MoneyParser::formatId($rawValue)
        : '';
    $inputId = $id ?? $name.'_display';
    $hiddenId = $name.'_raw';
?>

<div <?php echo e($attributes->merge(['class' => 'space-y-1'])); ?> data-rupiah-input>
    <?php if($label): ?>
    <label for="<?php echo e($inputId); ?>" class="text-xs font-semibold text-gray-600 block"><?php echo e($label); ?></label>
    <?php endif; ?>
    <div class="flex rounded-lg border border-gray-200 overflow-hidden focus-within:ring-1 focus-within:ring-bottle focus-within:border-bottle bg-white">
        <span class="inline-flex items-center px-3 text-sm font-semibold text-gray-600 bg-gray-50 border-r border-gray-200 shrink-0">Rp</span>
        <input type="text"
               id="<?php echo e($inputId); ?>"
               class="rupiah-input-display flex-1 min-w-0 px-3 py-2 text-sm outline-none border-0"
               inputmode="numeric"
               autocomplete="off"
               placeholder="<?php echo e($placeholder); ?>"
               value="<?php echo e($displayValue); ?>"
               <?php if($required): ?> required aria-required="true" <?php endif; ?>
               pattern="[0-9.]*"
               title="Hanya angka. Contoh: ketik 1000000 untuk Rp 1.000.000">
        <input type="hidden"
               name="<?php echo e($name); ?>"
               id="<?php echo e($hiddenId); ?>"
               data-rupiah-value
               value="<?php echo e($rawValue); ?>">
    </div>
    <p class="text-[10px] text-gray-500">Ketik angka saja — otomatis diformat (contoh: 1000000 → 1.000.000). Nilai disimpan tanpa pengali.</p>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/input-rupiah.blade.php ENDPATH**/ ?>