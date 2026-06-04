<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\JadwalMeeting;
use App\Models\Pesanan;
use App\Models\VendorMeeting;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index()
    {
        $start = Carbon::today();
        $end = $start->copy()->addDays(30);
        $korlapId = auth()->id();

        // ✅ Pesanan yang ditugaskan ke Korlap ini dan belum selesai
        $pesanans = Pesanan::where('korlap_id', $korlapId)
            ->with(['paket', 'progress', 'rundowns', 'jadwalMeetings'])
            ->whereNotIn('status', ['Dibatalkan'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('tanggal_acara', [$start, $end])
                    ->orWhere('status', 'Sedang Berlangsung');
            })
            ->orderBy('tanggal_acara')
            ->get();

        // ✅ Jadwal meeting internal (dari tabel jadwal_meetings)
        $meetings = JadwalMeeting::with('pesanan')
            ->whereBetween('tanggal_meeting', [$start, $end])
            ->orderBy('tanggal_meeting')
            ->orderBy('waktu_meeting')
            ->get();

        // ✅ VENDOR MEETINGS - Jadwal meeting vendor yang ditugaskan ke Korlap ini
        // Tampilkan yang belum completed dan dalam 30 hari ke depan
        $vendorMeetings = VendorMeeting::where('korlap_id', $korlapId)
            ->whereIn('status', ['scheduled', 'ongoing'])  // Hanya yang belum selesai
            ->whereBetween('meeting_date', [$start, $end])
            ->with(['booking.user', 'booking.paket'])
            ->orderBy('meeting_date')
            ->orderBy('meeting_time')
            ->get();

        return view('lapangan.modules.jadwal.index', [
            'activeMenu' => 'jadwal-rundown',
            'pesanans' => $pesanans,
            'meetings' => $meetings,
            'vendorMeetings' => $vendorMeetings,  // ✅ Kirim ke view
        ]);
    }
}
