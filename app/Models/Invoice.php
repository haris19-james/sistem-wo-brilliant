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

        return round((float) $this->total_biaya * (config('pembayaran.dp_persen', 20) / 100), 2);
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

    public function pembayaranJadwals()
    {
        return $this->hasMany(\App\Models\PembayaranJadwal::class);
    }

    public function applyPaymentSchedule(): void
    {
        \App\Services\PaymentScheduleService::applyToInvoice($this);
    }

    /**
     * @return array<int, array{urutan: int, label: string, jatuh_tempo: Carbon, nominal_saran: float}>
     */
    public function getJadwalCicilanAttribute(): array
    {
        if (\Illuminate\Support\Facades\Schema::hasTable('pembayaran_jadwals')) {
            $this->loadMissing('pembayaranJadwals');
            $fromDb = $this->pembayaranJadwals
                ->where('jenis', 'Cicilan')
                ->sortBy('urutan')
                ->map(fn ($row) => [
                    'urutan' => $row->urutan,
                    'label' => 'Cicilan ke-'.($row->urutan ?? 1),
                    'jatuh_tempo' => Carbon::parse($row->tanggal_jatuh_tempo),
                    'nominal_saran' => (float) $row->nominal_saran,
                    'status' => $row->status,
                ])
                ->values()
                ->all();

            if ($fromDb !== []) {
                return $fromDb;
            }
        }

        return \App\Services\PaymentScheduleService::buildCicilanSchedule($this);
    }

    public function getJadwalPembayaranRingkasAttribute(): array
    {
        return [
            'dp' => [
                'label' => 'DP / Uang Muka ('.config('pembayaran.dp_persen', 20).'%)',
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
