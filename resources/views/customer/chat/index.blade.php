@extends('layouts.customer')

@section('title', 'Chat')

@section('content')
<h1 class="text-2xl font-bold text-gray-900 mb-6">Chat dengan Admin</h1>

<div class="space-y-3">
    @forelse($pesanans as $p)
    <a href="{{ route('client.chat.show', $p) }}" class="block bg-white rounded-xl border border-gray-100 p-4 hover:border-bottle/40 shadow-sm">
        <div class="flex justify-between items-start">
            <div>
                <p class="font-semibold text-gray-900">{{ $p->nama_pasangan }}</p>
                <p class="text-sm text-gray-500">{{ $p->nomor_pesanan }} — {{ $p->paket?->nama_paket }}</p>
            </div>
            @if($p->chat_messages_count > 0)
            <span class="bg-bottle text-white text-xs px-2 py-1 rounded-full">{{ $p->chat_messages_count }} pesan</span>
            @endif
        </div>
    </a>
    @empty
    <div class="bg-white rounded-xl p-8 text-center text-gray-500 border border-gray-100">
        <p class="mb-4">Belum ada pesanan untuk chat.</p>
        <a href="{{ route('client.booking.create') }}" class="text-bottle font-semibold hover:underline">Buat booking terlebih dahulu</a>
    </div>
    @endforelse
</div>
@endsection
