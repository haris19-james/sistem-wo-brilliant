@extends('layouts.public')

@section('title', 'Paket Pernikahan — Brilliant WO')

@section('content')
<section class="bg-leafSoft/50 py-12 border-b border-green-100">
    <div class="container mx-auto px-6 text-center">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900">Paket Pernikahan</h1>
        <p class="text-gray-600 mt-2 max-w-xl mx-auto">Pilih paket yang sesuai dengan impian dan budget Anda</p>
    </div>
</section>

<main class="container mx-auto px-6 py-12">
    @if($paketKustom)
    <article class="mb-12 bg-gradient-to-br from-bottle via-bottleBright to-lime rounded-2xl p-8 md:p-10 text-white shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="max-w-2xl">
                <span class="inline-block text-xs font-bold uppercase tracking-wider bg-white/20 px-3 py-1 rounded-full mb-3">Fleksibel</span>
                <h2 class="text-2xl md:text-3xl font-bold mb-2">{{ $paketKustom->nama_paket }}</h2>
                <p class="text-white/90 text-sm md:text-base">{{ $paketKustom->deskripsi }}</p>
                <p class="text-white/80 text-sm mt-3">Masukkan budget → lihat perkiraan paket & layanan (mis. budget Rp 30 juta ≈ Gold Package).</p>
            </div>
            <div class="shrink-0 flex flex-col gap-2 w-full md:w-auto">
                @auth
                    @if(auth()->user()->role === 'client')
                    <a href="{{ route('client.booking.create', ['paket_id' => $paketKustom->id]) }}" class="block text-center bg-white text-bottle font-bold py-3 px-8 rounded-xl hover:bg-leafSoft">Buat Paket Kustom</a>
                    @endif
                @else
                <a href="{{ route('register') }}" class="block text-center bg-white text-bottle font-bold py-3 px-8 rounded-xl hover:bg-leafSoft">Daftar & Paket Kustom</a>
                @endauth
                <a href="{{ route('contact') }}" class="block text-center border border-white/50 text-white font-semibold py-2.5 px-8 rounded-xl hover:bg-white/10 text-sm">Konsultasi dulu</a>
            </div>
        </div>

        <div class="mt-8 pt-8 border-t border-white/20">
            @include('pages.partials.budget-calculator', [
                'paketStandarJson' => $paketStandarJson,
                'minBudget' => $minBudget,
                'variant' => 'dark',
            ])
        </div>
    </article>
    @endif

    <h2 class="text-xl font-bold text-gray-900 mb-6">Paket Standar</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($pakets as $paket)
        <article id="paket-{{ $paket->id }}" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden flex flex-col scroll-mt-24 hover:shadow-xl transition">
            <x-media-image
                :src="$paket->gambar"
                :fallback="$paket->gambar_url"
                :alt="$paket->nama_paket"
                type="package"
                wrapper-class="w-full h-52 bg-leafSoft"
                img-class="w-full h-full object-cover"
            />
            <div class="p-6 flex flex-col flex-1">
                <h2 class="text-xl font-bold text-bottle mb-2">{{ $paket->nama_paket }}</h2>
                <p class="text-2xl font-bold text-gray-900 mb-3">Rp {{ number_format($paket->harga, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-600 mb-4 flex-1">{{ $paket->deskripsi }}</p>
                @if($paket->layanan_termasuk)
                <ul class="text-sm text-gray-600 space-y-1.5 mb-6">
                    @foreach($paket->layanan_termasuk as $l)
                    <li class="flex items-start gap-2"><span class="text-bottle mt-1">✓</span>{{ $l }}</li>
                    @endforeach
                </ul>
                @endif
                <div class="flex flex-col gap-2 mt-auto">
                    @auth
                        @if(auth()->user()->role === 'client')
                        <a href="{{ route('client.booking.create', ['paket_id' => $paket->id]) }}" class="block text-center bg-bottle text-white font-semibold py-3 rounded-xl hover:bg-bottleHover">Booking Paket Ini</a>
                        @endif
                    @else
                    <a href="{{ route('register') }}" class="block text-center bg-bottle text-white font-semibold py-3 rounded-xl hover:bg-bottleHover">Daftar untuk Booking</a>
                    <a href="{{ route('login') }}" class="block text-center text-sm text-bottle font-semibold hover:underline">Sudah punya akun? Masuk</a>
                    @endauth
                    <a href="{{ \App\Support\Branding::whatsappUrl('Halo, saya tertarik paket '.$paket->nama_paket) }}" target="_blank" rel="noopener"
                       class="block text-center border border-bottle text-bottle font-semibold py-2.5 rounded-xl hover:bg-leafSoft text-sm">Tanya via WhatsApp</a>
                </div>
            </div>
        </article>
        @empty
        <div class="col-span-full text-center py-16 bg-gray-50 rounded-2xl">
            <p class="text-gray-500 mb-4">Paket sedang disiapkan.</p>
            <a href="{{ route('contact') }}" class="text-bottle font-semibold hover:underline">Hubungi kami untuk konsultasi</a>
        </div>
        @endforelse
    </div>
</main>

@include('pages.partials.cta-consult')
@endsection
