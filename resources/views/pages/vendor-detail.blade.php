@extends('layouts.public')

@section('title', $vendor->nama_vendor.' — Vendor Brilliant WO')

@section('content')
<section class="container mx-auto px-6 pt-8 pb-10">
    <nav class="text-sm text-gray-500 mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-bottle">Beranda</a>
        <span>›</span>
        <a href="{{ route('vendor') }}" class="hover:text-bottle">Vendor</a>
        <span>›</span>
        <a href="{{ route('vendor', ['kategori' => \App\Support\VendorCategories::slug($vendor->kategori)]) }}" class="hover:text-bottle">{{ $vendor->kategori }}</a>
        <span>›</span>
        <span class="text-gray-900 font-medium">{{ $vendor->nama_vendor }}</span>
    </nav>

    <div class="bg-leafSoft border border-green-100 rounded-xl px-4 py-3 text-sm text-gray-700 mb-8">
        <strong>Catatan:</strong> Halaman ini hanya profil mitra vendor. Semua komunikasi & penawaran harga diurus terpusat oleh <strong>Brilliant WO</strong> — tidak ada chat langsung ke vendor.
    </div>

    <div class="flex flex-col lg:flex-row gap-10">
        <div class="w-full lg:w-5/12">
            <span class="inline-block text-sm font-semibold text-bottle bg-leafSoft px-3 py-1 rounded-full mb-3">{{ $vendor->kategori }}</span>
            <h1 class="text-3xl lg:text-4xl font-serif font-bold text-gray-900 mb-2">{{ $vendor->nama_vendor }}</h1>
            @if($vendor->lokasi)
            <p class="text-gray-600 mb-4 flex items-center gap-1">
                <svg class="w-4 h-4 text-bottle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/></svg>
                {{ $vendor->lokasi }}
            </p>
            @endif
            @if($vendor->harga_info)
            <p class="text-xl font-bold text-bottle mb-6">{{ $vendor->harga_info }}</p>
            @endif

            <div class="prose prose-sm text-gray-600 mb-8">
                <p>
                    {{ $vendor->nama_vendor }} adalah mitra {{ $vendor->kategori }} yang bekerja sama dengan Brilliant Event & Wedding Organizer.
                    Jika Anda tertarik layanan sejenis, ajukan booking atau konsultasi — tim kami yang mengoordinasikan vendor terpilih untuk acara Anda.
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                @auth
                    @if(auth()->user()->role === 'client')
                    <a href="{{ route('client.booking.create') }}" class="inline-flex items-center justify-center bg-bottle text-white font-semibold py-3 px-8 rounded-xl hover:bg-bottleHover">
                        Ajukan Booking via WO
                    </a>
                    <a href="{{ route('client.chat') }}" class="inline-flex items-center justify-center border border-bottle text-bottle font-semibold py-3 px-8 rounded-xl hover:bg-leafSoft">
                        Chat Admin Brilliant
                    </a>
                    @endif
                @else
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-bottle text-white font-semibold py-3 px-8 rounded-xl hover:bg-bottleHover">Daftar & Booking</a>
                <a href="{{ route('contact') }}" class="inline-flex items-center justify-center border border-gray-300 text-gray-700 font-semibold py-3 px-8 rounded-xl hover:border-bottle hover:text-bottle">Konsultasi</a>
                @endauth
            </div>
        </div>
        <div class="w-full lg:w-7/12">
            <x-media-image
                :src="$vendor->gambar"
                :fallback="$vendor->gambar_url"
                :alt="$vendor->nama_vendor"
                type="vendor"
                wrapper-class="w-full h-64 md:h-96 rounded-3xl shadow-lg bg-leafSoft"
                img-class="w-full h-full object-cover"
            />
        </div>
    </div>
</section>

@if($related->isNotEmpty())
<section class="bg-gray-50 py-12">
    <div class="container mx-auto px-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Vendor {{ $vendor->kategori }} Lainnya</h2>
        <a href="{{ route('vendor', ['kategori' => \App\Support\VendorCategories::slug($vendor->kategori)]) }}" class="text-sm text-bottle font-semibold hover:underline mb-6 inline-block">Lihat semua {{ $vendor->kategori }} →</a>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($related as $v)
            <a href="{{ route('vendor.detail', $v) }}" class="bg-white rounded-xl border border-gray-100 overflow-hidden hover:shadow-md transition block">
                <x-media-image
                    :src="$v->gambar"
                    :fallback="$v->gambar_url"
                    :alt="$v->nama_vendor"
                    type="vendor"
                    wrapper-class="w-full h-32 bg-leafSoft"
                    img-class="w-full h-full object-cover"
                />
                <div class="p-4">
                    <h3 class="font-bold text-sm hover:text-bottle">{{ $v->nama_vendor }}</h3>
                    <p class="text-xs text-gray-500">{{ $v->kategori }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@include('pages.partials.cta-consult')
@endsection
