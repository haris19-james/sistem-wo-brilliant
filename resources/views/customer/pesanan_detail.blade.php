<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Rincian pesanan layanan Client Brilliant Event & Wedding Organizer.">

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
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #E2E8F0;
            border-radius: 4px;
        }
        .icon-box {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background-color: #EDFCF0;
            color: #00A32A;
            border-radius: 50%;
            flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden" 
      x-data="{ 
          sidebarOpen: false, 
          profileDropdown: false,
          statusDropdownOpen: false,
          statusFilter: 'Semua Status'
      }">

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
                <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                
                <!-- Pemesanan Saya (AKTIF) -->
                <a href="{{ route('client.pesanan') }}" class="flex items-center px-4 py-3.5 bg-leafSoft text-bottle font-semibold rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Pemesanan Saya
                </a>
                
                <a href="{{ route('client.jadwal') }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Jadwal Acara
                </a>
                <a href="{{ route('client.invoice', 1) }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    Pembayaran
                </a>
                <a href="#" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Chat Konsultasi
                </a>
                <a href="#" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pengaturan
                </a>
            </div>
            <div class="pt-6 border-t border-gray-50">
                <a href="#" class="flex items-center px-4 py-3.5 text-grayText hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
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
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 h-24 px-8 flex items-center justify-between shrink-0 sticky top-0 z-30">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-bottle focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                </button>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">Hi, Marsya</h2>
                </div>
            </div>

            <div class="flex items-center space-x-6">
                <!-- Notification Dropdown -->
                @include('components.notification-dropdown')
                <div class="w-px h-8 bg-gray-200"></div>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button @click="profileDropdown = !profileDropdown" @click.away="profileDropdown = false" class="flex items-center space-x-3 focus:outline-none">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2.5&w=256&h=256&q=80" alt="Customer Avatar" class="w-11 h-11 rounded-full object-cover border-2 border-green-50 shadow-sm">
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-bold text-gray-900 leading-tight">Marsya</p>
                        </div>
                        <svg class="hidden md:block w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="profileDropdown" style="display: none;" class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Profil Saya</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Pengaturan Akun</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN LAYOUT BODY -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-8">
            <div class="max-w-4xl mx-auto space-y-6">

                <!-- Action Bar: Back & Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-2">
                    <a href="{{ route('client.pesanan') }}" class="inline-flex items-center text-sm font-bold text-gray-600 hover:text-bottle transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                        Kembali ke Pemesanan Saya
                    </a>

                    <!-- Dropdown Status -->
                    <div class="relative w-48 shrink-0">
                        <button @click="statusDropdownOpen = !statusDropdownOpen" @click.away="statusDropdownOpen = false" class="w-full inline-flex items-center justify-between px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm focus:outline-none">
                            <span x-text="statusFilter">Semua Status</span>
                            <svg class="w-4 h-4 text-gray-400 ml-2" :class="statusDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="statusDropdownOpen" style="display: none;" class="absolute right-0 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-40">
                            <button @click="statusFilter = 'Semua Status'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Semua Status</button>
                            <button @click="statusFilter = 'Sedang Berlangsung'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Sedang Berlangsung</button>
                            <button @click="statusFilter = 'Selesai'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Selesai</button>
                        </div>
                    </div>
                </div>

                <!-- 1. Hero Section Pesanan -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 flex flex-col md:flex-row gap-8 items-start shadow-sm relative">
                    <!-- Photo -->
                    <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80" 
                         alt="Paket Gold" 
                         class="w-full md:w-64 h-56 object-cover rounded-xl shadow-sm shrink-0">
                    
                    <!-- Text Info -->
                    <div class="flex-1 w-full relative">
                        <div class="flex items-start justify-between mb-4">
                            <!-- Badge -->
                            <div class="inline-flex items-center px-3 py-1.5 bg-green-50 text-bottle text-xs font-bold rounded-md">
                                <span class="w-1.5 h-1.5 bg-bottle rounded-full mr-2"></span>
                                Sedang Berlangsung
                            </div>

                            <!-- Opsi Titik Tiga -->
                            <div x-data="{ heroOptOpen: false }" class="relative">
                                <button @click="heroOptOpen = !heroOptOpen" @click.away="heroOptOpen = false" class="w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 hover:text-gray-800 bg-white">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="5" cy="12" r="2"></circle><circle cx="12" cy="12" r="2"></circle><circle cx="19" cy="12" r="2"></circle>
                                    </svg>
                                </button>
                                <div x-show="heroOptOpen" style="display: none;" class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-40">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50" data-open-cancel-modal="{{ $pesanan->id }}">Ajukan Pembatalan</a>
                                </div>
                            </div>
                        </div>

                        <h2 class="text-3xl font-bold text-gray-900 leading-tight">Paket Gold</h2>
                        <h3 class="text-xl font-semibold text-gray-800 mt-2">Pernikahan Marsya & Axtra</h3>
                        
                        <div class="flex items-center text-gray-600 mt-4 text-sm font-medium">
                            <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            The Alana Hotel & Convention Center Garut
                        </div>

                        <p class="text-sm font-medium text-gray-500 mt-4">ID Pesanan: #BR-250524-001</p>
                    </div>
                </div>

                <!-- 2. Detail Acara -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                    <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                        <div class="icon-box mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Detail Acara</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-12">
                        <!-- Item -->
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Tanggal Acara</p>
                                <p class="text-sm text-gray-500 mt-1">30 Juni 2026</p>
                            </div>
                        </div>
                        <!-- Item -->
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Jumlah Tamu (Est.)</p>
                                <p class="text-sm text-gray-500 mt-1">300 Orang</p>
                            </div>
                        </div>
                        <!-- Item -->
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Jam Acara</p>
                                <p class="text-sm text-gray-500 mt-1">08.00 WIB</p>
                            </div>
                        </div>
                        <!-- Item -->
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Tema Acara</p>
                                <p class="text-sm text-gray-500 mt-1">Elegant Green & White</p>
                            </div>
                        </div>
                        <!-- Item -->
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-gray-400 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <div>
                                <p class="text-sm font-semibold text-gray-800">Lokasi</p>
                                <p class="text-sm text-gray-500 mt-1">The Alana Hotel & Convention Center Garut</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Layanan yang Didapat -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm relative">
                    <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                        <div class="icon-box mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Layanan yang Didapat</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-12">
                        <!-- Items -->
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-bottle mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-gray-800">Dekorasi</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-bottle mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-gray-800">Makeup</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-bottle mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-gray-800">Catering</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-bottle mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-gray-800">MC</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-bottle mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-gray-800">Dokumentasi</span>
                        </div>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-bottle mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-medium text-gray-800">Entertainment</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end md:absolute md:bottom-8 md:right-8">
                        <a href="#" class="text-sm font-bold text-bottle hover:underline">+ 5 Layanan Lainnya</a>
                    </div>
                </div>

                <!-- 4. Pembayaran & Invoice -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                    <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                        <div class="icon-box mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Pembayaran</h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left: Rincian Biaya -->
                        <div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-gray-800">Total Biaya</span>
                                    <span class="font-bold text-gray-900">Rp 35.000.000</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-gray-800">DP (30%)</span>
                                    <span class="font-bold text-gray-900">Rp 10.500.000</span>
                                </div>
                                <div class="flex justify-between items-center text-sm">
                                    <span class="font-semibold text-gray-800">Sisa Pembayaran</span>
                                    <span class="font-bold text-gray-900">Rp 24.500.000</span>
                                </div>
                            </div>
                            
                            @if($pesanan->catatan_pembayaran && $pesanan->status_pembayaran === 'unpaid')
                            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 mb-6 text-sm text-red-800">
                                <p class="font-semibold">Pembayaran Ditolak</p>
                                <p class="mt-1">{{ $pesanan->catatan_pembayaran }}</p>
                                <a href="#" class="inline-flex mt-3 items-center px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                    Unggah Ulang Bukti Transfer
                                </a>
                            </div>
                            @endif

                            <div class="mt-6 pt-4 border-t border-gray-100 flex justify-between items-center">
                                <span class="text-sm font-semibold text-gray-800">Status Pembayaran</span>
                                @switch($pesanan->status_pembayaran)
                                    @case('unpaid')
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase bg-red-50 text-red-700 border border-red-200">
                                            Menunggu Pembayaran DP
                                        </span>
                                        @break
                                    @case('dp_paid')
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase bg-yellow-50 text-yellow-700 border border-yellow-200">
                                            DP Terverifikasi
                                        </span>
                                        @break
                                    @case('fully_paid')
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase bg-green-50 text-green-700 border border-green-200">
                                            Lunas Penuh
                                        </span>
                                        @break
                                    @default
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold uppercase bg-gray-100 text-gray-600 border border-gray-200">
                                            {{ ucfirst(str_replace('_', ' ', $pesanan->status_pembayaran ?? 'tidak diketahui')) }}
                                        </span>
                                @endswitch
                            </div>
                        </div>

                        <!-- Right: Invoice Box -->
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-100 flex flex-col justify-between items-start">
                            <div class="flex items-start mb-4">
                                <div class="p-2 bg-white rounded shadow-sm text-bottle border border-gray-200 mr-4">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Invoice</h4>
                                    <p class="text-[11px] font-medium text-gray-500 mt-0.5">Lihat detail invoice pembayaran</p>
                                </div>
                            </div>
                            <a href="{{ route('client.invoice', 1) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-gray-800 text-sm font-bold rounded shadow-sm hover:bg-gray-50 transition">
                                Lihat Invoice 
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- 5. Timeline Persiapan -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                    <div class="flex items-center mb-8 border-b border-gray-100 pb-4">
                        <div class="icon-box mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Timeline Persiapan</h3>
                    </div>

                    <div class="relative ml-2 space-y-8">
                        <!-- Vertical line background -->
                        <div class="absolute top-2 left-[11px] bottom-6 w-0.5 bg-gray-200"></div>
                        <!-- Active Vertical line -->
                        <div class="absolute top-2 left-[11px] h-[65%] w-0.5 bg-bottle"></div>

                        <!-- Step 1 -->
                        <div class="relative pl-10">
                            <!-- Dot -->
                            <div class="absolute -left-[1px] top-0 w-6 h-6 bg-bottle rounded-full flex items-center justify-center text-white ring-4 ring-white shadow-sm z-10">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex justify-between items-start -mt-0.5">
                                <h4 class="text-sm font-semibold text-gray-900">Booking Dikonfirmasi</h4>
                                <span class="text-sm font-medium text-gray-500">24 Mei 2026</span>
                            </div>
                        </div>

                        <!-- Step 2 -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0 w-6 h-6 bg-bottle rounded-full flex items-center justify-center text-white ring-4 ring-white shadow-sm z-10">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex justify-between items-start -mt-0.5">
                                <h4 class="text-sm font-semibold text-gray-900">DP Diterima</h4>
                                <span class="text-sm font-medium text-gray-500">24 Mei 2026</span>
                            </div>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0 w-6 h-6 bg-bottle rounded-full flex items-center justify-center text-white ring-4 ring-white shadow-sm z-10">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div class="flex justify-between items-start -mt-0.5">
                                <h4 class="text-sm font-semibold text-gray-900">Vendor Dipilih & Dikonfirmasi</h4>
                                <span class="text-sm font-medium text-gray-500">10 Juni 2026</span>
                            </div>
                        </div>

                        <!-- Step 4 (Current/Pending) -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0 w-6 h-6 bg-white border-2 border-bottle rounded-full flex items-center justify-center ring-4 ring-white shadow-sm z-10">
                                <div class="w-2 h-2 bg-bottle rounded-full"></div>
                            </div>
                            <div class="flex justify-between items-start -mt-0.5">
                                <h4 class="text-sm font-bold text-bottle">Acara Berlangsung</h4>
                                <span class="text-sm font-bold text-bottle">30 Juni 2026</span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- 6. Review Vendor -->
                @if($pesanan->status === 'Selesai')
                <div id="review-vendor" class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm scroll-mt-24">
                    <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                        <div class="icon-box mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Review Vendor</h3>
                    </div>

                    @foreach($pesanan->vendors as $vendor)
                    <div class="border border-gray-100 rounded-xl p-5 mb-4 last:mb-0">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-start">
                                @if($vendor->image_url)
                                <img src="{{ $vendor->image_url }}" alt="{{ $vendor->nama_vendor }}" class="w-16 h-16 rounded-lg object-cover mr-4">
                                @else
                                <div class="w-16 h-16 rounded-lg bg-gray-100 flex items-center justify-center mr-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                @endif
                                <div>
                                    <h4 class="text-base font-bold text-gray-900">{{ $vendor->nama_vendor }}</h4>
                                    <p class="text-sm text-gray-500 mt-0.5">{{ $vendor->kategori }}</p>
                                    <div class="flex items-center mt-2">
                                        <x-rating-stars :value="$vendor->rating_avg ?? 0" :count="$vendor->rating_count ?? 0" size="sm" color="green" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Check if user already reviewed this vendor --}}
                        @php
                            $existingReview = \App\Models\Review::where('user_id', auth()->id())
                                ->where('vendor_id', $vendor->id)
                                ->where('pesanan_id', $pesanan->id)
                                ->first();
                        @endphp

                        @if($existingReview)
                            {{-- Show existing review with edit/delete option --}}
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $existingReview->rating)
                                            <svg class="w-4 h-4 text-green-500 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-300 fill-current" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                        @endif
                                    @endfor
                                    <span class="text-sm font-medium text-gray-600 ml-2">{{ $existingReview->rating }}/5</span>
                                </div>
                                @if($existingReview->ulasan)
                                    <p class="text-sm text-gray-700">{{ $existingReview->ulasan }}</p>
                                @endif
                                <div class="flex gap-2 mt-3">
                                    <button onclick="openEditReviewModal({{ $existingReview->id }}, {{ $existingReview->rating }}, '{{ $existingReview->ulasan ?? '' }}')" class="text-xs font-medium text-bottle hover:underline">Edit Review</button>
                                    <form action="{{ route('client.review.destroy', $existingReview->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus review ini?')" class="text-xs font-medium text-red-600 hover:underline">Hapus Review</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            {{-- Show review form --}}
                            <form action="{{ route('client.review.store', $vendor) }}" method="POST" x-data="{ rating: 0, hoverRating: 0 }">
                                @csrf
                                <input type="hidden" name="pesanan_id" value="{{ $pesanan->id }}">
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex items-center gap-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" 
                                                @click="rating = {{ $i }}"
                                                @mouseenter="hoverRating = {{ $i }}"
                                                @mouseleave="hoverRating = 0"
                                                class="focus:outline-none">
                                                <svg class="w-8 h-8 transition-colors" 
                                                    :class="(hoverRating >= {{ $i }} || rating >= {{ $i }}) ? 'text-green-500 fill-current' : 'text-gray-300 fill-current'"
                                                    viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            </button>
                                        @endfor
                                        <input type="hidden" name="rating" x-model="rating" required>
                                        <span class="text-sm font-medium text-gray-600 ml-2" x-text="rating + '/5'">0/5</span>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Ulasan (Opsional)</label>
                                    <textarea name="ulasan" rows="3" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-bottle focus:border-transparent" placeholder="Bagaimana pengalaman Anda dengan vendor ini?"></textarea>
                                </div>

                                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition">
                                    Kirim Ulasan
                                </button>
                            </form>
                        @endif
                    </div>
                    @endforeach
                </div>
                @endif

            </div> <!-- end max-w -->
        </main>
    </div>

</body>
</html>

@if($pesanan->status_pemesanan === 'completed')
<!-- Booking-level Review (Client) -->
<div class="fixed bottom-6 right-6 w-96">
    <div class="bg-white rounded-xl border p-4 shadow-lg">
        <h4 class="font-bold mb-2">Ulasan Acara</h4>
        @if($pesanan->bookingReview)
            <div class="text-sm text-gray-700 mb-2">
                <div class="font-semibold">Rating: {{ $pesanan->bookingReview->rating }}/5</div>
                @if($pesanan->bookingReview->review_text)
                    <p class="mt-1">{{ $pesanan->bookingReview->review_text }}</p>
                @endif
                <div class="mt-3 flex gap-2">
                    <form action="{{ route('pesanan.review.update', $pesanan->bookingReview) }}" method="POST">
                        @csrf @method('PUT')
                        <input type="hidden" name="rating" value="{{ $pesanan->bookingReview->rating }}">
                        <button class="text-xs text-bottle">Edit</button>
                    </form>
                    <form action="{{ route('pesanan.review.destroy', $pesanan->bookingReview) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="text-xs text-red-600">Hapus</button>
                    </form>
                </div>
            </div>
        @else
            <form action="{{ route('pesanan.review.store', $pesanan) }}" method="POST">
                @csrf
                <div class="mb-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Rating</label>
                    <select name="rating" required class="w-full border rounded px-2 py-1 text-sm">
                        @for($i=5; $i>=1; $i--)
                            <option value="{{ $i }}">{{ $i }} / 5</option>
                        @endfor
                    </select>
                </div>
                <div class="mb-2">
                    <label class="block text-xs font-medium text-gray-700 mb-1">Ulasan (opsional)</label>
                    <textarea name="review_text" rows="3" class="w-full border rounded px-2 py-1 text-sm"></textarea>
                </div>
                <button class="w-full py-2 bg-bottle text-white rounded text-sm">Kirim Ulasan Acara</button>
            </form>
        @endif
    </div>
</div>
@endif
