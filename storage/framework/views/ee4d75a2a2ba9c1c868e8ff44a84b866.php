<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'panel' => 'admin',
    'activeMenu' => '',
    'rundownUrl' => '#',
    'meetingUrl' => '#',
    'rundownLocked' => false,
    'meetingLocked' => false,
    'lockHint' => null,
    'linkActiveClass' => 'flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl',
    'linkIdleClass' => 'flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition',
    'subActiveClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm bg-leafSoft/80 text-bottle font-semibold rounded-lg',
    'subIdleClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm text-grayText hover:bg-gray-50 hover:text-bottle rounded-lg transition',
    'subLockedClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm text-gray-400 rounded-lg cursor-not-allowed select-none',
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
    'panel' => 'admin',
    'activeMenu' => '',
    'rundownUrl' => '#',
    'meetingUrl' => '#',
    'rundownLocked' => false,
    'meetingLocked' => false,
    'lockHint' => null,
    'linkActiveClass' => 'flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl',
    'linkIdleClass' => 'flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition',
    'subActiveClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm bg-leafSoft/80 text-bottle font-semibold rounded-lg',
    'subIdleClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm text-grayText hover:bg-gray-50 hover:text-bottle rounded-lg transition',
    'subLockedClass' => 'flex items-center pl-11 pr-4 py-2.5 text-sm text-gray-400 rounded-lg cursor-not-allowed select-none',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $jadwalMenus = ['jadwal', 'jadwal-rundown', 'jadwal-meeting', 'vendor-meetings'];
    $isJadwalGroupOpen = in_array($activeMenu, $jadwalMenus, true);
    $isParentActive = $isJadwalGroupOpen;
    $parentBtnClass = $isParentActive ? $linkActiveClass : $linkIdleClass;
?>

<div class="jadwal-acara-nav" data-jadwal-nav data-initial-open="<?php echo e($isJadwalGroupOpen ? '1' : '0'); ?>">
    <button type="button"
            class="<?php echo e($parentBtnClass); ?> w-full text-left jadwal-acara-nav__toggle"
            aria-expanded="<?php echo e($isJadwalGroupOpen ? 'true' : 'false'); ?>"
            aria-controls="jadwal-acara-submenu-<?php echo e($panel); ?>">
        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <span class="flex-1">Jadwal Acara</span>
        <svg class="w-4 h-4 shrink-0 jadwal-acara-nav__chevron transition-transform duration-300 <?php echo e($isJadwalGroupOpen ? 'rotate-180' : ''); ?>"
             fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <div id="jadwal-acara-submenu-<?php echo e($panel); ?>"
         class="jadwal-acara-nav__submenu overflow-hidden transition-[max-height] duration-300 ease-in-out <?php echo e($isJadwalGroupOpen ? 'is-open' : ''); ?>"
         <?php if($isJadwalGroupOpen): ?> style="max-height: 8rem;" <?php else: ?> style="max-height: 0;" <?php endif; ?>>
        <div class="mt-1 space-y-0.5 pb-1">
            <?php if($rundownLocked): ?>
                <span class="<?php echo e($subLockedClass); ?>" title="<?php echo e($lockHint ?? 'Terkunci — lunasi pembayaran untuk akses rundown'); ?>">
                    <svg class="w-3.5 h-3.5 mr-2 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Rundown Hari-H
                </span>
            <?php else: ?>
                <a href="<?php echo e($rundownUrl); ?>"
                   class="<?php echo e(in_array($activeMenu, ['jadwal', 'jadwal-rundown'], true) ? $subActiveClass : $subIdleClass); ?> jadwal-acara-nav__link"
                   data-loading-message="Menyiapkan seluruh rangkaian rundown acara...">
                    Rundown Hari-H
                </a>
            <?php endif; ?>

            <?php if($meetingLocked): ?>
                <span class="<?php echo e($subLockedClass); ?>" title="<?php echo e($lockHint ?? 'Terkunci'); ?>">
                    <svg class="w-3.5 h-3.5 mr-2 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    Jadwal Meeting Vendor
                </span>
            <?php else: ?>
                <a href="<?php echo e($meetingUrl); ?>"
                   class="<?php echo e(in_array($activeMenu, ['jadwal-meeting', 'vendor-meetings'], true) ? $subActiveClass : $subIdleClass); ?> jadwal-acara-nav__link"
                   data-loading-message="Memuat jadwal meeting vendor...">
                    Jadwal Meeting Vendor
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (! $__env->hasRenderedOnce('0a267f79-a47d-45e7-a641-623c922e823b')): $__env->markAsRenderedOnce('0a267f79-a47d-45e7-a641-623c922e823b'); ?>
    <?php $__env->startPush('scripts'); ?>
        <script src="<?php echo e(asset('js/sidebar-jadwal-acara.js')); ?>?v=2" defer></script>
    <?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/sidebar/jadwal-acara-nav.blade.php ENDPATH**/ ?>