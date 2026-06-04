<?php

namespace App\Models;

use App\Support\RupiahInput;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'nomor_invoice',
        'total_biaya',
        'dp_dibayar',
        'sisa_pembayaran',
        'status',
        'metode_pembayaran',
        'tanggal_invoice',
        'jatuh_tempo_dp',
        'jatuh_tempo_pelunasan',
        'jatuh_tempo',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_invoice' => 'date',
            'jatuh_tempo_dp' => 'date',
            'jatuh_tempo_pelunasan' => 'date',
            'jatuh_tempo' => 'date',
            'total_biaya' => 'decimal:2',
            'dp_dibayar' => 'decimal:2',
            'sisa_pembayaran' => 'decimal:2',
        ];
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function pembayaranKonfirmasis()
    {
        return $this->hasMany(PembayaranKonfirmasi::class);
    }

    public function konfirmasiPending()
    {
        return $this->hasOne(PembayaranKonfirmasi::class)
            ->where('status', 'Menunggu Konfirmasi')
            ->latestOfMany();
    }

    public function getDpMinimumAttribute(): float
    {
        $paket = $this->pesanan?->paket;
        $paketDp = ($paket && \Illuminate\Support\Facades\Schema::hasColumn('pakets', 'dp_minimal'))
            ? (int) ($paket->dp_minimal ?? 0)
            : 0;

        if ($paketDp >= RupiahInput::DP_MINIMAL_MIN) {
            return (float) min((float) $this->total_biaya, $paketDp);
        }

        return round((float) $this->total_biaya * (config('pembayaran.dp_persen', 30) / 100), 2);
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Lunas' => 'bg-green-50 text-green-700',
            'DP Lunas' => 'bg-yellow-50 text-yellow-700',
            default => 'bg-red-50 text-red-600',
        };
    }

    public function recalculateStatus(): void
    {
        $sisa = max(0, (float) $this->total_biaya - (float) $this->dp_dibayar);

        $this->sisa_pembayaran = $sisa;

        if ($sisa <= 0) {
            $this->status = 'Lunas';
        } elseif ((float) $this->dp_dibayar > 0) {
            $this->status = 'DP Lunas';
        } else {
            $this->status = 'Belum Bayar';
        }
    }

    public function applyPaymentSchedule(): void
    {
        $this->loadMissing('pesanan');

        $invoiceDate = $this->tanggal_invoice
            ? Carbon::parse($this->tanggal_invoice)
            : now();

        $tanggalAcara = $this->pesanan?->tanggal_acara
            ? Carbon::parse($this->pesanan->tanggal_acara)
            : $invoiceDate->copy()->addMonths(3);

        $this->jatuh_tempo_dp = $invoiceDate->copy()
            ->addDays((int) config('pembayaran.dp_hari_setelah_invoice', 7));

        $pelunasan = $tanggalAcara->copy()
            ->subDays((int) config('pembayaran.pelunasan_hari_sebelum_acara', 30));

        if ($pelunasan->lte($this->jatuh_tempo_dp)) {
            $pelunasan = $this->jatuh_tempo_dp->copy()->addDays(60);
        }

        $this->jatuh_tempo_pelunasan = $pelunasan;
        $this->jatuh_tempo = $pelunasan;

        if ($this->pesanan) {
            \App\Services\PaymentDeadlineService::syncFor($this->pesanan);
        }
    }

    /**
     * @return array<int, array{urutan: int, label: string, jatuh_tempo: Carbon, nominal_saran: float}>
     */
    public function getJadwalCicilanAttribute(): array
    {
        if (! $this->jatuh_tempo_dp || ! $this->jatuh_tempo_pelunasan) {
            return [];
        }

        $count = max(1, (int) config('pembayaran.jumlah_cicilan', 3));
        $dpDate = Carbon::parse($this->jatuh_tempo_dp);
        $pelunasanDate = Carbon::parse($this->jatuh_tempo_pelunasan);

        $sisaSetelahDp = max(0, (float) $this->total_biaya - (float) $this->dp_minimum);
        $perCicilan = $count > 0 ? round($sisaSetelahDp / $count, 2) : 0;

        $totalDays = max(1, $dpDate->diffInDays($pelunasanDate));
        $interval = (int) max(1, floor($totalDays / ($count + 1)));

        $jadwal = [];
        for ($i = 1; $i <= $count; $i++) {
            $due = $dpDate->copy()->addDays($interval * $i);
            if ($due->gt($pelunasanDate)) {
                $due = $pelunasanDate->copy();
            }

            $jadwal[] = [
                'urutan' => $i,
                'label' => 'Cicilan ke-'.$i,
                'jatuh_tempo' => $due,
                'nominal_saran' => $perCicilan,
            ];
        }

        return $jadwal;
    }

    public function getJadwalPembayaranRingkasAttribute(): array
    {
        return [
            'dp' => [
                'label' => 'DP / Uang Muka ('.config('pembayaran.dp_persen', 30).'%)',
                'jatuh_tempo' => $this->jatuh_tempo_dp,
                'nominal' => $this->dp_minimum,
            ],
            'cicilan' => $this->jadwal_cicilan,
            'pelunasan' => [
                'label' => 'Pelunasan (lunas)',
                'jatuh_tempo' => $this->jatuh_tempo_pelunasan,
                'nominal' => (float) $this->sisa_pembayaran,
            ],
        ];
    }
}
