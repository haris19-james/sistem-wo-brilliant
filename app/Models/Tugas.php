<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'vendor_id',
        'user_id',
        'pic_id',
        'nama_tugas',
        'kategori',
        'prioritas',
        'deadline',
        'catatan',
        'status',
        'is_auto_generated',
        'korlap_verified_at',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'korlap_verified_at' => 'datetime',
        'is_auto_generated' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class, 'tugas_id')->orderBy('urutan');
    }

    public function getProgressAttribute(): int
    {
        $total = $this->checklists()->count();
        if ($total === 0) {
            return 0;
        }

        $completed = $this->checklists()->where('is_completed', true)->count();

        return (int) round(($completed / $total) * 100);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Belum Dikerjakan',
            'in_progress' => 'Sedang Dikerjakan',
            'awaiting_verification' => 'Menunggu Verifikasi',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => $this->status,
        };
    }

    /**
     * Checklist selesai → menunggu verifikasi Korlap (bukan langsung completed).
     */
    public function autoCompleteIfReady(): void
    {
        if ($this->status === 'completed') {
            return;
        }

        $total = $this->checklists()->count();
        if ($total === 0) {
            return;
        }

        $allCompleted = $this->checklists()
            ->where('is_completed', false)
            ->doesntExist();

        if ($allCompleted) {
            $this->update([
                'status' => 'awaiting_verification',
            ]);
        }
    }

    public function scopeForKorlap($query, int $korlapId)
    {
        return $query->whereHas('pesanan', function ($q) use ($korlapId) {
            $q->where('korlap_id', $korlapId)->confirmedForLapangan();
        });
    }
}
