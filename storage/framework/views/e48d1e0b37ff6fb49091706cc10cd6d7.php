

<?php $__env->startSection('title', 'Laporan Keuangan'); ?>
<?php $__env->startSection('page-title', 'Laporan Keuangan'); ?>
<?php $__env->startSection('page-subtitle', 'Analitik pendapatan · Filter transaksi · Export & detail booking'); ?>

<?php $__env->startSection('content'); ?>
<div x-data="laporanKeuanganAdmin({
    exportUrl: <?php echo \Illuminate\Support\Js::from(route('admin.laporan-keuangan.export'))->toHtml() ?>,
    detailUrlTemplate: <?php echo \Illuminate\Support\Js::from(str_replace('/0/', '/__ID__/', route('admin.laporan-keuangan.detail', ['konfirmasi' => 0])))->toHtml() ?>,
    verifyUrlTemplate: <?php echo \Illuminate\Support\Js::from(str_replace('/0/', '/__ID__/', route('admin.pembayaran.verify', ['konfirmasi' => 0])))->toHtml() ?>,
    filters: <?php echo \Illuminate\Support\Js::from($filters)->toHtml() ?>,
})" class="space-y-6">

    
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-bottle to-bottleHover rounded-2xl p-5 text-white shadow-md sm:col-span-2 xl:col-span-1">
            <p class="text-xs font-semibold uppercase tracking-wide text-white/80">Pendapatan Kotor</p>
            <p class="text-2xl font-black mt-1">Rp <?php echo e(number_format($analytics['pendapatan_kotor'], 0, ',', '.')); ?></p>
            <p class="text-xs text-white/70 mt-1">DP + Pelunasan terverifikasi (sesuai filter)</p>
        </div>
        <div class="bg-white rounded-2xl border border-yellow-200 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-yellow-700">DP Terverifikasi</p>
            <p class="text-xl font-black text-gray-900 mt-1">Rp <?php echo e(number_format($analytics['total_dp'], 0, ',', '.')); ?></p>
            <p class="text-xs text-gray-500 mt-1"><?php echo e($analytics['count_dp']); ?> transaksi</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-200 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-bottle">Pelunasan</p>
            <p class="text-xl font-black text-gray-900 mt-1">Rp <?php echo e(number_format($analytics['total_pelunasan'], 0, ',', '.')); ?></p>
            <p class="text-xs text-gray-500 mt-1"><?php echo e($analytics['count_lunas']); ?> transaksi</p>
        </div>
        <div class="bg-white rounded-2xl border border-amber-200 bg-amber-50/40 p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">Menunggu Verifikasi</p>
            <p class="text-xl font-black text-amber-900 mt-1">Rp <?php echo e(number_format($analytics['total_pending'], 0, ',', '.')); ?></p>
            <p class="text-xs text-amber-700 mt-1"><?php echo e($analytics['count_pending']); ?> transaksi</p>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl border border-red-100 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase text-red-600">Booking Pending</p>
            <p class="text-3xl font-black text-gray-900 mt-1"><?php echo e($analytics['booking_pending']); ?></p>
            <p class="text-xs text-gray-500 mt-0.5">Belum bayar (unpaid)</p>
        </div>
        <div class="bg-white rounded-2xl border border-yellow-100 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase text-yellow-700">Booking DP</p>
            <p class="text-3xl font-black text-gray-900 mt-1"><?php echo e($analytics['booking_dp']); ?></p>
            <p class="text-xs text-gray-500 mt-0.5">DP terverifikasi</p>
        </div>
        <div class="bg-white rounded-2xl border border-green-100 p-4 shadow-sm">
            <p class="text-xs font-semibold uppercase text-bottle">Booking Lunas</p>
            <p class="text-3xl font-black text-gray-900 mt-1"><?php echo e($analytics['booking_lunas']); ?></p>
            <p class="text-xs text-gray-500 mt-0.5">Lunas penuh</p>
        </div>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
            <div>
                <p class="text-sm font-semibold text-gray-900">Filter &amp; Export</p>
                <p class="text-xs text-gray-500 mt-0.5">Export mengunduh seluruh data yang cocok dengan filter (bukan hanya halaman ini).</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" @click="exportPdf()" :disabled="exporting"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-xs font-bold rounded-xl hover:bg-red-700 disabled:opacity-50">
                    <span x-show="!exporting">Export PDF</span>
                    <span x-show="exporting">Memproses...</span>
                </button>
                <button type="button" @click="exportExcel()" :disabled="exporting"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 text-white text-xs font-bold rounded-xl hover:bg-green-800 disabled:opacity-50">
                    Export Excel
                </button>
            </div>
        </div>

        <form method="GET" action="<?php echo e(route('admin.laporan-keuangan')); ?>" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
            <div>
                <label for="date_from" class="block text-xs font-medium text-gray-600 mb-1">Dari tanggal</label>
                <input type="date" name="date_from" id="date_from" value="<?php echo e($filters['date_from']); ?>"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none">
            </div>
            <div>
                <label for="date_to" class="block text-xs font-medium text-gray-600 mb-1">Sampai tanggal</label>
                <input type="date" name="date_to" id="date_to" value="<?php echo e($filters['date_to']); ?>"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none">
            </div>
            <div>
                <label for="q" class="block text-xs font-medium text-gray-600 mb-1">Nama klien / pasangan</label>
                <input type="search" name="q" id="q" value="<?php echo e($filters['q']); ?>" placeholder="Cari nama..."
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none">
            </div>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-600 mb-1">Status transaksi</label>
                <select name="status" id="status"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle outline-none">
                    <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if($filters['status'] === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label for="booking_status" class="block text-xs font-medium text-gray-600 mb-1">Status pembayaran booking</label>
                <select name="booking_status" id="booking_status"
                    class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle outline-none">
                    <?php $__currentLoopData = $bookingStatusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if($filters['booking_status'] === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="md:col-span-2 xl:col-span-5 flex flex-wrap gap-2 pt-1">
                <button type="submit" class="px-4 py-2 bg-bottle text-white text-sm font-bold rounded-xl hover:bg-bottleHover">Terapkan Filter</button>
                <a href="<?php echo e(route('admin.laporan-keuangan')); ?>" class="px-4 py-2 border border-gray-200 text-sm font-semibold text-gray-700 rounded-xl hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>

    
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-50 flex items-center justify-between">
            <p class="text-sm font-semibold text-gray-900">Riwayat Transaksi</p>
            <p class="text-xs text-gray-500">Klik baris untuk detail · <?php echo e($transaksi->total()); ?> total</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left min-w-[1000px]">
                <thead class="bg-gray-50 text-gray-500 uppercase text-[11px] tracking-wide">
                    <tr>
                        <th class="px-4 py-3">ID Transaksi</th>
                        <th class="px-4 py-3">Client</th>
                        <th class="px-4 py-3">Paket</th>
                        <th class="px-4 py-3">Nominal</th>
                        <th class="px-4 py-3">Tipe</th>
                        <th class="px-4 py-3">Tgl Transfer</th>
                        <th class="px-4 py-3">Status Klien</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $transaksi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-leafSoft/30 cursor-pointer align-top transition-colors"
                        @click="openDetail(<?php echo e($trx->id); ?>)">
                        <td class="px-4 py-4 font-mono text-xs font-semibold text-gray-700"><?php echo e($trx->nomor_transaksi); ?></td>
                        <td class="px-4 py-4">
                            <p class="font-semibold text-gray-900"><?php echo e($trx->user?->name ?? '-'); ?></p>
                            <p class="text-xs text-gray-500"><?php echo e($trx->invoice?->pesanan?->nama_pasangan); ?></p>
                        </td>
                        <td class="px-4 py-4 text-gray-800"><?php echo e($trx->invoice?->pesanan?->paket?->nama_paket ?? '-'); ?></td>
                        <td class="px-4 py-4 font-bold text-gray-900 whitespace-nowrap">Rp <?php echo e(number_format($trx->jumlah, 0, ',', '.')); ?></td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-semibold bg-leafSoft text-bottle"><?php echo e($trx->jenis_pembayaran); ?></span>
                        </td>
                        <td class="px-4 py-4 text-gray-600 whitespace-nowrap"><?php echo e($trx->tanggal_transfer->format('d M Y')); ?></td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold <?php echo e($trx->invoice?->pesanan?->status_pembayaran_badge_class ?? 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo e($trx->invoice?->pesanan?->status_pembayaran_label ?? '-'); ?>

                            </span>
                        </td>
                        <td class="px-4 py-4">
                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-semibold <?php echo e($trx->status_verifikasi_badge_class); ?>">
                                <?php echo e($trx->status_verifikasi_label); ?>

                            </span>
                        </td>
                        <td class="px-4 py-4 text-right whitespace-nowrap" @click.stop>
                            <?php if($trx->bukti_url): ?>
                            <button type="button" @click="openBukti('<?php echo e($trx->bukti_url); ?>', '<?php echo e($trx->nomor_transaksi); ?>')"
                                class="text-xs font-semibold text-bottle hover:underline mr-2">Bukti</button>
                            <?php endif; ?>
                            <?php if($trx->isPending()): ?>
                            <form method="POST" action="<?php echo e(route('admin.pembayaran.verify', $trx)); ?>" class="inline"
                                onsubmit="return confirm('Setujui pembayaran <?php echo e($trx->nomor_transaksi); ?>?');">
                                <?php echo csrf_field(); ?>
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="px-2 py-1 bg-bottle text-white text-[11px] font-bold rounded-lg">Approve</button>
                            </form>
                            <button type="button" @click="openReject(<?php echo e($trx->id); ?>, '<?php echo e($trx->nomor_transaksi); ?>')"
                                class="px-2 py-1 border border-red-300 text-red-600 text-[11px] font-bold rounded-lg ml-1">Reject</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-gray-500">Tidak ada transaksi untuk filter ini.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div><?php echo e($transaksi->links()); ?></div>

    
    <div x-show="detailOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" @keydown.escape.window="detailOpen = false">
        <div @click.outside="detailOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Detail Transaksi</h3>
                <button type="button" @click="detailOpen = false" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-5 overflow-auto flex-1 space-y-4 text-sm" x-show="!detailLoading">
                <template x-if="detailData">
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div><p class="text-xs text-gray-500">ID Transaksi</p><p class="font-mono font-semibold" x-text="detailData.transaksi?.nomor_transaksi"></p></div>
                            <div><p class="text-xs text-gray-500">Nominal</p><p class="font-bold" x-text="detailData.transaksi?.jumlah_fmt"></p></div>
                            <div><p class="text-xs text-gray-500">Jenis</p><p x-text="detailData.transaksi?.jenis_pembayaran"></p></div>
                            <div><p class="text-xs text-gray-500">Status verifikasi</p><p x-text="detailData.transaksi?.status_label"></p></div>
                        </div>
                        <div class="rounded-xl border border-gray-100 p-4 bg-gray-50/80" x-show="detailData.booking">
                            <p class="text-xs font-bold uppercase text-bottle mb-2">Informasi Booking</p>
                            <div class="grid grid-cols-2 gap-2 text-sm">
                                <div><span class="text-gray-500">No. Pesanan:</span> <span class="font-semibold" x-text="detailData.booking?.nomor_pesanan"></span></div>
                                <div><span class="text-gray-500">Pasangan:</span> <span x-text="detailData.booking?.nama_pasangan"></span></div>
                                <div><span class="text-gray-500">Paket:</span> <span x-text="detailData.booking?.paket"></span></div>
                                <div><span class="text-gray-500">Acara:</span> <span x-text="detailData.booking?.tanggal_acara"></span></div>
                                <div><span class="text-gray-500">Klien:</span> <span x-text="detailData.booking?.client"></span></div>
                                <div><span class="text-gray-500">Status bayar:</span> <span class="font-semibold" x-text="detailData.booking?.status_pembayaran_label"></span></div>
                            </div>
                            <a :href="detailData.booking?.detail_url" class="inline-block mt-3 text-xs font-bold text-bottle hover:underline" @click.stop>Buka halaman booking →</a>
                        </div>
                        <div class="rounded-xl border border-gray-100 p-4" x-show="detailData.invoice">
                            <p class="text-xs font-bold uppercase text-gray-600 mb-2">Invoice</p>
                            <p><span class="text-gray-500">No:</span> <span x-text="detailData.invoice?.nomor_invoice"></span></p>
                            <p><span class="text-gray-500">Total:</span> <span x-text="detailData.invoice?.total_biaya_fmt"></span></p>
                        </div>
                        <div x-show="detailData.vendors && detailData.vendors.length">
                            <p class="text-xs font-bold uppercase text-gray-600 mb-2">Rincian Vendor</p>
                            <div class="space-y-2">
                                <template x-for="(v, i) in detailData.vendors" :key="i">
                                    <div class="flex justify-between items-start gap-2 p-3 rounded-xl bg-leafSoft/40 border border-green-100">
                                        <div>
                                            <p class="font-semibold text-gray-900" x-text="v.nama_vendor || 'Vendor'"></p>
                                            <p class="text-xs text-gray-500" x-text="v.kategori"></p>
                                            <p class="text-[11px] text-gray-600 mt-1" x-text="v.rincian"></p>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <p class="font-bold text-sm" x-text="v.total_biaya_fmt"></p>
                                            <p class="text-[10px] text-gray-600" x-text="v.status_pembayaran"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div x-show="detailData.bukti_url" class="pt-2">
                            <p class="text-xs font-medium text-gray-600 mb-2">Bukti transfer</p>
                            <img :src="detailData.bukti_url" alt="Bukti" class="max-h-48 rounded-xl border border-gray-200">
                        </div>
                    </div>
                </template>
            </div>
            <div x-show="detailLoading" class="p-8 text-center text-gray-500">Memuat detail...</div>
        </div>
    </div>

    
    <div x-show="buktiOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60" @keydown.escape.window="buktiOpen = false">
        <div @click.outside="buktiOpen = false" class="bg-white rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between px-5 py-4 border-b">
                <h3 class="font-bold">Bukti — <span x-text="buktiTrxId"></span></h3>
                <button type="button" @click="buktiOpen = false" class="p-2 rounded-lg hover:bg-gray-100">✕</button>
            </div>
            <div class="p-5 overflow-auto bg-gray-50">
                <img :src="buktiUrl" alt="Bukti" class="w-full max-h-[70vh] object-contain mx-auto rounded-xl border bg-white">
            </div>
        </div>
    </div>

    
    <div x-show="rejectOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6">
            <h3 class="font-bold text-gray-900 mb-1">Tolak Pembayaran</h3>
            <p class="text-xs text-gray-500 mb-4">Transaksi <span x-text="rejectTrxId" class="font-mono font-semibold"></span></p>
            <form :action="rejectAction" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="action" value="reject">
                <textarea name="alasan_penolakan" rows="3" required placeholder="Alasan penolakan..."
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm outline-none"></textarea>
                <div class="flex gap-2">
                    <button type="button" @click="rejectOpen = false" class="flex-1 py-2.5 border rounded-xl text-sm font-semibold">Batal</button>
                    <button type="submit" class="flex-1 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.2/dist/jspdf.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf-autotable@3.8.4/dist/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="<?php echo e(asset('js/laporan-keuangan.js')); ?>?v=1"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/admin/modules/laporan-keuangan/index.blade.php ENDPATH**/ ?>