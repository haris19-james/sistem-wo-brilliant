<?php

namespace App\Support;

class ProgressPersiapanDefaults
{
    public static function meta(): array
    {
        return [
            'venue' => [
                'label' => 'Venue & Lokasi',
                'icon' => 'venue',
                'menunggu' => 'Menunggu konfirmasi venue dan cek lokasi',
                'proses' => 'Proses booking & survey lokasi acara',
                'selesai' => 'Venue terkonfirmasi dan siap digunakan',
            ],
            'makeup' => [
                'label' => 'Makeup & Busana',
                'icon' => 'makeup',
                'menunggu' => 'Menunggu jadwal fitting dan trial makeup',
                'proses' => 'Fitting / trial makeup sedang berjalan',
                'selesai' => 'Makeup & busana final siap hari H',
            ],
            'catering' => [
                'label' => 'Catering & Hidangan',
                'icon' => 'catering',
                'menunggu' => 'Menunggu finalisasi menu dan jumlah porsi',
                'proses' => 'Menu disusun & koordinasi catering',
                'selesai' => 'Catering terkonfirmasi sesuai jumlah tamu',
            ],
            'dekorasi' => [
                'label' => 'Dekorasi & Florist',
                'icon' => 'dekorasi',
                'menunggu' => 'Menunggu konsep dan approval dekorasi',
                'proses' => 'Persiapan dekorasi & instalasi',
                'selesai' => 'Dekorasi selesai dipasang',
            ],
            'dokumentasi' => [
                'label' => 'Dokumentasi',
                'icon' => 'dokumentasi',
                'menunggu' => 'Menunggu konfirmasi paket foto/video',
                'proses' => 'Briefing tim dokumentasi',
                'selesai' => 'Tim dokumentasi siap hari H',
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function emptyAspek(): array
    {
        $items = [];
        foreach (self::meta() as $key => $meta) {
            $items[] = [
                'key' => $key,
                'label' => $meta['label'],
                'icon' => $meta['icon'],
                'status' => 'Menunggu',
                'badge_class' => 'bg-gray-100 text-gray-600 border-gray-200',
                'progress_percent' => 0,
                'deskripsi' => $meta['menunggu'],
            ];
        }

        return $items;
    }
}
