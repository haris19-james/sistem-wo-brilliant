<?php
    $status = $task->status;
    $borderClass = match ($status) {
        'pending' => 'border-green-100',
        'in_progress', 'awaiting_verification' => 'border-green-200',
        'completed' => 'border-green-300',
        default => 'border-gray-100',
    };
    $badgeClass = match ($status) {
        'pending' => 'bg-gray-100 text-gray-700',
        'in_progress' => 'bg-green-50 text-green-700',
        'awaiting_verification' => 'bg-amber-50 text-amber-800 border border-amber-200',
        'completed' => 'bg-green-100 text-green-800',
        default => 'bg-gray-100 text-gray-600',
    };
?>

<div class="task-card bg-white border <?php echo e($borderClass); ?> rounded-2xl p-5 shadow-sm hover:shadow-md transition"
     data-task-id="<?php echo e($task->id); ?>"
     data-task-name="<?php echo e($task->nama_tugas); ?>"
     data-pesanan-id="<?php echo e($task->pesanan_id); ?>"
     data-vendor-id="<?php echo e($task->vendor_id); ?>"
     data-status="<?php echo e($status); ?>"
     data-prioritas="<?php echo e($task->prioritas); ?>">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2 mb-1">
                <h3 class="text-base font-bold text-gray-900 truncate"><?php echo e($task->nama_tugas); ?></h3>
                <?php if($task->is_auto_generated): ?>
                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-green-50 text-green-600 border border-green-100">Rutin</span>
                <?php else: ?>
                <span class="text-[10px] font-semibold px-1.5 py-0.5 rounded bg-blue-50 text-blue-600 border border-blue-100">Ad-hoc</span>
                <?php endif; ?>
            </div>
            <p class="text-xs text-gray-500"><?php echo e($task->pesanan?->nama_pasangan ?? '—'); ?> · <?php echo e($task->pesanan?->nomor_pesanan); ?></p>
            <?php if($task->vendor): ?>
            <p class="text-xs text-green-600 font-medium mt-0.5"><?php echo e($task->vendor->nama_vendor); ?> (<?php echo e($task->vendor->kategori); ?>)</p>
            <?php endif; ?>
        </div>
        <span data-status-badge class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($badgeClass); ?>">
            <?php echo e($task->status_label); ?>

        </span>
    </div>

    <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-gray-600">
        <div>
            <p class="font-semibold text-gray-800">Deadline</p>
            <p><?php echo e(optional($task->deadline)->format('d M Y H:i') ?? '—'); ?></p>
        </div>
        <div>
            <p class="font-semibold text-gray-800">PIC</p>
            <p><?php echo e($task->pic?->name ?? '—'); ?></p>
        </div>
        <div class="col-span-2">
            <p class="font-semibold text-gray-800">Progress</p>
            <p class="text-green-600 font-semibold"><?php echo e($task->progress); ?>%</p>
        </div>
    </div>

    <?php if($status === 'in_progress' && !empty($task->alasan_penolakan)): ?>
    <div class="mt-3 p-3 bg-red-50 border border-red-100 rounded-lg text-xs">
        <p class="font-bold text-red-800 mb-1">Ditolak oleh Admin:</p>
        <p class="text-red-700"><?php echo e($task->alasan_penolakan); ?></p>
    </div>
    <?php endif; ?>

    <?php if($status !== 'completed'): ?>
    <div class="mt-3 flex flex-wrap items-center gap-2">
        <select class="task-status-select text-xs px-2 py-1.5 border border-gray-200 rounded-lg focus:border-green-500 focus:outline-none"
            data-task-id="<?php echo e($task->id); ?>" data-prev-status="<?php echo e($status); ?>">
            <option value="pending" <?php if($status === 'pending'): echo 'selected'; endif; ?>>Belum Dikerjakan</option>
            <option value="in_progress" <?php if($status === 'in_progress'): echo 'selected'; endif; ?>>Sedang Dikerjakan</option>
        </select>

        <?php if($status === 'in_progress'): ?>
        <button type="button" class="px-3 py-1.5 bg-bottle hover:bg-bottleHover text-white text-xs font-semibold rounded-lg transition"
            onclick="openUploadModal('<?php echo e($task->id); ?>')">
            Kirim Laporan
        </button>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <p class="mt-2 text-[10px] text-green-600">✓ Selesai</p>
    <?php endif; ?>

    <div class="mt-3 flex items-center justify-between gap-2 border-t border-gray-50 pt-2">
        <p class="text-xs text-gray-500 flex-1"><?php echo e(Str::limit($task->catatan ?? '—', 60)); ?></p>
        <a href="<?php echo e(route('lapangan.tugas.edit', $task)); ?>" class="text-xs font-semibold text-green-600 hover:text-green-700">Detail</a>
    </div>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/partials/task-card.blade.php ENDPATH**/ ?>