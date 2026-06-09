<?php $__env->startSection('title', 'Beranda — Brilliant Event & Wedding Organizer'); ?>

<?php $__env->startPush('head'); ?>
<?php if($heroSlides = \App\Support\Branding::heroGallerySlides()): ?>
<link rel="preload" as="image" href="<?php echo e($heroSlides[0]['src']); ?>" fetchpriority="high">
<?php endif; ?>
<style>
    .organic-frame { border-radius: 60% 40% 50% 50% / 50% 50% 60% 40%; overflow: hidden; }
    .leaf-bg-1 { position: absolute; width: 500px; height: 500px; background: linear-gradient(135deg, var(--brilliant-leaf-bg) 0%, transparent 100%); border-radius: 0 50% 50% 50%; top: -100px; right: -50px; transform: rotate(-15deg); z-index: 0; opacity: 0.6; }
    .leaf-bg-2 { position: absolute; width: 400px; height: 400px; background: linear-gradient(135deg, var(--brilliant-leaf-bg) 0%, transparent 100%); border-radius: 50% 0 50% 50%; bottom: 0; left: 20%; transform: rotate(45deg); z-index: 0; opacity: 0.6; }
    @media (prefers-reduced-motion: reduce) {
        .hero-carousel-slide { transition: none !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<section class="relative pt-8 pb-20 container mx-auto px-6 flex flex-col lg:flex-row items-center overflow-hidden">
    <div class="leaf-bg-1"></div>
    <div class="leaf-bg-2"></div>

    <div class="w-full lg:w-1/2 relative z-10 text-center lg:text-left">
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-ink leading-tight mb-4">
            Wujudkan Momen <span class="text-bottleBright">Spesial Anda</span> Bersama Brilliant
        </h1>
        <p class="text-lg text-gray-700 font-medium"><?php echo e(config('brilliant.name')); ?> <?php echo e(config('brilliant.tagline')); ?></p>
        <p class="text-xl text-lime font-medium mb-8 italic">"<?php echo e(config('brilliant.motto')); ?>"</p>
        <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->role === 'client'): ?>
                <a href="<?php echo e(route('client.booking.create')); ?>" class="w-full sm:w-auto bg-bottle text-white font-semibold py-3 px-8 rounded-lg shadow-lg hover:bg-bottleHover text-center">Booking Sekarang</a>
                <?php elseif(auth()->user()->role === 'admin'): ?>
                <a href="<?php echo e(route('admin.dashboard')); ?>" class="w-full sm:w-auto bg-bottle text-white font-semibold py-3 px-8 rounded-lg hover:bg-bottleHover text-center">Panel Admin</a>
                <?php else: ?>
                <a href="<?php echo e(route('lapangan.dashboard')); ?>" class="w-full sm:w-auto bg-teal-700 text-white font-semibold py-3 px-8 rounded-lg text-center">Panel Lapangan</a>
                <?php endif; ?>
            <?php else: ?>
            <a href="<?php echo e(route('register')); ?>" data-no-loading class="w-full sm:w-auto bg-bottle text-white font-semibold py-3 px-8 rounded-lg shadow-lg hover:bg-bottleHover text-center">Mulai Booking</a>
            <a href="<?php echo e(route('login')); ?>" class="w-full sm:w-auto border-2 border-bottle text-bottle font-semibold py-3 px-8 rounded-lg hover:bg-leafSoft text-center">Masuk</a>
            <?php endif; ?>
            <a href="<?php echo e(route('contact')); ?>" class="w-full sm:w-auto bg-white text-gray-900 font-semibold py-3 px-8 rounded-lg border-2 border-gray-900 hover:bg-gray-100 text-center">Konsultasi</a>
        </div>
    </div>

    <div class="w-full lg:w-1/2 mt-12 lg:mt-0 relative z-10 flex justify-center">
        <?php if (isset($component)) { $__componentOriginal0e9334623d3ef4efd904c0c8f4ab5b17 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal0e9334623d3ef4efd904c0c8f4ab5b17 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.hero-portfolio-carousel','data' => ['class' => 'w-[280px] h-[280px] md:w-[420px] md:h-[420px]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('hero-portfolio-carousel'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-[280px] h-[280px] md:w-[420px] md:h-[420px]']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal0e9334623d3ef4efd904c0c8f4ab5b17)): ?>
<?php $attributes = $__attributesOriginal0e9334623d3ef4efd904c0c8f4ab5b17; ?>
<?php unset($__attributesOriginal0e9334623d3ef4efd904c0c8f4ab5b17); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal0e9334623d3ef4efd904c0c8f4ab5b17)): ?>
<?php $component = $__componentOriginal0e9334623d3ef4efd904c0c8f4ab5b17; ?>
<?php unset($__componentOriginal0e9334623d3ef4efd904c0c8f4ab5b17); ?>
<?php endif; ?>
    </div>
</section>


<section class="container mx-auto px-6 pb-16 border-t border-gray-100 pt-12">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <a href="<?php echo e(route('about')); ?>" class="flex gap-4 p-4 rounded-2xl hover:bg-leafSoft transition group">
            <div class="bg-leafSoft group-hover:bg-white p-3 rounded-full text-bottle h-fit"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
            <div>
                <h2 class="text-lg font-bold text-bottle mb-1 group-hover:underline">Profesional & Terpercaya</h2>
                <p class="text-sm text-gray-600">Tim berpengalaman di bidang event & wedding.</p>
            </div>
        </a>
        <a href="<?php echo e(route('paket')); ?>" class="flex gap-4 p-4 rounded-2xl hover:bg-leafSoft transition group">
            <div class="bg-leafSoft group-hover:bg-white p-3 rounded-full text-bottle h-fit"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></div>
            <div>
                <h2 class="text-lg font-bold text-bottle mb-1 group-hover:underline">Paket Beragam</h2>
                <p class="text-sm text-gray-600">Pilihan paket sesuai kebutuhan & budget.</p>
            </div>
        </a>
        <a href="<?php echo e(route('vendor')); ?>" class="flex gap-4 p-4 rounded-2xl hover:bg-leafSoft transition group">
            <div class="bg-leafSoft group-hover:bg-white p-3 rounded-full text-bottle h-fit"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg></div>
            <div>
                <h2 class="text-lg font-bold text-bottle mb-1 group-hover:underline">Vendor Terkurasi</h2>
                <p class="text-sm text-gray-600">Mitra vendor aktif & berkualitas.</p>
            </div>
        </a>
    </div>
</section>


<?php if($paketKustom): ?>
<section class="container mx-auto px-6 py-10">
    <div class="bg-gradient-to-r from-bottle to-bottleBright rounded-2xl p-6 md:p-8 text-white flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-white/70 mb-1">Paket Kustom</p>
            <h2 class="text-xl font-bold"><?php echo e($paketKustom->nama_paket); ?></h2>
            <p class="text-sm text-white/85 mt-1 max-w-xl">Masukkan budget → dapat perkiraan paket & layanan. Dikoordinasikan terpusat oleh Brilliant WO.</p>
        </div>
        <a href="<?php echo e(route('paket')); ?>" class="shrink-0 inline-block bg-white text-bottle font-bold py-3 px-6 rounded-xl hover:bg-leafSoft text-center">Pelajari Paket Kustom</a>
    </div>
</section>
<?php endif; ?>

<?php if($pakets->isNotEmpty()): ?>
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-6">
        <div class="text-center mb-10">
            <h2 class="text-3xl font-bold text-gray-900">Paket Populer</h2>
            <p class="text-gray-600 mt-2">Pilih paket pernikahan yang sesuai impian Anda</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php $__currentLoopData = $pakets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $paket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col hover:-translate-y-1 transition">
                <a href="<?php echo e(route('paket')); ?>#paket-<?php echo e($paket->id); ?>">
                    <?php if (isset($component)) { $__componentOriginal866276d147fd9806dc1448ec1d213bc4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal866276d147fd9806dc1448ec1d213bc4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.media-image','data' => ['src' => $paket->gambar,'fallback' => $paket->gambar_url,'alt' => $paket->nama_paket,'type' => 'package','wrapperClass' => 'w-full h-48 bg-leafSoft','imgClass' => 'w-full h-full object-cover']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('media-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paket->gambar),'fallback' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paket->gambar_url),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($paket->nama_paket),'type' => 'package','wrapper-class' => 'w-full h-48 bg-leafSoft','img-class' => 'w-full h-full object-cover']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal866276d147fd9806dc1448ec1d213bc4)): ?>
<?php $attributes = $__attributesOriginal866276d147fd9806dc1448ec1d213bc4; ?>
<?php unset($__attributesOriginal866276d147fd9806dc1448ec1d213bc4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal866276d147fd9806dc1448ec1d213bc4)): ?>
<?php $component = $__componentOriginal866276d147fd9806dc1448ec1d213bc4; ?>
<?php unset($__componentOriginal866276d147fd9806dc1448ec1d213bc4); ?>
<?php endif; ?>
                </a>
                <div class="p-6 flex flex-col flex-1">
                    <h3 class="text-xl font-bold text-bottle mb-1"><?php echo e($paket->nama_paket); ?></h3>
                    <p class="text-lg font-bold text-gray-900 mb-3">Rp <?php echo e(number_format($paket->harga, 0, ',', '.')); ?></p>
                    <p class="text-sm text-gray-600 mb-4 flex-1 line-clamp-2"><?php echo e($paket->deskripsi); ?></p>
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->user()->role === 'client'): ?>
                        <a href="<?php echo e(route('client.booking.create', ['paket_id' => $paket->id])); ?>" class="block text-center bg-bottle text-white font-semibold py-3 rounded-xl hover:bg-bottleHover">Booking Paket</a>
                        <?php else: ?>
                        <a href="<?php echo e(route('paket')); ?>" class="block text-center border border-bottle text-bottle font-semibold py-3 rounded-xl hover:bg-leafSoft">Lihat Detail</a>
                        <?php endif; ?>
                    <?php else: ?>
                    <a href="<?php echo e(route('register')); ?>" class="block text-center bg-bottle text-white font-semibold py-3 rounded-xl hover:bg-bottleHover">Daftar & Booking</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <div class="text-center mt-10">
            <a href="<?php echo e(route('paket')); ?>" class="inline-block bg-bottle text-white font-semibold py-3 px-8 rounded-lg hover:bg-bottleHover">Lihat Semua Paket</a>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if($vendors->isNotEmpty()): ?>
<section class="container mx-auto px-6 py-16">
    <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-10">
        <div>
            <h2 class="text-3xl font-bold text-gray-900">Vendor Rekomendasi</h2>
            <p class="text-gray-600 mt-1">Mitra terpercaya untuk acara Anda</p>
        </div>
        <a href="<?php echo e(route('vendor')); ?>" class="text-bottle font-semibold hover:underline">Lihat semua vendor →</a>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <?php $__currentLoopData = $vendors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vendor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('vendor.detail', $vendor)); ?>" class="bg-white rounded-2xl shadow border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-1 transition block">
            <?php if (isset($component)) { $__componentOriginal866276d147fd9806dc1448ec1d213bc4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal866276d147fd9806dc1448ec1d213bc4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.media-image','data' => ['src' => $vendor->gambar,'fallback' => $vendor->gambar_url,'alt' => $vendor->nama_vendor,'type' => 'vendor','wrapperClass' => 'w-full h-40 bg-gradient-to-br from-leafSoft to-green-100','imgClass' => 'w-full h-full object-cover']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('media-image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['src' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor->gambar),'fallback' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor->gambar_url),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor->nama_vendor),'type' => 'vendor','wrapper-class' => 'w-full h-40 bg-gradient-to-br from-leafSoft to-green-100','img-class' => 'w-full h-full object-cover']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal866276d147fd9806dc1448ec1d213bc4)): ?>
<?php $attributes = $__attributesOriginal866276d147fd9806dc1448ec1d213bc4; ?>
<?php unset($__attributesOriginal866276d147fd9806dc1448ec1d213bc4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal866276d147fd9806dc1448ec1d213bc4)): ?>
<?php $component = $__componentOriginal866276d147fd9806dc1448ec1d213bc4; ?>
<?php unset($__componentOriginal866276d147fd9806dc1448ec1d213bc4); ?>
<?php endif; ?>
            <div class="p-4">
                <h3 class="font-bold text-gray-900"><?php echo e($vendor->nama_vendor); ?></h3>
                <p class="text-sm text-gray-500"><?php echo e($vendor->kategori); ?><?php if($vendor->lokasi): ?> · <?php echo e($vendor->lokasi); ?><?php endif; ?></p>
                <div class="mt-2">
                    <?php if (isset($component)) { $__componentOriginal077a61d60611f096a94f8e1725d6bb16 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal077a61d60611f096a94f8e1725d6bb16 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.rating-stars','data' => ['value' => $vendor->rating_avg ?? 0,'count' => $vendor->rating_count ?? 0]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('rating-stars'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor->rating_avg ?? 0),'count' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($vendor->rating_count ?? 0)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal077a61d60611f096a94f8e1725d6bb16)): ?>
<?php $attributes = $__attributesOriginal077a61d60611f096a94f8e1725d6bb16; ?>
<?php unset($__attributesOriginal077a61d60611f096a94f8e1725d6bb16); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal077a61d60611f096a94f8e1725d6bb16)): ?>
<?php $component = $__componentOriginal077a61d60611f096a94f8e1725d6bb16; ?>
<?php unset($__componentOriginal077a61d60611f096a94f8e1725d6bb16); ?>
<?php endif; ?>
                </div>
            </div>
        </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>
<?php endif; ?>


<?php if(!empty($blogPosts)): ?>
<section class="bg-gray-50 py-16">
    <div class="container mx-auto px-6">
        <div class="flex flex-col sm:flex-row justify-between items-center gap-4 mb-10">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Blog & Inspirasi</h2>
                <p class="text-gray-600 mt-1">Tips dan cerita pernikahan terbaru</p>
            </div>
            <a href="<?php echo e(route('blog')); ?>" class="text-bottle font-semibold hover:underline">Semua artikel →</a>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php $__currentLoopData = $blogPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('pages.partials.blog-card', ['post' => $post], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/hero-portfolio-carousel.js')); ?>"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/pages/home.blade.php ENDPATH**/ ?>