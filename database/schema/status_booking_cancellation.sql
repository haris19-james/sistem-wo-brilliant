-- Status booking & refund (Pembatalan Pemesanan)
-- Jalankan via: php artisan migrate

ALTER TABLE `pesanans`
  ADD COLUMN `status_booking` ENUM('pending','approved_dp','approved_lunas','cancelled') NOT NULL DEFAULT 'pending' AFTER `status_pembayaran`,
  ADD COLUMN `jumlah_refund` DECIMAL(14,2) NOT NULL DEFAULT 0 AFTER `alasan_pembatalan`;

-- Backfill contoh
-- UPDATE pesanans SET status_booking = 'cancelled' WHERE status = 'Dibatalkan';
-- UPDATE pesanans SET status_booking = 'approved_lunas' WHERE status_pembayaran = 'fully_paid' AND status_booking != 'cancelled';
-- UPDATE pesanans SET status_booking = 'approved_dp' WHERE status_pembayaran = 'dp_paid' AND status_booking NOT IN ('cancelled','approved_lunas');
