<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Brilliant Event & Wedding Organizer</title>
    
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
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden" x-data="{ sidebarOpen: false, profileDropdown: false }">

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
            <a href="#" class="flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                Dashboard
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                Booking
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
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
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                Chat
            </a>
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                Pembayaran
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
        
        <x-dashboard-header title="Dashboard Admin">
            <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Admin Avatar" class="w-9 h-9 rounded-full object-cover border border-gray-200">
            <div class="hidden md:block text-right min-w-0">
                <p class="text-sm font-semibold text-gray-900 leading-tight">Admin Brilliant</p>
                <p class="text-xs text-gray-500">Administrator</p>
            </div>
        </x-dashboard-header>

        <!-- KONTEN DASHBOARD -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">
            
            <!-- 3. METRIK RINGKASAN STATIS (4 Kolom) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
                <!-- Card 1: Total Booking -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4">
                    <div class="bg-leafSoft p-3.5 rounded-2xl text-bottle shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Total Booking</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">128</h3>
                        <p class="text-xs font-medium text-bottle flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            18% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 2: Booking Hari Ini -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4">
                    <div class="bg-leafSoft p-3.5 rounded-2xl text-bottle shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Booking Hari Ini</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">8</h3>
                        <p class="text-xs font-medium text-bottle flex items-center">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                            14% <span class="text-gray-400 ml-1 font-normal">dari kemarin</span>
                        </p>
                    </div>
                </div>

                <!-- Card 3: Vendor Aktif -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4">
                    <div class="bg-leafSoft p-3.5 rounded-2xl text-bottle shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Vendor Aktif</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">36</h3>
                        <p class="text-xs text-gray-400">Total vendor terdaftar</p>
                    </div>
                </div>

                <!-- Card 4: Paket Aktif -->
                <div class="bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] flex items-start space-x-4">
                    <div class="bg-leafSoft p-3.5 rounded-2xl text-bottle shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-500 mb-1">Paket Aktif</p>
                        <h3 class="text-3xl font-bold text-gray-900 mb-2">12</h3>
                        <p class="text-xs text-gray-400">Total paket tersedia</p>
                    </div>
                </div>
            </div>

            <!-- 4. KONTEN BARIS PERTAMA (Grid 2 Kolom) -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                
                <!-- Kolom Kiri: Booking Terbaru (Span 2) -->
                <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] overflow-hidden">
                    <div class="flex items-center justify-between p-6 border-b border-gray-50">
                        <h3 class="text-lg font-bold text-gray-900">Booking Terbaru</h3>
                        <a href="#" class="text-sm font-semibold text-bottle bg-leafSoft px-4 py-1.5 rounded-lg hover:bg-bottle hover:text-white transition">Lihat Semua</a>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs font-semibold tracking-wide uppercase border-b border-gray-100">
                                    <th class="px-6 py-4">Nama Pasangan</th>
                                    <th class="px-6 py-4">Paket</th>
                                    <th class="px-6 py-4">Tanggal Acara</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50">
                                <!-- Row 1 -->
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Dinda" class="w-8 h-8 rounded-full object-cover">
                                        <span class="font-semibold text-gray-900">Dinda & Arya</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">Gold Package</td>
                                    <td class="px-6 py-4 text-gray-600">25 Mei 2024</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-600">Menunggu</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-gray-400 hover:text-bottle border border-gray-200 bg-white rounded-full p-1 shadow-sm"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg></button>
                                    </td>
                                </tr>
                                <!-- Row 2 -->
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Salsa" class="w-8 h-8 rounded-full object-cover">
                                        <span class="font-semibold text-gray-900">Salsa & Rizky</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">Platinum Package</td>
                                    <td class="px-6 py-4 text-gray-600">26 Mei 2024</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-600">Diproses</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-gray-400 hover:text-bottle border border-gray-200 bg-white rounded-full p-1 shadow-sm"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg></button>
                                    </td>
                                </tr>
                                <!-- Row 3 -->
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Nadia" class="w-8 h-8 rounded-full object-cover">
                                        <span class="font-semibold text-gray-900">Nadia & Farhan</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">Silver Package</td>
                                    <td class="px-6 py-4 text-gray-600">30 Mei 2024</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-600">Menunggu</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-gray-400 hover:text-bottle border border-gray-200 bg-white rounded-full p-1 shadow-sm"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg></button>
                                    </td>
                                </tr>
                                <!-- Row 4 -->
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Putri" class="w-8 h-8 rounded-full object-cover">
                                        <span class="font-semibold text-gray-900">Putri & Bagas</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">Luxury Package</td>
                                    <td class="px-6 py-4 text-gray-600">1 Juni 2024</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-600">Selesai</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-gray-400 hover:text-bottle border border-gray-200 bg-white rounded-full p-1 shadow-sm"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg></button>
                                    </td>
                                </tr>
                                <!-- Row 5 -->
                                <tr class="hover:bg-gray-50/50 transition">
                                    <td class="px-6 py-4 flex items-center space-x-3">
                                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Anisa" class="w-8 h-8 rounded-full object-cover">
                                        <span class="font-semibold text-gray-900">Anisa & Reza</span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">Gold Package</td>
                                    <td class="px-6 py-4 text-gray-600">2 Juni 2024</td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-600">Diproses</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <button class="text-gray-400 hover:text-bottle border border-gray-200 bg-white rounded-full p-1 shadow-sm"><svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z"/></svg></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kolom Kanan: Jadwal Acara Mendatang (Span 1) -->
                <div class="xl:col-span-1 bg-white rounded-3xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Jadwal Acara Mendatang</h3>
                        <a href="#" class="text-sm font-semibold text-bottle bg-leafSoft px-4 py-1.5 rounded-lg hover:bg-bottle hover:text-white transition">Lihat Semua</a>
                    </div>

                    <div class="space-y-5">
                        <!-- Acara 1 -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Decor" class="w-14 h-14 rounded-xl object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Dinda & Arya</h4>
                                    <p class="text-xs text-gray-500 flex items-center mt-0.5">
                                        <svg class="w-3 h-3 text-bottle mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        The Alana Hotel Garut
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">25 Mei 2024 &bull; 10.00 WIB</p>
                                </div>
                            </div>
                            <div class="bg-leafSoft text-bottle px-3 py-2 rounded-xl text-center min-w-[3.5rem]">
                                <span class="block text-lg font-bold leading-none mb-0.5">25</span>
                                <span class="block text-[10px] font-semibold uppercase">Mei</span>
                            </div>
                        </div>

                        <!-- Acara 2 -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <img src="https://images.unsplash.com/photo-1522673607200-164d1b6ce486?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Decor" class="w-14 h-14 rounded-xl object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Salsa & Rizky</h4>
                                    <p class="text-xs text-gray-500 flex items-center mt-0.5">
                                        <svg class="w-3 h-3 text-bottle mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Kampung Sampireun
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">26 Mei 2024 &bull; 11.00 WIB</p>
                                </div>
                            </div>
                            <div class="bg-leafSoft text-bottle px-3 py-2 rounded-xl text-center min-w-[3.5rem]">
                                <span class="block text-lg font-bold leading-none mb-0.5">26</span>
                                <span class="block text-[10px] font-semibold uppercase">Mei</span>
                            </div>
                        </div>

                        <!-- Acara 3 -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <img src="https://images.unsplash.com/photo-1469334031218-e382a71b716b?ixlib=rb-4.0.3&auto=format&fit=crop&w=150&q=80" alt="Decor" class="w-14 h-14 rounded-xl object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Nadia & Farhan</h4>
                                    <p class="text-xs text-gray-500 flex items-center mt-0.5">
                                        <svg class="w-3 h-3 text-bottle mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                        Sabanida Garut
                                    </p>
                                    <p class="text-[10px] text-gray-400 mt-0.5">30 Mei 2024 &bull; 09.00 WIB</p>
                                </div>
                            </div>
                            <div class="bg-leafSoft text-bottle px-3 py-2 rounded-xl text-center min-w-[3.5rem]">
                                <span class="block text-lg font-bold leading-none mb-0.5">30</span>
                                <span class="block text-[10px] font-semibold uppercase">Mei</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- 5. KONTEN BARIS KEDUA (Grid 2 Kolom) -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
                
                <!-- Kolom Kiri: Statistik Booking Chart (Span 2) -->
                <div class="xl:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Statistik Booking</h3>
                        <div class="relative">
                            <select class="appearance-none bg-white border border-gray-200 text-gray-700 py-1.5 pl-4 pr-8 rounded-lg text-sm font-medium focus:outline-none focus:ring-1 focus:ring-bottle cursor-pointer">
                                <option>6 Bulan Terakhir</option>
                                <option>Tahun Ini</option>
                            </select>
                            <svg class="w-4 h-4 text-gray-500 absolute right-3 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    
                    <!-- Area Chart -->
                    <div class="relative h-64 w-full mb-6">
                        <canvas id="bookingChart"></canvas>
                    </div>

                    <!-- Sub Metrics -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-50">
                        <!-- Metrik 1 -->
                        <div class="bg-grayBox rounded-2xl p-4 flex items-start space-x-3">
                            <div class="bg-white p-2 rounded-xl shadow-sm text-bottle shrink-0 border border-gray-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-500 mb-0.5">Total Booking</p>
                                <h4 class="text-lg font-bold text-gray-900 mb-1 leading-none">400</h4>
                                <p class="text-[10px] font-medium text-bottle flex items-center">
                                    <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                    23% <span class="text-gray-400 ml-1 font-normal">dari 6 bln lalu</span>
                                </p>
                            </div>
                        </div>
                        <!-- Metrik 2 -->
                        <div class="bg-grayBox rounded-2xl p-4 flex items-start space-x-3">
                            <div class="bg-white p-2 rounded-xl shadow-sm text-bottle shrink-0 border border-gray-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-500 mb-0.5">Pemasukan</p>
                                <h4 class="text-lg font-bold text-gray-900 mb-1 leading-none">Rp 320 Jt</h4>
                                <p class="text-[10px] font-medium text-bottle flex items-center">
                                    <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                    28% <span class="text-gray-400 ml-1 font-normal">dari 6 bln lalu</span>
                                </p>
                            </div>
                        </div>
                        <!-- Metrik 3 -->
                        <div class="bg-grayBox rounded-2xl p-4 flex items-start space-x-3">
                            <div class="bg-white p-2 rounded-xl shadow-sm text-bottle shrink-0 border border-gray-100">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-semibold text-gray-500 mb-0.5">Rata-rata per Bulan</p>
                                <h4 class="text-lg font-bold text-gray-900 mb-1 leading-none">Rp 53,3 Jt</h4>
                                <p class="text-[10px] font-medium text-bottle flex items-center">
                                    <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                    12% <span class="text-gray-400 ml-1 font-normal">dari 6 bln lalu</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Chat (Span 1) -->
                <div class="xl:col-span-1 bg-white rounded-3xl border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.02)] p-6 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900">Chat</h3>
                        <a href="#" class="text-sm font-semibold text-bottle hover:underline">Lihat Semua</a>
                    </div>

                    <div class="space-y-4 flex-1 overflow-y-auto hide-scrollbar pr-2">
                        <!-- Chat 1 -->
                        <div class="flex items-start justify-between cursor-pointer hover:bg-gray-50 p-2 -mx-2 rounded-xl transition">
                            <div class="flex items-center space-x-3">
                                <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Dinda" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Dinda Ayu</h4>
                                    <p class="text-xs text-gray-500 truncate w-40">Halo admin, saya mau tanya untuk paket gold...</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1">10:24</p>
                                <span class="inline-flex items-center justify-center w-4 h-4 bg-bottle text-white text-[9px] font-bold rounded-full">2</span>
                            </div>
                        </div>

                        <!-- Chat 2 -->
                        <div class="flex items-start justify-between cursor-pointer hover:bg-gray-50 p-2 -mx-2 rounded-xl transition">
                            <div class="flex items-center space-x-3">
                                <img src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Rizky" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Rizky Pratama</h4>
                                    <p class="text-xs text-gray-500 truncate w-40">Terima kasih banyak ya admin 🙏</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1">09:15</p>
                                <span class="inline-flex items-center justify-center w-4 h-4 bg-bottle text-white text-[9px] font-bold rounded-full">1</span>
                            </div>
                        </div>

                        <!-- Chat 3 -->
                        <div class="flex items-start justify-between cursor-pointer hover:bg-gray-50 p-2 -mx-2 rounded-xl transition">
                            <div class="flex items-center space-x-3">
                                <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Nadia" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Nadia Putri</h4>
                                    <p class="text-xs text-gray-500 truncate w-40">Apakah masih tersedia untuk tanggal 30 Mei?</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1">08:45</p>
                                <span class="inline-flex items-center justify-center w-4 h-4 bg-bottle text-white text-[9px] font-bold rounded-full">3</span>
                            </div>
                        </div>

                        <!-- Chat 4 -->
                        <div class="flex items-start justify-between cursor-pointer hover:bg-gray-50 p-2 -mx-2 rounded-xl transition">
                            <div class="flex items-center space-x-3">
                                <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Farhan" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 text-gray-600">Farhan Maulana</h4>
                                    <p class="text-xs text-gray-400 truncate w-40">Baik, saya akan transfer DP hari ini.</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1">Kemarin</p>
                                <!-- No Badge means read -->
                            </div>
                        </div>
                        
                        <!-- Chat 5 -->
                        <div class="flex items-start justify-between cursor-pointer hover:bg-gray-50 p-2 -mx-2 rounded-xl transition">
                            <div class="flex items-center space-x-3">
                                <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="Salsa" class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Salsa Bella</h4>
                                    <p class="text-xs text-gray-500 truncate w-40">Bisa kirimkan detail paket platinum?</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400 mb-1">Kemarin</p>
                                <span class="inline-flex items-center justify-center w-4 h-4 bg-bottle text-white text-[9px] font-bold rounded-full">2</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
        </main>
    </div>

    <!-- Script Chart.js -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('bookingChart').getContext('2d');
            
            // Create Gradient
            let gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(26, 83, 26, 0.2)'); // bottle color with opacity
            gradient.addColorStop(1, 'rgba(26, 83, 26, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei'],
                    datasets: [{
                        label: 'Total Booking',
                        data: [45, 55, 62, 78, 70, 90],
                        borderColor: '#00A32A', // bottle color
                        backgroundColor: gradient,
                        borderWidth: 2,
                        pointBackgroundColor: '#00A32A',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Smooth curve
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: '#00A32A',
                            titleFont: { family: 'Poppins', size: 13 },
                            bodyFont: { family: 'Poppins', size: 12 },
                            padding: 10,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.parsed.y + ' Bookings';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: '#f1f5f9',
                                drawBorder: false,
                            },
                            ticks: {
                                font: { family: 'Poppins', size: 10 },
                                color: '#94a3b8',
                                stepSize: 20
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false,
                            },
                            ticks: {
                                font: { family: 'Poppins', size: 11 },
                                color: '#64748b'
                            }
                        }
                    },
                    interaction: {
                        intersect: false,
                        mode: 'index',
                    },
                }
            });
        });
    </script>
</body>
</html>
