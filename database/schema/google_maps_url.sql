-- Tambah kolom link share Google Maps pada tabel booking (pesanans)
-- Jalankan via: php artisan migrate

ALTER TABLE `pesanans`
  ADD COLUMN `google_maps_url` TEXT NULL AFTER `lokasi`;
