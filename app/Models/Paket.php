<?php

namespace App\Models;

use App\Support\ImageHelper;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paket extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'default_lokasi',
        'kapasitas_tamu',
        'harga_tambahan_per_tamu',
        'harga',
        'dp_minimal',
        'is_kustom',
        'layanan_termasuk',
        'gambar',
        'gambar_url',
    ];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'layanan_termasuk' => 'array',
            'is_kustom' => 'boolean',
            'harga' => 'integer',
            'dp_minimal' => 'integer',
            'kapasitas_tamu' => 'integer',
            'harga_tambahan_per_tamu' => 'integer',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => ImageHelper::url($this->gambar, $this->gambar_url));
    }

    public function scopeStandar($query)
    {
        return $query->where('is_kustom', false);
    }

    public function scopeKustom($query)
    {
        return $query->where('is_kustom', true);
    }

    public function isPaketKustom(): bool
    {
        return (bool) $this->is_kustom;
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'paket_vendor')
            ->withTimestamps()
            ->orderBy('kategori')
            ->orderBy('nama_vendor');
    }

    public function temas()
    {
        return $this->hasMany(PaketTema::class)->orderBy('urutan');
    }
}
