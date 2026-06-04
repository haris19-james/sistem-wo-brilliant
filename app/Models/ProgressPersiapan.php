<?php

namespace App\Models;

use App\Support\ProgressPersiapanDefaults;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressPersiapan extends Model
{
    use HasFactory;

    protected $table = 'progress_persiapans';

    protected $fillable = [
        'pesanan_id',
        'persentase',
        'status_venue',
        'status_makeup',
        'status_catering',
        'status_dekorasi',
        'status_dokumentasi',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function getAspekItemsAttribute(): array
    {
        $items = [];

        foreach (ProgressPersiapanDefaults::meta() as $key => $meta) {
            $status = $this->{'status_'.$key} ?? 'Menunggu';
            $statusKey = strtolower($status);

            $items[] = [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'status' => $status,
                'badge_class' => self::statusBadgeClass($status),
                'progress_percent' => self::statusToPercent($status),
                'deskripsi' => $meta[$statusKey] ?? $meta['menunggu'],
            ];
        }

        return $items;
    }

    public static function statusToPercent(string $status): int
    {
        return match ($status) {
            'Selesai' => 100,
            'Proses' => 50,
            default => 0,
        };
    }

    /**
     * Rata-rata bobot aspek: Menunggu=0, Proses=50, Selesai=100.
     */
    public static function hitungProgressDariStatus(array $statuses): int
    {
        $keys = array_keys(ProgressPersiapanDefaults::meta());
        $values = array_map(
            fn (string $key) => self::statusToPercent($statuses[$key] ?? 'Menunggu'),
            $keys
        );

        if ($values === []) {
            return 0;
        }

        return (int) round(array_sum($values) / count($values));
    }

    public static function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'Selesai' => 'bg-green-50 text-green-700 border-green-200',
            'Proses' => 'bg-amber-50 text-amber-800 border-amber-200',
            default => 'bg-gray-100 text-gray-600 border-gray-200',
        };
    }
}
