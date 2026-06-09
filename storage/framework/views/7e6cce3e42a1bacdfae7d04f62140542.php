<?php $__env->startSection('title', 'Masuk - Brilliant WO'); ?>

<?php $__env->startSection('role_label', 'Masuk'); ?>

<?php $__env->startSection('subtitle'); ?>
Masuk ke Brilliant WO dengan email dan kata sandi Anda.
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<form method="POST" action="<?php echo e(route('login')); ?>" class="space-y-4">
    <?php echo csrf_field(); ?>
    <?php if (isset($component)) { $__componentOriginal4fe80e9d239d0b60843c2b8ddd36eccb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fe80e9d239d0b60843c2b8ddd36eccb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth.input','data' => ['label' => 'Email','name' => 'email','type' => 'email','value' => old('email'),'required' => true,'autocomplete' => 'email']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Email','name' => 'email','type' => 'email','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('email')),'required' => true,'autocomplete' => 'email']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fe80e9d239d0b60843c2b8ddd36eccb)): ?>
<?php $attributes = $__attributesOriginal4fe80e9d239d0b60843c2b8ddd36eccb; ?>
<?php unset($__attributesOriginal4fe80e9d239d0b60843c2b8ddd36eccb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fe80e9d239d0b60843c2b8ddd36eccb)): ?>
<?php $component = $__componentOriginal4fe80e9d239d0b60843c2b8ddd36eccb; ?>
<?php unset($__componentOriginal4fe80e9d239d0b60843c2b8ddd36eccb); ?>
<?php endif; ?>
    <?php if (isset($component)) { $__componentOriginal4fe80e9d239d0b60843c2b8ddd36eccb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4fe80e9d239d0b60843c2b8ddd36eccb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth.input','data' => ['label' => 'Kata Sandi','name' => 'password','type' => 'password','required' => true,'autocomplete' => 'current-password']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Kata Sandi','name' => 'password','type' => 'password','required' => true,'autocomplete' => 'current-password']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4fe80e9d239d0b60843c2b8ddd36eccb)): ?>
<?php $attributes = $__attributesOriginal4fe80e9d239d0b60843c2b8ddd36eccb; ?>
<?php unset($__attributesOriginal4fe80e9d239d0b60843c2b8ddd36eccb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4fe80e9d239d0b60843c2b8ddd36eccb)): ?>
<?php $component = $__componentOriginal4fe80e9d239d0b60843c2b8ddd36eccb; ?>
<?php unset($__componentOriginal4fe80e9d239d0b60843c2b8ddd36eccb); ?>
<?php endif; ?>
    <label class="flex items-center text-sm text-gray-600">
        <input type="checkbox" name="remember" class="mr-2 rounded border-gray-300 text-bottle focus:ring-bottle">
        Ingat saya
    </label>
    <?php if (isset($component)) { $__componentOriginal15a15053a35cef5a0af1d2ea8afb55eb = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal15a15053a35cef5a0af1d2ea8afb55eb = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.auth.submit-button','data' => ['text' => 'Masuk']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('auth.submit-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['text' => 'Masuk']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal15a15053a35cef5a0af1d2ea8afb55eb)): ?>
<?php $attributes = $__attributesOriginal15a15053a35cef5a0af1d2ea8afb55eb; ?>
<?php unset($__attributesOriginal15a15053a35cef5a0af1d2ea8afb55eb); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal15a15053a35cef5a0af1d2ea8afb55eb)): ?>
<?php $component = $__componentOriginal15a15053a35cef5a0af1d2ea8afb55eb; ?>
<?php unset($__componentOriginal15a15053a35cef5a0af1d2ea8afb55eb); ?>
<?php endif; ?>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('footer'); ?>
<p class="text-gray-600">
    Belum punya akun?
    <a href="<?php echo e(route('register')); ?>" data-no-loading class="text-bottle font-semibold hover:text-bottleHover hover:underline">Daftar</a>
</p>
<p>
    <a href="<?php echo e(route('home')); ?>" class="hover:text-bottle transition">← Beranda</a>
</p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.auth-login', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/auth/login.blade.php ENDPATH**/ ?>