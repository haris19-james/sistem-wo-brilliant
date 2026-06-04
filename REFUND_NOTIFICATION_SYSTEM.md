# Sistem Refund & Notifikasi Multi-Role - Panduan Implementasi

Dokumentasi lengkap untuk sistem refund DP otomatis dan notifikasi real-time ke Admin, Client, dan Korlap.

---

## 📋 Daftar Isi
1. [Overview Sistem](#overview)
2. [Logika Refund DP](#logika-refund)
3. [Sistem Notifikasi Multi-Role](#notifikasi-multi-role)
4. [Implementasi di Controller](#implementasi-controller)
5. [API Endpoints](#api-endpoints)
6. [Frontend Integration](#frontend)
7. [Testing & Verification](#testing)

---

## Overview Sistem

Sistem ini menangani dua komponen utama yang terintegrasi:

### 1. **Logika Refund DP** (`RefundService`)
- Menghitung pengembalian DP otomatis dengan potongan penalti
- Formula: `finalRefund = dpAmount - (dpAmount * (penaltyPercent / 100))`
- Update status booking menjadi `refunded` dan `cancelled`
- Simpan nominal refund ke database

### 2. **Notifikasi Multi-Role** (`NotificationCenterService::sendNotification()`)
- Mengirim notifikasi ke 3 role sekaligus: **Admin, Client, Korlap**
- Setiap notifikasi disimpan di tabel `UserNotification`
- Terintegrasi dengan sistem polling real-time
- Support multi-channel: toast, panel, badge count

---

## Logika Refund

### RefundService::processRefund()

**Signature:**
```php
public function processRefund(
    int $pesananId, 
    int $penaltyPercent = 0, 
    ?string $alasanRefund = null
): array
```

**Proses:**
1. Validasi pesanan status pembayaran (harus `dp_paid` atau `fully_paid`)
2. Ambil nilai DP dari invoice terkait
3. Hitung potongan: `penaltyAmount = dpAmount * (penaltyPercent / 100)`
4. Hitung refund final: `finalRefund = dpAmount - penaltyAmount`
5. Update status pesanan → `refunded`, `cancelled`, `canceled`
6. Update invoice status → `Refund`
7. **Otomatis panggil** `sendNotification()` ke Admin, Client, Korlap
8. Log transaction untuk audit trail

**Response Format:**
```json
{
  "success": true,
  "message": "Refund berhasil diproses dan notifikasi telah dikirim ke semua pihak",
  "data": {
    "pesanan_id": 123,
    "pesanan_number": "WO-2024-001",
    "client_name": "John Doe",
    "client_id": 5,
    "korlap_id": 8,
    "admin_id": 1,
    "dp_amount": 5000000,
    "penalty_percent": 20,
    "penalty_amount": 1000000,
    "final_refund": 4000000,
    "acara_tanggal": "2024-12-25",
    "alasan": "Client meminta pembatalan"
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Gagal memproses refund: Pesanan belum melakukan pembayaran DP...",
  "error": "..."
}
```

---

## Notifikasi Multi-Role

### NotificationCenterService::sendNotification()

**Signature:**
```php
public function sendNotification(
    int $bookingId,
    string $eventType,
    string $message,
    array $targetRoles = ['admin', 'client', 'korlap'],
    ?string $linkRedirect = null,
    string $priority = 'normal',
    ?array $metadata = null
): array
```

**Parameters:**
| Param | Type | Required | Default | Deskripsi |
|-------|------|----------|---------|-----------|
| `bookingId` | int | ✅ | - | ID pesanan/booking |
| `eventType` | string | ✅ | - | Tipe event (refund_processed, booking_confirmed, dll) |
| `message` | string | ✅ | - | Pesan notifikasi |
| `targetRoles` | array | ❌ | ['admin', 'client', 'korlap'] | Role yang menerima notifikasi |
| `linkRedirect` | string | ❌ | null | URL untuk link aksi di notifikasi |
| `priority` | string | ❌ | 'normal' | 'normal' atau 'urgent' (high) |
| `metadata` | array | ❌ | null | Data tambahan (booking_id, final_refund, dll) |

**Response:**
```json
{
  "success": true,
  "notifications_sent": 3,
  "target_roles": ["admin", "client", "korlap"],
  "created_notifications": [
    {
      "id": 1001,
      "user_id": 1,
      "role": "admin",
      "message": "Refund DP untuk booking WO-2024-001 sebesar Rp 4.000.000 telah diproses.",
      "is_read": false,
      "category": "payment",
      "priority": "high",
      "created_at": "2024-06-04T10:30:00Z"
    },
    // ... client dan korlap notifications juga dikirim
  ]
}
```

**Target Roles Behavior:**
| Role | Recipient | Deskripsi |
|------|-----------|-----------|
| `admin` | Semua user dengan role = 'admin' | Notifikasi broadcast ke admin panel |
| `client` | User pemilik booking | Hanya client yang membuat booking |
| `korlap` | User dengan id = korlap_id | Korlap yang ditugaskan ke booking |

---

## Implementasi di Controller

### Contoh 1: Memanggil Refund di BookingController

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Models\Pesanan;
use App\Services\RefundService;
use Illuminate\Http\Request;

class AdminPesananController extends Controller
{
    protected $refundService;

    public function __construct(RefundService $refundService)
    {
        $this->refundService = $refundService;
    }

    /**
     * Proses pembatalan dengan refund otomatis
     * 
     * POST /admin/booking/{pesanan}/cancel-with-refund
     */
    public function cancelWithRefund(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'penalty_percent' => 'required|integer|min:0|max:100',
            'alasan' => 'nullable|string|max:500',
        ]);

        // Gunakan RefundService untuk process refund
        $result = $this->refundService->processRefund(
            pesananId: $pesanan->id,
            penaltyPercent: $validated['penalty_percent'],
            alasanRefund: $validated['alasan']
        );

        if ($result['success']) {
            return redirect()->back()
                ->with('success', 'Refund berhasil diproses. Notifikasi sudah dikirim ke semua pihak.')
                ->with('refund_data', $result['data']);
        } else {
            return redirect()->back()
                ->with('error', $result['message']);
        }
    }

    /**
     * Preview refund sebelum proses
     * 
     * GET /admin/booking/{pesanan}/refund-preview
     */
    public function refundPreview(Pesanan $pesanan, Request $request)
    {
        $penaltyPercent = (int) $request->query('penalty', 0);
        $preview = $this->refundService->getRefundPreview($pesanan->id, $penaltyPercent);

        return response()->json($preview);
    }
}
```

### Contoh 2: Memanggil sendNotification() Manual

```php
<?php

namespace App\Http\Controllers;

use App\Services\NotificationCenterService;
use App\Models\Pesanan;

class PaymentController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationCenterService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Ketika pembayaran DP dikonfirmasi
     */
    public function confirmPaymentDP(Pesanan $pesanan)
    {
        // ... process payment ...

        // Kirim notifikasi ke Admin, Client, Korlap
        $this->notificationService->sendNotification(
            bookingId: $pesanan->id,
            eventType: 'payment_approved',
            message: sprintf(
                'Pembayaran DP untuk booking "%s" (%s) sebesar Rp %s berhasil dikonfirmasi',
                $pesanan->nomor_pesanan,
                $pesanan->nama_pasangan,
                number_format($invoice->dp_dibayar, 0, ',', '.')
            ),
            targetRoles: ['admin', 'client', 'korlap'],
            linkRedirect: route('customer.pesanan.show', $pesanan->id),
            priority: 'normal',
            metadata: [
                'booking_id' => $pesanan->id,
                'payment_amount' => $invoice->dp_dibayar,
                'payment_type' => 'dp',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran DP dikonfirmasi dan notifikasi dikirim',
        ]);
    }

    /**
     * Ketika booking dikonfirmasi/approved
     */
    public function approveBooking(Pesanan $pesanan)
    {
        $pesanan->update(['status_booking' => 'approved_dp']);

        // Kirim notifikasi multi-role
        $this->notificationService->sendNotification(
            bookingId: $pesanan->id,
            eventType: 'booking_confirmed',
            message: sprintf(
                'Booking "%s" untuk %s pada %s telah dikonfirmasi. Akses jadwal meeting tersedia.',
                $pesanan->nomor_pesanan,
                $pesanan->nama_pasangan,
                $pesanan->tanggal_acara->format('d M Y')
            ),
            targetRoles: ['admin', 'client', 'korlap'],
            linkRedirect: route('customer.pesanan.show', $pesanan->id),
            priority: 'high',
            metadata: [
                'booking_id' => $pesanan->id,
                'booking_date' => $pesanan->tanggal_acara,
            ]
        );

        return redirect()->back()->with('success', 'Booking dikonfirmasi');
    }
}
```

### Contoh 3: Service-to-Service Integration

```php
<?php

namespace App\Services;

use App\Models\Pesanan;

class BookingService
{
    protected $notificationService;
    protected $refundService;

    public function __construct(
        NotificationCenterService $notificationService,
        RefundService $refundService
    ) {
        $this->notificationService = $notificationService;
        $this->refundService = $refundService;
    }

    /**
     * Complete booking workflow dengan notifikasi otomatis
     */
    public function completeBookingWithNotifications(Pesanan $pesanan, array $data)
    {
        // 1. Update booking status
        $pesanan->update([
            'status_pemesanan' => 'confirmed',
            'status_booking' => 'approved_lunas',
        ]);

        // 2. Kirim notifikasi multi-role
        $this->notificationService->sendNotification(
            bookingId: $pesanan->id,
            eventType: 'booking_completed',
            message: "Booking {$pesanan->nomor_pesanan} siap untuk dijalankan",
            targetRoles: ['admin', 'client', 'korlap'],
            priority: 'high'
        );

        // 3. Log event
        activity()
            ->performedOn($pesanan)
            ->log('Booking completed and notifications sent');
    }

    /**
     * Cancel booking dengan refund dan notifikasi
     */
    public function cancelBookingWithRefund(
        Pesanan $pesanan, 
        int $penaltyPercent, 
        string $reason
    ) {
        // RefundService akan otomatis mengirim notifikasi
        $refundResult = $this->refundService->processRefund(
            pesananId: $pesanan->id,
            penaltyPercent: $penaltyPercent,
            alasanRefund: $reason
        );

        if ($refundResult['success']) {
            // Notifikasi sudah dikirim oleh RefundService
            // Anda bisa menambah notifikasi tambahan jika perlu
            
            // Contoh: Notifikasi khusus admin tentang pembatalan
            $this->notificationService->sendNotification(
                bookingId: $pesanan->id,
                eventType: 'booking_cancelled',
                message: "Booking {$pesanan->nomor_pesanan} dibatalkan dengan alasan: {$reason}",
                targetRoles: ['admin'], // Hanya admin
                priority: 'normal'
            );
        }

        return $refundResult;
    }
}
```

---

## API Endpoints

### 1. Preview Refund (Tidak ubah database)

```
GET /admin/refund/{pesanan}/preview?penalty_percent=20

Response:
{
  "success": true,
  "pesanan_id": 123,
  "booking_number": "WO-2024-001",
  "dp_amount": 5000000,
  "penalty_percent": 20,
  "penalty_amount": 1000000,
  "final_refund": 4000000
}
```

### 2. Process Refund (Ubah database + kirim notifikasi)

```
POST /admin/refund/{pesanan}/process

Body:
{
  "penalty_percent": 20,
  "alasan_refund": "Client meminta pembatalan karena kondisi mendadak"
}

Response:
{
  "success": true,
  "message": "Refund berhasil diproses dan notifikasi telah dikirim ke semua pihak",
  "data": { ... } // Lihat response format di atas
}
```

### 3. Get Refund Status

```
GET /admin/refund/{pesanan}/status

Response:
{
  "success": true,
  "booking_id": 123,
  "booking_number": "WO-2024-001",
  "is_refunded": false,
  "status_pembayaran": "fully_paid",
  "refund_amount": null,
  "alasan_pembatalan": null,
  "dibatalkan_at": null,
  "dp_dibayar": 5000000
}
```

### 4. List Eligible Bookings untuk Refund

```
GET /admin/refund/eligible

Response:
{
  "success": true,
  "total": 5,
  "data": [
    {
      "id": 123,
      "nomor_pesanan": "WO-2024-001",
      "nama_pasangan": "John & Jane Doe",
      "client_name": "John Doe",
      "tanggal_acara": "2024-12-25",
      "status_pembayaran": "dp_paid",
      "dp_dibayar": 5000000
    },
    // ... more bookings
  ]
}
```

---

## Frontend Integration

### 1. Render Notifikasi di Toast

Notifikasi otomatis ditampilkan sebagai toast menggunakan `notification-poller.js` yang sudah ada:

```html
<!-- Ada di base layout -->
<div id="notification-toast-container"></div>

<!-- Script otomatis initialize polling -->
<div data-notification-auto-poll data-poll-interval="5000"></div>

<!-- Badge untuk unread count -->
<span data-notification-badge>3</span>
```

### 2. Render di Notification Panel

```html
<!-- Notification Panel - Ada di sidebar/header -->
<div data-notification-panel>
  <div data-notification-list>
    <!-- Notifikasi di-render oleh notification-poller.js -->
  </div>
</div>
```

### 3. Admin Refund Interface

```html
<!-- admin/booking/show.blade.php -->

@if($pesanan->status_pembayaran !== 'refunded')
<div class="card border-warning">
  <div class="card-header">
    <h5>Proses Refund DP</h5>
  </div>
  <div class="card-body">
    
    <!-- Form preview & process refund -->
    <form id="refundForm" method="POST" action="{{ route('admin.refund.process', $pesanan) }}">
      @csrf
      
      <div class="form-group mb-3">
        <label for="penalty_percent">Potongan Penalti (%)</label>
        <input type="number" class="form-control" id="penalty_percent" 
               name="penalty_percent" min="0" max="100" value="0">
        <small class="text-muted">Potongan penalti untuk pembatalan booking</small>
      </div>

      <div class="form-group mb-3">
        <label for="alasan_refund">Alasan Pembatalan</label>
        <textarea class="form-control" id="alasan_refund" name="alasan_refund" 
                  rows="3" placeholder="Opsional"></textarea>
      </div>

      <!-- Preview section -->
      <div id="refundPreview" class="alert alert-info d-none">
        <h6>Preview Refund</h6>
        <table class="table table-sm mb-0">
          <tbody>
            <tr>
              <td>DP Dibayarkan:</td>
              <td><strong id="previewDpAmount">Rp 0</strong></td>
            </tr>
            <tr>
              <td>Potongan Penalti:</td>
              <td><strong id="previewPenaltyAmount" class="text-danger">Rp 0</strong></td>
            </tr>
            <tr>
              <td>Refund Final:</td>
              <td><strong id="previewFinalRefund" class="text-success">Rp 0</strong></td>
            </tr>
          </tbody>
        </table>
      </div>

      <button type="submit" class="btn btn-danger">
        Proses Refund & Kirim Notifikasi
      </button>
    </form>
  </div>
</div>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
  const penaltyInput = document.getElementById('penalty_percent');
  const previewSection = document.getElementById('refundPreview');
  const pesananId = {{ $pesanan->id }};

  penaltyInput.addEventListener('change', async function() {
    const penalty = parseInt(this.value) || 0;
    
    // Call preview API
    const response = await fetch(
      `/admin/refund/${pesananId}/preview?penalty_percent=${penalty}`
    );
    const data = await response.json();

    if (data.success) {
      // Show preview
      document.getElementById('previewDpAmount').textContent = 
        'Rp ' + new Intl.NumberFormat('id-ID').format(data.dp_amount);
      document.getElementById('previewPenaltyAmount').textContent = 
        'Rp ' + new Intl.NumberFormat('id-ID').format(data.penalty_amount);
      document.getElementById('previewFinalRefund').textContent = 
        'Rp ' + new Intl.NumberFormat('id-ID').format(data.final_refund);
      
      previewSection.classList.remove('d-none');
    }
  });

  // Handle form submission
  document.getElementById('refundForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const response = await fetch(
      `/admin/refund/${pesananId}/process`,
      {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: formData
      }
    );

    const result = await response.json();

    if (result.success) {
      alert('Refund berhasil diproses! Notifikasi sudah dikirim ke Admin, Client, dan Korlap.');
      location.reload();
    } else {
      alert('Error: ' + result.message);
    }
  });
});
</script>
```

---

## Testing & Verification

### 1. Manual Test Refund Process

```php
// Tinker atau test command
php artisan tinker

$pesanan = \App\Models\Pesanan::findOrFail(123);
$refundService = app(\App\Services\RefundService::class);

// Test processRefund
$result = $refundService->processRefund(123, 20, 'Test alasan');
dd($result);

// Verify di database
$pesanan->refresh();
echo $pesanan->status_pembayaran; // 'refunded'
echo $pesanan->jumlah_refund;      // Nilai refund final

// Verify notifications dikirim
$notifications = \App\Models\UserNotification::where('reference_id', 123)->get();
echo $notifications->count(); // Harus 3 (admin, client, korlap)
```

### 2. Verify Notifikasi Diterima Semua Role

```bash
# Check database
SELECT * FROM user_notifications 
WHERE reference_id = 123 AND reference_type = 'refund'
ORDER BY created_at DESC;

# Harus ada 3 rows:
# - 1 untuk admin
# - 1 untuk client  
# - 1 untuk korlap
```

### 3. Test Frontend Notification Toast

1. Buka admin dashboard
2. Proses refund dari booking
3. Lihat toast notification muncul di kanan atas
4. Lihat notification panel di sidebar update dengan notifikasi baru
5. Klik notification untuk redirect ke booking detail

### 4. Automated Test

```php
// tests/Feature/RefundTest.php

namespace Tests\Feature;

use App\Models\Pesanan;
use App\Models\UserNotification;
use Tests\TestCase;

class RefundTest extends TestCase
{
    public function test_refund_process_with_notification()
    {
        $pesanan = Pesanan::factory()->create(['status_pembayaran' => 'dp_paid']);
        $invoice = $pesanan->invoices()->create([
            'dp_dibayar' => 5000000,
            'total_biaya' => 10000000,
        ]);

        $response = $this->post(route('admin.refund.process', $pesanan), [
            'penalty_percent' => 20,
            'alasan_refund' => 'Test pembatalan',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('success', true);

        // Verify database update
        $pesanan->refresh();
        $this->assertEquals('refunded', $pesanan->status_pembayaran);
        $this->assertEquals('cancelled', $pesanan->status_booking);
        $this->assertEquals(4000000, $pesanan->jumlah_refund); // 5M - (5M * 20%)

        // Verify notifications dikirim ke 3 role
        $notifications = UserNotification::where('reference_id', $pesanan->id)->get();
        $this->assertCount(3, $notifications); // Admin, Client, Korlap
    }

    public function test_refund_preview()
    {
        $pesanan = Pesanan::factory()->create();
        $pesanan->invoices()->create([
            'dp_dibayar' => 5000000,
            'total_biaya' => 10000000,
        ]);

        $response = $this->get(
            route('admin.refund.preview', $pesanan) . '?penalty_percent=20'
        );

        $response->assertJsonPath('success', true);
        $response->assertJsonPath('dp_amount', 5000000);
        $response->assertJsonPath('penalty_amount', 1000000);
        $response->assertJsonPath('final_refund', 4000000);
    }
}
```

---

## Database Fields Reference

### Pesanan Model (Fillable)
```php
[
    'user_id',              // Client/Pemilik booking
    'korlap_id',            // Korlap yang ditugaskan
    'status_pembayaran',    // unpaid, dp_paid, fully_paid, refunded
    'status_booking',       // pending, approved_dp, approved_lunas, cancelled
    'status_pemesanan',     // pending, confirmed, on_progress, completed, canceled
    'jumlah_refund',        // Nominal refund yang diproses
    'alasan_pembatalan',    // Alasan refund/pembatalan
    'dibatalkan_at',        // Timestamp pembatalan
]
```

### UserNotification Model (Fields)
```php
[
    'user_id',              // Penerima notifikasi
    'role',                 // Role penerima (admin, client, lapangan)
    'message',              // Text notifikasi
    'is_read',              // Boolean status baca
    'link_redirect',        // URL aksi
    'priority',             // normal, urgent
    'category',             // booking, payment, chat, vendor, task, dll
    'reference_id',         // ID pesanan/booking
    'reference_type',       // refund, booking, payment, dll
    'created_at',
    'updated_at',
]
```

---

## Troubleshooting

### Notifikasi tidak dikirim?
1. Cek apakah `RefundService` di-inject dengan benar di controller
2. Cek apakah middleware `admin` terpasang di route
3. Lihat logs: `storage/logs/laravel.log`

### Refund amount salah?
1. Verify DP amount di database: `SELECT dp_dibayar FROM invoices WHERE pesanan_id = {id}`
2. Check formula: `penaltyAmount = dpAmount * (penaltyPercent / 100)`

### Toast notification tidak muncul di frontend?
1. Pastikan `notification-poller.js` sudah included di layout
2. Pastikan `data-notification-auto-poll` ada di HTML
3. Check browser console untuk error messages

---

## Kesimpulan

Sistem refund dan notifikasi multi-role ini memberikan:

✅ **Otomasi** - Refund dihitung dan diproses tanpa input manual  
✅ **Transparansi** - Admin, Client, dan Korlap semua tahu status refund secara real-time  
✅ **Audit Trail** - Semua transaksi tercatat dengan timestamp dan reason  
✅ **Extensible** - Mudah menambah event type atau role baru  

Implementasi lengkap siap pakai untuk production! 🚀
