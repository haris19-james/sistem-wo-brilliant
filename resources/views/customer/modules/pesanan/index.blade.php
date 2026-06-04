@extends('layouts.customer')

@section('title', 'Pesanan Saya')
@section('page-title', 'Pesanan Saya')
@section('page-subtitle', 'Semua booking pernikahan Anda')

@section('content')
@if(session('success'))
<div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">{{ session('error') }}</div>
@endif
<div class="flex justify-between items-center mb-6">
    <p class="text-sm text-gray-600">{{ $daftarPesanan->count() }} pesanan</p>
    <a href="{{ route('client.booking.create') }}" class="px-5 py-2.5 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover">+ Booking Baru</a>
</div>

<div class="space-y-4">
    @forelse($daftarPesanan as $p)
    <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex flex-col md:flex-row gap-4">
        @if($p->paket?->image_url)
        <img src="{{ $p->paket->image_url }}" class="w-full md:w-36 h-28 object-cover rounded-xl shrink-0" alt="">
        @endif
        <div class="flex-1">
            <p class="text-xs font-semibold text-bottle">{{ $p->nomor_pesanan }}</p>
            <h3 class="text-lg font-bold text-gray-900">{{ $p->nama_pasangan }}</h3>
            <p class="text-sm text-gray-600">{{ $p->paket?->nama_paket }} · {{ $p->tanggal_formatted }} · {{ substr($p->jam_acara, 0, 5) }}</p>
            <p class="text-sm text-gray-500">{{ $p->lokasi }}</p>
            <span class="inline-block mt-2 px-2 py-1 rounded-full text-xs font-semibold {{ $p->status_badge_class }}">{{ $p->status_label }}</span>
        </div>
        <div class="flex md:flex-col gap-2 shrink-0 justify-center">
            <a href="{{ route('client.pesanan_detail', $p->id) }}" class="px-4 py-2 text-sm font-semibold bg-bottle text-white rounded-lg text-center hover:bg-bottleHover">Detail</a>
            @if($p->status !== 'Dibatalkan')
            <a href="{{ route('client.chat.show', $p->id) }}" class="px-4 py-2 text-sm font-semibold border border-bottle text-bottle rounded-lg text-center hover:bg-leafSoft">Chat</a>
            @endif
        </div>
    </div>
    @empty
    <div class="bg-white rounded-2xl p-12 text-center border border-gray-100">
        <p class="text-gray-500 mb-4">Belum ada pesanan.</p>
        <a href="{{ route('client.booking.create') }}" class="text-bottle font-semibold hover:underline">Buat booking pertama Anda</a>
    </div>
    @endforelse
</div>
@endsection
