<link rel="stylesheet" href="<?php echo e(asset('css/loading-premium.css')); ?>?v=4">

<div id="loading-overlay-premium"
     class="loading-overlay-premium hidden fixed inset-0 z-[9999] flex flex-col items-center justify-center bg-transparent"
     role="alertdialog"
     aria-modal="true"
     aria-busy="true"
     aria-hidden="true"
     aria-labelledby="loading-premium-logo"
     aria-describedby="loading-premium-message">

    <div class="flex flex-col items-center text-center px-6 pointer-events-none select-none">

        
        <div class="loading-premium-rings w-40 h-40 sm:w-48 sm:h-48 flex items-center justify-center animate-luxury-float">
            <img src="<?php echo e(asset('assets/img/cincin_wedding_brilliant_wo.png')); ?>"
                 alt="Memuat Brilliant WO"
                 width="192"
                 height="192"
                 class="w-full h-full object-contain loading-premium-rings__img"
                 draggable="false"
                 decoding="async">
        </div>

        <div id="loading-premium-logo" class="font-montserrat leading-none mt-5 loading-premium-caption">
            <p class="flex flex-wrap items-baseline justify-center gap-x-2 gap-y-0">
                <span class="font-playfair italic text-2xl text-amber-700 capitalize">Brilliant</span>
                <span class="font-montserrat font-semibold text-xl text-bottle uppercase tracking-[0.15em]">WO</span>
            </p>
            <div class="bg-gradient-to-r from-transparent via-amber-500/40 to-transparent w-16 h-[1px] mx-auto mt-2.5" aria-hidden="true"></div>
        </div>

        <p id="loading-premium-message"
           class="font-montserrat mt-3 text-xs text-gray-600 tracking-widest max-w-xs loading-premium-caption">
            Memproses permintaan Anda...
        </p>
    </div>
</div>

<script src="<?php echo e(asset('js/loading-premium.js')); ?>?v=5" defer></script>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/loading-overlay-premium.blade.php ENDPATH**/ ?>