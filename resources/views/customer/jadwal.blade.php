<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Acara - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Jadwal dan rundown acara pernikahan Client Brilliant Event & Wedding Organizer.">

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
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #E2E8F0; border-radius: 4px; }
        .icon-box {
            display: inline-flex; align-items: center; justify-content: center;
            width: 40px; height: 40px; background-color: #EDFCF0; color: #00A32A;
            border-radius: 50%; flex-shrink: 0;
        }
        .icon-box-sm {
            display: inline-flex; align-items: center; justify-content: center;
            width: 32px; height: 32px; background-color: #EDFCF0; color: #00A32A;
            border-radius: 50%; flex-shrink: 0;
        }
    </style>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden"
      x-data="{ sidebarOpen: false, profileDropdown: false }">

    <!-- ================================================ -->
    <!-- SIDEBAR                                           -->
    <!-- ================================================ -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display:none;"></div>

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
        <!-- Nav -->
        <div class="flex-1 overflow-y-auto custom-scrollbar py-6 px-4 space-y-1.5 flex flex-col justify-between">
            <div class="space-y-1.5">
                <a href="{{ route('client.dashboard') }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Dashboard
                </a>
                <a href="{{ route('client.pesanan') }}" class="flex items-center px-4 py-3.5 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                    <svg class="w-5 h-5 mr-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                    Pemesanan Saya
                </a>
                <!-- Jadwal Acara (AKTIF) -->
                <a href="{{ route('client.jadwal') }}" class="flex items-center px-4 py-3.5 bg-leafSoft text-bottle font-semibold rounded-xl transition">
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
    <!-- MAIN CONTENT                                      -->
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
                <button class="relative text-gray-400 hover:text-bottle transition focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    <span class="absolute -top-1 -right-1 flex items-center justify-center w-5 h-5 bg-bottle rounded-full ring-2 ring-white"><span class="text-[10px] font-bold text-white leading-none">2</span></span>
                </button>
                <div class="w-px h-8 bg-gray-200"></div>
                <div class="relative">
                    <button @click="profileDropdown = !profileDropdown" @click.away="profileDropdown = false" class="flex items-center space-x-3 focus:outline-none">
                        <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2.5&w=256&h=256&q=80" alt="Avatar" class="w-11 h-11 rounded-full object-cover border-2 border-green-50 shadow-sm">
                        <div class="hidden md:block text-left"><p class="text-sm font-bold text-gray-900 leading-tight">Marsya</p></div>
                        <svg class="hidden md:block w-4 h-4 text-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="profileDropdown" style="display:none;" class="absolute right-0 mt-3 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Profil Saya</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Pengaturan Akun</a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- BODY -->
        <main class="flex-1 overflow-y-auto custom-scrollbar p-6 lg:p-8">
            <div class="max-w-4xl mx-auto space-y-6">

                <!-- Page Title -->
                <h3 class="text-xl font-bold text-gray-900">Jadwal Acara</h3>

                <!-- ============================================ -->
                <!-- 1. DATE CARD / HERO                           -->
                <!-- ============================================ -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                        <!-- Photo -->
                        <img src="https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-1.2.1&auto=format&fit=crop&w=400&q=80"
                             alt="Wedding Venue"
                             class="w-full md:w-40 h-36 object-cover rounded-xl shadow-sm shrink-0">
                        <!-- Info -->
                        <div class="flex-1">
                            <div class="inline-flex items-center text-xs font-bold text-bottle mb-2">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                30 Juni 2026
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 leading-tight">Pernikahan Marsya & Axtra</h2>
                            <div class="flex items-center text-sm text-gray-500 font-medium mt-2">
                                <svg class="w-4 h-4 mr-1.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Sabda Alam Hotel Garut
                            </div>
                        </div>
                        <!-- Countdown -->
                        <div class="bg-leafSoft border border-green-100 rounded-xl px-6 py-4 text-center shrink-0 min-w-[140px]">
                            <div class="flex items-center justify-center mb-1">
                                <svg class="w-5 h-5 text-bottle mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-3xl font-bold text-bottle leading-tight">30 Hari</h3>
                            <p class="text-xs font-semibold text-bottle mt-1 flex items-center justify-center">
                                Menuju Acara
                                <svg class="w-4 h-4 ml-1 text-bottle" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- 2. RUNDOWN ACARA                              -->
                <!-- ============================================ -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm" x-data="{ akadOpen: false, resepsiOpen: false }">
                    <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                        <div class="icon-box mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Rundown Acara</h3>
                    </div>

                    <!-- Akad Nikah -->
                    <div class="mb-4">
                        <button @click="akadOpen = !akadOpen" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition group">
                            <div class="flex items-center gap-4">
                                <div class="icon-box-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                </div>
                                <div class="text-left">
                                    <h4 class="text-sm font-bold text-gray-900">Akad Nikah</h4>
                                    <p class="text-xs text-gray-500">Sabda Alam Hotel Garut · Ballroom 1</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-bottle bg-leafSoft px-3 py-1 rounded-lg">08.00 WIB</span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="akadOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>
                        <div x-show="akadOpen" x-collapse class="mt-2 pl-16 pr-4 space-y-3 text-sm text-gray-600">
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">07:00</span> Persiapan makeup & outfit pengantin</div>
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">07:45</span> Kedatangan keluarga inti</div>
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">08:00</span> Prosesi Akad Nikah dimulai</div>
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">08:30</span> Ijab Kabul & doa</div>
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">09:00</span> Sesi foto keluarga</div>
                        </div>
                    </div>

                    <!-- Resepsi -->
                    <div>
                        <button @click="resepsiOpen = !resepsiOpen" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-gray-100 rounded-xl transition group">
                            <div class="flex items-center gap-4">
                                <div class="icon-box-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A2.701 2.701 0 001.5 16M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5h18z"/></svg>
                                </div>
                                <div class="text-left">
                                    <h4 class="text-sm font-bold text-gray-900">Resepsi</h4>
                                    <p class="text-xs text-gray-500">Sabda Alam Hotel Garut · Ballroom Utama</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-bold text-bottle bg-leafSoft px-3 py-1 rounded-lg">11.00 WIB</span>
                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-200" :class="resepsiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </button>
                        <div x-show="resepsiOpen" x-collapse class="mt-2 pl-16 pr-4 space-y-3 text-sm text-gray-600">
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">10:30</span> Persiapan venue & cek dekorasi</div>
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">11:00</span> Penyambutan tamu undangan</div>
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">12:00</span> Makan siang & hidangan catering</div>
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">14:00</span> Penampilan band & hiburan spesial</div>
                            <div class="flex items-center gap-2 py-1"><span class="text-xs font-bold text-gray-400 w-16 shrink-0">16:00</span> Thank you & penutupan acara</div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- 3. TIMELINE ACARA                             -->
                <!-- ============================================ -->
                <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                    <div class="flex items-center mb-8 border-b border-gray-100 pb-4">
                        <div class="icon-box mr-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Timeline Acara</h3>
                    </div>

                    <div class="relative ml-2 space-y-8">
                        <!-- Vertical line -->
                        <div class="absolute top-2 left-[11px] bottom-4 w-0.5 bg-bottle"></div>

                        <!-- 07.00 -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0.5 w-6 h-6 bg-bottle rounded-full flex items-center justify-center ring-4 ring-white z-10">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                            <div class="flex items-start gap-4">
                                <span class="text-sm font-bold text-bottle w-20 shrink-0">07.00 WIB</span>
                                <div class="icon-box-sm shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Persiapan Makeup & Outfit</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Tim makeup mempersiapkan pengantin</p>
                                </div>
                            </div>
                        </div>

                        <!-- 08.00 -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0.5 w-6 h-6 bg-bottle rounded-full flex items-center justify-center ring-4 ring-white z-10">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                            <div class="flex items-start gap-4">
                                <span class="text-sm font-bold text-bottle w-20 shrink-0">08.00 WIB</span>
                                <div class="icon-box-sm shrink-0">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Akad Dimulai</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Prosesi akad nikah</p>
                                </div>
                            </div>
                        </div>

                        <!-- 10.00 -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0.5 w-6 h-6 bg-bottle rounded-full flex items-center justify-center ring-4 ring-white z-10">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                            <div class="flex items-start gap-4">
                                <span class="text-sm font-bold text-bottle w-20 shrink-0">10.00 WIB</span>
                                <div class="icon-box-sm shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Sesi Foto</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Foto bersama keluarga & sesi couple</p>
                                </div>
                            </div>
                        </div>

                        <!-- 11.00 -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0.5 w-6 h-6 bg-bottle rounded-full flex items-center justify-center ring-4 ring-white z-10">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                            <div class="flex items-start gap-4">
                                <span class="text-sm font-bold text-bottle w-20 shrink-0">11.00 WIB</span>
                                <div class="icon-box-sm shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Resepsi Dimulai</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Penyambutan tamu undangan</p>
                                </div>
                            </div>
                        </div>

                        <!-- 14.00 -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0.5 w-6 h-6 bg-bottle rounded-full flex items-center justify-center ring-4 ring-white z-10">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                            <div class="flex items-start gap-4">
                                <span class="text-sm font-bold text-bottle w-20 shrink-0">14.00 WIB</span>
                                <div class="icon-box-sm shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Hiburan & Entertainment</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Penampilan band & hiburan spesial</p>
                                </div>
                            </div>
                        </div>

                        <!-- 16.00 -->
                        <div class="relative pl-10">
                            <div class="absolute -left-[1px] top-0.5 w-6 h-6 bg-bottle rounded-full flex items-center justify-center ring-4 ring-white z-10">
                                <div class="w-2 h-2 bg-white rounded-full"></div>
                            </div>
                            <div class="flex items-start gap-4">
                                <span class="text-sm font-bold text-bottle w-20 shrink-0">16.00 WIB</span>
                                <div class="icon-box-sm shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Acara Selesai</h4>
                                    <p class="text-xs text-gray-500 mt-0.5">Thank you & penutupan acara</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================ -->
                <!-- 4. MEETING & VENDOR GRID                      -->
                <!-- ============================================ -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Meeting Card -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                        <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                            <div class="icon-box mr-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Jadwal Meeting & Persiapan</h3>
                        </div>
                        <div class="space-y-4">
                            <!-- Item 1 -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-1 h-10 bg-bottle rounded-full"></div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Meeting Vendor</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">26 Mei 2026</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-bottle">10.00 WIB</span>
                            </div>
                            <!-- Item 2 -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-1 h-10 bg-bottle rounded-full"></div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Final Check Venue</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">15 Juni 2026</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-bottle">13.00 WIB</span>
                            </div>
                            <!-- Item 3 -->
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <div class="flex items-center gap-3">
                                    <div class="w-1 h-10 bg-bottle rounded-full"></div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Gladi Bersih</h4>
                                        <p class="text-xs text-gray-500 mt-0.5">29 Juni 2026</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-bottle">15.00 WIB</span>
                            </div>
                        </div>
                    </div>

                    <!-- Vendor List -->
                    <div class="bg-white rounded-2xl border border-gray-100 p-6 lg:p-8 shadow-sm">
                        <div class="flex items-center mb-6 border-b border-gray-100 pb-4">
                            <div class="icon-box mr-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Vendor yang Bertugas</h3>
                        </div>
                        <div class="space-y-4">
                            <!-- Vendor Row -->
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="icon-box-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Makeup Artist</h4>
                                        <p class="text-xs text-gray-500">Tim Brilliant Makeup</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-bottle">05.00 WIB</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="icon-box-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0A2.701 2.701 0 001.5 16M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-5a2 2 0 00-2-2H5a2 2 0 00-2 2v5h18z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Catering</h4>
                                        <p class="text-xs text-gray-500">Brilliant Catering Team</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-bottle">07.00 WIB</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="icon-box-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2-1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Dekorasi</h4>
                                        <p class="text-xs text-gray-500">Brilliant Decoration</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-bottle">06.00 WIB</span>
                            </div>
                            <div class="flex items-center justify-between py-2 border-b border-gray-50">
                                <div class="flex items-center gap-3">
                                    <div class="icon-box-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">Dokumentasi</h4>
                                        <p class="text-xs text-gray-500">Brilliant Photography</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-bottle">07.00 WIB</span>
                            </div>
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center gap-3">
                                    <div class="icon-box-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-900">MC & Entertainment</h4>
                                        <p class="text-xs text-gray-500">Brilliant Entertainment</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-bottle">10.00 WIB</span>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ============================================ -->
                <!-- 5. FOOTER MOTIVASI                            -->
                <!-- ============================================ -->
                <div class="bg-white rounded-2xl border border-gray-100 px-6 lg:px-8 py-5 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="icon-box shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">Semua persiapan kami yang terbaik untuk hari bahagiamu!</h4>
                            <p class="text-xs text-gray-500 mt-0.5">Tim Brilliant akan memastikan semua berjalan lancar sesuai rencana.</p>
                        </div>
                    </div>
                    <svg class="w-6 h-6 text-bottle shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
