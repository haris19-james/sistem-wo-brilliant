<?php

namespace App\Http\Middleware;

use App\Models\Pesanan;
use App\Services\ScheduleAccessService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureScheduleAccess
{
    /**
     * Middleware pembatas akses jadwal Tim Lapangan berdasarkan DP/Lunas.
     *
     * Usage: ->middleware('schedule.access:full') atau schedule.access:partial
     */
    public function handle(Request $request, Closure $next, string $requiredLevel = 'partial'): Response
    {
        $pesanan = $request->route('pesanan');

        if (! $pesanan instanceof Pesanan) {
            return $next($request);
        }

        if ($pesanan->korlap_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if (! in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            abort(403, 'Acara ini belum diverifikasi pembayaran DP.');
        }

        $current = $pesanan->akses_jadwal ?? ScheduleAccessService::resolveAksesFromPayment($pesanan);

        if ($requiredLevel === 'full' && $current !== 'full' && ! $pesanan->isPembayaranLunas()) {
            abort(403, 'Fitur ini terkunci. Menunggu pelunasan penuh dari customer.');
        }

        return $next($request);
    }
}
