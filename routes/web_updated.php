<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PaketController as AdminPaketController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Admin\PesananController as AdminPesananController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Customer\PembayaranController as CustomerPembayaranController;
use App\Http\Controllers\BuktiPembayaranController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Customer\PesananController as CustomerPesananController;
use App\Http\Controllers\Lapangan\DashboardController as LapanganDashboardController;
use App\Http\Controllers\Lapangan\PesananController as LapanganPesananController;
use App\Http\Controllers\Lapangan\JadwalController as LapanganJadwalController;
use App\Http\Controllers\Lapangan\TugasController as LapanganTugasController;
use App\Http\Controllers\Lapangan\LaporanController as LapanganLaporanController;
use App\Http\Controllers\Lapangan\VendorController as LapanganVendorController;
use App\Http\Controllers\Lapangan\ChatController as LapanganChatController;
use App\Http\Controllers\Lapangan\PengaturanController as LapanganPengaturanController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;

// ============================================================
// HALAMAN DEPAN & PUBLIK
// ============================================================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/paket', [PageController::class, 'paket'])->name('paket');
Route::get('/vendor', [PageController::class, 'vendor'])->name('vendor');
Route::get('/vendor/{vendor}', [PageController::class, 'vendorDetail'])->name('vendor.detail');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('about');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [PageController::class, 'blogShow'])->name('blog.show');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::post('/kontak', [PageController::class, 'contactStore'])->name('contact.store');

// ============================================================
// AUTENTIKASI
// ============================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Bukti transfer
Route::middleware('auth')->get('/bukti-pembayaran/{konfirmasi}', [BuktiPembayaranController::class, 'show'])
    ->name('pembayaran.bukti');

// ============================================================
// PANEL ADMIN
// ============================================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showAdminLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'adminLogin']);
    });

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        // ✅ BOOKING MANAGEMENT
        Route::get('/booking', [AdminPesananController::class, 'index'])->name('booking');
        Route::get('/booking/{pesanan}', [AdminPesananController::class, 'show'])->name('booking.show');
        Route::patch('/booking/{pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('booking.status');
        Route::delete('/booking/{pesanan}', [AdminPesananController::class, 'destroy'])->name('booking.destroy');
        Route::patch('/booking/{pesanan}/assign-korlap', [AdminPesananController::class, 'assignKorlap'])->name('booking.assignKorlap');
        
        // Rundown Management
        Route::post('/booking/{pesanan}/rundown', [AdminPesananController::class, 'storeRundown'])->name('booking.rundown.store');
        Route::patch('/booking/{pesanan}/rundown/{rundown}', [AdminPesananController::class, 'updateRundown'])->name('booking.rundown.update');
        Route::delete('/booking/{pesanan}/rundown/{rundown}', [AdminPesananController::class, 'destroyRundown'])->name('booking.rundown.destroy');
        
        // Paket & Vendor
        Route::resource('paket', AdminPaketController::class)->except(['show']);
        Route::resource('vendor', AdminVendorController::class)->except(['show']);
        
        // Jadwal & Pembayaran
        Route::get('/jadwal', [AdminController::class, 'jadwal'])->name('jadwal');
        Route::get('/pembayaran', [AdminPembayaranController::class, 'index'])->name('pembayaran');
        Route::get('/pembayaran/konfirmasi/{konfirmasi}', [AdminPembayaranController::class, 'show'])->name('pembayaran.show');
        Route::post('/pembayaran/konfirmasi/{konfirmasi}/setujui', [AdminPembayaranController::class, 'approve'])->name('pembayaran.approve');
        Route::post('/pembayaran/konfirmasi/{konfirmasi}/tolak', [AdminPembayaranController::class, 'reject'])->name('pembayaran.reject');
        
        // Chat
        Route::get('/chat', [AdminChatController::class, 'index'])->name('chat');
        Route::get('/chat/{pesanan}', [AdminChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{pesanan}', [AdminChatController::class, 'store'])->name('chat.store');
    });
});

// ============================================================
// PANEL TIM LAPANGAN (KORLAP)
// ============================================================
Route::prefix('lapangan')->name('lapangan.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AuthController::class, 'showLapanganLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'lapanganLogin']);
    });

    Route::middleware(['auth', 'lapangan'])->group(function () {
        // ============================================================
        // DASHBOARD & MAIN PAGES
        // ============================================================
        Route::get('/dashboard', [LapanganDashboardController::class, 'index'])->name('dashboard');

        // ============================================================
        // PESANAN (BOOKINGS)
        // ============================================================
        Route::get('/pesanan', [LapanganPesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{pesanan}', [LapanganPesananController::class, 'show'])->name('pesanan.show');
        Route::patch('/pesanan/{pesanan}/progress', [LapanganPesananController::class, 'updateProgress'])->name('pesanan.progress');
        Route::post('/pesanan/{pesanan}/laporan', [LapanganPesananController::class, 'storeLaporan'])->name('pesanan.laporan');

        // API Endpoints untuk real-time updates
        Route::get('/pesanan/{pesanan}/metrics', [LapanganPesananController::class, 'getProgressMetrics'])->name('pesanan.metrics');

        // ============================================================
        // JADWAL ACARA
        // ============================================================
        Route::get('/jadwal', [LapanganJadwalController::class, 'index'])->name('jadwal');
        Route::get('/jadwal/rundown/{pesanan}', [LapanganJadwalController::class, 'getRundownDetail'])->name('jadwal.rundown');
        Route::get('/jadwal/meetings/{startDate}/{endDate}', [LapanganJadwalController::class, 'getMeetings'])->name('jadwal.meetings');

        // ============================================================
        // TUGAS KANBAN & CHECKLIST
        // ============================================================
        Route::resource('tugas', LapanganTugasController::class);
        
        // AJAX endpoints for Kanban
        Route::patch('/tugas/{tugas}/status', [LapanganTugasController::class, 'updateStatus'])->name('tugas.updateStatus');
        Route::patch('/tugas/{tugas}/checklist/{checklist}', [LapanganTugasController::class, 'updateChecklist'])->name('tugas.updateChecklist');
        Route::get('/tugas/{tugas}/detail', [LapanganTugasController::class, 'detail'])->name('tugas.detail');

        // ============================================================
        // LAPORAN LAPANGAN
        // ============================================================
        Route::get('/laporan', [LapanganLaporanController::class, 'index'])->name('laporan');
        
        // API endpoints for real-time reporting
        Route::post('/laporan/kendala', [LapanganLaporanController::class, 'storeKendala'])->name('laporan.kendala.store');
        Route::post('/laporan/dokumentasi', [LapanganLaporanController::class, 'uploadDokumentasi'])->name('laporan.dokumentasi.upload');
        Route::get('/laporan/metrics', [LapanganLaporanController::class, 'metrics'])->name('laporan.metrics');
        Route::get('/laporan/progress', [LapanganLaporanController::class, 'progressByPesanan'])->name('laporan.progress');
        Route::get('/laporan/kendala/{pesanan}', [LapanganLaporanController::class, 'kendalaList'])->name('laporan.kendala.list');
        Route::put('/laporan/catatan/{pesanan}', [LapanganLaporanController::class, 'updateCatatan'])->name('laporan.catatan.update');

        // ============================================================
        // VENDOR
        // ============================================================
        Route::get('/vendor', [LapanganVendorController::class, 'index'])->name('vendor');
        Route::post('/vendor/store', [LapanganVendorController::class, 'store'])->name('vendor.store');

        // ============================================================
        // CHAT
        // ============================================================
        Route::get('/chat', [LapanganChatController::class, 'index'])->name('chat');
        Route::post('/chat/send', [LapanganChatController::class, 'sendMessage'])->name('chat.send');

        // ============================================================
        // PENGATURAN (SETTINGS) - WITH PROFILE API
        // ============================================================
        Route::get('/pengaturan', [LapanganPengaturanController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan', [LapanganPengaturanController::class, 'update'])->name('pengaturan.update');
        Route::get('/api/user-profile', [LapanganPengaturanController::class, 'apiProfile'])->name('api.profile');
    });
});

// ============================================================
// Panel Client
// ============================================================
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/booking/buat', [CustomerBookingController::class, 'create'])->name('booking.create');
    Route::post('/booking', [CustomerBookingController::class, 'store'])->name('booking.store');
    
    Route::get('/chat', [CustomerChatController::class, 'index'])->name('chat');
    Route::get('/chat/{pesanan}', [CustomerChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{pesanan}', [CustomerChatController::class, 'store'])->name('chat.store');
    
    Route::get('/profil', [CustomerProfileController::class, 'show'])->name('profile');
    Route::get('/pengaturan', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/pengaturan', [CustomerProfileController::class, 'update'])->name('profile.update');
    
    Route::get('/pembayaran', [CustomerController::class, 'pembayaran'])->name('pembayaran');
    Route::get('/pembayaran/konfirmasi/{invoice}', [CustomerPembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran/konfirmasi/{invoice}', [CustomerPembayaranController::class, 'store'])->name('pembayaran.store');
    
    Route::get('/pesanan', [CustomerController::class, 'pesanan'])->name('pesanan');
    Route::get('/pesanan/detail/{id}', [CustomerController::class, 'detailPesanan'])->name('pesanan_detail');
    Route::post('/pesanan/{pesanan}/batalkan', [CustomerPesananController::class, 'batalkan'])->name('pesanan.batalkan');
    
    Route::post('/review/{vendor}', [CustomerReviewController::class, 'store'])->name('review.store');
    Route::put('/review/{review}', [CustomerReviewController::class, 'update'])->name('review.update');
    Route::delete('/review/{review}', [CustomerReviewController::class, 'destroy'])->name('review.destroy');
    
    Route::get('/jadwal', [CustomerController::class, 'jadwal'])->name('jadwal');
    Route::get('/invoice/{id}', [CustomerController::class, 'invoice'])->name('invoice');
});
