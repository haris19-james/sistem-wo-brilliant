<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['post']));

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

foreach (array_filter((['post']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>
<article class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md hover:border-bottle/30 transition group h-full">
    <a href="<?php echo e(route('blog.show', $post['slug'])); ?>" class="block h-48 overflow-hidden">
        <img src="<?php echo e($post['image']); ?>" alt="<?php echo e($post['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
    </a>
    <div class="p-5 flex flex-col flex-1">
        <span class="text-xs font-semibold text-bottle mb-2"><?php echo e($post['category_label']); ?></span>
        <h3 class="text-lg font-bold text-gray-900 mb-2 leading-snug">
            <a href="<?php echo e(route('blog.show', $post['slug'])); ?>" class="hover:text-bottle line-clamp-2"><?php echo e($post['title']); ?></a>
        </h3>
        <p class="text-sm text-gray-500 mb-4 flex-1 line-clamp-3"><?php echo e($post['excerpt']); ?></p>
        <div class="flex items-center justify-between text-xs text-gray-400 pt-3 border-t border-gray-50 mt-auto">
            <span><?php echo e($post['date_formatted']); ?></span>
            <span><?php echo e($post['read_time']); ?></span>
        </div>
        <a href="<?php echo e(route('blog.show', $post['slug'])); ?>" class="mt-3 text-bottle font-semibold text-sm inline-flex items-center hover:underline">
            Baca selengkapnya
            <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
        </a>
    </div>
</article>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/pages/partials/blog-card.blade.php ENDPATH**/ ?>