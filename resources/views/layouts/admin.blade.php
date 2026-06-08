<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') - Brilliant WO</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.brand-tailwind', ['extraColors' => ['grayBg' => '#F8FAFC', 'grayText' => '#64748B']])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    @stack('head')
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden" data-brilliant-panel="admin" x-data="{ sidebarOpen: false }">

    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display:none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0">
        <div class="flex items-center justify-center h-20 border-b border-gray-50 px-6">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-2">
            @if(\App\Support\Branding::hasLogo())
                <img src="{{ \App\Support\Branding::logoUrl() }}" alt="Brilliant" class="h-10 w-auto max-w-[200px] object-contain">
            @else
                <span class="text-xl font-bold text-bottle">Brilliant WO</span>
            @endif
                <div class="leading-tight">
                    <h1 class="text-lg font-bold text-gray-900">Brilliant</h1>
                    <p class="text-[0.45rem] text-gray-500 uppercase tracking-widest">Admin Panel</p>
                </div>
            </a>
        </div>

        <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            @php
                $active = $activeMenu ?? '';
                $link = fn ($key, $route) => $active === $key
                    ? 'flex items-center px-4 py-3 bg-bottle/10 text-bottle font-semibold rounded-xl ring-1 ring-bottle/15'
                    : 'flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition';
            @endphp
            <a href="{{ route('admin.dashboard') }}" class="{{ $link('dashboard', 'admin.dashboard') }}" data-loading-message="Memuat ringkasan dashboard...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                Dashboard
            </a>
            <a href="{{ route('admin.booking') }}" class="{{ $link('booking', 'admin.booking') }}" data-loading-message="Memuat data booking & pesanan...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Booking
            </a>
            <a href="{{ route('admin.paket.index') }}" class="{{ $link('paket', 'admin.paket.index') }}" data-loading-message="Memuat daftar paket pernikahan...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                Paket
            </a>
            <a href="{{ route('admin.vendor.index') }}" class="{{ $link('vendor', 'admin.vendor.index') }}" data-loading-message="Memuat direktori vendor...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Vendor
            </a>
            <x-sidebar.jadwal-acara-nav
                panel="admin"
                :active-menu="$active"
                :rundown-url="route('admin.jadwal-acara.rundown')"
                :meeting-url="route('admin.jadwal-acara.meeting-vendor')"
                :rundown-locked="false"
                :meeting-locked="false"
            />
            <a href="{{ route('admin.vendor-keuangan.index') }}" class="{{ $link('vendor-keuangan', 'admin.vendor-keuangan.index') }}" data-no-loading data-loading-message="Memuat keuangan vendor...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Keuangan Vendor
            </a>
            <a href="{{ route('admin.laporan-keuangan') }}" class="{{ $link('laporan-keuangan', 'admin.laporan-keuangan') }}" data-loading-message="Memuat laporan keuangan...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan Keuangan
            </a>
            <a href="{{ route('admin.chat') }}" class="{{ $link('chat', 'admin.chat') }}" data-loading-message="Membuka pusat chat...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                Chat
            </a>
            <a href="{{ route('admin.pengaturan') }}" class="{{ $link('pengaturan', 'admin.pengaturan') }}" data-loading-message="Memuat pengaturan sistem...">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                Pengaturan
            </a>
            <a href="{{ route('home') }}" target="_blank" data-no-loading class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                Lihat Website
            </a>
        </nav>

        <div class="p-4 border-t border-gray-50">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center px-4 py-3 text-grayText hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <x-dashboard-header :title="$pageTitle ?? 'Admin Dashboard'">
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Admin Avatar" class="w-9 h-9 rounded-full object-cover border border-gray-200">
            <div class="hidden md:block text-right min-w-0">
                <p class="text-sm font-semibold text-gray-900 leading-tight">{{ auth()->user()->name ?? 'Admin' }}</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
        </x-dashboard-header>

        <main id="app-main" class="flex-1 overflow-y-auto p-6 lg:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 text-green-800 border border-green-200 rounded-xl text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl text-sm">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-6 p-4 bg-red-50 text-red-800 border border-red-200 rounded-xl text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    <x-urgent-toast />
    @include('components.loading-overlay')
    @include('components.loading-overlay-premium')
    @include('components.wedding-decoration')

    @include('components.page-nav-skeleton')
    <script>
    window.BrilliantImageConfig = {
        placeholderPackage: @json(\App\Support\ImageHelper::placeholderUrl('package')),
        placeholderVendor: @json(\App\Support\ImageHelper::placeholderUrl('vendor')),
    };
    </script>
    <script src="{{ asset('js/image-fallback.js') }}?v=1" defer></script>
    <script src="{{ asset('js/brilliant-nav-loading.js') }}?v=1" defer></script>
    <script src="{{ asset('js/page-nav.js') }}?v=2" defer></script>
    @vite(['resources/js/app.jsx'])
    @stack('scripts')
</body>
</html>
