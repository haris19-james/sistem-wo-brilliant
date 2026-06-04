<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\JadwalMeeting;
use App\Models\LaporanLapangan;
use App\Models\Pesanan;
use App\Models\Rundown;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorMeeting;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today();
        $mingguDepan = $hariIni->copy()->addDays(7);
        $currentUser = auth()->user();

        // Acara
        $acaraAktif = Pesanan::aktifLapangan()
            ->with(['paket', 'progress', 'user'])
            ->orderBy('tanggal_acara')
            ->get();

        // Acara hari ini: booking Confirmed milik Korlap, tanggal = hari ini
        $acaraHariIni = Pesanan::query()
            ->where('korlap_id', auth()->id())
            ->whereDate('tanggal_acara', $hariIni)
            ->whereIn('status_pemesanan', ['confirmed', 'on_progress'])
            ->whereNotIn('status', ['Dibatalkan'])
            ->with(['paket', 'progress', 'user', 'rundowns'])
            ->orderBy('jam_acara')
            ->get();

        // Jadwal & Meeting
        $meetingMingguIni = JadwalMeeting::with('pesanan')
            ->whereBetween('tanggal_meeting', [$hariIni, $mingguDepan])
            ->where('status', '!=', 'Selesai')
            ->orderBy('tanggal_meeting')
            ->orderBy('waktu_meeting')
            ->take(8)
            ->get();

        // Timeline Jadwal Acara Hari Ini (Rundown)
        // Jika ada acara hari ini untuk Korlap, ambil rundowns untuk booking tersebut
        $bookingIdsHariIni = $acaraHariIni->pluck('id')->toArray();
        $jadwalHariIni = collect();
        if (!empty($bookingIdsHariIni)) {
            $jadwalHariIni = Rundown::with('pesanan')
                ->whereIn('pesanan_id', $bookingIdsHariIni)
                ->orderBy('waktu_mulai', 'asc')
                ->get();
        }

        // Vendor Aktif Hari Ini
        $vendorHariIni = Vendor::aktif()
            ->limit(4)
            ->get();

        $vendorCount = Vendor::aktif()->count();

        // Laporan
        $laporanTerbaru = LaporanLapangan::with(['pesanan', 'user'])
            ->latest()
            ->take(5)
            ->get();

        // Agenda meeting untuk Korlap (menu Jadwal Acara)
        $vendorMeetingsKorlap = VendorMeeting::where('korlap_id', auth()->id())
            ->where('meeting_date', '>=', Carbon::today())
            ->orderBy('meeting_date', 'asc')
            ->orderBy('meeting_time', 'asc')
            ->get();

        $laporanBulanCount = LaporanLapangan::whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->distinct('pesanan_id')
            ->count('pesanan_id');

        $pesananBulanCount = Pesanan::whereMonth('tanggal_acara', now()->month)
            ->whereYear('tanggal_acara', now()->year)
            ->whereNotIn('status', ['Dibatalkan'])
            ->count();

        $laporanPersen = 0;
        if ($pesananBulanCount > 0) {
            $laporanPersen = (int) round(($laporanBulanCount / $pesananBulanCount) * 100);
            $laporanPersen = min(100, max(0, $laporanPersen));
        }

        $progressPersiapan = 0;
        if ($acaraAktif->isNotEmpty()) {
            $progressPersiapan = (int) round($acaraAktif->map(function ($item) {
                return $item->progress?->persentase ?? 0;
            })->avg());
        }

        // Chat Messages - Get latest unique conversations (bukan semua messages)
        $chatTerbaru = $this->getLatestConversations();

        // Stats
        $stats = [
            'hari_ini' => $acaraHariIni->count(),
            'vendor_aktif' => $vendorCount,
            'tugas_pending' => 4, // Placeholder - bisa dari table lain jika ada
            'pesan_belum_dibaca' => $chatTerbaru->count(),
            'berlangsung' => Pesanan::where('status', 'Sedang Berlangsung')->count(),
            'laporan_bulan' => $laporanBulanCount,
            'laporan_persen' => $laporanPersen,
            'progress_persiapan' => $progressPersiapan,
        ];

        return view('lapangan.modules.dashboard', [
            'activeMenu' => 'dashboard',
            'stats' => $stats,
            'currentUser' => $currentUser,
            'hariIni' => $hariIni,
            'acaraAktif' => $acaraAktif,
            'acaraHariIni' => $acaraHariIni,
            'jadwalHariIni' => $jadwalHariIni,
            'vendorHariIni' => $vendorHariIni,
            'meetingMingguIni' => $meetingMingguIni,
            'vendorMeetingsKorlap' => $vendorMeetingsKorlap,
            'laporanTerbaru' => $laporanTerbaru,
            'chatTerbaru' => $chatTerbaru,
        ]);
    }

    /**
     * Get latest conversations with unread count for dashboard
     * Optimized query untuk performa lebih baik
     */
    private function getLatestConversations()
    {
        $currentUserId = auth()->id();
        $conversationData = [];

        // Query untuk mendapatkan contact_id dan last_message_id per konversasi
        $contactIds = ChatMessage::where(function ($q) use ($currentUserId) {
            $q->where('sender_id', $currentUserId)
                ->orWhere('receiver_id', $currentUserId);
        })
            ->selectRaw('
                CASE 
                    WHEN sender_id = ? THEN receiver_id
                    ELSE sender_id 
                END as contact_id,
                MAX(id) as last_message_id
            ', [$currentUserId])
            ->groupBy('contact_id')
            ->orderByDesc('last_message_id')
            ->limit(5)
            ->pluck('contact_id');

        // Ambil detail kontak dan pesan terakhir
        foreach ($contactIds as $contactId) {
            $contact = User::find($contactId);
            if (!$contact) {
                continue;
            }

            // Get last message
            $lastMessage = ChatMessage::where(function ($q) use ($currentUserId, $contactId) {
                $q->where('sender_id', $currentUserId)
                    ->where('receiver_id', $contactId)
                    ->orWhere('sender_id', $contactId)
                    ->where('receiver_id', $currentUserId);
            })
                ->latest()
                ->first();

            // Count unread messages from this contact
            $unreadCount = ChatMessage::where('sender_id', $contactId)
                ->where('receiver_id', $currentUserId)
                ->where('is_read', false)
                ->count();

            $conversationData[] = [
                'id' => $contact->id,
                'contact_id' => $contact->id,
                'nama' => $contact->name,
                'role' => $this->getRoleLabel($contact->role),
                'avatar_initials' => $this->getInitials($contact->name),
                'pesan_terakhir' => $lastMessage?->pesan ?? '-',
                'waktu_terakhir' => $this->getFormattedTime($lastMessage?->created_at),
                'unread_count' => $unreadCount,
                'is_online' => true,
            ];
        }

        return collect($conversationData);
    }

    /**
     * Helper: Get user initials for avatar
     */
    private function getInitials($name): string
    {
        $names = explode(' ', trim($name));
        $initials = '';

        foreach ($names as $n) {
            if ($n) {
                $initials .= strtoupper($n[0]);
            }
        }

        return substr($initials, 0, 2) ?: 'UN';
    }

    /**
     * Helper: Get role label in Indonesian
     */
    private function getRoleLabel($role): string
    {
        $roleMap = [
            'admin' => 'Admin',
            'client' => 'Client',
            'lapangan' => 'Korlap',
            'vendor' => 'Vendor',
        ];

        return $roleMap[$role] ?? ucfirst($role);
    }

    /**
     * Helper: Get formatted time (e.g., 10:24, Kemarin, 2 Hari lalu)
     */
    private function getFormattedTime($dateTime): string
    {
        if (!$dateTime) {
            return '-';
        }

        $now = now();

        // Today
        if ($dateTime->isToday()) {
            return $dateTime->format('H:i');
        }

        // Yesterday
        if ($dateTime->isYesterday()) {
            return 'Kemarin';
        }

        // This week
        if ($dateTime->diffInDays($now) < 7) {
            return $dateTime->diffInDays($now) . ' Hari lalu';
        }

        // Default
        return $dateTime->format('d M');
    }
}
