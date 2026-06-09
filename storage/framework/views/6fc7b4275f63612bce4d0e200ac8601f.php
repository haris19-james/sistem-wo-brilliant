<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'Login - Brilliant WO'); ?></title>
    <?php echo $__env->yieldPushContent('head'); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <?php echo $__env->make('partials.brand-tailwind', ['extraColors' => [
        'field' => config('brilliant.colors.bottle'),
        'fieldHover' => config('brilliant.colors.bottle_hover'),
    ]], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</head>
<body class="min-h-screen flex items-center justify-center p-6 bg-gradient-to-br from-bottle via-bottleHover to-ink" data-brilliant-panel="auth">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl shadow-black/10 p-8 md:p-10 ring-1 ring-white/20">
        <div class="text-center mb-6 md:mb-8">
            <?php if (isset($component)) { $__componentOriginal3e23a466771c4cdcfd7540b30c912b11 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal3e23a466771c4cdcfd7540b30c912b11 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.public-logo','data' => ['size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('public-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['size' => 'lg']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal3e23a466771c4cdcfd7540b30c912b11)): ?>
<?php $attributes = $__attributesOriginal3e23a466771c4cdcfd7540b30c912b11; ?>
<?php unset($__attributesOriginal3e23a466771c4cdcfd7540b30c912b11); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal3e23a466771c4cdcfd7540b30c912b11)): ?>
<?php $component = $__componentOriginal3e23a466771c4cdcfd7540b30c912b11; ?>
<?php unset($__componentOriginal3e23a466771c4cdcfd7540b30c912b11); ?>
<?php endif; ?>
            <?php if (! empty(trim($__env->yieldContent('role_label')))): ?>
            <p class="text-sm text-bottle font-semibold uppercase tracking-wider mt-4"><?php echo $__env->yieldContent('role_label'); ?></p>
            <?php endif; ?>
            <?php if (! empty(trim($__env->yieldContent('subtitle')))): ?>
            <p class="text-xs text-gray-500 mt-1.5 max-w-xs mx-auto leading-relaxed"><?php echo $__env->yieldContent('subtitle'); ?></p>
            <?php endif; ?>
        </div>

        <?php if($errors->any()): ?>
        <div class="mb-5 p-3.5 bg-red-50 border border-red-100 text-red-700 text-sm rounded-xl" role="alert">
            <?php echo e($errors->first()); ?>

        </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>

        <?php if (! empty(trim($__env->yieldContent('footer')))): ?>
        <div class="mt-6 md:mt-8 pt-5 border-t border-gray-100 space-y-2 text-sm text-center text-gray-500">
            <?php echo $__env->yieldContent('footer'); ?>
        </div>
        <?php endif; ?>
    </div>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/layouts/auth-login.blade.php ENDPATH**/ ?>