<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\LaporanLapangan;
use App\Models\Pesanan;
use App\Models\Vendor;
use App\Services\KorlapLaporanIntelligenceService;
use App\Support\AdminPerformanceCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function __construct(
        protected KorlapLaporanIntelligenceService $intelligence
    ) {}

    public function index(Request $request): View
    {
        $korlapId = (int) auth()->id();
        $pesananId = $request->filled('pesanan_id') ? (int) $request->pesanan_id : null;

        $report = $this->intelligence->build($korlapId, $pesananId);

        return view('lapangan.modules.laporan', array_merge($report, [
            'activeMenu' => 'laporan',
        ]));
    }

    public function confirmAttendance(Pesanan $pesanan, Vendor $vendor): JsonResponse
    {
        $attendance = $this->intelligence->confirmAttendance((int) auth()->id(), $pesanan, $vendor->id);

        AdminPerformanceCache::forgetKorlapMetrics((int) auth()->id(), (int) $pesanan->id);

        return response()->json([
            'success' => true,
            'message' => 'Kehadiran vendor dikonfirmasi oleh Korlap.',
            'attendance' => [
                'status' => $attendance->status,
                'arrived_at' => $attendance->arrived_at?->format('d M Y, H:i'),
                'confirmed' => true,
                'is_late' => $attendance->is_late,
            ],
        ]);
    }

    public function metrics(Request $request): JsonResponse
    {
        $korlapId = (int) auth()->id();
        $pesananId = $request->filled('pesanan_id') ? (int) $request->pesanan_id : null;
        $payload = $this->intelligence->metricsPayload($korlapId, $pesananId);

        return response()->json([
            'kpis' => $payload['kpis'],
            'kendala_chart' => $payload['kendala_chart'],
        ]);
    }

    public function progressByPesanan(): JsonResponse
    {
        $korlapId = (int) auth()->id();

        $data = Pesanan::query()
            ->visibleToKorlap($korlapId)
            ->aktifLapangan()
            ->select(['id', 'nama_pasangan', 'tanggal_acara'])
            ->withCount([
                'tugas as total_tugas',
                'tugas as verified_tugas' => fn ($q) => $q->where('status', 'completed')->whereNotNull('korlap_verified_at'),
            ])
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'nama_acara' => $p->nama_pasangan,
                'total_tasks' => $p->total_tugas,
                'completed_tasks' => $p->verified_tugas,
                'progress' => $p->total_tugas > 0
                    ? (int) round(($p->verified_tugas / $p->total_tugas) * 100)
                    : 0,
                'tanggal_acara' => $p->tanggal_acara?->format('Y-m-d'),
            ]);

        return response()->json(['data' => $data]);
    }

    public function storeKendala(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'kondisi' => 'required|in:Baik,Perhatian,Kritis,baik,perhatian,kritis',
            'kategori' => 'nullable|in:'.implode(',', LaporanLapangan::KATEGORI),
            'ringkasan' => 'required|string|max:500',
            'tindak_lanjut' => 'nullable|string|max:1000',
        ]);

        $pesanan = Pesanan::findOrFail($validated['pesanan_id']);
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $kondisi = ucfirst(strtolower($validated['kondisi']));
        $kategori = $validated['kategori']
            ?? $this->intelligence->inferKategoriFromRingkasan($validated['ringkasan'], $kondisi);

        $statusTindak = ! empty($validated['tindak_lanjut'])
            ? 'Dalam Penanganan'
            : ($kondisi === 'Baik' ? 'Selesai' : 'Menunggu Tindakan');

        $kendala = LaporanLapangan::create([
            'user_id' => auth()->id(),
            'pesanan_id' => $validated['pesanan_id'],
            'tanggal' => now()->toDateString(),
            'kondisi' => $kondisi,
            'kategori' => $kategori,
            'status_tindak' => $statusTindak,
            'ringkasan' => $validated['ringkasan'],
            'tindak_lanjut' => $validated['tindak_lanjut'] ?? null,
        ]);

        app(\App\Services\NotificationCenterService::class)->kendalaForStaff(
            $pesanan,
            $validated['ringkasan'],
            in_array($kondisi, ['Kritis', 'Perhatian'], true)
        );

        AdminPerformanceCache::forgetKorlapMetrics((int) auth()->id(), (int) $pesanan->id);

        return response()->json([
            'success' => true,
            'message' => 'Kendala berhasil dicatat.',
            'kendala' => $kendala,
        ]);
    }

    public function updateKendalaStatus(Request $request, LaporanLapangan $kendala): JsonResponse
    {
        $pesanan = $kendala->pesanan;
        if (! $pesanan || $pesanan->korlap_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'status_tindak' => 'required|in:Menunggu Tindakan,Dalam Penanganan,Selesai',
            'tindak_lanjut' => 'required_if:status_tindak,Selesai|nullable|string|max:2000',
        ]);

        $kendala->update([
            'status_tindak' => $validated['status_tindak'],
            'tindak_lanjut' => $validated['tindak_lanjut'] ?? $kendala->tindak_lanjut,
        ]);

        AdminPerformanceCache::forgetKorlapMetrics((int) auth()->id(), (int) $pesanan->id);

        return response()->json([
            'success' => true,
            'message' => 'Status kendala diperbarui.',
            'kendala' => $kendala->fresh(),
        ]);
    }

    public function updateCatatan(Request $request)
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'catatan_khusus' => 'nullable|string|max:2000',
        ]);

        $pesanan = Pesanan::where('id', $validated['pesanan_id'])
            ->where('korlap_id', auth()->id())
            ->firstOrFail();

        $pesanan->update([
            'catatan_khusus' => $validated['catatan_khusus'] ?? null,
        ]);

        return back()->with('success', 'Catatan Korlap berhasil diperbarui.');
    }

    public function kendalaList($pesanan_id): JsonResponse
    {
        $pesanan = Pesanan::where('id', $pesanan_id)->where('korlap_id', auth()->id())->firstOrFail();

        $kendala = LaporanLapangan::where('pesanan_id', $pesanan->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $kendala]);
    }
}
