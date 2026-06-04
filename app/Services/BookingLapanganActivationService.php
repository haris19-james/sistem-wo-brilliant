<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

/**
 * Mengaktifkan booking untuk tim lapangan setelah pembayaran diverifikasi admin:
 * assign Korlap, set status Confirmed, dan buat tugas vendor otomatis.
 */
class BookingLapanganActivationService
{
    public function __construct(
        protected VendorFieldTaskService $vendorFieldTaskService
    ) {}

    /**
     * @return array{korlap_id: int|null, tasks_created: int, status_pemesanan: string}
     */
    public function activate(Pesanan $pesanan, ?int $korlapId = null): array
    {
        $pesanan->refresh();

        if (! in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            throw new \InvalidArgumentException('Booking belum diverifikasi pembayaran (DP/Lunas).');
        }

        if (! $pesanan->korlap_id) {
            $resolvedKorlap = $korlapId ?? $this->resolveDefaultKorlapId();
            if (! $resolvedKorlap) {
                throw new \InvalidArgumentException('Belum ada Korlap. Pilih Koordinator Lapangan terlebih dahulu.');
            }
            $pesanan->korlap_id = $resolvedKorlap;
        } elseif ($korlapId && (int) $pesanan->korlap_id !== (int) $korlapId) {
            $pesanan->korlap_id = $korlapId;
        }

        $statusPemesanan = $pesanan->status_pembayaran === 'fully_paid' ? 'confirmed' : 'confirmed';

        $updates = [
            'korlap_id' => $pesanan->korlap_id,
            'status_pemesanan' => $statusPemesanan,
            'verified_admin_id' => $pesanan->verified_admin_id ?? Auth::id(),
            'verified_by_admin_at' => $pesanan->verified_by_admin_at ?? now(),
        ];

        if ($pesanan->status === 'Menunggu') {
            $updates['status'] = 'Sedang Berlangsung';
        }

        if ($pesanan->status_pembayaran === 'fully_paid' && ! $pesanan->fully_paid_by_admin_at) {
            $updates['fully_paid_by_admin_at'] = now();
        }

        $pesanan->update($updates);
        $pesanan->refresh();

        $tasksCreated = $this->vendorFieldTaskService->provisionTasksForPesanan($pesanan);

        app(BookingCancellationService::class)->syncStatusBooking($pesanan);

        Log::info('[BookingLapanganActivation] Booking diaktifkan untuk tim lapangan', [
            'pesanan_id' => $pesanan->id,
            'korlap_id' => $pesanan->korlap_id,
            'tasks_created' => $tasksCreated,
        ]);

        return [
            'korlap_id' => $pesanan->korlap_id,
            'tasks_created' => $tasksCreated,
            'status_pemesanan' => $pesanan->status_pemesanan,
        ];
    }

    public function needsActivation(Pesanan $pesanan): bool
    {
        if (! in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            return false;
        }

        if (! $pesanan->korlap_id) {
            return true;
        }

        if (! in_array($pesanan->status_pemesanan, ['confirmed', 'on_progress', 'completed'], true)) {
            return true;
        }

        return $this->countMissingVendorTasks($pesanan) > 0;
    }

    public function countMissingVendorTasks(Pesanan $pesanan): int
    {
        if (! Schema::hasTable('tugas') || ! Schema::hasTable('pesanan_vendor')) {
            return 0;
        }

        $pesanan->loadMissing('vendors');
        $vendorCount = $pesanan->vendors->count();
        if ($vendorCount === 0) {
            return 0;
        }

        $existing = Tugas::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('is_auto_generated', true)
            ->distinct()
            ->count('vendor_id');

        return max(0, $vendorCount - $existing);
    }

    protected function resolveDefaultKorlapId(): ?int
    {
        $korlap = User::query()
            ->where('role', 'lapangan')
            ->when(
                Schema::hasColumn('pesanans', 'korlap_id'),
                fn ($q) => $q->withCount([
                    'korlapPesanans as active_pesanans_count' => fn ($inner) => $inner
                        ->whereIn('status', ['Menunggu', 'Sedang Berlangsung', 'Mendesak']),
                ])
            )
            ->orderBy('active_pesanans_count')
            ->orderBy('name')
            ->value('id');

        return $korlap ? (int) $korlap : null;
    }
}
