<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\JadwalMeeting;
use App\Models\Pesanan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class JadwalController extends Controller
{
    /**
     * Display jadwal acara page with list of events
     */
    public function index()
    {
        $start = Carbon::today();
        $end = $start->copy()->addDays(30);

        $pesanans = Pesanan::with(['paket', 'progress', 'rundowns', 'jadwalMeetings'])
            ->where('korlap_id', auth()->id())  // ✅ Filter by current Korlap
            ->whereNotIn('status', ['Dibatalkan'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_acara', [$start, $end])
                    ->orWhere('status', 'Sedang Berlangsung');
            })
            ->orderBy('tanggal_acara')
            ->get();

        $meetings = JadwalMeeting::with('pesanan')
            ->whereHas('pesanan', function ($q) {
                $q->where('korlap_id', auth()->id());
            })
            ->whereBetween('tanggal_meeting', [$start, $end])
            ->orderBy('tanggal_meeting')
            ->orderBy('waktu_meeting')
            ->get();

        return view('lapangan.modules.jadwal.index', [
            'activeMenu' => 'jadwal',
            'pesanans' => $pesanans,
            'meetings' => $meetings,
        ]);
    }

    /**
     * Get rundown detail for a pesanan (AJAX endpoint)
     * This enables interactive panel update without page reload
     */
    public function getRundownDetail(Pesanan $pesanan): JsonResponse
    {
        // ✅ Authorization check
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get current time for status calculation
        $now = Carbon::now();
        $eventDate = $pesanan->tanggal_acara;
        $eventStartTime = $pesanan->jam_acara ? Carbon::createFromFormat('H:i', $pesanan->jam_acara) : null;

        $rundowns = $pesanan->rundowns()
            ->select('id', 'kategori_acara', 'waktu_mulai', 'waktu_selesai', 'kegiatan')
            ->orderBy('waktu_mulai')
            ->get()
            ->map(function($rundown) use ($now, $eventDate, $eventStartTime) {
                
                // Parse waktu
                $mulaiFormatted = $rundown->waktu_mulai_formatted;
                $selesaiFormatted = $rundown->waktu_selesai_formatted;

                // Determine timeline status
                $status = 'akan_datang';
                $statusLabel = 'Akan Datang';
                $statusBadgeClass = 'bg-gray-100 text-gray-600';
                $iconClass = 'text-gray-400';

                // Only calculate if event is today
                if ($eventDate->isToday()) {
                    $nowTime = $now->format('H:i');

                    if ($nowTime < $mulaiFormatted) {
                        $status = 'akan_datang';
                        $statusLabel = 'Akan Datang';
                        $statusBadgeClass = 'bg-blue-50 text-blue-700';
                        $iconClass = 'text-blue-400';
                    } elseif ($nowTime >= $mulaiFormatted && $nowTime < ($selesaiFormatted ?: $mulaiFormatted)) {
                        $status = 'berlangsung';
                        $statusLabel = 'Berlangsung';
                        $statusBadgeClass = 'bg-green-50 text-green-700';
                        $iconClass = 'text-green-400';
                    } else {
                        $status = 'selesai';
                        $statusLabel = 'Selesai';
                        $statusBadgeClass = 'bg-gray-50 text-gray-600';
                        $iconClass = 'text-gray-400';
                    }
                }

                return [
                    'id' => $rundown->id,
                    'kategori' => $rundown->kategori_acara,
                    'waktu_mulai' => $mulaiFormatted,
                    'waktu_selesai' => $selesaiFormatted,
                    'kegiatan' => $rundown->kegiatan,
                    'status' => $status,
                    'status_label' => $statusLabel,
                    'status_badge_class' => $statusBadgeClass,
                    'icon_class' => $iconClass,
                ];
            });

        $pesananData = $pesanan->only([
            'id', 'nama_pasangan', 'lokasi', 'tema', 'jumlah_tamu'
        ]);
        $pesananData['tanggal_acara'] = $pesanan->tanggal_acara->translatedFormat('d F Y');
        $pesananData['jam_acara'] = $pesanan->jam_acara ?? '-';

        return response()->json([
            'success' => true,
            'pesanan' => $pesananData,
            'rundowns' => $rundowns,
            'progress' => $pesanan->progress?->persentase ?? 0,
        ]);
    }

    /**
     * Get meetings for a date range (for calendar view)
     */
    public function getMeetings($startDate, $endDate): JsonResponse
    {
        $meetings = JadwalMeeting::with('pesanan')
            ->whereHas('pesanan', function ($q) {
                $q->where('korlap_id', auth()->id());
            })
            ->whereBetween('tanggal_meeting', [$startDate, $endDate])
            ->orderBy('tanggal_meeting')
            ->orderBy('waktu_meeting')
            ->get()
            ->map(fn($m) => [
                'id' => $m->id,
                'title' => $m->topik,
                'date' => $m->tanggal_meeting->format('Y-m-d'),
                'time' => $m->waktu_meeting,
                'pesanan' => $m->pesanan?->nama_pasangan,
                'lokasi' => $m->lokasi,
            ]);

        return response()->json(['meetings' => $meetings]);
    }
}
