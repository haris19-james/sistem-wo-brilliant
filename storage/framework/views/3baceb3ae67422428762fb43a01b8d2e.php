<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Client'); ?> - Brilliant WO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo $__env->make('partials.brand-tailwind', ['extraColors' => ['grayBg' => '#F8FAFC', 'grayText' => '#64748B']], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="<?php echo e(asset('js/notification-bell.js')); ?>?v=2"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    <?php echo $__env->yieldPushContent('head'); ?>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden" data-brilliant-panel="client" x-data="{ sidebarOpen: false, profileOpen: false }">

    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display:none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0">
        <div class="h-20 flex items-center justify-center border-b border-gray-50 px-4">
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
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <?php
                $active = $activeMenu ?? '';
                $navClass = fn ($key) => $active === $key
                    ? 'flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl'
                    : 'flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition';
            ?>
            <a href="<?php echo e(route('client.dashboard')); ?>" class="<?php echo e($navClass('dashboard')); ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="<?php echo e(route('client.booking.create')); ?>" class="<?php echo e($navClass('booking')); ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Booking Baru
            </a>
            <a href="<?php echo e(route('client.pesanan')); ?>" class="<?php echo e($navClass('pesanan')); ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                Pesanan Saya
            </a>
            <a href="<?php echo e(route('client.vendor-ratings.index')); ?>" class="<?php echo e($navClass('vendor-ratings')); ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                Vendor Ratings
            </a>
            <?php if (isset($component)) { $__componentOriginal98cd7d972020ef776f568d0ac7c195c3 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal98cd7d972020ef776f568d0ac7c195c3 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.sidebar.jadwal-acara-nav','data' => ['panel' => 'client','activeMenu' => $active,'rundownUrl' => route('client.jadwal'),'meetingUrl' => route('client.jadwal', ['section' => 'meetings']).'#vendor-meetings','rundownLocked' => $jadwalNavRundownLocked ?? true,'meetingLocked' => $jadwalNavMeetingLocked ?? true,'lockHint' => $jadwalNavLockHint ?? null]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('sidebar.jadwal-acara-nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['panel' => 'client','active-menu' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($active),'rundown-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('client.jadwal')),'meeting-url' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(route('client.jadwal', ['section' => 'meetings']).'#vendor-meetings'),'rundown-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jadwalNavRundownLocked ?? true),'meeting-locked' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jadwalNavMeetingLocked ?? true),'lock-hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($jadwalNavLockHint ?? null)]); ?>
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
            <a href="<?php echo e(route('client.pembayaran')); ?>" class="<?php echo e($navClass('pembayaran')); ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                Pembayaran
            </a>
            <a href="<?php echo e(route('client.chat')); ?>" class="<?php echo e($navClass('chat')); ?>">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Chat
                <?php if(($customerChatUnread ?? 0) > 0): ?>
                <span class="ml-auto bg-green-600 text-white text-[10px] font-bold min-w-[1.25rem] h-5 px-1.5 rounded-full flex items-center justify-center">
                    <?php echo e($customerChatUnread > 99 ? '99+' : $customerChatUnread); ?>

                </span>
                <?php endif; ?>
            </a>
            <a href="<?php echo e(route('client.profile')); ?>" class="<?php echo e($navClass('profile')); ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Profil Saya
            </a>
            <a href="<?php echo e(route('client.profile.edit')); ?>" class="<?php echo e($navClass('settings')); ?>">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan Akun
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
        <header class="bg-white border-b border-gray-100 h-16 px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-gray-900"><?php echo $__env->yieldContent('page-title', 'Panel Client'); ?></h2>
                    <p class="text-xs text-gray-500"><?php echo $__env->yieldContent('page-subtitle', ''); ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
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
            <div class="relative" @click.away="profileOpen = false">
                <button @click="profileOpen = !profileOpen" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-bottle">
                    <span class="w-9 h-9 rounded-full bg-leafSoft text-bottle flex items-center justify-center font-bold"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></span>
                    <span class="hidden sm:inline"><?php echo e(auth()->user()->name); ?></span>
                </button>
                <div x-show="profileOpen" x-transition class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50" style="display:none;">
                    <a href="<?php echo e(route('client.profile')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil Saya</a>
                    <a href="<?php echo e(route('client.profile.edit')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pengaturan Akun</a>
                    <hr class="my-1 border-gray-100">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                    </form>
                </div>
            </div>
            </div>
        </header>

        <main id="app-main" class="flex-1 overflow-y-auto p-6 lg:p-8">
            <?php if(session('success')): ?>
                <div class="mb-6 p-4 bg-green-50 text-green-800 border border-green-200 rounded-xl text-sm"><?php echo e(session('success')); ?></div>
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

    <?php echo $__env->make('components.page-nav-skeleton', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <script src="<?php echo e(asset('js/brilliant-nav-loading.js')); ?>?v=3" defer></script>
    <script src="<?php echo e(asset('js/page-nav.js')); ?>?v=2" defer></script>
    <?php if(config('broadcasting.default') === 'pusher' && config('broadcasting.connections.pusher.key')): ?>
        <script src="https://js.pusher.com/8.0/pusher.min.js" defer></script>
        <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.12.0/dist/echo.iife.js" defer></script>
    <?php endif; ?>
    <script>
        window.NotificationConfig = {
            pollUrl: '<?php echo e(route('api.notifications.poll')); ?>',
            countUrl: '<?php echo e(route('api.notifications.count')); ?>',
            roleChannel: 'notifications.<?php echo e(auth()->user()?->role ?? 'client'); ?>',
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
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/layouts/customer.blade.php ENDPATH**/ ?>