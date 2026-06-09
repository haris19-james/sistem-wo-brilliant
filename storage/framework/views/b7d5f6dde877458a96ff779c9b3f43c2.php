<?php
    /** @var \App\Models\LaporanLapangan $k */
    $isAktif = $k->isKendalaAktif();
?>
<div class="px-6 py-4 flex flex-col sm:flex-row sm:items-start gap-4 <?php echo e($isAktif ? '' : 'bg-green-50/30'); ?>"
     data-kendala-row="<?php echo e($k->id); ?>"
     data-kendala-aktif="<?php echo e($isAktif ? '1' : '0'); ?>">
    <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-1">
            <span class="px-2 py-0.5 rounded text-xs font-bold border <?php echo e($k->kondisi_badge_class); ?>"><?php echo e($k->kondisi); ?></span>
            <span class="px-2 py-0.5 rounded text-xs font-bold border kendala-status-badge <?php echo e($k->status_tindak_badge_class); ?>"><?php echo e($k->status_tindak); ?></span>
            <span class="text-xs text-gray-500"><?php echo e($k->pesanan?->nomor_pesanan); ?> · <?php echo e($k->pesanan?->nama_pasangan); ?></span>
        </div>
        <p class="text-sm text-gray-900 kendala-ringkasan"><?php echo e($k->ringkasan); ?></p>
        <p class="text-xs text-gray-500 mt-1">Dilaporkan <?php echo e($k->user?->name ?? 'Klien'); ?> · <?php echo e($k->created_at?->diffForHumans()); ?></p>
        <?php if($k->tindak_lanjut && $k->status_tindak === 'Selesai'): ?>
        <p class="text-xs text-green-800 mt-2 p-2 bg-green-50 rounded-lg border border-green-100 kendala-solusi">
            <span class="font-semibold">Solusi:</span> <?php echo e($k->tindak_lanjut); ?>

        </p>
        <?php endif; ?>
    </div>
    <?php if($isAktif): ?>
    <div class="flex flex-wrap gap-2 shrink-0 kendala-actions">
        <?php if($k->status_tindak === 'Menunggu Tindakan'): ?>
        <button type="button"
                class="btn-admin-kendala-tangani px-3 py-1.5 text-xs font-semibold rounded-lg border border-amber-300 text-amber-800 hover:bg-amber-50"
                data-kendala-id="<?php echo e($k->id); ?>">
            Tangani
        </button>
        <?php endif; ?>
        <button type="button"
                class="btn-admin-kendala-selesaikan px-3 py-1.5 text-xs font-semibold rounded-lg bg-green-600 text-white hover:bg-green-700"
                data-kendala-id="<?php echo e($k->id); ?>"
                data-ringkasan="<?php echo e(e($k->ringkasan)); ?>">
            Selesaikan
        </button>
        <?php if($k->pesanan): ?>
        <a href="<?php echo e(route('admin.booking.show', $k->pesanan_id)); ?>" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50">Detail</a>
        <?php endif; ?>
    </div>
    <?php elseif($k->pesanan): ?>
    <a href="<?php echo e(route('admin.booking.show', $k->pesanan_id)); ?>" class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-gray-200 text-gray-700 hover:bg-gray-50 shrink-0">Detail</a>
    <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/admin/modules/partials/kendala-row.blade.php ENDPATH**/ ?>