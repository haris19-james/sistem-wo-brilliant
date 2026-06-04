<?php

/**
 * CONTOH IMPLEMENTASI - Integration di AdminPesananController
 * 
 * File ini menunjukkan cara mengintegrasikan RefundService dan NotificationService
 * ke dalam controller existing (AdminPesananController).
 * 
 * Silakan copy-paste method yang sesuai dengan kebutuhan Anda.
 */

namespace App\Http\Controllers\Admin;

use App\Models\Pesanan;
use App\Models\PembayaranKonfirmasi;
use App\Services\RefundService;
use App\Services\NotificationCenterService;
use Illuminate\Http\Request;

/**
 * ========================= CONTOH 1: Integration di Booking Cancellation =========================
 */
class AdminPesananControllerIntegrationExample extends \App\Http\Controllers\Controller
{
    protected $refundService;
    protected $notificationService;

    public function __construct(
        RefundService $refundService,
        NotificationCenterService $notificationService
    ) {
        $this->refundService = $refundService;
        $this->notificationService = $notificationService;
    }

    /**
     * ✅ EXAMPLE 1: Admin Cancel Booking dengan Refund Otomatis
     * 
     * POST /admin/booking/{pesanan}/cancel-and-refund
     * 
     * Flow:
     * 1. Admin mengisi form: penalty_percent (0-100%) dan alasan_pembatalan
     * 2. System preview refund amount
     * 3. Admin confirm
     * 4. System otomatis:
     *    - Hitung refund final
     *    - Update pesanan & invoice status
     *    - Kirim notifikasi ke Admin, Client, Korlap
     */
    public function cancelAndRefund(Request $request, Pesanan $pesanan)
    {
        // Validasi input
        $validated = $request->validate([
            'penalty_percent' => 'required|integer|min:0|max:100',
            'alasan_pembatalan' => 'nullable|string|max:500',
            'confirm' => 'required|boolean',
        ]);

        // Verifikasi pesanan bisa di-refund
        if (!in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'])) {
            return redirect()->back()->with('error', 
                'Pesanan harus dalam status DP Paid atau Fully Paid untuk di-refund');
        }

        try {
            // GUNAKAN REFUND SERVICE - Ini sudah handle semuanya!
            $result = $this->refundService->processRefund(
                pesananId: $pesanan->id,
                penaltyPercent: $validated['penalty_percent'],
                alasanRefund: $validated['alasan_pembatalan'] ?? 'Pembatalan oleh Admin'
            );

            if ($result['success']) {
                // Notifikasi sudah otomatis dikirim oleh RefundService!
                // Tapi kita bisa tambah log di sistem Admin jika perlu

                return redirect()->back()->with('success', 
                    "Refund berhasil diproses!\n" .
                    "DP Amount: Rp " . number_format($result['data']['dp_amount'], 0) . "\n" .
                    "Potongan: Rp " . number_format($result['data']['penalty_amount'], 0) . "\n" .
                    "Refund Final: Rp " . number_format($result['data']['final_refund'], 0)
                );
            } else {
                return redirect()->back()->with('error', $result['message']);
            }

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * ✅ EXAMPLE 2: Approval Payment DP dengan Notifikasi Multi-Role
     * 
     * POST /admin/booking/{pesanan}/approve-dp-payment
     * 
     * Ketika Admin approve pembayaran DP, otomatis kirim notifikasi ke 3 role:
     * - Admin: Pembayaran DP dikonfirmasi
     * - Client: Pembayaran DP Anda berhasil dikonfirmasi
     * - Korlap: Booking baru ditugaskan, mulai persiapan
     */
    public function approveDPPayment(Request $request, Pesanan $pesanan)
    {
        // Validasi
        if ($pesanan->status_pembayaran !== 'unpaid') {
            return redirect()->back()->with('warning', 'Booking sudah diproses pembayaran');
        }

        try {
            $invoice = $pesanan->invoices()->first();
            
            // Update status pembayaran
            $pesanan->update([
                'status_pembayaran' => 'dp_paid',
                'status_booking' => 'approved_dp',
            ]);

            // =================== KIRIM NOTIFIKASI MULTI-ROLE ===================
            $this->notificationService->sendNotification(
                bookingId: $pesanan->id,
                eventType: 'payment_approved',
                message: sprintf(
                    'Pembayaran DP untuk booking "%s" (%s) sebesar Rp %s telah dikonfirmasi. Akses jadwal meeting tersedia.',
                    $pesanan->nomor_pesanan,
                    $pesanan->nama_pasangan,
                    number_format($invoice->dp_dibayar ?? 0, 0, ',', '.')
                ),
                targetRoles: ['admin', 'client', 'korlap'],
                linkRedirect: route('customer.pesanan.show', $pesanan->id),
                priority: 'normal',
                metadata: [
                    'booking_id' => $pesanan->id,
                    'payment_type' => 'dp',
                    'payment_amount' => $invoice->dp_dibayar ?? 0,
                ]
            );
            // =====================================================================

            return redirect()->back()->with('success', 
                'Pembayaran DP dikonfirmasi. Notifikasi sudah dikirim ke semua pihak.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * ✅ EXAMPLE 3: Approval Payment Pelunasan dengan Notifikasi
     * 
     * POST /admin/booking/{pesanan}/approve-full-payment
     * 
     * Ketika Admin approve pelunasan, kirim notifikasi ke semua role bahwa
     * booking sekarang fully paid dan akses jadwal penuh tersedia untuk korlap.
     */
    public function approveFullPayment(Request $request, Pesanan $pesanan)
    {
        if ($pesanan->status_pembayaran !== 'dp_paid') {
            return redirect()->back()->with('warning', 
                'Pelunasan hanya bisa dikonfirmasi setelah DP dikonfirmasi');
        }

        try {
            $invoice = $pesanan->invoices()->first();
            
            // Update status
            $pesanan->update([
                'status_pembayaran' => 'fully_paid',
                'status_booking' => 'approved_lunas',
                'akses_jadwal' => 'full',
                'fully_paid_by_admin_at' => now(),
            ]);

            // =================== NOTIFIKASI: FULL PAYMENT APPROVED ===================
            $this->notificationService->sendNotification(
                bookingId: $pesanan->id,
                eventType: 'payment_approved',
                message: sprintf(
                    'Pelunasan booking "%s" untuk %s pada %s berhasil dikonfirmasi. Akses jadwal penuh tersedia. Persiapan final bisa dimulai.',
                    $pesanan->nomor_pesanan,
                    $pesanan->nama_pasangan,
                    $pesanan->tanggal_acara->format('d M Y')
                ),
                targetRoles: ['admin', 'client', 'korlap'],
                linkRedirect: route('customer.pesanan.show', $pesanan->id),
                priority: 'high',
                metadata: [
                    'booking_id' => $pesanan->id,
                    'payment_type' => 'pelunasan',
                    'total_paid' => $invoice->total_biaya ?? 0,
                ]
            );
            // =========================================================================

            return redirect()->back()->with('success', 
                'Pelunasan dikonfirmasi. Booking siap untuk dijalankan.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * ✅ EXAMPLE 4: Reject Payment dengan Notifikasi Urgent
     * 
     * POST /admin/booking/{pesanan}/reject-payment
     * 
     * Ketika Admin reject pembayaran, kirim notifikasi URGENT ke client
     * agar tahu alasan penolakan dan bisa submit ulang dengan data benar.
     */
    public function rejectPaymentConfirmation(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'alasan_penolakan' => 'required|string|max:500',
        ]);

        try {
            // Update status kembali ke unpaid
            $pesanan->update([
                'status_pembayaran' => 'unpaid',
                'catatan_pembayaran' => $validated['alasan_penolakan'],
            ]);

            // =================== NOTIFIKASI URGENT: PAYMENT REJECTED ===================
            $this->notificationService->sendNotification(
                bookingId: $pesanan->id,
                eventType: 'payment_rejected',
                message: sprintf(
                    'Pembayaran untuk booking "%s" ditolak: %s. Silakan periksa dan submit ulang.',
                    $pesanan->nomor_pesanan,
                    $validated['alasan_penolakan']
                ),
                targetRoles: ['client', 'admin'], // Alert client & admin
                linkRedirect: route('customer.pesanan.show', $pesanan->id),
                priority: 'high', // Urgent!
                metadata: [
                    'booking_id' => $pesanan->id,
                    'rejection_reason' => $validated['alasan_penolakan'],
                ]
            );
            // ==========================================================================

            return redirect()->back()->with('success', 
                'Pembayaran ditolak. Notifikasi urgent sudah dikirim ke client.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * ✅ EXAMPLE 5: Assign Korlap dengan Notifikasi
     * 
     * POST /admin/booking/{pesanan}/assign-korlap
     * 
     * Ketika Admin assign korlap ke booking, kirim notifikasi ke:
     * - Admin: Korlap sudah ditugaskan
     * - Korlap: Booking baru untuk Anda! Mulai persiapan.
     */
    public function assignKorlap(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'korlap_id' => 'required|exists:users,id',
        ]);

        try {
            $korlap = \App\Models\User::findOrFail($validated['korlap_id']);

            // Update pesanan
            $pesanan->update(['korlap_id' => $korlap->id]);

            // =================== NOTIFIKASI: KORLAP ASSIGNED ===================
            $this->notificationService->sendNotification(
                bookingId: $pesanan->id,
                eventType: 'booking_assigned',
                message: sprintf(
                    'Booking baru ditugaskan untuk Anda: "%s" (%s) pada %s. Silakan mulai persiapan lapangan.',
                    $pesanan->nomor_pesanan,
                    $pesanan->nama_pasangan,
                    $pesanan->tanggal_acara->format('d M Y')
                ),
                targetRoles: ['korlap', 'admin'],
                linkRedirect: route('lapangan.pesanan.show', $pesanan->id),
                priority: 'high',
                metadata: [
                    'booking_id' => $pesanan->id,
                    'korlap_id' => $korlap->id,
                    'event_date' => $pesanan->tanggal_acara,
                ]
            );
            // ====================================================================

            return redirect()->back()->with('success', 
                "Korlap {$korlap->name} ditugaskan. Notifikasi sudah dikirim.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * ✅ EXAMPLE 6: Approve Cancellation Request dengan Refund
     * 
     * POST /admin/booking/{pesanan}/approve-cancellation
     * 
     * Ketika client request pembatalan dan admin approve:
     * 1. Process refund dengan penalty
     * 2. Notifikasi ke client dengan detail refund
     * 3. Notifikasi ke korlap bahwa booking dibatalkan
     */
    public function approveCancellation(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'penalty_percent' => 'required|integer|min:0|max:100',
            'alasan_admin' => 'nullable|string|max:500',
        ]);

        // Validasi pesanan dalam status pending cancellation
        if ($pesanan->status_pemesanan !== 'pending_cancellation') {
            return redirect()->back()->with('warning', 
                'Booking harus dalam status pending_cancellation');
        }

        try {
            // PROCESS REFUND - Ini otomatis kirim notifikasi!
            $result = $this->refundService->processRefund(
                pesananId: $pesanan->id,
                penaltyPercent: $validated['penalty_percent'],
                alasanRefund: 'Pembatalan disetujui oleh Admin. ' . ($validated['alasan_admin'] ?? '')
            );

            if ($result['success']) {
                // RefundService sudah kirim notifikasi ke admin, client, korlap
                // Tapi kita tambah notifikasi khusus ke korlap tentang pembatalan
                
                $this->notificationService->sendNotification(
                    bookingId: $pesanan->id,
                    eventType: 'booking_cancelled',
                    message: sprintf(
                        'Booking "%s" untuk %s dibatalkan. Refund akan diproses.',
                        $pesanan->nomor_pesanan,
                        $pesanan->nama_pasangan
                    ),
                    targetRoles: ['korlap'],
                    priority: 'normal'
                );

                return redirect()->back()->with('success', 
                    'Pembatalan disetujui. Refund berhasil diproses. Notifikasi dikirim ke semua pihak.');
            }

            return redirect()->back()->with('error', $result['message']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

/**
 * ========================= CONTOH 2: Standalone Service Usage =========================
 */
class BookingServiceIntegrationExample
{
    protected $refundService;
    protected $notificationService;

    public function __construct(
        RefundService $refundService,
        NotificationCenterService $notificationService
    ) {
        $this->refundService = $refundService;
        $this->notificationService = $notificationService;
    }

    /**
     * Complete workflow: Approve booking, update status, notify all roles
     */
    public function approveBookingWorkflow(Pesanan $pesanan)
    {
        try {
            // Step 1: Update status
            $pesanan->update([
                'status_pemesanan' => 'confirmed',
                'status_booking' => 'approved_dp',
            ]);

            // Step 2: Kirim notifikasi ke semua role
            $this->notificationService->sendNotification(
                bookingId: $pesanan->id,
                eventType: 'booking_confirmed',
                message: "Booking {$pesanan->nomor_pesanan} dikonfirmasi. Silakan mulai persiapan.",
                targetRoles: ['admin', 'client', 'korlap'],
                priority: 'high'
            );

            // Step 3: Log aktivitas
            activity()
                ->performedOn($pesanan)
                ->log('Booking approved dan notifikasi dikirim ke Admin, Client, Korlap');

            return [
                'success' => true,
                'message' => 'Workflow completed successfully',
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Cancel booking with automatic refund calculation and notification
     */
    public function cancelBookingWorkflow(Pesanan $pesanan, array $options = [])
    {
        $penaltyPercent = $options['penalty_percent'] ?? 20;
        $reason = $options['reason'] ?? 'Client request';

        // RefundService handle semuanya: hitung, update DB, kirim notifikasi
        return $this->refundService->processRefund(
            pesananId: $pesanan->id,
            penaltyPercent: $penaltyPercent,
            alasanRefund: $reason
        );
    }
}

/**
 * ========================= CONTOH 3: Frontend Integration =========================
 */

/*
// resources/views/admin/booking/cancel-modal.blade.php

@forelse($refundableBookings as $booking)
<div class="modal fade" id="cancelModal{{ $booking->id }}" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5>Cancel Booking & Process Refund</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form id="refundForm{{ $booking->id }}" action="{{ route('admin.booking.cancel-and-refund', $booking) }}" method="POST">
        @csrf
        
        <div class="modal-body">
          <div class="alert alert-info">
            <strong>{{ $booking->nomor_pesanan }}</strong> - {{ $booking->nama_pasangan }}
          </div>

          <div class="mb-3">
            <label for="penalty{{ $booking->id }}" class="form-label">Penalty Percent (%)</label>
            <input type="range" class="form-range" id="penalty{{ $booking->id }}" 
                   name="penalty_percent" min="0" max="100" value="0"
                   onchange="updatePreview({{ $booking->id }})">
            <small class="text-muted">
              DP: <strong id="dpAmount{{ $booking->id }}">Rp 0</strong> | 
              Potongan: <strong id="penalty{{ $booking->id }}-amount" class="text-danger">Rp 0</strong> |
              Refund: <strong id="finalRefund{{ $booking->id }}" class="text-success">Rp 0</strong>
            </small>
          </div>

          <div class="mb-3">
            <label for="alasan" class="form-label">Alasan Pembatalan</label>
            <textarea class="form-control" name="alasan_pembatalan" rows="3"></textarea>
          </div>

          <input type="hidden" name="confirm" value="1">
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">
            Process Refund & Notify
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@empty
<p class="text-muted">Tidak ada booking yang eligible untuk refund</p>
@endforelse

<script>
async function updatePreview(bookingId) {
  const penalty = document.getElementById('penalty' + bookingId).value;
  const response = await fetch(
    `/admin/refund/${bookingId}/preview?penalty_percent=${penalty}`
  );
  const data = await response.json();

  document.getElementById('dpAmount' + bookingId).textContent = 
    'Rp ' + new Intl.NumberFormat('id-ID').format(data.dp_amount);
  document.getElementById('penalty' + bookingId + '-amount').textContent = 
    'Rp ' + new Intl.NumberFormat('id-ID').format(data.penalty_amount);
  document.getElementById('finalRefund' + bookingId).textContent = 
    'Rp ' + new Intl.NumberFormat('id-ID').format(data.final_refund);
}
</script>
*/
