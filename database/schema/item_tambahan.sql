-- Skema referensi: item_tambahan (Pengajuan Item Tambahan / Add-on)
-- Jalankan via: php artisan migrate

CREATE TABLE IF NOT EXISTS `item_tambahan` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pesanan_id` bigint unsigned NOT NULL,
  `invoice_id` bigint unsigned DEFAULT NULL,
  `kategori` varchar(64) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `jumlah` int unsigned NOT NULL DEFAULT 1,
  `harga_satuan` decimal(14,2) DEFAULT NULL,
  `total_harga` decimal(14,2) DEFAULT NULL,
  `status` enum('pending','approved','rejected','paid') NOT NULL DEFAULT 'pending',
  `catatan_admin` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `injected_progress_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `item_tambahan_pesanan_id_status_index` (`pesanan_id`,`status`),
  CONSTRAINT `item_tambahan_pesanan_id_foreign` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `item_tambahan_invoice_id_foreign` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
