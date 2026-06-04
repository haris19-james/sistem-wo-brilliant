@props([
    'panel' => 'lapangan',
    'threads' => collect(),
    'filter' => 'all',
    'selectedPesananId' => null,
    'detail' => null,
])

@php
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
@endphp

<div class="flex flex-col h-[calc(100vh-10rem)] min-h-[520px]" id="bookingChatWorkspace"
     data-send-base="{{ $isLapangan ? url('/lapangan/chat') : url('/admin/chat') }}"
     data-csrf="{{ csrf_token() }}">

    <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Chat / Pesan</h1>
            <p class="text-sm text-gray-600 mt-0.5">Setiap percakapan terikat pada <strong>ID Booking</strong> aktif.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            @foreach($filters as $key => $label)
            <a href="{{ $indexRoute }}?filter={{ $key }}@if($selectedPesananId)&pesanan_id={{ $selectedPesananId }}@endif"
                class="px-3 py-1.5 rounded-lg text-xs font-semibold transition border {{ $filter === $key ? 'bg-green-600 text-white border-green-600' : 'bg-white text-gray-700 border-gray-200 hover:border-green-300' }}">
                {{ $label }}
            </a>
            @endforeach
        </div>
    </div>

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-12 gap-4 min-h-0">
        {{-- Thread list --}}
        <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col min-h-0 overflow-hidden">
            <div class="p-3 border-b border-gray-100">
                <input type="search" id="chatThreadSearch" placeholder="Cari booking / klien..."
                    class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none">
            </div>
            <div class="flex-1 overflow-y-auto divide-y divide-gray-50" id="chatThreadList">
                @forelse($threads as $thread)
                <a href="{{ $indexRoute }}?filter={{ $filter }}&pesanan_id={{ $thread['pesanan_id'] }}"
                    class="block p-3 hover:bg-green-50/60 transition {{ $selectedPesananId == $thread['pesanan_id'] ? 'bg-green-50 border-l-4 border-green-600' : '' }} chat-thread-item"
                    data-search="{{ strtolower($thread['nama_pasangan'].' '.$thread['client_name'].' '.$thread['nomor_pesanan']) }}">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $thread['nama_pasangan'] }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $thread['client_name'] }} · {{ $thread['nomor_pesanan'] }}</p>
                        </div>
                        @if($thread['unread_count'] > 0)
                        <span class="shrink-0 bg-green-600 text-white text-[10px] font-bold min-w-[1.25rem] h-5 px-1.5 rounded-full flex items-center justify-center">
                            {{ $thread['unread_count'] }}
                        </span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-2">
                        <span class="inline-flex px-2 py-0.5 rounded text-[10px] font-semibold border {{ $thread['status_class'] }}">
                            {{ $thread['status_label'] }}
                        </span>
                        <span class="text-[10px] text-gray-400">{{ $thread['last_message_time'] }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 truncate">{{ $thread['last_message'] }}</p>
                </a>
                @empty
                <p class="p-6 text-sm text-gray-500 text-center">Tidak ada chat untuk filter ini.</p>
                @endforelse
            </div>
        </div>

        {{-- Chat panel --}}
        <div class="lg:col-span-6 bg-white rounded-xl border border-gray-200 shadow-sm flex flex-col min-h-0 overflow-hidden">
            @if($detail)
            @if($detail['show_review_banner'])
            <div class="mx-4 mt-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800 flex items-start gap-2">
                <svg class="w-5 h-5 shrink-0 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p><strong>Acara selesai.</strong> Arahkan klien untuk mengisi formulir ulasan (rating) di dashboard customer mereka.</p>
            </div>
            @endif

            <div class="p-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-gray-900">{{ $detail['booking']['nama_pasangan'] }}</h2>
                    <p class="text-xs text-gray-500">{{ $detail['booking']['client_name'] }} · Booking #{{ $detail['booking']['nomor'] }}</p>
                </div>
                <span class="inline-flex px-2.5 py-1 rounded-lg text-xs font-semibold border {{ $detail['booking']['status_class'] }}">
                    {{ $detail['booking']['status_label'] }}
                </span>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-gray-50/50" id="chatMessagesBox">
                @foreach($detail['messages'] as $msg)
                <div class="flex {{ $msg['type'] === 'sent' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] px-4 py-2.5 rounded-2xl text-sm {{ $msg['type'] === 'sent' ? 'bg-green-600 text-white rounded-br-md' : 'bg-white border border-gray-200 text-gray-900 rounded-bl-md shadow-sm' }}">
                        @if($msg['type'] === 'received')
                        <p class="text-[10px] font-semibold mb-1 text-green-700">{{ $msg['sender_name'] }}</p>
                        @endif
                        <p class="whitespace-pre-wrap leading-relaxed">{{ $msg['text'] }}</p>
                        <p class="text-[10px] mt-1 {{ $msg['type'] === 'sent' ? 'text-green-100' : 'text-gray-400' }}">{{ $msg['time'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <form id="chatSendForm" class="p-3 border-t border-gray-100 bg-white" data-pesanan-id="{{ $selectedPesananId }}" data-no-loading data-ajax>
                @csrf
                <div class="flex gap-2">
                    <textarea name="pesan" id="chatMessageInput" rows="1" maxlength="2000" required
                        placeholder="Balas klien (terikat booking ini)..."
                        class="flex-1 px-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:border-green-500 focus:ring-1 focus:ring-green-500 outline-none resize-none"></textarea>
                    <button type="submit" class="shrink-0 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl text-sm transition">
                        Kirim
                    </button>
                </div>
            </form>
            @else
            <div class="flex-1 flex items-center justify-center p-8 text-center text-gray-500 text-sm">
                Pilih chat booking dari daftar kiri untuk memulai percakapan.
            </div>
            @endif
        </div>

        {{-- Booking sidebar + internal notes --}}
        <div class="lg:col-span-3 flex flex-col gap-4 min-h-0">
            @if($detail)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex-shrink-0">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    Booking Sidebar
                </h3>
                <dl class="space-y-2 text-sm">
                    <div>
                        <dt class="text-xs text-gray-500">Nama Klien</dt>
                        <dd class="font-semibold text-gray-900">{{ $detail['booking']['client_name'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Paket</dt>
                        <dd class="font-medium text-gray-800">{{ $detail['booking']['paket'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500">Status Booking</dt>
                        <dd><span class="inline-flex mt-0.5 px-2 py-0.5 rounded text-xs font-semibold border {{ $detail['booking']['status_class'] }}">{{ $detail['booking']['status_label'] }}</span></dd>
                    </div>
                    @if($detail['booking']['tanggal_acara'])
                    <div>
                        <dt class="text-xs text-gray-500">Tanggal Acara</dt>
                        <dd class="text-gray-800">{{ $detail['booking']['tanggal_acara'] }}@if($detail['booking']['jam_acara']) · {{ $detail['booking']['jam_acara'] }}@endif</dd>
                    </div>
                    @endif
                </dl>
                <div class="mt-4 pt-3 border-t border-gray-100">
                    <p class="text-xs font-semibold text-gray-700 mb-2">Rundown Singkat</p>
                    @if(count($detail['booking']['rundown']) > 0)
                    <ul class="space-y-1.5 max-h-32 overflow-y-auto">
                        @foreach($detail['booking']['rundown'] as $r)
                        <li class="text-xs text-gray-600 flex gap-2">
                            <span class="font-mono text-green-700 shrink-0">{{ $r['waktu'] }}</span>
                            <span>{{ $r['kegiatan'] }}</span>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-xs text-gray-400">Belum ada rundown.</p>
                    @endif
                </div>
            </div>

            <div class="bg-amber-50/80 rounded-xl border border-amber-200 p-4 flex flex-col min-h-0 flex-1 overflow-hidden">
                <h3 class="text-sm font-bold text-amber-900 mb-1">Internal Note</h3>
                <p class="text-[10px] text-amber-800 mb-3">Hanya tim internal (Admin/Korlap). Tidak terlihat customer.</p>
                <div class="flex-1 overflow-y-auto space-y-2 mb-3 min-h-[80px]" id="internalNotesList">
                    @foreach($detail['internal_notes'] as $note)
                    <div class="bg-white/90 rounded-lg p-2 border border-amber-100 text-xs">
                        <p class="text-gray-800">{{ $note['catatan'] }}</p>
                        <p class="text-[10px] text-gray-500 mt-1">{{ $note['author'] }} · {{ $note['time'] }}</p>
                    </div>
                    @endforeach
                </div>
                <form id="internalNoteForm" data-pesanan-id="{{ $selectedPesananId }}" data-no-loading data-ajax>
                    @csrf
                    <textarea name="catatan" rows="2" maxlength="1000" required placeholder="Catatan internal tim..."
                        class="w-full px-3 py-2 text-xs border border-amber-200 rounded-lg bg-white focus:border-amber-400 outline-none resize-none"></textarea>
                    <button type="submit" class="mt-2 w-full py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg transition">
                        Simpan Catatan Internal
                    </button>
                </form>
            </div>
            @else
            <div class="bg-white rounded-xl border border-dashed border-gray-200 p-6 text-center text-sm text-gray-500">
                Booking sidebar muncul saat chat dipilih.
            </div>
            @endif
        </div>
    </div>
</div>
