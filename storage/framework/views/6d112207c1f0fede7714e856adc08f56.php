<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items',
    'active',
    'variant' => 'client',
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
    'items',
    'active',
    'variant' => 'client',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<nav class="space-y-1">
    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
            $isActive = $active === $item['key'];
            $activeCustomer = $isActive ? 'rounded-2xl bg-green-50 px-4 py-3 text-green-700 shadow-sm' : 'rounded-2xl px-4 py-3 text-slate-600 hover:bg-green-50/50';
            $activeLapangan = $isActive ? 'bg-green-50 text-green-700' : 'text-gray-600 hover:text-gray-900 hover:bg-green-50/50';
        ?>
        <a href="<?php echo e($item['url']); ?>"
           class="flex items-center gap-3 text-sm font-medium transition <?php echo e($variant === 'client' ? $activeCustomer : 'px-3 py-2 rounded-lg '.$activeLapangan); ?>">
            <?php if($variant === 'client'): ?>
                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl <?php echo e($isActive ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500'); ?>">
                    <?php echo e($item['abbr']); ?>

                </span>
            <?php else: ?>
                <i data-feather="<?php echo e($item['icon']); ?>" class="w-4 h-4 mr-1 shrink-0"></i>
            <?php endif; ?>
            <span><?php echo e($item['label']); ?></span>
        </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</nav>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/settings/sidebar-nav.blade.php ENDPATH**/ ?>