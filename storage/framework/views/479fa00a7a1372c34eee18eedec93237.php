<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', config('brilliant.name').' — '.config('brilliant.tagline')); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Brilliant Event & Wedding Organizer — wujudkan pernikahan impian Anda di Garut dan sekitarnya.'); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo $__env->make('partials.brand-tailwind', ['fontSerif' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->yieldPushContent('head'); ?>
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-white text-ink font-sans antialiased overflow-x-hidden flex flex-col min-h-screen" x-data="{ mobileOpen: false }">

<?php
    $navItems = [
        'home' => ['label' => 'Beranda', 'route' => 'home'],
        'paket' => ['label' => 'Paket', 'route' => 'paket'],
        'vendor' => ['label' => 'Vendor', 'route' => 'vendor'],
        'about' => ['label' => 'Tentang Kami', 'route' => 'about'],
        'blog' => ['label' => 'Blog', 'route' => 'blog'],
        'contact' => ['label' => 'Kontak', 'route' => 'contact'],
    ];
    $activeNav = $activeNav ?? '';
?>

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm">
    <nav class="container mx-auto px-4 sm:px-6 py-3 md:py-4 flex justify-between items-center gap-4">
        <?php if (isset($component)) { $__componentOriginal3e23a466771c4cdcfd7540b30c912b11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e23a466771c4cdcfd7540b30c912b11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-logo','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e23a466771c4cdcfd7540b30c912b11)): ?>
<?php $attributes = $__attributesOriginal3e23a466771c4cdcfd7540b30c912b11; ?>
<?php unset($__attributesOriginal3e23a466771c4cdcfd7540b30c912b11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e23a466771c4cdcfd7540b30c912b11)): ?>
<?php $component = $__componentOriginal3e23a466771c4cdcfd7540b30c912b11; ?>
<?php unset($__componentOriginal3e23a466771c4cdcfd7540b30c912b11); ?>
<?php endif; ?>

        <div class="hidden lg:flex items-center gap-6 text-sm font-medium">
            <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($item['route'])); ?>"
               class="py-2 transition <?php echo e($activeNav === $key ? 'text-bottle border-b-2 border-bottle font-semibold' : 'text-gray-800 hover:text-bottle'); ?>">
                <?php echo e($item['label']); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <div class="hidden lg:flex items-center gap-3 text-sm font-medium shrink-0">
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->role === 'admin'): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-bottle font-semibold hover:underline">Admin</a>
                <?php elseif(auth()->user()->role === 'lapangan'): ?>
                <a href="<?php echo e(route('lapangan.dashboard')); ?>" class="text-teal-700 font-semibold hover:underline">Tim Lapangan</a>
                <?php else: ?>
                <a href="<?php echo e(route('client.dashboard')); ?>" class="text-bottle font-semibold hover:underline">Dashboard</a>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-gray-600 hover:text-bottle">Logout</button>
                </form>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="text-gray-800 hover:text-bottle px-2">Masuk</a>
                <a href="<?php echo e(route('register')); ?>" data-no-loading class="bg-bottle text-white font-semibold py-2 px-5 rounded-lg hover:bg-bottleHover transition">Daftar</a>
            <?php endif; ?>
        </div>

        <button type="button" @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-gray-800 hover:text-bottle rounded-lg" aria-label="Menu">
            <svg x-show="!mobileOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" x-cloak class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </nav>

    <div x-show="mobileOpen" x-cloak x-transition class="lg:hidden border-t border-gray-100 bg-white px-4 py-4 space-y-1">
        <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route($item['route'])); ?>" @click="mobileOpen = false"
           class="block py-3 px-3 rounded-xl text-sm font-medium <?php echo e($activeNav === $key ? 'bg-leafSoft text-bottle font-semibold' : 'text-gray-800 hover:bg-gray-50'); ?>">
            <?php echo e($item['label']); ?>

        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <div class="pt-3 border-t border-gray-100 flex flex-col gap-2">
            <?php if(auth()->guard()->check()): ?>
            <?php if(auth()->user()->role === 'admin'): ?>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="py-2 text-center text-bottle font-semibold">Panel Admin</a>
            <?php elseif(auth()->user()->role === 'lapangan'): ?>
            <a href="<?php echo e(route('lapangan.dashboard')); ?>" class="py-2 text-center text-teal-700 font-semibold">Tim Lapangan</a>
            <?php else: ?>
            <a href="<?php echo e(route('client.dashboard')); ?>" class="py-2 text-center bg-bottle text-white font-semibold rounded-xl">Dashboard Saya</a>
            <?php endif; ?>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full py-2 text-gray-600 text-sm">Logout</button>
            </form>
            <?php else: ?>
            <a href="<?php echo e(route('login')); ?>" class="block py-3 text-center border border-gray-200 rounded-xl font-semibold">Masuk</a>
            <a href="<?php echo e(route('register')); ?>" data-no-loading class="block py-3 text-center bg-bottle text-white font-semibold rounded-xl">Daftar</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<?php if(session('success')): ?>
<div class="bg-green-50 border-b border-green-200 text-green-800 text-sm text-center py-3 px-4"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="bg-red-50 border-b border-red-200 text-red-800 text-sm text-center py-3 px-4"><?php echo e(session('error')); ?></div>
<?php endif; ?>

<main class="flex-1">
    <?php echo $__env->yieldContent('content'); ?>
</main>

<footer class="bg-ink text-gray-300 mt-auto">
    <div class="container mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
        <div>
            <?php if (isset($component)) { $__componentOriginal3e23a466771c4cdcfd7540b30c912b11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e23a466771c4cdcfd7540b30c912b11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-logo','data' => ['size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e23a466771c4cdcfd7540b30c912b11)): ?>
<?php $attributes = $__attributesOriginal3e23a466771c4cdcfd7540b30c912b11; ?>
<?php unset($__attributesOriginal3e23a466771c4cdcfd7540b30c912b11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e23a466771c4cdcfd7540b30c912b11)): ?>
<?php $component = $__componentOriginal3e23a466771c4cdcfd7540b30c912b11; ?>
<?php unset($__componentOriginal3e23a466771c4cdcfd7540b30c912b11); ?>
<?php endif; ?>
            <p class="text-sm mt-4 leading-relaxed text-gray-400"><?php echo e(config('brilliant.motto')); ?></p>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Navigasi</h4>
            <ul class="space-y-2 text-sm">
                <?php $__currentLoopData = $navItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><a href="<?php echo e(route($item['route'])); ?>" class="hover:text-white transition"><?php echo e($item['label']); ?></a></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Kontak</h4>
            <ul class="space-y-2 text-sm text-gray-400">
                <li><a href="tel:<?php echo e(config('brilliant.contact.phone_digits')); ?>" class="hover:text-white"><?php echo e(config('brilliant.contact.phone')); ?></a></li>
                <li><a href="mailto:<?php echo e(config('brilliant.contact.email')); ?>" class="hover:text-white"><?php echo e(config('brilliant.contact.email')); ?></a></li>
                <li><?php echo e(config('brilliant.contact.address')); ?></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Ikuti Kami</h4>
            <div class="flex gap-3">
                <a href="<?php echo e(config('brilliant.social.instagram')); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bottle transition" aria-label="Instagram">IG</a>
                <a href="<?php echo e(config('brilliant.social.facebook')); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bottle transition" aria-label="Facebook">FB</a>
                <a href="<?php echo e(\App\Support\Branding::whatsappUrl()); ?>" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bottle transition" aria-label="WhatsApp">WA</a>
            </div>
            <a href="<?php echo e(route('contact')); ?>" class="inline-block mt-6 text-sm font-semibold text-white border border-white/30 px-5 py-2 rounded-full hover:bg-white hover:text-gray-900 transition">Konsultasi Gratis</a>
        </div>
    </div>
    <div class="border-t border-gray-800 text-center text-xs text-gray-500 py-4">
        &copy; <?php echo e(date('Y')); ?> <?php echo e(config('brilliant.name')); ?> <?php echo e(config('brilliant.tagline')); ?>. All rights reserved.
    </div>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
<script>
window.BrilliantImageConfig = {
    placeholderPackage: <?php echo json_encode(\App\Support\ImageHelper::placeholderUrl('package'), 15, 512) ?>,
    placeholderVendor: <?php echo json_encode(\App\Support\ImageHelper::placeholderUrl('vendor'), 15, 512) ?>,
};
</script>
<script src="<?php echo e(asset('js/image-fallback.js')); ?>?v=1" defer></script>
<?php echo $__env->make('components.wedding-decoration', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/layouts/public.blade.php ENDPATH**/ ?>