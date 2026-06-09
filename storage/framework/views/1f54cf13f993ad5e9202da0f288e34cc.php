<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Halo, '.auth()->user()->name.' 👋'); ?>
<?php $__env->startSection('page-subtitle', 'Ringkasan persiapan pernikahan Anda'); ?>

<?php $__env->startSection('content'); ?>
<?php if(session('success')): ?>
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-medium" role="status">
    <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<?php if(!empty($deadlineBanner)): ?>
<?php if (isset($component)) { $__componentOriginal1e2be9a4803edd37173f7d745a230dcb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1e2be9a4803edd37173f7d745a230dcb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.payment-alert-banner','data' => ['banner' => $deadlineBanner,'class' => 'mb-6']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('payment-alert-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['banner' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($deadlineBanner),'class' => 'mb-6']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1e2be9a4803edd37173f7d745a230dcb)): ?>
<?php $attributes = $__attributesOriginal1e2be9a4803edd37173f7d745a230dcb; ?>
<?php unset($__attributesOriginal1e2be9a4803edd37173f7d745a230dcb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1e2be9a4803edd37173f7d745a230dcb)): ?>
<?php $component = $__componentOriginal1e2be9a4803edd37173f7d745a230dcb; ?>
<?php unset($__componentOriginal1e2be9a4803edd37173f7d745a230dcb); ?>
<?php endif; ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginalc1251510d8d76c5a6f6986a0a3b5b834 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc1251510d8d76c5a6f6986a0a3b5b834 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.vendor-review-prompt-banner','data' => ['pendingReviews' => $pendingVendorReviews ?? collect(),'notifications' => $reviewNotifications ?? collect()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('vendor-review-prompt-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pending-reviews' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pendingVendorReviews ?? collect()),'notifications' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($reviewNotifications ?? collect())]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc1251510d8d76c5a6f6986a0a3b5b834)): ?>
<?php $attributes = $__attributesOriginalc1251510d8d76c5a6f6986a0a3b5b834; ?>
<?php unset($__attributesOriginalc1251510d8d76c5a6f6986a0a3b5b834); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc1251510d8d76c5a6f6986a0a3b5b834)): ?>
<?php $component = $__componentOriginalc1251510d8d76c5a6f6986a0a3b5b834; ?>
<?php unset($__componentOriginalc1251510d8d76c5a6f6986a0a3b5b834); ?>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <p class="text-sm text-gray-500">Total Pesanan</p>
        <p class="text-3xl font-bold text-gray-900"><?php echo e($stats['total_pesanan']); ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <p class="text-sm text-gray-500">Menunggu</p>
        <p class="text-3xl font-bold text-yellow-600"><?php echo e($stats['menunggu']); ?></p>
    </div>
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
        <p class="text-sm text-gray-500">Sedang Berlangsung</p>
        <p class="text-3xl font-bold text-bottle"><?php echo e($stats['berlangsung']); ?></p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-4">Pesanan Aktif</h3>
        <?php if($pesananAktif): ?>
        <div class="flex flex-col sm:flex-row gap-4">
            <?php if($pesananAktif->paket?->image_url): ?>
            <img src="<?php echo e($pesananAktif->paket->image_url); ?>" class="w-full sm:w-32 h-24 object-cover rounded-xl" alt="">
            <?php endif; ?>
            <div class="flex-1">
                <p class="text-sm text-bottle font-semibold"><?php echo e($pesananAktif->nomor_pesanan); ?></p>
                <h4 class="text-xl font-bold text-gray-900"><?php echo e($pesananAktif->nama_pasangan); ?></h4>
                <p class="text-sm text-gray-600"><?php echo e($pesananAktif->paket?->nama_paket); ?> · <?php echo e($pesananAktif->tanggal_formatted); ?></p>
                <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs font-semibold <?php echo e($pesananAktif->status_badge_class); ?>"><?php echo e($pesananAktif->status_label); ?></span>
                <div class="mt-4 flex flex-wrap gap-2">
                    <a href="<?php echo e(route('client.pesanan_detail', $pesananAktif->id)); ?>" class="text-sm font-semibold text-bottle hover:underline">Detail Pesanan</a>
                    <a href="<?php echo e(route('client.chat.show', $pesananAktif->id)); ?>" class="text-sm font-semibold text-bottle hover:underline">Chat Admin</a>
                </div>
            </div>
        </div>
        <div class="mt-6">
            <div class="flex justify-between text-sm mb-1"><span>Progress Persiapan</span><span class="font-bold text-bottle"><?php echo e($progressPersiapan); ?>%</span></div>
            <div class="w-full bg-gray-100 rounded-full h-2 mb-2"><div class="bg-bottle h-2 rounded-full" style="width: <?php echo e($progressPersiapan); ?>%"></div></div>
            <?php if($pesananAktif?->progress): ?>
            <a href="<?php echo e(route('client.jadwal', ['pesanan_id' => $pesananAktif->id])); ?>" class="text-xs font-semibold text-bottle hover:underline">Lihat jadwal & detail progress →</a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <p class="text-gray-500 text-sm mb-4">Anda belum memiliki pesanan aktif.</p>
        <a href="<?php echo e(route('client.booking.create')); ?>" class="inline-block px-5 py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover">Buat Booking Sekarang</a>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-900">Chat Terbaru</h3>
            <a href="<?php echo e(route('client.chat')); ?>" class="text-xs text-bottle font-semibold hover:underline">Lihat semua</a>
        </div>
        <div class="space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $notifikasiChat; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <a href="<?php echo e(route('client.chat.show', $msg->pesanan_id)); ?>" class="block p-3 rounded-xl bg-gray-50 hover:bg-leafSoft transition">
                <p class="text-xs text-gray-500"><?php echo e($msg->dari_admin ? 'Admin' : 'Anda'); ?> · <?php echo e($msg->pesanan?->nama_pasangan); ?></p>
                <p class="text-sm text-gray-800 line-clamp-2"><?php echo e($msg->pesan); ?></p>
                <p class="text-[10px] text-gray-400 mt-1"><?php echo e($msg->created_at->diffForHumans()); ?></p>
            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-sm text-gray-500">Belum ada pesan chat.</p>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php echo $__env->make('customer.modules.vendor-meetings.section_upcoming', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b flex justify-between items-center">
        <h3 class="font-bold">Pesanan Terbaru</h3>
        <a href="<?php echo e(route('client.pesanan')); ?>" class="text-sm text-bottle font-semibold hover:underline">Lihat semua</a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-gray-500 text-xs uppercase"><tr>
            <th class="px-6 py-3 text-left">No. Booking</th>
            <th class="px-6 py-3 text-left">Pasangan</th>
            <th class="px-6 py-3 text-left">Status</th>
            <th class="px-6 py-3"></th>
        </tr></thead>
        <tbody class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $pesananTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 font-medium"><?php echo e($p->nomor_pesanan); ?></td>
                <td class="px-6 py-4"><?php echo e($p->nama_pasangan); ?></td>
                <td class="px-6 py-4"><span class="px-2 py-0.5 rounded-full text-xs <?php echo e($p->status_badge_class); ?>"><?php echo e($p->status_label); ?></span></td>
                <td class="px-6 py-4 text-right"><a href="<?php echo e(route('client.pesanan_detail', $p->id)); ?>" class="text-bottle font-semibold">Detail</a></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada pesanan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/customer/modules/dashboard.blade.php ENDPATH**/ ?>