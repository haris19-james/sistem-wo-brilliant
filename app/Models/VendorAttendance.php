<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAttendance extends Model
{
    protected $table = 'vendor_attendance';

    protected $fillable = [
        'pesanan_id',
        'vendor_id',
        'korlap_id',
        'arrived_at',
        'status',
        'is_late',
        'korlap_confirmed_at',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'arrived_at' => 'datetime',
            'korlap_confirmed_at' => 'datetime',
            'is_late' => 'boolean',
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

    public function korlap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'korlap_id');
    }

    public function isConfirmedByKorlap(): bool
    {
        return $this->korlap_confirmed_at !== null && $this->status === 'Hadir';
    }
}
