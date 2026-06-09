<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard Admin'); ?>
<?php $__env->startSection('page-subtitle', 'Ringkasan sistem Brilliant WO'); ?>

<?php $__env->startSection('content'); ?>
<?php
    /** Brilliant Green pekat — selaras dengan tombol Filter (bg-bottle #00A32A) */
    $statusBadge = fn (string $status) => match ($status) {
        'Sedang Berlangsung' => 'bg-bottle text-white shadow-sm',
        'Selesai' => 'bg-leafSoft text-bottle ring-1 ring-bottle/30',
        'Menunggu' => 'bg-white text-bottle ring-1 ring-bottle/40',
        'Dibatalkan' => 'bg-gray-100 text-gray-600',
        default => 'bg-leafSoft text-bottle',
    };
?>

<?php if(session('success')): ?>
<div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm font-medium" role="status">
  <?php echo e(session('success')); ?>

</div>
<?php endif; ?>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
  
  <div class="relative bg-white rounded-2xl p-6 md:p-7 border border-bottle/25 shadow-sm ring-1 ring-bottle/10 h-full flex flex-col justify-between overflow-hidden">
    <div class="absolute top-5 right-5 text-bottle">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
    </div>
    <div>
      <p class="text-sm font-medium text-gray-600 pr-10">Total Booking</p>
      <p class="text-3xl font-bold text-bottle mt-2"><?php echo e($stats['total_booking']); ?></p>
    </div>
    <p class="text-xs font-semibold text-bottle/80 mt-3"><?php echo e($stats['booking_menunggu']); ?> menunggu konfirmasi</p>
  </div>

  
  <div class="relative bg-white rounded-2xl p-6 md:p-7 border border-bottle/25 shadow-sm ring-1 ring-bottle/10 h-full flex flex-col justify-between overflow-hidden">
    <div class="absolute top-5 right-5 text-bottle">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
      </svg>
    </div>
    <div>
      <p class="text-sm font-medium text-gray-600 pr-10">Paket Aktif</p>
      <p class="text-3xl font-bold text-bottle mt-2"><?php echo e($stats['total_paket']); ?></p>
    </div>
  </div>

  
  <div class="relative bg-white rounded-2xl p-6 md:p-7 border border-bottle/25 shadow-sm ring-1 ring-bottle/10 h-full flex flex-col justify-between overflow-hidden">
    <div class="absolute top-5 right-5 text-bottle">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
      </svg>
    </div>
    <div>
      <p class="text-sm font-medium text-gray-600 pr-10">Vendor Aktif</p>
      <p class="text-3xl font-bold text-bottle mt-2"><?php echo e($stats['total_vendor']); ?></p>
    </div>
  </div>

  
  <div class="relative bg-white rounded-2xl p-6 md:p-7 border border-bottle/25 shadow-sm ring-1 ring-bottle/10 h-full flex flex-col justify-between overflow-hidden">
    <div class="absolute top-5 right-5 text-bottle">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
      </svg>
    </div>
    <div>
      <p class="text-sm font-medium text-gray-600 pr-10">Client Terdaftar</p>
      <p class="text-3xl font-bold text-bottle mt-2"><?php echo e($stats['total_client']); ?></p>
    </div>
  </div>

  
  <div class="relative bg-white rounded-2xl p-6 md:p-7 border border-bottle/25 shadow-sm ring-1 ring-bottle/10 sm:col-span-2 xl:col-span-1 h-full flex flex-col justify-between overflow-hidden">
    <div class="absolute top-5 right-5 text-bottle">
      <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
      </svg>
    </div>
    <div>
      <p class="text-sm font-medium text-gray-600 pr-10">Konfirmasi Pembayaran</p>
      <p class="text-3xl font-bold text-bottle mt-2"><?php echo e($stats['pembayaran_pending']); ?></p>
    </div>
    <?php if($stats['pembayaran_pending'] > 0): ?>
    <a href="<?php echo e(route('admin.pembayaran')); ?>" class="text-xs font-semibold text-bottle hover:text-bottleHover mt-3 inline-flex items-center gap-1 transition">
      Review sekarang
      <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
    </a>
    <?php else: ?>
    <p class="text-xs font-medium text-gray-500 mt-3">Semua pembayaran terverifikasi</p>
    <?php endif; ?>
  </div>
</div>

<div id="admin-finance-charts-root" class="mb-8 min-h-[360px] w-full overflow-visible"></div>
<script>
    window.AdminFinanceChartsData = {
        monthlyRevenue: <?php echo json_encode($monthlyRevenue, 15, 512) ?>,
        vendorExpenses: <?php echo json_encode($vendorExpenses, 15, 512) ?>,
        paymentStatus: <?php echo json_encode($paymentStatus, 15, 512) ?>,
    };
</script>

<?php if(($bookingPerluVerifikasi ?? collect())->isNotEmpty()): ?>
<div class="bg-white rounded-2xl border border-bottle/30 shadow-sm overflow-hidden mb-8">
  <div class="px-6 py-5 border-b border-bottle/10 bg-leafSoft/40 flex flex-wrap justify-between items-center gap-3">
    <div>
      <h3 class="font-bold text-gray-900">Verifikasi Booking — Tim Lapangan</h3>
      <p class="text-xs text-gray-600 mt-0.5">DP/Lunas sudah masuk · perlu aktivasi tugas vendor &amp; assign Korlap</p>
    </div>
    <a href="<?php echo e(route('admin.pembayaran')); ?>" class="text-xs font-semibold text-bottle hover:underline">Kelola pembayaran →</a>
  </div>
  <div class="divide-y divide-gray-100">
    <?php $__currentLoopData = $bookingPerluVerifikasi; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="px-6 py-4 flex flex-col lg:flex-row lg:items-center gap-4">
      <div class="flex-1 min-w-0">
        <p class="font-semibold text-gray-900"><?php echo e($b->nama_pasangan); ?></p>
        <p class="text-xs text-gray-500 mt-0.5"><?php echo e($b->nomor_pesanan); ?> · <?php echo e($b->client_display_name); ?> · <?php echo e($b->paket?->nama_paket); ?> · <?php echo e($b->status_pembayaran_label); ?></p>
        <p class="text-xs text-amber-700 mt-1">Workflow: <strong><?php echo e($b->workflow_status_label); ?></strong>
          <?php if(!$b->korlap_id): ?> · Belum ada Korlap <?php endif; ?>
        </p>
      </div>
      <form method="POST" action="<?php echo e(route('admin.booking.verify_lapangan', $b)); ?>" class="flex flex-wrap items-end gap-2 shrink-0"
            onsubmit="return confirm('Aktifkan booking <?php echo e($b->nomor_pesanan); ?> untuk tim lapangan? Tugas vendor akan dibuat otomatis.');">
        <?php echo csrf_field(); ?>
        <div>
          <label class="text-[10px] font-semibold text-gray-500 block mb-1">Koordinator Lapangan</label>
          <select name="korlap_id" class="border border-gray-200 rounded-lg px-2 py-1.5 text-xs min-w-[160px] focus:border-bottle outline-none">
            <option value="">— Otomatis —</option>
            <?php $__currentLoopData = $korlapUsers ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($k->id); ?>" <?php if($b->korlap_id == $k->id): echo 'selected'; endif; ?>><?php echo e($k->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-bottle text-white text-xs font-bold rounded-lg hover:bg-bottleHover whitespace-nowrap">
          Verifikasi Booking
        </button>
        <a href="<?php echo e(route('admin.booking.show', $b)); ?>" class="px-3 py-2 text-xs font-semibold border border-gray-200 rounded-lg hover:bg-gray-50">Detail</a>
      </form>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>
<?php endif; ?>

<div class="bg-white rounded-2xl border border-bottle/20 shadow-sm overflow-hidden mb-8">
  <div class="px-6 py-5 border-b border-bottle/15 bg-leafSoft/60 flex flex-wrap justify-between items-center gap-3">
    <div>
      <h3 class="font-bold text-gray-900">Booking Aktif</h3>
      <p class="text-xs text-gray-600 mt-0.5">DP terverifikasi &amp; lunas penuh — <?php echo e(($bookingAktif ?? collect())->count()); ?> pesanan</p>
    </div>
    <a href="<?php echo e(route('admin.booking')); ?>" class="text-sm font-semibold text-bottle hover:text-bottleHover transition">Kelola semua →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead class="bg-leafSoft text-bottle uppercase text-xs tracking-wide">
        <tr>
          <th class="px-6 py-3.5 font-semibold">No. Booking</th>
          <th class="px-6 py-3.5 font-semibold">Klien</th>
          <th class="px-6 py-3.5 font-semibold">Pasangan</th>
          <th class="px-6 py-3.5 font-semibold">Paket</th>
          <th class="px-6 py-3.5 font-semibold">Tanggal Acara</th>
          <th class="px-6 py-3.5 font-semibold">Pembayaran</th>
          <th class="px-6 py-3.5 font-semibold"></th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php $__empty_1 = true; $__currentLoopData = $bookingAktif ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-leafSoft/50 transition-colors">
          <td class="px-6 py-4 font-semibold text-bottle"><?php echo e($b->nomor_pesanan); ?></td>
          <td class="px-6 py-4 text-gray-800 font-medium"><?php echo e($b->client_display_name); ?></td>
          <td class="px-6 py-4 text-gray-700"><?php echo e($b->nama_pasangan); ?></td>
          <td class="px-6 py-4 text-gray-600"><?php echo e($b->paket?->nama_paket ?? '—'); ?></td>
          <td class="px-6 py-4 text-gray-600"><?php echo e($b->tanggal_formatted); ?></td>
          <td class="px-6 py-4">
            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($b->status_pembayaran_badge_class); ?>">
              <?php echo e($b->status_pembayaran_label); ?>

            </span>
          </td>
          <td class="px-6 py-4">
            <a href="<?php echo e(route('admin.booking.show', $b)); ?>" class="text-bottle font-semibold hover:underline text-xs">Detail</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
          <td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada booking dengan DP terverifikasi atau lunas.</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php
    $hasKendala = ($kendalaAktif ?? collect())->isNotEmpty() || ($kendalaSelesai ?? collect())->isNotEmpty();
?>
<?php if($hasKendala): ?>
<div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden mb-8" id="adminKendalaPanel"
     data-status-url="<?php echo e(url('/admin/kendala')); ?>">
  <div class="px-6 py-5 border-b border-amber-100 bg-amber-50/80 flex flex-wrap justify-between items-center gap-3">
    <div>
      <h3 class="font-bold text-gray-900">Analisis Kendala</h3>
      <p class="text-xs text-gray-600 mt-0.5">
        <span class="text-amber-800 font-semibold"><?php echo e($kendalaAktifCount); ?> aktif</span>
        · <?php echo e(($kendalaSelesai ?? collect())->count()); ?> selesai
      </p>
    </div>
  </div>

  <div class="px-6 py-3 border-b border-gray-100 bg-gray-50/80">
    <h4 class="text-xs font-bold uppercase tracking-wide text-amber-800">Kendala Aktif</h4>
    <p class="text-[11px] text-gray-500">Menunggu tindakan &amp; dalam penanganan</p>
  </div>
  <div class="divide-y divide-gray-100" id="adminKendalaAktifList">
    <?php $__empty_1 = true; $__currentLoopData = $kendalaAktif; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php echo $__env->make('admin.modules.partials.kendala-row', ['k' => $k], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="px-6 py-8 text-sm text-gray-500 text-center" id="adminKendalaAktifEmpty">Tidak ada kendala aktif saat ini.</p>
    <?php endif; ?>
  </div>

  <div class="px-6 py-3 border-t border-b border-gray-100 bg-green-50/50">
    <h4 class="text-xs font-bold uppercase tracking-wide text-green-800">Kendala Selesai</h4>
    <p class="text-[11px] text-gray-500">Riwayat penyelesaian dengan catatan solusi</p>
  </div>
  <div class="divide-y divide-gray-100" id="adminKendalaSelesaiList">
    <?php $__empty_1 = true; $__currentLoopData = $kendalaSelesai ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <?php echo $__env->make('admin.modules.partials.kendala-row', ['k' => $k], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p class="px-6 py-8 text-sm text-gray-500 text-center" id="adminKendalaSelesaiEmpty">Belum ada kendala diselesaikan.</p>
    <?php endif; ?>
  </div>
</div>


<div id="adminKendalaSelesaiModal" class="fixed inset-0 z-[200] hidden items-center justify-center bg-black/50 p-4" role="dialog" aria-modal="true">
  <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-6">
    <h3 class="font-bold text-gray-900 text-lg mb-1">Selesaikan Kendala</h3>
    <p class="text-xs text-gray-500 mb-4" id="adminKendalaModalRingkasan"></p>
    <form id="adminKendalaSelesaiForm" class="space-y-4">
      <input type="hidden" name="kendala_id" id="adminKendalaModalId" value="">
      <div>
        <label for="adminKendalaSolusi" class="text-xs font-semibold text-gray-700 block mb-1">Solusi / Catatan Penyelesaian *</label>
        <textarea id="adminKendalaSolusi" name="tindak_lanjut" rows="4" required maxlength="2000"
                  class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:border-bottle focus:ring-1 focus:ring-bottle outline-none"
                  placeholder="Jelaskan langkah penyelesaian agar tim lapangan dapat membacanya…"></textarea>
      </div>
      <div class="flex gap-2 pt-1">
        <button type="button" id="adminKendalaModalBatal" class="flex-1 py-2.5 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</button>
        <button type="submit" class="flex-1 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold hover:bg-green-700">Simpan &amp; Selesai</button>
      </div>
    </form>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/admin-kendala.js')); ?>?v=2" defer></script>
<?php $__env->stopPush(); ?>
<?php endif; ?>

<div class="flex flex-wrap gap-3 mb-8">
  <a href="<?php echo e(route('admin.booking')); ?>"
     class="inline-flex items-center gap-2 px-5 py-2.5 bg-bottle text-white rounded-xl text-sm font-semibold shadow-sm hover:bg-bottleHover transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    Kelola Booking
  </a>
  <a href="<?php echo e(route('admin.paket.create')); ?>"
     class="inline-flex items-center gap-2 px-5 py-2.5 bg-bottle text-white rounded-xl text-sm font-semibold shadow-sm hover:bg-bottleHover transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Tambah Paket
  </a>
  <a href="<?php echo e(route('admin.vendor.create')); ?>"
     class="inline-flex items-center gap-2 px-5 py-2.5 bg-bottle text-white rounded-xl text-sm font-semibold shadow-sm hover:bg-bottleHover transition">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
    Tambah Vendor
  </a>
</div>

<div class="bg-white rounded-2xl border border-bottle/20 shadow-sm overflow-hidden">
  <div class="px-6 py-5 border-b border-bottle/15 bg-leafSoft/60 flex flex-wrap justify-between items-center gap-3">
    <h3 class="font-bold text-gray-900">Booking Terbaru (Berbayar)</h3>
    <a href="<?php echo e(route('admin.booking')); ?>" class="text-sm font-semibold text-bottle hover:text-bottleHover transition">Lihat semua →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
      <thead class="bg-leafSoft text-bottle uppercase text-xs tracking-wide">
        <tr>
          <th class="px-6 py-3.5 font-semibold">No. Booking</th>
          <th class="px-6 py-3.5 font-semibold">Klien</th>
          <th class="px-6 py-3.5 font-semibold">Pasangan</th>
          <th class="px-6 py-3.5 font-semibold">Paket</th>
          <th class="px-6 py-3.5 font-semibold">Tanggal</th>
          <th class="px-6 py-3.5 font-semibold">Status</th>
          <th class="px-6 py-3.5 font-semibold">Pembayaran</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        <?php $__empty_1 = true; $__currentLoopData = $bookingTerbaru; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-leafSoft/50 transition-colors">
          <td class="px-6 py-4 font-semibold text-bottle"><?php echo e($b->nomor_pesanan); ?></td>
          <td class="px-6 py-4 text-gray-800 font-medium"><?php echo e($b->client_display_name); ?></td>
          <td class="px-6 py-4 text-gray-700"><?php echo e($b->nama_pasangan); ?></td>
          <td class="px-6 py-4 text-gray-600"><?php echo e($b->paket?->nama_paket ?? '-'); ?></td>
          <td class="px-6 py-4 text-gray-600"><?php echo e($b->tanggal_formatted); ?></td>
          <td class="px-6 py-4">
            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($statusBadge($b->status)); ?>">
              <?php echo e($b->status); ?>

            </span>
          </td>
          <td class="px-6 py-4">
            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold <?php echo e($b->status_pembayaran_badge_class); ?>">
              <?php echo e($b->status_pembayaran_label); ?>

            </span>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
          <td colspan="7" class="px-6 py-12 text-center text-gray-500">Belum ada booking berbayar.</td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/admin/modules/dashboard.blade.php ENDPATH**/ ?>