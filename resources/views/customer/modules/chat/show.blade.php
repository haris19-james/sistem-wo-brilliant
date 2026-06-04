@extends('layouts.customer')

@section('title', 'Chat')
@section('page-title', 'Chat: '.$pesanan->nama_pasangan)
@section('page-subtitle', $pesanan->nomor_pesanan)

@section('content')
<div class="flex flex-wrap gap-3 mb-4 text-sm">
    <a href="{{ route('client.chat') }}" class="text-bottle font-semibold hover:underline">← Semua chat</a>
    <a href="{{ route('client.pesanan_detail', $pesanan->id) }}" class="text-gray-600 hover:text-bottle">Detail pesanan</a>
    <a href="{{ route('client.profile') }}" class="text-gray-600 hover:text-bottle ml-auto">Profil Saya</a>
    <a href="{{ route('client.profile.edit') }}" class="text-gray-600 hover:text-bottle">Pengaturan</a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col max-w-3xl" style="min-height: 420px;">
    <div class="p-4 border-b border-gray-100 flex items-center gap-3">
        @if($pesanan->paket?->image_url)
        <img src="{{ $pesanan->paket->image_url }}" class="w-10 h-10 rounded-lg object-cover" alt="">
        @endif
        <div>
            <p class="font-semibold text-sm">{{ $pesanan->nama_pasangan }}</p>
            <p class="text-xs text-gray-500">{{ $pesanan->paket?->nama_paket }} · Status: {{ $pesanan->status }}</p>
        </div>
    </div>

    <div class="flex-1 p-4 space-y-3 overflow-y-auto max-h-[22rem]" id="chat-messages">
        @forelse($pesanan->chatMessages as $msg)
        <div class="flex {{ $msg->dari_admin ? 'justify-start' : 'justify-end' }}">
            <div class="max-w-[85%] px-4 py-2.5 rounded-2xl text-sm {{ $msg->dari_admin ? 'bg-gray-100 text-gray-800 rounded-bl-md' : 'bg-bottle text-white rounded-br-md' }}">
                <p class="text-xs font-semibold mb-1 {{ $msg->dari_admin ? 'text-bottle' : 'text-green-100' }}">{{ $msg->dari_admin ? 'Admin Brilliant WO' : 'Anda' }}</p>
                <p class="whitespace-pre-wrap">{{ $msg->pesan }}</p>
                <p class="text-[10px] mt-1 opacity-60">{{ $msg->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-400 text-sm py-12">Belum ada pesan. Tulis pertanyaan untuk tim admin.</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('client.chat.store', $pesanan) }}" class="p-4 border-t border-gray-100 flex gap-2">
        @csrf
        <input type="text" name="pesan" required autofocus placeholder="Ketik pesan untuk admin..." class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:border-bottle focus:outline-none focus:ring-1 focus:ring-bottle">
        <button type="submit" class="px-6 py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover text-sm shrink-0">Kirim</button>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const el = document.getElementById('chat-messages');
    if (el) el.scrollTop = el.scrollHeight;
});
</script>
@endpush
@endsection
