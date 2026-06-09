<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'slides' => null,
    'interval' => null,
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
    'slides' => null,
    'interval' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $slides = $slides ?? \App\Support\Branding::heroGallerySlides();
    $interval = $interval ?? config('brilliant.hero_carousel_interval_ms', 4000);
    $slideCount = count($slides);
?>

<div <?php echo e($attributes->merge(['class' => 'organic-frame relative shadow-2xl border-8 border-white'])); ?>

     x-data="heroPortfolioCarousel(<?php echo e($slideCount); ?>, <?php echo e((int) $interval); ?>)"
     x-init="init()"
     @mouseenter="pause()"
     @mouseleave="resume()"
     @focusin="pause()"
     @focusout="resume()"
     role="region"
     aria-roledescription="carousel"
     aria-label="Galeri portofolio Brilliant WO">

    
    <div class="absolute top-4 left-4 z-20 flex items-center gap-1.5 rounded-full bg-white/90 backdrop-blur-sm px-3 py-1.5 text-[0.65rem] font-bold uppercase tracking-wider text-bottle shadow-sm pointer-events-none">
        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M21 19V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
        </svg>
        Galeri Portofolio
    </div>

    <div class="hero-carousel-viewport relative w-full h-full overflow-hidden bg-leafSoft">
        <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="hero-carousel-slide absolute inset-0 transition-opacity duration-1000 ease-in-out"
             :class="active === <?php echo e($index); ?> ? 'opacity-100 z-[1]' : 'opacity-0 z-0'"
             :aria-hidden="active !== <?php echo e($index); ?> ? 'true' : 'false'">
            <img src="<?php echo e($slide['src']); ?>"
                 alt="<?php echo e($slide['alt']); ?>"
                 class="w-full h-full object-cover"
                 width="840"
                 height="840"
                 <?php if($index === 0): ?> loading="eager" fetchpriority="high" <?php else: ?> loading="lazy" <?php endif; ?>
                 decoding="async">
            <?php if(!empty($slide['caption'])): ?>
            <div class="absolute inset-x-0 bottom-0 z-10 bg-gradient-to-t from-ink/70 via-ink/30 to-transparent px-4 pb-4 pt-10 pointer-events-none transition-opacity duration-700"
                 :class="active === <?php echo e($index); ?> ? 'opacity-100' : 'opacity-0'">
                <p class="text-white text-xs md:text-sm font-medium tracking-wide"><?php echo e($slide['caption']); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <?php if($slideCount > 1): ?>
    <div class="absolute bottom-3 left-0 right-0 z-20 flex justify-center gap-1.5" role="tablist" aria-label="Pilih foto portofolio">
        <?php $__currentLoopData = $slides; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <button type="button"
                class="hero-carousel-dot w-2 h-2 rounded-full transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-bottle focus-visible:ring-offset-2"
                :class="active === <?php echo e($index); ?> ? 'bg-white w-5' : 'bg-white/50 hover:bg-white/80'"
                @click="goTo(<?php echo e($index); ?>); pause(); resume()"
                :aria-selected="active === <?php echo e($index); ?>"
                role="tab"
                aria-label="Foto <?php echo e($index + 1); ?>: <?php echo e($slide['alt']); ?>"></button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/hero-portfolio-carousel.blade.php ENDPATH**/ ?>