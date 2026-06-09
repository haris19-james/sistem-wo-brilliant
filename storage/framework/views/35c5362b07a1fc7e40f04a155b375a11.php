<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['align' => 'right']));

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

foreach (array_filter((['align' => 'right']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $golden = config('brilliant.colors.golden', '#D4A017');
    $initialNotifications = collect($bellNotifications ?? [])->values();
    $initialUnread = (int) ($bellUnreadCount ?? 0);
    $viewAllRoute = auth()->user()?->role === 'admin'
        ? route('admin.notifications.index')
        : route('notifications.index');
?>

<div class="relative shrink-0 notification-bell-root" x-data="notificationBell()" x-init="init(<?php echo \Illuminate\Support\Js::from($initialUnread)->toHtml() ?>, <?php echo \Illuminate\Support\Js::from($initialNotifications)->toHtml() ?>)" @click.outside="open = false">
    <button type="button"
        @click="toggleDropdown()"
        class="notification-bell-btn relative inline-flex items-center justify-center w-10 h-10 rounded-lg transition hover:bg-goldenSoft"
        style="color: <?php echo e($golden); ?>;"
        aria-label="Notifikasi"
        aria-expanded="false"
        :aria-expanded="open"
        title="Notifikasi">
        <svg class="w-6 h-6 drop-shadow-sm" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
            <path d="M12 22a2.5 2.5 0 002.45-2h-4.9A2.5 2.5 0 0012 22zm7-6V11a7 7 0 10-14 0v5l-2 2v1h18v-1l-2-2z"/>
        </svg>
        <?php if($initialUnread > 0): ?>
        <span class="notification-bell-badge absolute -top-0.5 -right-0.5 flex min-w-[1.25rem] h-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white"
            x-show="unreadCount > 0"
            x-text="unreadCount > 99 ? '99+' : unreadCount"><?php echo e($initialUnread > 99 ? '99+' : $initialUnread); ?></span>
        <?php else: ?>
        <span x-show="unreadCount > 0" x-cloak
            class="notification-bell-badge absolute -top-0.5 -right-0.5 flex min-w-[1.25rem] h-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white"
            x-text="unreadCount > 99 ? '99+' : unreadCount"></span>
        <?php endif; ?>
    </button>

    <div x-show="open" x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="notification-bell-dropdown absolute <?php echo e($align === 'left' ? 'left-0' : 'right-0'); ?> top-full mt-2 w-[26rem] max-w-[calc(100vw-2rem)] bg-white rounded-xl border border-gray-100 shadow-2xl z-[60] overflow-hidden">

        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <div>
                <h3 class="text-xs font-bold text-gray-900 tracking-[0.12em]">NOTIFICATIONS</h3>
                <p class="text-xs text-gray-500 mt-1"><span x-text="unreadCount"></span> unread</p>
            </div>
            <button type="button"
                @click="markAllRead()"
                x-show="unreadCount > 0"
                class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition whitespace-nowrap">
                Mark all as read
            </button>
        </div>

        <div class="max-h-[28rem] overflow-y-auto">
            <template x-if="loading && notifications.length === 0">
                <div class="px-5 py-10 text-center text-sm text-gray-500">Memuat notifikasi...</div>
            </template>

            <template x-if="!loading && notifications.length === 0">
                <div class="px-5 py-12 text-center">
                    <p class="text-gray-500 text-sm font-medium">Belum ada notifikasi</p>
                </div>
            </template>

            <template x-for="notification in notifications" :key="notification.id">
                <a href="#"
                    @click.prevent="openItem(notification)"
                    class="notification-bell-item flex items-start gap-3 px-5 py-4 border-b border-gray-50 transition"
                    :class="notification.is_read ? 'bg-white hover:bg-gray-50' : 'notification-bell-item--unread'">

                    <span class="mt-2 shrink-0 w-2.5">
                        <span x-show="!notification.is_read" class="inline-flex h-2.5 w-2.5 rounded-sm bg-blue-500"></span>
                    </span>

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white border border-gray-100 text-gray-600">
                        <template x-if="notification.category_icon === 'credit-card'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </template>
                        <template x-if="notification.category_icon === 'calendar'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </template>
                        <template x-if="notification.category_icon === 'clipboard'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        </template>
                        <template x-if="notification.category_icon === 'chat'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                        </template>
                        <template x-if="notification.category_icon === 'alert'">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </template>
                        <template x-if="!['credit-card','calendar','clipboard','chat','alert'].includes(notification.category_icon)">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </template>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="text-sm leading-relaxed text-gray-800" :class="notification.is_read ? '' : 'font-medium'">
                            <span x-html="notification.display_message || notification.message"></span>
                            <span class="text-gray-400" x-text="' — ' + (notification.formatted_time || '')"></span>
                        </p>
                    </div>
                </a>
            </template>
        </div>

        <div class="px-5 py-3 border-t border-gray-100 bg-white text-center">
            <a href="<?php echo e($viewAllRoute); ?>"
               class="text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                View All Notifications
            </a>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/notification-bell.blade.php ENDPATH**/ ?>