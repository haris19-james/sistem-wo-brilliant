# 🧪 Testing Guide - Notification Dropdown

Panduan lengkap untuk test sistem notifikasi dropdown dengan mudah.

---

## ⚡ Quick Start Testing

### 1. Buat Notifikasi Dummy via Tinker

```bash
php artisan tinker
```

Kemudian copy-paste perintah berikut:

```php
$user = auth()->user() ?? \App\Models\User::first();

// Notifikasi normal
\App\Models\UserNotification::create([
    'user_id' => $user->id,
    'message' => 'Pesanan baru telah diterima dari klien',
    'category' => 'Booking',
    'priority' => 'normal',
    'link_redirect' => '/admin/booking',
    'reference_type' => 'booking',
    'is_read' => false,
]);

// Notifikasi urgent
\App\Models\UserNotification::create([
    'user_id' => $user->id,
    'message' => 'Pembayaran tertunda! Segera hubungi klien.',
    'category' => 'Pembayaran',
    'priority' => 'urgent',
    'link_redirect' => '/admin/pembayaran',
    'reference_type' => 'payment',
    'is_read' => false,
]);

// Notifikasi sudah dibaca
\App\Models\UserNotification::create([
    'user_id' => $user->id,
    'message' => 'Paket wedding baru telah ditambahkan',
    'category' => 'Paket',
    'priority' => 'normal',
    'link_redirect' => '/admin/paket',
    'is_read' => true,
]);

exit
```

**Hasil:** 3 notifikasi sudah tersimpan di database!

---

## 🎯 Test Scenarios

### Scenario 1: Lihat Notifikasi Muncul di Dropdown

**Langkah:**
1. Login ke aplikasi
2. Klik **bell icon** di header
3. Dropdown muncul dengan 3 notifikasi:
   - 2 unread (background biru)
   - 1 read (background putih)

**Expected Result:**
✅ Notifikasi tampil dengan styling benar
✅ Badge menunjukkan "2" (unread count)
✅ Urgent notifikasi memiliki border merah & text merah

---

### Scenario 2: Mark as Read

**Langkah:**
1. Dropdown terbuka
2. Klik notifikasi "Pesanan baru telah diterima"
3. Browser redirect ke `/admin/booking`

**Expected Result:**
✅ Notifikasi berubah ke background putih (read)
✅ Redirect berhasil
✅ Badge berkurang dari 2 → 1

---

### Scenario 3: Mark All as Read

**Langkah:**
1. Kembali ke halaman sebelumnya
2. Klik bell icon lagi
3. Klik "Tandai semua telah dibaca"

**Expected Result:**
✅ Semua notifikasi menjadi putih (read)
✅ Badge hilang (0 unread)
✅ Message "Tidak ada notifikasi" muncul (setelah refresh)

---

### Scenario 4: Delete Notifikasi

**Langkah:**
1. Buat notifikasi baru (pakai Tinker)
2. Klik bell icon
3. Hover pada notifikasi → tombol X muncul
4. Klik X dan confirm delete

**Expected Result:**
✅ Notifikasi hilang dari dropdown
✅ Alert konfirmasi muncul sebelum delete
✅ Database ter-update

---

### Scenario 5: Close Dropdown saat Klik di Luar

**Langkah:**
1. Buka dropdown
2. Klik area di luar dropdown (misal: main content)

**Expected Result:**
✅ Dropdown tutup dengan smooth animation
✅ Perubahan di dropdown tidak hilang (data masih tersimpan)

---

### Scenario 6: Auto-Polling

**Langkah:**
1. Buka dropdown
2. Tutup dropdown
3. Buat notifikasi baru via terminal:
```bash
php artisan tinker
\App\Models\UserNotification::create([
    'user_id' => 1,
    'message' => 'Polling test - notifikasi baru!',
    'category' => 'Test',
    'priority' => 'normal',
    'link_redirect' => '/admin/dashboard',
    'is_read' => false,
]);
exit
```
4. Tunggu 15 detik
5. Badge pada bell icon harus berubah (menunjukkan notifikasi baru)

**Expected Result:**
✅ Notifikasi baru ter-detect tanpa refresh halaman
✅ Polling berjalan di background

---

## 🔍 Testing via Browser Console

### Test Fetch API

```javascript
// Di browser console, jalankan:
const response = await fetch('/api/notifications/poll');
const data = await response.json();
console.log(data);
```

**Output yang diharapkan:**
```json
{
  "success": true,
  "unread_count": 2,
  "notifications": [
    {
      "id": 1,
      "message": "Pesanan baru telah diterima dari klien",
      "category": "Booking",
      "priority": "normal",
      "link_redirect": "/admin/booking",
      "is_read": false,
      "created_at": "2025-06-04T10:30:00Z"
    },
    ...
  ],
  "timestamp": "2025-06-04T11:45:00Z"
}
```

### Test Mark as Read

```javascript
const notifId = 1; // Ganti dengan ID notifikasi
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const response = await fetch(`/api/notifications/${notifId}/read`, {
    method: 'POST',
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json'
    }
});

const data = await response.json();
console.log(data);
```

**Expected output:**
```json
{
  "success": true
}
```

### Test Mark All as Read

```javascript
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const response = await fetch('/api/notifications/read-all', {
    method: 'POST',
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json'
    }
});

const data = await response.json();
console.log(data);
```

### Test Delete

```javascript
const notifId = 1;
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

const response = await fetch(`/api/notifications/${notifId}`, {
    method: 'DELETE',
    headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Content-Type': 'application/json'
    }
});

const data = await response.json();
console.log(data);
```

---

## 📊 Database Query Testing

### Check total notifikasi untuk user

```sql
SELECT COUNT(*) as total, is_read, priority 
FROM user_notifications 
WHERE user_id = 1
GROUP BY is_read, priority;
```

### Check notifikasi unread

```sql
SELECT * FROM user_notifications 
WHERE user_id = 1 AND is_read = false 
ORDER BY created_at DESC;
```

### Check notifikasi urgent

```sql
SELECT * FROM user_notifications 
WHERE user_id = 1 AND priority = 'urgent'
ORDER BY created_at DESC;
```

---

## 🎬 Recorded Test Cases

### Test Case 1: Full User Flow ✅
```
1. User login
2. Lihat badge pada bell icon (2 unread)
3. Klik bell icon → dropdown muncul
4. Klik notifikasi urgent (red) → redirect
5. Klik bell icon lagi
6. Klik "Tandai semua dibaca"
7. Badge hilang
8. Dropdown tutup saat klik di luar
```

### Test Case 2: API Only (via curl) ✅
```bash
# Set variables
USER_ID=1
CSRF_TOKEN="your-csrf-token"
COOKIE="your-session-cookie"

# Test GET /api/notifications/poll
curl -X GET http://localhost/api/notifications/poll \
  -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
  -H "Accept: application/json" \
  -b "XSRF-TOKEN=$COOKIE"

# Test POST /api/notifications/1/read
curl -X POST http://localhost/api/notifications/1/read \
  -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
  -H "Accept: application/json" \
  -b "XSRF-TOKEN=$COOKIE"

# Test DELETE /api/notifications/1
curl -X DELETE http://localhost/api/notifications/1 \
  -H "X-CSRF-TOKEN: $CSRF_TOKEN" \
  -H "Accept: application/json" \
  -b "XSRF-TOKEN=$COOKIE"
```

---

## ⚠️ Common Issues & Solutions

### Issue 1: Badge tidak update
**Solution:**
- Check network tab → `/api/notifications/poll` harus respond 200
- Clear browser cache (Ctrl+Shift+Del)
- Check browser console untuk error

### Issue 2: Redirect tidak bekerja
**Solution:**
- Pastikan `link_redirect` valid di database
- Check di Network tab, request ke URL berhasil?
- Test redirect manual: `window.location.href = '/admin/booking'`

### Issue 3: Dropdown tidak muncul
**Solution:**
- Check Alpine.js loaded: `console.log(Alpine)`
- Check HTML element exists: `document.getElementById('notification-wrapper')`
- Check browser console untuk error Alpine

### Issue 4: CSRF error saat delete
**Solution:**
- Pastikan CSRF token ada di meta tag: `<meta name="csrf-token">`
- Refresh halaman dan coba lagi
- Check middleware di `app/Http/Middleware/VerifyCsrfToken.php`

---

## 📈 Performance Testing

### Check Response Time

```javascript
const start = performance.now();
const response = await fetch('/api/notifications/poll');
const end = performance.now();

console.log(`Request time: ${end - start}ms`);
```

**Target:** < 100ms

### Check Memory Usage

```javascript
// Di browser DevTools → Performance tab
// Record → Klik bell icon → Stop
// Check memory allocation
```

---

## ✅ Checklist Test Final

- [ ] Notifikasi muncul di dropdown
- [ ] Badge menunjukkan angka unread
- [ ] Urgent notifikasi berwarna merah
- [ ] Klik notifikasi → mark as read + redirect
- [ ] "Tandai semua dibaca" → update semua
- [ ] Delete notifikasi → hapus dari list
- [ ] Klik di luar → dropdown tutup
- [ ] Auto-polling bekerja setiap 15 detik
- [ ] CSRF protection aktif
- [ ] Response time < 100ms
- [ ] Tampilan responsive di mobile
- [ ] Loading state muncul saat fetch

---

**🎉 Jika semua test passed, sistem notifikasi sudah production-ready!**
