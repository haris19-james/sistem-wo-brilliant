<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('brilliant.name').' — '.config('brilliant.tagline'))</title>
    <meta name="description" content="@yield('meta_description', 'Brilliant Event & Wedding Organizer — wujudkan pernikahan impian Anda di Garut dan sekitarnya.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.brand-tailwind', ['fontSerif' => true])
    @stack('head')
    <style>
        [x-cloak] { display: none !important; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="bg-white text-ink font-sans antialiased overflow-x-hidden flex flex-col min-h-screen" x-data="{ mobileOpen: false }">

@php
    $navItems = [
        'home' => ['label' => 'Beranda', 'route' => 'home'],
        'paket' => ['label' => 'Paket', 'route' => 'paket'],
        'vendor' => ['label' => 'Vendor', 'route' => 'vendor'],
        'about' => ['label' => 'Tentang Kami', 'route' => 'about'],
        'blog' => ['label' => 'Blog', 'route' => 'blog'],
        'contact' => ['label' => 'Kontak', 'route' => 'contact'],
    ];
    $activeNav = $activeNav ?? '';
@endphp

<header class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 shadow-sm">
    <nav class="container mx-auto px-4 sm:px-6 py-3 md:py-4 flex justify-between items-center gap-4">
        <x-public-logo />

        <div class="hidden lg:flex items-center gap-6 text-sm font-medium">
            @foreach($navItems as $key => $item)
            <a href="{{ route($item['route']) }}"
               class="py-2 transition {{ $activeNav === $key ? 'text-bottle border-b-2 border-bottle font-semibold' : 'text-gray-800 hover:text-bottle' }}">
                {{ $item['label'] }}
            </a>
            @endforeach
        </div>

        <div class="hidden lg:flex items-center gap-3 text-sm font-medium shrink-0">
            @auth
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="text-bottle font-semibold hover:underline">Admin</a>
                @elseif(auth()->user()->role === 'lapangan')
                <a href="{{ route('lapangan.dashboard') }}" class="text-teal-700 font-semibold hover:underline">Tim Lapangan</a>
                @else
                <a href="{{ route('client.dashboard') }}" class="text-bottle font-semibold hover:underline">Dashboard</a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-bottle">Logout</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-gray-800 hover:text-bottle px-2">Masuk</a>
                <a href="{{ route('register') }}" data-no-loading class="bg-bottle text-white font-semibold py-2 px-5 rounded-lg hover:bg-bottleHover transition">Daftar</a>
            @endauth
        </div>

        <button type="button" @click="mobileOpen = !mobileOpen" class="lg:hidden p-2 text-gray-800 hover:text-bottle rounded-lg" aria-label="Menu">
            <svg x-show="!mobileOpen" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="mobileOpen" x-cloak class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </nav>

    <div x-show="mobileOpen" x-cloak x-transition class="lg:hidden border-t border-gray-100 bg-white px-4 py-4 space-y-1">
        @foreach($navItems as $key => $item)
        <a href="{{ route($item['route']) }}" @click="mobileOpen = false"
           class="block py-3 px-3 rounded-xl text-sm font-medium {{ $activeNav === $key ? 'bg-leafSoft text-bottle font-semibold' : 'text-gray-800 hover:bg-gray-50' }}">
            {{ $item['label'] }}
        </a>
        @endforeach
        <div class="pt-3 border-t border-gray-100 flex flex-col gap-2">
            @auth
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="py-2 text-center text-bottle font-semibold">Panel Admin</a>
            @elseif(auth()->user()->role === 'lapangan')
            <a href="{{ route('lapangan.dashboard') }}" class="py-2 text-center text-teal-700 font-semibold">Tim Lapangan</a>
            @else
            <a href="{{ route('client.dashboard') }}" class="py-2 text-center bg-bottle text-white font-semibold rounded-xl">Dashboard Saya</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full py-2 text-gray-600 text-sm">Logout</button>
            </form>
            @else
            <a href="{{ route('login') }}" class="block py-3 text-center border border-gray-200 rounded-xl font-semibold">Masuk</a>
            <a href="{{ route('register') }}" data-no-loading class="block py-3 text-center bg-bottle text-white font-semibold rounded-xl">Daftar</a>
            @endauth
        </div>
    </div>
</header>

@if(session('success'))
<div class="bg-green-50 border-b border-green-200 text-green-800 text-sm text-center py-3 px-4">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="bg-red-50 border-b border-red-200 text-red-800 text-sm text-center py-3 px-4">{{ session('error') }}</div>
@endif

<main class="flex-1">
    @yield('content')
</main>

<footer class="bg-ink text-gray-300 mt-auto">
    <div class="container mx-auto px-6 py-12 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10">
        <div>
            <x-public-logo size="sm" />
            <p class="text-sm mt-4 leading-relaxed text-gray-400">{{ config('brilliant.motto') }}</p>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Navigasi</h4>
            <ul class="space-y-2 text-sm">
                @foreach($navItems as $item)
                <li><a href="{{ route($item['route']) }}" class="hover:text-white transition">{{ $item['label'] }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Kontak</h4>
            <ul class="space-y-2 text-sm text-gray-400">
                <li><a href="tel:{{ config('brilliant.contact.phone_digits') }}" class="hover:text-white">{{ config('brilliant.contact.phone') }}</a></li>
                <li><a href="mailto:{{ config('brilliant.contact.email') }}" class="hover:text-white">{{ config('brilliant.contact.email') }}</a></li>
                <li>{{ config('brilliant.contact.address') }}</li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-4">Ikuti Kami</h4>
            <div class="flex gap-3">
                <a href="{{ config('brilliant.social.instagram') }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bottle transition" aria-label="Instagram">IG</a>
                <a href="{{ config('brilliant.social.facebook') }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bottle transition" aria-label="Facebook">FB</a>
                <a href="{{ \App\Support\Branding::whatsappUrl() }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-bottle transition" aria-label="WhatsApp">WA</a>
            </div>
            <a href="{{ route('contact') }}" class="inline-block mt-6 text-sm font-semibold text-white border border-white/30 px-5 py-2 rounded-full hover:bg-white hover:text-gray-900 transition">Konsultasi Gratis</a>
        </div>
    </div>
    <div class="border-t border-gray-800 text-center text-xs text-gray-500 py-4">
        &copy; {{ date('Y') }} {{ config('brilliant.name') }} {{ config('brilliant.tagline') }}. All rights reserved.
    </div>
</footer>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
<script>
window.BrilliantImageConfig = {
    placeholderPackage: @json(\App\Support\ImageHelper::placeholderUrl('package')),
    placeholderVendor: @json(\App\Support\ImageHelper::placeholderUrl('vendor')),
};
</script>
<script src="{{ asset('js/image-fallback.js') }}?v=1" defer></script>
@include('components.wedding-decoration')
@stack('scripts')
</body>
</html>
