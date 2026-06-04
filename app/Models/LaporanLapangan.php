<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanLapangan extends Model
{
    use HasFactory;

    public const KATEGORI = ['Teknis', 'Vendor', 'Katering', 'Dekorasi', 'Logistik', 'Lainnya'];

    protected $fillable = [
        'pesanan_id',
        'user_id',
        'tanggal',
        'kondisi',
        'kategori',
        'status_tindak',
        'ringkasan',
        'tindak_lanjut',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getKondisiBadgeClassAttribute(): string
    {
        return match ($this->kondisi) {
            'Kritis' => 'bg-red-50 text-red-700 border-red-200',
            'Perhatian' => 'bg-amber-50 text-amber-800 border-amber-200',
            default => 'bg-green-50 text-green-700 border-green-200',
        };
    }

    public function getStatusTindakBadgeClassAttribute(): string
    {
        return match ($this->status_tindak) {
            'Selesai' => 'bg-green-50 text-green-700 border-green-200',
            'Dalam Penanganan' => 'bg-blue-50 text-blue-700 border-blue-200',
            default => 'bg-amber-50 text-amber-800 border-amber-200',
        };
    }

    public function isKendalaAktif(): bool
    {
        return in_array($this->status_tindak, ['Menunggu Tindakan', 'Dalam Penanganan'], true);
    }

    /** Setara status "resolved" di issue tracking. */
    public function isResolved(): bool
    {
        return $this->status_tindak === 'Selesai';
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status_tindak', ['Menunggu Tindakan', 'Dalam Penanganan']);
    }

    public function scopeResolved($query)
    {
        return $query->where('status_tindak', 'Selesai');
    }
}
