<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaketTema extends Model
{
    protected $fillable = [
        'paket_id',
        'nama_tema',
        'urutan',
    ];

    protected function casts(): array
    {
        return [
            'urutan' => 'integer',
        ];
    }

    public function paket(): BelongsTo
    {
        return $this->belongsTo(Paket::class);
    }
}
