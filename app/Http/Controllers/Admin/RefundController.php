<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\RefundService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * RefundController - Handle refund requests untuk admin
 * 
 * Controller ini mengelola:
 * 1. Preview perhitungan refund sebelum proses
 * 2. Proses refund dengan automatic notification ke 3 role
 * 3. Riwayat refund untuk booking
 * 
 * @author WO System
 */
class RefundController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
        $this->middleware('auth');
        $this->middleware('admin'); // Hanya admin yang bisa akses
    }

    /**
     * GET /admin/refund/{pesanan}/preview
     * 
     * Menampilkan preview perhitungan refund tanpa mengubah database
     * Digunakan untuk menampilkan estimated amount kepada admin sebelum confirm
     * 
     * @param Pesanan $pesanan
     * @param Request $request - query param: penalty_percent (default 0)
     * @return JsonResponse
     */
    public function preview(Pesanan $pesanan, Request $request): JsonResponse
    {
        $penaltyPercent = (int) $request->query('penalty_percent', 0);

        $preview = $this->refundService->getRefundPreview(
            $pesanan->id,
            $penaltyPercent
        );

        return response()->json($preview);
    }

    /**
     * POST /admin/refund/{pesanan}/process
     * 
     * Proses refund untuk booking
     * 
     * Flow:
     * 1. Validasi status pembayaran (harus dp_paid atau fully_paid)
     * 2. Hitung refund otomatis: finalRefund = dpAmount - (dpAmount * penaltyPercent/100)
     * 3. Update pesanan status menjadi 'refunded' dan 'cancelled'
     * 4. Simpan nominal refund di database
     * 5. Otomatis kirim notifikasi ke Admin, Client, dan Korlap
     * 6. Return response dengan detail refund
     * 
     * @param Pesanan $pesanan
     * @param Request $request - body: penalty_percent (int, default 0), alasan_refund (string, optional)
     * @return JsonResponse
     * 
     * @example
     * POST /admin/refund/123/process
     * {
     *   "penalty_percent": 20,
     *   "alasan_refund": "Client meminta pembatalan karena kondisi mendadak"
     * }
     * 
     * Response:
     * {
     *   "success": true,
     *   "message": "Refund berhasil diproses dan notifikasi telah dikirim ke semua pihak",
     *   "data": {
     *     "pesanan_id": 123,
     *     "pesanan_number": "WO-2024-001",
     *     "client_name": "John Doe",
     *     "dp_amount": 5000000,
     *     "penalty_percent": 20,
     *     "penalty_amount": 1000000,
     *     "final_refund": 4000000,
     *     "alasan": "Client meminta pembatalan..."
     *   }
     * }
     */
    public function process(Pesanan $pesanan, Request $request): JsonResponse
    {
        // Validasi input
        $validated = $request->validate([
            'penalty_percent' => 'required|integer|min:0|max:100',
            'alasan_refund' => 'nullable|string|max:500',
        ]);

        // Proses refund
        $result = $this->refundService->processRefund(
            pesananId: $pesanan->id,
            penaltyPercent: $validated['penalty_percent'],
            alasanRefund: $validated['alasan_refund'] ?? null
        );

        // Return dengan status code yang sesuai
        $statusCode = $result['success'] ? 200 : 422;

        return response()->json($result, $statusCode);
    }

    /**
     * GET /admin/refund
     * 
     * Menampilkan daftar pesanan yang eligible untuk refund
     * (status pembayaran: dp_paid atau fully_paid)
     * 
     * @return JsonResponse
     */
    public function listEligible(): JsonResponse
    {
        $eligibleBookings = Pesanan::whereIn(
            'status_pembayaran',
            ['dp_paid', 'fully_paid']
        )
        ->where('status_pemesanan', '!=', 'canceled')
        ->with('user', 'korlap')
        ->orderByDesc('created_at')
        ->get()
        ->map(function ($pesanan) {
            $invoice = $pesanan->invoices()->first();
            return [
                'id' => $pesanan->id,
                'nomor_pesanan' => $pesanan->nomor_pesanan,
                'nama_pasangan' => $pesanan->nama_pasangan,
                'client_name' => $pesanan->user->name,
                'tanggal_acara' => $pesanan->tanggal_acara,
                'status_pembayaran' => $pesanan->status_pembayaran,
                'dp_dibayar' => $invoice?->dp_dibayar ?? 0,
            ];
        });

        return response()->json([
            'success' => true,
            'total' => $eligibleBookings->count(),
            'data' => $eligibleBookings,
        ]);
    }

    /**
     * GET /admin/pesanan/{pesanan}/refund-status
     * 
     * Check status refund untuk booking
     * 
     * @param Pesanan $pesanan
     * @return JsonResponse
     */
    public function status(Pesanan $pesanan): JsonResponse
    {
        $isRefunded = $pesanan->status_pembayaran === 'refunded';
        $invoice = $pesanan->invoices()->first();

        return response()->json([
            'success' => true,
            'booking_id' => $pesanan->id,
            'booking_number' => $pesanan->nomor_pesanan,
            'is_refunded' => $isRefunded,
            'status_pembayaran' => $pesanan->status_pembayaran,
            'refund_amount' => $pesanan->jumlah_refund,
            'alasan_pembatalan' => $pesanan->alasan_pembatalan,
            'dibatalkan_at' => $pesanan->dibatalkan_at,
            'dp_dibayar' => $invoice?->dp_dibayar ?? 0,
        ]);
    }
}
