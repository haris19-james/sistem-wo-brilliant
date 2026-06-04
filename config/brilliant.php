<?php

return [

  'name' => 'Brilliant',
  'tagline' => 'Event & Wedding Organizer',
  'motto' => 'Bring liability and tactful',

  /*
  | Palet warna identitas (sesuai logo Brilliant)
  | - bottle: hijau utama (petal gelap)
  | - bottle_bright: hijau cerah (petal terang)
  | - lime: hijau muda (teks "EVENT & WEDDING ORGANIZER")
  | - ink: hitam teks "Brilliant"
  */
  'colors' => [
    'bottle' => '#00A32A',
    'bottle_hover' => '#008F24',
    'bottle_bright' => '#22D43B',
    'lime' => '#6EF07F',
    'leaf_soft' => '#EDFCF0',
    'leaf' => '#C5F5CC',
    'leaf_bg' => '#D4F9DC',
    'ink' => '#111111',
    'gray_box' => '#F8FAFC',
  ],

  /*
  | Logo: letakkan file Anda di public/images/branding/
  | - logo.png (disarankan, transparan)
  | - atau logo.jpg / logo.webp
  | Bisa juga set BRILLIANT_LOGO=/path/dari/public di .env
  */
  // File asli logo (jangan di-edit): public/images/branding/logo.png
  'logo' => env('BRILLIANT_LOGO', 'images/branding/logo.png'),

  /*
  | Galeri portofolio hero beranda (WebP di public/images/hero/).
  | Jalankan: php scripts/optimize-hero-images.php setelah menambah foto baru.
  */
  'hero_gallery' => [
    [
      'image' => 'images/hero/portfolio-1.jpg',
      'alt' => 'Konsep dekorasi pernikahan Brilliant WO',
      'caption' => 'Konsep Dekorasi Brilliant WO',
    ],
    [
      'image' => 'images/hero/portfolio-2.jpg',
      'alt' => 'Kolaborasi vendor & dekorasi pernikahan',
      'caption' => 'Kolaborasi Vendor Terbaik',
    ],
    [
      'image' => 'images/hero/portfolio-3.jpg',
      'alt' => 'Dekorasi akad dan resepsi Brilliant WO',
      'caption' => 'Akad & Resepsi Memorable',
    ],
    [
      'image' => 'images/hero/portfolio-4.jpg',
      'alt' => 'Momen spesial pernikahan Brilliant WO',
      'caption' => 'Momen Spesial Pasangan',
    ],
    [
      'image' => 'images/hero/portfolio-5.jpg',
      'alt' => 'Stage dan floral design Brilliant WO',
      'caption' => 'Stage & Floral Design',
    ],
    [
      'image' => 'images/hero/portfolio-6.jpg',
      'alt' => 'Hasil dekorasi pernikahan Brilliant WO',
      'caption' => 'Hasil Dekorasi Profesional',
    ],
  ],

  'hero_image' => env('BRILLIANT_HERO_IMAGE', 'images/hero/portfolio-1.jpg'),

  'hero_carousel_interval_ms' => (int) env('BRILLIANT_HERO_CAROUSEL_MS', 4000),

  'contact' => [
    'phone' => env('BRILLIANT_PHONE', '+62 812-3456-7890'),
    'phone_digits' => env('BRILLIANT_PHONE_DIGITS', '6281234567890'),
    'email' => env('BRILLIANT_EMAIL', 'halo@brilliant-wo.com'),
    'address' => env('BRILLIANT_ADDRESS', 'Jl. Kebahagiaan No. 123, Garut, Jawa Barat'),
    'hours' => 'Senin – Minggu, 08:00 – 20:00 WIB',
    'maps_url' => env('BRILLIANT_MAPS_URL', 'https://maps.google.com/?q=Garut+Jawa+Barat'),
    'maps_embed' => env('BRILLIANT_MAPS_EMBED', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3952.0!2d107.9!3d-7.2!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zR2FydXQ!5e0!3m2!1sid!2sid!4v1'),
  ],

  'social' => [
    'instagram' => env('BRILLIANT_INSTAGRAM', 'https://instagram.com'),
    'facebook' => env('BRILLIANT_FACEBOOK', 'https://facebook.com'),
    'tiktok' => env('BRILLIANT_TIKTOK', 'https://tiktok.com'),
  ],

  /*
  | Kategori vendor (filter halaman /vendor)
  */
  'vendor_categories' => [
    'Makeup',
    'Catering',
    'Dokumentasi',
    'Foto & Video',
    'Dekorasi',
    'MC',
    'Entertainment',
    'Venue',
    'Busana',
  ],

  'paket_kustom_min_budget' => (int) env('BRILLIANT_MIN_BUDGET', 10_000_000),

  'stats' => [
    'events' => '500+',
    'vendors' => '200+',
    'years' => '5+',
    'rating' => '4.9/5',
  ],

  'blog_categories' => [
    'semua' => 'Semua',
    'tips-wedding' => 'Tips Wedding',
    'inspirasi-dekorasi' => 'Inspirasi Dekorasi',
    'persiapan' => 'Persiapan Pernikahan',
    'vendor-spotlight' => 'Vendor Spotlight',
    'cerita-nyata' => 'Cerita Nyata',
  ],

  'blog_posts' => [
    [
      'slug' => 'checklist-persiapan-wedding-6-bulan',
      'category' => 'persiapan',
      'title' => 'Checklist Persiapan Wedding dari 6 Bulan Sebelum Hari H',
      'excerpt' => 'Persiapan pernikahan memang penuh detail. Simak checklist lengkap agar hari bahagiamu berjalan lancar.',
      'image' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
      'date' => '2024-06-10',
      'read_minutes' => 5,
      'author' => 'Tim Brilliant',
      'body' => [
        'Enam bulan sebelum hari H adalah waktu ideal untuk mengunci vendor utama: venue, catering, dokumentasi, dan dekorasi.',
        'Buat timeline mingguan dan bagi tugas dengan pasangan atau keluarga agar tidak ada yang terlewat.',
        'Gunakan panel customer Brilliant WO untuk memantau progress dan berkoordinasi dengan admin.',
      ],
    ],
    [
      'slug' => 'inspirasi-dekorasi-outdoor',
      'category' => 'inspirasi-dekorasi',
      'title' => '7 Inspirasi Dekorasi Pernikahan Outdoor yang Memesona',
      'excerpt' => 'Dekorasi outdoor memberikan kesan natural & romantis. Temukan inspirasinya di sini!',
      'image' => 'https://images.unsplash.com/photo-1519225421980-715cb0215aed?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
      'date' => '2024-06-05',
      'read_minutes' => 4,
      'author' => 'Tim Brilliant',
      'body' => [
        'Pilih tema warna yang selaras dengan landscape — hijau botol, krem, dan emas lembut cocok untuk taman atau pegunungan.',
        'Perhatikan backup indoor jika cuaca tidak mendukung.',
        'Kombinasikan lighting hangat dengan bunga lokal agar budget tetap efisien.',
      ],
    ],
    [
      'slug' => 'tips-memilih-vendor-wedding',
      'category' => 'vendor-spotlight',
      'title' => 'Tips Memilih Vendor Wedding yang Tepat & Profesional',
      'excerpt' => 'Vendor yang tepat akan membuat pernikahanmu lebih terorganisir dan berkesan.',
      'image' => 'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
      'date' => '2024-05-28',
      'read_minutes' => 6,
      'author' => 'Tim Brilliant',
      'body' => [
        'Cek portofolio dan ulasan klien sebelum menandatangani kontrak.',
        'Pastikan vendor memahami timeline acara dan ada PIC yang mudah dihubungi.',
        'Brilliant hanya merekomendasikan mitra vendor berstatus aktif dan terkurasi.',
      ],
    ],
    [
      'slug' => 'atur-budget-pernikahan-cerdas',
      'category' => 'tips-wedding',
      'title' => 'Cara Mengatur Budget Pernikahan dengan Cerdas',
      'excerpt' => 'Atur budget pernikahanmu dengan bijak tanpa mengurangi kualitas momen spesial.',
      'image' => 'https://images.unsplash.com/photo-1469334031218-e382a71b716b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
      'date' => '2024-05-15',
      'read_minutes' => 5,
      'author' => 'Tim Brilliant',
      'body' => [
        'Alokasikan 40% untuk venue & catering, 20% dokumentasi, 15% dekorasi, sisanya untuk busana dan lainnya.',
        'Sisakan buffer 10–15% untuk kebutuhan mendadak.',
        'Paket Brilliant sudah mencakup rincian layanan agar transparan sejak awal.',
      ],
    ],
    [
      'slug' => 'cerita-pernikahan-salsa-bimo',
      'category' => 'cerita-nyata',
      'title' => 'Cerita Nyata: Pernikahan Salsa & Bimo di Garut',
      'excerpt' => 'Dari konsultasi hingga hari H, lihat bagaimana Brilliant mendampingi pasangan ini.',
      'image' => 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80',
      'date' => '2024-04-20',
      'read_minutes' => 7,
      'author' => 'Tim Brilliant',
      'body' => [
        'Salsa & Bimo memilih paket Gold dengan dekorasi garden party.',
        'Tim lapangan memastikan rundown berjalan tepat waktu.',
        'Mereka merekomendasikan Brilliant ke teman karena pelayanan personal dan komunikasi yang jelas.',
      ],
    ],
  ],

];
