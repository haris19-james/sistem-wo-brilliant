<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\Invoice;
use App\Models\Pesanan;
use App\Models\VendorMeeting;
use App\Services\AgendaGeneratorService;
use App\Services\PaymentDeadlineService;
use App\Services\VendorReviewService;
use App\Support\CustomerPaymentPresenter;
use App\Support\JadwalTerpaduService;
use App\Support\BookingDynamicStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CustomerController extends Controller
{
    public function dashboard()
    {
        if (! session()->pull('skip_expire_sync', false)) {
            Pesanan::expireOverdueBookingsIfDue();
        }

        $user = Auth::user();

        $pesananAktif = Pesanan::with(['paket', 'progress', 'vendorMeetings.korlap'])
            ->where('user_id', $user->id)
            ->whereNotIn('status', [
                BookingDynamicStatus::DB_DIBATALKAN,
                BookingDynamicStatus::DB_SELESAI,
                BookingDynamicStatus::DB_MENUNGGU,
                BookingDynamicStatus::DB_EXPIRED,
            ])
            ->latest()
            ->first();

        if ($pesananAktif) {
            BookingDynamicStatus::sync($pesananAktif);
            $pesananAktif = $pesananAktif->fresh(['paket', 'progress', 'vendorMeetings.korlap']);
            if (in_array($pesananAktif->status, [
                BookingDynamicStatus::DB_EXPIRED,
                BookingDynamicStatus::DB_SELESAI,
                BookingDynamicStatus::DB_DIBATALKAN,
                BookingDynamicStatus::DB_MENUNGGU,
            ], true)) {
                $pesananAktif = null;
            }
        }

        $stats = [
            'total_pesanan' => Pesanan::where('user_id', $user->id)->count(),
            'menunggu' => Pesanan::where('user_id', $user->id)->where('status', 'Menunggu')->count(),
            'berlangsung' => Pesanan::where('user_id', $user->id)->whereIn('status', [
                BookingDynamicStatus::DB_SEDANG,
                BookingDynamicStatus::DB_MENDESAK,
            ])->count(),
            'selesai' => Pesanan::where('user_id', $user->id)->where('status', BookingDynamicStatus::DB_SELESAI)->count(),
            'expired' => Pesanan::where('user_id', $user->id)->where('status', BookingDynamicStatus::DB_EXPIRED)->count(),
        ];

        $progressPersiapan = $pesananAktif?->progress?->persentase ?? 0;

        $notifikasiChat = ChatMessage::whereHas('pesanan', fn ($q) => $q->where('user_id', $user->id))
            ->with(['user', 'pesanan'])
            ->latest()
            ->take(5)
            ->get();

        $pesananTerbaru = Pesanan::with(['paket', 'progress'])
            ->where('user_id', $user->id)
            ->latest()
            ->take(3)
            ->get()
            ->each(fn (Pesanan $p) => BookingDynamicStatus::sync($p));

        // ✅ Ambil vendor meetings mendatang untuk Client jika tabel sudah ada
        $upcomingVendorMeetings = collect();
        if (Schema::hasTable('vendor_meetings')) {
            $upcomingVendorMeetings = VendorMeeting::whereHas('booking', fn ($q) => $q->where('user_id', $user->id))
                ->whereIn('status', ['scheduled', 'ongoing'])
                ->where('meeting_date', '>=', now()->toDateString())
                ->with(['booking', 'korlap'])
                ->orderBy('meeting_date')
                ->orderBy('meeting_time')
                ->take(5)
                ->get();
        }

        // Next event (Pesanan) — used by Upcoming Schedule widget
        $nextEvent = Pesanan::where('user_id', $user->id)
            ->whereNotNull('tanggal_acara')
            ->whereDate('tanggal_acara', '>=', now()->toDateString())
            ->where(function($q){
                $q->where('status_pemesanan', '!=', 'canceled')
                  ->orWhereNull('status_pemesanan');
            })
            ->orderBy('tanggal_acara')
            ->orderBy('jam_acara')
            ->first();

        $deadlineBanner = null;
        if ($pesananAktif) {
            PaymentDeadlineService::syncFor($pesananAktif);
            $deadlineBanner = PaymentDeadlineService::customerBanner($pesananAktif);
        }

        $pendingVendorReviews = app(VendorReviewService::class)->pendingReviewsForUser($user);
        $reviewNotifications = $user->unreadNotifications()
            ->where('type', 'App\Notifications\VendorReviewReminderNotification')
            ->latest()
            ->take(3)
            ->get();

        return view('customer.modules.dashboard', [
            'activeMenu' => 'dashboard',
            'pesananAktif' => $pesananAktif,
            'stats' => $stats,
            'progressPersiapan' => $progressPersiapan,
            'notifikasiChat' => $notifikasiChat,
            'pesananTerbaru' => $pesananTerbaru,
            'upcomingVendorMeetings' => $upcomingVendorMeetings,
            'nextEvent' => $nextEvent,
            'deadlineBanner' => $deadlineBanner,
            'pendingVendorReviews' => $pendingVendorReviews,
            'reviewNotifications' => $reviewNotifications,
        ]);
    }

    public function pesanan()
    {
        Pesanan::expireOverdueBookingsIfDue();

        $daftarPesanan = Pesanan::with(['paket', 'progress'])
            ->where('user_id', Auth::id())
            ->where('status', '!=', 'Dibatalkan')
            ->orderByDesc('created_at')
            ->get()
            ->each(fn (Pesanan $p) => BookingDynamicStatus::sync($p));

        return view('customer.modules.pesanan.index', [
            'activeMenu' => 'pesanan',
            'daftarPesanan' => $daftarPesanan,
        ]);
    }

    public function detailPesanan($id)
    {
        Pesanan::expireOverdueBookingsIfDue();

        $with = ['paket', 'invoices.konfirmasiPending', 'progress', 'rundowns', 'jadwalMeetings', 'invoices', 'vendors', 'laporanLapangans.user'];
        if (Schema::hasTable('vendor_meetings')) {
            $with[] = 'vendorMeetings.korlap';
        }
        if (Schema::hasTable('item_tambahan')) {
            $with[] = 'itemTambahan.invoice';
        } elseif (Schema::hasTable('booking_addons')) {
            $with[] = 'bookingAddons.invoice';
        }

        $pesanan = Pesanan::with($with)
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Jika status DP sudah terverifikasi tetapi agenda otomatis belum dibuat,
        // buat agenda otomatis secara lazy untuk memastikan Client dapat melihat jadwal.
        if ($pesanan->status_pembayaran === 'dp_paid') {
            (new AgendaGeneratorService())->generateAgendas($pesanan);
            $pesanan->refresh();
        }

        BookingDynamicStatus::sync($pesanan);
        $pesanan->refresh();

        $agendas = Schema::hasTable('vendor_meetings')
            ? $pesanan->vendorMeetings()->orderBy('meeting_date', 'asc')->orderBy('meeting_time', 'asc')->get()
            : collect();

        // ✅ Compute refund breakdown untuk display di view
        $refundBreakdown = $this->computeRefundBreakdown($pesanan);

        return view('customer.modules.pesanan.show', [
            'activeMenu' => 'pesanan',
            'pesanan' => $pesanan,
            'agendas' => $agendas,  // ✅ Pass agenda ke view
            'refundBreakdown' => $refundBreakdown,  // ✅ Pass refund breakdown untuk ditampilkan
        ]);
    }

    /**
     * ✅ Compute refund breakdown untuk display di customer dashboard
     * 
     * Menghitung dan prepare data:
     * - DP Amount (dari invoice.dp_dibayar)
     * - Penalty Amount & Percent (dihitung atau dari database)
     * - Final Refund Amount (dari pesanan.jumlah_refund)
     */
    private function computeRefundBreakdown(Pesanan $pesanan): array
    {
        $invoice = $pesanan->invoices()->first();
        
        if (!$invoice || $pesanan->status_pembayaran !== 'refunded') {
            return [];
        }

        $dpAmount = (float) ($invoice->dp_dibayar ?? 0);
        $finalRefund = (float) ($pesanan->jumlah_refund ?? 0);
        $penaltyAmount = $dpAmount - $finalRefund;
        $penaltyPercent = $dpAmount > 0 ? round(($penaltyAmount / $dpAmount) * 100, 2) : 0;
        $refundRate = $dpAmount > 0 ? round(($finalRefund / $dpAmount) * 100, 2) : 0;

        return [
            'invoice_id' => $invoice->id,
            'dp_amount' => $dpAmount,
            'penalty_amount' => $penaltyAmount,
            'penalty_percent' => $penaltyPercent,
            'final_refund' => $finalRefund,
            'refund_rate' => $refundRate,
            'is_no_refund' => $finalRefund == 0,
            'cancellation_date' => $pesanan->dibatalkan_at,
            'cancellation_reason' => $pesanan->alasan_pembatalan,
        ];
    }

    public function pembayaran(Request $request, ?Pesanan $pesanan = null)
    {
        $pesananId = $pesanan?->id ?? $request->integer('pesanan_id') ?: null;

        if ($pesanan && $pesanan->user_id !== Auth::id()) {
            abort(403);
        }

        $invoices = Invoice::with([
            'pesanan.paket',
            'konfirmasiPending',
            'pembayaranKonfirmasis' => fn ($q) => $q->latest()->limit(5),
        ])
            ->whereHas('pesanan', fn ($q) => $q->where('user_id', Auth::id()))
            ->latest()
            ->get();

        $primaryInvoice = CustomerPaymentPresenter::pickPrimaryInvoice($invoices, $pesananId);
        $payment = $primaryInvoice ? CustomerPaymentPresenter::for($primaryInvoice) : null;

        if ($primaryInvoice && ! $primaryInvoice->jatuh_tempo_dp) {
            $primaryInvoice->applyPaymentSchedule();
            $primaryInvoice->save();
        }

        return view('customer.modules.pembayaran.index', [
            'activeMenu' => 'pembayaran',
            'invoices' => $invoices,
            'primaryInvoice' => $primaryInvoice,
            'payment' => $payment,
            'rekening' => config('pembayaran.rekening', []),
            'buktiMaxKb' => (int) config('pembayaran.bukti_max_kb', 10240),
            'uploadMaxPhp' => ini_get('upload_max_filesize'),
            'selectedPesananId' => $pesananId,
        ]);
    }

    public function invoice($id)
    {
        $pesanan = Pesanan::with(['paket', 'invoices', 'user'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $invoice = $pesanan->invoices()->with(['konfirmasiPending', 'pembayaranKonfirmasis' => fn ($q) => $q->latest()])->latest()->first();

        if (! $invoice) {
            return redirect()->route('client.pesanan_detail', $pesanan->id)
                ->with('error', 'Invoice untuk pesanan ini belum tersedia.');
        }

        return view('customer.modules.invoice.show', [
            'activeMenu' => 'pembayaran',
            'pesanan' => $pesanan,
            'invoice' => $invoice,
            'rekening' => config('pembayaran.rekening', []),
        ]);
    }

    public function jadwal(Request $request)
    {
        $data = JadwalTerpaduService::forCustomer(
            Auth::id(),
            $request->integer('pesanan_id') ?: null
        );

        return view('customer.modules.jadwal.index', array_merge([
            'activeMenu' => 'jadwal-rundown',
        ], $data));
    }
}
