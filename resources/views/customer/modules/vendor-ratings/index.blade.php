@extends('layouts.customer')

@section('title', 'Vendor Ratings')
@section('page-title', 'Vendor Ratings')
@section('page-subtitle', 'Berikan ulasan untuk vendor dan acara Anda')

@section('content')
<div class="max-w-4xl mx-auto opacity-0 translate-y-2 transition-all duration-500 ease-out" x-data="{ init() { this.$el.classList.remove('opacity-0', 'translate-y-2'); this.$el.classList.add('opacity-100', 'translate-y-0'); } }">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 text-green-800 border border-green-200 rounded-xl text-sm">{{ session('success') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="font-bold text-lg text-gray-900">Pilih Pesanan untuk Diberi Ulasan</h2>
            <p class="text-sm text-gray-500 mt-1">Acara Anda yang sudah selesai dan membutuhkan ulasan.</p>
        </div>
        <div class="divide-y divide-gray-100">
            @foreach($bookings as $pesanan)
            <div class="p-6 hover:bg-gray-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-gray-900">Pernikahan {{ $pesanan->nama_pasangan }}</h3>
                    <p class="text-sm text-gray-500 mt-1">Booking #{{ $pesanan->nomor_pesanan }} &middot; {{ $pesanan->tanggal_formatted }}</p>
                </div>
                <a href="{{ route('client.vendor-ratings.show', $pesanan->id) }}" class="inline-block px-4 py-2 bg-bottle text-white font-semibold text-sm rounded-lg hover:bg-bottleHover text-center transition">Beri Ulasan</a>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
