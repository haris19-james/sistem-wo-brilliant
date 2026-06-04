<?php

namespace App\Services;

use App\Models\Pesanan;
use App\Models\ProgressPersiapan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class KorlapBookingService
{
    public function bookingsQuery(Request $request, int $korlapId): Builder
    {
        $query = Pesanan::query()
            ->with(['paket', 'progress', 'rundowns'])
            ->visibleToKorlap($korlapId)
            ->whereNotIn('status', ['Dibatalkan'])
            ->orderBy('tanggal_acara');

        if ($request->filled('status') && $request->status !== 'semua') {
            $status = $request->status === 'Persiapan' ? 'Menunggu' : $request->status;
            $query->where('status', $status);
        } else {
            $query->aktifLapangan();
        }

        if ($request->filled('search') || $request->filled('q')) {
            $q = $request->input('search', $request->input('q'));
            $query->where(function ($builder) use ($q) {
                $builder->where('nama_pasangan', 'like', "%{$q}%")
                    ->orWhere('nomor_pesanan', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('date') || $request->filled('tanggal')) {
            $date = $request->input('date', $request->input('tanggal'));
            $query->whereDate('tanggal_acara', $date);
        }

        return $query;
    }

    public function serializeListItem(Pesanan $pesanan): array
    {
        return [
            'id' => $pesanan->id,
            'nama_pasangan' => $pesanan->nama_pasangan,
            'nomor_pesanan' => $pesanan->nomor_pesanan,
            'lokasi' => $pesanan->lokasi,
            'status' => $this->displayStatus($pesanan->status),
            'status_raw' => $pesanan->status,
            'tanggal_acara' => $pesanan->tanggal_acara?->format('Y-m-d'),
            'tanggal_formatted' => $pesanan->tanggal_formatted,
            'jam_mulai' => $pesanan->jam_mulai_formatted,
            'jam_selesai' => $pesanan->jam_selesai_formatted,
            'foto_url' => $this->fotoUrl($pesanan),
            'paket_nama' => $pesanan->paket?->nama_paket,
        ];
    }

    public function serializeDetail(Pesanan $pesanan): array
    {
        $pesanan->loadMissing(['paket', 'progress', 'rundowns', 'vendors']);

        $progress = $pesanan->progress;
        $vendorStatus = $this->vendorStatusGrid($progress);
        $vendors = $pesanan->vendors->map(fn ($v) => [
            'id' => $v->id,
            'nama_vendor' => $v->nama_vendor,
            'kategori' => $v->kategori,
            'status' => $v->pivot->status ?? 'Hadir',
            'icon' => $this->vendorIcon($v->kategori),
        ])->values()->all();

        $rundowns = $pesanan->rundowns->map(fn ($r) => [
            'id' => $r->id,
            'waktu_mulai' => $r->waktu_mulai_formatted,
            'waktu_selesai' => $r->waktu_selesai_formatted,
            'kegiatan' => $r->kegiatan,
            'kategori' => $r->kategori_acara,
        ])->values()->all();

        return [
            'booking' => [
                'id' => $pesanan->id,
                'nama_pasangan' => $pesanan->nama_pasangan,
                'nomor_pesanan' => $pesanan->nomor_pesanan,
                'lokasi' => $pesanan->lokasi,
                'status' => $this->displayStatus($pesanan->status),
                'tanggal_formatted' => $pesanan->tanggal_formatted,
                'jam_mulai' => $pesanan->jam_mulai_formatted,
                'jam_selesai' => $pesanan->jam_selesai_formatted,
                'foto_url' => $this->fotoUrl($pesanan),
                'paket_nama' => $pesanan->paket?->nama_paket ?? '—',
                'catatan_khusus' => $pesanan->catatan_khusus,
                'detail_url' => route('lapangan.pesanan.show', $pesanan),
            ],
            'progress_percent' => (int) ($progress?->persentase ?? 0),
            'vendor_status' => $vendorStatus,
            'rundowns' => $rundowns,
            'vendors' => $vendors,
        ];
    }

    /**
     * @return array<int, array{key: string, label: string, status: string}>
     */
    protected function vendorStatusGrid(?ProgressPersiapan $progress): array
    {
        $keys = [
            'dekorasi' => 'Dekorasi',
            'catering' => 'Catering',
            'makeup' => 'MUA',
            'dokumentasi' => 'Dokumentasi',
        ];

        $items = [];
        foreach ($keys as $key => $label) {
            $status = $progress?->{'status_'.$key} ?? 'Menunggu';
            $items[] = [
                'key' => $key,
                'label' => $label,
                'status' => $status,
            ];
        }

        return $items;
    }

    protected function vendorIcon(?string $kategori): string
    {
        return match (strtolower((string) $kategori)) {
            'dekorasi', 'dekorasi & florist' => '🎨',
            'catering', 'catering & hidangan' => '🍽️',
            'makeup', 'makeup & busana' => '💄',
            'dokumentasi' => '📷',
            default => '✓',
        };
    }

    protected function displayStatus(?string $status): string
    {
        return match ($status) {
            'Menunggu' => 'Persiapan',
            default => $status ?? '—',
        };
    }

    protected function fotoUrl(Pesanan $pesanan): string
    {
        if (! empty($pesanan->foto_venue)) {
            return asset('storage/'.$pesanan->foto_venue);
        }

        if ($pesanan->paket?->image_url) {
            return $pesanan->paket->image_url;
        }

        return 'https://via.placeholder.com/120x120?text=Acara';
    }
}
