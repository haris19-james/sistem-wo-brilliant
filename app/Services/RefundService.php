<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Pesanan;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * RefundService - Menangani logika refund DP dengan perhitungan otomatis dan notifikasi multi-role
 * 
 * Service ini bertanggung jawab untuk:
 * 1. Menghitung jumlah refund berdasarkan penalti
 * 2. Update status pesanan dan invoice
 * 3. Trigger notifikasi ke Admin, Client, dan Korlap
 * 
 * @author WO System
 */
class RefundService
{
    protected $notificationService;

    public function __construct(NotificationCenterService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Process refund untuk booking yang dibatalkan
     * 
     * Menghitung refund otomatis dengan formula:
     * finalRefund = dpAmount - (dpAmount * (penaltyPercent / 100))
     * 
     * @param int $pesananId - ID dari pesanan/booking
     * @param int $penaltyPercent - Persentase potongan (0-100)
     * @param string|null $alasanRefund - Alasan pembatalan refund
     * 
     * @return array - Response dengan detail refund (dp_amount, penalty_amount, final_refund)
     * 
     * @throws \Exception
     */
    public function processRefund(int $pesananId, int $penaltyPercent = 0, ?string $alasanRefund = null, ?string $buktiTransferUrl = null): array
    {
        try {
            // Validasi input
            if ($penaltyPercent < 0 || $penaltyPercent > 100) {
                throw new \Exception('Penalti harus antara 0-100 persen');
            }

            // Ambil pesanan dan invoice
            $pesanan = Pesanan::with('user', 'korlap')->findOrFail($pesananId);
            $invoice = $pesanan->invoices()->first();

            if (!$invoice) {
                throw new \Exception('Invoice tidak ditemukan untuk pesanan ini');
            }

            // Validasi status pembayaran - hanya bisa refund jika sudah bayar DP
            if ($pesanan->status_pembayaran !== 'dp_paid' && $pesanan->status_pembayaran !== 'fully_paid') {
                throw new \Exception('Pesanan belum melakukan pembayaran DP atau pembayaran sudah di-refund sebelumnya');
            }

            // Ambil nilai DP yang sudah dibayarkan
            $dpAmount = (float) $invoice->dp_dibayar;

            if ($dpAmount <= 0) {
                throw new \Exception('Tidak ada DP yang perlu di-refund');
            }

            // Hitung penalti dan refund final
            $penaltyAmount = round($dpAmount * ($penaltyPercent / 100), 2);
            $finalRefund = round($dpAmount - $penaltyAmount, 2);

            // Gunakan transaction untuk konsistensi data
            $refundData = DB::transaction(function () use ($pesanan, $invoice, $dpAmount, $penaltyAmount, $finalRefund, $alasanRefund) {
                // Update pesanan status menjadi refunded
                $pesanan->update([
                    'status_pembayaran' => 'refunded',
                    'status_booking' => 'refunded',
                    'status_pemesanan' => 'completed',
                    'jumlah_refund' => $finalRefund,
                    'alasan_pembatalan' => $alasanRefund ?? 'Refund DP',
                    'dibatalkan_at' => now(),
                    'waktu_transfer' => now(),
                    'bukti_transfer_url' => $buktiTransferUrl,
                ]);

                // Update invoice status
                $invoice->update([
                    'status' => 'Refund',
                    'sisa_pembayaran' => 0, // Tidak ada sisa pembayaran
                ]);

                // Log refund transaction
                Log::info('Refund processed', [
                    'pesanan_id' => $pesanan->id,
                    'dp_amount' => $dpAmount,
                    'penalty_percent' => $penaltyAmount,
                    'final_refund' => $finalRefund,
                    'processed_by' => auth()?->id(),
                    'timestamp' => now(),
                ]);

                return [
                    'pesanan_id' => $pesanan->id,
                    'pesanan_number' => $pesanan->nomor_pesanan,
                    'client_name' => $pesanan->user->name,
                    'client_id' => $pesanan->user_id,
                    'korlap_id' => $pesanan->korlap_id,
                    'admin_id' => auth()->id(),
                    'dp_amount' => $dpAmount,
                    'penalty_percent' => $penaltyPercent,
                    'penalty_amount' => $penaltyAmount,
                    'final_refund' => $finalRefund,
                    'acara_tanggal' => $pesanan->tanggal_acara,
                    'alasan' => $alasanRefund,
                ];
            });

            // Kirim notifikasi multi-role SETELAH refund berhasil diproses
            $this->sendRefundNotification($pesanan, $refundData);

            return [
                'success' => true,
                'message' => 'Refund berhasil diproses dan notifikasi telah dikirim ke semua pihak',
                'data' => $refundData,
            ];

        } catch (\Exception $e) {
            Log::error('Refund processing error', [
                'pesanan_id' => $pesananId,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Gagal memproses refund: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kirim notifikasi refund ke Admin, Client, dan Korlap
     * 
     * @param Pesanan $pesanan
     * @param array $refundData - Data detail refund
     */
    protected function sendRefundNotification(Pesanan $pesanan, array $refundData): void
    {
        try {
            // Format pesan yang informatif
            $message = sprintf(
                'Refund DP untuk booking "%s" (%s) sebesar Rp %s telah diproses. Potongan penalti: Rp %s',
                $pesanan->nomor_pesanan,
                $pesanan->nama_pasangan,
                number_format($refundData['final_refund'], 0, ',', '.'),
                number_format($refundData['penalty_amount'], 0, ',', '.')
            );

            // Kirim notifikasi ke 3 role: Admin, Client, Korlap
            $this->notificationService->sendNotification(
                bookingId: $pesanan->id,
                eventType: 'refund_processed',
                message: $message,
                targetRoles: ['admin', 'client', 'korlap'],
                linkRedirect: route('customer.pesanan.show', $pesanan->id),
                priority: 'high',
                metadata: [
                    'booking_id' => $pesanan->id,
                    'booking_number' => $pesanan->nomor_pesanan,
                    'final_refund' => $refundData['final_refund'],
                    'penalty_amount' => $refundData['penalty_amount'],
                ]
            );

        } catch (\Exception $e) {
            Log::error('Failed to send refund notification', [
                'pesanan_id' => $pesanan->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Get refund calculation preview (tanpa update database)
     * Berguna untuk preview di frontend sebelum admin confirm refund
     * 
     * @param int $pesananId
     * @param int $penaltyPercent
     * @return array
     */
    public function getRefundPreview(int $pesananId, int $penaltyPercent = 0): array
    {
        try {
            $pesanan = Pesanan::findOrFail($pesananId);
            $invoice = $pesanan->invoices()->first();

            if (!$invoice) {
                return [
                    'success' => false,
                    'message' => 'Invoice tidak ditemukan',
                ];
            }

            $dpAmount = (float) $invoice->dp_dibayar;
            $penaltyAmount = round($dpAmount * ($penaltyPercent / 100), 2);
            $finalRefund = round($dpAmount - $penaltyAmount, 2);

            return [
                'success' => true,
                'pesanan_id' => $pesananId,
                'booking_number' => $pesanan->nomor_pesanan,
                'dp_amount' => $dpAmount,
                'penalty_percent' => $penaltyPercent,
                'penalty_amount' => $penaltyAmount,
                'final_refund' => $finalRefund,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }
}
