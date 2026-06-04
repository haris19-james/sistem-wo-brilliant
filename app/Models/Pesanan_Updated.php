<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';

    protected $fillable = [
        'user_id',
        'paket_id',
        'korlap_id',  // ✅ ADD THIS FOR KORLAP RELATIONSHIP
        'nomor_pesanan',
        'nama_pasangan',
        'tanggal_acara',
        'jam_acara',
        'lokasi',
        'tema',
        'jumlah_tamu',
        'status',
        'catatan_khusus',
        'detail_paket_kustom',
        'estimasi_budget',
        'alasan_pembatalan',
        'pembatalan_diminta_at',
        'dibatalkan_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_acara' => 'date',
            'pembatalan_diminta_at' => 'datetime',
            'dibatalkan_at' => 'datetime',
            'estimasi_budget' => 'decimal:2',
        ];
    }

    // ========================
    // RELATIONSHIPS
    // ========================

    /**
     * Pesanan dimiliki oleh satu user/klien (customer).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pesanan ditangani oleh satu Korlap (Tim Lapangan).
     */
    public function korlap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'korlap_id')
            ->where('role', 'lapangan');
    }

    /**
     * Pesanan menggunakan satu paket WO.
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    /**
     * Pesanan bisa memiliki banyak invoice.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Pesanan bisa memiliki banyak item rundown jadwal.
     */
    public function rundowns()
    {
        return $this->hasMany(Rundown::class)->orderBy('waktu_mulai');
    }

    /**
     * Pesanan memiliki satu data progress persiapan.
     */
    public function progress()
    {
        return $this->hasOne(ProgressPersiapan::class);
    }

    public function jadwalMeetings()
    {
        return $this->hasMany(JadwalMeeting::class)
            ->orderBy('tanggal_meeting')
            ->orderBy('waktu_meeting');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function laporanLapangans()
    {
        return $this->hasMany(LaporanLapangan::class)->latest('tanggal');
    }

    /**
     * Pesanan memiliki banyak tugas (tasks).
     */
    public function tugas()
    {
        return $this->hasMany(Tugas::class, 'pesanan_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'pesanan_vendor')
            ->withPivot(['waktu_setup'])
            ->withTimestamps();
    }

    public function scopeAktifLapangan($query)
    {
        return $query->whereIn('status', ['Menunggu', 'Sedang Berlangsung']);
    }

    // ========================
    // ACCESSORS
    // ========================

    /**
     * Format status badge warna (untuk Tailwind classes).
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'Sedang Berlangsung' => 'bg-green-50 text-green-700',
            'Selesai'            => 'bg-blue-50 text-blue-700',
            'Menunggu'           => 'bg-yellow-50 text-yellow-700',
            'Dibatalkan'         => 'bg-red-50 text-red-600',
            default              => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Accessor: format tanggal acara ke bahasa Indonesia.
     */
    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal_acara
            ? $this->tanggal_acara->translatedFormat('d F Y')
            : '-';
    }

    public function isDibatalkan(): bool
    {
        return $this->status === 'Dibatalkan';
    }

    public function canCancelByCustomer(): bool
    {
        return in_array($this->status, ['Menunggu', 'Sedang Berlangsung'], true);
    }

    public function isPaketKustom(): bool
    {
        return $this->paket?->isPaketKustom() ?? false;
    }
}
