<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\ItemTambahanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ItemTambahanController extends Controller
{
    public function __construct(
        protected ItemTambahanService $itemTambahanService
    ) {}

    public function store(Request $request, ?Pesanan $pesanan = null)
    {
        $pesanan = $this->resolvePesanan($request, $pesanan);

        if (! $this->canSubmitAddon($pesanan)) {
            return $this->errorResponse($request, 'Tidak dapat mengajukan item tambahan pada booking ini.', 422);
        }

        $validated = $request->validate([
            'kategori' => ['required', 'string', Rule::in(config('item_tambahan.kategori', []))],
            'deskripsi' => ['required', 'string', 'max:500'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:9999'],
        ], [
            'deskripsi.required' => 'Nama item / deskripsi wajib diisi.',
        ]);

        $item = $this->itemTambahanService->submitCustomerRequest($pesanan, $validated);

        $message = 'Pengajuan item tambahan berhasil dikirim. Admin akan meninjau dan menetapkan harga.';

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => $message,
                'item' => $item,
            ], 201);
        }

        return back()->with('success', $message);
    }

    protected function resolvePesanan(Request $request, ?Pesanan $pesanan): Pesanan
    {
        if ($pesanan) {
            if ($pesanan->user_id !== Auth::id()) {
                abort(403);
            }

            return $pesanan;
        }

        $pesananId = $request->integer('pesanan_id') ?: $request->integer('booking_id');
        abort_unless($pesananId, 422, 'Pesanan wajib dipilih.');

        return Pesanan::where('id', $pesananId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    protected function canSubmitAddon(Pesanan $pesanan): bool
    {
        return in_array($pesanan->status, ['Menunggu', 'Sedang Berlangsung'], true)
            && ! in_array($pesanan->status_pemesanan, ['expired', 'pending_cancellation', 'canceled', 'cancelled'], true);
    }

    protected function errorResponse(Request $request, string $message, int $code = 400)
    {
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => $message], $code);
        }

        return back()->with('error', $message);
    }
}
