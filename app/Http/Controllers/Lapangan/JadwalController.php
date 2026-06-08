<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\JadwalMeeting;
use App\Models\Pesanan;
use App\Services\LapanganVendorMeetingService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request, LapanganVendorMeetingService $meetingService)
    {
        $start = Carbon::today();
        $end = $start->copy()->addDays(30);
        $korlapId = auth()->id();

        $pesanans = Pesanan::where('korlap_id', $korlapId)
            ->with(['paket', 'progress', 'rundowns', 'jadwalMeetings', 'user:id,name'])
            ->whereNotIn('status', ['Dibatalkan'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_acara', [$start, $end])
                    ->orWhere('status', 'Sedang Berlangsung');
            })
            ->orderBy('tanggal_acara')
            ->get();

        $meetings = JadwalMeeting::with('pesanan')
            ->whereBetween('tanggal_meeting', [$start, $end])
            ->orderBy('tanggal_meeting')
            ->orderBy('waktu_meeting')
            ->get();

        $meetingFilters = [
            'tanggal' => $request->input('tanggal'),
            'klien' => $request->input('klien', ''),
            'status' => $request->input('status', 'semua'),
        ];

        $vendorMeetingData = $meetingService->groupedForKorlap($korlapId, $meetingFilters);

        $bookingsForMeeting = Pesanan::visibleToKorlap($korlapId)
            ->with(['user:id,name,email'])
            ->orderByDesc('tanggal_acara')
            ->get()
            ->map(fn (Pesanan $p) => [
                'id' => $p->id,
                'nomor_pesanan' => $p->nomor_pesanan,
                'client_name' => ($name = trim((string) ($p->user?->name ?? ''))) !== ''
                    ? $name
                    : trim((string) ($p->nama_pasangan ?? 'Klien')),
                'nama_pasangan' => $p->nama_pasangan,
                'tanggal_acara' => $p->tanggal_acara?->translatedFormat('d M Y') ?? '—',
                'payment_label' => $p->status_pembayaran_label,
            ])
            ->values();

        $activeMenu = $request->input('section') === 'meetings'
            || $request->filled('tanggal')
            || $request->filled('klien')
            || $request->has('status')
            ? 'jadwal-meeting'
            : 'jadwal-rundown';

        return view('lapangan.modules.jadwal.index', [
            'activeMenu' => $activeMenu,
            'pesanans' => $pesanans,
            'meetings' => $meetings,
            'meetingGroups' => $vendorMeetingData['groups'],
            'meetingTotal' => $vendorMeetingData['total_meetings'],
            'meetingFilters' => $vendorMeetingData['filters'],
            'bookingsForMeeting' => $bookingsForMeeting,
        ]);
    }
}
