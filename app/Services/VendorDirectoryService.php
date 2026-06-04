<?php

namespace App\Services;

use App\Models\Vendor;
use Illuminate\Support\Collection;

class VendorDirectoryService
{
    /**
     * @return array<string, mixed>
     */
    public function cardPayload(Vendor $vendor): array
    {
        $rating = (float) ($vendor->rating_avg ?? 0);
        $displayRating = $rating > 0 ? $rating : 4.8;

        return [
            'id' => $vendor->id,
            'nama_vendor' => (string) $vendor->nama_vendor,
            'kategori' => (string) ($vendor->kategori ?? ''),
            'lokasi' => (string) ($vendor->lokasi ?? ''),
            'harga_info' => $vendor->harga_info ? (string) $vendor->harga_info : null,
            'kontak' => $vendor->kontak ? (string) $vendor->kontak : null,
            'status' => (string) ($vendor->status ?? ''),
            'image_url' => is_string($vendor->image_url) ? $vendor->image_url : null,
            'rating_avg' => round($displayRating, 1),
            'rating_count' => (int) $vendor->rating_count,
            'instagram_url' => $vendor->instagramUrl(),
            'whatsapp_url' => $vendor->whatsappUrl(),
            'website_url' => $vendor->websiteUrl(),
            'edit_url' => route('admin.vendor.edit', $vendor),
            'destroy_url' => route('admin.vendor.destroy', $vendor),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function detailPayload(Vendor $vendor): array
    {
        $vendor->load(['pesanans' => fn ($q) => $q->with('paket')->orderByDesc('tanggal_acara')->limit(12)]);

        $projects = $vendor->pesanans->map(fn ($p) => [
            'id' => $p->id,
            'nomor_pesanan' => $p->nomor_pesanan,
            'nama_pasangan' => $p->nama_pasangan,
            'paket' => $p->paket?->nama_paket,
            'tanggal' => $p->tanggal_acara?->translatedFormat('d M Y') ?? '—',
            'status' => $p->status,
            'lokasi' => $p->lokasi,
        ]);

        $completed = $vendor->pesanans()->where('pesanans.status', 'Selesai')->count();
        $total = $vendor->pesanans()->count();
        $rating = (float) ($vendor->rating_avg ?? 0);
        $displayRating = $rating > 0 ? $rating : 4.8;

        return array_merge($this->cardPayload($vendor), [
            'deskripsi' => null,
            'portfolio' => $vendor->portfolioGallery(),
            'projects' => $projects->values()->all(),
            'stats' => [
                'proyek_selesai' => $completed,
                'total_proyek' => $total,
                'rating_klien' => round($displayRating, 1),
                'jumlah_ulasan' => (int) $vendor->rating_count,
            ],
        ]);
    }

    /**
     * @return Collection<int, string>
     */
    public function distinctLocations(Collection $vendors): Collection
    {
        return $vendors->pluck('lokasi')
            ->filter(fn ($l) => filled($l))
            ->map(fn ($value) => is_scalar($value) ? (string) $value : '')
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }
}
