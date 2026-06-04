<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OperasionalLapangan extends Model
{
    protected $table = 'operasional_lapangan';

    protected $fillable = [
        'pesanan_id',
        'korlap_id',
        'allocated_by',
        'pembayaran_konfirmasi_id',
        'jumlah_dialokasikan',
        'jumlah_terpakai',
        'sumber',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_dialokasikan' => 'decimal:2',
            'jumlah_terpakai' => 'decimal:2',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function korlap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'korlap_id');
    }

    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    public function konfirmasi(): BelongsTo
    {
        return $this->belongsTo(PembayaranKonfirmasi::class, 'pembayaran_konfirmasi_id');
    }

    public function realisasi(): HasMany
    {
        return $this->hasMany(RealisasiOperasional::class);
    }

    public function sisaAnggaran(): float
    {
        return max(0, (float) $this->jumlah_dialokasikan - (float) $this->jumlah_terpakai);
    }
}
