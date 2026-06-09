<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pembayaran - Brilliant Event & Wedding Organizer</title>
    <meta name="description" content="Halaman kelola pembayaran dan invoice Brilliant Event & Wedding Organizer.">

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
        /* Smooth transitions */
        .payment-row {
            transition: background-color 0.15s ease;
        }
        .btn-action {
            transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-action:hover {
            transform: translateY(-1px);
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
        /* Modal Scrollbar */
        .modal-scroll::-webkit-scrollbar {
            width: 4px;
        }
        .modal-scroll::-webkit-scrollbar-thumb {
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
          showModal: false, // Ditutup secara default agar user bisa menguji dropdown filter status melayang di latar belakang
          toastShow: false,
          toastMessage: '',
          
          // Data lengkap demo pembayaran (Seluruh lokasi difokuskan ke area Garut & sinkron dengan mockup)
          payments: [
              { 
                  code: 'BRI-250524-001', 
                  couple: 'Dinda & Arya', 
                  package: 'Gold Package', 
                  location: 'Gedung Intan Dewata, Garut',
                  totalBill: 'Rp 65.000.000',
                  paid: 'Rp 45.000.000',
                  remaining: 'Rp 20.000.000',
                  status: 'Belum Bayar', 
                  method: 'Transfer Bank BCA',
                  notes: '-',
                  lastPaymentDate: '-',
                  history: [
                      { status: 'Belum Bayar', date: '20 Mei 2024', amount: 'Rp 0', time: '10:30 WIB', dotBg: 'bg-orange-500' }
                  ],
                  hasProof: false
              },
              { 
                  code: 'BRI-250524-002', 
                  couple: 'Salsa & Rizky', 
                  package: 'Platinum Package', 
                  location: 'Gedung Bale Paminton, Garut',
                  totalBill: 'Rp 85.000.000',
                  paid: 'Rp 85.000.000',
                  remaining: 'Rp 0',
                  status: 'Lunas',
                  method: 'Transfer Bank Mandiri',
                  notes: 'Lunas tepat waktu',
                  lastPaymentDate: '26 Mei 2024',
                  history: [
                      { status: 'DP Awal (Pembayaran 1)', date: '10 Mei 2024', amount: 'Rp 40.000.000', time: '09:00 WIB', dotBg: 'bg-green-500' },
                      { status: 'Pelunasan (Pembayaran 2)', date: '26 Mei 2024', amount: 'Rp 45.000.000', time: '14:30 WIB', dotBg: 'bg-green-500' }
                  ],
                  hasProof: true,
                  proofImg: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80'
              },
              { 
                  code: 'BRI-240530-003', 
                  couple: 'Nadia & Farhan', 
                  package: 'Silver Package', 
                  location: 'Hotel Santika, Garut',
                  totalBill: 'Rp 55.000.000',
                  paid: 'Rp 25.000.000',
                  remaining: 'Rp 30.000.000',
                  status: 'DP Dibayar',
                  method: 'Transfer Bank BCA',
                  notes: '-',
                  lastPaymentDate: '30 Mei 2024',
                  history: [
                      { status: 'DP Awal (Pembayaran 1)', date: '12 Mei 2024', amount: 'Rp 25.000.000', time: '11:15 WIB', dotBg: 'bg-blue-500' }
                  ],
                  hasProof: true,
                  proofImg: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80'
              },
              { 
                  code: 'BRI-240516-004', 
                  couple: 'Putri & Bagas', 
                  package: 'Luxury Package', 
                  location: 'Gedung Korpri, Garut',
                  totalBill: 'Rp 95.000.000',
                  paid: 'Rp 75.000.000',
                  remaining: 'Rp 20.000.000',
                  status: 'Belum Bayar',
                  method: 'Transfer Bank BCA',
                  notes: 'Sisa cicilan pelunasan belum diselesaikan.',
                  lastPaymentDate: '1 Juni 2024',
                  history: [
                      { status: 'DP Awal', date: '01 Mei 2024', amount: 'Rp 75.000.000', time: '13:00 WIB', dotBg: 'bg-green-500' }
                  ],
                  hasProof: false
              },
              { 
                  code: 'BRI-240510-005', 
                  couple: 'Anisa & Reza', 
                  package: 'Gold Package', 
                  location: 'Sabda Alam Resort, Garut',
                  totalBill: 'Rp 65.000.000',
                  paid: 'Rp 65.000.000',
                  remaining: 'Rp 0',
                  status: 'Lunas',
                  method: 'Transfer Bank BNI',
                  notes: '-',
                  lastPaymentDate: '2 Juni 2024',
                  history: [
                      { status: 'Pelunasan Penuh', date: '25 April 2024', amount: 'Rp 65.000.000', time: '10:00 WIB', dotBg: 'bg-green-500' }
                  ],
                  hasProof: true,
                  proofImg: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80'
              },
              { 
                  code: 'BRI-240508-006', 
                  couple: 'Vina & Andi', 
                  package: 'Silver Package', 
                  location: 'Gedung Islamic Center, Garut',
                  totalBill: 'Rp 55.000.000',
                  paid: 'Rp 30.000.000',
                  remaining: 'Rp 25.000.000',
                  status: 'DP Dibayar',
                  method: 'Transfer Bank BCA',
                  notes: '-',
                  lastPaymentDate: '8 Juni 2024',
                  history: [
                      { status: 'Pembayaran DP', date: '05 Mei 2024', amount: 'Rp 30.000.000', time: '11:30 WIB', dotBg: 'bg-blue-500' }
                  ],
                  hasProof: true,
                  proofImg: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80'
              },
              { 
                  code: 'BRI-240507-007', 
                  couple: 'Rani & Dika', 
                  package: 'Platinum Package', 
                  location: 'Kampung Sampireun, Garut',
                  totalBill: 'Rp 85.000.000',
                  paid: 'Rp 60.000.000',
                  remaining: 'Rp 25.000.000',
                  status: 'Belum Bayar',
                  method: 'Transfer Bank BCA',
                  notes: '-',
                  lastPaymentDate: '9 Juni 2024',
                  history: [
                      { status: 'Pembayaran Tahap 1', date: '15 Mei 2024', amount: 'Rp 60.000.000', time: '16:45 WIB', dotBg: 'bg-green-500' }
                  ],
                  hasProof: false
              },
              { 
                  code: 'BRI-240505-008', 
                  couple: 'Maya & Fikri', 
                  package: 'Gold Package', 
                  location: 'Villa Rancabango, Garut',
                  totalBill: 'Rp 65.000.000',
                  paid: 'Rp 65.000.000',
                  remaining: 'Rp 0',
                  status: 'Lunas',
                  method: 'Transfer Bank BCA',
                  notes: '-',
                  lastPaymentDate: '15 Juni 2024',
                  history: [
                      { status: 'Pelunasan Penuh', date: '01 Mei 2024', amount: 'Rp 65.000.000', time: '09:30 WIB', dotBg: 'bg-green-500' }
                  ],
                  hasProof: true,
                  proofImg: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80'
              },
              { 
                  code: 'BRI-240503-009', 
                  couple: 'Chelsea & Kevin', 
                  package: 'Luxury Package', 
                  location: 'Hotel Harmoni, Garut',
                  totalBill: 'Rp 95.000.000',
                  paid: 'Rp 50.000.000',
                  remaining: 'Rp 45.000.000',
                  status: 'Belum Bayar',
                  method: 'Transfer Bank BCA',
                  notes: '-',
                  lastPaymentDate: '16 Juni 2024',
                  history: [
                      { status: 'DP Booking', date: '20 Mei 2024', amount: 'Rp 50.000.000', time: '10:00 WIB', dotBg: 'bg-green-500' }
                  ],
                  hasProof: false
              },
              { 
                  code: 'BRI-240501-010', 
                  couple: 'Ayu & Ilham', 
                  package: 'Silver Package', 
                  location: 'Gedung PGRI, Garut',
                  totalBill: 'Rp 55.000.000',
                  paid: 'Rp 55.000.000',
                  remaining: 'Rp 0',
                  status: 'Lunas',
                  method: 'Transfer Bank Mandiri',
                  notes: '-',
                  lastPaymentDate: '20 Juni 2024',
                  history: [
                      { status: 'Lunas', date: '05 Mei 2024', amount: 'Rp 55.000.000', time: '13:30 WIB', dotBg: 'bg-green-500' }
                  ],
                  hasProof: true,
                  proofImg: 'https://images.unsplash.com/photo-1554415707-6e8cfc93fe23?ixlib=rb-1.2.1&auto=format&fit=crop&w=600&q=80'
              }
          ],
          
          activePayment: null,
          
          init() {
              // Set Dinda & Arya secara default ke activePayment
              this.activePayment = this.payments[0];
          },

          selectPayment(item) {
              this.activePayment = item;
              this.showModal = true;
          },

          showToast(message) {
              this.toastMessage = message;
              this.toastShow = true;
              setTimeout(() => { this.toastShow = false; }, 3000);
          },

          get filteredPayments() {
              return this.payments.filter(p => {
                  const matchSearch = p.couple.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                      p.code.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                      p.package.toLowerCase().includes(this.searchQuery.toLowerCase());
                  
                  let matchStatus = true;
                  if (this.statusFilter !== 'Semua Status') {
                      matchStatus = p.status === this.statusFilter;
                  }
                  return matchSearch && matchStatus;
              });
          }
      }">

    <!-- Toast Notification -->
    <div x-show="toastShow" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-5 right-5 z-[9999] max-w-sm bg-white rounded-2xl shadow-xl border border-gray-100 p-4 flex items-center space-x-3"
         style="display: none;">
        <div class="bg-green-100 text-green-600 p-2 rounded-xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <div>
            <p class="text-sm font-semibold text-gray-900">Sukses</p>
            <p class="text-xs text-gray-500 mt-0.5" x-text="toastMessage"></p>
        </div>
    </div>

    <!-- ================================================ -->
    <!-- 1. SIDEBAR NAVIGASI (Kiri)                        -->
    <!-- ================================================ -->

    <!-- Mobile Sidebar Overlay -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 bg-black/50 lg:hidden" @click="sidebarOpen = false" style="display: none;"></div>

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

            <!-- Pembayaran — AKTIF -->
            <a href="#" id="nav-pembayaran" class="flex items-center px-4 py-3 bg-leafSoft text-bottle font-semibold rounded-xl transition">
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
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative" :class="showModal ? 'backdrop-blur-[1.5px]' : ''">

        <!-- HEADER -->
        <header class="bg-white border-b border-gray-100 h-20 px-6 flex items-center justify-between shrink-0">
            <div class="flex items-center">
                <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-bottle focus:outline-none mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                    </svg>
                </button>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 leading-tight font-sans">Hi, Admin</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola dan tinjau riwayat transaksi pembayaran dari klien.</p>
                </div>
            </div>

            <!-- Profile Info -->
            <div class="flex items-center space-x-6">
                <!-- Notification Dropdown -->
                <x-notification-bell />

                <div class="w-px h-6 bg-gray-200"></div>

                <div class="relative">
                    <button @click="profileDropdown = !profileDropdown"
                            @click.away="profileDropdown = false"
                            class="flex items-center space-x-3 focus:outline-none">
                        <img src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                             alt="Admin Avatar"
                             class="w-10 h-10 rounded-full object-cover border border-gray-100 shadow-sm">
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-bold text-gray-900 leading-tight font-sans">Admin Brilliant</p>
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

        <!-- MAIN LAYOUT BODY -->
        <main class="flex-grow overflow-y-auto p-6 lg:p-8">
            <h3 class="text-lg font-bold text-gray-900 mb-6 font-sans">Pembayaran</h3>

            <!-- ============================================ -->
            <!-- 3. METRIK CARD ATAS (Sesuai Mockup Terkini)    -->
            <!-- ============================================ -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Pembayaran -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)] flex items-start space-x-4">
                    <div class="bg-green-50 p-3.5 rounded-2.5xl text-bottle shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Pembayaran</p>
                        <h3 class="text-2xl font-bold text-gray-900 leading-tight mb-2">Rp 512.750.000</h3>
                        <p class="text-xs font-semibold text-green-600 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            23% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Menunggu Pembayaran -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)] flex items-start space-x-4">
                    <div class="bg-orange-50 p-3.5 rounded-2.5xl text-orange-500 shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Menunggu Pembayaran</p>
                        <h3 class="text-2xl font-bold text-gray-900 leading-tight mb-2">Rp 87.500.000</h3>
                        <p class="text-xs font-semibold text-green-600 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            12% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Lunas -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)] flex items-start space-x-4">
                    <div class="bg-blue-50 p-3.5 rounded-2.5xl text-blue-600 shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Lunas</p>
                        <h3 class="text-2xl font-bold text-gray-900 leading-tight mb-2">Rp 382.250.000</h3>
                        <p class="text-xs font-semibold text-green-600 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                            </svg>
                            18% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>

                <!-- Terlambat -->
                <div class="metric-card bg-white rounded-3xl p-6 border border-gray-100 shadow-[0_2px_10px_rgba(0,0,0,0.01)] flex items-start space-x-4">
                    <div class="bg-red-50 p-3.5 rounded-2.5xl text-red-500 shrink-0 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Terlambat</p>
                        <h3 class="text-2xl font-bold text-gray-900 leading-tight mb-2">Rp 43.000.000</h3>
                        <p class="text-xs font-semibold text-red-500 flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                            8% <span class="text-gray-400 ml-1 font-normal">dari bulan lalu</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- ============================================ -->
            <!-- 4. FILTER BAR & DROPDOWN POP-OVER INTERAKTIF  -->
            <!-- ============================================ -->
            <div class="flex flex-col xl:flex-row items-stretch xl:items-center justify-between mb-6 gap-4">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 flex-1 max-w-4xl">
                    
                    <!-- Trigger Dropdown Utama -->
                    <div class="relative w-full sm:w-56" x-data="{ open: false }">
                        <button @click="open = !open"
                                @click.away="open = false"
                                class="w-full inline-flex items-center justify-between px-4 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 focus:outline-none transition shadow-sm">
                            <span class="flex items-center">
                                <template x-if="statusFilter === 'Lunas'">
                                    <span class="w-2.5 h-2.5 rounded-full bg-green-500 mr-2"></span>
                                </template>
                                <template x-if="statusFilter === 'DP Dibayar'">
                                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500 mr-2"></span>
                                </template>
                                <template x-if="statusFilter === 'Belum Bayar'">
                                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 mr-2"></span>
                                </template>
                                <span x-text="statusFilter">Semua Status</span>
                            </span>
                            <svg class="w-4 h-4 text-gray-400 ml-2 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        
                        <!-- Menu Dropdown Melayang (Saat Klik Semua Status) -->
                        <div x-show="open" 
                             style="display: none;"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute left-0 mt-2 w-full bg-white rounded-2xl shadow-xl border border-gray-100 py-2.5 z-40">
                            
                            <!-- Opsi reset filter -->
                            <button @click="statusFilter = 'Semua Status'; open = false" 
                                    class="w-full flex items-center px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition font-medium">
                                <svg class="w-4 h-4 text-gray-400 mr-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 7.89"/>
                                </svg>
                                Semua Status
                            </button>

                            <div class="border-t border-gray-100/80 my-1"></div>

                            <!-- 🟢 Lunas -->
                            <button @click="statusFilter = 'Lunas'; open = false" 
                                    class="w-full flex items-center px-4 py-2.5 text-sm text-gray-800 hover:bg-green-50/50 hover:text-green-800 transition font-medium">
                                <svg class="w-4.5 h-4.5 text-green-600 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Lunas
                            </button>

                            <!-- 🔵 DP Dibayar -->
                            <button @click="statusFilter = 'DP Dibayar'; open = false" 
                                    class="w-full flex items-center px-4 py-2.5 text-sm text-gray-800 hover:bg-blue-50/50 hover:text-blue-800 transition font-medium">
                                <svg class="w-4.5 h-4.5 text-blue-500 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                </svg>
                                DP Dibayar
                            </button>

                            <!-- 🟠 Belum Bayar -->
                            <button @click="statusFilter = 'Belum Bayar'; open = false" 
                                    class="w-full flex items-center px-4 py-2.5 text-sm text-gray-800 hover:bg-orange-50/50 hover:text-orange-800 transition font-medium">
                                <svg class="w-4.5 h-4.5 text-orange-500 mr-2.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Belum Bayar
                            </button>
                        </div>
                    </div>

                    <!-- Date Range Picker Mockup -->
                    <div class="relative w-full sm:w-64">
                        <input type="text" 
                               readonly
                               value="01 Mei 2024 - 31 Mei 2024"
                               class="w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 focus:outline-none cursor-pointer hover:bg-gray-50 transition shadow-sm">
                        <svg class="absolute right-3.5 top-3.5 w-4.5 h-4.5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>

                    <!-- Search Input -->
                    <div class="relative flex-1">
                        <input type="text"
                               x-model="searchQuery"
                               placeholder="Cari nama pasangan / no. booking / paket..."
                               class="search-input w-full pl-4 pr-10 py-2.5 bg-white border border-gray-200 rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-bottle transition-all duration-200 shadow-sm">
                        <svg class="absolute right-3.5 top-3.5 w-5 h-5 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Export Button -->
                <button @click="showToast('Data pembayaran berhasil diekspor')" 
                        class="inline-flex items-center justify-center px-5 py-2.5 bg-white text-bottle border border-gray-200 rounded-xl text-sm font-semibold hover:bg-leafSoft hover:border-green-200 transition shadow-sm shrink-0">
                    <svg class="w-4.5 h-4.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export
                </button>
            </div>

            <!-- ============================================ -->
            <!-- 5. TABEL DAFTAR DATA PEMBAYARAN (Sesuai Mockup)-->
            <!-- ============================================ -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-[0_2px_12px_rgba(0,0,0,0.015)] overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/75 border-b border-gray-100">
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider w-40">No. Booking</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Nama Pasangan</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Paket</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Tagihan</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Dibayar</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Sisa Pembayaran</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider">Tanggal Pembayaran Terakhir</th>
                                <th class="px-6 py-4.5 text-xs font-semibold text-gray-400 uppercase tracking-wider text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 text-sm">
                            
                            <!-- 
                                LARAVEL INTEGRASI LOOP:
                                @foreach($payments as $payment)
                                    ...
                                @endforeach
                            -->

                            <template x-for="pay in filteredPayments" :key="pay.code">
                                <tr class="payment-row hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-500 tracking-wide" x-text="pay.code"></td>
                                    <td class="px-6 py-4">
                                        <span class="font-bold text-gray-900 block" x-text="pay.couple"></span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600" x-text="pay.package"></td>
                                    <td class="px-6 py-4 font-semibold text-gray-900" x-text="pay.totalBill"></td>
                                    <td class="px-6 py-4 text-gray-600 font-semibold" x-text="pay.paid"></td>
                                    <td class="px-6 py-4 font-semibold" :class="pay.remaining !== 'Rp 0' ? 'text-[#C27803]' : 'text-gray-900'" x-text="pay.remaining"></td>
                                    <td class="px-6 py-4">
                                        <span :class="{
                                            'bg-green-50 text-green-600': pay.status === 'Lunas',
                                            'bg-blue-50 text-blue-600': pay.status === 'DP Dibayar',
                                            'bg-orange-50 text-orange-600': pay.status === 'Belum Bayar'
                                        }" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="{
                                                'bg-green-500': pay.status === 'Lunas',
                                                'bg-blue-500': pay.status === 'DP Dibayar',
                                                'bg-orange-500': pay.status === 'Belum Bayar'
                                            }"></span>
                                            <span x-text="pay.status"></span>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 font-medium" x-text="pay.lastPaymentDate"></td>
                                    <td class="px-6 py-4 text-center">
                                        <button @click="selectPayment(pay)"
                                                class="btn-action inline-flex items-center px-3 py-1.5 text-xs font-bold text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:outline-none">
                                            <svg class="w-3.5 h-3.5 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            Detail
                                        </button>
                                    </td>
                                </tr>
                            </template>

                            <template x-if="filteredPayments.length === 0">
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada data pembayaran ditemukan
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Table pagination -->
                <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-50 flex items-center justify-between">
                    <p class="text-xs text-gray-500">
                        Menampilkan <span class="font-semibold text-gray-700">1 - 10</span> dari <span class="font-semibold text-gray-700">48</span> data
                    </p>
                    <div class="flex items-center space-x-1">
                        <button class="p-2 text-gray-400 hover:text-bottle rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg></button>
                        <button class="px-3 py-1.5 text-xs font-semibold bg-bottle text-white rounded-lg">1</button>
                        <button class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-leafSoft hover:text-bottle rounded-lg">2</button>
                        <button class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-leafSoft hover:text-bottle rounded-lg">3</button>
                        <span class="text-gray-400 text-xs px-1">...</span>
                        <button class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:bg-leafSoft hover:text-bottle rounded-lg">5</button>
                        <button class="p-2 text-gray-400 hover:text-bottle rounded-lg"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg></button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- ================================================ -->
    <!-- 6. KOMPONEN MODAL POP-UP DETAIL PEMBAYARAN       -->
    <!-- ================================================ -->
    <div x-show="showModal" 
         class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4"
         style="display: none;">
        
        <!-- Gelap Overlay dengan blur -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" 
             @click="showModal = false"></div>

        <!-- Box Modal -->
        <div class="bg-white rounded-3xl overflow-hidden shadow-2xl transform transition-all w-full max-w-4xl relative z-10 flex flex-col max-h-[90vh]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95">
            
            <!-- Header Modal -->
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between shrink-0">
                <h3 class="text-lg font-bold text-gray-900 font-sans">Detail Pembayaran</h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 focus:outline-none rounded-lg p-1 hover:bg-gray-50 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content Area (Scrollable) -->
            <div class="overflow-y-auto p-6 lg:p-8 space-y-8 flex-1 modal-scroll">
                
                <template x-if="activePayment">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        
                        <!-- KOLOM KIRI (Informasi & Finansial) -->
                        <div class="space-y-6">
                            
                            <!-- 1. Informasi Pemesanan -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-bold text-bottle border-l-3 border-bottle pl-2 uppercase tracking-wide">| Informasi Pemesanan</h4>
                                <div class="grid grid-cols-3 gap-y-3 text-xs leading-relaxed">
                                    <span class="text-gray-400 font-medium">No. Booking</span>
                                    <span class="col-span-2 font-bold text-gray-900 tracking-wide" x-text="activePayment.code"></span>

                                    <span class="text-gray-400 font-medium">Nama Pasangan</span>
                                    <span class="col-span-2 font-bold text-gray-900" x-text="activePayment.couple"></span>

                                    <span class="text-gray-400 font-medium">Paket</span>
                                    <span class="col-span-2 font-bold text-gray-900" x-text="activePayment.package"></span>

                                    <span class="text-gray-400 font-medium">Lokasi Acara</span>
                                    <span class="col-span-2 font-bold text-gray-900" x-text="activePayment.location"></span>
                                </div>
                            </div>

                            <!-- 2. Informasi Pembayaran -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-bold text-bottle border-l-3 border-bottle pl-2 uppercase tracking-wide">| Informasi Pembayaran</h4>
                                <div class="grid grid-cols-3 gap-y-3.5 text-xs items-center leading-relaxed">
                                    <span class="text-gray-400 font-medium">Total Tagihan</span>
                                    <span class="col-span-2 font-bold text-gray-900" x-text="activePayment.totalBill"></span>

                                    <span class="text-gray-400 font-medium">Sudah Dibayar</span>
                                    <span class="col-span-2 font-bold text-gray-900" x-text="activePayment.paid"></span>

                                    <span class="text-gray-400 font-medium">Sisa Pembayaran</span>
                                    <span class="col-span-2 font-bold text-[#C27803] text-sm" x-text="activePayment.remaining"></span>

                                    <span class="text-gray-400 font-medium">Status Pembayaran</span>
                                    <div class="col-span-2">
                                        <span :class="{
                                            'bg-green-50 text-green-600 border-green-100': activePayment.status === 'Lunas',
                                            'bg-blue-50 text-blue-600 border-blue-100': activePayment.status === 'DP Dibayar',
                                            'bg-orange-50 text-orange-600 border-orange-100': activePayment.status === 'Belum Bayar'
                                        }" class="inline-flex items-center px-3 py-1 border rounded-md text-[11px] font-bold tracking-wide">
                                            <span x-text="activePayment.status"></span>
                                        </span>
                                    </div>

                                    <span class="text-gray-400 font-medium">Metode Pembayaran</span>
                                    <span class="col-span-2 font-bold text-gray-900" x-text="activePayment.method"></span>

                                    <span class="text-gray-400 font-medium">Catatan</span>
                                    <span class="col-span-2 font-medium text-gray-500" x-text="activePayment.notes"></span>
                                </div>
                            </div>
                        </div>

                        <!-- KOLOM KANAN (Timeline & Bukti) -->
                        <div class="space-y-6">
                            
                            <!-- 1. Riwayat Pembayaran -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-bold text-bottle border-l-3 border-bottle pl-2 uppercase tracking-wide">| Riwayat Pembayaran</h4>
                                
                                <div class="space-y-3">
                                    <template x-for="(hist, index) in activePayment.history" :key="index">
                                        <div class="bg-gray-50/60 border border-gray-100 rounded-2xl p-4 flex items-start space-x-3.5 relative">
                                            <div class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0" :class="hist.dotBg"></div>
                                            
                                            <div class="flex-1 text-xs">
                                                <div class="flex items-center justify-between">
                                                    <span class="font-bold text-gray-900" x-text="hist.status"></span>
                                                    <span class="text-[10px] text-gray-400 font-semibold" x-text="hist.date"></span>
                                                </div>
                                                <div class="flex items-center justify-between mt-2 text-gray-500">
                                                    <span class="font-semibold text-gray-800" x-text="hist.amount"></span>
                                                    <span class="text-[10px] font-medium" x-text="hist.time"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 2. Bukti Transfer -->
                            <div class="space-y-4">
                                <h4 class="text-sm font-bold text-bottle border-l-3 border-bottle pl-2 uppercase tracking-wide">| Bukti Transfer</h4>
                                
                                <!-- Jika ada bukti transfer -->
                                <template x-if="activePayment.hasProof">
                                    <div class="bg-gray-50/40 rounded-3xl border border-gray-100 p-4 flex flex-col items-center">
                                        <div class="overflow-hidden rounded-2xl w-full max-h-56 shadow-sm border border-gray-100 cursor-pointer" 
                                             @click="alert('Memperbesar bukti transfer...')">
                                            <img :src="activePayment.proofImg" alt="Struk Transfer" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                                        </div>
                                        <span class="text-[10px] text-gray-400 mt-2 font-medium">Klik gambar untuk memperbesar</span>
                                    </div>
                                </template>

                                <!-- Jika belum ada bukti transfer -->
                                <template x-if="!activePayment.hasProof">
                                    <div class="bg-gray-50/40 rounded-3xl border border-2 border-dashed border-gray-200 p-8 flex flex-col items-center justify-center text-center">
                                        <div class="p-3 bg-gray-100 rounded-full text-gray-400 mb-3.5">
                                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                        <p class="text-xs font-bold text-gray-900">Belum ada bukti transfer</p>
                                        <p class="text-[10px] text-gray-400 mt-1 max-w-[200px] leading-relaxed">Bukti transfer akan muncul setelah pembayaran dilakukan.</p>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Footer Modal -->
            <div class="border-t border-gray-100 p-4 bg-gray-50/80 flex items-center justify-end space-x-3 shrink-0">
                <button @click="showModal = false" 
                        class="px-5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 rounded-xl text-xs font-semibold text-gray-600 transition">
                    Tutup
                </button>
                <button @click="showToast('Invoice ' + activePayment.code + ' berhasil diunduh')" 
                        class="inline-flex items-center px-5 py-2.5 bg-bottle hover:bg-bottleHover text-white rounded-xl text-xs font-semibold shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Invoice
                </button>
            </div>
        </div>
    </div>
</body>
</html>

