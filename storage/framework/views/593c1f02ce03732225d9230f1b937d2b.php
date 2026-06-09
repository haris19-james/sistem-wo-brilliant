<?php if(session('urgent_toast')): ?>
<?php $toast = session('urgent_toast'); ?>
<div id="urgentToast" class="fixed top-20 right-4 z-[100] max-w-sm w-full pointer-events-none">
    <div class="pointer-events-auto flex items-start gap-3 p-4 rounded-xl border shadow-lg
        <?php echo e(($toast['type'] ?? '') === 'urgent' ? 'bg-red-50 border-red-200 text-red-900' : 'bg-green-50 border-green-200 text-green-900'); ?>">
        <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div class="flex-1 min-w-0">
            <p class="text-sm font-semibold">Pemberitahuan Penting</p>
            <p class="text-xs mt-0.5 leading-relaxed"><?php echo e($toast['message'] ?? ''); ?></p>
        </div>
        <button type="button" onclick="document.getElementById('urgentToast')?.remove()" class="text-gray-400 hover:text-gray-600 shrink-0">×</button>
    </div>
</div>
<script>
setTimeout(function() { document.getElementById('urgentToast')?.remove(); }, 8000);
</script>
<?php endif; ?>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/urgent-toast.blade.php ENDPATH**/ ?>