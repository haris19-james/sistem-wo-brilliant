<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\User;
use App\Models\VendorMeeting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk mengotomasi pembuatan agenda standar (milestones) ketika status booking berubah menjadi 'dp_paid'.
 * 
 * Fitur:
 * - Generate 4 agenda default dengan tanggal yang sudah dihitung otomatis
 * - Mencegah duplicate agenda jika sudah pernah dibuat
 * - Menggunakan transaction untuk data consistency
 */
class AgendaGeneratorService
{
    /**
     * Daftar agenda standar yang akan di-generate otomatis saat booking dp_paid.
     * Format: [
     *     'title' => '...',
     *     'days_before' => 60,                    // H-60 dari tanggal acara
     *     'agenda_type' => 'self_preparation',    // Tipe agenda
     *     'default_time' => '10:00:00',           // Waktu default
     *     'location' => 'TBD'                      // Lokasi default
     * ]
     */
    private array $defaultAgendas = [
        [
            'title' => 'Food Testing (Cicip Menu Catering)',
            'days_before' => 60,
            'agenda_type' => 'self_preparation',
            'default_time' => '10:00:00',
            'location' => 'Lokasi Catering',
        ],
        [
            'title' => 'Fitting Baju Pengantin Pertama',
            'days_before' => 45,
            'agenda_type' => 'self_preparation',
            'default_time' => '10:00:00',
            'location' => 'Lokasi Fitting',
        ],
        [
            'title' => 'Batas Akhir Kelengkapan Berkas KUA/Sipil',
            'days_before' => 30,
            'agenda_type' => 'self_preparation',
            'default_time' => '17:00:00',
            'location' => 'KUA / Kantor Catatan Sipil',
        ],
        [
            'title' => 'Gladi Bersih (Rehearsal) di Venue',
            'days_before' => 1,
            'agenda_type' => 'self_preparation',
            'default_time' => '10:00:00',
            'location' => 'Venue Acara',
        ],
    ];

    /**
     * Generate agenda otomatis untuk sebuah booking.
     * 
     * @param Pesanan $booking
     * @return int Jumlah agenda yang berhasil di-generate
     */
    public function generateAgendas(Pesanan $booking): int
    {
        // Validasi bahwa booking memiliki tanggal acara yang valid
        if (!$booking->tanggal_acara) {
            return 0;
        }

        // Gunakan transaction untuk memastikan atomicity
        return DB::transaction(function () use ($booking) {
            // Cek apakah sudah ada agenda auto-generated untuk booking ini
            // Jika sudah ada, jangan buat duplicate
            $existingAutoAgendas = VendorMeeting::query()
                ->where('booking_id', $booking->id)
                ->where('is_auto_generated', true)
                ->count();

            if ($existingAutoAgendas > 0) {
                return 0; // Sudah ada agenda auto-generated, skip
            }

            $createdCount = 0;
            $eventDate = Carbon::parse($booking->tanggal_acara);

            $korlapId = $booking->korlap_id ?? $this->getDefaultKorlapId();

            if (!$korlapId) {
                Log::warning('Auto-generated agenda DP paid skipped because booking has no korlap_id and no default lapangan user was found.', [
                    'booking_id' => $booking->id,
                    'tanggal_acara' => $booking->tanggal_acara,
                ]);

                return 0;
            }

            foreach ($this->defaultAgendas as $agendaTemplate) {
                // Hitung tanggal meeting dengan pengurangan hari
                $meetingDate = $eventDate->copy()->subDays($agendaTemplate['days_before']);

                // Jika tanggal sudah lewat, jangan buat agenda (safety check)
                if ($meetingDate->isPast()) {
                    continue;
                }

                // Create agenda
                VendorMeeting::create([
                    'booking_id' => $booking->id,
                    'korlap_id' => $korlapId,
                    'title' => $agendaTemplate['title'],
                    'meeting_date' => $meetingDate,
                    'meeting_time' => $agendaTemplate['default_time'],
                    'location' => $agendaTemplate['location'],
                    'agenda_type' => $agendaTemplate['agenda_type'],
                    'is_auto_generated' => true,
                    'days_before_event' => $agendaTemplate['days_before'],
                    'status' => 'scheduled',
                    'notes' => 'Agenda auto-generated saat status booking menjadi DP Terverifikasi',
                ]);

                $createdCount++;
            }

            return $createdCount;
        });
    }

    /**
     * Regenerate agenda untuk booking yang sudah ada.
     * Gunakan method ini jika ingin rebuild agenda setelah tanggal acara berubah.
     * 
     * @param Pesanan $booking
     * @return int Jumlah agenda yang berhasil di-generate
     */
    public function regenerateAgendas(Pesanan $booking): int
    {
        // Hapus agenda auto-generated yang lama
        VendorMeeting::query()
            ->where('booking_id', $booking->id)
            ->where('is_auto_generated', true)
            ->delete();

        // Generate agenda baru
        return $this->generateAgendas($booking);
    }

    /**
     * Cek apakah booking sudah memiliki agenda auto-generated.
     * 
     * @param Pesanan $booking
     * @return bool
     */
    public function hasAutoGeneratedAgendas(Pesanan $booking): bool
    {
        return VendorMeeting::query()
            ->where('booking_id', $booking->id)
            ->where('is_auto_generated', true)
            ->exists();
    }

    /**
     * Get default korlap ID (untuk kasus ketika booking belum ditugaskan ke korlap).
     * 
     * Kamu bisa customize logic ini sesuai kebutuhan:
     * - Return fixed user ID (e.g., system admin)
     * - Return first available korlap
     * - Throw exception agar booking harus punya korlap terlebih dahulu
     * 
     * @return int|null
     */
    private function getDefaultKorlapId(): ?int
    {
        $korlap = User::query()
            ->where('role', 'lapangan')
            ->orderBy('id')
            ->first();

        return $korlap?->id;
    }
}
