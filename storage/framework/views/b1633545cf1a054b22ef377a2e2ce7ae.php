<?php $__env->startSection('title', $pesanan->nomor_pesanan); ?>
<?php $__env->startSection('page-title', $pesanan->nama_pasangan); ?>
<?php $__env->startSection('page-subtitle', $pesanan->nomor_pesanan.' · '.$pesanan->status); ?>

<?php $__env->startSection('content'); ?>
<a href="<?php echo e(route('lapangan.pesanan.index')); ?>" class="text-sm text-field font-semibold hover:underline mb-4 inline-block">← Kembali ke monitor acara</a>

<?php
    $prog = $timeline['progress'];
    $vendorLocked = ! $pesanan->hasFullScheduleAccess();
?>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 space-y-6">
        
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-gray-500">Client</p><p class="font-semibold"><?php echo e($pesanan->user?->name); ?></p><p class="text-xs text-gray-500"><?php echo e($pesanan->user?->phone_number); ?></p></div>
                <div><p class="text-gray-500">Paket</p><p class="font-semibold"><?php echo e($pesanan->paket?->nama_paket); ?></p></div>
                <div><p class="text-gray-500">Tanggal H</p><p class="font-semibold"><?php echo e($pesanan->tanggal_formatted); ?></p></div>
                <div><p class="text-gray-500">Jam</p><p class="font-semibold"><?php echo e(substr((string) $pesanan->jam_acara, 0, 5)); ?> WIB</p></div>
                <div class="col-span-2">
                    <?php if (isset($component)) { $__componentOriginald8098941d547d286dc0ffac5b213eff6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald8098941d547d286dc0ffac5b213eff6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.pesanan.location-display','data' => ['pesanan' => $pesanan,'label' => 'Lokasi Acara','class' => 'text-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('pesanan.location-display'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pesanan' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pesanan),'label' => 'Lokasi Acara','class' => 'text-sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald8098941d547d286dc0ffac5b213eff6)): ?>
<?php $attributes = $__attributesOriginald8098941d547d286dc0ffac5b213eff6; ?>
<?php unset($__attributesOriginald8098941d547d286dc0ffac5b213eff6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald8098941d547d286dc0ffac5b213eff6)): ?>
<?php $component = $__componentOriginald8098941d547d286dc0ffac5b213eff6; ?>
<?php unset($__componentOriginald8098941d547d286dc0ffac5b213eff6); ?>
<?php endif; ?>
                </div>
                <?php if($pesanan->catatan_khusus): ?><div class="col-span-2"><p class="text-gray-500">Catatan</p><p><?php echo e($pesanan->catatan_khusus); ?></p></div><?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border-2 border-field/20 p-6 shadow-sm">
            <div class="flex items-center gap-2 mb-1">
                <h3 class="font-bold text-gray-900">Update Progress Persiapan</h3>
                <span class="text-[10px] bg-teal-100 text-field px-2 py-0.5 rounded-full font-bold">Sinkron ke Customer</span>
            </div>
            <p class="text-xs text-gray-500 mb-4">Perubahan langsung terlihat di halaman Jadwal Acara customer.</p>

            <?php
                $progress = $pesanan->progress;
                $aspekKeys = ['venue' => 'Venue & Lokasi', 'makeup' => 'Makeup & Busana', 'catering' => 'Catering', 'dekorasi' => 'Dekorasi', 'dokumentasi' => 'Dokumentasi'];
                $initialStatuses = [];
                foreach (array_keys($aspekKeys) as $k) {
                    $initialStatuses[$k] = old('status_'.$k, $progress?->{'status_'.$k} ?? 'Menunggu');
                }
            ?>
            <form method="POST" action="<?php echo e(route('lapangan.pesanan.progress', $pesanan)); ?>" class="space-y-4"
                  x-data="progressPersiapanForm(<?php echo \Illuminate\Support\Js::from($initialStatuses)->toHtml() ?>, <?php echo e((int) old('persentase', $progress?->persentase ?? 0)); ?>)"
                  @submit="beforeSubmit()">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>
                <?php $__currentLoopData = $aspekKeys; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4">
                    <label class="text-sm font-medium text-gray-700 sm:w-40 shrink-0"><?php echo e($label); ?></label>
                    <select name="status_<?php echo e($key); ?>" x-model="statuses.<?php echo e($key); ?>"
                        class="flex-1 border border-gray-300 rounded-xl px-3 py-2 text-sm focus:border-field focus:outline-none">
                        <?php $__currentLoopData = ['Menunggu', 'Proses', 'Selesai']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($st); ?>"><?php echo e($st); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 pt-2 border-t">
                    <label class="text-sm font-medium text-gray-700 sm:w-40">Total Progress (%)</label>
                    <div class="flex flex-wrap items-center gap-2">
                        <input type="number" id="persentaseDisplay"
                            min="0" max="100" value="<?php echo e((int) old('persentase', $progress?->persentase ?? 0)); ?>"
                            class="w-32 border border-gray-300 rounded-xl px-3 py-2 text-sm bg-gray-100 text-gray-500 cursor-not-allowed"
                            readonly>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full bg-teal-100 text-field">
                            Dihitung Otomatis
                        </span>
                    </div>
                    <p class="text-xs text-gray-500 sm:flex-1">Progress total dihitung otomatis berdasarkan jumlah tugas (vendor) yang selesai diverifikasi oleh Admin.</p>
                </div>
                <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-field text-white font-bold rounded-xl hover:bg-fieldHover">
                    Simpan Progress
                </button>
            </form>
        </div>

        
        <div id="rundown-acara" class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm relative scroll-mt-24">
            <?php if($vendorLocked): ?>
            <div class="absolute inset-0 bg-white/80 backdrop-blur-[1px] rounded-2xl z-10 flex flex-col items-center justify-center p-6 text-center">
                <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <p class="font-semibold text-gray-700">Terkunci — Menunggu Pelunasan</p>
                <p class="text-xs text-gray-500 mt-1">Rundown Hari H terbuka setelah admin verifikasi pelunasan penuh.</p>
            </div>
            <?php endif; ?>
            <h3 class="font-bold mb-4">Rundown Hari H</h3>
            <?php if($pesanan->rundowns->isNotEmpty()): ?>
            <?php $__currentLoopData = $pesanan->rundowns->groupBy('kategori_acara'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kat => $items): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="text-sm font-semibold text-field mb-2"><?php echo e($kat); ?></p>
            <ul class="space-y-2 mb-4 text-sm">
                <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="p-2 bg-gray-50 rounded-lg"><?php echo e($r->waktu_mulai_formatted); ?>–<?php echo e($r->waktu_selesai_formatted ?? ''); ?> · <?php echo e($r->kegiatan); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
            <p class="text-sm text-gray-500">Rundown belum diisi admin.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-6">
        <?php if (isset($component)) { $__componentOriginal045a999b8d0b7f430a75b980cd82e8a6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal045a999b8d0b7f430a75b980cd82e8a6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.payment-status-card','data' => ['pesanan' => $pesanan,'panel' => 'lapangan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('payment-status-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['pesanan' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($pesanan),'panel' => 'lapangan']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal045a999b8d0b7f430a75b980cd82e8a6)): ?>
<?php $attributes = $__attributesOriginal045a999b8d0b7f430a75b980cd82e8a6; ?>
<?php unset($__attributesOriginal045a999b8d0b7f430a75b980cd82e8a6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal045a999b8d0b7f430a75b980cd82e8a6)): ?>
<?php $component = $__componentOriginal045a999b8d0b7f430a75b980cd82e8a6; ?>
<?php unset($__componentOriginal045a999b8d0b7f430a75b980cd82e8a6); ?>
<?php endif; ?>

        <a href="<?php echo e(route('lapangan.realisasi.index', $pesanan)); ?>" class="block bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:border-bottle/30 transition text-sm font-semibold text-bottle">
            Laporan Penggunaan Dana Operasional →
        </a>

        
        <div class="bg-gradient-to-br from-field to-bottle text-white rounded-2xl p-6 shadow-lg">
            <p class="text-sm opacity-90">Progress Keseluruhan</p>
            <p class="text-5xl font-black mt-1"><?php echo e($prog['persentase']); ?>%</p>
            <p class="text-xs opacity-80 mt-2"><?php echo e($prog['selesai']); ?>/<?php echo e($prog['total']); ?> aspek selesai</p>
            <div class="h-2 bg-white/30 rounded-full mt-4 overflow-hidden">
                <div class="h-full bg-white rounded-full" style="width: <?php echo e($prog['persentase']); ?>%"></div>
            </div>
        </div>

        
        <?php if($pesanan->vendors->isNotEmpty()): ?>
        <div class="bg-white rounded-2xl border border-rose-100 p-5 shadow-sm hover:shadow-md transition-shadow relative">
            <?php if($vendorLocked): ?>
            <div class="absolute inset-0 bg-white/85 backdrop-blur-[1px] rounded-2xl z-10 flex flex-col items-center justify-center p-6 text-center">
                <svg class="w-10 h-10 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <p class="font-semibold text-gray-700">Terkunci — Menunggu Pelunasan</p>
                <p class="text-xs text-gray-500 mt-1">Daftar vendor eksternal tersembunyi hingga status lunas.</p>
            </div>
            <?php endif; ?>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-gradient-to-br from-rose-100 to-pink-100 rounded-lg">
                        <svg class="w-5 h-5 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 3.5c2.5 0 4.5 2 4.5 4.5S12.5 12.5 10 12.5 5.5 10.5 5.5 8 7.5 3.5 10 3.5zM2 15c0-1.5 3-2.5 8-2.5s8 1 8 2.5v2H2v-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">Vendor Hari Ini</h3>
                        <p class="text-xs text-gray-500"><?php echo e($pesanan->tanggal_formatted); ?></p>
                    </div>
                </div>
                <span class="text-sm bg-rose-100 text-rose-700 px-3 py-1.5 rounded-full font-semibold">
                    <?php echo e($pesanan->vendors->count()); ?> vendor
                </span>
            </div>

            <div class="space-y-3">
                <?php $__currentLoopData = $pesanan->vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $currentStatus = $vendor->pivot->status ?? 'Belum Hadir';
                    
                    $statusConfig = [
                        'Belum Hadir' => [
                            'active' => 'bg-gradient-to-r from-gray-300 to-gray-400 text-gray-900 shadow-md',
                            'inactive' => 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                            'icon' => '❌',
                            'bgGradient' => 'from-gray-50 to-slate-50',
                            'borderColor' => 'border-gray-200'
                        ],
                        'Perjalanan' => [
                            'active' => 'bg-gradient-to-r from-amber-400 to-orange-500 text-amber-900 shadow-md',
                            'inactive' => 'bg-amber-100 text-amber-700 hover:bg-amber-200',
                            'icon' => '🚗',
                            'bgGradient' => 'from-amber-50 to-orange-50',
                            'borderColor' => 'border-amber-200'
                        ],
                        'Hadir' => [
                            'active' => 'bg-gradient-to-r from-green-400 to-emerald-500 text-green-900 shadow-md',
                            'inactive' => 'bg-green-100 text-green-700 hover:bg-green-200',
                            'icon' => '✅',
                            'bgGradient' => 'from-green-50 to-emerald-50',
                            'borderColor' => 'border-green-200'
                        ]
                    ];

                    $statusOptions = ['Belum Hadir', 'Perjalanan', 'Hadir'];
                ?>
                
                <div class="p-4 bg-gradient-to-br <?php echo e($statusConfig[$currentStatus]['bgGradient']); ?> rounded-xl border <?php echo e($statusConfig[$currentStatus]['borderColor']); ?> transition-all duration-200 hover:shadow-md">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-gray-900 truncate">
                                <?php echo e($vendor->nama_vendor); ?>

                            </p>
                            <p class="text-xs font-medium text-rose-600">
                                <?php echo e($vendor->kategori); ?>

                            </p>
                            
                            <?php if($vendor->pivot->waktu_setup): ?>
                            <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                                <span class="text-sm">⏰</span>
                                <span class="font-mono text-gray-700"><?php echo e($vendor->pivot->waktu_setup); ?></span>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <span class="text-3xl shrink-0">
                            <?php echo e($statusConfig[$currentStatus]['icon']); ?>

                        </span>
                    </div>
                    
                    <div class="flex flex-wrap gap-2 pt-3 border-t <?php echo e($statusConfig[$currentStatus]['borderColor']); ?>">
                        <?php $__currentLoopData = $statusOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" 
                            class="text-xs px-3 py-1.5 rounded-full font-semibold transition-all duration-200 update-vendor-status cursor-pointer
                                <?php echo e(($currentStatus === $status) 
                                    ? $statusConfig[$status]['active'] 
                                    : $statusConfig[$status]['inactive']); ?>"
                            data-vendor-id="<?php echo e($vendor->id); ?>"
                            data-vendor-name="<?php echo e($vendor->nama_vendor); ?>"
                            data-status="<?php echo e($status); ?>"
                            data-pesanan-id="<?php echo e($pesanan->id); ?>"
                            title="Ubah status ke <?php echo e($status); ?>">
                            <?php echo e($status); ?>

                        </button>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(\Illuminate\Support\Facades\Schema::hasTable('item_tambahan')): ?>
            <?php $paidAddons = $pesanan->itemTambahan->where('status', 'paid'); ?>
            <?php if($paidAddons->isNotEmpty()): ?>
            <div class="bg-white rounded-2xl border border-teal-100 p-5 shadow-sm">
                <h3 class="font-bold mb-1 flex items-center gap-2">
                    Checklist Item Tambahan
                    <span class="text-[10px] bg-teal-100 text-teal-800 px-2 py-0.5 rounded-full font-bold">Lunas</span>
                </h3>
                <p class="text-xs text-gray-500 mb-3">Otomatis masuk ke progress persiapan setelah customer membayar.</p>
                <div class="space-y-2">
                    <?php $__currentLoopData = $paidAddons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="p-3 bg-teal-50/50 rounded-xl border border-teal-200 text-sm">
                        <p class="font-semibold text-gray-900"><?php echo e($item->kategori); ?> — <?php echo e($item->deskripsi); ?></p>
                        <p class="text-xs text-gray-600 mt-0.5"><?php echo e($item->jumlah); ?> unit · Vendor: <?php echo e($item->progressKey() ?? 'umum'); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>
        <?php elseif(\Illuminate\Support\Facades\Schema::hasTable('booking_addons') && $pesanan->bookingAddons->isNotEmpty()): ?>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h3 class="font-bold mb-3">Catatan Item Tambahan</h3>
            <?php $__currentLoopData = $pesanan->bookingAddons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $addon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <p class="text-sm"><?php echo e($addon->nama_item); ?></p>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <?php endif; ?>

        
        <?php if($tasks->isNotEmpty()): ?>
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h3 class="font-bold mb-4">Tugas Lapangan</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                <?php $__currentLoopData = $tasks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $task): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="p-3 bg-gray-50 rounded-lg text-xs">
                    <div class="flex items-start justify-between gap-2 mb-1">
                        <span class="font-semibold flex-1"><?php echo e($task->nama_tugas); ?></span>
                        <span class="px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-medium text-[10px] shrink-0">
                            <?php
                                $statusLabel = match($task->status) {
                                    'pending' => 'Tertunda',
                                    'in_progress' => 'Berjalan',
                                    'completed' => 'Selesai',
                                    default => $task->status
                                };
                            ?>
                            <?php echo e($statusLabel); ?>

                        </span>
                    </div>
                    <p class="text-gray-500 mb-1"><?php echo e($task->kategori); ?> · Prioritas: <?php echo e(ucfirst($task->prioritas)); ?></p>
                    <?php if($task->pic): ?>
                    <p class="text-gray-400 text-[11px]">PIC: <?php echo e($task->pic->name); ?></p>
                    <?php endif; ?>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h3 class="font-bold mb-3">Laporan Lapangan</h3>
            <form method="POST" action="<?php echo e(route('lapangan.pesanan.laporan', $pesanan)); ?>" class="space-y-3 text-sm">
                <?php echo csrf_field(); ?>
                <input type="date" name="tanggal" value="<?php echo e(old('tanggal', now()->toDateString())); ?>" max="<?php echo e(now()->toDateString()); ?>" required
                    class="w-full border border-gray-300 rounded-xl px-3 py-2">
                <select name="kondisi" required class="w-full border border-gray-300 rounded-xl px-3 py-2">
                    <option value="Baik">Baik</option>
                    <option value="Perhatian">Perhatian</option>
                    <option value="Kritis">Kritis</option>
                </select>
                <textarea name="ringkasan" rows="3" required placeholder="Ringkasan kondisi di lapangan..."
                    class="w-full border border-gray-300 rounded-xl px-3 py-2"></textarea>
                <textarea name="tindak_lanjut" rows="2" placeholder="Tindak lanjut (opsional)"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2"></textarea>
                <button type="submit" class="w-full py-2.5 bg-gray-800 text-white font-semibold rounded-xl hover:bg-gray-900">Kirim Laporan</button>
            </form>
            <div class="mt-4 space-y-2 max-h-64 overflow-y-auto">
                <?php $__empty_1 = true; $__currentLoopData = $pesanan->laporanLapangans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lap): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="p-3 bg-gray-50 rounded-lg text-xs">
                    <div class="flex justify-between gap-2">
                        <span class="font-bold px-1.5 py-0.5 rounded border <?php echo e($lap->kondisi_badge_class); ?>"><?php echo e($lap->kondisi); ?></span>
                        <span class="text-gray-400"><?php echo e($lap->tanggal->format('d M Y')); ?></span>
                    </div>
                    <p class="mt-2 text-gray-700"><?php echo e($lap->ringkasan); ?></p>
                    <?php if($lap->tindak_lanjut): ?><p class="mt-1 text-gray-500"><strong>TL:</strong> <?php echo e($lap->tindak_lanjut); ?></p><?php endif; ?>
                    <p class="text-gray-400 mt-1"><?php echo e($lap->user?->name); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-gray-500 text-center py-4">Belum ada laporan.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <h3 class="font-bold mb-3">Meeting Vendor</h3>
            <?php $__empty_1 = true; $__currentLoopData = $pesanan->jadwalMeetings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="text-sm py-2 border-b last:border-0">
                <p class="font-semibold"><?php echo e($m->judul_meeting); ?></p>
                <p class="text-xs text-gray-500"><?php echo e($m->tanggal_meeting->format('d M Y')); ?> · <?php echo e($m->waktu_meeting_formatted); ?></p>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-xs text-gray-500">Tidak ada meeting.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/lapangan-progress-form.js')); ?>?v=1"></script>
<script>
(function() {
    'use strict';
    
    const statusColorMap = {
        'Belum Hadir': {
            active: ['bg-gradient-to-r', 'from-gray-300', 'to-gray-400', 'text-gray-900', 'shadow-md'],
            inactive: ['bg-gray-100', 'text-gray-600', 'hover:bg-gray-200']
        },
        'Perjalanan': {
            active: ['bg-gradient-to-r', 'from-amber-400', 'to-orange-500', 'text-amber-900', 'shadow-md'],
            inactive: ['bg-amber-100', 'text-amber-700', 'hover:bg-amber-200']
        },
        'Hadir': {
            active: ['bg-gradient-to-r', 'from-green-400', 'to-emerald-500', 'text-green-900', 'shadow-md'],
            inactive: ['bg-green-100', 'text-green-700', 'hover:bg-green-200']
        }
    };

    document.querySelectorAll('.update-vendor-status').forEach(button => {
        button.addEventListener('click', handleVendorStatusClick);
    });

    async function handleVendorStatusClick(e) {
        e.preventDefault();
        
        const button = this;
        const vendorId = button.dataset.vendorId;
        const vendorName = button.dataset.vendorName;
        const newStatus = button.dataset.status;
        const pesananId = button.dataset.pesananId;

        button.disabled = true;
        const originalContent = button.textContent;
        button.innerHTML = '<span class="animate-spin inline-block">⏳</span>';

        try {
            updateButtonUI(button, newStatus);

            const response = await fetch(`/lapangan/pesanan/${pesananId}/vendor-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({
                    vendor_id: vendorId,
                    status: newStatus
                })
            });

            const data = await response.json();

            if (!response.ok || !data.success) {
                throw new Error(data.error || 'Gagal mengupdate status');
            }

            showNotification(`✅ ${vendorName} → ${newStatus}`, 'success');

            if (newStatus === 'Hadir') {
                setTimeout(() => location.reload(), 1500);
            }

        } catch (error) {
            console.error('Error:', error);
            
            button.innerHTML = originalContent;
            button.disabled = false;
            
            showNotification(`❌ ${error.message}`, 'error');
            setTimeout(() => location.reload(), 2000);
        }
    }

    function updateButtonUI(button, newStatus) {
        const container = button.closest('div').querySelectorAll('.update-vendor-status');
        
        container.forEach(btn => {
            Object.values(statusColorMap).forEach(colors => {
                btn.classList.remove(...colors.active, ...colors.inactive);
            });
            btn.classList.add(...statusColorMap['Belum Hadir'].inactive);
        });

        button.classList.remove(...statusColorMap[newStatus].inactive);
        button.classList.add(...statusColorMap[newStatus].active);
    }

    function showNotification(message, type = 'info') {
        const bgColor = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
        
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 ${bgColor} border px-4 py-3 rounded-lg z-50 shadow-lg animate-fade-in max-w-sm`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.classList.add('animate-fade-out');
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    function getCsrfToken() {
        return document.querySelector('meta[name="csrf-token"]')?.content || 
               document.querySelector('input[name="_token"]')?.value || '';
    }
})();
</script>

<style>
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-10px);
        }
    }

    .animate-fade-in {
        animation: fadeIn 0.3s ease-out;
    }

    .animate-fade-out {
        animation: fadeOut 0.3s ease-out;
    }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lapangan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/pesanan/show.blade.php ENDPATH**/ ?>