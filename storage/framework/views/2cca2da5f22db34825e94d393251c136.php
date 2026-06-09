<?php $__env->startSection('title', 'Pengaturan'); ?>
<?php $__env->startSection('page-title', 'Pengaturan'); ?>
<?php $__env->startSection('page-subtitle', $settingsSubtitle); ?>

<?php $__env->startSection('content'); ?>
<div class="min-h-screen bg-gray-50">
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-6 py-8">
            <h1 class="text-3xl font-bold text-gray-900">Pengaturan</h1>
            <p class="text-gray-600 mt-2"><?php echo e($settingsSubtitle); ?></p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 py-8">
        <?php if(session('success')): ?>
        <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <aside class="lg:col-span-1">
                <nav class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 h-fit sticky top-20">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4 px-2">Menu Pengaturan</h3>
                    <?php if (isset($component)) { $__componentOriginalf0e24510b21d06e548f8609ccd0ffe3a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf0e24510b21d06e548f8609ccd0ffe3a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.settings.sidebar-nav','data' => ['items' => $settingsMenuItems,'active' => $settingsSection,'variant' => 'lapangan']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('settings.sidebar-nav'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['items' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($settingsMenuItems),'active' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($settingsSection),'variant' => 'lapangan']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf0e24510b21d06e548f8609ccd0ffe3a)): ?>
<?php $attributes = $__attributesOriginalf0e24510b21d06e548f8609ccd0ffe3a; ?>
<?php unset($__attributesOriginalf0e24510b21d06e548f8609ccd0ffe3a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf0e24510b21d06e548f8609ccd0ffe3a)): ?>
<?php $component = $__componentOriginalf0e24510b21d06e548f8609ccd0ffe3a; ?>
<?php unset($__componentOriginalf0e24510b21d06e548f8609ccd0ffe3a); ?>
<?php endif; ?>
                </nav>
            </aside>

            <div class="lg:col-span-3 space-y-6">
                <?php switch($settingsSection):
                    case ('pengaturan_akun'): ?>
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8">
                            <h2 class="text-xl font-bold text-gray-900 mb-6">Profil Akun</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                                <div class="flex flex-col items-center md:col-span-1">
                                    <div class="w-24 h-24 rounded-full bg-green-50 border-2 border-green-100 flex items-center justify-center">
                                        <span class="text-3xl font-bold text-green-700"><?php echo e(strtoupper(substr($user->name ?? 'K', 0, 1))); ?></span>
                                    </div>
                                    <p class="mt-3 text-sm text-gray-500 text-center">Akun koordinator lapangan</p>
                                </div>
                                <div class="md:col-span-2">
                                    <form action="<?php echo e(route('lapangan.pengaturan.update')); ?>" method="POST" class="space-y-4">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PUT'); ?>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                                                <input type="text" name="nama_lengkap" value="<?php echo e($user->name ?? ''); ?>"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                                <input type="email" name="email" value="<?php echo e($user->email ?? ''); ?>"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                                                <input type="tel" name="nomor_telepon" value="<?php echo e($user->phone ?? $user->phone_number ?? ''); ?>"
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-green-500 outline-none" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                                                <input type="text" value="Koordinator Lapangan" disabled
                                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-500" />
                                            </div>
                                        </div>
                                        <button type="submit" class="bg-green-600 text-white px-6 py-2.5 rounded-xl font-semibold hover:bg-green-700 transition">
                                            Simpan Perubahan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php break; ?>

                    <?php case ('profil_korlap'): ?>
                        <?php echo $__env->make('settings.partials.profil-korlap', ['updateRoute' => route('lapangan.pengaturan.update')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php break; ?>

                    <?php case ('notifikasi'): ?>
                        <?php echo $__env->make('settings.partials.notifikasi', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php break; ?>

                    <?php case ('keamanan'): ?>
                        <?php echo $__env->make('settings.partials.keamanan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        <?php break; ?>

                    <?php default: ?>
                        <div class="rounded-xl border border-red-100 bg-red-50 p-6 text-sm text-red-700">Menu tidak tersedia untuk peran Anda.</div>
                <?php endswitch; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script>feather.replace();</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.lapangan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/pengaturan/index.blade.php ENDPATH**/ ?>