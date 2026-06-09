<div id="dashboard-live-refresh">
    <div class="rounded-2xl border lp-card bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div>
                <h2 class="text-base font-bold text-slate-900">Jadwal &amp; Rundown Acara Hari Ini</h2>
                <p class="text-xs text-slate-500 mt-0.5">Booking berstatus Confirmed · <?php echo e($hariIni->translatedFormat('d F Y')); ?></p>
            </div>
            <a href="<?php echo e(route('lapangan.jadwal')); ?>"
               class="lapangan-stat-detail lp-btn-outline text-xs font-semibold px-3 py-1.5 rounded-lg">
                Lihat semua
            </a>
        </div>

        <?php if($acaraHariIni->isNotEmpty()): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <?php $__currentLoopData = $acaraHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $acara): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if (isset($component)) { $__componentOriginaldeea39fbeea147a449094da154daf2b0 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaldeea39fbeea147a449094da154daf2b0 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.lapangan.acara-hari-ini-card','data' => ['acara' => $acara]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('lapangan.acara-hari-ini-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['acara' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($acara)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaldeea39fbeea147a449094da154daf2b0)): ?>
<?php $attributes = $__attributesOriginaldeea39fbeea147a449094da154daf2b0; ?>
<?php unset($__attributesOriginaldeea39fbeea147a449094da154daf2b0); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaldeea39fbeea147a449094da154daf2b0)): ?>
<?php $component = $__componentOriginaldeea39fbeea147a449094da154daf2b0; ?>
<?php unset($__componentOriginaldeea39fbeea147a449094da154daf2b0); ?>
<?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <?php if($jadwalHariIni->isNotEmpty()): ?>
        <div class="border-t border-leaf/40 pt-6">
            <p class="text-xs font-bold uppercase tracking-wide text-bottle mb-4">Timeline rundown hari ini</p>
            <div class="space-y-0">
                <?php $__currentLoopData = $jadwalHariIni; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rundown): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex gap-4 pb-6 last:pb-0">
                    <div class="flex flex-col items-center shrink-0">
                        <span class="flex h-3.5 w-3.5 rounded-full bg-bottle ring-4 ring-leafSoft border border-leaf"></span>
                        <?php if(!$loop->last): ?>
                        <span class="mt-2 w-px flex-1 min-h-[2rem] bg-leaf"></span>
                        <?php endif; ?>
                    </div>
                    <div class="min-w-0 flex-1 pt-0.5">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm font-bold text-bottle"><?php echo e($rundown->waktu_mulai_formatted ?? '—'); ?></p>
                            <?php if($rundown->waktu_selesai_formatted): ?>
                            <span class="text-xs text-slate-400">– <?php echo e($rundown->waktu_selesai_formatted); ?></span>
                            <?php endif; ?>
                            <?php if($rundown->pesanan): ?>
                            <span class="text-xs text-slate-500">· <?php echo e($rundown->pesanan->nama_pasangan); ?></span>
                            <?php endif; ?>
                        </div>
                        <p class="mt-1 text-sm font-medium text-slate-800"><?php echo e($rundown->kegiatan); ?></p>
                        <?php if(!empty($rundown->kategori_acara)): ?>
                        <span class="mt-2 inline-flex lp-badge rounded-full px-2.5 py-0.5 text-[10px] font-semibold uppercase"><?php echo e($rundown->kategori_acara); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="flex flex-col items-center justify-center py-14 px-4 text-center">
            <div class="lp-empty-icon flex h-20 w-20 items-center justify-center rounded-2xl mb-5">
                <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 14l2 2 4-4" opacity="0.5"/>
                </svg>
            </div>
            <p class="text-base font-semibold text-slate-800">Tidak ada acara dijadwalkan hari ini</p>
            <p class="text-sm text-slate-500 mt-2 max-w-sm">Belum ada booking Confirmed dengan tanggal acara hari ini. Cek jadwal mendatang atau hubungi admin.</p>
            <a href="<?php echo e(route('lapangan.jadwal')); ?>" class="lapangan-stat-detail mt-6 lp-btn-outline inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold">
                Buka kalender jadwal
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/dashboard_live_status.blade.php ENDPATH**/ ?>