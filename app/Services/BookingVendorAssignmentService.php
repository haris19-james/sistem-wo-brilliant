<?php

namespace App\Services;

use App\Models\Paket;
use App\Models\Pesanan;
use App\Models\Vendor;
use App\Models\VendorMeeting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class BookingVendorAssignmentService
{
    /**
     * Sinkronkan vendor paket ke pesanan & buat draft technical meeting per vendor.
     * Gagal di sini tidak boleh menggagalkan booking utama (dipanggil dalam try-catch).
     *
     * @param  list<int>|null  $vendorIds  Vendor eksplisit (paket kustom); null = ambil dari relasi paket.
     */
    public function assignFromPaket(Pesanan $pesanan, Paket $paket, ?array $vendorIds = null): int
    {
        if (! Schema::hasTable('pesanan_vendor')) {
            return 0;
        }

        $vendors = $this->resolveVendors($paket, $vendorIds);

        if ($vendors->isEmpty()) {
            return 0;
        }

        $ids = $vendors->pluck('id')->all();

        if ($paket->isPaketKustom()) {
            $pesanan->vendors()->syncWithoutDetaching($ids);
        } else {
            $pesanan->vendors()->sync($ids);
        }

        $this->generateFieldTasks($pesanan, $vendors);

        return $this->createDraftMeetings($pesanan, $vendors);
    }

    /**
     * @param  list<int>|null  $vendorIds
     * @return Collection<int, Vendor>
     */
    private function resolveVendors(Paket $paket, ?array $vendorIds): Collection
    {
        if ($vendorIds !== null && $vendorIds !== []) {
            return Vendor::query()
                ->whereIn('id', $vendorIds)
                ->where('status', 'Aktif')
                ->orderBy('kategori')
                ->orderBy('nama_vendor')
                ->get();
        }

        if (! Schema::hasTable('paket_vendor')) {
            return collect();
        }

        $paket->loadMissing('vendors');

        return $paket->vendors
            ->where('status', 'Aktif')
            ->sortBy(fn (Vendor $v) => $v->kategori.'-'.$v->nama_vendor)
            ->values();
    }

    /**
     * @param  Collection<int, Vendor>  $vendors
     */
    private function createDraftMeetings(Pesanan $pesanan, Collection $vendors): int
    {
        if (! Schema::hasTable('vendor_meetings')) {
            return 0;
        }

        $created = 0;
        $meetingDate = $pesanan->tanggal_acara
            ? $pesanan->tanggal_acara->copy()->subDays(21)
            : now()->addDays(14);

        if ($meetingDate->isPast()) {
            $meetingDate = now()->addDays(7);
        }

        foreach ($vendors as $vendor) {
            $exists = VendorMeeting::query()
                ->where('booking_id', $pesanan->id)
                ->when(
                    Schema::hasColumn('vendor_meetings', 'vendor_id'),
                    fn ($q) => $q->where('vendor_id', $vendor->id)
                )
                ->where('title', 'like', '%'.$vendor->nama_vendor.'%')
                ->exists();

            if ($exists) {
                continue;
            }

            $payload = [
                'booking_id' => $pesanan->id,
                'korlap_id' => $pesanan->korlap_id ?? null,
                'title' => 'Technical Meeting Awal dengan '.$vendor->nama_vendor,
                'meeting_date' => $meetingDate->toDateString(),
                'meeting_time' => '10:00:00',
                'location' => $pesanan->lokasi ?: 'Menyusul / TBD',
                'status' => 'scheduled',
                'agenda_type' => 'technical_meeting',
                'is_auto_generated' => true,
                'notes' => 'Draft otomatis saat booking — kategori: '.$vendor->kategori,
            ];

            if (Schema::hasColumn('vendor_meetings', 'vendor_id')) {
                $payload['vendor_id'] = $vendor->id;
            }

            VendorMeeting::create($payload);
            $created++;
        }

        return $created;
    }

    /**
     * @param  Collection<int, Vendor>  $vendors
     */
    private function generateFieldTasks(Pesanan $pesanan, Collection $vendors): void
    {
        if (! Schema::hasTable('tugas') || ! Schema::hasColumn('tugas', 'vendor_id')) {
            return;
        }

        $taskService = app(VendorFieldTaskService::class);

        foreach ($vendors as $vendor) {
            try {
                $taskService->generateRoutineTasksForVendor($pesanan, $vendor, $pesanan->korlap_id);
            } catch (\Throwable $e) {
                Log::warning('Auto-generate tugas vendor gagal.', [
                    'pesanan_id' => $pesanan->id,
                    'vendor_id' => $vendor->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }
    }

    public function logFailure(Pesanan $pesanan, Paket $paket, \Throwable $e): void
    {
        Log::warning('Auto-assign vendor booking gagal (booking tetap tersimpan).', [
            'pesanan_id' => $pesanan->id,
            'paket_id' => $paket->id,
            'message' => $e->getMessage(),
        ]);
    }
}
