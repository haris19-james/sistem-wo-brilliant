# 🔐 Conditional Payment Workflow - Dokumentasi Implementasi

**Tanggal:** 30 Mei 2026  
**Status:** ✅ Backend & Route Complete | UI Snippets Ready  
**Sistem:** Laravel + Eloquent + Blade + Tailwind CSS

---

## 📋 Ringkasan Fitur

Sistem **Conditional Payment Workflow** mengontrol pergerakan pesanan (booking) berdasarkan status verifikasi pembayaran Admin:

| Tahap | Status Pembayaran | Status Pemesanan | Akses Korlap | Akses Checklist |
|-------|-------------------|------------------|--------------|-----------------|
| **1. Baru Booking** | `unpaid` | `pending` | ❌ Tidak Muncul | - |
| **2. DP Diverifikasi** | `dp_paid` | `on_progress` | ✅ Terlihat | 🔒 Partial (persiapan) |
| **3. Pelunasan Diverifikasi** | `fully_paid` | `on_progress` | ✅ Terlihat | 🔓 Penuh (hari-H) |

---

## 🗄️ 1. Struktur Database

### Migration Baru (Sudah Dibuat)
**File:** `database/migrations/2026_05_30_add_payment_workflow_to_pesanans.php`

Kolom yang ditambahkan:
```sql
ALTER TABLE pesanans ADD COLUMN status_pembayaran ENUM('unpaid', 'dp_paid', 'fully_paid') DEFAULT 'unpaid';
ALTER TABLE pesanans ADD COLUMN status_pemesanan ENUM('pending', 'confirmed', 'on_progress', 'success', 'cancelled') DEFAULT 'pending';
ALTER TABLE pesanans ADD COLUMN verified_admin_id BIGINT UNSIGNED NULLABLE;
ALTER TABLE pesanans ADD COLUMN verified_by_admin_at TIMESTAMP NULLABLE;
ALTER TABLE pesanans ADD COLUMN fully_paid_by_admin_at TIMESTAMP NULLABLE;
ALTER TABLE pesanans ADD FOREIGN KEY (verified_admin_id) REFERENCES users(id) ON DELETE SET NULL;
```

### Jalankan Migration
```bash
php artisan migrate
```

---

## 📌 2. Model Eloquent (Pesanan.php)

### ✅ Sudah Diupdate:
1. **Fillable properties** - tambah kolom baru
2. **Casts** - casting untuk datetime fields
3. **Relationship** - `verifiedByAdmin()`
4. **Scopes** - untuk filter by payment status:

```php
// Filter booking visible ke Korlap
Pesanan::visibleToKorlap(auth()->id())->get();

// Filter by payment status
Pesanan::byPaymentStatus(['dp_paid', 'fully_paid'])->get();

// Filter by order status
Pesanan::byOrderStatus(['on_progress'])->get();

// Convenience scopes
Pesanan::fullyPaid()->get();
Pesanan::waitingForFullPayment()->get();
Pesanan::unpaid()->get();
```

### 5. **Accessors** - untuk display di view:
```php
$pesanan->status_pembayaran_label      // "Belum Bayar" | "DP Terverifikasi" | "Lunas Penuh"
$pesanan->status_pembayaran_badge_class // "bg-red-50 text-red-700 ..."
$pesanan->status_pemesanan_label       // "Menunggu Verifikasi" | "Dikonfirmasi" | etc.
```

---

## 🎮 3. Admin Controller - Verification Methods

### File: `app/Http/Controllers/Admin/PesananController.php`

#### Method: `verifyDP(Request $request, Pesanan $pesanan)`
Ketika Admin klik "Verifikasi DP":
- ✅ Ubah `status_pembayaran` → `'dp_paid'`
- ✅ Ubah `status_pemesanan` → `'on_progress'`
- ✅ Log admin yang verifikasi (`verified_admin_id`, `verified_by_admin_at`)
- ✅ Pesanan **MULAI MUNCUL** di dashboard Korlap
- ✅ Korlap bisa akses jadwal dan persiapan (checklist sebagian)

**Validasi:**
- Pesanan harus dalam status `unpaid` untuk bisa verifikasi DP

**Contoh kode:**
```php
public function verifyDP(Request $request, Pesanan $pesanan)
{
    if ($pesanan->status_pembayaran !== 'unpaid') {
        return redirect()->back()->with('warning', 'Sudah diproses pembayaran DP.');
    }

    \DB::transaction(function () use ($pesanan) {
        $pesanan->update([
            'status_pembayaran' => 'dp_paid',
            'status_pemesanan' => 'on_progress',
            'verified_admin_id' => auth()->id(),
            'verified_by_admin_at' => now(),
        ]);
    });

    return redirect()->back()->with('success', 'DP diverifikasi!');
}
```

#### Method: `verifyPelunasan(Request $request, Pesanan $pesanan)`
Ketika Admin klik "Verifikasi Pelunasan":
- ✅ Ubah `status_pembayaran` → `'fully_paid'`
- ✅ Log waktu verifikasi (`fully_paid_by_admin_at`)
- ✅ **UNLOCK PENUH** checklist hari-H untuk Korlap di Kanban Board
- ✅ Customer dapat notifikasi pembayaran terkonfirmasi lunas

**Validasi:**
- Pesanan harus pernah diverifikasi DP (`status_pembayaran` = `'dp_paid'`)
- Tidak bisa verifikasi pelunasan jika status pembayaran masih `'unpaid'`

**Contoh kode:**
```php
public function verifyPelunasan(Request $request, Pesanan $pesanan)
{
    if ($pesanan->status_pembayaran === 'unpaid') {
        return redirect()->back()->with('error', 'Verifikasi DP dulu!');
    }

    if ($pesanan->status_pembayaran === 'fully_paid') {
        return redirect()->back()->with('info', 'Sudah lunas.');
    }

    \DB::transaction(function () use ($pesanan) {
        $pesanan->update([
            'status_pembayaran' => 'fully_paid',
            'fully_paid_by_admin_at' => now(),
        ]);
    });

    return redirect()->back()->with('success', 'Pelunasan diverifikasi!');
}
```

---

## 🛣️ 4. Routes (Sudah Ditambahkan)

**File:** `routes/web.php` - Bagian Admin Group

```php
Route::middleware(['auth', 'admin'])->group(function () {
    // ...existing routes...
    
    // ✅ Payment Verification Routes
    Route::post('/booking/{pesanan}/verify-dp', [AdminPesananController::class, 'verifyDP'])
        ->name('admin.booking.verify_dp');
    
    Route::post('/booking/{pesanan}/verify-pelunasan', [AdminPesananController::class, 'verifyPelunasan'])
        ->name('admin.booking.verify_pelunasan');
});
```

**Contoh penggunaan di view:**
```blade
<form action="{{ route('admin.booking.verify_dp', $pesanan->id) }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="btn btn-yellow">✓ Verifikasi DP</button>
</form>

<form action="{{ route('admin.booking.verify_pelunasan', $pesanan->id) }}" method="POST" class="inline">
    @csrf
    <button type="submit" class="btn btn-green">✓ Verifikasi Pelunasan</button>
</form>
```

---

## 🎨 5. Blade Views & UI Components

### A. Customer Dashboard - Status Pembayaran
Lihat file: **CONDITIONAL_PAYMENT_BLADE_SNIPPETS.md** - Section 1

**Contoh:**
```blade
@if($pesanan->status_pembayaran === 'unpaid')
    <span class="badge badge-danger">Menunggu Pembayaran</span>
@elseif($pesanan->status_pembayaran === 'dp_paid')
    <span class="badge badge-warning">DP Terverifikasi - Menunggu Pelunasan</span>
@elseif($pesanan->status_pembayaran === 'fully_paid')
    <span class="badge badge-success">Pembayaran Lunas</span>
@endif
```

### B. Admin Booking Detail - Verification Panel
Lihat file: **CONDITIONAL_PAYMENT_BLADE_SNIPPETS.md** - Section 2

Menampilkan:
- ✅ Status pembayaran & pemesanan saat ini
- ✅ Audit trail (siapa & kapan verifikasi)
- ✅ Tombol aksi: "Verifikasi DP", "Verifikasi Pelunasan"
- ✅ Disabled state jika sudah diverifikasi

### C. Korlap Dashboard - Filtered List
Lihat file: **CONDITIONAL_PAYMENT_BLADE_SNIPPETS.md** - Section 3

**Query di Controller:**
```php
// Hanya tampilkan pesanan yang sudah diverifikasi DP/lunas
$pesanans = Pesanan::visibleToKorlap(auth()->id())
    ->with(['user', 'paket'])
    ->latest()
    ->paginate(15);
```

Pesanan dengan status `unpaid` **TIDAK MUNCUL** di dashboard Korlap!

### D. Korlap Detail Pesanan - Payment Indicator
Lihat file: **CONDITIONAL_PAYMENT_BLADE_SNIPPETS.md** - Section 4

Menampilkan info untuk Korlap:
- Jika `dp_paid`: "🔒 Checklist hari-H sebagian terbatas"
- Jika `fully_paid`: "🔓 Akses penuh ke checklist hari-H"

### E. Kanban Board - Conditional Checklist Access
Lihat file: **CONDITIONAL_PAYMENT_BLADE_SNIPPETS.md** - Section 5

**Logika:**
- Jika `status_pembayaran === 'dp_paid'` → Checklist hari-H (pagi/siang/malam) **TERBATAS** (disabled/locked)
- Jika `status_pembayaran === 'fully_paid'` → Semua checklist **BISA DIAKSES PENUH**

**Implementasi di Controller:**
```php
public function kanbanBoard(Pesanan $pesanan)
{
    // Authorize: Korlap hanya bisa akses pesanan miliknya
    $this->authorize('view', $pesanan);

    $tasksBy = $pesanan->tugas()
        ->with('checklists')
        ->get()
        ->groupBy('stage');

    // Kirim status pembayaran ke view untuk conditional rendering
    return view('lapangan.kanban', [
        'pesanan' => $pesanan,
        'tasksBy' => $tasksBy,
        'canAccessFullChecklist' => $pesanan->status_pembayaran === 'fully_paid',
    ]);
}
```

**Di Blade view:**
```blade
@if($pesanan->status_pembayaran !== 'fully_paid')
    <div class="alert alert-warning">
        🔒 Beberapa checklist terbatas sampai pelunasan diverifikasi
    </div>
@endif

@foreach($tasksBy as $stage => $tasks)
    <div class="kanban-column">
        {{-- Jika dp_paid & stage adalah morning/afternoon/evening → disable --}}
        @if($pesanan->status_pembayaran === 'dp_paid' && in_array($stage, ['morning', 'afternoon', 'evening']))
            <div class="locked">🔒 Terbuka setelah pelunasan</div>
        @else
            {{-- Render draggable tasks normal --}}
            @foreach($tasks as $task)
                <div class="task" draggable="true">...</div>
            @endforeach
        @endif
    </div>
@endforeach
```

### F. Reusable Payment Badge Component
Lihat file: **CONDITIONAL_PAYMENT_BLADE_SNIPPETS.md** - Section 6

**File:** `resources/views/components/payment-badge.blade.php`

**Penggunaan:**
```blade
<x-payment-badge :status="$pesanan->status_pembayaran" />
```

---

## 🔄 6. Flow Diagram - Conditional Payment Workflow

```
Customer membuat booking
         ↓
   ┌─────────────────────┐
   │  Status: unpaid     │
   │  Pemesanan: pending │
   │  Korlap: ❌ Tidak lihat  │
   └─────────────────────┘
         ↓
   [Admin verifikasi DP] ← Route: POST /admin/booking/{id}/verify-dp
         ↓
   ┌─────────────────────────────┐
   │  Status: dp_paid            │
   │  Pemesanan: on_progress     │
   │  Korlap: ✅ Mulai lihat       │
   │  Checklist: 🔒 Terbatas      │
   └─────────────────────────────┘
         ↓
   [Admin verifikasi Pelunasan] ← Route: POST /admin/booking/{id}/verify-pelunasan
         ↓
   ┌─────────────────────────────┐
   │  Status: fully_paid         │
   │  Pemesanan: on_progress     │
   │  Korlap: ✅ Terlihat         │
   │  Checklist: 🔓 Akses Penuh  │
   └─────────────────────────────┘
         ↓
   Event selesai → Status: success
```

---

## ✨ 7. Integrasi dengan Notifikasi (Opsional)

### Event: PaymentVerified
```php
// app/Events/PaymentVerified.php
namespace App\Events;

use App\Models\Pesanan;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PaymentVerified implements ShouldBroadcast
{
    public function __construct(
        public Pesanan $pesanan,
        public string $stage // 'dp_paid' atau 'fully_paid'
    ) {}

    public function broadcastOn(): Channel
    {
        return new PrivateChannel('pesanan.' . $this->pesanan->id);
    }
}
```

### Trigger di Admin Controller
```php
// Di verifyDP method
event(new PaymentVerified($pesanan, 'dp_paid'));

// Di verifyPelunasan method
event(new PaymentVerified($pesanan, 'fully_paid'));
```

### Notifikasi ke Customer
```php
// app/Notifications/PaymentVerifiedNotification.php
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification
{
    public function __construct(public Pesanan $pesanan, public string $stage) {}

    public function via(): array
    {
        return ['database', 'mail']; // Database + Email
    }

    public function toDatabase(): array
    {
        $message = $this->stage === 'dp_paid'
            ? 'Pembayaran DP Anda telah diverifikasi. Tim Lapangan sekarang bisa mulai persiapan.'
            : 'Pembayaran lunas! Tim Lapangan memiliki akses penuh untuk hari H event Anda.';

        return [
            'pesanan_id' => $this->pesanan->id,
            'message' => $message,
        ];
    }
}
```

### Broadcast Real-Time (JavaScript)
```javascript
// resources/js/bootstrap.js atau blade view
import Echo from 'laravel-echo';

Echo.private(`pesanan.${pesananId}`)
    .listen('PaymentVerified', (event) => {
        console.log('Pembayaran terverifikasi:', event.pesanan);
        
        // Update status badge secara real-time
        document.querySelector('.payment-status').textContent = 
            event.stage === 'dp_paid' ? 'DP Terverifikasi' : 'Lunas Penuh';
        
        // Unlock Kanban jika fully_paid
        if (event.stage === 'fully_paid') {
            document.querySelectorAll('[data-stage]').forEach(el => {
                el.classList.remove('locked');
            });
        }
    });
```

---

## 🔒 8. Authorization & Policies (Opsional)

### Protect Routes dengan Middleware
```php
Route::post('/booking/{pesanan}/verify-dp', [AdminPesananController::class, 'verifyDP'])
    ->middleware(['auth', 'admin'])
    ->name('admin.booking.verify_dp');
```

### Gate atau Policy
```php
// app/Policies/PesananPolicy.php
public function verifyPayment(User $user, Pesanan $pesanan): bool
{
    // Hanya admin yang bisa verifikasi
    return $user->role === 'admin';
}

// Di controller
$this->authorize('verifyPayment', $pesanan);
```

---

## 📝 9. Testing Checklist

### ✅ Manual Testing Steps:

**Test 1: Korlap tidak bisa lihat pesanan unpaid**
1. Login sebagai Customer → Buat booking
2. Login sebagai Korlap → Cek dashboard (pesanan tidak muncul)
3. ✅ Expected: Pesanan tidak ada di list

**Test 2: Verifikasi DP membuka akses Korlap**
1. Login sebagai Admin → Buka detail booking
2. Klik "Verifikasi DP"
3. Login sebagai Korlap → Refresh dashboard
4. ✅ Expected: Pesanan muncul dengan badge "DP Terverifikasi"

**Test 3: Checklist terbatas sebelum fully_paid**
1. Buka Kanban Board saat status `dp_paid`
2. Cek checklist pagi/siang/malam
3. ✅ Expected: Checklist ada pesan "🔒 Terbuka setelah pelunasan"

**Test 4: Unlock checklist setelah fully_paid**
1. Admin klik "Verifikasi Pelunasan"
2. Korlap refresh Kanban Board
3. ✅ Expected: Checklist pagi/siang/malam bisa di-drag/drop

**Test 5: Audit Trail**
1. Admin verifikasi pembayaran
2. Cek database: `verified_admin_id` & `verified_by_admin_at` terisi
3. ✅ Expected: Data audit trail tercatat dengan benar

---

## 🚀 10. Deployment Checklist

- [ ] Run migration: `php artisan migrate`
- [ ] Update model Pesanan dengan scopes dan accessors
- [ ] Tambah methods ke Admin PesananController
- [ ] Update routes di `routes/web.php`
- [ ] Copy Blade snippets ke view files (customer, admin, korlap)
- [ ] Test semua scenarios di testing checklist
- [ ] (Optional) Buat Event & Notification untuk real-time updates
- [ ] (Optional) Setup Laravel Echo for broadcasting payment updates
- [ ] Deploy ke production

---

## 📚 File Reference

| File | Status | Deskripsi |
|------|--------|-----------|
| `database/migrations/2026_05_30_add_payment_workflow_to_pesanans.php` | ✅ Created | Migration untuk kolom baru |
| `app/Models/Pesanan.php` | ✅ Updated | Scopes, casts, accessors, relationships |
| `app/Http/Controllers/Admin/PesananController.php` | ✅ Updated | Methods verifyDP & verifyPelunasan |
| `routes/web.php` | ✅ Updated | Routes untuk verification endpoints |
| `CONDITIONAL_PAYMENT_BLADE_SNIPPETS.md` | ✅ Created | Blade snippets siap pakai |
| `CONDITIONAL_PAYMENT_IMPLEMENTATION_GUIDE.md` | ✅ Created | Dokumentasi ini |

---

**Dokumentasi Lengkap Selesai!** ✨  
Silakan mereferensi dokumentasi ini untuk integrasi dengan view files Anda.

