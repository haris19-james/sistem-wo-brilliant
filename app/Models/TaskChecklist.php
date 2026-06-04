<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskChecklist extends Model
{
    use HasFactory;

    protected $table = 'task_checklists';

    protected $fillable = [
        'tugas_id',
        'deskripsi',
        'is_completed',
        'urutan',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Checklist belongs to a Task
     */
    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class);
    }
}
