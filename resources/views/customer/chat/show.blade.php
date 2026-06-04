@extends('layouts.customer')

@section('title', 'Chat Booking')

@section('content')
<div class="mb-4">
    <a href="{{ route('client.chat') }}" class="text-sm text-bottle font-semibold hover:underline">← Daftar chat</a>
    <h1 class="text-xl font-bold mt-2">{{ $pesanan->nama_pasangan }}</h1>
    <p class="text-sm text-gray-500">{{ $pesanan->nomor_pesanan }}</p>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col" style="min-height: 400px;">
    <div class="flex-1 p-4 space-y-3 overflow-y-auto max-h-96">
        @forelse($pesanan->chatMessages as $msg)
        <div class="flex {{ $msg->dari_admin ? 'justify-start' : 'justify-end' }}">
            <div class="max-w-[80%] px-4 py-2 rounded-2xl text-sm {{ $msg->dari_admin ? 'bg-gray-100 text-gray-800 rounded-bl-none' : 'bg-bottle text-white rounded-br-none' }}">
                <p class="text-xs font-semibold mb-1 opacity-75">{{ $msg->dari_admin ? 'Admin' : 'Anda' }}</p>
                <p class="whitespace-pre-wrap">{{ $msg->pesan }}</p>
                <p class="text-[10px] mt-1 opacity-60">{{ $msg->created_at->format('d M H:i') }}</p>
            </div>
        </div>
        @empty
        <p class="text-center text-gray-400 text-sm py-8">Belum ada pesan. Sapa admin untuk mulai diskusi.</p>
        @endforelse
    </div>
    <form method="POST" action="{{ route('client.chat.store', $pesanan) }}" class="p-4 border-t border-gray-100 flex gap-2">
        @csrf
        <input type="text" name="pesan" required placeholder="Tulis pesan..." class="flex-1 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:border-bottle focus:outline-none">
        <button type="submit" class="px-5 py-2 bg-bottle text-white font-semibold rounded-xl hover:bg-bottleHover text-sm">Kirim</button>
    </form>
</div>
@endsection
