Galeri Portofolio Hero — Brilliant WO
=====================================

Cara menambah / update foto carousel beranda:

1. Simpan foto JPG/PNG/WebP ke folder ini (nama bebas).
2. Jalankan: php scripts/copy-hero-images.php
   → File akan disalin ke portfolio-1.jpg, portfolio-2.jpg, dst.
3. Refresh halaman beranda.

Opsional (jika PHP GD aktif):
   php scripts/optimize-hero-images.php
   → Konversi ke WebP untuk loading lebih ringan.

Config slide: config/brilliant.php → hero_gallery
