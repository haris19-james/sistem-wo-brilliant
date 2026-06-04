<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Vendor - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Halaman manajemen vendor Brilliant Event & Wedding Organizer. Kelola mitra vendor pernikahan yang terdaftar di sistem.">

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
                        bottle: '#00A32A',
                        bottleHover: '#008F24',
                        leafSoft: '#EDFCF0',
                        grayBg: '#F8FAFC',
                        grayText: '#64748B',
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
        /* Smooth row hover */
        .vendor-row {
            transition: background-color 0.15s ease;
        }
        /* Action button micro-animation */
        .btn-action {
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        /* Pagination button */
        .page-btn {
            transition: all 0.18s ease;
        }
        /* Search focus glow */
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(26, 83, 26, 0.1);
        }
        /* Card hover lift */
        .metric-card {
            transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease;
        }
        .metric-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(0,0,0,0.07);
        }
    </style>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden"
      x-data="{ sidebarOpen: false, profileDropdown: false, searchQuery: '' }">

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
        <div class="flex items-center justify-center h-20 border-b border-gray-50 px-6">
            <div class="flex items-center space-x-2">
                <svg class="w-8 h-8 text-bottle" viewBox="0 0 24 24" fill="currentColor">
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
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <!-- Dashboard -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Dashboard
            </a>
            <!-- Booking -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Booking
            </a>
            <!-- Paket -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                Paket
            </a>
            <!-- Vendor — AKTIF -->
            <a href="#" id="nav-vendor" class="flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Vendor
            </a>
            <!-- Jadwal Acara -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Jadwal Acara
            </a>
            <!-- Pembayaran -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                </svg>
                Pembayaran
            </a>
            <!-- Chat -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Chat
            </a>
            <!-- Laporan -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Laporan
            </a>
            <!-- Pengaturan -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                Pengaturan
            </a>
        </div>

        <!-- Logout -->
        <div class="p-4 border-t border-gray-50">
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- ================================================ -->
    <!-- 2. MAIN CONTENT AREA                              -->
    <!-- ================================================ -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        <!-- HEADER -->
        <header class="bg-white border-b border-gray-100 h-20 px-6 flex items-center justify-between shrink-0">
            <!-- Left -->
            <div class="flex items-center">
                <!-- Hamburger (mobile) -->
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-bottle focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 leading-tight">Hi, Admin</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola data mitra vendor pernikahan Anda.</p>
                </div>
            </div>

            <!-- Right -->
            <div class="flex items-center space-x-6">
                <!-- Notification Dropdown -->
                @include('components.notification-dropdown')

                <div class="w-px h-6 bg-gray-200"></div>

                <!-- Admin Profile Dropdown -->
                <div class="relative">
                    <button @click="profileDropdown = !profileDropdown"
                            @click.away="profileDropdown = false"
                            class="flex items-center space-x-3 focus:outline-none">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                             alt="Admin Avatar"
                             class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm">
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-bold text-gray-900 leading-tight">Admin Brilliant</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <svg class="hidden md:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="profileDropdown" style="display: none;"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil Saya</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Pengaturan</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- Page Title -->
            <h3 class="text-lg font-bold text-gray-900 mb-6">Vendor</h3>

            <!-- ============================================ -->
            <!-- 3. METRIK RINGKASAN VENDOR (4 Info Cards)    -->
            <!-- ============================================ -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                <!-- Card 1: Total Vendor -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4" id="card-total-vendor">
                    <div class="bg-leafSoft p-3.5 rounded-2xl text-bottle shrink-0">
                        <!-- Calendar / vendor icon -->
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Total Vendor</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">48</h3>
                        <p class="text-xs font-medium text-green-600 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            16% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 2: Vendor Aktif -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4" id="card-vendor-aktif">
                    <div class="bg-orange-50 p-3.5 rounded-2xl text-orange-500 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Vendor Aktif</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">42</h3>
                        <p class="text-xs font-medium text-orange-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            14% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 3: Vendor Tidak Aktif -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4" id="card-vendor-nonaktif">
                    <div class="bg-red-50 p-3.5 rounded-2xl text-red-500 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Vendor Tidak Aktif</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">6</h3>
                        <p class="text-xs font-medium text-red-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            25% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 4: Menunggu Verifikasi -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4" id="card-vendor-verifikasi">
                    <div class="bg-yellow-50 p-3.5 rounded-2xl text-yellow-500 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Menunggu Verifikasi</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">3</h3>
                        <p class="text-xs font-medium text-yellow-600 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            50% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 4. KONTROL AKSI: Search + Tambah + Export    -->
            <!-- ============================================ -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                <!-- Search Bar (kiri) -->
                <div class="relative w-full sm:w-72">
                    <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        type="text"
                        id="input-search-vendor"
                        x-model="searchQuery"
                        placeholder="Cari vendor..."
                        class="search-input w-full pl-10 pr-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-bottle transition-all duration-200">
                </div>

                <!-- Action Buttons (kanan) -->
                <div class="flex items-center gap-3 shrink-0">
                    <!-- Tambah Vendor -->
                    <button id="btn-tambah-vendor"
                            class="inline-flex items-center px-5 py-2.5 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Vendor
                    </button>
                    <!-- Export -->
                    <button id="btn-export-vendor"
                            class="inline-flex items-center px-5 py-2.5 bg-white text-gray-600 text-sm font-semibold rounded-xl border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export
                    </button>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 5. TABEL DAFTAR VENDOR                       -->
            <!-- ============================================ -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.03)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse" id="vendor-table">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100">
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide w-14">No.</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Vendor</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kategori</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kontak</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wide text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm" id="vendor-tbody">

                            <!-- Row 1: Dekorasi Indah — Aktif -->
                            <tr class="vendor-row hover:bg-gray-50/60 transition-colors" id="vendor-row-1">
                                <td class="px-6 py-4 text-gray-500 font-medium">1</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-leafSoft flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-bottle" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></path></svg>
                                        </div>
                                        <span class="font-semibold text-gray-900">Dekorasi Indah</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Dekorasi</td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs tracking-wide">0812-1234-5678</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50" id="btn-detail-vendor-1">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-edit-vendor-1">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50" id="btn-status-vendor-1">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Nonaktifkan
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 2: Catering Sejahtera — Aktif -->
                            <tr class="vendor-row hover:bg-gray-50/60 transition-colors" id="vendor-row-2">
                                <td class="px-6 py-4 text-gray-500 font-medium">2</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                        </div>
                                        <span class="font-semibold text-gray-900">Catering Sejahtera</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Catering</td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs tracking-wide">0812-2345-6789</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50" id="btn-detail-vendor-2">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-edit-vendor-2">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50" id="btn-status-vendor-2">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Nonaktifkan
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 3: Foto Moment — Aktif -->
                            <tr class="vendor-row hover:bg-gray-50/60 transition-colors" id="vendor-row-3">
                                <td class="px-6 py-4 text-gray-500 font-medium">3</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <span class="font-semibold text-gray-900">Foto Moment</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Fotografi</td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs tracking-wide">0812-3456-7890</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50" id="btn-detail-vendor-3">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-edit-vendor-3">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50" id="btn-status-vendor-3">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Nonaktifkan
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 4: Rias Cantik — Tidak Aktif -->
                            <tr class="vendor-row hover:bg-gray-50/60 transition-colors" id="vendor-row-4">
                                <td class="px-6 py-4 text-gray-500 font-medium">4</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-pink-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                                        </div>
                                        <span class="font-semibold text-gray-900">Rias Cantik</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Make Up</td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs tracking-wide">0812-4567-8901</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-500">
                                        Tidak Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50" id="btn-detail-vendor-4">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-edit-vendor-4">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <!-- Aktifkan (green) karena saat ini Tidak Aktif -->
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-green-600 bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-status-vendor-4">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Aktifkan
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 5: MC Professional — Aktif -->
                            <tr class="vendor-row hover:bg-gray-50/60 transition-colors" id="vendor-row-5">
                                <td class="px-6 py-4 text-gray-500 font-medium">5</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                        </div>
                                        <span class="font-semibold text-gray-900">MC Professional</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">MC</td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs tracking-wide">0812-5678-9012</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50" id="btn-detail-vendor-5">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-edit-vendor-5">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50" id="btn-status-vendor-5">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Nonaktifkan
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 6: Musik Harmoni — Menunggu Verifikasi -->
                            <tr class="vendor-row hover:bg-gray-50/60 transition-colors" id="vendor-row-6">
                                <td class="px-6 py-4 text-gray-500 font-medium">6</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                        </div>
                                        <span class="font-semibold text-gray-900">Musik Harmoni</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Musik</td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs tracking-wide">0812-6789-0123</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-orange-50 text-orange-500">
                                        Menunggu Verifikasi
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50" id="btn-detail-vendor-6">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-edit-vendor-6">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50" id="btn-status-vendor-6">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Nonaktifkan
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 7: Lighting Pro — Aktif -->
                            <tr class="vendor-row hover:bg-gray-50/60 transition-colors" id="vendor-row-7">
                                <td class="px-6 py-4 text-gray-500 font-medium">7</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>
                                        </div>
                                        <span class="font-semibold text-gray-900">Lighting Pro</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Lighting</td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs tracking-wide">0812-7890-1234</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600">
                                        Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50" id="btn-detail-vendor-7">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-edit-vendor-7">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50" id="btn-status-vendor-7">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Nonaktifkan
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Row 8: Souvenir Unik — Tidak Aktif -->
                            <tr class="vendor-row hover:bg-gray-50/60 transition-colors" id="vendor-row-8">
                                <td class="px-6 py-4 text-gray-500 font-medium">8</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center shrink-0">
                                            <svg class="w-4 h-4 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/></svg>
                                        </div>
                                        <span class="font-semibold text-gray-900">Souvenir Unik</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">Souvenir</td>
                                <td class="px-6 py-4 text-gray-600 font-mono text-xs tracking-wide">0812-8901-2345</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-red-500">
                                        Tidak Aktif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50" id="btn-detail-vendor-8">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Detail
                                        </button>
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-edit-vendor-8">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
                                        <!-- Aktifkan (green) karena saat ini Tidak Aktif -->
                                        <button class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-medium text-green-600 bg-white border border-green-200 rounded-lg hover:bg-leafSoft" id="btn-status-vendor-8">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Aktifkan
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>

                <!-- ============================================ -->
                <!-- 6. FOOTER TABEL & PAGINASI                   -->
                <!-- ============================================ -->
                <div class="flex flex-col sm:flex-row items-center justify-between px-6 py-4 border-t border-gray-100 gap-4">
                    <!-- Info kiri -->
                    <p class="text-sm text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">1 - 8</span> dari <span class="font-semibold text-gray-700">48</span> data
                    </p>

                    <!-- Paginasi kanan -->
                    <nav class="flex items-center gap-1" aria-label="Paginasi vendor" id="vendor-pagination">
                        <!-- Prev -->
                        <button class="page-btn w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-400 bg-white hover:border-bottle hover:text-bottle disabled:opacity-40 disabled:cursor-not-allowed transition" disabled id="btn-page-prev" aria-label="Halaman sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        </button>

                        <!-- Page 1 — aktif -->
                        <button class="page-btn w-9 h-9 flex items-center justify-center rounded-lg text-sm font-semibold bg-bottle text-white border border-bottle shadow-sm" id="btn-page-1" aria-current="page">1</button>

                        <!-- Page 2 -->
                        <button class="page-btn w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium border border-gray-200 text-gray-600 bg-white hover:border-bottle hover:text-bottle transition" id="btn-page-2">2</button>

                        <!-- Page 3 -->
                        <button class="page-btn w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium border border-gray-200 text-gray-600 bg-white hover:border-bottle hover:text-bottle transition" id="btn-page-3">3</button>

                        <!-- Ellipsis -->
                        <span class="w-9 h-9 flex items-center justify-center text-sm text-gray-400 select-none">...</span>

                        <!-- Page 6 -->
                        <button class="page-btn w-9 h-9 flex items-center justify-center rounded-lg text-sm font-medium border border-gray-200 text-gray-600 bg-white hover:border-bottle hover:text-bottle transition" id="btn-page-6">6</button>

                        <!-- Next -->
                        <button class="page-btn w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 bg-white hover:border-bottle hover:text-bottle transition" id="btn-page-next" aria-label="Halaman berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </nav>
                </div>
            </div>
            <!-- End Tabel Vendor -->

        </main>
    </div>

    <!-- ================================================ -->
    <!-- SCRIPT: Client-side search filter                 -->
    <!-- ================================================ -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('input-search-vendor');
            const rows = document.querySelectorAll('#vendor-tbody tr');

            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const q = this.value.toLowerCase().trim();
                    rows.forEach(row => {
                        const text = row.textContent.toLowerCase();
                        row.style.display = (q === '' || text.includes(q)) ? '' : 'none';
                    });
                });
            }

            // Pagination active state toggle (UI demo)
            const pageBtns = document.querySelectorAll('[id^="btn-page-"]:not(#btn-page-prev):not(#btn-page-next)');
            pageBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    pageBtns.forEach(b => {
                        b.classList.remove('bg-bottle', 'text-white', 'border-bottle', 'shadow-sm');
                        b.classList.add('border-gray-200', 'text-gray-600', 'bg-white');
                    });
                    this.classList.add('bg-bottle', 'text-white', 'border-bottle', 'shadow-sm');
                    this.classList.remove('border-gray-200', 'text-gray-600', 'bg-white');
                });
            });
        });
    </script>

</body>
</html>
