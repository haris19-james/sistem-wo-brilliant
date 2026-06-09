<?php $__env->startSection('title', 'Chat'); ?>
<?php $__env->startSection('page-title', 'Chat dengan Tim Brilliant'); ?>
<?php $__env->startSection('page-subtitle', 'Percakapan resmi terikat booking Anda'); ?>

<?php $__env->startSection('content'); ?>
<?php if($bookings->isEmpty()): ?>
<div class="max-w-lg mx-auto bg-white rounded-2xl border border-gray-100 p-10 text-center shadow-sm">
    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
    </svg>
    <h2 class="text-lg font-bold text-gray-900 mb-2">Belum ada booking</h2>
    <p class="text-sm text-gray-600 mb-6">Buat booking terlebih dahulu untuk memulai chat dengan tim kami.</p>
    <a href="<?php echo e(route('client.booking.create')); ?>" class="inline-flex px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition">
        Buat Booking
    </a>
</div>
<?php else: ?>
<div id="customerChatRoot" class="max-w-6xl mx-auto" data-send-url="<?php echo e($selectedPesanan ? route('client.chat.store', $selectedPesanan) : ''); ?>">

    <?php if($bookings->count() > 1): ?>
    <div class="mb-4 flex flex-col sm:flex-row sm:items-center gap-2">
        <label for="bookingSelect" class="text-sm font-medium text-gray-700 shrink-0">Pilih booking yang dibahas:</label>
        <select id="bookingSelect" onchange="if(this.value) window.location='<?php echo e(route('client.chat')); ?>?pesanan_id='+this.value"
            class="flex-1 max-w-md px-4 py-2 text-sm border border-gray-200 rounded-xl bg-white focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            <?php $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($b->id); ?>" <?php if($selectedPesanan?->id == $b->id): echo 'selected'; endif; ?>>
                <?php echo e($b->nama_pasangan); ?> — <?php echo e($b->nomor_pesanan); ?> (<?php echo e($b->tanggal_acara?->format('d M Y') ?? 'TBD'); ?>)
            </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 min-h-[calc(100vh-14rem)]">
        
        <div class="lg:col-span-8 bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col overflow-hidden">
            <?php if($thread): ?>
            <?php if($thread['show_review_cta']): ?>
            <div class="mx-4 mt-4 p-4 bg-green-50 border border-green-200 rounded-xl flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-start gap-3">
                    <div class="p-2 rounded-lg bg-green-100 text-green-600 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-green-900">Acara telah selesai</p>
                        <p class="text-xs text-green-800 mt-0.5">Berikan ulasan dan rating untuk vendor mitra kami.</p>
                    </div>
                </div>
                <a href="<?php echo e($thread['review_url']); ?>"
                    class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition shrink-0">
                    Isi Ulasan & Rating
                </a>
            </div>
            <?php endif; ?>

            <div class="px-5 py-4 border-b border-gray-100">
                <p class="text-xs text-gray-500 uppercase tracking-wide font-semibold">Chat Resmi · Booking #<?php echo e($thread['booking']['nomor']); ?></p>
                <h2 class="text-lg font-bold text-gray-900 mt-0.5"><?php echo e($thread['booking']['nama_acara']); ?></h2>
                <p class="text-xs text-gray-500 mt-1">Komunikasi dua arah dengan tim Brilliant WO — aman & terdokumentasi.</p>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-4 bg-gray-50/40 min-h-[280px] max-h-[50vh]" id="customerChatMessages">
                <?php $__empty_1 = true; $__currentLoopData = $thread['messages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex <?php echo e($msg['type'] === 'sent' ? 'justify-end' : 'justify-start'); ?>">
                    <div class="max-w-[88%] sm:max-w-[75%]">
                        <p class="text-[10px] font-semibold mb-1 <?php echo e($msg['type'] === 'sent' ? 'text-right text-green-700' : 'text-gray-500'); ?>">
                            <?php echo e($msg['sender_label']); ?>

                        </p>
                        <div class="px-4 py-2.5 rounded-2xl text-sm shadow-sm <?php echo e($msg['type'] === 'sent' ? 'bg-green-600 text-white rounded-br-md' : 'bg-white border border-gray-100 text-gray-900 rounded-bl-md'); ?>">
                            <p class="whitespace-pre-wrap leading-relaxed"><?php echo e($msg['text']); ?></p>
                            <div class="flex items-center justify-end gap-1 mt-1.5 <?php echo e($msg['type'] === 'sent' ? 'text-green-100' : 'text-gray-400'); ?>">
                                <span class="text-[10px]"><?php echo e($msg['time']); ?></span>
                                <?php if($msg['type'] === 'sent'): ?>
                                <span class="inline-flex" title="<?php echo e($msg['read_receipt'] === 'read' ? 'Sudah dibaca tim' : 'Terkirim'); ?>">
                                    <?php if($msg['read_receipt'] === 'read'): ?>
                                    <svg class="w-3.5 h-3.5 text-green-200" fill="currentColor" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41L9 16.17zm6.83-2.41l-1.41 1.41L18 19l1.41-1.41L15.83 13.76z"/></svg>
                                    <?php else: ?>
                                    <svg class="w-3.5 h-3.5 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <?php endif; ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="text-center text-gray-400 text-sm py-16">Belum ada pesan. Sampaikan pertanyaan Anda di bawah.</p>
                <?php endif; ?>
            </div>

            <form id="customerChatForm" method="POST" action="<?php echo e(route('client.chat.store', $selectedPesanan)); ?>" class="p-4 border-t border-gray-100 bg-white" data-no-loading data-ajax>
                <?php echo csrf_field(); ?>
                <div class="flex gap-2 items-end">
                    <textarea name="pesan" id="customerChatInput" rows="1" maxlength="2000" required
                        placeholder="Tulis pesan untuk tim Brilliant WO..."
                        class="flex-1 px-4 py-3 text-sm border border-gray-200 rounded-xl focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none resize-none bg-white"></textarea>
                    <button type="submit" id="customerChatSubmit" class="shrink-0 px-5 py-3 bg-green-600 hover:bg-green-700 disabled:opacity-60 text-white font-semibold rounded-xl text-sm transition shadow-sm">
                        Kirim
                    </button>
                </div>
            </form>
            <?php endif; ?>
        </div>

        
        <div class="lg:col-span-4">
            <?php if($thread): ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sticky top-4">
                <h3 class="text-sm font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Info Singkat Booking
                </h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Nama Acara</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($thread['booking']['nama_acara']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Tanggal Acara</dt>
                        <dd class="font-medium text-gray-800"><?php echo e($thread['booking']['tanggal'] ?? 'Akan ditentukan'); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Paket</dt>
                        <dd class="text-gray-800"><?php echo e($thread['booking']['paket']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Status Booking</dt>
                        <dd class="mt-1">
                            <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border <?php echo e($thread['booking']['status_class']); ?>">
                                <?php echo e($thread['booking']['status_label']); ?>

                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">No. Booking</dt>
                        <dd class="font-mono text-xs text-gray-600"><?php echo e($thread['booking']['nomor']); ?></dd>
                    </div>
                </dl>
                <p class="mt-5 pt-4 border-t border-gray-100 text-[11px] text-gray-500 leading-relaxed">
                    Anda sedang chat dalam konteks booking ini. Tim internal tidak menampilkan catatan rahasia kepada klien.
                </p>
                <a href="<?php echo e(route('client.pesanan_detail', $selectedPesanan->id)); ?>" class="mt-3 inline-block text-xs font-semibold text-green-600 hover:text-green-700">
                    Lihat detail pesanan →
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="<?php echo e(asset('js/customer-chat.js')); ?>?v=2"></script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.customer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/customer/modules/chat/index.blade.php ENDPATH**/ ?>