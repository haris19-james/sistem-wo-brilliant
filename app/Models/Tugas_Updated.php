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
        'user_id',
        'pic_id',
        'nama_tugas',
        'kategori',
        'prioritas',
        'deadline',
        'catatan',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Task belongs to an Order (Pesanan)
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * Relationship: Task belongs to a User (creator)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: Task belongs to a PIC (Person In Charge)
     */
    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    /**
     * Relationship: Task has many Checklists
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class, 'tugas_id')->orderBy('urutan');
    }

    /**
     * Get progress percentage based on completed checklists
     * 
     * Formula: (Jumlah checklist dicentang / Total checklist) * 100
     */
    public function getProgressAttribute(): int
    {
        $totalChecklists = $this->checklists()->count();

        if ($totalChecklists === 0) {
            return 0;
        }

        $completedChecklists = $this->checklists()
            ->where('is_completed', true)
            ->count();

        return (int) (($completedChecklists / $totalChecklists) * 100);
    }

    /**
     * Auto-complete task if all checklists are done
     * 
     * This is called when a checklist is updated.
     * If progress reaches 100%, automatically change status to 'completed'
     */
    public function autoCompleteIfReady(): void
    {
        // Check if ALL checklists are completed
        $allCompleted = $this->checklists()
            ->where('is_completed', false)
            ->doesntExist();

        // Only auto-complete if:
        // 1. All checklists are completed
        // 2. There are checklists
        // 3. Status is not already 'completed'
        if ($allCompleted && $this->checklists()->count() > 0 && $this->status !== 'completed') {
            $this->update(['status' => 'completed']);
        }
    }

    /**
     * Get priority badge class for Tailwind styling
     */
    public function getPriorityBadgeAttribute(): string
    {
        return match ($this->prioritas) {
            'high' => 'bg-red-100 text-red-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'low' => 'bg-blue-100 text-blue-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status badge class for Tailwind styling
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'completed' => 'bg-green-100 text-green-800',
            'in_progress' => 'bg-blue-100 text-blue-800',
            'pending' => 'bg-gray-100 text-gray-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
