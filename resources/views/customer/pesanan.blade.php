<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemesanan Saya - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Halaman daftar riwayat pemesanan Client Brilliant Event & Wedding Organizer.">

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
        .btn-action {
            transition: all 0.2s ease;
        }
        .btn-action:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
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
                <!-- Dashboard -->
                <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Dashboard
                </a>
                
                <!-- Pemesanan Saya (AKTIF) -->
                <a href="{{ route('client.pesanan') }}" class="flex items-center px-4 py-3.5 bg-leafSoft text-bottle font-semibold rounded-xl transition">
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
        <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 h-24 px-8 flex items-center justify-between shrink-0 sticky top-0 z-30">
            <!-- Left Header -->
            <div class="flex items-center">
                <!-- Hamburger Menu Button (mobile only) -->
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-bottle focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">Hi, Marsya</h2>
                </div>
            </div>

            <!-- Right Header -->
            <div class="flex items-center space-x-6">
                <!-- Notification Dropdown -->
                @include('components.notification-dropdown')

                <div class="w-px h-8 bg-gray-200"></div>

                <!-- Profile Dropdown -->
                <div class="relative">
                    <button @click="profileDropdown = !profileDropdown"
                            @click.away="profileDropdown = false"
                            class="flex items-center space-x-3 focus:outline-none">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2.5&w=256&h=256&q=80"
                             alt="Customer Avatar"
                             class="w-11 h-11 rounded-full object-cover border-2 border-green-50 shadow-sm">
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-bold text-gray-900 leading-tight">Marsya</p>
                        </div>
                        <svg class="hidden md:block w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <!-- Dropdown Content -->
                    <div x-show="profileDropdown" style="display: none;"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
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

            <!-- Filter Row -->
            <div class="flex justify-end mb-6">
                <!-- Dropdown Status -->
                <div class="relative w-48">
                    <button @click="statusDropdownOpen = !statusDropdownOpen"
                            @click.away="statusDropdownOpen = false"
                            class="w-full inline-flex items-center justify-between px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all duration-200 shadow-sm">
                        <span x-text="statusFilter">Semua Status</span>
                        <svg class="w-4 h-4 text-gray-400 ml-2 transition-transform duration-200" :class="statusDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    
                    <!-- Dropdown Options -->
                    <div x-show="statusDropdownOpen" 
                         style="display: none;"
                         class="absolute right-0 mt-2 w-full bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-40">
                        <button @click="statusFilter = 'Semua Status'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Semua Status</button>
                        <button @click="statusFilter = 'Sedang Berlangsung'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Sedang Berlangsung</button>
                        <button @click="statusFilter = 'Selesai'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Selesai</button>
                        <button @click="statusFilter = 'Dibatalkan'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Dibatalkan</button>
                    </div>
                </div>
            </div>

            <!-- List Pesanan (Card Utama) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                
                <!-- Header Kolom (Tabel Semu) -->
                <div class="hidden lg:grid grid-cols-12 gap-4 px-8 py-4 border-b border-gray-100 bg-white">
                    <div class="col-span-4 text-xs font-bold text-gray-800 tracking-wide"></div> <!-- Spacer untuk info kiri -->
                    <div class="col-span-2 text-xs font-bold text-gray-800 tracking-wide">Paket / Layanan</div>
                    <div class="col-span-2 text-xs font-bold text-gray-800 tracking-wide">Tanggal Acara</div>
                    <div class="col-span-2 text-xs font-bold text-gray-800 tracking-wide">Total</div>
                    <div class="col-span-1 text-xs font-bold text-gray-800 tracking-wide">Status</div>
                    <div class="col-span-1 text-xs font-bold text-gray-800 tracking-wide text-center">Aksi</div>
                </div>

                <!-- Baris Pesanan 1 (Sesuai Mockup) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 p-6 lg:px-8 lg:py-6 border-b border-gray-50 items-center hover:bg-gray-50/50 transition duration-150">
                    
                    <!-- Info Kiri (Gambar & Detail Paket) -->
                    <div class="col-span-1 lg:col-span-4 flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-1.2.1&auto=format&fit=crop&w=300&q=80" 
                             alt="Paket Gold" 
                             class="w-32 h-32 object-cover rounded-xl shadow-sm shrink-0">
                        <div class="flex flex-col min-w-0">
                            <h3 class="text-base font-bold text-gray-900 leading-tight">Paket Gold</h3>
                            <p class="text-sm font-semibold text-gray-800 mt-1 truncate">Pernikahan Marsya & Axtra</p>
                            <p class="text-xs text-gray-500 mt-1 truncate">The Alana Hotel & Convention Center Garut</p>
                            <p class="text-[11px] text-gray-400 font-medium mt-2">ID Pesanan: #BR-250524-001</p>
                        </div>
                    </div>

                    <!-- Paket / Layanan -->
                    <div class="col-span-1 lg:col-span-2 flex flex-col justify-center">
                        <div class="lg:hidden text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Paket / Layanan</div>
                        <ul class="text-sm text-gray-700 font-medium space-y-1.5 list-disc pl-4 marker:text-gray-300">
                            <li>Dekorasi</li>
                            <li>Catering</li>
                            <li>Dokumentasi</li>
                        </ul>
                        <a href="#" class="text-[11px] font-semibold text-bottle mt-2 hover:underline">+ 5 Layanan Lainnya</a>
                    </div>

                    <!-- Tanggal Acara -->
                    <div class="col-span-1 lg:col-span-2 flex flex-col justify-center">
                        <div class="lg:hidden text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Tanggal Acara</div>
                        <p class="text-sm font-bold text-gray-900">30 Juni 2026</p>
                        <p class="text-xs font-medium text-gray-500 mt-1">08.00 WIB</p>
                    </div>

                    <!-- Total -->
                    <div class="col-span-1 lg:col-span-2 flex flex-col justify-center">
                        <div class="lg:hidden text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Total</div>
                        <p class="text-sm font-bold text-gray-900">Rp 35.000.000</p>
                    </div>

                    <!-- Status -->
                    <div class="col-span-1 lg:col-span-1 flex flex-col justify-center items-start">
                        <div class="lg:hidden text-xs font-bold text-gray-400 uppercase tracking-wide mb-2">Status</div>
                        
                        <div class="flex items-center space-x-1.5">
                            <span class="w-1.5 h-1.5 bg-bottle rounded-full"></span>
                            <span class="text-xs font-bold text-bottle">Sedang Berlangsung</span>
                        </div>
                        <p class="text-xs font-medium text-gray-500 mt-1.5">DP Lunas</p>
                    </div>

                    <!-- Aksi -->
                    <div class="col-span-1 lg:col-span-1 flex items-center justify-start lg:justify-center gap-2 mt-4 lg:mt-0">
                        <a href="{{ route('client.pesanan_detail', 1) }}" class="btn-action px-3 py-1.5 border border-gray-200 text-gray-600 text-xs font-semibold rounded-lg hover:bg-gray-50 bg-white inline-block">
                            Lihat Detail
                        </a>
                        
                        <!-- Dropdown Opsi (...) -->
                        <div class="relative" x-data="{ optionsOpen: false }">
                            <button @click="optionsOpen = !optionsOpen"
                                    @click.away="optionsOpen = false"
                                    class="btn-action w-8 h-8 flex items-center justify-center border border-gray-200 text-gray-500 rounded-full hover:bg-gray-50 hover:text-gray-800 bg-white focus:outline-none">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <circle cx="5" cy="12" r="2"></circle>
                                    <circle cx="12" cy="12" r="2"></circle>
                                    <circle cx="19" cy="12" r="2"></circle>
                                </svg>
                            </button>
                            
                            <div x-show="optionsOpen" 
                                 style="display: none;"
                                 class="absolute right-0 mt-2 w-40 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-40">
                                <a href="{{ route('client.invoice', 1) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Unduh Invoice</a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Kontak Vendor</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tempat untuk iterasi baris pesanan selanjutnya (Jika ada) -->
                <!-- 
                <div class="grid grid-cols-1 lg:grid-cols-12 ..."> ... </div>
                -->

            </div>

        </main>
    </div>

</body>
</html>
