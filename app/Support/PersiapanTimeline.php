<?php

namespace App\Support;

use App\Models\Pesanan;
use App\Services\ItemTambahanService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class PersiapanTimeline
{
    /**
     * @return array{
     *     countdown: array{hari: int|null, label: string, kelas: string},
     *     progress: array{persentase: int, aspek: array<int, array<string, mixed>>, selesai: int, total: int},
     *     alur: array<int, array<string, mixed>>,
     *     stats: array<string, int>
     * }
     */
    public static function build(Pesanan $pesanan): array
    {
        $pesanan->loadMissing(['progress', 'jadwalMeetings', 'rundowns', 'invoices']);

        $aspek = $pesanan->progress?->aspek_items ?? ProgressPersiapanDefaults::emptyAspek();

        if (Schema::hasTable('item_tambahan')) {
            $aspek = array_merge($aspek, app(ItemTambahanService::class)->korlapAddonChecklistItems($pesanan));
        }

        $selesai = collect($aspek)->where('status', 'Selesai')->count();
        $totalAspek = count($aspek);

        return [
            'countdown' => self::countdown($pesanan),
            'progress' => [
                'persentase' => $pesanan->progress?->persentase ?? 0,
                'persentase_hitung' => $totalAspek > 0
                    ? (int) round(collect($aspek)->avg('progress_percent'))
                    : 0,
                'aspek' => $aspek,
                'selesai' => $selesai,
                'total' => $totalAspek,
            ],
            'alur' => self::alurSteps($pesanan, $aspek),
            'stats' => [
                'meeting' => $pesanan->jadwalMeetings->count(),
                'meeting_selesai' => $pesanan->jadwalMeetings->where('status', 'Selesai')->count(),
                'rundown' => $pesanan->rundowns->count(),
                'invoice_lunas' => $pesanan->invoices->where('status', 'Lunas')->count(),
            ],
        ];
    }

    private static function countdown(Pesanan $pesanan): array
    {
        if (! $pesanan->tanggal_acara) {
            return ['hari' => null, 'label' => 'Tanggal acara belum ditetapkan', 'kelas' => 'text-gray-500'];
        }

        $hari = (int) now()->startOfDay()->diffInDays(
            Carbon::parse($pesanan->tanggal_acara)->startOfDay(),
            false
        );

        if ($hari > 0) {
            return [
                'hari' => $hari,
                'label' => $hari.' hari lagi menuju hari H',
                'kelas' => 'text-bottle',
            ];
        }

        if ($hari === 0) {
            return ['hari' => 0, 'label' => 'Hari ini hari H acara Anda!', 'kelas' => 'text-green-600'];
        }

        return [
            'hari' => abs($hari),
            'label' => 'Acara telah berlangsung '.abs($hari).' hari lalu',
            'kelas' => 'text-gray-500',
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $aspek
     * @return array<int, array<string, mixed>>
     */
    private static function alurSteps(Pesanan $pesanan, array $aspek): array
    {
        $steps = [];

        $steps[] = [
            'fase' => 'booking',
            'judul' => 'Booking Terkirim',
            'deskripsi' => 'Pesanan '.$pesanan->nomor_pesanan.' diterima sistem',
            'status' => 'selesai',
            'tanggal' => $pesanan->created_at?->translatedFormat('d M Y'),
        ];

        $konfirmasiSelesai = ! in_array($pesanan->status, ['Menunggu', 'Dibatalkan'], true);
        $steps[] = [
            'fase' => 'konfirmasi',
            'judul' => 'Konfirmasi Admin',
            'deskripsi' => $konfirmasiSelesai
                ? 'Booking dikonfirmasi — tim mulai koordinasi'
                : 'Menunggu tim admin mengonfirmasi booking',
            'status' => $konfirmasiSelesai ? 'selesai' : ($pesanan->status === 'Menunggu' ? 'berjalan' : 'menunggu'),
            'tanggal' => $konfirmasiSelesai ? $pesanan->updated_at?->translatedFormat('d M Y') : null,
        ];

        $invoice = $pesanan->invoices->first();
        if ($invoice) {
            $dpOk = (float) $invoice->dp_dibayar > 0;
            $lunas = $invoice->status === 'Lunas';
            $steps[] = [
                'fase' => 'pembayaran',
                'judul' => 'Pembayaran & DP',
                'deskripsi' => $lunas
                    ? 'Pembayaran lunas'
                    : ($dpOk ? 'DP diterima — sisa Rp '.number_format($invoice->sisa_pembayaran, 0, ',', '.') : 'Menunggu pembayaran DP'),
                'status' => $lunas ? 'selesai' : ($dpOk ? 'berjalan' : 'menunggu'),
                'tanggal' => $invoice->tanggal_invoice?->translatedFormat('d M Y'),
            ];
        }

        foreach ($aspek as $item) {
            $steps[] = [
                'fase' => 'aspek_'.$item['key'],
                'judul' => $item['label'],
                'deskripsi' => $item['deskripsi'],
                'status' => match ($item['status']) {
                    'Selesai' => 'selesai',
                    'Proses' => 'berjalan',
                    default => 'menunggu',
                },
                'tanggal' => null,
                'persentase' => $item['progress_percent'],
            ];
        }

        foreach ($pesanan->jadwalMeetings as $meeting) {
            $steps[] = [
                'fase' => 'meeting_'.$meeting->id,
                'judul' => $meeting->judul_meeting,
                'deskripsi' => ($meeting->lokasi ? $meeting->lokasi.' · ' : '').'Meeting vendor',
                'status' => $meeting->status === 'Selesai' ? 'selesai' : 'berjalan',
                'tanggal' => $meeting->tanggal_meeting?->translatedFormat('d M Y').' '.$meeting->waktu_meeting_formatted,
            ];
        }

        $rundownReady = $pesanan->rundowns->isNotEmpty();
        $steps[] = [
            'fase' => 'rundown',
            'judul' => 'Rundown Hari H',
            'deskripsi' => $rundownReady
                ? $pesanan->rundowns->count().' item jadwal acara siap'
                : 'Rundown detail akan diisi admin mendekati hari H',
            'status' => $rundownReady ? 'selesai' : 'menunggu',
            'tanggal' => null,
        ];

        $hariH = $pesanan->tanggal_acara
            ? Carbon::parse($pesanan->tanggal_acara)->translatedFormat('d F Y')
            : null;
        $steps[] = [
            'fase' => 'hari_h',
            'judul' => 'Hari H — '.$pesanan->nama_pasangan,
            'deskripsi' => $pesanan->lokasi.' · '.substr((string) $pesanan->jam_acara, 0, 5).' WIB',
            'status' => $pesanan->status === 'Selesai' ? 'selesai' : 'menunggu',
            'tanggal' => $hariH,
        ];

        return $steps;
    }
}
