# Quick Reference - Refund & Notifikasi Multi-Role

## 🚀 Quick Start (Copy-Paste Ready)

### 1. Inject Services di Constructor

```php
protected $refundService;
protected $notificationService;

public function __construct(
    RefundService $refundService,
    NotificationCenterService $notificationService
) {
    $this->refundService = $refundService;
    $this->notificationService = $notificationService;
}
```

---

## 🔄 Proses Refund (Dengan Auto-Notification)

```php
// Controller method
public function processRefund(Request $request, Pesanan $pesanan)
{
    $result = $this->refundService->processRefund(
        pesananId: $pesanan->id,
        penaltyPercent: $request->input('penalty_percent', 0),
        alasanRefund: $request->input('alasan')
    );

    if ($result['success']) {
        // Notifikasi sudah otomatis dikirim oleh RefundService!
        return response()->json($result);
    }
    
    return response()->json($result, 422);
}
```

**Apa yang otomatis terjadi:**
- ✅ DP amount dihitung
- ✅ Potongan penalti dihitung
- ✅ Status pesanan diupdate ke `refunded` dan `cancelled`
- ✅ Notifikasi dikirim ke Admin, Client, dan Korlap
- ✅ Transaksi di-log untuk audit

---

## 📢 Kirim Notifikasi Manual ke Multi-Role

```php
// Kapan saja, di controller/service mana pun
$this->notificationService->sendNotification(
    bookingId: $pesanan->id,
    eventType: 'payment_approved',        // refund_processed, booking_confirmed, dll
    message: 'Pesan notifikasi di sini',
    targetRoles: ['admin', 'client', 'korlap'],  // Siapa dapat notifikasi
    linkRedirect: route('customer.pesanan.show', $pesanan->id),
    priority: 'normal',                   // atau 'high'/'urgent'
    metadata: ['payment_amount' => 5000000]
);
```

---

## 📋 Parameter Event Types yang Tersedia

```php
'refund_processed'     // Refund berhasil diproses
'payment_approved'     // Pembayaran dikonfirmasi
'payment_rejected'     // Pembayaran ditolak
'booking_confirmed'    // Booking dikonfirmasi
'booking_assigned'     // Korlap ditugaskan
'booking_cancelled'    // Booking dibatalkan
'task_created'         // Tugas dibuat
'chat_new'             // Pesan chat baru
'vendor_checkin'       // Vendor check-in
'status_changed'       // Status booking berubah
```

---

## 💡 Common Usage Patterns

### Pattern 1: Refund + Notifikasi (Sudah Terintegrasi)
```php
// RefundService otomatis kirim notifikasi
$result = $this->refundService->processRefund(123, 20, 'Client batal');
// Admin, Client, Korlap sudah dapat notifikasi! ✅
```

### Pattern 2: Manual Multi-Role Notification
```php
// Kapan butuh kirim notifikasi ke 3 role
$this->notificationService->sendNotification(
    bookingId: 123,
    eventType: 'booking_confirmed',
    message: 'Booking WO-2024-001 dikonfirmasi',
    targetRoles: ['admin', 'client', 'korlap']
);
```

### Pattern 3: Notifikasi Selective (Hanya Admin)
```php
// Hanya kirim ke Admin saja
$this->notificationService->sendNotification(
    bookingId: 123,
    eventType: 'issue_reported',
    message: 'Ada kendala di lapangan',
    targetRoles: ['admin'],
    priority: 'high'
);
```

### Pattern 4: Notifikasi dengan Meta Data
```php
// Dengan tracking metadata untuk audit
$this->notificationService->sendNotification(
    bookingId: 123,
    eventType: 'refund_processed',
    message: 'Refund berhasil',
    metadata: [
        'dp_amount' => 5000000,
        'penalty_percent' => 20,
        'final_refund' => 4000000,
        'processed_by' => auth()->id(),
    ]
);
```

---

## 🔍 Preview Refund (Sebelum Process)

```php
// Lihat estimasi refund tanpa ubah database
$preview = $this->refundService->getRefundPreview(
    pesananId: 123,
    penaltyPercent: 20
);

// Return:
// {
//   "success": true,
//   "dp_amount": 5000000,
//   "penalty_percent": 20,
//   "penalty_amount": 1000000,
//   "final_refund": 4000000
// }
```

---

## 📡 API Endpoints Cheat Sheet

| Method | Endpoint | Fungsi |
|--------|----------|--------|
| GET | `/admin/refund/eligible` | List booking yang bisa di-refund |
| GET | `/admin/refund/{id}/preview?penalty_percent=20` | Preview refund amount |
| POST | `/admin/refund/{id}/process` | Proses refund + kirim notifikasi |
| GET | `/admin/refund/{id}/status` | Check refund status |

---

## 🎯 Database Field Reference

**Pesanan model (setelah refund):**
```
status_pembayaran  → 'refunded'          (dari 'dp_paid' atau 'fully_paid')
status_booking     → 'cancelled'         (dari 'pending' atau 'approved_dp')
status_pemesanan   → 'canceled'          (dari lainnya)
jumlah_refund      → 4000000             (nominal yang dikembalikan)
alasan_pembatalan  → 'Client minta...'   (reason yang disimpan)
dibatalkan_at      → 2024-06-04 10:30    (timestamp pembatalan)
```

**UserNotification (untuk setiap role):**
```
user_id            → ID admin/client/korlap
role               → 'admin' / 'client' / 'lapangan'
message            → Pesan notifikasi
priority           → 'normal' / 'high'
category           → 'payment' (untuk refund events)
is_read            → false (initial), true (setelah dibaca)
link_redirect      → /customer/pesanan/123
created_at         → timestamp
```

---

## ⚡ Frontend Integration Minimal

```html
<!-- Otomatis polling notifikasi setiap 5 detik -->
<div data-notification-auto-poll data-poll-interval="5000"></div>

<!-- Badge untuk unread count -->
<span data-notification-badge>3</span>

<!-- Toast container (auto-generated jika tidak ada) -->
<div id="notification-toast-container"></div>

<!-- Notification panel -->
<div data-notification-panel>
  <div data-notification-list></div>
</div>
```

Script `notification-poller.js` akan otomatis:
- ✅ Poll `/api/notifications/poll` setiap 5 detik
- ✅ Tampilkan toast notification
- ✅ Update badge count
- ✅ Add ke notification panel

---

## 🐛 Debug Commands (Tinker)

```php
# Check refund status
$pesanan = App\Models\Pesanan::find(123);
$pesanan->jumlah_refund;         // Nominal refund
$pesanan->status_pembayaran;     // Should be 'refunded'

# Check notifications dikirim
$notif = App\Models\UserNotification::where('reference_id', 123)->get();
$notif->count();                 // Should be 3 (admin, client, korlap)
$notif->pluck('role');           // ['admin', 'client', 'lapangan']

# Find unread notifications
App\Models\UserNotification::where('user_id', auth()->id())
    ->where('is_read', false)
    ->count();

# Mark all as read
App\Models\UserNotification::where('user_id', auth()->id())
    ->update(['is_read' => true]);
```

---

## 📊 Status Flow After Refund

```
BEFORE:
status_pembayaran = 'dp_paid' atau 'fully_paid'
status_booking    = 'pending' atau 'approved_dp'
status_pemesanan  = 'confirmed'

          ↓ processRefund()

AFTER:
status_pembayaran = 'refunded'        ← Changed
status_booking    = 'cancelled'       ← Changed
status_pemesanan  = 'canceled'        ← Changed
jumlah_refund     = 4000000           ← Set
alasan_pembatalan = 'Alasan refund'   ← Set
dibatalkan_at     = now()             ← Set
```

---

## 🚨 Validation Rules

```php
// Refund request validation
'penalty_percent' => 'required|integer|min:0|max:100',
'alasan_refund'   => 'nullable|string|max:500',

// Only allowed if:
// - status_pembayaran IN ('dp_paid', 'fully_paid')
// - status_pemesanan != 'canceled'
// - invoice.dp_dibayar > 0
```

---

## 📝 Logging & Audit

Setiap refund di-log ke `storage/logs/laravel.log`:

```
[2024-06-04 10:30:45] local.INFO: Refund processed {
  "pesanan_id": 123,
  "dp_amount": 5000000,
  "penalty_percent": 20,
  "final_refund": 4000000,
  "processed_by": 1,
  "timestamp": "2024-06-04T10:30:45Z"
}
```

---

## ✨ Best Practices

1. **Selalu gunakan transaction** - RefundService sudah wrap dalam DB::transaction()
2. **Validasi input** - Always validate penalty_percent (0-100)
3. **Provide reason** - Selalu sertakan alasan_refund untuk audit trail
4. **Test preview dulu** - Call getRefundPreview() sebelum processRefund()
5. **Check eligible** - Pastikan pesanan memiliki DP sebelum refund
6. **Monitor notifications** - Cek UserNotification table untuk memastikan dikirim

---

## 💬 Common Questions

**Q: Gimana kalau korlap tidak ada (belum ditugaskan)?**  
A: Notifikasi hanya dikirim ke admin dan client saja. Korlap skip otomatis.

**Q: Bisa customize message notifikasi?**  
A: Ya! Parameter `message` bisa berisi pesan custom, atau gunakan `sprintf()` untuk dynamic content.

**Q: Bisa kirim notifikasi tanpa refund?**  
A: Ya! Gunakan `sendNotification()` untuk notifikasi standalone, tanpa `processRefund()`.

**Q: Toast notification hilang setelah refresh?**  
A: Itu normal. Toast hanya untuk notifikasi real-time. Notifikasi permanent tersimpan di `user_notifications` table.

**Q: Bisa ubah priority setelah notifikasi dikirim?**  
A: Tidak, update UserNotification.priority di database saja jika perlu.

---

**Dokumentasi lengkap:** Lihat `REFUND_NOTIFICATION_SYSTEM.md`  
**Contoh implementation:** Lihat `RefundController.php` dan contoh di dokumentasi
