<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #INV-250524-001 - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Invoice resmi pembayaran Brilliant Event & Wedding Organizer.">

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

        /* Print Media Styles */
        @media print {
            /* Hide non-printable elements */
            aside, header, #back-to-order, #btn-download-pdf {
                display: none !important;
            }

            /* Reset page wrappers for standard flow */
            html, body {
                height: auto !important;
                overflow: visible !important;
                background-color: #ffffff !important;
                color: #000000 !important;
                font-size: 11pt !important;
            }

            .flex, .flex-col, .flex-1, main, .overflow-hidden, .overflow-y-auto {
                display: block !important;
                height: auto !important;
                overflow: visible !important;
                position: static !important;
            }

            main {
                padding: 0 !important;
                margin: 0 !important;
            }

            .max-w-4xl {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Invoice sheet styles */
            #invoice-paper {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                background: transparent !important;
            }

            /* Force background colors and colors to print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .grid {
                display: grid !important;
            }

            /* Specific page setup */
            @page {
                size: A4;
                margin: 15mm;
            }
        }
    </style>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden" 
      x-data="{ 
          sidebarOpen: false, 
          profileDropdown: false
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
                <x-notification-bell />
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

                <!-- 1. Header & Aksi (Kembali & Download) -->
                <div class="flex flex-col space-y-4 md:space-y-0 md:flex-row md:items-end md:justify-between mb-2">
                    <div class="space-y-3">
                        <a href="{{ route('client.pesanan_detail', 1) }}" class="inline-flex items-center text-sm font-bold text-gray-600 hover:text-bottle transition" id="back-to-order">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            Kembali ke Detail Pesanan
                        </a>
                        <div>
                            <h2 class="text-3xl font-bold text-gray-900 leading-tight">Invoice</h2>
                            <p class="text-sm font-bold text-gray-500 mt-1">#INV-250524-001</p>
                        </div>
                    </div>
                    
                    <button onclick="window.print()" id="btn-download-pdf" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-bottle text-bottle hover:bg-bottle hover:text-white transition duration-200 text-sm font-bold rounded-lg shadow-sm focus:outline-none">
                        <span class="mr-2">📥</span>
                        Download PDF
                    </button>
                </div>

                <!-- 2. Invoice Document Sheet Card -->
                <div id="invoice-paper" class="bg-white rounded-2xl border border-gray-100 p-8 lg:p-12 shadow-sm space-y-10">
                    
                    <!-- Section Header Invoice: Logo & Detail Perusahaan -->
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-6 border-b border-gray-100 pb-8">
                        <!-- Logo Brilliant WO -->
                        <div class="flex items-center space-x-3">
                            <svg class="w-11 h-11 text-bottle shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.477 2 2 6.477 2 12c0 2.125.666 4.095 1.791 5.709l-.498.498a1 1 0 001.414 1.414l.498-.498A9.957 9.957 0 0012 22c5.523 2 10-2.477 10-10S17.523 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8z"/>
                                <path d="M12 22C6.47715 22 2 17.5228 2 12C2 10.8954 2.1791 9.83226 2.50652 8.83582C3.12535 12.8378 6.5828 15.9372 10.7766 16.0827C10.9234 16.0878 11.0706 16.0905 11.2183 16.0905C11.5173 16.0905 11.8133 16.0818 12.1056 16.065C16.3262 15.823 19.7891 12.4411 20.218 8.16335C20.4851 7.89436 20.733 7.6083 20.9599 7.3065C21.6366 8.74233 22 10.3278 22 12C22 17.5228 17.5228 22 12 22Z" fill="#00A32A"/>
                            </svg>
                            <div class="leading-tight">
                                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Brilliant</h1>
                                <p class="text-[0.5rem] text-gray-500 font-semibold tracking-widest uppercase">Event & Wedding Organizer</p>
                            </div>
                        </div>
                        
                        <!-- Detail Perusahaan -->
                        <div class="text-left md:text-right text-xs text-gray-500 font-medium leading-relaxed">
                            <h4 class="text-sm font-bold text-gray-900 mb-1">Brilliant Event & Wedding Organizer</h4>
                            <p>Jl. Sunset Road No. 88, Kuta</p>
                            <p>Badung, Bali 80361</p>
                            <p class="mt-1">Phone: (0361) 1234 567</p>
                            <p>Email: hello@brilliant-wedding.com</p>
                        </div>
                    </div>

                    <!-- Box Detail Perusahaan & Pemesan (Grid 3 Kolom) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Kolom Kiri: Informasi Pemesan -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4 pb-1 border-b border-gray-50">Informasi Pemesan</h4>
                            <div class="space-y-3 text-sm font-semibold text-gray-700">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <span>Marsya</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    <span>0812 3456 7890</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <span class="break-all font-medium text-gray-600">marsya@email.com</span>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Tengah: Informasi Acara -->
                        <div>
                            <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-4 pb-1 border-b border-gray-50">Informasi Acara</h4>
                            <div class="space-y-2.5 text-xs">
                                <div class="grid grid-cols-3 gap-2">
                                    <span class="text-gray-500 font-semibold">Paket</span>
                                    <span class="col-span-2 text-gray-800 font-bold">: Paket Gold</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <span class="text-gray-500 font-semibold">Acara</span>
                                    <span class="col-span-2 text-gray-800 font-bold">: Pernikahan Marsya & Axtra</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <span class="text-gray-500 font-semibold">Tanggal</span>
                                    <span class="col-span-2 text-gray-800 font-bold">: 30 Juni 2026</span>
                                </div>
                                <div class="grid grid-cols-3 gap-2">
                                    <span class="text-gray-500 font-semibold">Lokasi</span>
                                    <span class="col-span-2 text-gray-800 font-bold">: The Alana Hotel & Convention Center Garut</span>
                                </div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Sidebar Status (Box Terpisah) -->
                        <div>
                            <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Status Pembayaran</span>
                                <div class="inline-flex items-center px-2.5 py-1 bg-green-50 text-bottle text-xs font-bold rounded-full mt-2">
                                    <span class="w-1.5 h-1.5 bg-bottle rounded-full mr-1.5"></span>
                                    DP Lunas
                                </div>
                                
                                <div class="mt-4">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Tanggal Invoice</span>
                                    <span class="text-sm font-bold text-gray-900 mt-1 block">24 Mei 2026</span>
                                </div>
                                
                                <div class="mt-4">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Jatuh Tempo Pelunasan</span>
                                    <span class="text-sm font-bold text-gray-900 mt-1 block">24 Mei 2026</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Rincian Layanan (Main Content) -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wider pb-1 border-b border-gray-50">Rincian Layanan</h4>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-700">
                                <thead class="text-xs text-gray-500 uppercase tracking-wider border-b border-gray-200">
                                    <tr>
                                        <th scope="col" class="py-3 font-bold w-12 text-center">No</th>
                                        <th scope="col" class="py-3 font-bold px-4">Layanan</th>
                                        <th scope="col" class="py-3 font-bold text-right w-48">Harga</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 font-medium">
                                    <tr>
                                        <td class="py-4 text-center text-gray-400">1</td>
                                        <td class="py-4 px-4 text-gray-900">Dekorasi</td>
                                        <td class="py-4 text-right text-gray-900">Rp 12.000.000</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 text-center text-gray-400">2</td>
                                        <td class="py-4 px-4 text-gray-900">Catering</td>
                                        <td class="py-4 text-right text-gray-900">Rp 15.000.000</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 text-center text-gray-400">3</td>
                                        <td class="py-4 px-4 text-gray-900">Dokumentasi</td>
                                        <td class="py-4 text-right text-gray-900">Rp 5.000.000</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 text-center text-gray-400">4</td>
                                        <td class="py-4 px-4 text-gray-900">Makeup</td>
                                        <td class="py-4 text-right text-gray-900">Rp 2.500.000</td>
                                    </tr>
                                    <tr>
                                        <td class="py-4 text-center text-gray-400">5</td>
                                        <td class="py-4 px-4 text-gray-900">MC</td>
                                        <td class="py-4 text-right text-gray-900">Rp 1.500.000</td>
                                    </tr>
                                    <tr class="border-t border-gray-100/50">
                                        <td class="py-3 text-center"></td>
                                        <td class="py-3 px-4 text-xs font-semibold text-gray-400 italic" colspan="2">+ 5 Layanan Lainnya</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Section Ringkasan Pembayaran (Grid 2 Kolom) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                        <!-- Kolom Kiri: Tabel rincian total -->
                        <div class="space-y-3.5 pr-0 md:pr-8">
                            <div class="flex justify-between items-center text-sm font-semibold text-gray-500 py-1">
                                <span>Total Biaya</span>
                                <span class="text-gray-900 font-bold">Rp 35.000.000</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-semibold text-gray-500 py-1">
                                <span>DP (30%)</span>
                                <span class="text-gray-900 font-bold">Rp 10.500.000</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-semibold text-gray-500 py-1">
                                <span>Sisa Pembayaran</span>
                                <span class="text-gray-900 font-bold">Rp 24.500.000</span>
                            </div>
                            <div class="flex justify-between items-center text-base font-bold text-bottle pt-3.5 border-t border-gray-100">
                                <span>Total Dibayar</span>
                                <span class="text-lg font-extrabold">Rp 10.500.000</span>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Box informasi Metode Pembayaran -->
                        <div class="bg-gray-50 border border-gray-100 rounded-xl p-5">
                            <h5 class="text-xs font-bold text-gray-900 uppercase tracking-wider mb-2.5">Metode Pembayaran</h5>
                            <p class="text-xs font-semibold text-gray-400">Transfer Bank</p>
                            <div class="mt-2.5 text-sm text-gray-800 font-bold space-y-1">
                                <p>Bank BCA</p>
                                <p class="text-base tracking-wide text-gray-900">1234 5678 9101</p>
                                <p class="text-xs font-medium text-gray-500">a.n. Brilliant Event Organizer</p>
                            </div>
                        </div>
                    </div>

                    <!-- Catatan Footer: Box Notifikasi Hijau Muda Sangat Halus -->
                    <div class="bg-[#EDFCF0] border border-green-100/50 rounded-xl p-4 flex items-start space-x-3.5 mt-8">
                        <svg class="w-6 h-6 text-bottle shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <div class="text-sm font-medium text-bottle leading-relaxed">
                            <strong>Terima kasih!</strong> Pembayaran DP Anda telah kami terima. Pelunasan dapat dilakukan sebelum tanggal jatuh tempo.
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

</body>
</html>

