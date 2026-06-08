<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\RefundService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\BookingCancellationService;
use App\Models\RefundAudit;
use Illuminate\Support\Facades\Redirect;

class RefundController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function preview(Pesanan $pesanan, Request $request): JsonResponse
    {
        $penaltyPercent = (int) $request->query('penalty_percent', 20);

        $preview = $this->refundService->getRefundPreview($pesanan->id, $penaltyPercent);

        return response()->json($preview);
    }

    public function pendingIndex(): \Illuminate\View\View
    {
        $pendings = Pesanan::where('status_pemesanan', 'pending_cancellation')
            ->with('user')
            ->orderByDesc('pembatalan_diminta_at')
            ->get();

        return view('admin.modules.booking.pending_cancellations', ['pendings' => $pendings]);
    }

    public function approve(Pesanan $pesanan, Request $request, BookingCancellationService $cancellationService): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'refund_dp' => 'nullable|boolean',
            'jumlah_refund' => 'nullable|numeric|min:0',
        ]);

        $refundDp = (bool) ($validated['refund_dp'] ?? false);
        $jumlah = isset($validated['jumlah_refund']) ? (float) $validated['jumlah_refund'] : null;

        $final = $cancellationService->approvePendingCancellation($pesanan, $refundDp, $jumlah);

        // create audit if refund > 0
        if ((float) ($final->jumlah_refund ?? 0) > 0) {
            RefundAudit::create([
                'pesanan_id' => $pesanan->id,
                'admin_id' => auth()->id(),
                'dp_amount' => $final->invoices()->first()?->dp_dibayar ?? 0,
                'penalty_percent' => 20,
                'penalty_amount' => round((float) ($final->invoices()->first()?->dp_dibayar ?? 0) * 0.20, 2),
                'final_refund' => (float) $final->jumlah_refund,
                'note' => 'Disetujui oleh admin via dashboard',
            ]);
        }

        // send notification to client
        $notif = app(\App\Services\NotificationCenterService::class);
        $notif->notifyUser(
            $pesanan->user_id,
            'Refund pembatalan telah diproses: Rp '.number_format($final->jumlah_refund ?? 0, 0, ',', '.'),
            route('client.pesanan_detail', $pesanan->id),
            'urgent',
            'refund'
        );

        return Redirect::back()->with('success', 'Permintaan pembatalan disetujui dan refund dicatat.');
    }

    public function deny(Pesanan $pesanan, Request $request): \Illuminate\Http\RedirectResponse
    {
        // revert pending cancellation
        $pesanan->forceFill([
            'status_pemesanan' => 'confirmed',
            'alasan_pembatalan' => null,
            'pembatalan_diminta_at' => null,
            'jumlah_refund' => 0,
        ])->save();

        // notify client
        app(\App\Services\NotificationCenterService::class)->notifyUser(
            $pesanan->user_id,
            "Permintaan pembatalan untuk {$pesanan->nomor_pesanan} ditolak oleh Admin.",
            route('client.pesanan_detail', $pesanan->id),
            'urgent',
            'cancellation'
        );

        return Redirect::back()->with('success', 'Permintaan pembatalan ditolak.');
    }

    public function process(Pesanan $pesanan, Request $request)
    {
        $validated = $request->validate([
            'penalty_percent' => 'nullable|integer|min:0|max:100',
            'alasan_refund' => 'nullable|string|max:500',
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,webp,gif|max:'.(int) (config('pembayaran.bukti_max_kb', 10240)),
        ]);

        $file = $request->file('bukti_transfer');
        $path = Storage::disk('public')->putFileAs(
            'refund_proofs',
            $file,
            'refund-proof-'.$pesanan->id.'-'.now()->format('YmdHis').'.'.$file->extension()
        );

        $buktiTransferUrl = Storage::disk('public')->url($path);
        $penalty = $validated['penalty_percent'] ?? 20;

        $result = $this->refundService->processRefund(
            $pesanan->id,
            $penalty,
            $validated['alasan_refund'] ?? null,
            $buktiTransferUrl
        );

        if ($result['success']) {
            app(\App\Services\NotificationCenterService::class)->notifyUser(
                $pesanan->user_id,
                'Refund Anda sebesar Rp '.number_format($result['data']['final_refund'], 0, ',', '.').' telah dikirimkan. Silakan cek bukti transfer di dasbor.',
                route('client.pesanan_detail', $pesanan->id),
                'urgent',
                'refund'
            );

            if ($request->wantsJson()) {
                return response()->json($result);
            }

            return Redirect::back()->with('success', $result['message']);
        }

        if ($request->wantsJson()) {
            return response()->json($result, 422);
        }

        return Redirect::back()->withErrors(['refund' => $result['message']]);
    }

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
