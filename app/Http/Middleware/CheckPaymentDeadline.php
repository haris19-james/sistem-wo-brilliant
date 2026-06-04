<?php

namespace App\Http\Middleware;

use App\Models\Pesanan;
use App\Models\Tugas;
use App\Services\PaymentDeadlineService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPaymentDeadline
{
    /**
     * Bekukan aksi Korlap jika customer melewati tenggat pelunasan (H-14 event).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $pesanan = $this->resolvePesanan($request);

        if ($pesanan instanceof Pesanan && PaymentDeadlineService::isKorlapFrozen($pesanan)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Akses dibekukan karena customer melewati batas waktu pelunasan.',
                ], 403);
            }

            return back()->with('error', 'Akses dibekukan karena customer melewati batas waktu pelunasan.');
        }

        return $next($request);
    }

    private function resolvePesanan(Request $request): ?Pesanan
    {
        $pesanan = $request->route('pesanan');
        if ($pesanan instanceof Pesanan) {
            return $pesanan;
        }

        $tugas = $request->route('tugas');
        if ($tugas instanceof Tugas) {
            return $tugas->pesanan;
        }

        $pesananId = $request->input('pesanan_id');
        if ($pesananId) {
            return Pesanan::find($pesananId);
        }

        return null;
    }
}
