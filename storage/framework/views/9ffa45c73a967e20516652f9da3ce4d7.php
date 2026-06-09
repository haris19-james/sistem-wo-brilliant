<?php $__env->startSection('title', 'Tugas Lapangan'); ?>

<?php $__env->startPush('head'); ?>
<style>[x-cloak]{display:none!important}</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6 px-4 sm:px-6 py-6" id="tugasLapanganRoot"
     x-data="korlapTugasPage()"
     x-init="initPage()"
     data-verify-url="<?php echo e(url('/lapangan/tugas')); ?>"
     data-store-url="<?php echo e(route('lapangan.tugas.store')); ?>"
     data-vendors-url-base="<?php echo e(url('/lapangan/tugas/pesanan')); ?>"
     data-selected-pesanan="<?php echo e($selectedPesananId ?? ''); ?>"
     data-open-drawer="<?php echo e($openDrawer ? '1' : '0'); ?>"
     data-default-pic="<?php echo e(auth()->id()); ?>"
     data-flash-success="<?php echo e(e(session('success') ?? '')); ?>"
     data-acara-meta='<?php echo json_encode($acaraMeta, 15, 512) ?>'>
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tugas Lapangan</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola tugas vendor per acara — hanya booking <strong>Confirmed</strong> (DP/Lunas diverifikasi admin).</p>
        </div>
        <button type="button"
            id="btnTambahTugas"
            @click="openDrawer()"
            onclick="window.openKorlapTugasDrawer()"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5 shrink-0 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition whitespace-nowrap">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tugas
        </button>
    </div>

    <form method="GET" action="<?php echo e(route('lapangan.tugas.index')); ?>" id="tugasFilterForm"
        class="flex flex-col lg:flex-row flex-wrap gap-3 items-stretch lg:items-center bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex-1 relative">
            <input type="text" placeholder="Cari tugas..." id="searchInput"
                class="w-full px-4 py-2 pl-10 border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none text-sm">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <select name="pesanan_id" id="filterAcara" required
            class="px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 outline-none text-sm min-w-[200px]">
            <option value="">— Pilih Acara —</option>
            <?php $__currentLoopData = $acaraList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $a): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($a->id); ?>" <?php if($selectedPesananId == $a->id): echo 'selected'; endif; ?>>
                <?php echo e($a->nama_pasangan); ?> (<?php echo e($a->nomor_pesanan); ?>)
            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>

        <select id="filterPrioritas" class="px-4 py-2 border border-gray-200 rounded-lg focus:border-green-500 outline-none text-sm">
            <option value="">Semua Prioritas</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
        </select>

        <button type="submit" class="px-4 py-2 bg-white border border-green-600 text-green-700 hover:bg-green-50 text-sm font-semibold rounded-lg transition">Terapkan</button>
    </form>

    <?php if(!$selectedPesananId): ?>
    <div class="text-center py-16 bg-white border border-dashed border-green-200 rounded-xl">
        <p class="text-gray-600 text-sm">Pilih acara di filter lalu klik <strong>Terapkan</strong>, atau langsung klik <strong>Tambah Tugas</strong> di kanan atas.</p>
    </div>
    <?php else: ?>
    <?php
        $belum = $tugas->where('status', 'pending');
        $sedang = $tugas->where('status', 'in_progress');
        $selesai = $tugas->where('status', 'completed');
    ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-gray-50 border-b-2 border-gray-200 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                <h2 class="font-semibold text-gray-900 flex-1 text-sm">Belum Dikerjakan</h2>
                <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full text-xs font-bold pending-count"><?php echo e($belum->count()); ?></span>
            </div>
            <div class="space-y-3 pending-column min-h-[120px]">
                <?php $__empty_1 = true; $__currentLoopData = $belum; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('lapangan.modules.partials.task-card', ['task' => $t], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-400 text-xs text-center py-6">Tidak ada tugas</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-green-50/70 border-b-2 border-green-300 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                <h2 class="font-semibold text-gray-900 flex-1 text-sm">Sedang Dikerjakan</h2>
                <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded-full text-xs font-bold in-progress-count"><?php echo e($sedang->count()); ?></span>
            </div>
            <div class="space-y-3 in-progress-column min-h-[120px]">
                <?php $__empty_1 = true; $__currentLoopData = $sedang; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('lapangan.modules.partials.task-card', ['task' => $t], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-400 text-xs text-center py-6">Tidak ada tugas</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="space-y-3">
            <div class="flex items-center gap-3 p-3 bg-green-50 border-b-2 border-green-500 rounded-lg">
                <span class="w-2 h-2 rounded-full bg-green-600"></span>
                <h2 class="font-semibold text-gray-900 flex-1 text-sm">Selesai (Terverifikasi)</h2>
                <span class="bg-green-200 text-green-800 px-2 py-0.5 rounded-full text-xs font-bold completed-count"><?php echo e($selesai->count()); ?></span>
            </div>
            <div class="space-y-3 completed-column min-h-[120px]">
                <?php $__empty_1 = true; $__currentLoopData = $selesai; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php echo $__env->make('lapangan.modules.partials.task-card', ['task' => $t], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-400 text-xs text-center py-6">Belum ada tugas diverifikasi</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php echo $__env->make('lapangan.modules.partials.tugas-drawer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Upload Laporan Modal -->
    <div id="uploadLaporanModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm px-4">
        <div class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl relative">
            <button type="button" onclick="closeUploadModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">✕</button>
            <h3 class="font-bold text-lg text-gray-900 mb-4">Upload Laporan Tugas</h3>
            <form id="uploadLaporanForm" method="POST" action="" enctype="multipart/form-data" class="space-y-4">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Bukti Pengerjaan</label>
                    <input type="file" name="foto_bukti" id="foto_bukti" accept="image/*" required
                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="submit" class="w-full py-2 bg-bottle hover:bg-bottleHover text-white font-semibold rounded-lg transition">Kirim Laporan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
    function openUploadModal(taskId) {
        const modal = document.getElementById('uploadLaporanModal');
        const form = document.getElementById('uploadLaporanForm');
        // Define the route for upload laporan
        form.action = `/lapangan/tugas/${taskId}/upload-laporan`;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeUploadModal() {
        const modal = document.getElementById('uploadLaporanModal');
        const form = document.getElementById('uploadLaporanForm');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        form.reset();
    }
</script>
<script src="<?php echo e(asset('js/korlap-tugas.js')); ?>?v=5"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lapangan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/tugas.blade.php ENDPATH**/ ?>