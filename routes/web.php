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
use App\Http\Controllers\Admin\OperasionalLapanganController as AdminOperasionalController;
use App\Http\Controllers\Admin\PembayaranController as AdminPembayaranController;
use App\Http\Controllers\Admin\LaporanKeuanganController as AdminLaporanKeuanganController;
use App\Http\Controllers\Admin\VendorMeetingController as AdminVendorMeetingController;
use App\Http\Controllers\Admin\PengaturanController as AdminPengaturanController;
use App\Http\Controllers\Customer\PembayaranController as CustomerPembayaranController;
use App\Http\Controllers\BuktiPembayaranController;
use App\Http\Controllers\Customer\BookingController as CustomerBookingController;
use App\Http\Controllers\Customer\KendalaController as CustomerKendalaController;
use App\Http\Controllers\Admin\KendalaController as AdminKendalaController;
use App\Http\Controllers\Customer\ChatController as CustomerChatController;
use App\Http\Controllers\Customer\ProfileController as CustomerProfileController;
use App\Http\Controllers\Api\BookingCancellationController;
use App\Http\Controllers\Api\KorlapBookingController;
use App\Http\Controllers\Api\KorlapVendorController;
use App\Http\Controllers\Customer\ItemTambahanController as CustomerItemTambahanController;
use App\Http\Controllers\Customer\PesananController as CustomerPesananController;
use App\Http\Controllers\Lapangan\DashboardController as LapanganDashboardController;
use App\Http\Controllers\Lapangan\PesananController as LapanganPesananController;
use App\Http\Controllers\Lapangan\JadwalController as LapanganJadwalController;
use App\Http\Controllers\Lapangan\VendorMeetingController as LapanganVendorMeetingController;
use App\Http\Controllers\Lapangan\TugasController as LapanganTugasController;
use App\Http\Controllers\Lapangan\LaporanController as LapanganLaporanController;
use App\Http\Controllers\Lapangan\VendorController as LapanganVendorController;
use App\Http\Controllers\Lapangan\ChatController as LapanganChatController;
use App\Http\Controllers\Lapangan\RealisasiController as LapanganRealisasiController;
use App\Http\Controllers\Lapangan\PengaturanController as LapanganPengaturanController;
use App\Http\Controllers\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\NotificationCenterController;

// Halaman depan
Route::get('/', [HomeController::class, 'index'])->name('home');

// Halaman publik
Route::get('/paket', [PageController::class, 'paket'])->name('paket');
Route::get('/vendor', [PageController::class, 'vendor'])->name('vendor');
Route::get('/vendor/{vendor}', [PageController::class, 'vendorDetail'])->name('vendor.detail');
Route::get('/tentang-kami', [PageController::class, 'about'])->name('about');
Route::get('/blog', [PageController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [PageController::class, 'blogShow'])->name('blog.show');
Route::get('/kontak', [PageController::class, 'contact'])->name('contact');
Route::post('/kontak', [PageController::class, 'contactStore'])->name('contact.store');

// Autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::patch('/notifications/{notification}/read', [NotificationCenterController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationCenterController::class, 'markAllRead'])->name('notifications.read-all');

    // ===== REAL-TIME NOTIFICATION POLLING API =====
    Route::prefix('api/notifications')->group(function () {
        Route::get('/poll', [\App\Http\Controllers\NotificationController::class, 'pollNotifications'])->name('api.notifications.poll');
        Route::get('/count', [\App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('api.notifications.count');
        Route::post('/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('api.notifications.mark-read');
        Route::post('/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('api.notifications.read-all');
        Route::delete('/{notification}', [\App\Http\Controllers\NotificationController::class, 'delete'])->name('api.notifications.delete');
    });
});

// Bukti transfer (admin & customer — lewat Laravel, tidak bergantung symlink/APP_URL)
Route::middleware('auth')->get('/bukti-pembayaran/{konfirmasi}', [BuktiPembayaranController::class, 'show'])
    ->name('pembayaran.bukti');

// Panel Admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', fn () => redirect()->route('login'))->name('login');

    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        
        // ✅ Admin Profile Management
        Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'show'])->name('profile.show');
        Route::patch('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::get('/profile/current', [\App\Http\Controllers\Admin\ProfileController::class, 'getCurrentProfile'])->name('profile.current');
        
        Route::get('/booking', [AdminPesananController::class, 'index'])->name('booking');
        Route::get('/booking/{pesanan}', [AdminPesananController::class, 'show'])->name('booking.show');
        Route::patch('/booking/{pesanan}/status', [AdminPesananController::class, 'updateStatus'])->name('booking.status');
        Route::delete('/booking/{pesanan}', [AdminPesananController::class, 'destroy'])->name('booking.destroy');
        
        // ✅ Payment Verification Routes - Conditional Payment Workflow
        Route::post('/booking/{pesanan}/verify-dp', [AdminPesananController::class, 'verifyDP'])->name('booking.verify_dp');
        Route::post('/booking/{pesanan}/verify-pelunasan', [AdminPesananController::class, 'verifyPelunasan'])->name('booking.verify_pelunasan');
        Route::post('/booking/{pesanan}/verify-lapangan', [AdminPesananController::class, 'verifyLapangan'])->name('booking.verify_lapangan');
        Route::post('/booking/{pesanan}/tugas/{tugas}/verify', [AdminPesananController::class, 'verifyTask'])->name('booking.tugas.verify');
        Route::post('/booking/{pesanan}/tugas/{tugas}/force-finish', [AdminPesananController::class, 'forceFinishTask'])->name('booking.tugas.force_finish');
        Route::post('/booking/{pesanan}/reject-payment', [AdminPesananController::class, 'rejectPayment'])->name('booking.reject_payment');
        Route::post('/booking/{pesanan}/approve-cancellation', [AdminPesananController::class, 'approveCancellation'])->name('booking.approve_cancellation');
        Route::get('/booking/{pesanan}/refund/preview', [\App\Http\Controllers\Admin\RefundController::class, 'preview'])->name('booking.refund.preview');
        Route::post('/booking/{pesanan}/refund/process', [\App\Http\Controllers\Admin\RefundController::class, 'process'])->name('booking.refund.process');
        Route::get('/booking/pending-cancellations', [\App\Http\Controllers\Admin\RefundController::class, 'pendingIndex'])->name('booking.pending_cancellations');
        Route::post('/booking/{pesanan}/refund/approve', [\App\Http\Controllers\Admin\RefundController::class, 'approve'])->name('booking.refund.approve');
        Route::post('/booking/{pesanan}/refund/deny', [\App\Http\Controllers\Admin\RefundController::class, 'deny'])->name('booking.refund.deny');
        
        // ✅ Refund Routes - Auto-calculate & notify multi-role
        Route::get('/refund/eligible', [\App\Http\Controllers\Admin\RefundController::class, 'listEligible'])->name('refund.eligible');
        Route::get('/refund/{pesanan}/preview', [\App\Http\Controllers\Admin\RefundController::class, 'preview'])->name('refund.preview');
        Route::get('/refund/{pesanan}/status', [\App\Http\Controllers\Admin\RefundController::class, 'status'])->name('refund.status');
        Route::post('/refund/{pesanan}/process', [\App\Http\Controllers\Admin\RefundController::class, 'process'])->name('refund.process');
        Route::patch('/booking/{pesanan}/addons/{addon}/pay', [AdminPesananController::class, 'payAddon'])->name('booking.addon.pay');
        Route::post('/booking/{pesanan}/item-tambahan/{itemTambahan}/approve', [AdminPesananController::class, 'approveItemTambahan'])->name('booking.item-tambahan.approve');
        Route::post('/booking/{pesanan}/item-tambahan/{itemTambahan}/reject', [AdminPesananController::class, 'rejectItemTambahan'])->name('booking.item-tambahan.reject');
        Route::patch('/booking/{pesanan}/item-tambahan/{itemTambahan}/pay', [AdminPesananController::class, 'payItemTambahan'])->name('booking.item-tambahan.pay');
        
        // Rundown Management
        Route::post('/booking/{pesanan}/rundown', [AdminPesananController::class, 'storeRundown'])->name('booking.rundown.store');
        Route::patch('/booking/{pesanan}/rundown/{rundown}', [AdminPesananController::class, 'updateRundown'])->name('booking.rundown.update');
        Route::delete('/booking/{pesanan}/rundown/{rundown}', [AdminPesananController::class, 'destroyRundown'])->name('booking.rundown.destroy');
        
        // Tambah Jadwal Meeting Vendor dari halaman detail booking
        Route::post('/booking/{pesanan}/meetings', [AdminPesananController::class, 'storeVendorMeeting'])->name('meetings.store');
        
        Route::resource('paket', AdminPaketController::class)->except(['show']);
        Route::get('/vendor/cards', [AdminVendorController::class, 'cards'])->name('vendor.cards');
        Route::get('/vendor/{vendor}/detail', [AdminVendorController::class, 'detail'])->name('vendor.detail');
        Route::resource('vendor', AdminVendorController::class)->except(['show']);
        
        // ✅ Vendor Meetings Management - Admin Panel
        Route::get('/vendor-meetings', [AdminVendorMeetingController::class, 'index'])->name('vendor-meetings.index');
        Route::get('/vendor-meetings/create', [AdminVendorMeetingController::class, 'create'])->name('vendor-meetings.create');
        Route::post('/vendor-meetings', [AdminVendorMeetingController::class, 'storeMeeting'])->name('vendor-meetings.store');
        Route::get('/vendor-meetings/{vendorMeeting}', [AdminVendorMeetingController::class, 'show'])->name('vendor-meetings.show');
        Route::get('/vendor-meetings/{vendorMeeting}/edit', [AdminVendorMeetingController::class, 'edit'])->name('vendor-meetings.edit');
        Route::patch('/vendor-meetings/{vendorMeeting}', [AdminVendorMeetingController::class, 'update'])->name('vendor-meetings.update');
        Route::delete('/vendor-meetings/{vendorMeeting}', [AdminVendorMeetingController::class, 'destroy'])->name('vendor-meetings.destroy');
        Route::patch('/vendor-meetings/{vendorMeeting}/status', [AdminVendorMeetingController::class, 'updateStatus'])->name('vendor-meetings.updateStatus');

        Route::prefix('jadwal-acara')->name('jadwal-acara.')->group(function () {
            Route::get('/rundown', [AdminController::class, 'jadwal'])->name('rundown');
            Route::get('/meeting-vendor', [AdminVendorMeetingController::class, 'index'])->name('meeting-vendor');
        });
        Route::get('/jadwal', fn () => redirect()->route('admin.jadwal-acara.rundown'))->name('jadwal');
        Route::get('/laporan-keuangan', [AdminLaporanKeuanganController::class, 'index'])->name('laporan-keuangan');
        Route::get('/laporan-keuangan/export', [AdminLaporanKeuanganController::class, 'exportData'])->name('laporan-keuangan.export');
        Route::get('/laporan-keuangan/konfirmasi/{konfirmasi}/detail', [AdminLaporanKeuanganController::class, 'detail'])->name('laporan-keuangan.detail');
        Route::get('/pembayaran', fn () => redirect()->route('admin.laporan-keuangan'))->name('pembayaran');
        Route::get('/pembayaran/konfirmasi/{konfirmasi}', [AdminPembayaranController::class, 'show'])->name('pembayaran.show');
        Route::post('/pembayaran/verify/{konfirmasi}', [AdminPembayaranController::class, 'verify'])->name('pembayaran.verify');
        Route::post('/pembayaran/konfirmasi/{konfirmasi}/setujui', [AdminPembayaranController::class, 'approve'])->name('pembayaran.approve');
        Route::post('/pembayaran/konfirmasi/{konfirmasi}/tolak', [AdminPembayaranController::class, 'reject'])->name('pembayaran.reject');
        Route::post('/booking/{pesanan}/operasional', [AdminOperasionalController::class, 'store'])->name('booking.operasional.store');
        Route::patch('/operasional/{operasional}/status', [AdminOperasionalController::class, 'updateStatus'])->name('operasional.status');
        Route::get('/chat', [AdminChatController::class, 'index'])->name('chat');
        Route::get('/chat/{pesanan}', [AdminChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{pesanan}', [AdminChatController::class, 'store'])->name('chat.store');
        Route::post('/chat/{pesanan}/send', [AdminChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/chat/{pesanan}/internal-note', [AdminChatController::class, 'storeInternalNote'])->name('chat.internal-note');
        // Cetak Kwitansi (PDF) - Admin
        Route::get('/booking/{pesanan}/kwitansi', [\App\Http\Controllers\InvoicePdfController::class, 'downloadInvoice'])->name('booking.download_invoice');
        Route::get('/pengaturan', [AdminPengaturanController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan', [AdminPengaturanController::class, 'update'])->name('pengaturan.update');
        Route::patch('/kendala/{kendala}/status', [AdminKendalaController::class, 'updateStatus'])->name('kendala.status');
        Route::get('/vendor-keuangan', [\App\Http\Controllers\Admin\VendorKeuanganController::class, 'index'])->name('vendor-keuangan.index');
        Route::get('/vendor-keuangan/{pesanan}', [\App\Http\Controllers\Admin\VendorKeuanganController::class, 'show'])->name('vendor-keuangan.show');
        Route::post('/vendor-keuangan/{pesanan}', [\App\Http\Controllers\Admin\VendorKeuanganController::class, 'store'])->name('vendor-keuangan.store');
        Route::put('/vendor-anggaran/{anggaran}', [\App\Http\Controllers\Admin\VendorKeuanganController::class, 'update'])->name('vendor-keuangan.update');
        Route::delete('/vendor-anggaran/{anggaran}', [\App\Http\Controllers\Admin\VendorKeuanganController::class, 'destroy'])->name('vendor-keuangan.destroy');
        Route::patch('/vendor-anggaran/{anggaran}/pembayaran', [\App\Http\Controllers\Admin\VendorKeuanganController::class, 'updatePaymentStatus'])->name('vendor-keuangan.payment');
    });
});

// Panel Tim Lapangan
Route::prefix('lapangan')->name('lapangan.')->group(function () {
    Route::get('/login', fn () => redirect()->route('login'))->name('login');

    Route::middleware(['auth', 'lapangan'])->group(function () {
        Route::get('/dashboard', [LapanganDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/refresh', [LapanganDashboardController::class, 'refresh'])->name('dashboard.refresh');
        Route::get('/pesanan', [LapanganPesananController::class, 'index'])->name('pesanan.index');
        Route::get('/pesanan/{pesanan}', [LapanganPesananController::class, 'show'])->name('pesanan.show');
        Route::patch('/pesanan/{pesanan}/progress', [LapanganPesananController::class, 'updateProgress'])->middleware('payment.deadline')->name('pesanan.progress');
        Route::post('/pesanan/{pesanan}/vendor-status', [LapanganPesananController::class, 'updateVendorStatus'])->middleware(['schedule.access:full', 'payment.deadline'])->name('pesanan.vendor-status');
        Route::get('/pesanan/{pesanan}/realisasi', [LapanganRealisasiController::class, 'index'])->name('realisasi.index');
        Route::post('/pesanan/{pesanan}/realisasi/{operasional}', [LapanganRealisasiController::class, 'store'])->middleware('schedule.access:full')->name('realisasi.store');
        Route::post('/pesanan/{pesanan}/laporan', [LapanganPesananController::class, 'storeLaporan'])->middleware('payment.deadline')->name('pesanan.laporan');
        Route::post('/pesanan/{pesanan}/complete', [LapanganPesananController::class, 'complete'])->middleware('payment.deadline')->name('pesanan.complete');
        Route::get('/jadwal', [LapanganJadwalController::class, 'index'])->name('jadwal');
        Route::get('/laporan', [LapanganLaporanController::class, 'index'])->name('laporan');
        Route::get('/vendor', [LapanganVendorController::class, 'index'])->name('vendor');
        Route::get('/vendor/{vendor}', [LapanganVendorController::class, 'show'])->name('vendor.show');
        Route::get('/vendor-hari-ini', [LapanganVendorController::class, 'vendorHariIni'])->name('vendor.hari-ini');
        Route::post('/vendor/{pesanan}/{vendor}/status', [LapanganVendorController::class, 'updateStatus'])->name('vendor.update-status');
        Route::get('/chat', [LapanganChatController::class, 'index'])->name('chat');
        Route::get('/chat/{pesanan}', [LapanganChatController::class, 'show'])->name('chat.show');
        Route::post('/chat/{pesanan}/send', [LapanganChatController::class, 'sendMessage'])->name('chat.send');
        Route::post('/chat/{pesanan}/internal-note', [LapanganChatController::class, 'storeInternalNote'])->name('chat.internal-note');
        Route::post('/chat/{pesanan}/mark-read', [LapanganChatController::class, 'markAsRead'])->name('chat.markAsRead');
        Route::get('/pengaturan', [LapanganPengaturanController::class, 'index'])->name('pengaturan');
        Route::put('/pengaturan', [LapanganPengaturanController::class, 'update'])->name('pengaturan.update');
        Route::get('/tugas/pesanan/{pesanan}/vendors', [LapanganTugasController::class, 'vendorsForPesanan'])->name('tugas.pesanan.vendors');
        Route::post('/tugas/{tugas}/verify', [LapanganTugasController::class, 'verifyComplete'])->middleware('payment.deadline')->name('tugas.verify');
        Route::resource('tugas', LapanganTugasController::class);

        // ✅ Vendor Meetings Routes for Lapangan (Korlap)
        Route::post('/vendor-meetings', [LapanganVendorMeetingController::class, 'store'])->name('vendor-meetings.store');
        Route::get('/vendor-meetings/{vendorMeeting}', [LapanganVendorMeetingController::class, 'show'])->name('vendor-meetings.show');
        Route::post('/vendor-meetings/{vendorMeeting}/complete', [LapanganVendorMeetingController::class, 'complete'])->name('vendor-meetings.complete');
        Route::patch('/vendor-meetings/{vendorMeeting}/status', [LapanganVendorMeetingController::class, 'updateStatus'])->name('vendor-meetings.updateStatus');

        // AJAX endpoints for Kanban and real-time features
        Route::patch('/tugas/{tugas}/status', [LapanganTugasController::class, 'updateStatus'])->middleware('payment.deadline')->name('tugas.updateStatus');
        Route::patch('/tugas/{tugas}/checklists/{checklist}', [LapanganTugasController::class, 'updateChecklist'])->middleware('payment.deadline')->name('tugas.updateChecklist');
        Route::get('/tugas/{tugas}/detail', [LapanganTugasController::class, 'detail'])->name('tugas.detail');

        // AJAX endpoints for Laporan/Reporting
        Route::get('/laporan/metrics', [LapanganLaporanController::class, 'metrics'])->name('laporan.metrics');
        Route::get('/laporan/progress', [LapanganLaporanController::class, 'progressByPesanan'])->name('laporan.progress');
        Route::post('/laporan/pesanan/{pesanan}/vendor/{vendor}/confirm-attendance', [LapanganLaporanController::class, 'confirmAttendance'])->name('laporan.attendance.confirm');
        Route::post('/laporan/kendala', [LapanganLaporanController::class, 'storeKendala'])->name('laporan.kendala.store');
        Route::patch('/laporan/kendala/{kendala}/status', [LapanganLaporanController::class, 'updateKendalaStatus'])->name('laporan.kendala.status');
        Route::post('/laporan/catatan', [LapanganLaporanController::class, 'updateCatatan'])->name('laporan.catatan.update');
        Route::get('/laporan/kendala/{pesanan}', [LapanganLaporanController::class, 'kendalaList'])->name('laporan.kendala.list');
    });
});

// API Korlap — daftar & detail pemesanan (filter search/status/date)
Route::middleware(['auth', 'lapangan'])->prefix('api/korlap')->name('api.korlap.')->group(function () {
    Route::get('/bookings', [KorlapBookingController::class, 'index'])->name('bookings.index');
    Route::get('/bookings/{pesanan}', [KorlapBookingController::class, 'show'])->name('bookings.show');
    Route::get('/vendors', [KorlapVendorController::class, 'index'])->name('vendors.index');
    Route::get('/vendors/{vendor}', [KorlapVendorController::class, 'show'])->name('vendors.show');
});

// Anti double-booking: tanggal terisi (DP / lunas)
Route::middleware(['auth', 'client'])->get('/bookings/disabled-dates', [CustomerBookingController::class, 'disabledDates'])
    ->name('bookings.disabled-dates');

// Pembatalan pemesanan (client & admin)
Route::middleware('auth')->match(['post', 'patch'], '/api/bookings/{pesanan}/cancel', [BookingCancellationController::class, 'cancel'])
    ->name('api.bookings.cancel');

// Redirect URL lama /customer/* → /client/*
Route::get('/customer/{path?}', function (?string $path = '') {
    $target = '/client'.($path !== '' && $path !== null ? '/'.ltrim($path, '/') : '');

    return redirect($target, 301);
})->where('path', '.*');

// Panel Client (wajib login)
Route::middleware(['auth', 'client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/booking/buat', [CustomerBookingController::class, 'create'])->name('booking.create');
    Route::get('/api/paket/{paket}/defaults', [CustomerBookingController::class, 'paketDefaults'])->name('api.paket.defaults');
    Route::get('/api/paket/{paket}/vendors', [CustomerBookingController::class, 'paketVendors'])->name('api.paket.vendors');
    Route::post('/booking', [CustomerBookingController::class, 'store'])->name('booking.store');
    Route::get('/chat', [CustomerChatController::class, 'index'])->name('chat');
    Route::get('/chat/{pesanan}', [CustomerChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{pesanan}', [CustomerChatController::class, 'store'])->name('chat.store');
    Route::get('/profil', [CustomerProfileController::class, 'show'])->name('profile');
    Route::get('/pengaturan', [CustomerProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/pengaturan', [CustomerProfileController::class, 'update'])->name('profile.update');
    Route::get('/pembayaran', [CustomerController::class, 'pembayaran'])->name('pembayaran');
    Route::get('/pembayaran/pesanan/{pesanan}', [CustomerController::class, 'pembayaran'])->name('pembayaran.pesanan');
    Route::get('/pembayaran/konfirmasi/{invoice}', [CustomerPembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran/konfirmasi/{invoice}', [CustomerPembayaranController::class, 'store'])->name('pembayaran.store');
    Route::get('/pesanan', [CustomerController::class, 'pesanan'])->name('pesanan');
    Route::get('/pesanan/detail/{id}', [CustomerController::class, 'detailPesanan'])->name('pesanan_detail');
    Route::get('/pesanan/{pesanan}/calendar.ics', [\App\Http\Controllers\CalendarController::class, 'downloadIcs'])->name('calendar.download');
    Route::post('/pesanan/{pesanan}/kendala', [CustomerKendalaController::class, 'store'])->name('pesanan.kendala.store');
    Route::get('/pesanan/{pesanan}/kendala', [CustomerKendalaController::class, 'index'])->name('pesanan.kendala.index');
    Route::post('/pesanan/{pesanan}/batalkan', [CustomerPesananController::class, 'requestCancellation'])->name('pesanan.batalkan');
    Route::post('/pesanan/{pesanan}/request-cancellation', [CustomerPesananController::class, 'requestCancellation'])->name('pesanan.request_cancellation');
    Route::post('/api/client/tambahan', [CustomerItemTambahanController::class, 'store'])->name('api.client.tambahan');
    Route::post('/api/client/tambahan/{pesanan}', [CustomerItemTambahanController::class, 'store'])->name('api.client.tambahan.pesanan');
    // Alias nama route lama (view / integrasi lama memakai "customer" bukan "client")
    Route::post('/api/customer/tambahan/{pesanan}', [CustomerItemTambahanController::class, 'store'])->name('api.customer.tambahan.pesanan');
    Route::post('/pesanan/{pesanan}/addons', [CustomerPesananController::class, 'storeAddon'])->name('pesanan.addon.store');
    Route::post('/review/{vendor}', [CustomerReviewController::class, 'store'])->name('review.store');
    Route::put('/review/{review}', [CustomerReviewController::class, 'update'])->name('review.update');
    Route::delete('/review/{review}', [CustomerReviewController::class, 'destroy'])->name('review.destroy');
    // Booking-level review (client reviews the whole booking)
    Route::post('/pesanan/{pesanan}/review', [\App\Http\Controllers\Customer\BookingReviewController::class, 'store'])->name('pesanan.review.store');
    Route::put('/pesanan/review/{bookingReview}', [\App\Http\Controllers\Customer\BookingReviewController::class, 'update'])->name('pesanan.review.update');
    Route::delete('/pesanan/review/{bookingReview}', [\App\Http\Controllers\Customer\BookingReviewController::class, 'destroy'])->name('pesanan.review.destroy');
    Route::get('/jadwal', [CustomerController::class, 'jadwal'])->name('jadwal');
    Route::get('/vendor-meetings', [\App\Http\Controllers\Customer\VendorMeetingController::class, 'index'])->name('vendor-meetings.index');
    Route::get('/invoice/{id}', [CustomerController::class, 'invoice'])->name('invoice');
    // Cetak Kwitansi (PDF) - Client (pemilik booking)
    Route::get('/pesanan/{pesanan}/kwitansi', [\App\Http\Controllers\InvoicePdfController::class, 'downloadInvoice'])->name('pesanan.download_invoice');
});
