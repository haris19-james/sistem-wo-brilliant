<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan/Obrolan - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Halaman pesan dan komunikasi dengan pasangan/klien Brilliant Event & Wedding Organizer.">

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
        .chat-item-row {
            transition: all 0.15s ease;
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
        /* Message Area Scrollbar */
        .chat-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .chat-scroll::-webkit-scrollbar-thumb {
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
          statusFilter: 'Semua',
          chatRoomOpen: true, // Untuk layout responsif mobile (menampilkan ruang chat / daftar chat)
          activeChatId: 1,
          newMessageText: '',
          
          // Data mockup percakapan lengkap dari gambar mockup
          chats: [
              { 
                  id: 1, 
                  name: 'Dinda & Arya', 
                  code: 'BRI-250524-001', 
                  initials: 'DA', 
                  avatarBg: 'bg-green-100 text-green-700', 
                  online: true, 
                  unread: 2, 
                  lastMsgTime: '10:30',
                  lastMsgText: 'Halo, admin. Kami ingin menanyakan...',
                  messages: [
                      { id: 1, sender: 'client', text: 'Halo, admin. Kami ingin menanyakan beberapa detail terkait paket Gold Package.', time: '10:28' },
                      { id: 2, sender: 'admin', text: 'Halo Dinda & Arya, selamat pagi! 😊\nTentu, silakan informasikan detail yang ingin ditanyakan ya.', time: '10:29', read: true },
                      { id: 3, sender: 'client', text: 'Apa saja yang sudah termasuk dalam paket tersebut ya?', time: '10:29' },
                      { id: 4, sender: 'admin', attachment: { name: 'Detail_Gold_Package.pdf', size: 'PDF • 1.2 MB' }, time: '10:30', read: true },
                      { id: 5, sender: 'client', text: 'Terima kasih admin 🙏\nKami akan diskusikan dulu dan kabari lagi ya.', time: '10:30' },
                      { id: 6, sender: 'admin', text: 'Baik, ditunggu kabar baiknya ya! 😊', time: '10:31', read: true }
                  ]
              },
              { 
                  id: 2, 
                  name: 'Salsa & Rizky', 
                  code: 'BRI-250524-002', 
                  initials: 'SR', 
                  avatarBg: 'bg-blue-100 text-blue-700', 
                  online: false, 
                  unread: 0, 
                  lastMsgTime: '09:15',
                  lastMsgText: 'Baik, terima kasih banyak admin 🙏',
                  messages: [
                      { id: 1, sender: 'client', text: 'Halo admin, untuk fitting baju jadinya di mana ya?', time: '09:00' },
                      { id: 2, sender: 'admin', text: 'Halo Kak Salsa & Rizky, untuk fitting baju akan dilaksanakan di butik pusat ya.', time: '09:10', read: true },
                      { id: 3, sender: 'client', text: 'Baik, terima kasih banyak admin 🙏', time: '09:15' }
                  ]
              },
              { 
                  id: 3, 
                  name: 'Nadia & Farhan', 
                  code: 'BRI-240530-003', 
                  initials: 'NF', 
                  avatarBg: 'bg-yellow-100 text-yellow-700', 
                  online: false, 
                  unread: 1, 
                  lastMsgTime: 'Kemarin',
                  lastMsgText: 'Kapan bisa meeting untuk detailnya?',
                  messages: [
                      { id: 1, sender: 'client', text: 'Kapan bisa meeting untuk detailnya?', time: '14:20' }
                  ]
              },
              { 
                  id: 4, 
                  name: 'Putri & Bagas', 
                  code: 'BRI-240516-004', 
                  initials: 'PB', 
                  avatarBg: 'bg-purple-100 text-purple-700', 
                  online: false, 
                  unread: 0, 
                  lastMsgTime: 'Kemarin',
                  lastMsgText: 'Oke, kami tunggu infonya ya',
                  messages: [
                      { id: 1, sender: 'admin', text: 'Kami akan kirimkan proposal revisi siang ini ya kak.', time: '11:00', read: true },
                      { id: 2, sender: 'client', text: 'Oke, kami tunggu infonya ya', time: '11:15' }
                  ]
              },
              { 
                  id: 5, 
                  name: 'Anisa & Reza', 
                  code: 'BRI-240510-005', 
                  initials: 'AR', 
                  avatarBg: 'bg-pink-100 text-pink-700', 
                  online: false, 
                  unread: 0, 
                  lastMsgTime: '23 Mei',
                  lastMsgText: 'Apakah masih tersedia di tanggal itu?',
                  messages: [
                      { id: 1, sender: 'client', text: 'Apakah masih tersedia di tanggal itu?', time: '16:00' }
                  ]
              },
              { 
                  id: 6, 
                  name: 'Vina & Andi', 
                  code: 'BRI-240508-006', 
                  initials: 'VA', 
                  avatarBg: 'bg-orange-100 text-orange-700', 
                  online: false, 
                  unread: 0, 
                  lastMsgTime: '22 Mei',
                  lastMsgText: 'Terima kasih admin',
                  messages: [
                      { id: 1, sender: 'client', text: 'Terima kasih admin', time: '10:45' }
                  ]
              },
              { 
                  id: 7, 
                  name: 'Rani & Dika', 
                  code: 'BRI-240507-007', 
                  initials: 'RD', 
                  avatarBg: 'bg-indigo-100 text-indigo-700', 
                  online: false, 
                  unread: 0, 
                  lastMsgTime: '22 Mei',
                  lastMsgText: 'Baik, nanti kami transfer DP-nya',
                  messages: [
                      { id: 1, sender: 'client', text: 'Baik, nanti kami transfer DP-nya', time: '13:00' }
                  ]
              },
              { 
                  id: 8, 
                  name: 'Maya & Fikri', 
                  code: 'BRI-240505-008', 
                  initials: 'MF', 
                  avatarBg: 'bg-teal-100 text-teal-700', 
                  online: false, 
                  unread: 0, 
                  lastMsgTime: '21 Mei',
                  lastMsgText: 'Bisa dikirimkan rundown acaranya?',
                  messages: [
                      { id: 1, sender: 'client', text: 'Bisa dikirimkan rundown acaranya?', time: '11:30' }
                  ]
              }
          ],

          get filteredChats() {
              return this.chats.filter(c => {
                  const matchSearch = c.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                      c.code.toLowerCase().includes(this.searchQuery.toLowerCase());
                  
                  if (this.statusFilter === 'Belum Dibaca') {
                      return matchSearch && c.unread > 0;
                  } else if (this.statusFilter === 'Online') {
                      return matchSearch && c.online;
                  }
                  return matchSearch;
              });
          }
      }"
      @filter-changed.window="statusFilter = $event.detail">

    <!-- ================================================ -->
    <!-- 1. SIDEBAR NAVIGASI (Kiri)                        -->
    <!-- ================================================ -->

    <!-- Mobile Sidebar Overlay -->
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
            
            <!-- Vendor -->
            <a href="#" class="flex items-center px-4 py-3 text-grayText hover:bg-gray-50 hover:text-bottle font-medium rounded-xl transition">
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
            
            <!-- Chat — AKTIF -->
            <a href="#" id="nav-chat" class="flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl transition">
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
                <!-- Hamburger Button (mobile) -->
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-bottle focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 leading-tight">Hi, Admin</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola komunikasi dengan pasangan / klien.</p>
                </div>
            </div>

            <!-- Right Header -->
            <div class="flex items-center space-x-6">
                <!-- Notification Dropdown -->
                @include('components.notification-dropdown')

                <div class="w-px h-6 bg-gray-200"></div>

                <!-- Profile Dropdown -->
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

                    <div x-show="profileDropdown" style="display: none;"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-50">
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

        <!-- TWO COLUMN WORKSPACE -->
        <div class="flex-1 flex overflow-hidden relative">

            <!-- ============================================ -->
            <!-- 2. PANEL DAFTAR PERCAKAPAN (Kiri)            -->
            <!-- ============================================ -->
            <div :class="chatRoomOpen ? 'hidden md:flex' : 'flex'"
                 class="w-full md:w-80 xl:w-96 border-r border-gray-100 bg-white flex flex-col shrink-0 h-full">
                
                <!-- Panel Header -->
                <div class="p-5 border-b border-gray-50 flex items-center justify-between shrink-0">
                    <h3 class="text-lg font-bold text-gray-900">Percakapan</h3>
                    
                    <!-- Dropdown Select Filter -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" 
                                class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-gray-900 focus:outline-none transition">
                            <span x-text="statusFilter">Semua</span>
                            <svg class="w-4 h-4 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <!-- Options select -->
                        <div x-show="open" style="display: none;"
                             class="absolute right-0 mt-2 w-36 bg-white rounded-xl shadow-lg border border-gray-100 py-1.5 z-40">
                            <button @click="statusFilter = 'Semua'; open = false" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Semua</button>
                            <button @click="statusFilter = 'Belum Dibaca'; open = false" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Belum Dibaca</button>
                            <button @click="statusFilter = 'Online'; open = false" class="w-full text-left px-4 py-2 text-xs text-gray-700 hover:bg-gray-50">Online</button>
                        </div>
                    </div>
                </div>

                <!-- Search and Filter icon -->
                <div class="p-4 border-b border-gray-50 flex items-center gap-2.5 shrink-0">
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-2.5 w-4.5 h-4.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Cari nama pasangan / no. booking..."
                               class="search-input w-full pl-9 pr-4 py-2 bg-gray-50 border border-gray-100 rounded-xl text-xs text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-bottle transition-all">
                    </div>
                    <!-- Squared filter button -->
                    <button class="p-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-gray-600 hover:border-gray-300 transition duration-150 flex items-center justify-center shrink-0">
                        <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 8.293A1 1 0 013 7.586V4z"/>
                        </svg>
                    </button>
                </div>

                <!-- Chat Lists -->
                <div class="flex-1 overflow-y-auto divide-y divide-gray-50/70">
                    
                    <!-- 
                        LARAVEL INTEGRASI loop:
                        @foreach($conversations as $convo)
                            ...
                        @endforeach
                    -->

                    <template x-for="chat in filteredChats" :key="chat.id">
                        <div @click="activeChatId = chat.id; chatRoomOpen = true; chat.unread = 0"
                             :class="activeChatId === chat.id ? 'bg-[#f4fbf4]/60 border-l-4 border-bottle' : 'hover:bg-gray-50/40 cursor-pointer border-l-4 border-transparent'"
                             class="chat-item-row p-4 flex items-start space-x-3 transition-all duration-150 cursor-pointer">
                            
                            <!-- Avatar (DA, SR, NF) -->
                            <div :class="chat.avatarBg" class="w-11 h-11 rounded-full flex items-center justify-center font-bold text-sm tracking-wider shrink-0" x-text="chat.initials"></div>
                            
                            <!-- Middle section -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold text-gray-900 truncate" x-text="chat.name"></span>
                                    <span class="text-[10px] text-gray-400 font-medium ml-2 shrink-0" x-text="chat.lastMsgTime"></span>
                                </div>
                                <p class="text-xs text-gray-500 truncate mt-1.5" x-text="chat.lastMsgText"></p>
                            </div>
                            
                            <!-- Badge Notif -->
                            <div class="shrink-0 flex items-center justify-center pt-1" x-show="chat.unread > 0">
                                <span class="w-5.5 h-5.5 rounded-full bg-green-600 text-white text-[9.5px] font-bold flex items-center justify-center" x-text="chat.unread">2</span>
                            </div>
                        </div>
                    </template>

                    <!-- Empty state -->
                    <template x-if="filteredChats.length === 0">
                        <div class="p-8 text-center text-gray-400">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-xs font-semibold">Tidak ada chat ditemukan</p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 3. PANEL AREA OBROLAN / CHAT ROOM (Kanan)    -->
            <!-- ============================================ -->
            <div :class="chatRoomOpen ? 'flex' : 'hidden md:flex'"
                 class="flex-1 bg-grayBg flex flex-col min-w-0 h-full relative"
                 x-data="{ activeChat: null }"
                 x-effect="activeChat = chats.find(c => c.id === activeChatId)">

                <template x-if="activeChat">
                    <div class="flex-grow flex flex-col overflow-hidden h-full">
                        
                        <!-- Chat Header -->
                        <div class="h-20 shrink-0 bg-white border-b border-gray-100 px-6 flex items-center justify-between z-10 shadow-sm shadow-gray-50/50">
                            <div class="flex items-center space-x-3.5 min-w-0">
                                
                                <!-- Back arrow button on mobile -->
                                <button @click="chatRoomOpen = false" class="md:hidden text-gray-500 hover:text-bottle mr-2 focus:outline-none">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                    </svg>
                                </button>
                                
                                <!-- Avatar -->
                                <div :class="activeChat.avatarBg" class="w-11 h-11 rounded-full flex items-center justify-center font-bold text-sm tracking-wider shrink-0" x-text="activeChat.initials"></div>
                                
                                <div class="min-w-0">
                                    <div class="flex items-center">
                                        <h4 class="text-sm font-bold text-gray-900 truncate" x-text="activeChat.name"></h4>
                                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-white ml-2 block shadow-sm" x-show="activeChat.online"></span>
                                    </div>
                                    <p class="text-xs text-gray-400 font-medium tracking-wide mt-0.5" x-text="activeChat.code"></p>
                                </div>
                            </div>

                            <div class="flex items-center space-x-2 shrink-0">
                                <!-- Lihat Detail Booking Button -->
                                <button @click="alert('Menampilkan Detail Booking untuk ' + activeChat.code)"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:border-bottle rounded-xl text-xs font-semibold text-gray-600 hover:text-bottle shadow-sm transition">
                                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-400 group-hover:text-bottle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    Lihat Detail Booking
                                </button>
                                <!-- Option dropdown button -->
                                <button class="p-2 bg-white border border-gray-100 hover:bg-gray-50 text-gray-400 hover:text-gray-600 rounded-xl transition focus:outline-none">
                                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Chat Messages Container -->
                        <div class="flex-grow overflow-y-auto p-6 space-y-5 flex flex-col chat-scroll" id="chat-messages-box">
                            
                            <!-- Date Indicator -->
                            <div class="flex justify-center my-2">
                                <span class="px-3.5 py-1 bg-white border border-gray-100 rounded-full text-[10px] font-semibold text-gray-400 shadow-sm tracking-wide">24 Mei 2024</span>
                            </div>

                            <template x-for="msg in activeChat.messages" :key="msg.id">
                                <div class="flex flex-col max-w-[70%] sm:max-w-[55%]"
                                     :class="msg.sender === 'admin' ? 'self-end items-end' : 'self-start items-start'">
                                    
                                    <!-- Chat Bubble -->
                                    <div class="p-3.5 rounded-2xl text-sm shadow-sm"
                                         :class="msg.sender === 'admin' 
                                             ? 'bg-[#EAF2EA] text-gray-800 rounded-tr-none' 
                                             : 'bg-white text-gray-800 rounded-tl-none border border-gray-100'">
                                         
                                         <!-- Text Content -->
                                         <p class="whitespace-pre-line leading-relaxed" x-text="msg.text" x-show="msg.text"></p>
                                         
                                         <!-- Document PDF Attachment Component -->
                                         <div x-show="msg.attachment" class="mt-2.5 p-3 bg-white rounded-xl border border-gray-100 flex items-center space-x-3 w-64">
                                             <!-- PDF Icon Red -->
                                             <div class="bg-red-50 p-2.5 rounded-xl text-red-500 shrink-0">
                                                 <svg class="w-6.5 h-6.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1.5M9 13h6"/>
                                                 </svg>
                                             </div>
                                             
                                             <!-- File Info -->
                                             <div class="flex-1 min-w-0">
                                                 <p class="text-xs font-bold text-gray-900 truncate" x-text="msg.attachment ? msg.attachment.name : ''"></p>
                                                 <p class="text-[9.5px] text-gray-400 font-semibold uppercase tracking-wider mt-0.5" x-text="msg.attachment ? msg.attachment.size : ''"></p>
                                             </div>
                                             
                                             <!-- Download Button -->
                                             <button @click.stop="alert('Mengunduh berkas...')"
                                                     class="text-bottle hover:text-bottleHover p-1.5 hover:bg-leafSoft rounded-lg transition shrink-0">
                                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                 </svg>
                                             </button>
                                         </div>
                                    </div>
                                    
                                    <!-- Timing & Status checkmarks -->
                                    <div class="flex items-center space-x-1.5 mt-1.5 px-1">
                                        <span class="text-[9.5px] text-gray-400" x-text="msg.time"></span>
                                        <template x-if="msg.sender === 'admin'">
                                            <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <!-- Double green checkmark read receipt -->
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7M10 18l4 4L22 10"/>
                                            </svg>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Chat Input Form -->
                        <div class="p-4 bg-white border-t border-gray-100 shrink-0">
                            <form @submit.prevent="if(newMessageText.trim()) { 
                                activeChat.messages.push({
                                    id: Date.now(),
                                    sender: 'admin',
                                    text: newMessageText,
                                    time: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }),
                                    read: true
                                });
                                activeChat.lastMsgText = newMessageText;
                                activeChat.lastMsgTime = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                                newMessageText = '';
                                $nextTick(() => { 
                                    let el = document.getElementById('chat-messages-box');
                                    el.scrollTop = el.scrollHeight;
                                });
                            }" class="flex items-center gap-3">
                                
                                <!-- Attachment icon -->
                                <button type="button" class="p-2.5 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-xl focus:outline-none transition shrink-0">
                                    <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                    </svg>
                                </button>
                                
                                <!-- Input Message -->
                                <input type="text"
                                       x-model="newMessageText"
                                       placeholder="Tulis pesan..."
                                       class="flex-1 bg-gray-50 border border-gray-100 rounded-xl px-4 py-2.5 text-xs sm:text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-bottle transition-all">
                                
                                <!-- Send icon button -->
                                <button type="submit" 
                                        class="bg-bottle hover:bg-bottleHover text-white p-2.5 rounded-full focus:outline-none transition shadow-sm hover:shadow-md shrink-0 flex items-center justify-center">
                                    <svg class="w-4.5 h-4.5 transform rotate-45 -translate-x-0.5 translate-y-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </template>

                <!-- Loading / Select a chat state -->
                <template x-if="!activeChat">
                    <div class="flex-grow flex flex-col items-center justify-center text-gray-400">
                        <svg class="w-16 h-16 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <p class="text-sm font-semibold">Pilih percakapan untuk memulai obrolan</p>
                    </div>
                </template>
            </div>
        </div>
    </div>
</body>
</html>
