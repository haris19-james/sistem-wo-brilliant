<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAnggaran extends Model
{
    protected $fillable = [
        'pesanan_id',
        'vendor_id',
        'total_biaya',
        'rincian_biaya',
        'status_pembayaran',
        'allocated_by',
        'dibayar_at',
        'lunas_at',
    ];

    protected function casts(): array
    {
        return [
            'total_biaya' => 'decimal:2',
            'dibayar_at' => 'datetime',
            'lunas_at' => 'datetime',
        ];
    }

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function allocatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }

    public function getStatusPembayaranLabelAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'lunas' => 'Lunas',
            'dibayar' => 'Dibayar',
            default => 'Menunggu',
        };
    }

    public function getStatusPembayaranBadgeClassAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'lunas' => 'bg-green-100 text-green-800 border-green-200',
            'dibayar' => 'bg-blue-50 text-blue-700 border-blue-200',
            default => 'bg-orange-50 text-orange-800 border-orange-200',
        };
    }

    public function isTerbayarPenuh(): bool
    {
        return $this->status_pembayaran === 'lunas';
    }
}
