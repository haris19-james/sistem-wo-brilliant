<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'pesanan' => null,
    'invoice' => null,
    'panel' => 'client',
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
    'pesanan' => null,
    'invoice' => null,
    'panel' => 'client',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $paymentStatus = $pesanan?->status_pembayaran ?? 'unpaid';
    $akses = $pesanan?->akses_jadwal ?? 'none';
    $invoiceStatus = $invoice?->status ?? null;
    $pendingKonfirmasi = $invoice?->konfirmasiPending ?? null;

    $statusLabel = match (true) {
        $pendingKonfirmasi !== null => 'Menunggu Verifikasi Admin',
        $paymentStatus === 'fully_paid' => 'Lunas',
        $paymentStatus === 'dp_paid' => 'DP Terverifikasi',
        $invoiceStatus === 'DP Lunas' => 'DP Lunas (Invoice)',
        default => 'Belum Bayar',
    };

    $statusClass = match (true) {
        str_contains($statusLabel, 'Menunggu') => 'bg-amber-50 text-amber-800 border-amber-200',
        $paymentStatus === 'fully_paid' => 'bg-green-50 text-green-800 border-green-200',
        $paymentStatus === 'dp_paid' => 'bg-yellow-50 text-yellow-800 border-yellow-200',
        default => 'bg-red-50 text-red-700 border-red-200',
    };

    $aksesLabel = match ($akses) {
        'full' => 'Jadwal Terbuka Penuh',
        'partial' => 'Jadwal Parsial (Persiapan Awal)',
        default => 'Jadwal Terkunci',
    };

    $aksesIcon = match ($akses) {
        'full' => 'unlock',
        'partial' => 'half',
        default => 'lock',
    };
?>

<div <?php echo e($attributes->merge(['class' => 'bg-white rounded-2xl border border-gray-100 p-5 shadow-sm'])); ?>>
    <div class="flex items-start justify-between gap-3 mb-4">
        <div>
            <h3 class="text-sm font-bold text-gray-900">Status Pembayaran &amp; Akses Jadwal</h3>
            <p class="text-xs text-gray-500 mt-0.5">Workflow DP → Pelunasan → Eksekusi Lapangan</p>
        </div>
        <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold border <?php echo e($statusClass); ?>">
            <?php echo e($statusLabel); ?>

        </span>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
        <div class="rounded-xl border border-gray-100 p-3 bg-gray-50">
            <p class="text-xs text-gray-500 mb-1">Status Pembayaran</p>
            <p class="font-semibold text-gray-900"><?php echo e($pesanan?->status_pembayaran_label ?? $statusLabel); ?></p>
            <?php if($invoice): ?>
            <p class="text-xs text-gray-500 mt-1">Invoice: <?php echo e($invoice->status); ?></p>
            <?php endif; ?>
        </div>
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses([
            'rounded-xl border p-3',
            'border-green-200 bg-leafSoft' => $akses === 'full',
            'border-yellow-200 bg-yellow-50' => $akses === 'partial',
            'border-gray-200 bg-gray-50' => $akses === 'none',
        ]); ?>">
            <p class="text-xs text-gray-500 mb-1 flex items-center gap-1">
                <?php if($aksesIcon === 'lock'): ?>
                <svg class="w-3.5 h-3.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <?php elseif($aksesIcon === 'unlock'): ?>
                <svg class="w-3.5 h-3.5 text-bottle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                <?php else: ?>
                <svg class="w-3.5 h-3.5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <?php endif; ?>
                Akses Jadwal
            </p>
            <p class="font-semibold text-gray-900"><?php echo e($aksesLabel); ?></p>
            <?php if($akses === 'partial'): ?>
            <p class="text-[11px] text-yellow-700 mt-1">Vendor eksternal &amp; rundown hari-H terkunci hingga pelunasan.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php if($panel === 'client' && $invoice && $invoice->status !== 'Lunas' && ! $pendingKonfirmasi): ?>
    <div class="mt-4 pt-4 border-t border-gray-100">
        <a href="<?php echo e(route('client.pembayaran.create', $invoice)); ?>"
           class="inline-flex items-center gap-2 px-4 py-2 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover">
            Upload Bukti Transfer
        </a>
    </div>
    <?php endif; ?>

    <?php if($pendingKonfirmasi): ?>
    <p class="text-xs text-amber-700 mt-3 flex items-center gap-1.5">
        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        Bukti transfer sedang diverifikasi admin (<?php echo e($pendingKonfirmasi->jenis_pembayaran); ?> — Rp <?php echo e(number_format($pendingKonfirmasi->jumlah, 0, ',', '.')); ?>).
    </p>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/payment-status-card.blade.php ENDPATH**/ ?>