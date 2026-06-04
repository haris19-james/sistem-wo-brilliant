<?php

return [
    'rekening' => [
        [
            'bank' => 'BCA',
            'nomor' => '1234567890',
            'atas_nama' => 'PT Brilliant Event WO',
        ],
        [
            'bank' => 'Mandiri',
            'nomor' => '0987654321',
            'atas_nama' => 'PT Brilliant Event WO',
        ],
    ],
    'dp_persen' => 30,
    /** Hari setelah invoice dibuat — batas bayar DP / uang muka */
    'dp_hari_setelah_invoice' => 7,
    /** Hari sebelum tanggal acara — batas pelunasan penuh (H-14) */
    'pelunasan_hari_sebelum_acara' => (int) env('PEMBAYARAN_PELUNASAN_HARI', 14),
    /** Pengingat banner customer saat sisa hari <= nilai ini (status DP) */
    'deadline_warning_hari' => (int) env('PEMBAYARAN_DEADLINE_WARNING_HARI', 7),
    /** Jumlah cicilan antara DP dan pelunasan */
    'jumlah_cicilan' => 3,
    /** Maks. ukuran bukti transfer (KB) — sesuaikan dengan upload_max di PHP */
    'bukti_max_kb' => 10240,

    /** Persentase alokasi uang operasional lapangan dari nominal pembayaran masuk */
    'operasional_persen_dp' => 10,
    'operasional_persen_pelunasan' => 15,
    'operasional_persen_cicilan' => 5,
];
