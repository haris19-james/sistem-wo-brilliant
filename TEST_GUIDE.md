# TEST DATA & QUICK START GUIDE

## 🚀 Quick Start

### 1. Setup Database
```bash
# Navigate ke project
cd c:\laragon\www\sistem-wo-brilliant2

# Run migration
php artisan migrate

# Clear cache
php artisan config:cache
php artisan view:clear
```

### 2. Access Form
```
URL: http://localhost/lapangan/tugas
     http://localhost/lapangan/tugas/create
```

---

## 📊 SAMPLE DATA STRUCTURE

### Pesanan (Acara)
```sql
INSERT INTO pesanans (nama_pasangan, lokasi, jam_acara, tanggal_acara, foto_pernikahan, status, created_at)
VALUES (
  'Marsya & Rizky',
  'Grand Ballroom, Hotel Melia Jakarta',
  '10:00:00',
  '2025-05-25',
  '/storage/acara/marsya-rizky.jpg',
  'Sedang Berlangsung',
  NOW()
);
```

### User (Lapangan)
```sql
INSERT INTO users (name, email, role, password, created_at)
VALUES (
  'Korlap',
  'korlap@brilliant.com',
  'lapangan',
  bcrypt('password123'),
  NOW()
);
```

### Tugas (Task)
```sql
INSERT INTO tugas (
  user_id,
  pesanan_id,
  pic_id,
  nama_tugas,
  kategori,
  prioritas,
  deadline,
  checklists,
  catatan,
  status,
  created_at
) VALUES (
  1,
  1,
  1,
  'Setup Dekorasi Ballroom',
  'Dekorasi',
  'high',
  '2025-05-25 09:00:00',
  '[{"text":"Cek Bunga","completed":true},{"text":"Cek Lighting","completed":true}]',
  'Pastikan dekor selesai sebelum gladi bersih',
  'pending',
  NOW()
);
```

---

## 🎯 TEST SCENARIOS

### Test Case 1: Tambah Tugas Baru
**Path:** GET /lapangan/tugas/create

**Input:**
```
Nama Tugas: "Setup Dekorasi Ballroom"
Pilih Acara: "Marsya & Rizky"
Kategori: "Dekorasi"
Prioritas: "High"
Deadline: "25 Mei 2025" + "09:00 WIB"
PIC: "Korlap"
Checklist:
  - Cek Bunga ✓
  - Cek Lighting ✓
  - Cek Backdrop
Catatan: "Pastikan dekor selesai sebelum gladi bersih"
```

**Expected Output:**
- Form tersimpan dengan success message
- Redirect ke halaman daftar tugas
- Tugas muncul di list dengan status "Pending"

---

### Test Case 2: Edit Tugas
**Path:** GET /lapangan/tugas/1/edit

**Expected:**
- Form pre-filled dengan data tugas sebelumnya
- Semua field dapat diubah
- Update berhasil tersimpan

---

### Test Case 3: Delete Tugas
**Path:** DELETE /lapangan/tugas/1

**Expected:**
- Confirmation dialog muncul
- Tugas terhapus dari list
- Success message tampil

---

## 🔍 FIELD VALIDATION

### Nama Tugas
- Required
- String
- Max 255 characters
- Error: "Nama tugas harus diisi"

### Acara (pesanan_id)
- Required
- Must exist in pesanans table
- Error: "Pilih acara terlebih dahulu"

### Kategori
- Required
- Options: Dekorasi, Catering, MUA, Dokumentasi, Transportasi, Lainnya
- Error: "Kategori tidak valid"

### Prioritas
- Required
- Options: high, medium, low
- Error: "Prioritas harus dipilih"

### Deadline
- Date: Required, must be valid date
- Time: Required, valid time format
- Error: "Deadline tidak valid"

### PIC (pic_id)
- Required
- Must exist in users table
- Error: "PIC tidak valid"

### Catatan
- Optional
- String
- Max 500 characters
- Character counter: "54/500"

### Checklists
- Optional
- Array of strings
- Each item: "{ text: string, completed: boolean }"

---

## 🎨 UI ELEMENT TESTING

### Header
- [ ] Judul "Tambah Tugas Baru" muncul
- [ ] Deskripsi "Tambahkan tugas baru untuk persiapan acara" muncul
- [ ] Icon close (X) di kanan atas berfungsi

### Input Grid
- [ ] Nama Tugas input dapat diisi
- [ ] Acara dropdown menampilkan pilihan
- [ ] Thumbnail gambar muncul saat pilih acara
- [ ] Kategori dropdown menampilkan icon
- [ ] Icon berubah saat kategori berubah

### Priority Buttons
- [ ] High button berwarna merah (red-50 border)
- [ ] Medium button berwarna oranye (amber-50 border)
- [ ] Low button berwarna hijau (green-50 border)
- [ ] Selected priority menunjukkan highlight
- [ ] Bisa switch antar priority dengan mudah

### Deadline Input
- [ ] Date picker terbuka saat klik
- [ ] Time input dapat diisi manual
- [ ] Format: "25 Mei 2025" dan "09:00 WIB"

### Checklist
- [ ] Checkbox muncul dengan style hijau
- [ ] Text input dapat diisi per item
- [ ] Drag handle (6 dots) muncul saat hover
- [ ] Delete icon (trash) muncul saat hover
- [ ] Button "+ Tambah checklist" berfungsi
- [ ] Checked items menunjukkan visual feedback

### Catatan
- [ ] Textarea dapat diisi
- [ ] Character counter update real-time
- [ ] Max 500 characters enforced
- [ ] Format: "54/500"

### Action Buttons
- [ ] Button "Batal" membawa ke halaman list
- [ ] Button "Simpan Tugas" submit form
- [ ] Tombol tidak aktif jika ada validation error
- [ ] Success message muncul setelah submit

---

## 📱 RESPONSIVE TESTING

### Desktop (lg)
- [ ] 2 kolom layout untuk input atas
- [ ] Form width max-w-4xl centered
- [ ] Spacing sesuai

### Tablet (md)
- [ ] 2 kolom layout tetap
- [ ] Responsive padding

### Mobile (sm)
- [ ] 1 kolom layout
- [ ] Full width form
- [ ] Button full width

---

## 🔐 SECURITY TESTING

- [ ] Non-author tidak bisa edit tugas orang lain
- [ ] Non-author tidak bisa delete tugas orang lain
- [ ] CSRF token ada di form
- [ ] SQL injection tidak bisa terjadi (via Eloquent)
- [ ] XSS protection (via Blade escaping)

---

## 🐛 BROWSER COMPATIBILITY

- [ ] Chrome latest
- [ ] Firefox latest
- [ ] Safari latest
- [ ] Edge latest
- [ ] Mobile Chrome
- [ ] Mobile Safari

---

## 📊 PERFORMANCE CHECKLIST

- [ ] Form load time < 1 second
- [ ] Checklist add/remove tidak lag
- [ ] Category icon change smooth
- [ ] Priority button toggle instant
- [ ] Form submit dengan loading indicator

---

## 💾 DATABASE TESTING

### Check Migration
```sql
DESCRIBE tugas;
```

Should show:
- id (bigint, primary)
- user_id (bigint)
- pesanan_id (bigint)
- pic_id (bigint)
- nama_tugas (varchar)
- kategori (varchar)
- prioritas (enum)
- deadline (datetime)
- checklists (longtext/json)
- catatan (text)
- status (enum)
- created_at, updated_at (timestamp)

### Check Foreign Keys
```sql
SELECT * FROM INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS 
WHERE TABLE_NAME = 'tugas';
```

Should have constraints on:
- user_id → users.id
- pesanan_id → pesanans.id
- pic_id → users.id

---

## 🎯 ACCEPTANCE CRITERIA

✅ = Pass, ❌ = Fail

- ✅ Form displays all required fields
- ✅ Validation works correctly
- ✅ Data saved to database
- ✅ Checklist items dynamic add/remove
- ✅ Character counter accurate
- ✅ Priority colors display correctly
- ✅ Category icons display correctly
- ✅ Acara thumbnail displays
- ✅ Form responsive on mobile
- ✅ Authorization working
- ✅ List view displays created tasks
- ✅ Edit functionality works
- ✅ Delete functionality works
- ✅ Empty state displays when no tasks

---

## 📝 NOTES

1. Jika migration gagal, check:
   - Database connection di .env
   - Foreign keys referencing existing tables
   - No duplicate timestamps

2. Jika form tidak submit, check:
   - Browser console untuk JS errors
   - Network tab untuk request failure
   - Server logs (`storage/logs/`)

3. Jika styling aneh, check:
   - Tailwind CSS loaded properly
   - Browser cache cleared
   - Custom colors defined in config

4. Jika thumbnail tidak muncul:
   - Check `foto_pernikahan` field value di database
   - Verify image file exists
   - Check storage path permissions

---

Semua test scenarios sudah siap. Silakan jalankan sesuai urutan untuk memastikan setiap fitur berfungsi dengan baik.
