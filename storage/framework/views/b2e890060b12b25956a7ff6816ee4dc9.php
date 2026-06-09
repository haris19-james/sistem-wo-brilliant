<?php $__env->startSection('title', 'Pemesanan Acara'); ?>

<?php
    $displayStatus = fn (?string $status) => $status === 'Menunggu' ? 'Persiapan' : ($status ?? '—');
    $fotoUrl = function ($pesanan) {
        if (! empty($pesanan->foto_venue)) {
            return asset('storage/'.$pesanan->foto_venue);
        }
        if ($pesanan->paket?->image_url) {
            return $pesanan->paket->image_url;
        }
        return 'https://via.placeholder.com/80x80?text=Acara';
    };
    $firstId = $pesanans->first()?->id;
?>

<?php $__env->startSection('content'); ?>
<div id="korlapPemesananRoot" class="w-full h-full overflow-hidden flex flex-col"
     data-api-bookings="<?php echo e($apiBookingsUrl); ?>"
     data-api-detail="<?php echo e($apiBookingDetailUrl); ?>"
     data-initial-booking-id="<?php echo e($firstId); ?>">
    <div class="px-6 lg:px-8 pt-6 pb-4">
        <h1 class="text-3xl font-bold text-gray-900">Pemesanan Acara</h1>
        <p class="text-gray-600 text-sm mt-1">Monitor seluruh acara dan status persiapan vendor.</p>
    </div>

    <div class="flex flex-1 overflow-hidden gap-6 px-6 lg:px-8 pb-6">
        <div class="flex-1 flex flex-col min-w-0 bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 space-y-3">
                <form id="korlapFilterForm" method="GET" class="flex flex-wrap gap-2">
                    <div class="flex-1 min-w-[150px] relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" name="q" value="<?php echo e($filters['q'] ?? ''); ?>" placeholder="Cari nama pengantin..."
                            class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-green-500 focus:ring-1 focus:ring-green-500 focus:outline-none">
                    </div>
                    <select name="status" class="px-4 py-2 border border-gray-200 rounded-lg text-sm focus:border-green-500 focus:outline-none bg-white">
                        <option value="">Status acara</option>
                        <option value="Persiapan" <?php if(($filters['status'] ?? '') === 'Persiapan'): echo 'selected'; endif; ?>>Persiapan</option>
                        <option value="Sedang Berlangsung" <?php if(($filters['status'] ?? '') === 'Sedang Berlangsung'): echo 'selected'; endif; ?>>Berjalan</option>
                        <option value="Selesai" <?php if(($filters['status'] ?? '') === 'Selesai'): echo 'selected'; endif; ?>>Selesai</option>
                    </select>
                    <div class="relative">
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <input type="date" name="tanggal" value="<?php echo e($filters['tanggal'] ?? ''); ?>"
                            class="pl-4 pr-10 py-2 border border-gray-200 rounded-lg text-sm focus:border-green-500 focus:outline-none">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition">Filter</button>
                </form>
            </div>

            <div class="flex-1 overflow-y-auto space-y-2 p-3" id="pesananList">
                <?php $__empty_1 = true; $__currentLoopData = $pesanans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isFirst = $loop->first;
                    $statusLabel = $displayStatus($p->status);
                ?>
                <button type="button" data-booking-id="<?php echo e($p->id); ?>"
                    class="pesanan-card w-full text-left group p-4 rounded-lg border-2 transition-all hover:border-green-500/40 hover:bg-green-50/50 <?php echo e($isFirst ? 'border-green-500 bg-green-50' : 'border-gray-100'); ?>">
                    <div class="flex gap-3">
                        <div class="w-20 h-20 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                            <img src="<?php echo e($fotoUrl($p)); ?>" alt="Venue" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="font-bold text-gray-900 truncate"><?php echo e($p->nama_pasangan); ?></h3>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold whitespace-nowrap
                                    <?php if($p->status === 'Menunggu'): ?> bg-blue-100 text-blue-800
                                    <?php elseif($p->status === 'Sedang Berlangsung'): ?> bg-yellow-100 text-yellow-800
                                    <?php else: ?> bg-green-100 text-green-800 <?php endif; ?>">
                                    <?php echo e($statusLabel); ?>

                                </span>
                            </div>
                            <p class="text-xs text-gray-600 mb-1"><?php echo e($p->lokasi ?? 'Lokasi belum ditentukan'); ?></p>
                            <div class="flex items-center gap-4 text-xs text-gray-500 mb-2">
                                <span>📅 <?php echo e($p->tanggal_formatted); ?></span>
                                <span>🕐 <?php echo e($p->jam_mulai_formatted); ?> - <?php echo e($p->jam_selesai_formatted); ?> WIB</span>
                            </div>
                            <span class="inline-flex items-center text-xs font-semibold text-green-600 group-hover:text-green-700 transition">
                                Lihat Detail
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </div>
                    </div>
                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="text-center py-12 text-gray-500 text-sm">Tidak ada pemesanan ditemukan</div>
                <?php endif; ?>
            </div>

            <?php if($pesanans->hasPages()): ?>
            <div class="border-t border-gray-200 p-3 bg-gray-50">
                <div class="flex items-center justify-center gap-1">
                    <?php echo e($pesanans->links('pagination::tailwind')); ?>

                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="hidden lg:flex lg:w-96 flex-col bg-white rounded-lg border border-gray-200 overflow-hidden" id="detailPanel">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h2 class="font-bold text-gray-900">Detail Acara</h2>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition" id="closeDetail" aria-label="Tutup detail">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto" id="detailContent">
                <div class="p-8 text-center text-sm text-gray-500">Memuat detail acara…</div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/korlap-pemesanan.js')); ?>?v=1" defer></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.lapangan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/pesanan/index.blade.php ENDPATH**/ ?>