<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\TaskChecklist;
use App\Models\Tugas;
use App\Models\User;
use App\Models\Vendor;
use Carbon\Carbon;

class VendorFieldTaskService
{
    /**
     * Buat tugas rutin otomatis saat vendor di-assign ke booking.
     */
    public function generateRoutineTasksForVendor(Pesanan $pesanan, Vendor $vendor, ?int $korlapUserId = null): ?Tugas
    {
        $korlapId = $korlapUserId ?? $pesanan->korlap_id;
        if (! $korlapId) {
            return null;
        }

        $exists = Tugas::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('vendor_id', $vendor->id)
            ->where('is_auto_generated', true)
            ->exists();

        if ($exists) {
            return null;
        }

        $deadline = $this->resolveDeadline($pesanan);
        $template = $this->routineTemplate($vendor);

        $tugas = Tugas::create([
            'user_id' => $korlapId,
            'pesanan_id' => $pesanan->id,
            'vendor_id' => $vendor->id,
            'pic_id' => $korlapId,
            'nama_tugas' => $template['nama'],
            'kategori' => $vendor->kategori,
            'prioritas' => 'medium',
            'deadline' => $deadline,
            'catatan' => 'Tugas rutin otomatis — '.$vendor->nama_vendor.' untuk '.$pesanan->nama_pasangan,
            'status' => 'pending',
            'is_auto_generated' => true,
        ]);

        foreach ($template['checklists'] as $index => $deskripsi) {
            TaskChecklist::create([
                'tugas_id' => $tugas->id,
                'deskripsi' => $deskripsi,
                'is_completed' => false,
                'urutan' => $index,
            ]);
        }

        app(NotificationCenterService::class)->taskCreatedForKorlap($pesanan, $tugas->nama_tugas);

        return $tugas;
    }

    /**
     * Buat tugas rutin untuk semua vendor pada booking (idempotent per vendor).
     */
    public function provisionTasksForPesanan(Pesanan $pesanan): int
    {
        if (! $pesanan->korlap_id) {
            return 0;
        }

        $pesanan->loadMissing('vendors');
        $created = 0;

        foreach ($pesanan->vendors as $vendor) {
            if ($this->generateRoutineTasksForVendor($pesanan, $vendor, (int) $pesanan->korlap_id)) {
                $created++;
            }
        }

        return $created;
    }

    /**
     * Setelah Korlap verifikasi, sinkronkan status kehadiran vendor di pivot.
     */
    public function syncVendorPerformance(Pesanan $pesanan, Vendor $vendor): void
    {
        $hasOpen = Tugas::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('vendor_id', $vendor->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();

        if ($hasOpen) {
            return;
        }

        $allDone = Tugas::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('vendor_id', $vendor->id)
            ->where('status', 'completed')
            ->exists();

        if ($allDone) {
            $pesanan->vendors()->updateExistingPivot($vendor->id, [
                'status' => 'Hadir',
            ]);
        }
    }

    protected function resolveDeadline(Pesanan $pesanan): Carbon
    {
        if ($pesanan->tanggal_acara) {
            $time = $pesanan->jam_acara ? substr((string) $pesanan->jam_acara, 0, 5) : '12:00';

            return Carbon::parse($pesanan->tanggal_acara->format('Y-m-d').' '.$time);
        }

        return now()->addDays(7);
    }

    /**
     * @return array{nama: string, checklists: array<int, string>}
     */
    protected function routineTemplate(Vendor $vendor): array
    {
        $kategori = strtolower((string) $vendor->kategori);

        return match (true) {
            str_contains($kategori, 'dekor') => [
                'nama' => 'Pasang & finalisasi dekorasi',
                'checklists' => [
                    'Survey layout venue',
                    'Pasang dekorasi utama',
                    'Cek lighting & finishing',
                ],
            ],
            str_contains($kategori, 'cater') => [
                'nama' => 'Persiapan catering & hidangan',
                'checklists' => [
                    'Setup area buffet',
                    'Koordinasi jumlah porsi',
                    'Quality check hidangan',
                ],
            ],
            str_contains($kategori, 'makeup') || str_contains($kategori, 'mua') => [
                'nama' => 'Makeup & busana pengantin',
                'checklists' => [
                    'Trial touch-up',
                    'Makeup sesi akad/resepsi',
                    'Touch-up pernikahan',
                ],
            ],
            str_contains($kategori, 'dokum') || str_contains($kategori, 'foto') => [
                'nama' => 'Dokumentasi acara',
                'checklists' => [
                    'Briefing angle & rundown',
                    'Dokumentasi sesi utama',
                    'Serah file preview ke Korlap',
                ],
            ],
            default => [
                'nama' => 'Operasional vendor — '.$vendor->nama_vendor,
                'checklists' => [
                    'Briefing dengan Korlap',
                    'Eksekusi layanan di lapangan',
                    'Konfirmasi penyelesaian ke Korlap',
                ],
            ],
        };
    }
}
