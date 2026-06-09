<?php
    $c = config('brilliant.colors');
    $extraColors = $extraColors ?? [];
    $fontSerif = $fontSerif ?? false;
?>
<link rel="stylesheet" href="<?php echo e(asset('css/brilliant-brand.css')); ?>">
<script>
    tailwind.config = {
        theme: {
            extend: {
                colors: {
                    bottle: <?php echo json_encode($c['bottle'], 15, 512) ?>,
                    bottleHover: <?php echo json_encode($c['bottle_hover'], 15, 512) ?>,
                    bottleBright: <?php echo json_encode($c['bottle_bright'], 15, 512) ?>,
                    lime: <?php echo json_encode($c['lime'], 15, 512) ?>,
                    leafSoft: <?php echo json_encode($c['leaf_soft'], 15, 512) ?>,
                    leaf: <?php echo json_encode($c['leaf'], 15, 512) ?>,
                    leafBg: <?php echo json_encode($c['leaf_bg'], 15, 512) ?>,
                    ink: <?php echo json_encode($c['ink'], 15, 512) ?>,
                    grayBox: <?php echo json_encode($c['gray_box'], 15, 512) ?>,
                    golden: <?php echo json_encode($c['golden'], 15, 512) ?>,
                    goldenHover: <?php echo json_encode($c['golden_hover'], 15, 512) ?>,
                    goldenSoft: <?php echo json_encode($c['golden_soft'], 15, 512) ?>,
                    <?php $__currentLoopData = $extraColors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo e($key); ?>: <?php echo json_encode($value, 15, 512) ?>,
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                },
                fontFamily: {
                    sans: ['Poppins', 'sans-serif'],
                    <?php if($fontSerif): ?>
                    serif: ['Playfair Display', 'serif'],
                    <?php endif; ?>
                },
            }
        }
    }
</script>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/partials/brand-tailwind.blade.php ENDPATH**/ ?>