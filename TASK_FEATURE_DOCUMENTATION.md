# Dokumentasi Fitur Tugas (Task Management)

## Ringkasan Perubahan

Fitur manajemen tugas telah berhasil diimplementasikan dengan tampilan yang sesuai mockup UI. Berikut adalah file-file yang telah dibuat dan diubah:

### File yang Dibuat:

1. **Model: `app/Models/Tugas.php`**
   - Model untuk mengelola data tugas
   - Relasi dengan Pesanan, User (creator), dan User (PIC)
   - Mendukung JSON untuk checklists

2. **Controller: `app/Http/Controllers/Lapangan/TugasController.php`**
   - CRUD operations untuk tugas
   - Validasi input
   - Authorization via TugasPolicy

3. **Policy: `app/Policies/TugasPolicy.php`**
   - Authorization untuk CRUD operations
   - Memastikan hanya user yang membuat tugas bisa mengedit/menghapus

4. **Views:**
   - `resources/views/lapangan/modules/tugas.blade.php` - Daftar tugas
   - `resources/views/lapangan/modules/tugas_form.blade.php` - Form tambah/edit tugas

5. **Migration: `database/migrations/2026_05_27_create_tugas_table.php`**
   - Table structure untuk tugas dengan kolom:
     - nama_tugas, kategori, prioritas
     - deadline, checklists (JSON), catatan
     - user_id (pembuat), pic_id (penanggung jawab), pesanan_id

### File yang Diubah:

1. **Routes: `routes/web.php`**
   - Menambahkan import TugasController
   - Menambahkan resource route: `Route::resource('tugas', LapanganTugasController::class);`

2. **Layout: `resources/views/layouts/lapangan.blade.php`**
   - Update link Tugas di sidebar ke `route('lapangan.tugas.index')`

3. **Dashboard: `resources/views/lapangan/modules/dashboard.blade.php`**
   - Kode sudah diperbarui untuk display yang lebih baik

## Fitur Form Tugas

### Komponen UI (sesuai mockup):

1. **Header Card**
   - Judul "Tambah Tugas Baru"
   - Deskripsi "Tambahkan tugas baru untuk persiapan acara"
   - Icon close (X) di kanan atas

2. **Input Form (2 Grid)**
   - Nama Tugas: Text input
   - Pilih Acara: Select dengan thumbnail gambar
   - Kategori: Select dengan icon dinamis (Dekorasi, Catering, MUA, Dokumentasi)
   - Prioritas: Button group radio (High/Red, Medium/Amber, Low/Green)

3. **Deadline & PIC**
   - Deadline: Split date & time input dengan icon
   - PIC: Select dengan icon user

4. **Checklist Detail (Dynamic)**
   - Checkbox (hijau saat dicentang)
   - Input teks untuk item
   - Drag handle (6 dots)
   - Delete button (trash icon)
   - Button "+ Tambah checklist" untuk menambah item baru
   - Alpine.js untuk interaksi dinamis

5. **Catatan**
   - Textarea large dengan counter (54/500)

6. **Action Buttons**
   - Batal (outline)
   - Simpan Tugas (solid green dengan icon calendar)

## Implementasi JavaScript/Alpine.js

Form menggunakan Alpine.js untuk:
- Dynamic checklist management (add/remove items)
- Priority selection dengan visual feedback
- Character counter untuk catatan
- Thumbnail display saat pilih acara

Fitur interaktif tanpa page reload.

## Database Setup

```bash
# Jalankan migration
php artisan migrate

# Clear cache jika diperlukan
php artisan config:cache
php artisan view:clear
```

## Routes yang Tersedia

- `GET /lapangan/tugas` - Daftar tugas (TugasController@index)
- `GET /lapangan/tugas/create` - Form tambah tugas (TugasController@create)
- `POST /lapangan/tugas` - Simpan tugas baru (TugasController@store)
- `GET /lapangan/tugas/{tugas}/edit` - Form edit tugas (TugasController@edit)
- `PUT /lapangan/tugas/{tugas}` - Update tugas (TugasController@update)
- `DELETE /lapangan/tugas/{tugas}` - Hapus tugas (TugasController@destroy)

## Tailwind CSS Utility Classes Digunakan

- Grid Layout: `grid-cols-1`, `md:grid-cols-2`, `lg:grid-cols-4`
- Spacing: `gap-6`, `p-6`, `mb-4`, etc.
- Colors: `bg-field`, `text-field`, `border-gray-200`
- Interactions: `hover:bg-gray-50`, `focus:border-field`, `transition`
- Responsive: `max-w-4xl`, `flex-1`, etc.
- Typography: `text-3xl`, `font-bold`, `text-gray-900`

## Catatan Penting

1. Untuk production, pastikan Pesanan model memiliki method `aktifLapangan()` atau `aktif()`
2. User model harus memiliki field `role` untuk filter lapangan
3. Foto pengantin di tabel pesanans diperlukan untuk thumbnail di dropdown acara
4. Checklists disimpan sebagai JSON array dengan structure: `[{text: string, completed: boolean}]`

## Testing

1. Buka `/lapangan/tugas` untuk melihat daftar tugas
2. Klik "Tambah Tugas" untuk membuka form
3. Isi semua field sesuai requirement
4. Gunakan "+ Tambah checklist" untuk menambah item
5. Klik "Simpan Tugas" untuk menyimpan

## Troubleshooting

- Jika route tidak ditemukan: Pastikan migration sudah dijalankan dan routes sudah ditambahkan
- Jika form tidak berfungsi: Pastikan Alpine.js sudah ter-load di layout
- Jika validation error: Check controller validation rules di TugasController
