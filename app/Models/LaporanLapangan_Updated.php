<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanLapangan extends Model
{
    use HasFactory;

    protected $table = 'laporan_lapangans';

    protected $fillable = [
        'pesanan_id',
        'user_id',
        'ringkasan',
        'kondisi',
        'foto_path',
        'dokumentasi_path',  // ✅ ADD THIS FOR DOCUMENTATION PHOTOS
        'tanggal',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tanggal' => 'datetime',
    ];

    /**
     * Relationship: Laporan belongs to a Pesanan
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * Relationship: Laporan belongs to a User (Korlap)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get status badge class for Tailwind styling
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->kondisi) {
            'Kritis' => 'bg-red-100 text-red-800',
            'Perhatian' => 'bg-yellow-100 text-yellow-800',
            'Baik' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get icon for kondisi
     */
    public function getIconAttribute(): string
    {
        return match ($this->kondisi) {
            'Kritis' => 'alert-circle',
            'Perhatian' => 'alert-triangle',
            'Baik' => 'check-circle',
            default => 'info',
        };
    }

    /**
     * Scope: Get kendala (issues) - has foto
     */
    public function scopeKendala($query)
    {
        return $query->where('foto_path', '!=', null);
    }

    /**
     * Scope: Get dokumentasi - has dokumentasi_path
     */
    public function scopeDokumentasi($query)
    {
        return $query->where('dokumentasi_path', '!=', null);
    }
}
