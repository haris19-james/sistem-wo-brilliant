

<div class="bg-white border border-green-200 rounded-2xl p-6 shadow-sm">
  <div class="flex items-center justify-between mb-4">
    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
      <svg class="w-6 h-6 text-bottle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      Jadwal Meeting Vendor
    </h3>
    <a href="<?php echo e(route('client.vendor-meetings.index')); ?>" class="text-sm text-bottle hover:text-bottleHover font-semibold">Lihat Semua →</a>
  </div>

  <?php if(($upcomingVendorMeetings ?? collect())->isNotEmpty()): ?>
  <div class="space-y-3">
    <?php $__currentLoopData = $upcomingVendorMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $meeting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
      $booking = $meeting->booking;
      $isToday = $meeting->isToday();
      $isOverdue = $meeting->isOverdue();
      $urgencyClass = $isToday || $isOverdue
        ? 'border-l-4 border-amber-400 bg-amber-50/60'
        : 'border-l-4 border-green-300 bg-gradient-to-r from-leafSoft/60 to-white';
    ?>

    <a href="<?php echo e($booking ? route('client.pesanan_detail', $booking->id) : route('client.vendor-meetings.index')); ?>"
       class="block p-4 rounded-xl border border-gray-100 <?php echo e($urgencyClass); ?> hover:shadow-md transition">
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
          <h4 class="font-semibold text-gray-900"><?php echo e($meeting->title); ?></h4>
          <?php if($booking): ?>
          <p class="text-xs text-gray-500 mt-0.5"><?php echo e($booking->nomor_pesanan); ?> · <?php echo e($booking->nama_pasangan); ?></p>
          <?php endif; ?>
          <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 text-sm text-gray-600">
            <div class="flex items-center gap-1">
              <svg class="w-4 h-4 text-bottle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              <?php echo e($meeting->meeting_date->translatedFormat('d M Y')); ?>

            </div>
            <div class="flex items-center gap-1">
              <svg class="w-4 h-4 text-bottle" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <?php echo e($meeting->meeting_time); ?>

            </div>
          </div>
          <div class="flex items-center gap-1 mt-1 text-xs text-gray-500">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            </svg>
            <?php echo e($meeting->location); ?>

          </div>
        </div>
        <div class="flex flex-col items-end gap-2 flex-shrink-0">
          <span class="px-2.5 py-1 rounded-lg text-xs font-bold <?php echo e($meeting->status_badge_class); ?>">
            <?php echo e($meeting->status_label); ?>

          </span>
          <?php if($isToday): ?>
          <span class="px-2 py-1 rounded text-xs font-bold bg-bottle text-white">Hari Ini</span>
          <?php elseif($isOverdue): ?>
          <span class="px-2 py-1 rounded text-xs font-bold bg-amber-100 text-amber-800">Perlu perhatian</span>
          <?php endif; ?>
        </div>
      </div>
    </a>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
  <?php else: ?>
  <div class="text-center py-10 px-4 rounded-xl border-2 border-dashed border-bottle/25 bg-gradient-to-br from-leafSoft/50 to-white">
    <svg class="w-10 h-10 text-bottle/40 mx-auto mb-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
      <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <p class="text-sm font-semibold text-gray-800">Belum ada jadwal meeting</p>
    <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
      <?php if(($pesananAktif ?? null) && ($activeBookingMeetings ?? collect())->isEmpty()): ?>
        Booking <strong><?php echo e($pesananAktif->nomor_pesanan); ?></strong> sudah aktif. Tim Brilliant akan menjadwalkan meeting vendor atau Anda dapat melihat pembaruan di halaman jadwal.
      <?php else: ?>
        Jadwal meeting vendor akan muncul setelah pembayaran DP terverifikasi atau lunas.
      <?php endif; ?>
    </p>
    <?php if($pesananAktif): ?>
    <a href="<?php echo e(route('client.jadwal', ['pesanan_id' => $pesananAktif->id, 'section' => 'meetings'])); ?>#vendor-meetings"
       class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-bottle hover:bg-bottleHover text-white text-xs font-semibold rounded-xl transition">
      Buka jadwal acara
    </a>
    <?php endif; ?>
  </div>
  <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/customer/modules/vendor-meetings/section_upcoming.blade.php ENDPATH**/ ?>