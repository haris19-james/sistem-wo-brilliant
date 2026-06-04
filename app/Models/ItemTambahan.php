<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItemTambahan extends Model
{
    protected $table = 'item_tambahan';

    protected $fillable = [
        'pesanan_id',
        'invoice_id',
        'kategori',
        'deskripsi',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'status',
        'catatan_admin',
        'approved_at',
        'injected_progress_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'harga_satuan' => 'decimal:2',
            'total_harga' => 'decimal:2',
            'approved_at' => 'datetime',
            'injected_progress_at' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui — Menunggu Bayar',
            'rejected' => 'Ditolak',
            'paid' => 'Lunas & Aktif Lapangan',
            default => ucfirst($this->status),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-50 text-amber-800 border-amber-200',
            'approved' => 'bg-blue-50 text-blue-800 border-blue-200',
            'rejected' => 'bg-red-50 text-red-700 border-red-200',
            'paid' => 'bg-green-50 text-green-700 border-green-200',
            default => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }

    public function progressKey(): ?string
    {
        return config('item_tambahan.progress_key_map.'.$this->kategori);
    }

    public function isEligibleForKorlapChecklist(): bool
    {
        return $this->status === 'paid' && $this->progressKey() !== null;
    }
}
