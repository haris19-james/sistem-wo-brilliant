<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Paket - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Halaman manajemen paket wedding Brilliant Event & Wedding Organizer. Kelola paket pernikahan yang ditampilkan di website.">
    
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
                        bottle: '#00A32A',       /* Hijau Botol Premium */
                        bottleHover: '#008F24',
                        leafSoft: '#EDFCF0',     /* Hijau Sangat Muda / Background Aktif */
                        grayBg: '#F8FAFC',       /* Background Utama Dashboard */
                        grayText: '#64748B',     /* Teks Abu-abu */
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
        /* Hide scrollbar for chat/list */
        .hide-scrollbar::-webkit-scrollbar { display: none; }
        .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

        /* Smooth hover transitions for package cards */
        .paket-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .paket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        /* Badge hover pulse */
        .badge-facility {
            transition: all 0.2s ease;
        }
        .badge-facility:hover {
            background-color: #EDFCF0;
            border-color: #00A32A;
        }

        /* Action button hover */
        .btn-action {
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            transform: translateY(-1px);
        }

        /* Search input focus animation */
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(26, 83, 26, 0.1);
        }

        /* Notification badge pulse */
        @keyframes pulse-ring {
            0% { transform: scale(0.95); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.6; }
            100% { transform: scale(0.95); opacity: 1; }
        }
        .notification-pulse {
            animation: pulse-ring 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, profileDropdown: false, searchQuery: '' }">

    <!-- ========================================== -->
    <!-- 1. SIDEBAR NAVIGASI (Kiri)                 -->
    <!-- ========================================== -->
    
    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-100 flex flex-col transition-transform duration-300 lg:translate-x-0 lg:static lg:inset-0">
        
        <!-- Logo Area -->
        <div class="flex items-center justify-center h-20 border-b border-gray-50 px-6">
            <div class="flex items-center space-x-2">
                <svg class="w-8 h-8 text-bottle" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C6.477 2 2 6.477 2 12c0 2.125.666 4.095 1.791 5.709l-.498.498a1 1 0 001.414 1.414l.498-.498A9.957 9.957 0 0012 22c5.523 2 10-2.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-1-13a1 1 0 10-2 0v2a1 1 0 102 0V7zm0 4a1 1 0 10-2 0v6a1 1 0 102 0v-6z"/>
                    <path d="M12 22C6.47715 22 2 17.5228 2 12C2 10.8954 2.1791 9.83226 2.50652 8.83582C3.12535 12.8378 6.5828 15.9372 10.7766 16.0827C10.9234 16.0878 11.0706 16.0905 11.2183 16.0905C11.5173 16.0905 11.8133 16.0818 12.1056 16.065C16.3262 15.823 19.7891 12.4411 20.218 8.16335C20.4851 7.89436 20.733 7.6083 20.9599 7.3065C21.6366 8.74233 22 10.3278 22 12C22 17.5228 17.5228 22 12 22Z" fill="#00A32A"/>
                </svg>
                <div class="leading-tight">
                    <h1 class="text-xl font-bold text-gray-900 tracking-tight">Brilliant</h1>
                    <p class="text-[0.45rem] text-gray-500 font-medium tracking-widest uppercase">Event & Wedding Organizer</p>
                </div>
            </div>
        </div>

        <!-- Menu Navigasi -->
        <div class="flex-1 overflow-y-auto py-6 px-4 space-y-1">
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Booking
            </a>
            <!-- PAKET: Menu Aktif -->
            <a href="#" id="nav-paket" class="flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                Paket
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Vendor
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Jadwal Acara
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                Pembayaran
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                Chat
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Laporan
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Pengaturan
            </a>
        </div>

        <!-- Logout Bottom -->
        <div class="p-4 border-t border-gray-50">
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-red-50 hover:text-red-600 font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                Logout
            </a>
        </div>
    </aside>

    <!-- ========================================== -->
    <!-- 2. MAIN CONTENT AREA                       -->
    <!-- ========================================== -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- HEADER (Atas Kanan) -->
        <header class="bg-white border-b border-gray-100 h-20 px-6 flex items-center justify-between shrink-0">
            <!-- Left Side Header -->
            <div class="flex items-center">
                <!-- Hamburger Menu for Mobile -->
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-bottle focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 leading-tight">Hi, Admin 👋</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola paket wedding yang ditampilkan di website.</p>
                </div>
            </div>

            <!-- Right Side Header -->
            <div class="flex items-center space-x-6">
                <!-- Notification Dropdown -->
                <x-notification-bell />

                <div class="w-px h-6 bg-gray-200"></div>

                <!-- Admin Profile Dropdown -->
                <div class="relative">
                    <button @click="profileDropdown = !profileDropdown" @click.away="profileDropdown = false" class="flex items-center space-x-3 focus:outline-none">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Admin Avatar" class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm">
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-bold text-gray-900 leading-tight">Admin Brilliant</p>
                            <p class="text-xs text-gray-500">Administrator</p>
                        </div>
                        <svg class="hidden md:block w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <!-- Dropdown Menu -->
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

        <!-- KONTEN KELOLA PAKET -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            
            <!-- ========================================== -->
            <!-- 3. METRIK RINGKASAN PAKET (4 Info Cards)   -->
            <!-- ========================================== -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                
                <!-- Card 1: Total Paket -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4" id="card-total-paket">
                    <div class="bg-leafSoft p-3.5 rounded-2xl text-bottle shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Total Paket</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">28</h3>
                        <p class="text-xs font-medium text-green-600 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            15% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 2: Paket Aktif -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4" id="card-paket-aktif">
                    <div class="bg-leafSoft p-3.5 rounded-2xl text-bottle shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Paket Aktif</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">22</h3>
                        <p class="text-xs font-medium text-green-600 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            10% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 3: Paket Nonaktif -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4" id="card-paket-nonaktif">
                    <div class="bg-red-50 p-3.5 rounded-2xl text-red-500 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Paket Nonaktif</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">6</h3>
                        <p class="text-xs font-medium text-red-500 flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                            14% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 4: Paket Terlaris -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4" id="card-paket-terlaris">
                    <div class="bg-leafSoft p-3.5 rounded-2xl text-bottle shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Paket Terlaris</p>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2 leading-tight">Gold Package</h3>
                        <p class="text-xs font-medium text-green-600">32% dari total penjualan</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 4. KONTROL AKSI (Tombol + Search)          -->
            <!-- ========================================== -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-6 gap-4">
                <!-- Tombol Tambah Paket -->
                <button id="btn-tambah-paket" class="inline-flex items-center px-5 py-2.5 bg-bottle text-white text-sm font-semibold rounded-xl hover:bg-bottleHover transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tambah Paket
                </button>

                <!-- Search Bar -->
                <div class="relative w-full sm:w-72">
                    <input 
                        type="text" 
                        x-model="searchQuery"
                        id="input-search-paket"
                        placeholder="Cari paket..." 
                        class="search-input w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-bottle transition-all duration-200"
                    >
                    <svg class="absolute right-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- 5. DAFTAR PAKET PERNIKAHAN (Cards)         -->
            <!-- ========================================== -->
            <div class="space-y-5" id="paket-list">

                <!-- ===== PAKET 1: Silver Package ===== -->
                <div class="paket-card bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.03)] overflow-hidden" id="paket-silver">
                    <div class="flex flex-col lg:flex-row">
                        <!-- Foto Paket -->
                        <div class="lg:w-56 xl:w-64 shrink-0">
                            <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                                 alt="Silver Package - Dekorasi Pernikahan Elegan" 
                                 class="w-full h-48 lg:h-full object-cover lg:rounded-l-2xl">
                        </div>

                        <!-- Info & Actions -->
                        <div class="flex-1 p-5 lg:p-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Informasi Utama -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-1">
                                    <div>
                                        <h4 class="text-lg font-bold text-bottle mb-0.5">Silver Package</h4>
                                        <p class="text-base font-bold text-bottle">Rp 28.000.000</p>
                                    </div>
                                    <!-- Status Badge -->
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100 lg:hidden xl:inline-flex">
                                        Aktif <span class="ml-1.5 w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2 mb-3 leading-relaxed">Paket pernikahan dengan konsep elegan dan simple.</p>
                                
                                <!-- Fasilitas Badges -->
                                <div class="flex flex-wrap gap-2">
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Dekorasi
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Catering 300 pax
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Dokumentasi
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        MC
                                    </span>
                                </div>
                            </div>

                            <!-- Status (Desktop Only) & Tombol Aksi -->
                            <div class="flex flex-col items-end gap-3 shrink-0">
                                <!-- Status Badge (Desktop) -->
                                <span class="hidden lg:inline-flex xl:hidden items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">
                                    Aktif <span class="ml-1.5 w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                </span>

                                <!-- Tombol Aksi -->
                                <div class="flex items-center gap-2 mt-auto">
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition" id="btn-detail-silver">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </button>
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft hover:border-bottle transition" id="btn-edit-silver">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:border-red-400 transition" id="btn-hapus-silver">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== PAKET 2: Gold Package ===== -->
                <div class="paket-card bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.03)] overflow-hidden" id="paket-gold">
                    <div class="flex flex-col lg:flex-row">
                        <!-- Foto Paket -->
                        <div class="lg:w-56 xl:w-64 shrink-0">
                            <img src="https://images.unsplash.com/photo-1522673607200-164d1b6ce486?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                                 alt="Gold Package - Dekorasi Pernikahan Premium" 
                                 class="w-full h-48 lg:h-full object-cover lg:rounded-l-2xl">
                        </div>

                        <!-- Info & Actions -->
                        <div class="flex-1 p-5 lg:p-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Informasi Utama -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-1">
                                    <div>
                                        <h4 class="text-lg font-bold text-bottle mb-0.5">Gold Package</h4>
                                        <p class="text-base font-bold text-bottle">Rp 46.000.000</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100 lg:hidden xl:inline-flex">
                                        Aktif <span class="ml-1.5 w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2 mb-3 leading-relaxed">Paket premium dengan fasilitas lengkap untuk hari spesial Anda.</p>
                                
                                <!-- Fasilitas Badges -->
                                <div class="flex flex-wrap gap-2">
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Dekorasi Premium
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Catering 500 pax
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Makeup
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Dokumentasi
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        MC
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Entertainment
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Photo Booth
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex flex-col items-end gap-3 shrink-0">
                                <span class="hidden lg:inline-flex xl:hidden items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">
                                    Aktif <span class="ml-1.5 w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                </span>
                                <div class="flex items-center gap-2 mt-auto">
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition" id="btn-detail-gold">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </button>
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft hover:border-bottle transition" id="btn-edit-gold">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:border-red-400 transition" id="btn-hapus-gold">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== PAKET 3: Platinum Package ===== -->
                <div class="paket-card bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.03)] overflow-hidden" id="paket-platinum">
                    <div class="flex flex-col lg:flex-row">
                        <!-- Foto Paket -->
                        <div class="lg:w-56 xl:w-64 shrink-0">
                            <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                                 alt="Platinum Package - Dekorasi Pernikahan Eksklusif" 
                                 class="w-full h-48 lg:h-full object-cover lg:rounded-l-2xl">
                        </div>

                        <!-- Info & Actions -->
                        <div class="flex-1 p-5 lg:p-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Informasi Utama -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-1">
                                    <div>
                                        <h4 class="text-lg font-bold text-bottle mb-0.5">Platinum Package</h4>
                                        <p class="text-base font-bold text-bottle">Rp 68.000.000</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100 lg:hidden xl:inline-flex">
                                        Aktif <span class="ml-1.5 w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2 mb-3 leading-relaxed">Paket eksklusif dengan pelayanan terbaik dan fasilitas super lengkap.</p>
                                
                                <!-- Fasilitas Badges -->
                                <div class="flex flex-wrap gap-2">
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Dekorasi Exclusive
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Catering 700 pax
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Makeup & Hairdo
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Dokumentasi
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        MC
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Entertainment
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Photo Booth
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Venue
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Souvenir
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex flex-col items-end gap-3 shrink-0">
                                <span class="hidden lg:inline-flex xl:hidden items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">
                                    Aktif <span class="ml-1.5 w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                </span>
                                <div class="flex items-center gap-2 mt-auto">
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition" id="btn-detail-platinum">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </button>
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft hover:border-bottle transition" id="btn-edit-platinum">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:border-red-400 transition" id="btn-hapus-platinum">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ===== PAKET 4: Intimate Package ===== -->
                <div class="paket-card bg-white rounded-2xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.03)] overflow-hidden" id="paket-intimate">
                    <div class="flex flex-col lg:flex-row">
                        <!-- Foto Paket -->
                        <div class="lg:w-56 xl:w-64 shrink-0">
                            <img src="https://images.unsplash.com/photo-1511795409834-ef04bbd61622?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" 
                                 alt="Intimate Package - Dekorasi Pernikahan Intimate" 
                                 class="w-full h-48 lg:h-full object-cover lg:rounded-l-2xl">
                        </div>

                        <!-- Info & Actions -->
                        <div class="flex-1 p-5 lg:p-6 flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                            <!-- Informasi Utama -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between mb-1">
                                    <div>
                                        <h4 class="text-lg font-bold text-bottle mb-0.5">Intimate Package</h4>
                                        <p class="text-base font-bold text-bottle">Rp 18.000.000</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100 lg:hidden xl:inline-flex">
                                        Aktif <span class="ml-1.5 w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-2 mb-3 leading-relaxed">Paket intimate untuk acara akad atau resepsi kecil dan hangat.</p>
                                
                                <!-- Fasilitas Badges -->
                                <div class="flex flex-wrap gap-2">
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Dekorasi Simple
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Catering 100 pax
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        Dokumentasi
                                    </span>
                                    <span class="badge-facility inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100">
                                        <svg class="w-3.5 h-3.5 mr-1.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                        MC
                                    </span>
                                </div>
                            </div>

                            <!-- Tombol Aksi -->
                            <div class="flex flex-col items-end gap-3 shrink-0">
                                <span class="hidden lg:inline-flex xl:hidden items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-600 border border-green-100">
                                    Aktif <span class="ml-1.5 w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                </span>
                                <div class="flex items-center gap-2 mt-auto">
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-gray-300 transition" id="btn-detail-intimate">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Detail
                                    </button>
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft hover:border-bottle transition" id="btn-edit-intimate">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        Edit
                                    </button>
                                    <button class="btn-action inline-flex items-center px-3 py-2 text-xs font-semibold text-red-500 bg-white border border-red-200 rounded-lg hover:bg-red-50 hover:border-red-400 transition" id="btn-hapus-intimate">
                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- End Daftar Paket -->

        </main>
    </div>

    <!-- ========================================== -->
    <!-- SCRIPT: Search Filter                      -->
    <!-- ========================================== -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('input-search-paket');
            const paketCards = document.querySelectorAll('[id^="paket-"]');
            
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.toLowerCase().trim();
                    
                    paketCards.forEach(card => {
                        const cardText = card.textContent.toLowerCase();
                        if (query === '' || cardText.includes(query)) {
                            card.style.display = '';
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        } else {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(-10px)';
                            setTimeout(() => {
                                if (!cardText.includes(searchInput.value.toLowerCase().trim())) {
                                    card.style.display = 'none';
                                }
                            }, 200);
                        }
                    });
                });
            }
        });
    </script>

</body>
</html>

