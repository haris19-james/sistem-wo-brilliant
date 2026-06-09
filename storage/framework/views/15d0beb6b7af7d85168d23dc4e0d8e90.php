

<?php $__env->startSection('title', 'Pesanan Saya'); ?>
<?php $__env->startSection('page-title', 'Pesanan Saya'); ?>
<?php $__env->startSection('page-subtitle', 'Semua booking pernikahan Anda'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if(session('error')): ?>
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm"><?php echo e(session('error')); ?></div>
<?php endif; ?>
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-600"><?php echo e($daftarPesanan->count()); ?> pesanan</p>
    <a href="<?php echo e(route('client.booking.create')); ?>" class="px-5 py-2.5 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover">+ Booking Baru</a>
</div>

<div class="space-y-4">
    <?php $__empty_1 = true; $__currentLoopData = $daftarPesanan; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex flex-col md:flex-row gap-4">
        <?php if($p->paket?->image_url): ?>
        <img src="<?php echo e($p->paket->image_url); ?>" class="w-full md:w-36 h-28 object-cover rounded-xl shrink-0" alt="">
        <?php endif; ?>
        <div class="flex-1">
            <p class="text-xs font-semibold text-bottle"><?php echo e($p->nomor_pesanan); ?></p>
            <h3 class="text-lg font-bold text-gray-900"><?php echo e($p->nama_pasangan); ?></h3>
            <p class="text-sm text-gray-600"><?php echo e($p->paket?->nama_paket); ?> · <?php echo e($p->tanggal_formatted); ?> · <?php echo e(substr($p->jam_acara, 0, 5)); ?></p>
            <p class="text-sm text-gray-500"><?php echo e($p->lokasi); ?></p>
            <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs font-semibold <?php echo e($p->status_badge_class); ?>"><?php echo e($p->status_label); ?></span>
        </div>
        <div class="flex md:flex-col gap-2 shrink-0 justify-center">
            <a href="<?php echo e(route('client.pesanan_detail', $p->id)); ?>" class="px-4 py-2 text-sm font-semibold bg-bottle text-white rounded-lg text-center hover:bg-bottleHover">Detail</a>
            <?php if($p->status !== 'Dibatalkan'): ?>
            <a href="<?php echo e(route('client.chat.show', $p->id)); ?>" class="px-4 py-2 text-sm font-semibold border border-bottle text-bottle rounded-lg text-center hover:bg-leafSoft">Chat</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
        <p class="text-gray-500 mb-4">Belum ada pesanan.</p>
        <a href="<?php echo e(route('client.booking.create')); ?>" class="text-bottle font-semibold hover:underline">Buat booking pertama Anda</a>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/customer/modules/pesanan/index.blade.php ENDPATH**/ ?>