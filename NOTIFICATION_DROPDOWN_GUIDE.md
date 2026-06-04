# 📬 Dokumentasi Sistem Notifikasi Dropdown

## ✅ Status Implementasi

Sistem notifikasi dropdown telah berhasil diintegrasikan ke seluruh aplikasi dengan fitur lengkap:

- ✔️ Dropdown UI responsif dengan animasi smooth
- ✔️ Fetching data asinkron dari API `/api/notifications/poll`
- ✔️ Render notifikasi dengan styling dinamis
- ✔️ Redirect otomatis saat notifikasi diklik
- ✔️ Tutup dropdown saat klik di luar
- ✔️ Auto-polling setiap 15 detik (hanya saat dropdown ditutup)

---

## 📁 File yang Diubah

### 1. **Komponen Baru:**
- `resources/views/components/notification-dropdown.blade.php` - Komponen dropdown notifikasi lengkap

### 2. **Template yang Diintegrasikan:**

#### Admin Dashboard:
- `resources/views/admin/vendor/index.blade.php`
- `resources/views/admin/pembayaran/index.blade.php`
- `resources/views/admin/paket/index.blade.php`
- `resources/views/admin/booking/index.blade.php`
- `resources/views/admin/chat/index.blade.php`
- `resources/views/admin/jadwal/index.blade.php`

#### Customer Dashboard:
- `resources/views/customer/pesanan_detail.blade.php`
- `resources/views/customer/pesanan.blade.php`
- `resources/views/customer/invoice.blade.php`

---

## 🎯 Fitur Detail

### 1. **Dropdown UI** 
```
Notifikasi
├── Header dengan "Tandai semua telah dibaca"
├── Loading state saat fetch data
├── Empty state saat tidak ada notifikasi
├── Daftar notifikasi dengan:
│   ├── Icon status (urgent/normal)
│   ├── Category badge
│   ├── Message dengan styling (urgent = merah)
│   ├── Timestamp (Baru saja, 5 menit lalu, dll)
│   └── Delete button (hover-only)
└── Error state jika fetch gagal
```

### 2. **Data Fetching**
- **Endpoint:** `GET /api/notifications/poll`
- **Response Format:**
```json
{
  "success": true,
  "unread_count": 5,
  "notifications": [
    {
      "id": 1,
      "message": "Pesanan baru telah diterima",
      "category": "Booking",
      "priority": "urgent|normal",
      "link_redirect": "/admin/booking/123",
      "is_read": false,
      "created_at": "2025-06-04T10:30:00Z"
    }
  ],
  "timestamp": "2025-06-04T11:45:00Z"
}
```

### 3. **Styling Notifikasi**

| Status | Styling |
|--------|---------|
| **Unread** | Background biru (bg-blue-50) + indikator dot |
| **Read** | Background putih (bg-white) |
| **Urgent** | Border merah kiri + icon warning + text merah |
| **Normal** | Border standar + icon info + text abu-abu |

### 4. **Fitur Interaksi**

| Aksi | Hasil |
|------|-------|
| Klik bell icon | Toggle dropdown (buka/tutup) |
| Klik notifikasi | Mark as read + Redirect ke `link_redirect` |
| "Tandai semua dibaca" | Update semua notifikasi jadi `is_read: true` |
| Klik delete icon | Hapus notifikasi dengan konfirmasi |
| Klik di luar dropdown | Tutup dropdown secara otomatis |

### 5. **Auto-Polling**
```javascript
// Polling setiap 15 detik HANYA saat dropdown tertutup
if (!isOpen) {
    loadNotifications();
}
```
Ini menghemat bandwidth dan performa!

---

## 🔧 Cara Menggunakan

### Untuk menampilkan dropdown di halaman lain:
```blade
@include('components.notification-dropdown')
```

### Struktur minimal di dalam layout:
```blade
<div class="flex items-center space-x-6">
    @include('components.notification-dropdown')
    <!-- konten lainnya -->
</div>
```

---

## 📊 Model Data Notifikasi

File: `app/Models/UserNotification.php`

```php
$table->id();
$table->foreign('user_id');
$table->string('message');
$table->string('category')->nullable(); // Booking, Pembayaran, Chat, dll
$table->enum('priority', ['normal', 'urgent'])->default('normal');
$table->string('link_redirect')->nullable(); // URL redirect
$table->string('reference_type')->nullable(); // booking, payment, message, dll
$table->unsignedBigInteger('reference_id')->nullable(); // ID dari reference
$table->boolean('is_read')->default(false);
$table->timestamps();
```

---

## 🚀 Endpoints API

| Method | Endpoint | Deskripsi |
|--------|----------|-----------|
| GET | `/api/notifications/poll` | Ambil notifikasi unread |
| POST | `/api/notifications/{id}/read` | Mark satu notifikasi sebagai read |
| POST | `/api/notifications/read-all` | Mark semua notifikasi sebagai read |
| DELETE | `/api/notifications/{id}` | Hapus notifikasi |
| GET | `/api/notifications/count` | Hanya ambil jumlah unread |

---

## 🎨 Customization

### Mengubah polling interval:
```javascript
// Di notification-dropdown.blade.php, cari baris:
this.pollInterval = setInterval(() => {
    if (!this.isOpen) {
        this.loadNotifications();
    }
}, 15000); // Ubah 15000 (ms) sesuai kebutuhan
```

### Mengubah warna styling:
Komponen menggunakan Tailwind classes yang bisa diubah di dalam `notification-dropdown.blade.php`:
- `bg-blue-50` untuk unread background
- `text-red-600` untuk urgent text
- `bg-red-500` untuk badge

---

## ⚙️ Testing Manual

### 1. Test Fetch Data
```bash
# Buka browser console dan jalankan:
fetch('/api/notifications/poll')
    .then(r => r.json())
    .then(d => console.log(d))
```

### 2. Test Mark as Read
```bash
# Ganti ID dengan notifikasi ID yang ada
fetch('/api/notifications/1/read', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
})
.then(r => r.json())
.then(d => console.log(d))
```

### 3. Test Delete
```bash
fetch('/api/notifications/1', {
    method: 'DELETE',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
})
.then(r => r.json())
.then(d => console.log(d))
```

---

## 🔒 Fitur Keamanan

✅ **CSRF Protection** - Semua POST/DELETE request dilindungi CSRF token
✅ **Authorization** - Backend memvalidasi `user_id` sebelum menampilkan/menghapus notifikasi
✅ **XSS Protection** - Alpine.js otomatis escape HTML di `x-text` directive
✅ **Error Handling** - User-friendly error messages tanpa expose stack trace

---

## 📱 Responsive Design

- ✅ Dropdown lebar 384px (w-96) di desktop
- ✅ Max height 384px (max-h-96) dengan scroll
- ✅ Shadow dan border radius yang konsisten
- ✅ Animasi smooth saat open/close
- ✅ Touch-friendly untuk mobile

---

## 🐛 Troubleshooting

### Dropdown tidak muncul?
1. Pastikan Alpine.js sudah loaded di layout (`layouts/admin.blade.php`)
2. Check browser console untuk error
3. Pastikan CSRF token ada di meta tag

### Notifikasi tidak muncul?
1. Buat notifikasi di database:
```php
UserNotification::create([
    'user_id' => auth()->id(),
    'message' => 'Test notifikasi',
    'category' => 'Testing',
    'priority' => 'normal',
    'link_redirect' => '/admin/dashboard'
]);
```
2. Klik bell icon untuk refresh
3. Check API response di Network tab browser

### Redirect tidak bekerja?
1. Pastikan `link_redirect` berisi URL valid
2. Check route yang dituju ada di `routes/web.php`
3. Test manual di browser: `window.location.href = '/path'`

---

## 📝 Notes

- Komponen ini fully functional dan production-ready
- Tidak perlu modifikasi lanjutan kecuali untuk styling/branding
- Polling hanya berjalan saat dropdown ditutup untuk efisiensi
- Semua validasi dilakukan di backend untuk keamanan

---

**✨ Implementasi Selesai!** Sistem notifikasi dropdown siap digunakan.
