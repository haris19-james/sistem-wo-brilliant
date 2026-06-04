<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\LaporanLapangan;
use App\Models\Pesanan;
use App\Models\Tugas;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class LaporanController extends Controller
{
    /**
     * Display laporan dashboard
     */
    public function index(): View
    {
        $activeMenu = 'laporan';

        // Get pesanans assigned to current Korlap
        $pesanans = Pesanan::where('korlap_id', auth()->id())
            ->whereNotIn('status', ['Dibatalkan'])
            ->with(['progress', 'tugas', 'laporanLapangans'])
            ->get();

        if ($pesanans->isEmpty()) {
            $totalTasks = 0;
            $completedTasks = 0;
            $kendalaCount = 0;
        } else {
            $totalTasks = Tugas::whereIn('pesanan_id', $pesanans->pluck('id'))->count();
            $completedTasks = Tugas::whereIn('pesanan_id', $pesanans->pluck('id'))
                ->where('status', 'completed')
                ->count();
            $kendalaCount = LaporanLapangan::whereIn('pesanan_id', $pesanans->pluck('id'))->count();
        }

        // Stats from real data
        $stats = [
            'active_events' => $pesanans->count(),
            'vendor_present' => ['present' => 18, 'total' => 21],
            'tasks_complete' => ['complete' => $completedTasks, 'total' => $totalTasks],
            'challenges' => $kendalaCount,
        ];

        // Progress tahapan (5 tahapan) - example
        $stages = [
            ['name' => 'Venue', 'completed' => true],
            ['name' => 'Dekorasi', 'completed' => true],
            ['name' => 'Catering', 'completed' => true],
            ['name' => 'Dokumentasi', 'completed' => true],
            ['name' => 'Makeup', 'completed' => false],
        ];
        $completedStages = collect($stages)->filter(fn($s) => $s['completed'])->count();
        $stagePercentage = ($completedStages / count($stages)) * 100;

        // Timeline rundown - get from Tugas
        $rundowns = Tugas::where('user_id', auth()->id())
            ->with('pic')
            ->orderBy('deadline')
            ->limit(4)
            ->get()
            ->map(function($task) {
                return [
                    'time' => $task->deadline->format('H:i'),
                    'activity' => $task->nama_tugas,
                    'pic' => $task->pic?->name ?? 'Unknown',
                    'status' => $task->status,
                ];
            })
            ->toArray();

        // Kendala lapangan from database
        $kendalaList = LaporanLapangan::whereIn('pesanan_id', $pesanans->pluck('id'))
            ->where('foto_path', '!=', null)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($k) {
                $statusMap = ['Baik' => 'completed', 'Perhatian' => 'in_progress', 'Kritis' => 'new'];
                $normalizedStatus = $statusMap[$k->kondisi] ?? 'new';

                return [
                    'icon' => $k->kondisi === 'Kritis' ? 'alert-circle' : 'info',
                    'title' => $k->kondisi === 'Kritis' ? 'Kendala Kritis' : 'Kendala',
                    'event' => $k->ringkasan,
                    'time' => $k->created_at->format('H:i'),
                    'status' => $normalizedStatus,
                    'foto' => $k->foto_path,
                ];
            })
            ->toArray();

        // Fallback challenges if empty
        $challenges = !empty($kendalaList) ? $kendalaList : [
            [
                'icon' => 'clock',
                'title' => 'Keterlambatan Vendor',
                'event' => 'Pernikahan Andi & Dinda',
                'time' => '09:45',
                'status' => 'new',
            ],
            [
                'icon' => 'cloud-rain',
                'title' => 'Hujan Lebat',
                'event' => 'Resepsi Outdoor',
                'time' => '11:30',
                'status' => 'in_progress',
            ],
        ];

        // Dokumentasi galeri
        $photos = LaporanLapangan::whereIn('pesanan_id', $pesanans->pluck('id'))
            ->where('dokumentasi_path', '!=', null)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get()
            ->map(fn($doc) => [
                'url' => $doc->dokumentasi_path,
                'title' => $doc->ringkasan,
                'time' => $doc->created_at->format('d M H:i'),
            ])
            ->toArray();

        // Fallback photos if empty
        if (empty($photos)) {
            $photos = [
                [
                    'url' => 'https://via.placeholder.com/250x250?text=Progress+Dekorasi',
                    'title' => 'Progress Dekorasi',
                    'time' => '09:15 WIB',
                ],
                [
                    'url' => 'https://via.placeholder.com/250x250?text=Venue+Check',
                    'title' => 'Venue Check',
                    'time' => '08:45 WIB',
                ],
            ];
        }

        // Catatan Khusus
        $catatanKhusus = $pesanans->first()?->catatan_khusus;
        $notes = $catatanKhusus
            ? collect(preg_split('/\r\n|\r|\n/', trim($catatanKhusus)))
                ->filter()
                ->map(fn($note) => trim($note))
                ->values()
                ->toArray()
            : [
                'Pastikan semua vendor sudah tiba 1 jam sebelum acara dimulai',
                'Koordinasi dengan tim dokumentasi untuk cover semua momen penting',
                'Update status laporan setiap 30 menit ke admin',
                'Siapkan backup plan untuk hujan (indoor setup)',
                'Konfirmasi ulang jumlah tamu dengan customer sebelum catering disiapkan',
            ];

        return view('lapangan.modules.laporan', compact(
            'activeMenu',
            'stats',
            'stagePercentage',
            'rundowns',
            'challenges',
            'photos',
            'notes'
        ));
    }

    /**
     * Store kendala lapangan (API endpoint)
     */
    public function storeKendala(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'ringkasan' => 'required|string|max:500',
            'kondisi' => 'required|in:Baik,Perhatian,Kritis',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Verify Korlap access
        $pesanan = Pesanan::find($validated['pesanan_id']);
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $laporan = new LaporanLapangan([
            'pesanan_id' => $validated['pesanan_id'],
            'user_id' => auth()->id(),
            'ringkasan' => $validated['ringkasan'],
            'kondisi' => $validated['kondisi'],
        ]);

        // Handle foto upload
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('kendala', 'public');
            $laporan->foto_path = '/storage/' . $path;
        }

        $laporan->save();

        return response()->json([
            'success' => true,
            'message' => 'Kendala berhasil dilaporkan',
            'laporan' => [
                'id' => $laporan->id,
                'ringkasan' => $laporan->ringkasan,
                'kondisi' => $laporan->kondisi,
                'foto_path' => $laporan->foto_path,
                'created_at' => $laporan->created_at->format('H:i'),
            ]
        ]);
    }

    /**
     * Upload dokumentasi foto lapangan (API endpoint)
     */
    public function uploadDokumentasi(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pesanan_id' => 'required|exists:pesanans,id',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
            'keterangan' => 'nullable|string|max:255',
        ]);

        // Verify Korlap access
        $pesanan = Pesanan::find($validated['pesanan_id']);
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Store foto
        $path = $request->file('foto')->store('documentations', 'public');

        $laporan = LaporanLapangan::create([
            'pesanan_id' => $validated['pesanan_id'],
            'user_id' => auth()->id(),
            'ringkasan' => $validated['keterangan'] ?? 'Dokumentasi lapangan',
            'kondisi' => 'Baik',
            'dokumentasi_path' => '/storage/' . $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Foto dokumentasi berhasil diunggah',
            'photo' => [
                'url' => $laporan->dokumentasi_path,
                'title' => $laporan->ringkasan,
                'time' => $laporan->created_at->format('d M H:i'),
            ],
        ]);
    }

    /**
     * Get metrics for dashboard (API endpoint)
     */
    public function metrics(): JsonResponse
    {
        $pesanans = Pesanan::where('korlap_id', auth()->id())
            ->pluck('id');

        $totalTasks = Tugas::whereIn('pesanan_id', $pesanans)->count();
        $completedTasks = Tugas::whereIn('pesanan_id', $pesanans)
            ->where('status', 'completed')
            ->count();
        $kendalaCount = LaporanLapangan::whereIn('pesanan_id', $pesanans)->count();

        return response()->json([
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'kendala_count' => $kendalaCount,
            'completion_rate' => $totalTasks > 0 ? (int) (($completedTasks / $totalTasks) * 100) : 0,
        ]);
    }

    /**
     * Get kendala list for a pesanan
     */
    public function kendalaList(Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $kendala = $pesanan->laporanLapangans()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($k) => [
                'id' => $k->id,
                'ringkasan' => $k->ringkasan,
                'kondisi' => $k->kondisi,
                'foto_path' => $k->foto_path,
                'created_at' => $k->created_at->format('d M Y H:i'),
            ]);

        return response()->json(['kendala' => $kendala]);
    }

    /**
     * Update catatan lapangan
     */
    public function updateCatatan(Request $request, Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'catatan_khusus' => 'required|string|max:1000',
        ]);

        $pesanan->update(['catatan_khusus' => $validated['catatan_khusus']]);

        return response()->json([
            'success' => true,
            'message' => 'Catatan berhasil diperbarui',
            'catatan_khusus' => $pesanan->catatan_khusus,
        ]);
    }

    /**
     * Get progress by pesanan (for admin monitoring)
     */
    public function progressByPesanan(): JsonResponse
    {
        $pesanans = Pesanan::where('korlap_id', auth()->id())
            ->with('progress', 'tugas')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'nama_pasangan' => $p->nama_pasangan,
                'tanggal_acara' => $p->tanggal_acara->format('d M Y'),
                'progress_percent' => $p->progress?->persentase ?? 0,
                'total_tasks' => $p->tugas->count(),
                'completed_tasks' => $p->tugas->where('status', 'completed')->count(),
            ]);

        return response()->json(['data' => $pesanans]);
    }
}
