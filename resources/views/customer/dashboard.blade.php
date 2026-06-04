<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Halaman dashboard utama untuk Client Brilliant Event & Wedding Organizer.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400;1,500&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bottle: '#00A32A',       /* Hijau Botol Identitas Utama */
                        bottleHover: '#008F24',  /* Hover Hijau Botol */
                        leafSoft: '#EDFCF0',     /* Latar Belakang Hijau Muda / Aktif Menu */
                        grayBg: '#F8FAFC',       /* Latar Belakang Dashboard */
                        grayText: '#64748B',     /* Warna Teks Abu-abu */
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js"></script>

    <style>
        /* Progress Circle SVG Animation */
        .progress-ring__circle {
            transition: stroke-dashoffset 1s ease-in-out;
            transform: rotate(-90deg);
            transform-origin: 50% 50%;
        }
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #E2E8F0;
            border-radius: 4px;
        }
        .card-hover {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, profileDropdown: false }">

    <!-- ================================================ -->
    <!-- 1. SIDEBAR NAVIGASI (Kiri)                        -->
    <!-- ================================================ -->

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen"
         class="fixed inset-0 z-40 bg-black/50 lg:hidden"
         @click="sidebarOpen = false"
         style="display: none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0">

        <!-- Logo -->
        <div class="flex items-center justify-center h-24 border-b border-gray-50 px-6">
            <div class="flex items-center space-x-3">
                <!-- SVG Logo Event Organizer -->
                <svg class="w-9 h-9 text-bottle" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2C6.477 2 2 6.477 2 12c0 2.125.666 4.095 1.791 5.709l-.498.498a1 1 0 001.414 1.414l.498-.498A9.957 9.957 0 0012 22c5.523 2 10-2.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/>
                    <path d="M12 22C6.47715 22 2 17.5228 2 12C2 10.8954 2.1791 9.83226 2.50652 8.83582C3.12535 12.8378 6.5828 15.9372 10.7766 16.0827C10.9234 16.0878 11.0706 16.0905 11.2183 16.0905C11.5173 16.0905 11.8133 16.0818 12.1056 16.065C16.3262 15.823 19.7891 12.4411 20.218 8.16335C20.4851 7.89436 20.733 7.6083 20.9599 7.3065C21.6366 8.74233 22 10.3278 22 12C22 17.5228 17.5228 22 12 22Z" fill="#00A32A"/>
                </svg>
                <div class="leading-tight">
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">Brilliant</h1>
                    <p class="text-[0.45rem] text-gray-500 font-medium tracking-widest uppercase">Event & Wedding Organizer</p>
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="flex-1 overflow-y-auto custom-scrollbar py-6 px-4 space-y-1.5 flex flex-col justify-between">
            
            <div class="space-y-1.5">
                <!-- Dashboard (Aktif) -->
                <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-3.5 bg-leafSoft text-bottle font-semibold rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <!-- Pemesanan Saya -->
                <a href="{{ route('client.pesanan') }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                    Pemesanan Saya
                </a>
                
                <!-- Jadwal Acara -->
                <a href="{{ route('client.jadwal') }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Jadwal Acara
                </a>
                
                <!-- Pembayaran -->
                <a href="{{ route('client.invoice', 1) }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    Pembayaran
                </a>
                
                <!-- Chat Konsultasi -->
                <a href="#" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    Chat Konsultasi
                </a>
                
                <!-- Pengaturan -->
                <a href="#" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Pengaturan
                </a>
            </div>

            <!-- Logout -->
            <div class="pt-6 border-t border-gray-50">
                <a href="#" class="flex items-center px-4 py-3.5 text-grayText hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Logout
                </a>
            </div>
        </div>
    </aside>

    <!-- ================================================ -->
    <!-- 2. MAIN CONTENT AREA                              -->
    <!-- ================================================ -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- HEADER -->
        <x-dashboard-header title="Dashboard Saya">
            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2.5&w=256&h=256&q=80" alt="Customer Avatar" class="w-9 h-9 rounded-full object-cover border border-gray-200">
            <div class="hidden md:block text-right min-w-0">
                <p class="text-sm font-semibold text-gray-900 leading-tight">{{ auth()->user()->name ?? 'User' }}</p>
                <p class="text-xs text-gray-500">Klien</p>
            </div>
        </x-dashboard-header>

        <!-- MAIN LAYOUT BODY -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-8 space-y-6">

            <!-- ============================================ -->
            <!-- 3. METRIK RINGKASAN PROGRES (4 Info Cards)    -->
            <!-- ============================================ -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

                <!-- Card 1: Status Booking -->
                <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-full text-bottle shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Status Booking</p>
                            <h3 class="text-xl font-bold text-gray-900 leading-tight">Paket Gold</h3>
                            <p class="text-xs font-semibold text-bottle flex items-center mt-1.5">
                                <span class="w-2 h-2 bg-bottle rounded-full mr-1.5"></span>
                                Sedang Berlangsung
                            </p>
                        </div>
                    </div>
                    <a href="#" class="text-sm font-semibold text-bottle hover:text-bottleHover flex items-center mt-2 group">
                        Lihat Detail <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Card 2: Total Pembayaran -->
                <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-full text-bottle shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Pembayaran</p>
                            <h3 class="text-xl font-bold text-gray-900 leading-tight">Rp 25.000.000</h3>
                            <p class="text-xs font-medium text-gray-500 mt-1">Dari Rp 35.000.000</p>
                        </div>
                    </div>
                    <a href="#" class="text-sm font-semibold text-bottle hover:text-bottleHover flex items-center mt-2 group">
                        Lihat Detail <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Card 3: Jadwal Terdekat -->
                <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-start space-x-4 mb-4">
                        <div class="bg-green-50 p-3 rounded-full text-bottle shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Jadwal Terdekat</p>
                            <h3 class="text-xl font-bold text-gray-900 leading-tight">25 Mei 2026</h3>
                            <p class="text-xs font-medium text-gray-500 mt-1">Meeting Vendor</p>
                        </div>
                    </div>
                    <a href="#" class="text-sm font-semibold text-bottle hover:text-bottleHover flex items-center mt-2 group">
                        Lihat Jadwal <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                <!-- Card 4: Progress Persiapan -->
                <div class="card-hover bg-white rounded-2xl p-6 border border-gray-100 flex flex-col justify-between">
                    <div class="flex items-center space-x-4 mb-4">
                        <!-- SVG Progress Circle 70% -->
                        <div class="relative w-14 h-14 shrink-0">
                            <svg class="w-14 h-14 transform -rotate-90">
                                <circle class="text-gray-100" stroke-width="4" stroke="currentColor" fill="transparent" r="24" cx="28" cy="28"/>
                                <circle class="text-bottle" stroke-width="4" stroke-dasharray="150" stroke-dashoffset="45" stroke-linecap="round" stroke="currentColor" fill="transparent" r="24" cx="28" cy="28"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-sm font-bold text-gray-900">70%</span>
                            </div>
                        </div>
                        <div>
                            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-1">Progress Persiapan</p>
                            <h3 class="text-xl font-bold text-gray-900 leading-tight">70%</h3>
                            <p class="text-[10px] font-medium text-gray-500 mt-1">Persiapan Pernikahan</p>
                        </div>
                    </div>
                    <a href="#" class="text-sm font-semibold text-bottle hover:text-bottleHover flex items-center mt-2 group">
                        Lihat Progress <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

            </div>

            <!-- ============================================ -->
            <!-- 4. KONTEN TENGAH (Acara Terdekat & Chat)      -->
            <!-- ============================================ -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                
                <!-- Kolom Kiri: Acara Terdekat (Timeline) -->
                <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-100 p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-bold text-gray-900">Acara Terdekat</h3>
                        <a href="#" class="text-sm font-semibold text-bottle hover:text-bottleHover flex items-center group">
                            Lihat Semua Jadwal <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>

                    <div class="relative border-l border-dashed border-gray-300 ml-4 space-y-8">
                        
                        <!-- Item 1 -->
                        <div class="relative pl-8">
                            <span class="absolute -left-3.5 top-0 w-7 h-7 bg-green-50 border-2 border-bottle rounded-full flex items-center justify-center text-bottle shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between -mt-1.5">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base">Meeting Vendor</h4>
                                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Hutan Pinus Cipaniis/Gedung Garut
                                    </p>
                                </div>
                                <div class="mt-3 sm:mt-0 text-left sm:text-right">
                                    <p class="text-sm font-semibold text-gray-900">25 Mei 2026</p>
                                    <p class="text-xs text-gray-500 mt-1">10:00 WIB</p>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-4">
                                    <span class="inline-block px-3 py-1 bg-green-50 text-bottle text-xs font-semibold rounded-full">Akan Datang</span>
                                </div>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div class="relative pl-8">
                            <span class="absolute -left-3.5 top-0 w-7 h-7 bg-green-50 border-2 border-bottle rounded-full flex items-center justify-center text-bottle shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                            </span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between -mt-1.5">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base">Fitting Baju</h4>
                                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Bridal House
                                    </p>
                                </div>
                                <div class="mt-3 sm:mt-0 text-left sm:text-right">
                                    <p class="text-sm font-semibold text-gray-900">12 Juni 2026</p>
                                    <p class="text-xs text-gray-500 mt-1">11:00 WIB</p>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-4">
                                    <span class="inline-block px-3 py-1 bg-green-50 text-bottle text-xs font-semibold rounded-full">Akan Datang</span>
                                </div>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div class="relative pl-8">
                            <span class="absolute -left-3.5 top-0 w-7 h-7 bg-green-50 border-2 border-bottle rounded-full flex items-center justify-center text-bottle shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                            </span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between -mt-1.5">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base">Gladi Bersih</h4>
                                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Gedung Intan Dewata Garut
                                    </p>
                                </div>
                                <div class="mt-3 sm:mt-0 text-left sm:text-right">
                                    <p class="text-sm font-semibold text-gray-900">24 Juni 2026</p>
                                    <p class="text-xs text-gray-500 mt-1">15:00 WIB</p>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-4">
                                    <span class="inline-block px-3 py-1 bg-green-50 text-bottle text-xs font-semibold rounded-full">Akan Datang</span>
                                </div>
                            </div>
                        </div>

                        <!-- Item 4 -->
                        <div class="relative pl-8">
                            <span class="absolute -left-3.5 top-0 w-7 h-7 bg-green-50 border-2 border-bottle rounded-full flex items-center justify-center text-bottle shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between -mt-1.5">
                                <div>
                                    <h4 class="font-bold text-gray-900 text-base">Hari H - Akad & Resepsi</h4>
                                    <p class="text-sm text-gray-500 mt-1 flex items-center">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        Graha Bela Negara Garut
                                    </p>
                                </div>
                                <div class="mt-3 sm:mt-0 text-left sm:text-right">
                                    <p class="text-sm font-semibold text-gray-900">30 Juni 2026</p>
                                    <p class="text-xs text-gray-500 mt-1">08:00 WIB</p>
                                </div>
                                <div class="mt-3 sm:mt-0 sm:ml-4">
                                    <span class="inline-block px-3 py-1 bg-green-50 text-bottle text-xs font-semibold rounded-full">Akan Datang</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Kolom Kanan: Chat Konsultasi (Inbox) -->
                <div class="lg:col-span-5 space-y-6">
                    {{-- Upcoming Schedule Widget --}}
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8">
                        @include('components.client.upcoming-schedule', ['nextEvent' => $nextEvent])
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 flex flex-col">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Chat Konsultasi</h3>
                        <a href="#" class="text-sm font-semibold text-bottle hover:text-bottleHover">Lihat Semua</a>
                    </div>

                    <div class="space-y-4 flex-1">
                        <!-- Chat 1 -->
                        <div class="flex items-start space-x-4 p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer group">
                            <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar" class="w-12 h-12 rounded-full object-cover">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold text-gray-900 truncate">Vina Aprilia</p>
                                    <p class="text-xs text-gray-500 shrink-0">10:24</p>
                                </div>
                                <p class="text-[11px] text-gray-400 mb-1">Wedding Planner</p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 truncate">Baik, terima kasih banyak Marsya 🙏</p>
                                    <span class="ml-2 w-5 h-5 bg-bottle text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0">2</span>
                                </div>
                            </div>
                        </div>

                        <!-- Chat 2 -->
                        <div class="flex items-start space-x-4 p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer group">
                            <img src="https://images.unsplash.com/photo-1438761681033-6461ffad8d80?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar" class="w-12 h-12 rounded-full object-cover">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold text-gray-900 truncate">Dinda Ayu</p>
                                    <p class="text-xs text-gray-500 shrink-0">09:15</p>
                                </div>
                                <p class="text-[11px] text-gray-400 mb-1">Dekorasi</p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 truncate font-medium text-gray-900">Berikut ini adalah referensi dekorasi...</p>
                                    <span class="ml-2 w-5 h-5 bg-bottle text-white text-[10px] font-bold rounded-full flex items-center justify-center shrink-0">1</span>
                                </div>
                            </div>
                        </div>

                        <!-- Chat 3 -->
                        <div class="flex items-start space-x-4 p-3 hover:bg-gray-50 rounded-xl transition cursor-pointer group">
                            <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Avatar" class="w-12 h-12 rounded-full object-cover">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-bold text-gray-900 truncate">Rizky Pratama</p>
                                    <p class="text-xs text-gray-500 shrink-0">Kemarin</p>
                                </div>
                                <p class="text-[11px] text-gray-400 mb-1">Venue</p>
                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-500 truncate">Untuk layout sudah saya kirim ya...</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <button class="w-full py-3.5 bg-leafSoft text-bottle font-bold rounded-xl border border-transparent hover:bg-green-100 hover:border-green-200 transition">
                            Buka Chat
                        </button>
                    </div>
                </div>

            </div>

            <!-- ============================================ -->
            <!-- 5. KONTEN BAWAH (Progress & Pembayaran)       -->
            <!-- ============================================ -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

                <!-- Kolom Kiri: Progress Persiapan Pernikahan (Milestone) -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8">
                    <div class="flex items-center justify-between mb-10">
                        <h3 class="text-lg font-bold text-gray-900">Progress Persiapan Pernikahan</h3>
                        <a href="#" class="text-sm font-semibold text-bottle hover:text-bottleHover">Lihat Detail</a>
                    </div>

                    <!-- Milestone Nodes -->
                    <div class="relative max-w-lg mx-auto mb-10">
                        <!-- Horizontal Connecting Line (Background) -->
                        <div class="absolute top-7 left-8 right-8 h-1 bg-gray-100 -z-10"></div>
                        
                        <!-- Horizontal Connecting Line (Active Progress) -->
                        <div class="absolute top-7 left-8 w-[30%] h-1 bg-bottle -z-10"></div>

                        <div class="flex justify-between items-start">
                            
                            <!-- Step 1: Venue -->
                            <div class="flex flex-col items-center">
                                <div class="relative w-14 h-14 bg-leafSoft text-bottle rounded-full flex items-center justify-center border-2 border-bottle shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    <!-- Check badge -->
                                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-bottle text-white rounded-full flex items-center justify-center border-2 border-white">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-gray-900 mt-3">Venue</p>
                                <p class="text-xs font-semibold text-bottle mt-0.5">Selesai</p>
                            </div>

                            <!-- Step 2: Makeup -->
                            <div class="flex flex-col items-center">
                                <div class="relative w-14 h-14 bg-leafSoft text-bottle rounded-full flex items-center justify-center border-2 border-bottle shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.514"/></svg> <!-- using stand-in icon -->
                                    <!-- Check badge -->
                                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-bottle text-white rounded-full flex items-center justify-center border-2 border-white">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </span>
                                </div>
                                <p class="text-sm font-bold text-gray-900 mt-3">Makeup</p>
                                <p class="text-xs font-semibold text-bottle mt-0.5">Selesai</p>
                            </div>

                            <!-- Step 3: Catering -->
                            <div class="flex flex-col items-center">
                                <div class="relative w-14 h-14 bg-white text-bottle rounded-full flex items-center justify-center border-2 border-bottle shadow-sm">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5h18z"/></svg> <!-- catering icon -->
                                </div>
                                <p class="text-sm font-bold text-gray-900 mt-3">Catering</p>
                                <p class="text-xs font-semibold text-gray-500 mt-0.5">Proses</p>
                            </div>

                            <!-- Step 4: Dekorasi -->
                            <div class="flex flex-col items-center opacity-60">
                                <div class="relative w-14 h-14 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center border border-gray-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2-1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-600 mt-3">Dekorasi</p>
                                <p class="text-xs text-gray-500 mt-0.5">Proses</p>
                            </div>

                            <!-- Step 5: Dokumentasi -->
                            <div class="flex flex-col items-center opacity-60">
                                <div class="relative w-14 h-14 bg-gray-50 text-gray-400 rounded-full flex items-center justify-center border border-gray-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <p class="text-sm font-semibold text-gray-600 mt-3">Dokumentasi</p>
                                <p class="text-xs text-gray-500 mt-0.5">Proses</p>
                            </div>

                        </div>
                    </div>

                    <!-- Progress Bar Bottom -->
                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-900">3 dari 5 tahap selesai</span>
                            <span class="text-sm font-bold text-gray-900">70%</span>
                        </div>
                        <div class="w-full h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-bottle rounded-full transition-all duration-1000" style="width: 70%;"></div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Pembayaran Terbaru -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Pembayaran Terbaru</h3>
                            <a href="#" class="text-sm font-semibold text-bottle hover:text-bottleHover">Lihat Semua</a>
                        </div>

                        <!-- Info Box -->
                        <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">Pelunasan Tahap 2</h4>
                                    <p class="text-sm text-gray-500 mt-1">Jatuh tempo 10 Mei 2026</p>
                                </div>
                                <div class="text-right">
                                    <h4 class="text-base font-bold text-gray-900">Rp 10.000.000</h4>
                                    <span class="inline-block px-2.5 py-1 bg-orange-100 text-orange-600 text-[10px] font-bold uppercase tracking-wider rounded mt-1">Menunggu</span>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Mini -->
                        <div class="flex items-center justify-between mt-6 px-2">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 mb-1">Total Pembayaran</p>
                                <p class="text-sm font-bold text-gray-900">Rp 35.000.000</p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-semibold text-gray-400 mb-1">Terbayar</p>
                                <p class="text-sm font-bold text-gray-900">Rp 25.000.000 <span class="text-bottle font-semibold">(71%)</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Button -->
                    <div class="mt-8">
                        <button class="w-full py-3.5 bg-leafSoft text-bottle font-bold rounded-xl border border-transparent hover:bg-green-100 hover:border-green-200 transition">
                            Lihat Detail Pembayaran
                        </button>
                    </div>
                </div>

            </div>

        </main>
    </div>

    @include('components.loading-overlay')

    <script>
        // Add loading on all module links
        document.querySelectorAll('a[href*="route("]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!this.href.includes('javascript:') && !this.href.startsWith('http')) {
                    window.loadingOverlay?.show();
                }
            });
        });

        // Add loading on module navbar links
        document.querySelectorAll('aside a[href^="/customer/"], aside a[href^="/lapangan/"], aside a[href^="/admin/"]').forEach(link => {
            link.addEventListener('click', function(e) {
                window.loadingOverlay?.show();
            });
        });
    </script>

</body>
</html>
