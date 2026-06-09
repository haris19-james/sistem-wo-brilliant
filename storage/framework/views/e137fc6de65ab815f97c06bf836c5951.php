<?php $__env->startSection('title', 'Chat / Pesan'); ?>

<?php $__env->startSection('content'); ?>
<div class="px-4 sm:px-6 py-6">
    <?php if (isset($component)) { $__componentOriginal3bf2a0b9b266e187f97905ff7698b636 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3bf2a0b9b266e187f97905ff7698b636 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.chat.booking-workspace','data' => ['panel' => 'lapangan','threads' => $threads,'filter' => $filter,'selectedPesananId' => $selectedPesananId,'detail' => $detail]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('chat.booking-workspace'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['panel' => 'lapangan','threads' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($threads),'filter' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($filter),'selected-pesanan-id' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($selectedPesananId),'detail' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($detail)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3bf2a0b9b266e187f97905ff7698b636)): ?>
<?php $attributes = $__attributesOriginal3bf2a0b9b266e187f97905ff7698b636; ?>
<?php unset($__attributesOriginal3bf2a0b9b266e187f97905ff7698b636); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3bf2a0b9b266e187f97905ff7698b636)): ?>
<?php $component = $__componentOriginal3bf2a0b9b266e187f97905ff7698b636; ?>
<?php unset($__componentOriginal3bf2a0b9b266e187f97905ff7698b636); ?>
<?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/booking-chat.js')); ?>?v=1"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.lapangan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/lapangan/modules/chat/index.blade.php ENDPATH**/ ?>