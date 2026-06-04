<?php

namespace App\Models;

use App\Support\ImageHelper;
use App\Models\Tugas;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_vendor',
        'kategori',
        'lokasi',
        'harga_info',
        'rating_avg',
        'rating_count',
        'kontak',
        'instagram',
        'whatsapp',
        'website',
        'portfolio_images',
        'status',
        'gambar',
        'gambar_url',
    ];

    protected $appends = ['image_url'];

    protected function casts(): array
    {
        return [
            'rating_avg' => 'float',
            'rating_count' => 'integer',
            'portfolio_images' => 'array',
        ];
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(fn () => ImageHelper::url($this->gambar, $this->gambar_url));
    }

    public function scopeAktif($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function scopeKategori($query, ?string $kategori)
    {
        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        return $query;
    }

    public function pesanans()
    {
        return $this->belongsToMany(Pesanan::class, 'pesanan_vendor')
            ->withPivot(['waktu_setup', 'status', 'nama_pic', 'kontak_pic'])
            ->withTimestamps();
    }

    public function pakets()
    {
        return $this->belongsToMany(Paket::class, 'paket_vendor')
            ->withTimestamps();
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    public function updateRating(): void
    {
        app(\App\Services\VendorRatingService::class)->recalculateForVendor($this);
    }

    public function instagramUrl(): ?string
    {
        if (! filled($this->instagram)) {
            return null;
        }

        $handle = ltrim(trim($this->instagram), '@');

        if (str_starts_with($handle, 'http')) {
            return $handle;
        }

        return 'https://instagram.com/'.$handle;
    }

    public function whatsappUrl(): ?string
    {
        $raw = $this->whatsapp ?: $this->kontak;
        if (! filled($raw)) {
            return null;
        }

        $num = preg_replace('/\D+/', '', $raw);
        if ($num === '') {
            return null;
        }

        if (str_starts_with($num, '0')) {
            $num = '62'.substr($num, 1);
        } elseif (! str_starts_with($num, '62')) {
            $num = '62'.$num;
        }

        return 'https://wa.me/'.$num;
    }

    public function websiteUrl(): ?string
    {
        if (! filled($this->website)) {
            return null;
        }

        $url = trim($this->website);

        return str_starts_with($url, 'http') ? $url : 'https://'.$url;
    }

    /**
     * @return array<int, string>
     */
    public function portfolioGallery(): array
    {
        $images = collect($this->portfolio_images ?? [])
            ->filter(fn ($u) => filled($u))
            ->values();

        if ($this->image_url) {
            $images->prepend($this->image_url);
        }

        return $images
            ->map(fn ($url) => is_string($url) ? ImageHelper::resolvePath($url, null, 'vendor') : null)
            ->filter()
            ->unique()
            ->take(6)
            ->values()
            ->all();
    }
}
