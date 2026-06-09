<?php $__env->startSection('title', 'Jadwal Acara'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  <!-- Header -->
  <div>
    <h1 class="text-3xl font-bold text-gray-900">Jadwal Acara</h1>
    <p class="text-sm text-gray-600 mt-1">Rundown dan timeline seluruh acara yang akan berlangsung.</p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- LEFT: Master list -->
    <div class="bg-white border border-gray-100 rounded-2xl p-4">
      <!-- Date picker -->
      <div class="mb-4">
        <label class="sr-only">Pilih tanggal</label>
        <div class="relative">
          <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <select name="tanggal" class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-0">
            <option value="">Pilih tanggal</option>
            
            <?php $__currentLoopData = $dates ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <option value="<?php echo e($d); ?>"><?php echo e($d); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </select>
        </div>
      </div>

      <!-- Events list -->
      <div class="divide-y divide-gray-100">
        <?php $__currentLoopData = $pesanans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $isActive = isset($pesanan) ? ($pesanan->id == $p->id) : ($loop->first);
          $colorDot = $p->color ?? 'bg-gray-400';
        ?>
        <a href="<?php echo e(route('lapangan.pesanan.show', $p)); ?>"
           class="flex items-center justify-between gap-3 p-3 rounded-lg transition <?php echo e($isActive ? 'bg-gray-50 border-l-4 border-green-200' : 'hover:bg-gray-50'); ?>">
          <div class="flex items-start gap-3 min-w-0">
            <div class="mt-1">
              <span class="inline-block w-3 h-3 rounded-full <?php echo e($colorDot); ?>"></span>
            </div>
            <div class="min-w-0">
              <p class="font-semibold text-sm text-gray-900 truncate"><?php echo e($p->nama_pasangan); ?></p>
              <p class="text-xs text-gray-500 truncate"><?php echo e($p->lokasi); ?></p>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <span class="text-xs text-gray-500"><?php echo e(substr((string)$p->jam_awal ?? '',0,5)); ?> - <?php echo e(substr((string)$p->jam_akhir ?? '',0,5)); ?></span>
            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>

      <!-- Bottom button -->
      <div class="mt-6">
        <a href="<?php echo e(route('lapangan.pesanan.index')); ?>" class="w-full inline-flex items-center justify-center gap-2 py-2 border border-gray-200 rounded-lg text-sm text-green-700 hover:bg-green-50">
          <svg class="w-5 h-5 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          Lihat Kalender Bulanan
        </a>
      </div>
    </div>

    <!-- RIGHT: Detail view (span 2 cols on lg) -->
    <div class="lg:col-span-2 bg-white border border-gray-100 rounded-2xl p-6">
      <?php $sel = $pesanan ?? ($pesanans->first() ?? null); ?>
      <?php if($sel): ?>
      <!-- Banner -->
      <div class="flex items-center gap-6 mb-6">
        <img src="<?php echo e($sel->foto_pernikahan ?? '/images/placeholder.jpg'); ?>" alt="thumbnail" class="w-28 h-20 rounded-lg object-cover border border-gray-100">
        <div class="flex-1 min-w-0">
          <h2 class="text-xl font-bold text-gray-900"><?php echo e($sel->nama_pasangan); ?></h2>
          <p class="text-sm text-gray-600 mt-1 flex items-center gap-3">
            <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8A5 5 0 117 8c0 7 5 11 5 11s5-4 5-11z"/></svg>
            <?php echo e($sel->lokasi); ?>

          </p>
          <div class="flex items-center gap-4 text-sm text-gray-600 mt-2">
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
              <span><?php echo e($sel->tanggal_acara?->translatedFormat('l, d F Y')); ?></span>
            </div>
            <div class="flex items-center gap-2">
              <svg class="w-4 h-4 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
              <span><?php echo e(substr((string)$sel->jam_awal ?? '',0,5)); ?> - <?php echo e(substr((string)$sel->jam_akhir ?? '',0,5)); ?> WIB</span>
            </div>
          </div>
        </div>

        <div class="flex flex-col items-end gap-3">
          <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700"><?php echo e($sel->status ?? 'Persiapan'); ?></span>
          <a href="<?php echo e(route('lapangan.pesanan.show', $sel)); ?>" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm hover:bg-gray-50">
            Lihat Detail Acara
            <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
          </a>
        </div>
      </div>

      <!-- Timeline table -->
      <div class="bg-white border border-gray-100 rounded-xl p-0 overflow-hidden">
        <div class="grid grid-cols-12 gap-0 items-start">
          <!-- Time column -->
          <div class="col-span-2 p-4 border-r border-gray-100 text-sm text-gray-600">
            <?php $__currentLoopData = $sel->rundowns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <div class="py-4"><?php echo e(substr((string)$r->waktu_mulai_formatted ?? ($r->waktu_mulai ?? ''),0,5)); ?></div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>

          <!-- Timeline middle -->
          <div class="col-span-1 p-4 border-r border-gray-100 flex flex-col items-center">
            <div class="h-full flex flex-col items-center justify-start space-y-4">
              <?php $__currentLoopData = $sel->rundowns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                  $status = $r->status ?? ($r->selesai ? 'Selesai' : 'Akan Datang');
                  $dotClass = $status === 'Selesai' ? 'bg-green-600' : ($status === 'Berlangsung' ? 'border-2 border-amber-400 bg-white' : 'bg-gray-300');
                ?>
                <div class="relative flex items-center">
                  <div class="w-3 h-3 rounded-full <?php echo e($dotClass); ?> flex items-center justify-center">
                    <?php if($status === 'Selesai'): ?>
                      <svg class="w-2 h-2 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          </div>

          <!-- Activity column -->
          <div class="col-span-6 p-4">
            <?php $__currentLoopData = $sel->rundowns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $status = $r->status ?? ($r->selesai ? 'Selesai' : 'Akan Datang'); ?>
            <div class="py-4 border-b border-gray-100 flex items-start justify-between">
              <div class="min-w-0">
                <p class="font-semibold text-gray-900"><?php echo e($r->kegiatan); ?></p>
                <p class="text-xs text-gray-500 mt-1"><?php echo e($r->keterangan ?? ''); ?></p>
              </div>

              <div class="ml-6 text-right space-y-1">
                <p class="text-xs text-gray-500"><?php echo e($r->vendor ?? $r->pic ?? '-'); ?></p>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                  <?php echo e($status === 'Selesai' ? 'bg-green-50 text-green-700' : ($status === 'Berlangsung' ? 'bg-amber-50 text-amber-700' : 'bg-gray-50 text-gray-700')); ?>">
                  <?php if($status === 'Selesai'): ?>
                    <svg class="w-3 h-3 text-green-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                  <?php elseif($status === 'Berlangsung'): ?>
                    <svg class="w-3 h-3 text-amber-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                  <?php else: ?>
                    <svg class="w-3 h-3 text-gray-700" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3"/></svg>
                  <?php endif; ?>
                  <?php echo e($status); ?>

                </span>
              </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>

          <!-- Spacer col for alignment -->
          <div class="col-span-3 p-4 hidden lg:block"></div>
        </div>
      </div>

      <?php else: ?>
      <p class="text-gray-500">Pilih acara dari daftar kiri untuk melihat rundown.</p>
      <?php endif; ?>
    </div>
  </div>

  
  <div id="vendor-meetings"
       class="scroll-mt-24"
       x-data="lapanganVendorMeetingDrawer(<?php echo \Illuminate\Support\Js::from($bookingsForMeeting ?? [])->toHtml() ?>)"
       x-init="init()">
    <?php echo $__env->make('lapangan.modules.vendor-meetings.section_upcoming_meetings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('lapangan.modules.partials.meeting-drawer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>window.__meetingDrawerOld = <?php echo json_encode(old() ?: [], 15, 512) ?>;</script>
<script src="<?php echo e(asset('js/lapangan-vendor-meeting-drawer.js')); ?>?v=2"></script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lapangan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/jadwal/index.blade.php ENDPATH**/ ?>