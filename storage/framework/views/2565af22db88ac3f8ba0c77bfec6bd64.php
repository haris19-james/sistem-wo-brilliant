

<?php $__env->startSection('title', 'Anggaran Vendor — '.$pesanan->nomor_pesanan); ?>
<?php $__env->startSection('page-title', 'Anggaran Vendor'); ?>
<?php $__env->startSection('page-subtitle', $pesanan->nomor_pesanan.' · '.$pesanan->nama_pasangan); ?>

<?php $__env->startSection('content'); ?>
<?php
    $fmtRp = fn ($n) => 'Rp '.number_format(\App\Support\MoneyParser::toFloat($n), 0, ',', '.');
    $finBadge = match($financial['status'] ?? 'menunggu') {
        'lunas' => 'bg-green-100 text-green-800 border-green-200',
        'dp' => 'bg-amber-50 text-amber-800 border-amber-200',
        default => 'bg-orange-50 text-orange-800 border-orange-200',
    };
    $finLabel = match($financial['status'] ?? 'menunggu') {
        'lunas' => 'Lunas (semua vendor)',
        'dp' => 'Sebagian terbayar',
        default => 'Menunggu Pelunasan',
    };
?>

<?php if(session('success')): ?>
<div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-800 rounded-xl text-sm"><?php echo e(session('success')); ?></div>
<?php endif; ?>
<?php if($errors->any()): ?>
<div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm">
    <ul class="list-disc list-inside"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
</div>
<?php endif; ?>

<a href="<?php echo e(route('admin.vendor-keuangan.index')); ?>" class="text-sm text-bottle font-semibold hover:underline mb-4 inline-block">← Daftar booking</a>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-1">Ringkasan Proyek (sinkron lapangan)</h3>
        <p class="text-xs text-gray-500 mb-4">Total di dashboard Korlap = penjumlahan anggaran vendor di bawah</p>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
            <div class="p-4 bg-gray-50 rounded-xl">
                <p class="text-gray-500 text-xs">Total biaya operasional</p>
                <p class="text-xl font-bold text-gray-900 mt-1"><?php echo e($fmtRp($financial['total_biaya'])); ?></p>
            </div>
            <div class="p-4 bg-green-50 rounded-xl">
                <p class="text-gray-500 text-xs">Sudah dibayar</p>
                <p class="text-xl font-bold text-green-700 mt-1"><?php echo e($fmtRp($financial['dibayar'])); ?></p>
            </div>
            <div class="p-4 bg-orange-50 rounded-xl">
                <p class="text-gray-500 text-xs">Sisa pelunasan</p>
                <p class="text-xl font-bold text-orange-600 mt-1"><?php echo e($fmtRp($financial['sisa_pelunasan'])); ?></p>
            </div>
        </div>
        <span class="inline-flex mt-4 px-3 py-1 rounded-lg text-xs font-semibold border <?php echo e($finBadge); ?>"><?php echo e($finLabel); ?></span>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-3">Tambah Anggaran Vendor</h3>
        <?php if($vendorsTanpaAnggaran->isEmpty()): ?>
        <p class="text-sm text-gray-500">Semua vendor pada booking ini sudah memiliki anggaran.</p>
        <?php else: ?>
        <form method="POST" action="<?php echo e(route('admin.vendor-keuangan.store', $pesanan)); ?>" class="space-y-3">
            <?php echo csrf_field(); ?>
            <div>
                <label class="text-xs font-semibold text-gray-600">Vendor</label>
                <select name="vendor_id" required class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih vendor…</option>
                    <?php $__currentLoopData = $vendorsTanpaAnggaran; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($v->id); ?>"><?php echo e($v->kategori); ?> — <?php echo e($v->nama_vendor); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <?php if (isset($component)) { $__componentOriginal1565b56a0536065e7a4d86ebdbcd1c6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1565b56a0536065e7a4d86ebdbcd1c6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-rupiah','data' => ['name' => 'total_biaya','label' => 'Total biaya','placeholder' => '0','value' => old('total_biaya')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-rupiah'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'total_biaya','label' => 'Total biaya','placeholder' => '0','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('total_biaya'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1565b56a0536065e7a4d86ebdbcd1c6a)): ?>
<?php $attributes = $__attributesOriginal1565b56a0536065e7a4d86ebdbcd1c6a; ?>
<?php unset($__attributesOriginal1565b56a0536065e7a4d86ebdbcd1c6a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1565b56a0536065e7a4d86ebdbcd1c6a)): ?>
<?php $component = $__componentOriginal1565b56a0536065e7a4d86ebdbcd1c6a; ?>
<?php unset($__componentOriginal1565b56a0536065e7a4d86ebdbcd1c6a); ?>
<?php endif; ?>
            <div>
                <label class="text-xs font-semibold text-gray-600">Rincian biaya</label>
                <textarea name="rincian_biaya" rows="3" class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm"
                          placeholder="Contoh: Dekorasi pelaminan, lighting, transport…"></textarea>
            </div>
            <button type="submit" class="w-full py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover text-sm">Simpan Anggaran</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">Rincian Anggaran per Vendor</h3>
        <p class="text-xs text-gray-500 mt-0.5">Ubah status pembayaran — dashboard lapangan mengikuti otomatis</p>
    </div>

    <?php if($pesanan->vendorAnggarans->isEmpty()): ?>
    <p class="px-6 py-12 text-center text-gray-500 text-sm">Belum ada anggaran vendor. Tambahkan dari panel kanan.</p>
    <?php else: ?>
    <div class="divide-y divide-gray-100">
        <?php $__currentLoopData = $pesanan->vendorAnggarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $anggaran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="px-6 py-5">
            <div class="flex flex-col lg:flex-row lg:items-start gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <p class="font-bold text-gray-900"><?php echo e($anggaran->vendor?->nama_vendor); ?></p>
                        <span class="text-xs text-gray-500"><?php echo e($anggaran->vendor?->kategori); ?></span>
                        <span class="px-2 py-0.5 rounded text-xs font-bold border <?php echo e($anggaran->status_pembayaran_badge_class); ?>">
                            <?php echo e($anggaran->status_pembayaran_label); ?>

                        </span>
                    </div>
                    <p class="text-lg font-bold text-bottle"><?php echo e($fmtRp($anggaran->total_biaya)); ?></p>
                    <?php if($anggaran->rincian_biaya): ?>
                    <p class="text-sm text-gray-600 mt-2 whitespace-pre-line"><?php echo e($anggaran->rincian_biaya); ?></p>
                    <?php endif; ?>
                    <p class="text-[11px] text-gray-400 mt-2">
                        Diinput <?php echo e($anggaran->allocatedBy?->name ?? 'Admin'); ?>

                        · <?php echo e($anggaran->updated_at?->diffForHumans()); ?>

                    </p>
                </div>

                <div class="flex flex-col gap-2 shrink-0 min-w-[200px]">
                    <p class="text-[10px] font-bold uppercase text-gray-500">Status Pembayaran</p>
                    <form method="POST" action="<?php echo e(route('admin.vendor-keuangan.payment', $anggaran)); ?>" class="flex flex-wrap gap-2">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="status_pembayaran" value="menunggu">
                        <button type="submit" class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'px-3 py-1.5 text-xs font-semibold rounded-lg border',
                            $anggaran->status_pembayaran === 'menunggu'
                                ? 'bg-orange-100 border-orange-300 text-orange-800'
                                : 'border-gray-200 text-gray-600 hover:bg-gray-50',
                        ]); ?>">Menunggu</button>
                    </form>
                    <form method="POST" action="<?php echo e(route('admin.vendor-keuangan.payment', $anggaran)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="status_pembayaran" value="dibayar">
                        <button type="submit" class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'w-full px-3 py-1.5 text-xs font-semibold rounded-lg border',
                            $anggaran->status_pembayaran === 'dibayar'
                                ? 'bg-blue-100 border-blue-300 text-blue-800'
                                : 'border-gray-200 text-gray-600 hover:bg-blue-50',
                        ]); ?>">Dibayar</button>
                    </form>
                    <form method="POST" action="<?php echo e(route('admin.vendor-keuangan.payment', $anggaran)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <input type="hidden" name="status_pembayaran" value="lunas">
                        <button type="submit" class="<?php echo \Illuminate\Support\Arr::toCssClasses([
                            'w-full px-3 py-1.5 text-xs font-semibold rounded-lg',
                            $anggaran->status_pembayaran === 'lunas'
                                ? 'bg-green-600 text-white'
                                : 'bg-green-50 border border-green-200 text-green-700 hover:bg-green-100',
                        ]); ?>">Lunas</button>
                    </form>
                </div>
            </div>

            <details class="mt-4 group">
                <summary class="text-xs font-semibold text-bottle cursor-pointer hover:underline">Edit rincian biaya</summary>
                <form method="POST" action="<?php echo e(route('admin.vendor-keuangan.update', $anggaran)); ?>" class="mt-3 p-4 bg-gray-50 rounded-xl space-y-3">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <input type="hidden" name="vendor_id" value="<?php echo e($anggaran->vendor_id); ?>">
                    <?php if (isset($component)) { $__componentOriginal1565b56a0536065e7a4d86ebdbcd1c6a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1565b56a0536065e7a4d86ebdbcd1c6a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.input-rupiah','data' => ['name' => 'total_biaya','label' => 'Total biaya','value' => $anggaran->total_biaya]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-rupiah'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'total_biaya','label' => 'Total biaya','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($anggaran->total_biaya)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1565b56a0536065e7a4d86ebdbcd1c6a)): ?>
<?php $attributes = $__attributesOriginal1565b56a0536065e7a4d86ebdbcd1c6a; ?>
<?php unset($__attributesOriginal1565b56a0536065e7a4d86ebdbcd1c6a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1565b56a0536065e7a4d86ebdbcd1c6a)): ?>
<?php $component = $__componentOriginal1565b56a0536065e7a4d86ebdbcd1c6a; ?>
<?php unset($__componentOriginal1565b56a0536065e7a4d86ebdbcd1c6a); ?>
<?php endif; ?>
                    <div>
                        <label class="text-xs font-semibold text-gray-600">Rincian</label>
                        <textarea name="rincian_biaya" rows="2" class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2 text-sm"><?php echo e($anggaran->rincian_biaya); ?></textarea>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-bottle text-white text-xs font-semibold rounded-lg">Simpan</button>
                        <button type="submit" formaction="<?php echo e(route('admin.vendor-keuangan.destroy', $anggaran)); ?>" formmethod="POST"
                                onclick="return confirm('Hapus anggaran vendor ini?');"
                                class="px-4 py-2 border border-red-200 text-red-600 text-xs font-semibold rounded-lg hover:bg-red-50">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            Hapus
                        </button>
                    </div>
                </form>
            </details>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</div>

<div class="mt-6">
    <a href="<?php echo e(route('admin.booking.show', $pesanan)); ?>" class="text-sm text-gray-600 hover:text-bottle">Lihat detail booking →</a>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/rupiah-input.js')); ?>?v=1" defer></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/admin/modules/vendor-keuangan/show.blade.php ENDPATH**/ ?>