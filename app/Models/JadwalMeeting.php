<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JadwalMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'judul_meeting',
        'tanggal_meeting',
        'waktu_meeting',
        'lokasi',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_meeting' => 'date',
        ];
    }

    public function getWaktuMeetingFormattedAttribute(): string
    {
        if ($this->waktu_meeting instanceof \Carbon\CarbonInterface) {
            return $this->waktu_meeting->format('H:i');
        }

        return substr((string) $this->waktu_meeting, 0, 5);
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }
}
