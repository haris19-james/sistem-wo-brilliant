<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\Vendor;
use App\Support\VendorCategories;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class KorlapVendorService
{
    public function __construct(
        protected VendorReviewService $vendorReviewService
    ) {}

    /**
     * Pesanan yang dianggap "sedang berjalan" untuk monitoring vendor Korlap.
     */
    public function activeBookingsQuery(int $korlapId): Builder
    {
        return Pesanan::query()
            ->visibleToKorlap($korlapId)
            ->aktifLapangan()
            ->whereNotIn('status', ['Dibatalkan']);
    }

    public function vendorsQuery(Request $request, int $korlapId): Builder
    {
        $query = Vendor::query()
            ->where('status', 'Aktif')
            ->orderBy('nama_vendor');

        if ($request->filled('search') || $request->filled('q')) {
            $search = $request->input('search', $request->input('q'));
            $query->where(function ($builder) use ($search) {
                $builder->where('nama_vendor', 'like', "%{$search}%")
                    ->orWhere('kategori', 'like', "%{$search}%")
                    ->orWhere('kontak', 'like', "%{$search}%")
                    ->orWhere('lokasi', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $label = VendorCategories::resolve($request->kategori) ?? $request->kategori;
            $query->where('kategori', $label);
        }

        $monitoringStatus = $request->input('status', $request->input('monitoring_status'));
        if ($monitoringStatus === 'aktif') {
            $query->whereHas('pesanans', fn ($q) => $this->constrainActiveAssignment($q, $korlapId));
        } elseif ($monitoringStatus === 'tersedia') {
            $query->whereDoesntHave('pesanans', fn ($q) => $this->constrainActiveAssignment($q, $korlapId));
        }

        return $query->with([
            'pesanans' => fn ($q) => $this->constrainActiveAssignment($q, $korlapId)
                ->select([
                    'pesanans.id',
                    'pesanans.nomor_pesanan',
                    'pesanans.nama_pasangan',
                    'pesanans.tanggal_acara',
                    'pesanans.lokasi',
                    'pesanans.status',
                    'pesanans.korlap_id',
                ])
                ->withPivot(['status', 'nama_pic', 'kontak_pic', 'waktu_setup'])
                ->orderBy('tanggal_acara'),
        ]);
    }

    /**
     * Filter penugasan aktif pada relasi vendor ↔ pesanan (BelongsToMany).
     */
    protected function constrainActiveAssignment($query, int $korlapId)
    {
        return $query
            ->where('pesanans.korlap_id', $korlapId)
            ->whereIn('pesanans.status_pembayaran', ['dp_paid', 'fully_paid'])
            ->whereIn('pesanans.status', ['Menunggu', 'Sedang Berlangsung']);
    }

    public function serializeListItem(Vendor $vendor): array
    {
        $activeBookings = $this->activeBookingsForVendor($vendor);
        $isActive = $activeBookings->isNotEmpty();
        $primary = $activeBookings->first();

        return [
            'id' => $vendor->id,
            'nama_vendor' => $vendor->nama_vendor,
            'kategori' => $vendor->kategori,
            'kontak' => $vendor->kontak,
            'email' => $this->extractEmail($vendor->kontak),
            'telepon' => $this->extractTelepon($vendor->kontak),
            'lokasi' => $vendor->lokasi,
            'rating' => round((float) ($vendor->rating_avg ?? 0), 1),
            'rating_count' => (int) ($vendor->rating_count ?? 0),
            'image_url' => $vendor->image_url,
            'monitoring_status' => $isActive ? 'aktif_di_acara' : 'tersedia',
            'monitoring_label' => $isActive ? 'Aktif di Acara' : 'Tersedia',
            'nomor_pesanan' => $primary?->nomor_pesanan,
            'pesanan_id' => $primary?->id,
        ];
    }

    public function serializeDetail(Vendor $vendor, int $korlapId): array
    {
        $vendor->load([
            'pesanans' => fn ($q) => $this->constrainActiveAssignment($q, $korlapId)
                ->withPivot(['status', 'nama_pic', 'kontak_pic', 'waktu_setup'])
                ->orderBy('tanggal_acara'),
        ]);

        $activeBookings = $this->activeBookingsForVendor($vendor);
        $isActive = $activeBookings->isNotEmpty();

        $jadwalAktif = $activeBookings->map(function ($pesanan) {
            $pivot = $pesanan->pivot;

            return [
                'pesanan_id' => $pesanan->id,
                'nomor_pesanan' => $pesanan->nomor_pesanan,
                'nama_pasangan' => $pesanan->nama_pasangan,
                'tanggal_formatted' => $pesanan->tanggal_acara?->translatedFormat('d F Y') ?? '—',
                'lokasi' => $pesanan->lokasi,
                'status_acara' => $pesanan->status,
                'kehadiran' => [
                    'status' => $pivot->status ?? 'Belum Hadir',
                    'nama_pic' => $pivot->nama_pic,
                    'kontak_pic' => $pivot->kontak_pic,
                ],
                'checklist' => $this->kehadiranChecklist($pivot->status ?? 'Belum Hadir'),
            ];
        })->values()->all();

        return [
            'vendor' => [
                'id' => $vendor->id,
                'nama_vendor' => $vendor->nama_vendor,
                'kategori' => $vendor->kategori,
                'kontak' => $vendor->kontak,
                'email' => $this->extractEmail($vendor->kontak),
                'telepon' => $this->extractTelepon($vendor->kontak),
                'lokasi' => $vendor->lokasi ?? '—',
                'harga_info' => $vendor->harga_info,
                'rating' => round((float) ($vendor->rating_avg ?? 0), 1),
                'rating_count' => (int) ($vendor->rating_count ?? 0),
                'image_url' => $vendor->image_url,
                'monitoring_status' => $isActive ? 'aktif_di_acara' : 'tersedia',
                'monitoring_label' => $isActive ? 'Aktif di Acara' : 'Tersedia',
                'nomor_pesanan' => $activeBookings->first()?->nomor_pesanan,
            ],
            'jadwal_aktif' => $jadwalAktif,
            'ulasan_klien' => $this->vendorReviewService->serializeReviewsForVendor($vendor),
        ];
    }

    /**
     * @return Collection<int, Pesanan>
     */
    protected function activeBookingsForVendor(Vendor $vendor): Collection
    {
        return $vendor->pesanans
            ->sortBy([
                fn ($p) => $p->status === 'Sedang Berlangsung' ? 0 : 1,
                fn ($p) => $p->tanggal_acara?->timestamp ?? PHP_INT_MAX,
            ])
            ->values();
    }

    /**
     * @return array<int, array{label: string, done: bool}>
     */
    protected function kehadiranChecklist(string $status): array
    {
        $order = ['Belum Hadir', 'Perjalanan', 'Hadir'];
        $currentIndex = array_search($status, $order, true);
        if ($currentIndex === false) {
            $currentIndex = 0;
        }

        $steps = [
            ['key' => 'Belum Hadir', 'label' => 'Belum hadir di lokasi'],
            ['key' => 'Perjalanan', 'label' => 'Dalam perjalanan ke venue'],
            ['key' => 'Hadir', 'label' => 'Hadir & siap operasional (Hari-H)'],
        ];

        return array_map(function ($step, $index) use ($currentIndex) {
            return [
                'label' => $step['label'],
                'done' => $index <= $currentIndex,
            ];
        }, $steps, array_keys($steps));
    }

    protected function extractEmail(?string $kontak): string
    {
        if ($kontak && str_contains($kontak, '@')) {
            if (preg_match('/[\w.\-+]+@[\w.\-]+\.\w+/', $kontak, $m)) {
                return $m[0];
            }

            return trim($kontak);
        }

        return '—';
    }

    protected function extractTelepon(?string $kontak): string
    {
        if (! $kontak) {
            return '—';
        }

        if (str_contains($kontak, '@')) {
            $parts = preg_split('/[|,;]/', $kontak);
            foreach ($parts as $part) {
                $part = trim($part);
                if ($part && ! str_contains($part, '@')) {
                    return $part;
                }
            }

            return '—';
        }

        return $kontak;
    }
}
