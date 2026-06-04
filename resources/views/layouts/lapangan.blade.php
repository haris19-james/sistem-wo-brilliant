<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tim Lapangan') - Brilliant WO</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('partials.brand-tailwind', ['extraColors' => [
        'field' => config('brilliant.colors.bottle'),
        'fieldHover' => config('brilliant.colors.bottle_hover'),
        'grayBg' => '#F8FAFC',
        'grayText' => '#64748B',
    ]])
    <link rel="stylesheet" href="{{ asset('css/lapangan-panel.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none!important}</style>
    @stack('head')
</head>
<body class="bg-gradient-to-br from-slate-50 via-white to-slate-50 font-sans antialiased text-gray-800 flex h-screen overflow-hidden relative" x-data="{ sidebarOpen: false }">

<div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display:none;"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static">
    <div class="flex items-center justify-center h-20 border-b border-gray-200 px-6 bg-white">
        <a href="{{ route('lapangan.dashboard') }}" class="flex items-center space-x-2">
            <svg class="w-8 h-8 text-bottle" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
            </svg>
            <div class="leading-tight">
                <h1 class="text-sm font-bold text-gray-900">Brilliant</h1>
                <p class="text-[0.65rem] uppercase tracking-widest text-gray-500">Event Organizer</p>
            </div>
        </a>
    </div>

    <nav class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
        @php
            $active = $activeMenu ?? '';
            $link = fn ($key) => $active === $key
                ? 'flex items-center px-4 py-3 lp-sidebar-link--active font-semibold rounded-lg'
                : 'flex items-center px-4 py-3 lp-sidebar-link font-medium rounded-lg transition';
        @endphp
        <a href="{{ route('lapangan.dashboard') }}" class="{{ $link('dashboard') }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('lapangan.pesanan.index') }}" class="{{ $link('pesanan') }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            Pemesanan
        </a>
        <a href="{{ route('lapangan.vendor') }}" class="{{ $link('vendor') }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 20H9m8-4a3 3 0 01-6 0m6 0a3 3 0 00-6 0m6 0H9m6 0a3 3 0 00-6 0"/></svg>
            Vendor
        </a>
        <x-sidebar.jadwal-acara-nav
            panel="lapangan"
            :active-menu="$active"
            :rundown-url="route('lapangan.jadwal')"
            :meeting-url="route('lapangan.jadwal').'#vendor-meetings'"
            :rundown-locked="$jadwalNavRundownLocked ?? true"
            :meeting-locked="$jadwalNavMeetingLocked ?? true"
            :lock-hint="$jadwalNavLockHint ?? null"
            link-active-class="flex items-center px-4 py-3 lp-sidebar-link--active font-semibold rounded-lg"
            link-idle-class="flex items-center px-4 py-3 lp-sidebar-link font-medium rounded-lg transition"
            sub-active-class="flex items-center pl-11 pr-4 py-2.5 text-sm lp-sidebar-link--active font-semibold rounded-lg"
            sub-idle-class="flex items-center pl-11 pr-4 py-2.5 text-sm lp-sidebar-link rounded-lg transition"
            sub-locked-class="flex items-center pl-11 pr-4 py-2.5 text-sm text-gray-400 rounded-lg cursor-not-allowed select-none"
        />
        <a href="{{ route('lapangan.tugas.index') }}" class="{{ $link('tugas') }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
            Tugas
        </a>
        <a href="{{ route('lapangan.chat') }}" class="{{ $link('chat') }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
            Chat / Pesan
        </a>
        <a href="{{ route('lapangan.laporan') }}" class="{{ $link('laporan') }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            Laporan
        </a>
        <a href="{{ route('lapangan.pengaturan') }}" class="{{ $link('pengaturan') }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Pengaturan
        </a>
    </nav>

    <div class="p-4 border-t border-gray-200">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center px-4 py-3 text-gray-600 hover:bg-red-50 hover:text-red-600 font-medium rounded-lg transition text-sm">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
    </div>
</aside>

<div class="flex-1 flex flex-col overflow-hidden min-h-0 relative z-10">
    <header class="bg-white border-b border-gray-200 px-4 lg:px-8 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-3">
            <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-gray-100">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-4 py-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span class="text-sm font-medium text-gray-700">@yield('header-date', 'Tanggal')</span>
                </div>
                <x-notification-bell />
            </div>
            <div class="flex items-center gap-3 pl-6 border-l border-gray-200">
                <div class="text-right">
                    <p class="text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Korlap' }}</p>
                    <p class="text-xs text-gray-500">Koordinator Lapangan</p>
                </div>
                <button class="w-10 h-10 lp-icon-wrap rounded-full flex items-center justify-center font-bold hover:bg-leafSoft transition">
                    {{ substr(auth()->user()->name ?? 'K', 0, 1) }}
                </button>
            </div>
        </div>
    </header>

    <main id="app-main" class="flex-1 overflow-y-auto min-h-0 relative z-10">
        @if(session('success') || session('error'))
        <div class="container mx-auto px-6 pt-4">
            @if(session('success'))
            <div class="mb-4 p-4 bg-leafSoft border border-leaf rounded-xl text-bottle text-sm">{{ session('success') }}</div>
            @endif
            @if(session('error'))
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-sm">{{ session('error') }}</div>
            @endif
        </div>
        @endif
        @yield('content')
    </main>
</div>
<x-urgent-toast />
@include('components.loading-overlay')
@include('components.loading-overlay-premium')
@include('components.floral-decoration')
@include('components.wedding-decoration')

@include('components.page-nav-skeleton')
<script src="{{ asset('js/page-nav.js') }}" defer></script>
@stack('scripts')
</body>
</html>
