# 🚨 FIX - TABLE TIDAK DITEMUKAN

## Error yang Terjadi:
```
SQLSTATE[42S02]: Base table or view not found: 1146 
Table 'sistem_wo_brilliant.tugas' doesn't exist
```

## ✅ SOLUSI (Pilih Salah Satu)

### CARA 1: Via Command Line (RECOMMENDED)
Buka Command Prompt / Terminal di folder project:

```bash
cd c:\laragon\www\sistem-wo-brilliant2
php artisan migrate
```

Kalau command prompt tidak bisa, coba:

```batch
php artisan migrate:fresh
```

**Tunggu sampai selesai**, kemudian jalankan:

```bash
php artisan config:cache
php artisan view:clear
```

---

### CARA 2: Via Web Browser

1. Buka file `run_migration.php` di folder project
2. Akses di browser: `http://localhost/run_migration.php`
3. Tunggu hasilnya
4. Jika sukses, delete file `run_migration.php`

---

### CARA 3: Via phpMyAdmin (Manual)

1. Buka phpMyAdmin
2. Pilih database: `sistem_wo_brilliant`
3. Jalankan SQL query ini:

```sql
CREATE TABLE `tugas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `pesanan_id` bigint unsigned NOT NULL,
  `pic_id` bigint unsigned NOT NULL,
  `nama_tugas` varchar(255) NOT NULL,
  `kategori` varchar(255) NOT NULL,
  `prioritas` enum('high','medium','low') DEFAULT 'medium',
  `deadline` datetime NOT NULL,
  `checklists` json,
  `catatan` text,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `tugas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tugas_ibfk_2` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tugas_ibfk_3` FOREIGN KEY (`pic_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## ✅ VERIFIKASI SETELAH FIX

Cek di phpMyAdmin:
1. Database: `sistem_wo_brilliant`
2. Tabel: `tugas` harus ada
3. Columns: id, user_id, pesanan_id, pic_id, nama_tugas, kategori, prioritas, deadline, checklists, catatan, status, created_at, updated_at

---

## 🔄 SETELAH TABEL DIBUAT

Jalankan:

```bash
php artisan config:cache
php artisan view:clear
php artisan route:clear
```

Kemudian test lagi:
```
http://localhost/lapangan/tugas
```

---

## 🆘 JIKA MASIH ERROR

### Error: "Connection refused"
→ Pastikan MySQL berjalan (Laragon dashboard → Start All)
→ Check `.env` file, DATABASE settings benar

### Error: "SQLSTATE[HY000]"
→ MySQL service tidak jalan
→ Buka Laragon, klik "Start All"

### Error: "Table already exists"
→ Tabel sudah ada, delete `run_migration.php` dan refresh browser

---

## 📝 NOTES

- Pastikan MySQL RUNNING sebelum migrate
- Pastikan `.env` sudah configured dengan benar
- Jangan delete migration file!
- Kalau migrate error, cek `storage/logs/laravel.log`

---

**RECOMMENDED APPROACH:** CARA 1 via Command Line

Jika sudah selesai, delete file ini dan file `run_migration.php` ✅
