<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KuaChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'title',
        'status',
        'customer_check_in_at',
        'updated_by_user_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'customer_check_in_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Pesanan::class, 'booking_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by_user_id');
    }

    public function isComplete(): bool
    {
        return $this->status === 'complete';
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'complete' => [
                'label' => 'AMAN',
                'class' => 'bg-green-600 text-white',
            ],
            'in_progress' => [
                'label' => 'PROSES',
                'class' => 'bg-amber-100 text-amber-800 border border-amber-200',
            ],
            default => [
                'label' => 'PENDING',
                'class' => 'bg-gray-100 text-gray-600 border border-gray-200',
            ],
        };
    }

    public function getUpdateNoteAttribute(): string
    {
        if ($this->customer_check_in_at) {
            return 'After Customer check-in at '.$this->customer_check_in_at->format('g:i A');
        }

        if ($this->updatedByUser && $this->updated_at) {
            return 'Diperbarui oleh '.$this->updatedByUser->name.' · '.$this->updated_at->translatedFormat('d M Y, H:i');
        }

        if ($this->updated_at) {
            return 'Terakhir diperbarui · '.$this->updated_at->translatedFormat('d M Y, H:i');
        }

        return 'Belum ada pembaruan dari customer atau admin.';
    }
}
