@extends('layouts.public')

@section('title', 'Kontak Kami — Brilliant WO')

@section('content')
<section class="container mx-auto px-6 py-12 lg:py-16 flex flex-col lg:flex-row items-center gap-10">
    <div class="w-full lg:w-1/2 text-center lg:text-left">
        <h1 class="text-4xl md:text-5xl font-serif font-bold text-gray-900 mb-4">Kontak <span class="text-bottle">Kami</span></h1>
        <p class="text-lg text-gray-600 leading-relaxed">Kami siap membantu mewujudkan momen spesial Anda. Hubungi kami atau kirim pesan melalui formulir.</p>
    </div>
    <div class="w-full lg:w-1/2">
        <img src="{{ config('brilliant.hero_image') }}" alt="Kontak Brilliant" class="w-full h-64 lg:h-80 object-cover rounded-2xl shadow-lg">
    </div>
</section>

<div class="bg-gray-50 py-14">
    <section class="container mx-auto px-6 mb-14">
        <h2 class="text-2xl font-serif font-bold text-gray-900 mb-8">Hubungi Kami</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="tel:{{ config('brilliant.contact.phone_digits') }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md hover:border-bottle/30 transition">
                <div class="w-14 h-14 bg-leafSoft rounded-full flex items-center justify-center text-bottle mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">Telepon</h3>
                <p class="text-gray-600 text-sm">{{ config('brilliant.contact.phone') }}</p>
            </a>
            <a href="{{ \App\Support\Branding::whatsappUrl() }}" target="_blank" rel="noopener" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md hover:border-bottle/30 transition">
                <div class="w-14 h-14 bg-leafSoft rounded-full flex items-center justify-center text-bottle mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">WhatsApp</h3>
                <p class="text-gray-600 text-sm">Chat langsung</p>
            </a>
            <a href="mailto:{{ config('brilliant.contact.email') }}" class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center hover:shadow-md hover:border-bottle/30 transition">
                <div class="w-14 h-14 bg-leafSoft rounded-full flex items-center justify-center text-bottle mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                <p class="text-gray-600 text-sm break-all">{{ config('brilliant.contact.email') }}</p>
            </a>
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 text-center">
                <div class="w-14 h-14 bg-leafSoft rounded-full flex items-center justify-center text-bottle mx-auto mb-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h3 class="font-semibold text-gray-900 mb-1">Alamat & Jam</h3>
                <p class="text-gray-600 text-sm">{{ config('brilliant.contact.address') }}</p>
                <p class="text-gray-500 text-xs mt-2">{{ config('brilliant.contact.hours') }}</p>
            </div>
        </div>
    </section>

    <section class="container mx-auto px-6 grid lg:grid-cols-2 gap-10">
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 lg:p-8">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Kirim Pesan</h2>
            @if($errors->any())
            <div class="mb-4 p-3 bg-red-50 text-red-700 text-sm rounded-lg">
                <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif
            <form method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama *</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bottle focus:border-bottle">
                </div>
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bottle focus:border-bottle">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                        <input type="text" name="telepon" value="{{ old('telepon') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bottle focus:border-bottle">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subjek *</label>
                    <input type="text" name="subjek" value="{{ old('subjek') }}" required placeholder="Contoh: Konsultasi paket Gold" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bottle focus:border-bottle">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pesan *</label>
                    <textarea name="pesan" rows="5" required minlength="10" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-bottle focus:border-bottle">{{ old('pesan') }}</textarea>
                </div>
                <button type="submit" class="w-full bg-bottle text-white font-semibold py-3 rounded-xl hover:bg-bottleHover transition">Kirim via WhatsApp</button>
                <p class="text-xs text-gray-500 text-center">Setelah submit, Anda akan diarahkan ke WhatsApp dengan pesan terisi.</p>
            </form>
        </div>

        <div>
            <h2 class="text-xl font-bold text-gray-900 mb-4">Temukan Kami</h2>
            <div class="rounded-2xl overflow-hidden shadow-lg border border-gray-100 h-[320px] lg:h-full min-h-[320px]">
                <iframe src="{{ config('brilliant.contact.maps_embed') }}" width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Peta lokasi Brilliant"></iframe>
            </div>
            <a href="{{ config('brilliant.contact.maps_url') }}" target="_blank" rel="noopener" class="inline-flex items-center mt-4 text-bottle font-semibold text-sm hover:underline">
                Buka di Google Maps
                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        </div>
    </section>
</div>
@endsection
