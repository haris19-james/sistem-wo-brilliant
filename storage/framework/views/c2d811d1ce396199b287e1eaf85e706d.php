<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'value' => 0,
    'count' => 0,
    'size' => 'sm', // sm|md
    'color' => 'green', // green|brand
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
    'value' => 0,
    'count' => 0,
    'size' => 'sm', // sm|md
    'color' => 'green', // green|brand
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $v = (float) $value;
    $filled = (int) floor($v);
    $hasHalf = ($v - $filled) >= 0.5;
    $empty = 5 - $filled - ($hasHalf ? 1 : 0);

    $starClass = $size === 'md' ? 'w-4 h-4' : 'w-3.5 h-3.5';
    $textClass = $size === 'md' ? 'text-sm' : 'text-xs';
    $filledColor = ($color ?? 'green') === 'green' ? 'text-green-500' : 'text-bottleBright';
?>

<?php if($count > 0): ?>
    <div class="flex items-center gap-2">
        <div class="flex items-center gap-0.5">
            <?php for($i=0; $i < $filled; $i++): ?>
                <svg class="<?php echo e($starClass); ?> <?php echo e($filledColor); ?>" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            <?php endfor; ?>

            <?php if($hasHalf): ?>
                <svg class="<?php echo e($starClass); ?> <?php echo e($filledColor); ?>" viewBox="0 0 20 20" aria-hidden="true">
                    <defs>
                        <linearGradient id="halfStar-<?php echo e(uniqid()); ?>" x1="0" x2="1" y1="0" y2="0">
                            <stop offset="50%" stop-color="currentColor" />
                            <stop offset="50%" stop-color="#E5E7EB" />
                        </linearGradient>
                    </defs>
                    <path fill="url(#halfStar-<?php echo e(uniqid()); ?>)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            <?php endif; ?>

            <?php for($i=0; $i < $empty; $i++): ?>
                <svg class="<?php echo e($starClass); ?> text-gray-200" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            <?php endfor; ?>
        </div>

        <div class="<?php echo e($textClass); ?> text-gray-600">
            <span class="font-semibold text-gray-800"><?php echo e(number_format($v, 1)); ?></span>
            <span class="text-gray-400">(<?php echo e(number_format((int) $count, 0, ',', '.')); ?>)</span>
        </div>
    </div>
<?php else: ?>
    <div class="flex items-center gap-2">
        <div class="<?php echo e($textClass); ?> text-gray-500 italic">
            Belum ada ulasan
        </div>
    </div>
<?php endif; ?>

<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/rating-stars.blade.php ENDPATH**/ ?>