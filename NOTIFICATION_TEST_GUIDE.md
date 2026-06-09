# 🚀 Quick Test Guide - Notifikasi Admin Update

## ⚡ Apa yang Berubah?

### SEBELUM (v1):
- Tampil hanya notifikasi belum dibaca (`is_read: false`)
- Query: 50 notifikasi dengan filter `is_read: false`
- Jika semua dibaca → "Belum ada notifikasi"

### SESUDAH (v2):
- Tampil **10 notifikasi terbaru** (baca & belum baca)
- Query: 10 terbaru tanpa filter `is_read`
- Belum dibaca: **Blue highlight + bold + dot indicator**
- Sudah dibaca: **White + faded appearance**
- Riwayat tetap ditampilkan (tidak kosong jika ada)

---

## 🧪 Quick Testing Steps

### 1. Verify Backend Response
```bash
# Open browser console dan jalankan:
fetch('/api/notifications/poll', {
  headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
})
.then(r => r.json())
.then(d => {
  console.log('Total received:', d.notifications.length);
  console.log('Unread count:', d.unread_count);
  console.log('Sample:', d.notifications[0]);
})
```

**Expected:**
- `length` = 10 (or less if < 10 total)
- Mix of `is_read: true` dan `false`
- Setiap item punya field `is_read`

---

### 2. Check Dropdown Visual

1. Buka dropdown notifikasi (klik lonceng)
2. Lihat notifikasi yang ada:
   - **Belum dibaca** → Background biru muda + tulisan tebal + dot biru ✓
   - **Sudah dibaca** → Background putih + tulisan normal + no dot ✓

3. Lihat header:
   - Jika ada unread → "Tandai semua telah dibaca" visible ✓
   - Jika semua read → Button tidak terlihat ✓

---

### 3. Test Mark as Read

1. Klik notifikasi belum dibaca → styling berubah ke "sudah dibaca" ✓
2. Klik "Tandai semua" → semua item berubah styling ✓
3. Check: Redirect ke `link_redirect` berfungsi ✓

---

### 4. Check Empty State

1. Hapus semua notifikasi user (via database)
2. Refresh dropdown → "Belum ada notifikasi" muncul ✓

---

### 5. Check History Display

1. Create 10 notifikasi, mark semua as read
2. Open dropdown → Tetap tampil 10 notifikasi dengan white styling ✓
3. Create 1 notifikasi baru (unread)
4. Dropdown → New one blue, others white ✓

---

## 📋 Visual Checklist

```
UNREAD (is_read: false)           READ (is_read: true)
┌───────────────────────────┐     ┌───────────────────────────┐
│ 💵 Ada pembayaran baru    │     │ 📝 Tugas sudah selesai    │
│ 15 menit lalu    ●        │     │ 2 jam lalu                │
│ [bg-blue-50/80]           │     │ [bg-white opacity-75]     │
│ [border-l-blue-400]       │     │ [no dot]                  │
│ [font-bold]               │     │ [normal font]             │
└───────────────────────────┘     └───────────────────────────┘
```

---

## 🔍 Debug Commands

### Check unread count in state:
```javascript
// Type di console dalam Alpine.js context:
console.log('Unread:', window.$data.unreadCount);
console.log('Total notifications:', window.$data.notifications.length);
console.log('Grouped:', window.$data.groupedNotifications);
```

### Force refresh notifications:
```javascript
window.$data.loadNotifications();
```

### Check API response:
```javascript
const resp = await fetch('/api/notifications/poll', {
  headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content}
});
const data = await resp.json();
console.table(data.notifications); // Lihat semua field
```

---

## ✅ Acceptance Criteria

- [ ] Backend return 10 latest (not 50)
- [ ] No filter `is_read: false` in query
- [ ] Response include `is_read` field untuk setiap item
- [ ] Frontend tidak filter, tampilkan semua dari API
- [ ] Unread items: Blue bg + bold text + dot indicator
- [ ] Read items: White bg + faded + no dot
- [ ] Empty state jika `notifications.length === 0`
- [ ] History ditampilkan (white items) jika ada
- [ ] Badge count akurat (unread only)
- [ ] "Mark all" button hanya jika unread > 0

---

## 🐛 Common Issues

**Issue:** Empty state selalu muncul padahal ada notifikasi
- Check: `notifications.length` vs `groupedNotifications.length`
- Fix: Should check `notifications.length`

**Issue:** Semua notifikasi white, tidak ada blue
- Check: Backend send `is_read: true` untuk semua?
- Create test notifikasi dengan `is_read: false`

**Issue:** Styling tidak berubah setelah mark as read
- Check: `handleNotificationClick()` update state
- Verify: `notification.is_read = true` setelah POST success

**Issue:** Blue dot tidak terlihat
- Check: Template syntax `x-if="!notification.is_read"`
- Verify: CSS class `h-2 w-2 rounded-full bg-blue-500` applied

---

## 📊 Files to Check

1. `app/Http/Controllers/NotificationController.php`
   - Line 27-32: Query changed to `limit(10)` without filter

2. `resources/views/components/notification-dropdown.blade.php`
   - Line 56: Empty state check `notifications.length === 0`
   - Line 82-83: Styling `:class` for is_read distinction
   - Line 288-306: Removed filter in `buildGroupedNotifications()`
   - Line 100: Blue dot indicator template

