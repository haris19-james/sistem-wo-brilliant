<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['acara']));

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

foreach (array_filter((['acara']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $progress = (int) ($acara->progress?->persentase ?? 0);
    $progress = min(100, max(0, $progress));
    $clientName = $acara->user?->name ?? $acara->nama_pasangan;
    $detailUrl = route('lapangan.pesanan.show', $acara).'#rundown-acara';
?>

<a href="<?php echo e($detailUrl); ?>"
   class="lapangan-stat-detail group block rounded-2xl border lp-card bg-white p-5 shadow-sm transition-all hover:-translate-y-0.5"
   aria-label="Lihat rundown <?php echo e($clientName); ?>">
    <div class="flex items-start justify-between gap-3 mb-4">
        <div class="flex items-center gap-3 min-w-0">
            <div class="lp-icon-wrap flex h-11 w-11 shrink-0 items-center justify-center rounded-xl">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="min-w-0">
                <p class="truncate text-base font-bold text-slate-900 group-hover:text-bottle transition-colors"><?php echo e($clientName); ?></p>
                <p class="truncate text-xs text-slate-500 mt-0.5"><?php echo e($acara->paket?->nama_paket ?? 'Paket belum diatur'); ?></p>
            </div>
        </div>
        <span class="lp-badge inline-flex shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide">
            Hari H
        </span>
    </div>

    <div class="flex items-start gap-2 text-sm text-slate-600 mb-4">
        <svg class="h-4 w-4 shrink-0 text-bottle mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <span class="line-clamp-2"><?php echo e($acara->lokasi ?: 'Lokasi belum diisi'); ?></span>
    </div>

    <div>
        <div class="flex items-center justify-between text-xs mb-1.5">
            <span class="font-medium text-slate-600">Progress persiapan</span>
            <span class="font-bold text-bottle"><?php echo e($progress); ?>%</span>
        </div>
        <div class="h-2.5 rounded-full lp-progress-track overflow-hidden">
            <div class="h-full rounded-full lp-progress-fill transition-all duration-500" style="width: <?php echo e($progress); ?>%"></div>
        </div>
    </div>

    <p class="mt-4 text-xs font-semibold lp-link inline-flex items-center gap-1">
        Lihat rundown acara
        <svg class="h-3.5 w-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </p>
</a>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/lapangan/acara-hari-ini-card.blade.php ENDPATH**/ ?>