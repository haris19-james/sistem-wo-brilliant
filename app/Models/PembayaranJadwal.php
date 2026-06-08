<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranJadwal extends Model
{
    protected $table = 'pembayaran_jadwals';

    protected $fillable = [
        'invoice_id',
        'pesanan_id',
        'jenis',
        'urutan',
        'tanggal_jatuh_tempo',
        'nominal_saran',
        'status',
        'pembayaran_konfirmasi_id',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_jatuh_tempo' => 'date',
            'nominal_saran' => 'decimal:2',
            'urutan' => 'integer',
        ];
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function pembayaranKonfirmasi(): BelongsTo
    {
        return $this->belongsTo(PembayaranKonfirmasi::class);
    }

    public function isOverdue(): bool
    {
        return $this->status === 'scheduled'
            && $this->tanggal_jatuh_tempo
            && now()->startOfDay()->gt($this->tanggal_jatuh_tempo->startOfDay());
    }

    public function daysUntilDue(): int
    {
        if (! $this->tanggal_jatuh_tempo) {
            return 0;
        }

        return (int) now()->startOfDay()->diffInDays($this->tanggal_jatuh_tempo->startOfDay(), false);
    }
}
