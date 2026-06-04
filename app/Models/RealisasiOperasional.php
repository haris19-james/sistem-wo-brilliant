<?php

namespace App\Models;

use App\Support\ImageHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RealisasiOperasional extends Model
{
    protected $table = 'realisasi_operasional';

    protected $fillable = [
        'operasional_lapangan_id',
        'pesanan_id',
        'korlap_id',
        'judul',
        'jumlah',
        'tanggal_pengeluaran',
        'keterangan',
        'bukti_nota',
        'status',
        'catatan_admin',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_pengeluaran' => 'date',
        ];
    }

    public function operasional(): BelongsTo
    {
        return $this->belongsTo(OperasionalLapangan::class, 'operasional_lapangan_id');
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function korlap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'korlap_id');
    }

    public function buktiNotaUrl(): ?string
    {
        return $this->bukti_nota ? ImageHelper::url($this->bukti_nota) : null;
    }
}
