

<?php $__env->startSection('title', 'Detail Jadwal Meeting Vendor'); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
  
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-gray-900"><?php echo e($meeting->title); ?></h1>
      <p class="text-sm text-gray-600 mt-1">Informasi detail jadwal meeting vendor</p>
    </div>
    <a href="<?php echo e(route('lapangan.jadwal')); ?>"
       class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
      <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
      </svg>
      Kembali
    </a>
  </div>

  
  <div class="p-4 rounded-xl border <?php echo e($meeting->status_badge_class); ?>">
    <div class="flex items-center justify-between">
      <div>
        <p class="font-semibold <?php echo e($meeting->status === 'completed' ? 'text-green-900' : ($meeting->status === 'ongoing' ? 'text-amber-900' : 'text-blue-900')); ?>">
          Status: <?php echo e($meeting->status_label); ?>

        </p>
        <?php if($meeting->isOverdue()): ?>
        <p class="text-sm text-red-600 mt-1">⚠️ Meeting ini sudah lewat tanggalnya!</p>
        <?php elseif($meeting->isToday()): ?>
        <p class="text-sm <?php echo e($meeting->status === 'completed' ? 'text-green-600' : 'text-red-600'); ?> mt-1">
          🎯 Meeting berlangsung HARI INI
        </p>
        <?php elseif($meeting->isUpcoming()): ?>
        <p class="text-sm text-blue-600 mt-1">⏰ Meeting akan berlangsung dalam 3 hari ke depan</p>
        <?php endif; ?>
      </div>
      <div class="text-right text-sm text-gray-600">
        Dibuat: <?php echo e($meeting->created_at->translatedFormat('d F Y H:i')); ?>

      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <div class="lg:col-span-2 space-y-6">
      
      <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-6">
        <div class="border-b border-gray-200 pb-6">
          <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-pink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Informasi Meeting
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <div class="bg-pink-50 rounded-lg p-4 border border-pink-100">
              <p class="text-xs font-semibold text-pink-700 uppercase tracking-wider">Tanggal</p>
              <p class="text-lg font-bold text-gray-900 mt-1"><?php echo e($meeting->meeting_date->translatedFormat('d F Y')); ?></p>
              <p class="text-xs text-pink-600 mt-1"><?php echo e($meeting->meeting_date->translatedFormat('l')); ?></p>
            </div>

            
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
              <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">Jam</p>
              <p class="text-lg font-bold text-gray-900 mt-1"><?php echo e($meeting->meeting_time); ?></p>
            </div>
          </div>
        </div>

        
        <div class="border-b border-gray-200 pb-6">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <div>
              <h3 class="font-semibold text-gray-900">Lokasi Meeting</h3>
              <p class="text-gray-600 mt-1"><?php echo e($meeting->location); ?></p>
            </div>
          </div>
        </div>

        
        <?php if($meeting->booking): ?>
        <div class="border-t border-gray-200 pt-6">
          <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            Informasi Customer
          </h3>
          <div class="bg-purple-50 rounded-lg p-4 border border-purple-100">
            <div class="space-y-3">
              <div>
                <p class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Nama Pasangan</p>
                <p class="text-gray-900 font-medium mt-1"><?php echo e($meeting->booking->nama_pasangan); ?></p>
              </div>
              <div>
                <p class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Kontak Customer</p>
                <p class="text-gray-900 mt-1"><?php echo e($meeting->booking->user->name ?? 'N/A'); ?></p>
                <p class="text-sm text-gray-600"><?php echo e($meeting->booking->user->email ?? '-'); ?></p>
              </div>
              <div>
                <p class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Hari H</p>
                <p class="text-gray-900 font-medium mt-1"><?php echo e($meeting->booking->tanggal_acara->translatedFormat('d F Y')); ?></p>
              </div>
            </div>
          </div>
        </div>
        <?php endif; ?>
      </div>

      
      <div class="bg-white border border-gray-100 rounded-2xl p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          Notulensi & Catatan
        </h2>

        <?php if($meeting->status === 'completed' && $meeting->notes): ?>
        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
          <div class="flex items-start gap-2 mb-3">
            <svg class="w-5 h-5 text-green-600 flex-shrink-0 mt-0.5" viewBox="0 0 24 24" fill="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <span class="font-semibold text-green-900">Meeting Sudah Selesai</span>
          </div>
          <div class="prose prose-sm max-w-none text-green-800 bg-white rounded p-3 border border-green-100">
            <?php echo nl2br(e($meeting->notes)); ?>

          </div>
          <p class="text-xs text-green-600 mt-3">
            Terakhir update: <?php echo e($meeting->updated_at->translatedFormat('d F Y H:i')); ?>

          </p>
        </div>
        <?php elseif($meeting->notes && $meeting->status !== 'completed'): ?>
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
          <p class="text-sm text-gray-600 mb-3">Catatan yang ada:</p>
          <div class="prose prose-sm max-w-none text-gray-800 bg-white rounded p-3 border border-gray-100">
            <?php echo nl2br(e($meeting->notes)); ?>

          </div>
        </div>
        <?php else: ?>
        <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
          <svg class="w-10 h-10 text-gray-300 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
          </svg>
          <p class="text-gray-500 font-medium">Belum ada notulensi</p>
          <p class="text-sm text-gray-400 mt-1">Notulensi akan muncul setelah meeting selesai</p>
        </div>
        <?php endif; ?>
      </div>
    </div>

    
    <div class="lg:col-span-1">
      <div class="bg-white border border-gray-100 rounded-2xl p-6 sticky top-6 space-y-4">
        <h3 class="font-bold text-gray-900">Aksi</h3>

        <?php if($meeting->status === 'completed'): ?>
        <div class="p-4 bg-green-50 rounded-lg border border-green-200">
          <p class="text-sm text-green-700 flex items-center gap-2">
            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
            </svg>
            <strong>Meeting Selesai</strong>
          </p>
        </div>
        <?php else: ?>
        
        <div class="space-y-3 border-b border-gray-200 pb-4">
          <p class="text-sm font-medium text-gray-600">Update Status Meeting</p>
          
          <form action="<?php echo e(route('lapangan.vendor-meetings.updateStatus', $meeting)); ?>" method="POST" class="space-y-2">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            
            <div class="grid grid-cols-2 gap-2">
              <?php if($meeting->status !== 'ongoing'): ?>
              <button type="submit" name="status" value="ongoing"
                      class="px-3 py-2 rounded-lg text-sm font-medium bg-yellow-50 text-yellow-700 border border-yellow-200 hover:bg-yellow-100 transition">
                ▶ Mulai Meeting
              </button>
              <?php endif; ?>
              
              <?php if($meeting->status !== 'scheduled'): ?>
              <button type="submit" name="status" value="scheduled" disabled
                      class="px-3 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-500 border border-gray-200 cursor-not-allowed">
                ← Jadwalkan Ulang
              </button>
              <?php endif; ?>
            </div>
          </form>
        </div>

        
        <button onclick="document.getElementById('completeModal').classList.remove('hidden')"
                class="w-full px-4 py-3 bg-gradient-to-r from-pink-500 to-rose-500 text-white font-semibold rounded-lg hover:from-pink-600 hover:to-rose-600 transition flex items-center justify-center gap-2">
          <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
            <path d="M5 13l4 4L19 7"/>
          </svg>
          Selesaikan Meeting
        </button>
        <?php endif; ?>

        
        <a href="<?php echo e(route('lapangan.jadwal')); ?>"
           class="w-full px-4 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition text-center">
          Kembali ke Jadwal
        </a>

        
        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
          <p class="text-xs font-semibold text-blue-900 uppercase tracking-wider mb-2">Instruksi</p>
          <ul class="text-xs text-blue-800 space-y-1">
            <li>✓ Review informasi meeting dengan teliti</li>
            <li>✓ Pastikan waktu dan lokasi sudah dikonfirmasi</li>
            <li>✓ Setelah meeting selesai, klik tombol "Selesaikan"</li>
            <li>✓ Isi notulensi dengan detail hasil diskusi</li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</div>


<?php echo $__env->make('lapangan.modules.vendor-meetings.complete_form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lapangan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/vendor-meetings/show.blade.php ENDPATH**/ ?>