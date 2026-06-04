<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rundown extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'kategori_acara',
        'waktu_mulai',
        'waktu_selesai',
        'kegiatan',
    ];

    public function getWaktuMulaiFormattedAttribute(): string
    {
        return $this->formatWaktu($this->waktu_mulai);
    }

    public function getWaktuSelesaiFormattedAttribute(): ?string
    {
        return $this->waktu_selesai ? $this->formatWaktu($this->waktu_selesai) : null;
    }

    private function formatWaktu(mixed $value): string
    {
        if ($value instanceof \Carbon\CarbonInterface) {
            return $value->format('H:i');
        }

        return substr((string) $value, 0, 5);
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
