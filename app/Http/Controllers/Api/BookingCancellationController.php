<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\BookingCancellationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class BookingCancellationController extends Controller
{
    public function __construct(
        protected BookingCancellationService $cancellationService
    ) {}

    /**
     * POST|PATCH /api/bookings/{pesanan}/cancel
     */
    public function cancel(Request $request, Pesanan $pesanan)
    {
        $user = Auth::user();
        $isAdmin = $user?->role === 'admin';

        if (! $isAdmin && $pesanan->user_id !== $user?->id) {
            abort(403, 'Anda tidak berhak membatalkan pesanan ini.');
        }

        $validated = $request->validate([
            'alasan_pembatalan' => ['required', 'string', 'min:10', 'max:1000'],
            'konfirmasi' => [$isAdmin ? 'nullable' : 'accepted'],
            'jumlah_refund' => ['nullable', 'numeric', 'min:0'],
            'refund_dp' => ['nullable', 'boolean'],
        ], [
            'alasan_pembatalan.min' => 'Alasan pembatalan minimal 10 karakter.',
            'konfirmasi.accepted' => 'Anda harus menyetujui ketentuan pembatalan.',
        ]);

        try {
            // If client (not admin) is cancelling, compute refund automatically: 20% of DP
            if (! $isAdmin) {
                $invoice = $pesanan->invoices()->first();
                $dpAmount = (float) ($invoice->dp_dibayar ?? 0);
                $calculated = round($dpAmount * 0.20, 2);
                $validated['jumlah_refund'] = $calculated;
                $validated['refund_dp'] = true;
            }

            $result = $this->cancellationService->cancel(
                $pesanan,
                $validated['alasan_pembatalan'],
                [
                    'by_admin' => $isAdmin,
                    'jumlah_refund' => $validated['jumlah_refund'] ?? null,
                    'refund_dp' => (bool) ($validated['refund_dp'] ?? false),
                ]
            );
        } catch (InvalidArgumentException $e) {
            return $this->errorResponse($request, $e->getMessage(), 422);
        }

        $fresh = $result->fresh();
        $immediate = $fresh->status_booking === 'cancelled';

        $message = $immediate
            ? 'Pesanan berhasil dibatalkan. Tanggal acara telah dibebaskan untuk klien lain.'
                .($fresh->jumlah_refund > 0
                    ? ' Refund: Rp '.number_format((float) $fresh->jumlah_refund, 0, ',', '.').'.'
                    : ' Dana DP tidak dapat dikembalikan (Rp 0).')
            : 'Permintaan pembatalan dikirim. Admin akan meninjau refund untuk pesanan lunas.';

        $redirectUrl = $isAdmin
            ? route('admin.booking.show', $pesanan)
            : ($immediate ? route('client.pesanan') : route('client.pesanan_detail', $pesanan));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'redirect_url' => $redirectUrl,
                'pesanan' => [
                    'id' => $fresh->id,
                    'status_booking' => $fresh->status_booking,
                    'status' => $fresh->status,
                    'jumlah_refund' => (float) ($fresh->jumlah_refund ?? 0),
                ],
                'date_released' => $immediate,
            ]);
        }

        return redirect($redirectUrl)->with('success', $message);
    }

    protected function errorResponse(Request $request, string $message, int $code = 400)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $message], $code);
        }

        return back()->with('error', $message)->withInput();
    }
}
