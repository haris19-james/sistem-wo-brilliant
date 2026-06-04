# Fitur Rincian Refund di Dashboard Klien - Dokumentasi

## 📋 Overview

Fitur ini menampilkan rincian detail refund ketika booking dibatalkan, sehingga klien dapat memahami dengan jelas:
- Berapa DP yang dibayarkan
- Berapa nominal potongan (penalty)
- Berapa refund yang akan diterima
- Alasan perhitungan refund tersebut

---

## 🎯 Lokasi Tampilan

**Page:** Detail Pesanan Klien  
**URL:** `/customer/pesanan/detail/{booking_id}`  
**Kondisi:** Hanya tampil ketika `booking_status = 'refunded'` atau `status_pembayaran = 'refunded'`

---

## 📊 Data yang Ditampilkan

### 1. **Breakdown Cards** (3 Kartu Informasi)

| Kartu | Nama | Data | Keterangan |
|-------|------|------|-----------|
| 1️⃣ | DP Dibayarkan | `Rp X.XXX.XXX` | Total DP yang dibayarkan dari table `invoices.dp_dibayar` |
| 2️⃣ | Potongan | `Rp X.XXX.XXX` | Nominal potongan = DP - Final Refund |
| 3️⃣ | Refund Diterima | `Rp X.XXX.XXX` | Final refund dari `pesanans.jumlah_refund` |

### 2. **Persentase Potongan**
```
Persentase Potongan = (Potongan Amount / DP Amount) × 100
Contoh: (1.000.000 / 5.000.000) × 100 = 20%
```

### 3. **Status Message**

**Jika Refund = 0 (No Refund):**
```
⚠️ Maaf, DP Tidak Dapat Dikembalikan

Berdasarkan kebijakan pembatalan yang berlaku, DP tidak dapat dikembalikan 
sesuai dengan ketentuan yang telah disepakati saat booking.
```

**Jika Refund > 0 (Refund Tersedia):**
```
✓ Refund Sedang Diproses

Dana refund sebesar Rp X.XXX.XXX akan ditransfer ke rekening Anda 
dalam 3-5 hari kerja.
```

### 4. **Alasan Pembatalan**
```
📝 Alasan Pembatalan
[Text dari pesanans.alasan_pembatalan]
```

### 5. **Tooltip Info Icon**

Hover di icon info (?) untuk melihat penjelasan lengkap:
```
📊 Cara Perhitungan Refund

1. DP yang dibayarkan: Rp X.XXX.XXX
2. Potongan kebijakan pembatalan: X%
3. Potongan nominal: Rp X.XXX.XXX
───────────────────────────────
Refund diterima: Rp X.XXX.XXX
```

---

## 🔧 Technical Implementation

### Controller Update

File: `app/Http/Controllers/CustomerController.php`

```php
public function detailPesanan($id)
{
    // ... existing code ...
    
    // ✅ Compute refund breakdown untuk display di view
    $refundBreakdown = $this->computeRefundBreakdown($pesanan);

    return view('customer.modules.pesanan.show', [
        'activeMenu' => 'pesanan',
        'pesanan' => $pesanan,
        'agendas' => $agendas,
        'refundBreakdown' => $refundBreakdown,  // ✅ NEW
    ]);
}

private function computeRefundBreakdown(Pesanan $pesanan): array
{
    $invoice = $pesanan->invoices()->first();
    
    if (!$invoice || $pesanan->status_pembayaran !== 'refunded') {
        return [];
    }

    $dpAmount = (float) ($invoice->dp_dibayar ?? 0);
    $finalRefund = (float) ($pesanan->jumlah_refund ?? 0);
    $penaltyAmount = $dpAmount - $finalRefund;
    $penaltyPercent = $dpAmount > 0 ? round(($penaltyAmount / $dpAmount) * 100, 2) : 0;

    return [
        'dp_amount' => $dpAmount,
        'penalty_amount' => $penaltyAmount,
        'penalty_percent' => $penaltyPercent,
        'final_refund' => $finalRefund,
        'is_no_refund' => $finalRefund == 0,
        'cancellation_date' => $pesanan->dibatalkan_at,
        'cancellation_reason' => $pesanan->alasan_pembatalan,
    ];
}
```

### Component Blade

File: `resources/views/customer/components/refund-summary.blade.php`

Features:
- ✅ Responsive design (grid 1/3 columns di mobile, 3 columns di desktop)
- ✅ Colored cards (blue = DP, red = penalty, green = refund)
- ✅ Hover tooltip dengan penjelasan perhitungan
- ✅ Conditional message untuk refund = 0 atau > 0
- ✅ Dark mode friendly styling

### View Integration

File: `resources/views/customer/modules/pesanan/show.blade.php`

```blade
@if($pesanan->isDibatalkan() && $pesanan->alasan_pembatalan)
    {{-- ✅ Refund Summary Component --}}
    @include('customer.components.refund-summary', [
        'pesanan' => $pesanan,
        'invoice' => $pesanan->invoices()->first()
    ])
@endif
```

---

## 📐 Formula & Calculation

### Refund Calculation

```
Formula:
finalRefund = dpAmount - (dpAmount × (penaltyPercent / 100))

Contoh dengan 20% penalty:
- DP Dibayarkan: Rp 5.000.000
- Penalty Percent: 20%
- Penalty Amount: Rp 5.000.000 × (20/100) = Rp 1.000.000
- Final Refund: Rp 5.000.000 - Rp 1.000.000 = Rp 4.000.000
```

### Refund Rate Calculation (Display)

```
refundRate = (finalRefund / dpAmount) × 100

Contoh:
- refundRate = (4.000.000 / 5.000.000) × 100 = 80%
- Klien menerima 80% dari DP yang dibayarkan
```

---

## 📊 Database Schema Reference

### Pesanan Model
```php
'status_pembayaran'  // 'refunded' (setelah processRefund)
'jumlah_refund'      // Nominal refund yang diterima (decimal:2)
'alasan_pembatalan'  // Alasan refund/pembatalan (string)
'dibatalkan_at'      // Timestamp pembatalan (datetime)
```

### Invoice Model
```php
'dp_dibayar'         // DP yang dibayarkan (decimal:2)
'total_biaya'        // Total harga paket (decimal:2)
'sisa_pembayaran'    // Sisa tagihan (decimal:2)
```

---

## 🎨 UI/UX Features

### 1. **Color Coding**
- 🔵 **Blue (DP)** - Informasi DP yang dibayarkan
- 🔴 **Red (Penalty)** - Perhatian: potongan refund
- 🟢 **Green (Refund)** - Positif: refund yang diterima

### 2. **Tooltip Information**
- **Trigger:** Hover pada icon ℹ️
- **Content:** Breakdown perhitungan step-by-step
- **Style:** Dark background, semi-transparent
- **Position:** Top-right, dengan arrow pointer

### 3. **Responsive Layout**
```
Mobile (< 640px):
[DP Dibayarkan]
[Potongan (20%)]
[Refund Diterima]

Desktop (>= 640px):
[DP Dibayarkan] [Potongan (20%)] [Refund Diterima]
```

### 4. **Icons**
- 💰 DP Dibayarkan
- 📉 Potongan
- ✓ Refund Diterima
- 📝 Alasan Pembatalan
- ⚠️ Warning (no refund)
- ✓ Success (refund approved)

---

## 📝 Usage Examples

### Example 1: Booking dengan 20% Penalty

```
BEFORE:
Status: Pending
DP: Rp 5.000.000

AFTER CANCELLATION:
Status: Cancelled
💰 DP Dibayarkan: Rp 5.000.000
📉 Potongan (20%): Rp 1.000.000
✓ Refund Diterima: Rp 4.000.000

Message: "Dana refund sebesar Rp 4.000.000 akan ditransfer 
dalam 3-5 hari kerja."
```

### Example 2: Booking dengan No Refund (100% Penalty)

```
Status: Cancelled
💰 DP Dibayarkan: Rp 5.000.000
📉 Potongan (100%): Rp 5.000.000
✓ Refund Diterima: Rp 0

Message: "Maaf, DP tidak dapat dikembalikan sesuai ketentuan 
pembatalan yang berlaku."
```

---

## 🔒 Security & Data Integrity

### Validation

1. **Only show to booking owner**
   ```php
   if ($pesanan->user_id !== Auth::id()) {
       abort(403);
   }
   ```

2. **Only show when refunded**
   ```php
   if ($pesanan->isDibatalkan() && $pesanan->alasan_pembatalan)
   ```

3. **Validate data from database**
   - `dp_dibayar` from invoice (never user input)
   - `jumlah_refund` from pesanan (set by admin via RefundService)
   - `alasan_pembatalan` from pesanan (admin-approved)

### Calculation Integrity

All calculations are:
- ✅ Server-side computed (tidak trust client-side calculation)
- ✅ Database-backed (values from DB, tidak hardcoded)
- ✅ Audit-logged (tersimpan di `user_notifications`)
- ✅ Read-only di frontend (klien hanya bisa baca, tidak edit)

---

## 📱 Mobile Responsiveness

### Breakpoints
```
Mobile (< 640px):
- Cards stack vertically
- Full width cards
- Font size adjustable

Tablet (640px - 1024px):
- Cards in 2-column grid
- Slightly reduced padding

Desktop (> 1024px):
- Cards in 3-column grid
- Tooltip at full width (w-72)
- Optimal spacing
```

---

## 🧪 Testing Checklist

- [ ] Booking dengan refund 20% menampilkan breakdown yang benar
- [ ] Booking dengan no refund (penalty 100%) menampilkan warning
- [ ] Tooltip muncul dan menampilkan info dengan benar
- [ ] Format currency dengan thousand separator benar
- [ ] Data tidak hilang setelah refresh page
- [ ] Mobile view responsive dan readable
- [ ] Only booking owner dapat melihat refund detail
- [ ] Alasan pembatalan ditampilkan dengan benar
- [ ] Timestamp pembatalan ditampilkan dengan timezone benar

---

## 🚀 Future Enhancements

- [ ] Add PDF export untuk refund receipt
- [ ] Add timeline/history untuk refund process
- [ ] Real-time notification ketika refund berhasil ditransfer
- [ ] Add bank account details untuk refund destination
- [ ] Integration dengan payment gateway untuk auto-refund
- [ ] Add dispute/appeal mechanism untuk refund
- [ ] Email confirmation dengan refund detail

---

## 🔗 Related Components & Routes

| Component | Route | Status |
|-----------|-------|--------|
| Refund Summary | `/customer/pesanan/detail/{id}` | ✅ Done |
| Refund Service | `RefundService::processRefund()` | ✅ Done |
| Refund Notification | `sendNotification()` | ✅ Done |
| Refund Admin Panel | `/admin/refund/{id}/process` | ✅ Done |

---

## 📚 Documentation Files

- 📖 [REFUND_NOTIFICATION_SYSTEM.md](../REFUND_NOTIFICATION_SYSTEM.md) - Backend implementation
- ⚡ [REFUND_QUICK_REFERENCE.md](../REFUND_QUICK_REFERENCE.md) - Quick snippets
- 🎨 **CUSTOMER_REFUND_UI.md** - This file (UI Documentation)

---

## ❓ FAQ

**Q: Bagaimana klien tahu alasan potongan 20%?**  
A: Ada tooltip yang menjelaskan ketika hover di icon info.

**Q: Apakah refund akan ditransfer otomatis?**  
A: Saat ini manual transfer via admin. Bisa di-automate dengan payment gateway.

**Q: Bagaimana jika DB error saat compute refund?**  
A: Component gracefully fallback ke default "Rp 0" dan tidak error.

**Q: Apakah klien bisa edit nominal refund?**  
A: Tidak, semua readonly di frontend. Only admin yang bisa via RefundController.

---

## 📞 Support

Untuk pertanyaan atau issue terkait refund UI:
1. Check tooltip di halaman refund
2. Lihat documentation files di atas
3. Contact admin untuk detail lebih lanjut
