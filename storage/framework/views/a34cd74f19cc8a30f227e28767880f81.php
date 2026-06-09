<?php if($hasMaps): ?>
<div class="flex flex-wrap items-center gap-2">
    <a href="<?php echo e($href); ?>"
       data-open-google-maps
       data-maps-url="<?php echo e($href); ?>"
       class="font-semibold text-bottle hover:text-bottleHover hover:underline underline-offset-2 transition-colors inline-flex items-center gap-1.5 group">
        <span><?php echo e($pesanan->lokasi); ?></span>
        <svg class="w-4 h-4 shrink-0 opacity-70 group-hover:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
    </a>
    <a href="<?php echo e($href); ?>"
       data-open-google-maps
       data-maps-url="<?php echo e($href); ?>"
       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-red-50 text-red-700 border border-red-100 hover:bg-red-100 transition shrink-0"
       title="Buka di Google Maps">
        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
        Maps
    </a>
</div>
<?php else: ?>
<p class="font-semibold text-gray-900"><?php echo e($pesanan->lokasi); ?></p>
<?php if($showMissingHint ?? false): ?>
<p class="text-xs text-gray-400 mt-0.5">Link Google Maps belum diisi customer</p>
<?php endif; ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/pesanan/partials/location-maps-link.blade.php ENDPATH**/ ?>