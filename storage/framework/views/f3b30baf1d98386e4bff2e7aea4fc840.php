<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Admin'); ?> - Brilliant WO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo $__env->make('partials.brand-tailwind', ['extraColors' => ['grayBg' => '#F8FAFC', 'grayText' => '#64748B']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="<?php echo e(asset('js/notification-bell.js')); ?>?v=2"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden" data-brilliant-panel="admin" data-notification-auto-poll data-poll-interval="15000" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display:none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0">
        <div class="flex items-center justify-center h-20 border-b border-gray-50 px-6">
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center space-x-2">
            <?php if(\App\Support\Branding::hasLogo()): ?>
                <img src="<?php echo e(\App\Support\Branding::logoUrl()); ?>" alt="Brilliant" class="h-10 w-auto max-w-[200px] object-contain">
            <?php else: ?>
                <span class="text-xl font-bold text-bottle">Brilliant WO</span>
            <?php endif; ?>
                <div class="leading-tight">
                    <h1 class="text-lg font-bold text-gray-900">Brilliant</h1>
                    <p class="text-[0.45rem] text-gray-500 uppercase tracking-widest">Admin Panel</p>
                </div>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <?php
                $active = $activeMenu ?? '';
                $link = fn ($key, $route) => $active === $key
                    ? 'flex items-center px-4 py-3 bg-bottle/10 text-bottle font-semibold rounded-xl ring-1 ring-bottle/15'
                    : 'flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition';
            ?>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="<?php echo e($link('dashboard', 'admin.dashboard')); ?>" data-loading-message="Memuat ringkasan dashboard...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="<?php echo e(route('admin.booking')); ?>" class="<?php echo e($link('booking', 'admin.booking')); ?>" data-loading-message="Memuat data booking & pesanan...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Booking
            </a>
            <a href="<?php echo e(route('admin.paket.index')); ?>" class="<?php echo e($link('paket', 'admin.paket.index')); ?>" data-loading-message="Memuat daftar paket pernikahan...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Paket
            </a>
            <a href="<?php echo e(route('admin.vendor.index')); ?>" class="<?php echo e($link('vendor', 'admin.vendor.index')); ?>" data-loading-message="Memuat direktori vendor...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Vendor
            </a>
            <?php if (isset($component)) { $__componentOriginal98cd7d972020ef776f568d0ac7c195c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98cd7d972020ef776f568d0ac7c195c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar.jadwal-acara-nav','data' => ['panel' => 'admin','activeMenu' => $active,'rundownUrl' => route('admin.jadwal-acara.rundown'),'meetingUrl' => route('admin.jadwal-acara.meeting-vendor'),'rundownLocked' => false,'meetingLocked' => false]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar.jadwal-acara-nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['panel' => 'admin','active-menu' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($active),'rundown-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.jadwal-acara.rundown')),'meeting-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('admin.jadwal-acara.meeting-vendor')),'rundown-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false),'meeting-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(false)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal98cd7d972020ef776f568d0ac7c195c3)): ?>
<?php $attributes = $__attributesOriginal98cd7d972020ef776f568d0ac7c195c3; ?>
<?php unset($__attributesOriginal98cd7d972020ef776f568d0ac7c195c3); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal98cd7d972020ef776f568d0ac7c195c3)): ?>
<?php $component = $__componentOriginal98cd7d972020ef776f568d0ac7c195c3; ?>
<?php unset($__componentOriginal98cd7d972020ef776f568d0ac7c195c3); ?>
<?php endif; ?>
            <a href="<?php echo e(route('admin.vendor-keuangan.index')); ?>" class="<?php echo e($link('vendor-keuangan', 'admin.vendor-keuangan.index')); ?>" data-no-loading data-loading-message="Memuat keuangan vendor...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Keuangan Vendor
            </a>
            <a href="<?php echo e(route('admin.laporan-keuangan')); ?>" class="<?php echo e($link('laporan-keuangan', 'admin.laporan-keuangan')); ?>" data-loading-message="Memuat laporan keuangan...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Keuangan
            </a>
            <a href="<?php echo e(route('admin.chat')); ?>" class="<?php echo e($link('chat', 'admin.chat')); ?>" data-loading-message="Membuka pusat chat...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Chat
            </a>
            <a href="<?php echo e(route('admin.pengaturan')); ?>" class="<?php echo e($link('pengaturan', 'admin.pengaturan')); ?>" data-loading-message="Memuat pengaturan sistem...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>
            <a href="<?php echo e(route('home')); ?>" target="_blank" data-no-loading class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat Website
            </a>
        </nav>

        <div class="p-4 border-t border-gray-50">
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="w-full flex items-center px-4 py-3 text-grayText hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <?php if (isset($component)) { $__componentOriginald37f1b809d8dad08d9600a37cd72bf8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald37f1b809d8dad08d9600a37cd72bf8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.dashboard.header','data' => ['title' => $pageTitle ?? 'Admin Dashboard']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('dashboard-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pageTitle ?? 'Admin Dashboard')]); ?>
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Admin Avatar" class="w-9 h-9 rounded-full object-cover border border-gray-200">
            <div class="hidden md:block text-right min-w-0">
                <p class="text-sm font-semibold text-gray-900 leading-tight"><?php echo e(auth()->user()->name ?? 'Admin'); ?></p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald37f1b809d8dad08d9600a37cd72bf8e)): ?>
<?php $attributes = $__attributesOriginald37f1b809d8dad08d9600a37cd72bf8e; ?>
<?php unset($__attributesOriginald37f1b809d8dad08d9600a37cd72bf8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald37f1b809d8dad08d9600a37cd72bf8e)): ?>
<?php $component = $__componentOriginald37f1b809d8dad08d9600a37cd72bf8e; ?>
<?php unset($__componentOriginald37f1b809d8dad08d9600a37cd72bf8e); ?>
<?php endif; ?>

        <main id="app-main" class="flex-1 overflow-y-auto p-6 lg:p-8">
            <?php if(session('success')): ?>
                <div class="mb-6 p-4 bg-green-50 text-green-800 border border-green-200 rounded-xl text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="mb-6 p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl text-sm"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <div class="mb-6 p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl text-sm">
                    <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    <?php if (isset($component)) { $__componentOriginal78a1c6abb62915a9cf83a6f55fd8c63d = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal78a1c6abb62915a9cf83a6f55fd8c63d = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.urgent-toast','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('urgent-toast'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal78a1c6abb62915a9cf83a6f55fd8c63d)): ?>
<?php $attributes = $__attributesOriginal78a1c6abb62915a9cf83a6f55fd8c63d; ?>
<?php unset($__attributesOriginal78a1c6abb62915a9cf83a6f55fd8c63d); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal78a1c6abb62915a9cf83a6f55fd8c63d)): ?>
<?php $component = $__componentOriginal78a1c6abb62915a9cf83a6f55fd8c63d; ?>
<?php unset($__componentOriginal78a1c6abb62915a9cf83a6f55fd8c63d); ?>
<?php endif; ?>
    <?php echo $__env->make('components.loading-overlay', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.loading-overlay-premium', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('components.wedding-decoration', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->make('components.page-nav-skeleton', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script>
    window.BrilliantImageConfig = {
        placeholderPackage: <?php echo json_encode(\App\Support\ImageHelper::placeholderUrl('package'), 15, 512) ?>,
        placeholderVendor: <?php echo json_encode(\App\Support\ImageHelper::placeholderUrl('vendor'), 15, 512) ?>,
    };
    </script>
    <script src="<?php echo e(asset('js/image-fallback.js')); ?>?v=1" defer></script>
    <script src="<?php echo e(asset('js/brilliant-nav-loading.js')); ?>?v=1" defer></script>
    <script src="<?php echo e(asset('js/page-nav.js')); ?>?v=2" defer></script>
    <?php if(config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key')): ?>
        <script src="https://js.pusher.com/8.0/pusher.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.12.0/dist/echo.iife.js" defer></script>
    <?php endif; ?>
    <script>
        window.NotificationConfig = {
            pollUrl: '<?php echo e(route('api.notifications.poll')); ?>',
            countUrl: '<?php echo e(route('api.notifications.count')); ?>',
            readAllUrl: '<?php echo e(route('api.notifications.read-all')); ?>',
            pollInterval: 15000,
            roleChannel: 'notifications.<?php echo e(auth()->user()?->role ?? 'admin'); ?>',
            eventName: '.notification.received',
            usePusher: <?php echo e(config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key') ? 'true' : 'false'); ?>,
            pusherKey: '<?php echo e(config('broadcasting.connections.pusher.key')); ?>',
            pusherCluster: '<?php echo e(config('broadcasting.connections.pusher.options.cluster')); ?>',
        };

        if (window.NotificationConfig.usePusher && window.Pusher && typeof Echo !== 'undefined') {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: window.NotificationConfig.pusherKey,
                cluster: window.NotificationConfig.pusherCluster,
                forceTLS: true,
                encrypted: true,
                disableStats: true,
            });
        }
    </script>
    <script src="<?php echo e(asset('js/notification-poller.js')); ?>" defer></script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.jsx']); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/layouts/admin.blade.php ENDPATH**/ ?>