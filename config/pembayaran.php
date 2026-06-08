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
    /** Persentase DP minimal dari total tagihan (contoh: 20% dari 75jt = 15jt) */
    'dp_persen' => (int) env('PEMBAYARAN_DP_PERSEN', 20),
    /** Hari setelah booking disetujui — batas bayar DP */
    'dp_hari_setelah_disetujui' => (int) env('PEMBAYARAN_DP_HARI', 3),
    /** Fallback jika booking_disetujui_at belum tercatat */
    'dp_hari_setelah_invoice' => (int) env('PEMBAYARAN_DP_HARI_INVOICE', 3),
    /** Hari sebelum tanggal acara — batas pelunasan penuh */
    'pelunasan_hari_sebelum_acara' => (int) env('PEMBAYARAN_PELUNASAN_HARI', 30),
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
