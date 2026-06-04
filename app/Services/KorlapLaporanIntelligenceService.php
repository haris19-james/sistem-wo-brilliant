<?php

namespace App\Services;

use App\Models\LaporanLapangan;
use App\Models\OperasionalLapangan;
use App\Models\Pesanan;
use App\Services\VendorKeuanganService;
use App\Models\RealisasiOperasional;
use App\Models\Tugas;
use App\Models\Vendor;
use App\Models\VendorAttendance;
use App\Support\AdminPerformanceCache;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class KorlapLaporanIntelligenceService
{
    /**
     * @return array<string, mixed>
     */
    public function build(int $korlapId, ?int $pesananId = null): array
    {
        $pesananQuery = Pesanan::query()
            ->visibleToKorlap($korlapId)
            ->aktifLapangan()
            ->orderBy('tanggal_acara');

        $acaraList = (clone $pesananQuery)->get(['id', 'nama_pasangan', 'nomor_pesanan', 'tanggal_acara', 'jam_acara', 'status_pembayaran']);

        $scopedPesananIds = $pesananId
            ? $acaraList->where('id', $pesananId)->pluck('id')
            : $acaraList->pluck('id');

        if ($pesananId && $scopedPesananIds->isEmpty()) {
            abort(403, 'Anda tidak memiliki akses ke acara ini.');
        }

        $selectedPesanan = $pesananId ? $acaraList->firstWhere('id', $pesananId) : null;

        return [
            'acaraList' => $acaraList,
            'selectedPesananId' => $pesananId,
            'selectedPesanan' => $selectedPesanan,
            'kpis' => $this->kpis($korlapId, $scopedPesananIds, $selectedPesanan),
            'attendanceRows' => $this->attendanceRows($korlapId, $scopedPesananIds, $selectedPesanan),
            'kendalaChart' => $this->kendalaByKategori($scopedPesananIds),
            'kendalaRecent' => $this->recentKendala($scopedPesananIds),
            'kendalaAktif' => $this->kendalaByStatus($scopedPesananIds, ['Menunggu Tindakan', 'Dalam Penanganan'], 8),
            'kendalaSelesai' => $this->kendalaByStatus($scopedPesananIds, ['Selesai'], 6),
            'topVendors' => $this->topRatedVendors($korlapId, $scopedPesananIds),
            'problemVendors' => $this->problemVendors($korlapId, $scopedPesananIds),
            'financial' => $this->financialSummary($scopedPesananIds, $selectedPesanan),
            'vendorBills' => $this->vendorBillRows($scopedPesananIds),
        ];
    }

    /**
     * KPI + chart only — cached for AJAX refresh (avoids full build()).
     *
     * @return array{kpis: array<string, mixed>, kendala_chart: array<int, mixed>}
     */
    public function metricsPayload(int $korlapId, ?int $pesananId = null): array
    {
        $cacheKey = AdminPerformanceCache::korlapMetricsKey($korlapId, $pesananId);

        return Cache::remember($cacheKey, now()->addSeconds(45), function () use ($korlapId, $pesananId) {
            $scopedPesananIds = $this->resolveScopedPesananIds($korlapId, $pesananId);
            $selectedPesanan = $pesananId
                ? Pesanan::query()->find($pesananId, ['id', 'status_pembayaran'])
                : null;

            return [
                'kpis' => $this->kpis($korlapId, $scopedPesananIds, $selectedPesanan),
                'kendala_chart' => $this->kendalaByKategori($scopedPesananIds),
            ];
        });
    }

    /**
     * @return \Illuminate\Support\Collection<int, int>
     */
    protected function resolveScopedPesananIds(int $korlapId, ?int $pesananId): Collection
    {
        $query = Pesanan::query()
            ->visibleToKorlap($korlapId)
            ->aktifLapangan();

        if ($pesananId) {
            $ids = (clone $query)->where('id', $pesananId)->pluck('id');
            if ($ids->isEmpty()) {
                abort(403, 'Anda tidak memiliki akses ke acara ini.');
            }

            return $ids;
        }

        return $query->pluck('id');
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<string, mixed>
     */
    protected function kpis(int $korlapId, Collection $pesananIds, ?Pesanan $selected): array
    {
        $vendorQuery = DB::table('pesanan_vendor')
            ->whereIn('pesanan_id', $pesananIds);

        $totalVendors = (clone $vendorQuery)->count();

        $hadirQuery = VendorAttendance::query()
            ->whereIn('pesanan_id', $pesananIds)
            ->whereNotNull('korlap_confirmed_at')
            ->where('status', 'Hadir');

        $vendorHadir = (clone $hadirQuery)->count();

        $tugasQuery = Tugas::query()->forKorlap($korlapId);
        if ($pesananIds->isNotEmpty()) {
            $tugasQuery->whereIn('pesanan_id', $pesananIds);
        }
        $totalTugas = (clone $tugasQuery)->count();
        $tugasVerified = (clone $tugasQuery)->where('status', 'completed')->whereNotNull('korlap_verified_at')->count();
        $progresPct = $totalTugas > 0 ? (int) round(($tugasVerified / $totalTugas) * 100) : 0;

        $kendalaAktif = LaporanLapangan::query()
            ->whereIn('pesanan_id', $pesananIds->isEmpty() ? [-1] : $pesananIds)
            ->whereIn('status_tindak', ['Menunggu Tindakan', 'Dalam Penanganan'])
            ->count();

        $vendorPerluBayar = $this->countVendorsNeedingPayment($pesananIds);

        $paymentLabel = match ($selected?->status_pembayaran) {
            'fully_paid' => 'Lunas',
            'dp_paid' => 'DP Terbayar',
            default => 'Menunggu',
        };

        return [
            'vendor_hadir' => ['present' => $vendorHadir, 'total' => max($totalVendors, 1), 'raw_total' => $totalVendors],
            'vendor_hadir_pct' => $totalVendors > 0 ? (int) round(($vendorHadir / $totalVendors) * 100) : 0,
            'progres_tugas' => ['verified' => $tugasVerified, 'total' => $totalTugas, 'percent' => $progresPct],
            'kendala_aktif' => $kendalaAktif,
            'vendor_perlu_bayar' => $vendorPerluBayar,
            'status_pembayaran_label' => $paymentLabel,
            'status_pembayaran_code' => $selected?->status_pembayaran ?? 'unpaid',
        ];
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<int, array<string, mixed>>
     */
    protected function attendanceRows(int $korlapId, Collection $pesananIds, ?Pesanan $selected): array
    {
        if ($pesananIds->isEmpty()) {
            return [];
        }

        $targetIds = $selected ? collect([$selected->id]) : $pesananIds;

        $assignments = DB::table('pesanan_vendor')
            ->join('vendors', 'vendors.id', '=', 'pesanan_vendor.vendor_id')
            ->join('pesanans', 'pesanans.id', '=', 'pesanan_vendor.pesanan_id')
            ->whereIn('pesanan_vendor.pesanan_id', $targetIds)
            ->where('pesanans.korlap_id', $korlapId)
            ->select([
                'pesanan_vendor.pesanan_id',
                'pesanan_vendor.vendor_id',
                'pesanan_vendor.waktu_setup',
                'pesanan_vendor.status as pivot_status',
                'vendors.nama_vendor',
                'vendors.kategori',
                'pesanans.tanggal_acara',
                'pesanans.jam_acara',
                'pesanans.nama_pasangan',
            ])
            ->orderBy('vendors.nama_vendor')
            ->get();

        $attendanceMap = VendorAttendance::query()
            ->whereIn('pesanan_id', $targetIds)
            ->get()
            ->keyBy(fn ($a) => $a->pesanan_id.'-'.$a->vendor_id);

        return $assignments->map(function ($row) use ($attendanceMap, $korlapId) {
            $key = $row->pesanan_id.'-'.$row->vendor_id;
            $att = $attendanceMap->get($key);
            $expected = $this->expectedArrival($row);

            $status = $att?->status ?? ($row->pivot_status === 'Hadir' ? 'Hadir' : 'Belum Hadir');
            if ($att && $att->korlap_confirmed_at && $att->status === 'Hadir') {
                $status = 'Hadir';
            } elseif ($att?->is_late) {
                $status = 'Terlambat';
            }

            return [
                'pesanan_id' => $row->pesanan_id,
                'vendor_id' => $row->vendor_id,
                'nama_vendor' => $row->nama_vendor,
                'kategori' => $row->kategori,
                'acara' => $row->nama_pasangan,
                'expected_at' => $expected?->format('Y-m-d H:i'),
                'arrived_at' => $att?->arrived_at?->format('d M Y, H:i') ?? '—',
                'status' => $status,
                'confirmed' => (bool) $att?->korlap_confirmed_at,
                'can_confirm' => ! $att?->korlap_confirmed_at,
            ];
        })->values()->all();
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<int, array{label: string, count: int}>
     */
    protected function kendalaByKategori(Collection $pesananIds): array
    {
        if ($pesananIds->isEmpty()) {
            return [];
        }

        $rows = LaporanLapangan::query()
            ->whereIn('pesanan_id', $pesananIds)
            ->select('kategori', DB::raw('count(*) as total'))
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        return $rows->map(fn ($r) => [
            'label' => $r->kategori ?: 'Lainnya',
            'count' => (int) $r->total,
        ])->values()->all();
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     */
    protected function recentKendala(Collection $pesananIds): Collection
    {
        return $this->kendalaByStatus($pesananIds, ['Menunggu Tindakan', 'Dalam Penanganan', 'Selesai'], 6);
    }

    /**
     * @param  array<int, string>  $statuses
     */
    protected function kendalaByStatus(Collection $pesananIds, array $statuses, int $limit): Collection
    {
        if ($pesananIds->isEmpty()) {
            return collect();
        }

        return LaporanLapangan::with('pesanan:id,nama_pasangan,nomor_pesanan')
            ->whereIn('pesanan_id', $pesananIds)
            ->whereIn('status_tindak', $statuses)
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<int, array<string, mixed>>
     */
    protected function topRatedVendors(int $korlapId, Collection $pesananIds): array
    {
        $vendorIds = DB::table('pesanan_vendor')
            ->join('pesanans', 'pesanans.id', '=', 'pesanan_vendor.pesanan_id')
            ->where('pesanans.korlap_id', $korlapId)
            ->when($pesananIds->isNotEmpty(), fn ($q) => $q->whereIn('pesanan_vendor.pesanan_id', $pesananIds))
            ->distinct()
            ->pluck('pesanan_vendor.vendor_id');

        if ($vendorIds->isEmpty()) {
            return [];
        }

        return Vendor::query()
            ->whereIn('id', $vendorIds)
            ->where('rating_count', '>', 0)
            ->orderByDesc('rating_avg')
            ->orderByDesc('rating_count')
            ->limit(5)
            ->get(['id', 'nama_vendor', 'kategori', 'rating_avg', 'rating_count'])
            ->map(fn ($v) => [
                'id' => $v->id,
                'nama' => $v->nama_vendor,
                'kategori' => $v->kategori,
                'rating' => round((float) $v->rating_avg, 1),
                'reviews' => (int) $v->rating_count,
            ])
            ->all();
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<int, array<string, mixed>>
     */
    protected function problemVendors(int $korlapId, Collection $pesananIds): array
    {
        $lateCounts = VendorAttendance::query()
            ->join('pesanans', 'pesanans.id', '=', 'vendor_attendance.pesanan_id')
            ->where('pesanans.korlap_id', $korlapId)
            ->when($pesananIds->isNotEmpty(), fn ($q) => $q->whereIn('vendor_attendance.pesanan_id', $pesananIds))
            ->where(function ($q) {
                $q->where('vendor_attendance.is_late', true)
                    ->orWhere('vendor_attendance.status', 'Terlambat');
            })
            ->select('vendor_attendance.vendor_id', DB::raw('count(*) as late_count'))
            ->groupBy('vendor_attendance.vendor_id')
            ->pluck('late_count', 'vendor_id');

        $openTugas = Tugas::query()
            ->forKorlap($korlapId)
            ->when($pesananIds->isNotEmpty(), fn ($q) => $q->whereIn('pesanan_id', $pesananIds))
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->whereNotNull('vendor_id')
            ->select('vendor_id', DB::raw('count(*) as open_count'))
            ->groupBy('vendor_id')
            ->pluck('open_count', 'vendor_id');

        $vendorIds = $lateCounts->keys()->merge($openTugas->keys())->unique();

        if ($vendorIds->isEmpty()) {
            return [];
        }

        $vendors = Vendor::whereIn('id', $vendorIds)->get()->keyBy('id');

        return $vendorIds->map(function ($id) use ($lateCounts, $openTugas, $vendors) {
            $v = $vendors->get($id);
            if (! $v) {
                return null;
            }
            $score = ((int) ($lateCounts[$id] ?? 0) * 2) + (int) ($openTugas[$id] ?? 0);

            return [
                'id' => $v->id,
                'nama' => $v->nama_vendor,
                'kategori' => $v->kategori,
                'late_count' => (int) ($lateCounts[$id] ?? 0),
                'open_tasks' => (int) ($openTugas[$id] ?? 0),
                'score' => $score,
            ];
        })
            ->filter()
            ->sortByDesc('score')
            ->take(3)
            ->values()
            ->all();
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<string, mixed>
     */
    protected function financialSummary(Collection $pesananIds, ?Pesanan $selected): array
    {
        if ($pesananIds->isEmpty()) {
            return [
                'total_biaya' => 0,
                'dibayar' => 0,
                'sisa_pelunasan' => 0,
                'menunggu_review' => 0,
                'status' => 'menunggu',
            ];
        }

        return app(VendorKeuanganService::class)->financialSummary($pesananIds, $selected);
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     */
    protected function countVendorsNeedingPayment(Collection $pesananIds): int
    {
        if ($pesananIds->isEmpty()) {
            return 0;
        }

        $unpaidPesanan = Pesanan::query()
            ->whereIn('id', $pesananIds)
            ->whereIn('status_pembayaran', ['unpaid', 'dp_paid'])
            ->pluck('id');

        return (int) DB::table('pesanan_vendor')
            ->whereIn('pesanan_id', $unpaidPesanan)
            ->distinct('vendor_id')
            ->count('vendor_id');
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<int, array<string, mixed>>
     */
    protected function vendorBillRows(Collection $pesananIds): array
    {
        return app(VendorKeuanganService::class)->vendorBillRows($pesananIds, 8);
    }

    protected function expectedArrival(object $row): ?Carbon
    {
        if (! $row->tanggal_acara) {
            return null;
        }

        $time = $row->waktu_setup
            ? substr((string) $row->waktu_setup, 0, 5)
            : ($row->jam_acara ? substr((string) $row->jam_acara, 0, 5) : '08:00');

        try {
            return Carbon::parse($row->tanggal_acara.' '.$time);
        } catch (\Throwable) {
            return null;
        }
    }

    public function confirmAttendance(int $korlapId, Pesanan $pesanan, int $vendorId): VendorAttendance
    {
        if ($pesanan->korlap_id !== $korlapId) {
            abort(403);
        }

        if (! $pesanan->vendors()->where('vendor_id', $vendorId)->exists()) {
            abort(422, 'Vendor tidak terdaftar pada acara ini.');
        }

        $expected = $pesanan->vendors()->where('vendor_id', $vendorId)->first();
        $pivot = $expected?->pivot;
        $expectedAt = $this->expectedArrival((object) [
            'tanggal_acara' => $pesanan->tanggal_acara?->format('Y-m-d'),
            'jam_acara' => $pesanan->jam_acara,
            'waktu_setup' => $pivot?->waktu_setup,
        ]);

        $now = now();
        $isLate = $expectedAt && $now->gt($expectedAt->copy()->addMinutes(15));
        $status = $isLate ? 'Terlambat' : 'Hadir';

        $attendance = VendorAttendance::updateOrCreate(
            ['pesanan_id' => $pesanan->id, 'vendor_id' => $vendorId],
            [
                'korlap_id' => $korlapId,
                'arrived_at' => $now,
                'status' => $status,
                'is_late' => $isLate,
                'korlap_confirmed_at' => $now,
            ]
        );

        $pesanan->vendors()->updateExistingPivot($vendorId, ['status' => 'Hadir']);

        $vendorName = Vendor::query()->whereKey($vendorId)->value('nama_vendor') ?? 'Vendor';

        app(NotificationCenterService::class)->vendorCheckInForKorlap($pesanan, $vendorName, $isLate);

        return $attendance;
    }

    public function inferKategoriFromRingkasan(string $ringkasan, string $kondisi): string
    {
        $text = strtolower($ringkasan);

        return match (true) {
            str_contains($text, 'cater') || str_contains($text, 'makan') => 'Katering',
            str_contains($text, 'vendor') => 'Vendor',
            str_contains($text, 'dekor') => 'Dekorasi',
            str_contains($text, 'listrik') || str_contains($text, 'sound') || str_contains($text, 'teknis') => 'Teknis',
            str_contains($text, 'transport') || str_contains($text, 'parkir') => 'Logistik',
            default => $kondisi === 'Kritis' ? 'Vendor' : 'Lainnya',
        };
    }
}
