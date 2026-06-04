-- ============================================================================
-- CONDITIONAL PAYMENT WORKFLOW - SQL MIGRATIONS (MANUAL)
-- ============================================================================
-- Jika `php artisan migrate` mengalami error koneksi database,
-- jalankan SQL ini langsung di phpMyAdmin atau MySQL CLI
-- ============================================================================

USE `sistem_wo_brilliant`;

-- ============================================================================
-- Tambahkan kolom-kolom pembayaran ke tabel pesanans
-- ============================================================================

-- 1. Status Pembayaran (unpaid, dp_paid, fully_paid)
ALTER TABLE `pesanans` 
ADD COLUMN `status_pembayaran` ENUM('unpaid', 'dp_paid', 'fully_paid') 
DEFAULT 'unpaid' 
AFTER `status` 
COMMENT 'Status verifikasi pembayaran: unpaid (belum bayar), dp_paid (DP terverifikasi), fully_paid (lunas penuh)';

-- 2. Status Pemesanan (pending, confirmed, on_progress, success, cancelled)
ALTER TABLE `pesanans` 
ADD COLUMN `status_pemesanan` ENUM('pending', 'confirmed', 'on_progress', 'success', 'cancelled') 
DEFAULT 'pending' 
AFTER `status_pembayaran` 
COMMENT 'Status pemesanan dalam alur: pending, confirmed, on_progress, success, cancelled';

-- 3. Waktu verifikasi DP oleh admin
ALTER TABLE `pesanans` 
ADD COLUMN `verified_by_admin_at` TIMESTAMP NULL 
AFTER `status_pemesanan` 
COMMENT 'Kapan DP diverifikasi oleh admin';

-- 4. Admin ID yang melakukan verifikasi
ALTER TABLE `pesanans` 
ADD COLUMN `verified_admin_id` BIGINT UNSIGNED NULL 
AFTER `verified_by_admin_at` 
COMMENT 'Admin yang melakukan verifikasi DP';

-- 5. Waktu verifikasi pelunasan oleh admin
ALTER TABLE `pesanans` 
ADD COLUMN `fully_paid_by_admin_at` TIMESTAMP NULL 
AFTER `verified_admin_id` 
COMMENT 'Kapan pelunasan diverifikasi oleh admin';

-- 6. Tambahkan Foreign Key untuk verified_admin_id
ALTER TABLE `pesanans` 
ADD CONSTRAINT `pesanans_verified_admin_id_foreign` 
FOREIGN KEY (`verified_admin_id`) 
REFERENCES `users` (`id`) 
ON DELETE SET NULL;

-- ============================================================================
-- VERIFIKASI - Pastikan kolom sudah terbuat dengan benar
-- ============================================================================
-- Jalankan query ini untuk melihat structure tabel:
-- DESCRIBE `pesanans`;
-- Atau:
-- SHOW CREATE TABLE `pesanans`;

-- ============================================================================
-- ROLLBACK (Jika perlu undo/delete kolom-kolom)
-- ============================================================================
-- Uncomment dan jalankan jika ingin menghapus kolom-kolom pembayaran:

/*
ALTER TABLE `pesanans` DROP CONSTRAINT `pesanans_verified_admin_id_foreign`;
ALTER TABLE `pesanans` DROP COLUMN `status_pembayaran`;
ALTER TABLE `pesanans` DROP COLUMN `status_pemesanan`;
ALTER TABLE `pesanans` DROP COLUMN `verified_by_admin_at`;
ALTER TABLE `pesanans` DROP COLUMN `verified_admin_id`;
ALTER TABLE `pesanans` DROP COLUMN `fully_paid_by_admin_at`;
*/

-- ============================================================================
-- SAMPLE DATA TESTING - Opsional
-- ============================================================================
-- Uncomment untuk menambahkan sample pesanan dengan berbagai status pembayaran

/*
-- Pesanan 1: Belum bayar (tidak akan tampil di Korlap dashboard)
UPDATE `pesanans` 
SET `status_pembayaran` = 'unpaid', 
    `status_pemesanan` = 'pending' 
WHERE `id` = 1;

-- Pesanan 2: DP sudah verifikasi (tampil di Korlap dashboard, checklist terbatas)
UPDATE `pesanans` 
SET `status_pembayaran` = 'dp_paid', 
    `status_pemesanan` = 'on_progress',
    `verified_admin_id` = 1,
    `verified_by_admin_at` = NOW() 
WHERE `id` = 2;

-- Pesanan 3: Lunas (tampil di Korlap, checklist penuh)
UPDATE `pesanans` 
SET `status_pembayaran` = 'fully_paid', 
    `status_pemesanan` = 'on_progress',
    `verified_admin_id` = 1,
    `verified_by_admin_at` = NOW(),
    `fully_paid_by_admin_at` = NOW() 
WHERE `id` = 3;
*/

-- ============================================================================
-- END OF MIGRATION
-- ============================================================================
