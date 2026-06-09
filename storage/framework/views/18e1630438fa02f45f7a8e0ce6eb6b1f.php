<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Tim Lapangan'); ?> - Brilliant WO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo $__env->make('partials.brand-tailwind', ['extraColors' => [
        'field' => config('brilliant.colors.bottle'),
        'fieldHover' => config('brilliant.colors.bottle_hover'),
        'grayBg' => '#F8FAFC',
        'grayText' => '#64748B',
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/lapangan-panel.css')); ?>">
    <script src="<?php echo e(asset('js/notification-bell.js')); ?>?v=2"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-slate-50 font-sans antialiased text-gray-800 flex h-screen overflow-hidden relative" x-data="{ sidebarOpen: false }">

<div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display:none;"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static">
    <div class="flex items-center justify-center h-20 border-b border-gray-200 px-6 bg-white">
        <a href="<?php echo e(route('lapangan.dashboard')); ?>" class="flex items-center space-x-2">
            <svg class="w-8 h-8 text-bottle" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
            <div class="leading-tight">
                <h1 class="text-sm font-bold text-gray-900">Brilliant</h1>
                <p class="text-[0.65rem] uppercase tracking-widest text-gray-500">Event Organizer</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
        <?php
            $active = $activeMenu ?? '';
            $link = fn ($key) => $active === $key
                ? 'flex items-center px-4 py-3 lp-sidebar-link--active font-semibold rounded-lg'
                : 'flex items-center px-4 py-3 lp-sidebar-link font-medium rounded-lg transition';
        ?>
        <a href="<?php echo e(route('lapangan.dashboard')); ?>" class="<?php echo e($link('dashboard')); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="<?php echo e(route('lapangan.pesanan.index')); ?>" class="<?php echo e($link('pesanan')); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Pemesanan
        </a>
        <a href="<?php echo e(route('lapangan.vendor')); ?>" class="<?php echo e($link('vendor')); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 20H9m8-4a3 3 0 01-6 0m6 0a3 3 0 00-6 0m6 0H9m6 0a3 3 0 00-6 0"/></svg>
            Vendor
        </a>
        <?php if (isset($component)) { $__componentOriginal98cd7d972020ef776f568d0ac7c195c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98cd7d972020ef776f568d0ac7c195c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar.jadwal-acara-nav','data' => ['panel' => 'lapangan','activeMenu' => $active,'rundownUrl' => route('lapangan.jadwal'),'meetingUrl' => route('lapangan.jadwal', ['section' => 'meetings']).'#vendor-meetings','rundownLocked' => $jadwalNavRundownLocked ?? true,'meetingLocked' => $jadwalNavMeetingLocked ?? true,'lockHint' => $jadwalNavLockHint ?? null,'linkActiveClass' => 'flex items-center px-4 py-3 lp-sidebar-link--active font-semibold rounded-lg','linkIdleClass' => 'flex items-center px-4 py-3 lp-sidebar-link font-medium rounded-lg transition','subActiveClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm lp-sidebar-link--active font-semibold rounded-lg','subIdleClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm lp-sidebar-link rounded-lg transition','subLockedClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm text-gray-400 rounded-lg cursor-not-allowed select-none']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar.jadwal-acara-nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['panel' => 'lapangan','active-menu' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($active),'rundown-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('lapangan.jadwal')),'meeting-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('lapangan.jadwal', ['section' => 'meetings']).'#vendor-meetings'),'rundown-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jadwalNavRundownLocked ?? true),'meeting-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jadwalNavMeetingLocked ?? true),'lock-hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jadwalNavLockHint ?? null),'link-active-class' => 'flex items-center px-4 py-3 lp-sidebar-link--active font-semibold rounded-lg','link-idle-class' => 'flex items-center px-4 py-3 lp-sidebar-link font-medium rounded-lg transition','sub-active-class' => 'flex items-center pl-11 pr-4 py-2.5 text-sm lp-sidebar-link--active font-semibold rounded-lg','sub-idle-class' => 'flex items-center pl-11 pr-4 py-2.5 text-sm lp-sidebar-link rounded-lg transition','sub-locked-class' => 'flex items-center pl-11 pr-4 py-2.5 text-sm text-gray-400 rounded-lg cursor-not-allowed select-none']); ?>
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
        <a href="<?php echo e(route('lapangan.tugas.index')); ?>" class="<?php echo e($link('tugas')); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            Tugas
        </a>
        <a href="<?php echo e(route('lapangan.chat')); ?>" class="<?php echo e($link('chat')); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Chat / Pesan
        </a>
        <a href="<?php echo e(route('lapangan.laporan')); ?>" class="<?php echo e($link('laporan')); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Laporan
        </a>
        <a href="<?php echo e(route('lapangan.pengaturan')); ?>" class="<?php echo e($link('pengaturan')); ?>">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
    </nav>

    <div class="p-4 border-t border-gray-200">
        <form method="POST" action="<?php echo e(route('logout')); ?>">
            <?php echo csrf_field(); ?>
            <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-red-600 font-medium rounded-lg transition text-sm">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<div class="flex-1 flex flex-col overflow-hidden min-h-0 relative z-10">
    <header class="bg-white border-b border-gray-200 px-4 lg:px-8 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-4 py-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-sm font-medium text-gray-700"><?php echo $__env->yieldContent('header-date', 'Tanggal'); ?></span>
                </div>
                <?php if (isset($component)) { $__componentOriginal6541145ad4a57bfb6e6f221ba77eb386 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6541145ad4a57bfb6e6f221ba77eb386 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.notification-bell','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('notification-bell'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6541145ad4a57bfb6e6f221ba77eb386)): ?>
<?php $attributes = $__attributesOriginal6541145ad4a57bfb6e6f221ba77eb386; ?>
<?php unset($__attributesOriginal6541145ad4a57bfb6e6f221ba77eb386); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6541145ad4a57bfb6e6f221ba77eb386)): ?>
<?php $component = $__componentOriginal6541145ad4a57bfb6e6f221ba77eb386; ?>
<?php unset($__componentOriginal6541145ad4a57bfb6e6f221ba77eb386); ?>
<?php endif; ?>
            </div>
            <div class="flex items-center gap-3 pl-6 border-l border-gray-200">
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900"><?php echo e(auth()->user()->name ?? 'Korlap'); ?></p>
                    <p class="text-xs text-gray-500">Koordinator Lapangan</p>
                </div>
                <button class="w-10 h-10 lp-icon-wrap rounded-full flex items-center justify-center font-bold hover:bg-leafSoft transition">
                    <?php echo e(substr(auth()->user()->name ?? 'K', 0, 1)); ?>

                </button>
            </div>
        </div>
    </header>

    <main id="app-main" class="flex-1 overflow-y-auto min-h-0 relative z-10">
        <?php if(session('success') || session('error')): ?>
        <div class="container mx-auto px-6 pt-4">
            <?php if(session('success')): ?>
            <div class="mb-4 p-4 bg-leafSoft border border-leaf rounded-xl text-bottle text-sm"><?php echo e(session('success')); ?></div>
            <?php endif; ?>
            <?php if(session('error')): ?>
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
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
<?php echo $__env->make('components.floral-decoration', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php echo $__env->make('components.wedding-decoration', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php echo $__env->make('components.page-nav-skeleton', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<script src="<?php echo e(asset('js/page-nav.js')); ?>" defer></script>
<?php if(config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key')): ?>
    <script src="https://js.pusher.com/8.0/pusher.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.12.0/dist/echo.iife.js" defer></script>
<?php endif; ?>
<script>
    window.NotificationConfig = {
        pollUrl: '<?php echo e(route('api.notifications.poll')); ?>',
        countUrl: '<?php echo e(route('api.notifications.count')); ?>',
        roleChannel: 'notifications.<?php echo e(auth()->user()?->role ?? 'lapangan'); ?>',
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
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/layouts/lapangan.blade.php ENDPATH**/ ?>