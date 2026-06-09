<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jadwal Acara - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Halaman manajemen jadwal acara pernikahan Brilliant Event & Wedding Organizer.">

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
        /* Smooth row hover */
        .jadwal-row {
            transition: background-color 0.15s ease;
        }
        /* Action button micro-animation */
        .btn-action {
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-action:hover {
            transform: translateY(-1.5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
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
            box-shadow: 0 8px 28px rgba(0,0,0,0.06);
        }
        /* Custom scrollbar for sidebar */
        aside::-webkit-scrollbar {
            width: 4px;
        }
        aside::-webkit-scrollbar-thumb {
            background-color: #E2E8F0;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-grayBg font-sans antialiased text-gray-800 flex h-screen overflow-hidden"
      x-data="{ 
          sidebarOpen: false, 
          profileDropdown: false, 
          searchQuery: '', 
          statusFilter: 'Semua Status',
          statusDropdownOpen: false,
          toastShow: false,
          toastMessage: '',
          toastType: 'success',
          
          // Data mockup jadwal acara khusus di area Garut
          events: [
              { code: 'EVT-240501', couple: 'Dinda & Arya', package: 'Gold Package', date: '25 Mei 2024', time: '08:00 - 14:00', location: 'Gedung Pendopo Garut', status: 'Selesai' },
              { code: 'EVT-240502', couple: 'Salsa & Rizky', package: 'Platinum Package', date: '26 Mei 2024', time: '09:00 - 15:00', location: 'Favehotel Cimanuk Garut', status: 'Selesai' },
              { code: 'EVT-240503', couple: 'Nadia & Farhan', package: 'Silver Package', date: '30 Mei 2024', time: '08:00 - 13:00', location: 'Gedung Lasminingrat Garut', status: 'Akan Datang' },
              { code: 'EVT-240504', couple: 'Putri & Bagas', package: 'Luxury Package', date: '1 Juni 2024', time: '10:00 - 16:00', location: 'Harmoni Hotel Garut', status: 'Akan Datang' },
              { code: 'EVT-240505', couple: 'Anisa & Reza', package: 'Gold Package', date: '2 Juni 2024', time: '08:00 - 14:00', location: 'Sumber Alam Resort Garut', status: 'Akan Datang' },
              { code: 'EVT-240506', couple: 'Vina & Andi', package: 'Silver Package', date: '8 Juni 2024', time: '09:00 - 14:00', location: 'Gedung Islamic Center Garut', status: 'Akan Datang' },
              { code: 'EVT-240507', couple: 'Rani & Dika', package: 'Platinum Package', date: '9 Juni 2024', time: '10:00 - 16:00', location: 'Kampung Sampireun Resort', status: 'Akan Datang' },
              { code: 'EVT-240508', couple: 'Maya & Fikri', package: 'Gold Package', date: '15 Juni 2024', time: '08:00 - 14:00', location: 'Gedung Pendopo Garut', status: 'Dibatalkan' },
              { code: 'EVT-240509', couple: 'Chelsea & Kevin', package: 'Luxury Package', date: '16 Juni 2024', time: '09:00 - 15:00', location: 'Harmoni Hotel Garut', status: 'Akan Datang' },
              { code: 'EVT-240510', couple: 'Ayu & Ilham', package: 'Silver Package', date: '20 Juni 2024', time: '08:00 - 13:00', location: 'Favehotel Cimanuk Garut', status: 'Akan Datang' }
          ],

          // Hitung metrik dinamis berdasarkan array demo
          get counts() {
              let total = 45; // Metrik static mockup awal
              let thisMonth = 12;
              let upcoming = 28;
              let completed = 5;
              
              return { total, thisMonth, upcoming, completed };
          },

          cancelEvent(code) {
              let item = this.events.find(e => e.code === code);
              if (item) {
                  item.status = 'Dibatalkan';
                  this.showToast('Jadwal acara ' + code + ' telah dibatalkan.', 'danger');
              }
          },

          showToast(message, type = 'success') {
              this.toastMessage = message;
              this.toastType = type;
              this.toastShow = true;
              setTimeout(() => { this.toastShow = false; }, 3000);
          },

          get filteredEvents() {
              return this.events.filter(e => {
                  const matchSearch = e.couple.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                      e.code.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                      e.location.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                      e.package.toLowerCase().includes(this.searchQuery.toLowerCase());
                  const matchStatus = this.statusFilter === 'Semua Status' || e.status === this.statusFilter;
                  return matchSearch && matchStatus;
              });
          }
      }">

    <!-- Toast Notification -->
    <div x-show="toastShow" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2 md:translate-y-0 md:translate-x-4"
         x-transition:enter-end="opacity-100 translate-y-0 md:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-5 right-5 z-[9999] max-w-sm w-full bg-white rounded-2xl shadow-xl border border-gray-100 p-4 flex items-center space-x-3"
         style="display: none;">
        <div :class="toastType === 'success' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600'" class="p-2 rounded-xl shrink-0">
            <template x-if="toastType === 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </template>
            <template x-if="toastType !== 'success'">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </template>
        </div>
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-900" x-text="toastType === 'success' ? 'Berhasil' : 'Tindakan'"></p>
            <p class="text-xs text-gray-500 mt-0.5" x-text="toastMessage"></p>
        </div>
    </div>

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
                <!-- SVG Logo Event Organizer -->
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
            
            <!-- Vendor -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Vendor
            </a>
            
            <!-- Jadwal Acara — AKTIF -->
            <a href="#" id="nav-jadwal" class="flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl transition">
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
            <!-- Left Header -->
            <div class="flex items-center">
                <!-- Hamburger Menu Button (mobile only) -->
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-bottle focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 leading-tight">Hi, Admin</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola dan pantau jadwal acara event & wedding klien Anda.</p>
                </div>
            </div>

            <!-- Right Header -->
            <div class="flex items-center space-x-6">
                <!-- Notification Dropdown -->
                <x-notification-bell />

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

                    <!-- Dropdown Content -->
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

        <!-- MAIN LAYOUT BODY -->
        <main class="flex-1 overflow-y-auto p-6 lg:p-8">

            <!-- ============================================ -->
            <!-- 3. METRIK RINGKASAN JADWAL (4 Info Cards)     -->
            <!-- ============================================ -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">

                <!-- Card 1: Total Acara -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)] flex items-start space-x-4">
                    <div class="bg-blue-50 p-3.5 rounded-2.5xl text-blue-600 shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Acara (Tahun Ini)</p>
                        <h3 class="text-3xl font-bold text-gray-900 leading-tight mb-2" x-text="counts.total">45</h3>
                        <p class="text-xs font-semibold text-green-600 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            15% <span class="text-gray-400 ml-1 font-normal">dari tahun lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Card 2: Acara Bulan Ini -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)] flex items-start space-x-4">
                    <div class="bg-green-50 p-3.5 rounded-2.5xl text-bottle shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Acara Bulan Ini</p>
                        <h3 class="text-3xl font-bold text-gray-900 leading-tight mb-2" x-text="counts.thisMonth">12</h3>
                        <p class="text-xs font-semibold text-gray-500 flex items-center">
                            <span class="text-gray-400 font-normal">Jadwal aktif Mei 2024</span>
                        </p>
                    </div>
                </div>

                <!-- Card 3: Akan Datang -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)] flex items-start space-x-4">
                    <div class="bg-orange-50 p-3.5 rounded-2.5xl text-orange-500 shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Akan Datang</p>
                        <h3 class="text-3xl font-bold text-gray-900 leading-tight mb-2" x-text="counts.upcoming">28</h3>
                        <p class="text-xs font-semibold text-gray-500 flex items-center">
                            <span class="text-gray-400 font-normal">Terkonfirmasi & terjadwal</span>
                        </p>
                    </div>
                </div>

                <!-- Card 4: Selesai -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)] flex items-start space-x-4">
                    <div class="bg-green-50 p-3.5 rounded-2.5xl text-green-600 shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Selesai</p>
                        <h3 class="text-3xl font-bold text-gray-900 leading-tight mb-2" x-text="counts.completed">5</h3>
                        <p class="text-xs font-semibold text-green-600 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            100% <span class="text-gray-400 ml-1 font-normal">sukses bulan ini</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 4. FILTER & KONTROL BAR                           -->
            <!-- ============================================ -->
            <div class="flex flex-col xl:flex-row items-stretch xl:items-center justify-between mb-6 gap-4">
                
                <!-- Left Filters Group -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1 max-w-4xl">
                    <!-- Dropdown Status -->
                    <div class="relative w-full sm:w-52">
                        <button @click="statusDropdownOpen = !statusDropdownOpen"
                                @click.away="statusDropdownOpen = false"
                                class="w-full inline-flex items-center justify-between px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-all duration-200">
                            <span x-text="statusFilter">Semua Status</span>
                            <svg class="w-4 h-4 text-gray-400 ml-2 transition-transform duration-200" :class="statusDropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Options -->
                        <div x-show="statusDropdownOpen" 
                             style="display: none;"
                             class="absolute left-0 mt-2 w-full bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-40">
                            <button @click="statusFilter = 'Semua Status'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Semua Status</button>
                            <button @click="statusFilter = 'Akan Datang'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Akan Datang</button>
                            <button @click="statusFilter = 'Sedang Berlangsung'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Sedang Berlangsung</button>
                            <button @click="statusFilter = 'Selesai'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Selesai</button>
                            <button @click="statusFilter = 'Dibatalkan'; statusDropdownOpen = false" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-bottle">Dibatalkan</button>
                        </div>
                    </div>

                    <!-- Search Bar -->
                    <div class="relative flex-1">
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Cari nama pasangan / kode acara / lokasi gedung..."
                               class="search-input w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-bottle transition-all duration-200">
                        <svg class="absolute right-3.5 top-3 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Right Actions Group -->
                <div class="flex items-center gap-3 shrink-0">
                    <!-- Add Event Button -->
                    <button @click="showToast('Membuka form tambah jadwal acara...', 'success')"
                            class="inline-flex items-center px-5 py-2.5 bg-bottle text-white border border-transparent rounded-xl text-sm font-semibold hover:bg-bottleHover transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Tambah Jadwal
                    </button>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 5. TABEL DAFTAR DATA JADWAL (Main Content)   -->
            <!-- ============================================ -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.015)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-100">
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider w-36">Kode Acara</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Klien / Pasangan</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Jadwal (Waktu)</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Lokasi Gedung (Garut)</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center w-64">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            
                            <!-- 
                                BAGIAN DINAMIS UNTUK LARAVEL INTEGRASI DATABASE:
                                @foreach($events as $event)
                                    ...
                                @endforeach
                            -->

                            <!-- Baris Tabel Menggunakan Loop Alpine.js untuk Kemudahan Interaktivitas Demo -->
                            <template x-for="event in filteredEvents" :key="event.code">
                                <tr class="jadwal-row hover:bg-gray-50/50 transition-colors">
                                    
                                    <!-- Kode Acara -->
                                    <td class="px-6 py-4 font-medium text-gray-500 tracking-wide" x-text="event.code"></td>
                                    
                                    <!-- Nama Pasangan & Paket -->
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-900 block" x-text="event.couple"></span>
                                        <span class="text-xs text-gray-500 mt-0.5 block" x-text="event.package"></span>
                                    </td>
                                    
                                    <!-- Tanggal & Waktu -->
                                    <td class="px-6 py-4">
                                        <span class="text-gray-900 font-medium block" x-text="event.date"></span>
                                        <span class="text-xs text-gray-500 flex items-center mt-1">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            <span x-text="event.time"></span>
                                        </span>
                                    </td>
                                    
                                    <!-- Lokasi (Fokus Garut) -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 text-gray-400 mt-0.5 mr-1.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span class="text-gray-700 leading-tight" x-text="event.location"></span>
                                        </div>
                                    </td>
                                    
                                    <!-- Status Badge -->
                                    <td class="px-6 py-4">
                                        <!-- Logika Status Badge Sesuai Persyaratan -->
                                        <span :class="{
                                            'bg-green-50 text-green-600': event.status === 'Selesai',
                                            'bg-orange-50 text-orange-600': event.status === 'Akan Datang',
                                            'bg-red-50 text-red-600': event.status === 'Dibatalkan',
                                            'bg-blue-50 text-blue-600': event.status === 'Sedang Berlangsung'
                                        }" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide border"
                                            :class="{
                                                'border-green-100': event.status === 'Selesai',
                                                'border-orange-100': event.status === 'Akan Datang',
                                                'border-red-100': event.status === 'Dibatalkan',
                                                'border-blue-100': event.status === 'Sedang Berlangsung'
                                            }">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="{
                                                'bg-green-500': event.status === 'Selesai',
                                                'bg-orange-500': event.status === 'Akan Datang',
                                                'bg-red-500': event.status === 'Dibatalkan',
                                                'bg-blue-500': event.status === 'Sedang Berlangsung'
                                            }"></span>
                                            <span x-text="event.status"></span>
                                        </span>
                                    </td>
                                    
                                    <!-- Aksi Tombol -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <!-- Tombol Detail -->
                                            <button @click="showToast('Melihat detail acara ' + event.code, 'success')"
                                                    class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none">
                                                Detail
                                            </button>

                                            <!-- Tombol Edit -->
                                            <button @click="showToast('Membuka form edit untuk ' + event.code, 'success')"
                                                    class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-bottle bg-white border border-green-200 rounded-lg hover:bg-leafSoft focus:outline-none">
                                                Edit
                                            </button>

                                            <!-- Tombol Batalkan -->
                                            <button @click="cancelEvent(event.code)"
                                                    :disabled="event.status === 'Dibatalkan' || event.status === 'Selesai'"
                                                    :class="(event.status === 'Dibatalkan' || event.status === 'Selesai') ? 'opacity-40 cursor-not-allowed' : 'hover:bg-red-50'"
                                                    class="btn-action inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-red-500 bg-white border border-red-200 rounded-lg focus:outline-none">
                                                Batal
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <!-- State ketika data pencarian kosong -->
                            <tr x-show="filteredEvents.length === 0" style="display: none;">
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <p class="text-sm font-semibold text-gray-700">Tidak ada jadwal acara ditemukan</p>
                                    <p class="text-xs text-gray-400 mt-1">Coba gunakan kata kunci pencarian lain atau ubah filter status.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Footer Pagination Mockup -->
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700" x-text="Math.min(filteredEvents.length, 10)"></span> dari <span class="font-semibold text-gray-700" x-text="events.length"></span> data jadwal acara (Aktif filter)
                    </p>
                    
                    <!-- Pagination Buttons -->
                    <div class="flex items-center space-x-1">
                        <button class="page-btn p-2 text-gray-400 hover:text-bottle hover:bg-leafSoft rounded-lg transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <button class="page-btn px-3 py-1.5 text-xs font-semibold bg-bottle text-white rounded-lg">1</button>
                        <button class="page-btn px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-bottle hover:bg-leafSoft rounded-lg transition-all duration-200">2</button>
                        <button class="page-btn px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-bottle hover:bg-leafSoft rounded-lg transition-all duration-200">3</button>
                        <button class="page-btn p-2 text-gray-400 hover:text-bottle hover:bg-leafSoft rounded-lg transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

        </main>
    </div>

</body>
</html>

