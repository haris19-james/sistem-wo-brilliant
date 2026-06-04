@extends('layouts.admin')

@section('title', 'Chat')
@section('page-title', 'Chat: '.$pesanan->nama_pasangan)
@section('page-subtitle', $pesanan->nomor_pesanan.' — '.$pesanan->user?->email)

@section('content')
<a href="{{ route('admin.chat') }}" class="text-sm text-bottle font-semibold hover:underline mb-4 inline-block">← Kembali</a>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm max-w-3xl">
    <div class="p-4 space-y-3 max-h-[28rem] overflow-y-auto">
        @forelse($pesanan->chatMessages as $msg)
        <div class="flex {{ $msg->dari_admin ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[80%] px-4 py-2 rounded-2xl text-sm {{ $msg->dari_admin ? 'bg-bottle text-white rounded-br-none' : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                <p class="text-xs font-semibold mb-1 opacity-75">{{ $msg->dari_admin ? 'Admin (Anda)' : $msg->user?->name }}</p>
                <p class="whitespace-pre-wrap">{{ $msg->pesan }}</p>
                <p class="text-[10px] mt-1 opacity-60">{{ $msg->created_at->format('d M Y H:i') }}</p>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-400 py-8">Belum ada pesan dari customer.</p>
        @endforelse
    </div>
    <form method="POST" action="{{ route('admin.chat.store', $pesanan) }}" class="p-4 border-t border-gray-100 flex gap-2">
        @csrf
        <input type="text" name="pesan" required placeholder="Balas customer..." class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:border-bottle focus:outline-none">
        <button type="submit" class="px-5 py-2.5 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover text-sm">Kirim</button>
    </form>
</div>

<div class="mt-4">
    <a href="{{ route('admin.booking.show', $pesanan) }}" class="text-sm text-gray-600 hover:text-bottle">Lihat detail booking →</a>
</div>
@endsection
