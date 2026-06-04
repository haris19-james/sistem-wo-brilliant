@extends('layouts.public')

@section('title', 'Mitra Vendor — Brilliant WO')

@section('content')
<section class="bg-leafSoft/50 py-12 border-b border-green-100">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Mitra Vendor</h1>
        <p class="text-gray-600 mt-2 max-w-2xl mx-auto">
            Profil vendor mitra Brilliant WO — <strong>koordinasi acara terpusat melalui tim WO</strong>, bukan chat langsung ke vendor.
        </p>
    </div>
</section>

<section class="container mx-auto px-6 py-8">
    <div class="flex flex-wrap gap-2 justify-center">
        <a href="{{ route('vendor') }}"
           class="px-4 py-2 rounded-full text-sm font-semibold border transition {{ $activeCategory === 'semua' ? 'bg-bottle text-white border-bottle' : 'bg-white text-gray-700 border-gray-200 hover:border-bottle hover:text-bottle' }}">
            Semua ({{ $totalAktif }})
        </a>
        @foreach($categories as $cat)
        @if($cat['count'] > 0 || in_array($cat['label'], config('brilliant.vendor_categories', []), true))
        <a href="{{ route('vendor', ['kategori' => $cat['slug']]) }}"
           class="px-4 py-2 rounded-full text-sm font-semibold border transition {{ $activeCategory === $cat['slug'] ? 'bg-bottle text-white border-bottle' : 'bg-white text-gray-700 border-gray-200 hover:border-bottle hover:text-bottle' }}">
            {{ $cat['label'] }}@if($cat['count'] > 0) ({{ $cat['count'] }})@endif
        </a>
        @endif
        @endforeach
    </div>
    @if($activeCategoryLabel)
    <p class="text-center text-sm text-gray-500 mt-4">Menampilkan kategori: <strong class="text-bottle">{{ $activeCategoryLabel }}</strong></p>
    @endif
</section>

<main class="container mx-auto px-6 pb-12">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($vendors as $vendor)
        <a href="{{ route('vendor.detail', $vendor) }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:-translate-y-1 hover:shadow-xl transition block group">
            <x-media-image
                :src="$vendor->gambar"
                :fallback="$vendor->gambar_url"
                :alt="$vendor->nama_vendor"
                type="vendor"
                wrapper-class="w-full h-48 bg-gradient-to-br from-leafSoft to-green-100"
                img-class="w-full h-full object-cover group-hover:scale-105 transition duration-300"
            />
            <div class="p-5">
                <span class="inline-block text-xs font-semibold text-bottle bg-leafSoft px-2 py-0.5 rounded-full mb-2">{{ $vendor->kategori }}</span>
                <h2 class="font-bold text-gray-900 mb-1 group-hover:text-bottle transition">{{ $vendor->nama_vendor }}</h2>
                @if($vendor->lokasi)
                <p class="text-sm text-gray-500 mb-2">{{ $vendor->lokasi }}</p>
                @endif
                @if($vendor->harga_info)
                <p class="text-sm font-bold text-bottle mb-2">{{ $vendor->harga_info }}</p>
                @endif
                <span class="text-xs font-semibold text-gray-500 group-hover:text-bottle">Lihat profil vendor →</span>
            </div>
        </a>
        @empty
        <div class="col-span-full text-center py-16 bg-gray-50 rounded-2xl">
            <p class="text-gray-500 mb-4">Belum ada vendor aktif untuk filter ini.</p>
            <a href="{{ route('vendor') }}" class="text-bottle font-semibold hover:underline">Lihat semua vendor</a>
        </div>
        @endforelse
    </div>
</main>

@include('pages.partials.cta-consult')
@endsection
