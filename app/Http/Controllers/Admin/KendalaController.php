<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LaporanLapangan;
use App\Services\NotificationCenterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KendalaController extends Controller
{
    public function updateStatus(Request $request, LaporanLapangan $kendala): JsonResponse
    {
        $validated = $request->validate([
            'status_tindak' => 'required|in:Menunggu Tindakan,Dalam Penanganan,Selesai',
            'tindak_lanjut' => 'required_if:status_tindak,Selesai|nullable|string|max:2000',
        ], [
            'tindak_lanjut.required_if' => 'Catatan solusi penyelesaian wajib diisi saat menyelesaikan kendala.',
        ]);

        $kendala->update([
            'status_tindak' => $validated['status_tindak'],
            'tindak_lanjut' => $validated['status_tindak'] === 'Selesai'
                ? ($validated['tindak_lanjut'] ?? $kendala->tindak_lanjut)
                : ($validated['tindak_lanjut'] ?? $kendala->tindak_lanjut),
        ]);

        $kendala->refresh()->load(['pesanan:id,nomor_pesanan,nama_pasangan,korlap_id,user_id', 'user:id,name']);

        $pesanan = $kendala->pesanan;
        if ($pesanan) {
            if ($validated['status_tindak'] === 'Dalam Penanganan' && $pesanan->korlap_id) {
                app(NotificationCenterService::class)->notifyUser(
                    (int) $pesanan->korlap_id,
                    'Kendala '.$pesanan->nomor_pesanan.' sedang ditangani admin.',
                    route('lapangan.laporan', ['pesanan_id' => $pesanan->id]),
                    'normal',
                    'issue'
                );
            }

            if ($validated['status_tindak'] === 'Selesai') {
                if ($pesanan->korlap_id) {
                    app(NotificationCenterService::class)->notifyUser(
                        (int) $pesanan->korlap_id,
                        'Kendala '.$pesanan->nomor_pesanan.' selesai. Solusi: '.mb_substr((string) $kendala->tindak_lanjut, 0, 120),
                        route('lapangan.laporan', ['pesanan_id' => $pesanan->id]),
                        'normal',
                        'issue'
                    );
                }
                if ($pesanan->user_id) {
                    app(NotificationCenterService::class)->notifyUser(
                        (int) $pesanan->user_id,
                        'Kendala pesanan '.$pesanan->nomor_pesanan.' telah diselesaikan oleh admin.',
                        route('client.pesanan_detail', $pesanan->id),
                        'normal',
                        'issue'
                    );
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => match ($validated['status_tindak']) {
                'Selesai' => 'Kendala ditandai selesai. Tim lapangan dapat membaca catatan penyelesaian.',
                'Dalam Penanganan' => 'Kendala sedang dalam penanganan.',
                default => 'Status kendala diperbarui.',
            },
            'kendala' => $this->formatKendala($kendala),
        ]);
    }

    /**
     * Kendala aktif untuk dashboard admin (Menunggu / Dalam penanganan).
     */
    public static function aktifUntukDashboard(int $limit = 12): Collection
    {
        return static::queryDasar()
            ->whereIn('status_tindak', ['Menunggu Tindakan', 'Dalam Penanganan'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Kendala selesai untuk dashboard admin.
     */
    public static function selesaiUntukDashboard(int $limit = 8): Collection
    {
        return static::queryDasar()
            ->where('status_tindak', 'Selesai')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    protected static function queryDasar()
    {
        return LaporanLapangan::query()
            ->with(['pesanan:id,nomor_pesanan,nama_pasangan', 'user:id,name']);
    }

    /**
     * @return array<string, mixed>
     */
    protected function formatKendala(LaporanLapangan $kendala): array
    {
        return [
            'id' => $kendala->id,
            'status_tindak' => $kendala->status_tindak,
            'status_tindak_badge_class' => $kendala->status_tindak_badge_class,
            'tindak_lanjut' => $kendala->tindak_lanjut,
            'ringkasan' => $kendala->ringkasan,
            'kondisi' => $kendala->kondisi,
            'kondisi_badge_class' => $kendala->kondisi_badge_class,
            'is_aktif' => $kendala->isKendalaAktif(),
        ];
    }
}
