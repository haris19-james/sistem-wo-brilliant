<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'panel' => 'lapangan',
    'threads' => collect(),
    'filter' => 'all',
    'selectedPesananId' => null,
    'detail' => null,
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'panel' => 'lapangan',
    'threads' => collect(),
    'filter' => 'all',
    'selectedPesananId' => null,
    'detail' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isLapangan = $panel === 'lapangan';
    $indexRoute = $isLapangan ? route('lapangan.chat') : route('admin.chat');
    $sendRoute = fn ($id) => $isLapangan
        ? route('lapangan.chat.send', $id)
        : route('admin.chat.send', $id);
    $noteRoute = fn ($id) => $isLapangan
        ? route('lapangan.chat.internal-note', $id)
        : route('admin.chat.internal-note', $id);
    $filters = [
        'all' => 'Semua Chat',
        'unread' => 'Chat Belum Dibalas',
        'active' => 'Chat Aktif',
    ];
?>

<div class="flex flex-col h-[calc(100vh-10rem)] min-h-[520px]" id="bookingChatWorkspace"
     data-send-base="<?php echo e($isLapangan ? url('/lapangan/chat') : url('/admin/chat')); ?>"
     data-csrf="<?php echo e(csrf_token()); ?>">

    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chat / Pesan</h1>
            <p class="text-sm text-gray-600 mt-0.5">Setiap percakapan terikat pada <strong>ID Booking</strong> aktif.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e($indexRoute); ?>?filter=<?php echo e($key); ?><?php if($selectedPesananId): ?>&pesanan_id=<?php echo e($selectedPesananId); ?><?php endif; ?>"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border <?php echo e($filter === $key ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-200 hover:border-green-300'); ?>">
                <?php echo e($label); ?>

            </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-4 min-h-0">
        
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col min-h-0 overflow-hidden">
            <div class="p-3 border-b border-gray-100">
                <input type="search" id="chatThreadSearch" placeholder="Cari booking / klien..."
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-gray-50" id="chatThreadList">
                <?php $__empty_1 = true; $__currentLoopData = $threads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $thread): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <a href="<?php echo e($indexRoute); ?>?filter=<?php echo e($filter); ?>&pesanan_id=<?php echo e($thread['pesanan_id']); ?>"
                    class="block p-3 hover:bg-green-50/60 transition <?php echo e($selectedPesananId == $thread['pesanan_id'] ? 'bg-green-50 border-l-4 border-green-600' : ''); ?> chat-thread-item"
                    data-search="<?php echo e(strtolower($thread['nama_pasangan'].' '.$thread['client_name'].' '.$thread['nomor_pesanan'])); ?>">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 truncate"><?php echo e($thread['nama_pasangan']); ?></p>
                            <p class="text-xs text-gray-500 truncate"><?php echo e($thread['client_name']); ?> · <?php echo e($thread['nomor_pesanan']); ?></p>
                        </div>
                        <?php if($thread['unread_count'] > 0): ?>
                        <span class="shrink-0 bg-green-600 text-white text-[10px] font-bold min-w-[1.25rem] h-5 px-1.5 rounded-full flex items-center justify-center">
                            <?php echo e($thread['unread_count']); ?>

                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold border <?php echo e($thread['status_class']); ?>">
                            <?php echo e($thread['status_label']); ?>

                        </span>
                        <span class="text-[10px] text-gray-400"><?php echo e($thread['last_message_time']); ?></span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 truncate"><?php echo e($thread['last_message']); ?></p>
                </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="p-6 text-sm text-gray-500 text-center">Tidak ada chat untuk filter ini.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="lg:col-span-6 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col min-h-0 overflow-hidden">
            <?php if($detail): ?>
            <?php if($detail['show_review_banner']): ?>
            <div class="mx-4 mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 flex items-start gap-2">
                <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p><strong>Acara selesai.</strong> Arahkan klien untuk mengisi formulir ulasan (rating) di dashboard customer mereka.</p>
            </div>
            <?php endif; ?>

            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900"><?php echo e($detail['booking']['nama_pasangan']); ?></h2>
                    <p class="text-xs text-gray-500"><?php echo e($detail['booking']['client_name']); ?> · Booking #<?php echo e($detail['booking']['nomor']); ?></p>
                </div>
                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border <?php echo e($detail['booking']['status_class']); ?>">
                    <?php echo e($detail['booking']['status_label']); ?>

                </span>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50" id="chatMessagesBox">
                <?php $__currentLoopData = $detail['messages']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex <?php echo e($msg['type'] === 'sent' ? 'justify-end' : 'justify-start'); ?>">
                    <div class="max-w-[85%] px-4 py-2.5 rounded-2xl text-sm <?php echo e($msg['type'] === 'sent' ? 'bg-green-600 text-white rounded-br-md' : 'bg-white border border-gray-200 text-gray-900 rounded-bl-md shadow-sm'); ?>">
                        <?php if($msg['type'] === 'received'): ?>
                        <p class="text-[10px] font-semibold mb-1 text-green-700"><?php echo e($msg['sender_name']); ?></p>
                        <?php endif; ?>
                        <p class="whitespace-pre-wrap leading-relaxed"><?php echo e($msg['text']); ?></p>
                        <p class="text-[10px] mt-1 <?php echo e($msg['type'] === 'sent' ? 'text-green-100' : 'text-gray-400'); ?>"><?php echo e($msg['time']); ?></p>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <form id="chatSendForm" class="p-3 border-t border-gray-100 bg-white" data-pesanan-id="<?php echo e($selectedPesananId); ?>" data-no-loading data-ajax>
                <?php echo csrf_field(); ?>
                <div class="flex gap-2">
                    <textarea name="pesan" id="chatMessageInput" rows="1" maxlength="2000" required
                        placeholder="Balas klien (terikat booking ini)..."
                        class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none resize-none"></textarea>
                    <button type="submit" class="shrink-0 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition">
                        Kirim
                    </button>
                </div>
            </form>
            <?php else: ?>
            <div class="flex-1 flex items-center justify-center p-8 text-center text-gray-500 text-sm">
                Pilih chat booking dari daftar kiri untuk memulai percakapan.
            </div>
            <?php endif; ?>
        </div>

        
        <div class="lg:col-span-3 flex flex-col gap-4 min-h-0">
            <?php if($detail): ?>
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex-shrink-0">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Booking Sidebar
                </h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Nama Klien</dt>
                        <dd class="font-semibold text-gray-900"><?php echo e($detail['booking']['client_name']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Paket</dt>
                        <dd class="font-medium text-gray-800"><?php echo e($detail['booking']['paket']); ?></dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Status Booking</dt>
                        <dd><span class="inline-flex mt-0.5 px-2 py-0.5 rounded text-xs font-semibold border <?php echo e($detail['booking']['status_class']); ?>"><?php echo e($detail['booking']['status_label']); ?></span></dd>
                    </div>
                    <?php if($detail['booking']['tanggal_acara']): ?>
                    <div>
                        <dt class="text-xs text-gray-500">Tanggal Acara</dt>
                        <dd class="text-gray-800"><?php echo e($detail['booking']['tanggal_acara']); ?><?php if($detail['booking']['jam_acara']): ?> · <?php echo e($detail['booking']['jam_acara']); ?><?php endif; ?></dd>
                    </div>
                    <?php endif; ?>
                </dl>
                <div class="mt-4 pt-3 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-700 mb-2">Rundown Singkat</p>
                    <?php if(count($detail['booking']['rundown']) > 0): ?>
                    <ul class="space-y-1.5 max-h-32 overflow-y-auto">
                        <?php $__currentLoopData = $detail['booking']['rundown']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="text-xs text-gray-600 flex gap-2">
                            <span class="font-mono text-green-700 shrink-0"><?php echo e($r['waktu']); ?></span>
                            <span><?php echo e($r['kegiatan']); ?></span>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <?php else: ?>
                    <p class="text-xs text-gray-400">Belum ada rundown.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="bg-amber-50/80 rounded-xl border border-amber-200 p-4 flex flex-col min-h-0 flex-1 overflow-hidden">
                <h3 class="text-sm font-bold text-amber-900 mb-1">Internal Note</h3>
                <p class="text-[10px] text-amber-800 mb-3">Hanya tim internal (Admin/Korlap). Tidak terlihat customer.</p>
                <div class="flex-1 overflow-y-auto space-y-2 mb-3 min-h-[80px]" id="internalNotesList">
                    <?php $__currentLoopData = $detail['internal_notes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $note): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white/90 rounded-lg p-2 border border-amber-100 text-xs">
                        <p class="text-gray-800"><?php echo e($note['catatan']); ?></p>
                        <p class="text-[10px] text-gray-500 mt-1"><?php echo e($note['author']); ?> · <?php echo e($note['time']); ?></p>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <form id="internalNoteForm" data-pesanan-id="<?php echo e($selectedPesananId); ?>" data-no-loading data-ajax>
                    <?php echo csrf_field(); ?>
                    <textarea name="catatan" rows="2" maxlength="1000" required placeholder="Catatan internal tim..."
                        class="w-full px-3 py-2 text-xs border border-amber-200 rounded-lg bg-white focus:border-amber-400 outline-none resize-none"></textarea>
                    <button type="submit" class="mt-2 w-full py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg transition">
                        Simpan Catatan Internal
                    </button>
                </form>
            </div>
            <?php else: ?>
            <div class="bg-white rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500">
                Booking sidebar muncul saat chat dipilih.
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\sistem-wo-brilliant2\resources\views/components/chat/booking-workspace.blade.php ENDPATH**/ ?>