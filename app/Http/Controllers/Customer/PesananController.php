<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\BookingCancellationService;
use App\Services\ItemTambahanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class PesananController extends Controller
{
    public function requestCancellation(Request $request, Pesanan $pesanan, BookingCancellationService $cancellationService)
    {
        if ($pesanan->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'alasan_pembatalan' => ['required', 'string', 'min:10', 'max:1000'],
            'konfirmasi' => ['accepted'],
        ], [
            'alasan_pembatalan.min' => 'Alasan pembatalan minimal 10 karakter.',
            'konfirmasi.accepted' => 'Anda harus menyetujui ketentuan pembatalan.',
        ]);

        try {
            $result = $cancellationService->cancel($pesanan, $validated['alasan_pembatalan']);
        } catch (InvalidArgumentException $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        if ($result->status_pemesanan === 'pending_cancellation') {
            return redirect()
                ->route('client.pesanan_detail', $pesanan->id)
                ->with('success', 'Permintaan pembatalan dikirim. Admin akan meninjau refund untuk pesanan lunas.');
        }

        return redirect()
            ->route('client.pesanan')
            ->with('success', 'Pesanan berhasil dibatalkan. Tanggal acara telah dibebaskan. Dana DP tidak dikembalikan (Rp 0).');
    }

    /** @deprecated Gunakan API POST /api/bookings/{id}/cancel */
    public function storeAddon(Request $request, Pesanan $pesanan, ItemTambahanService $service)
    {
        return app(ItemTambahanController::class)->store($request, $pesanan);
    }
}
