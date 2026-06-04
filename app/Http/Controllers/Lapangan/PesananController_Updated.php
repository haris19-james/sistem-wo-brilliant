<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\LaporanLapangan;
use App\Models\Pesanan;
use App\Models\ProgressPersiapan;
use App\Support\PersiapanTimeline;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PesananController extends Controller
{
    /**
     * Display list of pesanans assigned to current Korlap
     */
    public function index(Request $request)
    {
        $query = Pesanan::with(['user', 'paket', 'progress', 'korlap'])
            ->where('korlap_id', auth()->id())  // ✅ FILTER BY LOGGED-IN KORLAP
            ->whereNotIn('status', ['Dibatalkan'])
            ->orderBy('tanggal_acara');

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        } else {
            $query->aktifLapangan();
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($builder) use ($q) {
                $builder->where('nama_pasangan', 'like', "%{$q}%")
                    ->orWhere('nomor_pesanan', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%");
            });
        }

        $pesanans = $query->paginate(12)->withQueryString();

        return view('lapangan.modules.pesanan.index', [
            'activeMenu' => 'pesanan',
            'pesanans' => $pesanans,
            'filters' => $request->only(['status', 'q']),
        ]);
    }

    /**
     * Display detail of a specific pesanan
     */
    public function show(Pesanan $pesanan)
    {
        // ✅ AUTHORIZATION CHECK
        if ($pesanan->korlap_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this pesanan');
        }

        if ($pesanan->status === 'Dibatalkan') {
            abort(404);
        }

        $pesanan->load([
            'user', 'paket', 'progress', 'rundowns', 'jadwalMeetings',
            'invoices', 'laporanLapangans.user',
        ]);

        $timeline = PersiapanTimeline::build($pesanan);

        return view('lapangan.modules.pesanan.show', [
            'activeMenu' => 'pesanan',
            'pesanan' => $pesanan,
            'timeline' => $timeline,
        ]);
    }

    /**
     * Update progress persiapan for a pesanan
     */
    public function updateProgress(Request $request, Pesanan $pesanan)
    {
        // ✅ AUTHORIZATION CHECK
        if ($pesanan->korlap_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this pesanan');
        }

        if ($pesanan->status === 'Dibatalkan') {
            return back()->with('error', 'Pesanan dibatalkan.');
        }

        $validated = $request->validate([
            'status_venue' => ['required', 'in:Menunggu,Proses,Selesai'],
            'status_makeup' => ['required', 'in:Menunggu,Proses,Selesai'],
            'status_catering' => ['required', 'in:Menunggu,Proses,Selesai'],
            'status_dekorasi' => ['required', 'in:Menunggu,Proses,Selesai'],
            'status_dokumentasi' => ['required', 'in:Menunggu,Proses,Selesai'],
            'persentase' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $progress = ProgressPersiapan::firstOrCreate(
            ['pesanan_id' => $pesanan->id],
            ['persentase' => 5]
        );

        $progress->fill($validated);
        $progress->save();

        if ($request->filled('persentase')) {
            $progress->update(['persentase' => $validated['persentase']]);
        } else {
            $progress->update([
                'persentase' => (int) round(collect($progress->fresh()->aspek_items)->avg('progress_percent')),
            ]);
        }

        if ($pesanan->status === 'Menunggu' && $progress->persentase >= 10) {
            $pesanan->update(['status' => 'Sedang Berlangsung']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Progress persiapan berhasil diperbarui',
                'progress' => [
                    'persentase' => $progress->persentase,
                    'aspek_items' => $progress->aspek_items,
                ]
            ]);
        }

        return back()->with('success', 'Progress persiapan diperbarui. Client dapat melihat di jadwal acara.');
    }

    /**
     * Get progress metrics as JSON (for real-time dashboard)
     */
    public function getProgressMetrics(Pesanan $pesanan): JsonResponse
    {
        // ✅ AUTHORIZATION CHECK
        if ($pesanan->korlap_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $progress = $pesanan->progress;
        $tugas = $pesanan->tugas();

        return response()->json([
            'pesanan_id' => $pesanan->id,
            'nama_pasangan' => $pesanan->nama_pasangan,
            'progress_percent' => $progress?->persentase ?? 0,
            'total_tasks' => $tugas->count(),
            'completed_tasks' => $tugas->where('status', 'completed')->count(),
            'pending_tasks' => $tugas->where('status', 'pending')->count(),
            'aspek_items' => $progress?->aspek_items ?? [],
        ]);
    }
}
