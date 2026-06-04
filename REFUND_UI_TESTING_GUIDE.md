<!-- 
  TESTING & DEMO GUIDE - Refund Summary Component

  File ini menunjukkan berbagai state/scenario refund summary UI
  untuk testing dan demo purposes.
-->

# Refund Summary Component - Testing & Demo Guide

## 🎬 Demo Scenarios

### Scenario 1: Refund dengan Potongan 20%

**Setup:**
- DP Dibayarkan: Rp 5.000.000
- Potongan: 20% = Rp 1.000.000
- Refund Diterima: Rp 4.000.000

**Expected UI:**

```
┌─────────────────────────────────────────────────────────────────┐
│ Rincian Refund DP                                    ℹ️          │
│ Booking dibatalkan pada 04 Jun 2026, 10:30                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │ 💰 DP Dibayarkan      │ 📉 Potongan (20%)    │ ✓ Refund Diterima     │
│  │ Rp 5.000.000 │  │ Rp 1.000.000 │  │ Rp 4.000.000 │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ✓ Refund Sedang Diproses                                       │
│  Dana refund sebesar Rp 4.000.000 akan ditransfer ke rekening   │
│  Anda dalam 3-5 hari kerja.                                    │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  📝 Alasan Pembatalan                                            │
│  Terjadi hal mendadak yang memaksa kami untuk membatalkan       │
│  acara pernikahan ini. Kami minta maaf atas ketidaknyamanan...  │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Kebijakan Pembatalan: Potongan 20% berlaku sesuai dengan       │
│  terms & conditions yang telah disepakati pada saat booking.    │
│  Lihat FAQ →                                                     │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘

TOOLTIP (Hover di info icon):
┌─────────────────────────────────┐
│ 📊 Cara Perhitungan Refund      │
│                                 │
│ 1. DP yang dibayarkan:          │
│    Rp 5.000.000                 │
│                                 │
│ 2. Potongan kebijakan:          │
│    20%                          │
│                                 │
│ 3. Potongan nominal:            │
│    Rp 1.000.000                 │
│ ─────────────────────────────── │
│ Refund diterima:                │
│ Rp 4.000.000                    │
│                                 │
└─────────────────────────────────┘
```

### Scenario 2: No Refund (100% Penalty)

**Setup:**
- DP Dibayarkan: Rp 5.000.000
- Potongan: 100% = Rp 5.000.000
- Refund Diterima: Rp 0

**Expected UI:**

```
┌─────────────────────────────────────────────────────────────────┐
│ Rincian Refund DP                                    ℹ️          │
│ Booking dibatalkan pada 01 Jun 2026, 14:15                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │ 💰 DP Dibayarkan      │ 📉 Potongan (100%)   │ ✓ Refund Diterima     │
│  │ Rp 5.000.000 │  │ Rp 5.000.000 │  │    Rp 0      │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ⚠️ Maaf, DP Tidak Dapat Dikembalikan                           │
│  Berdasarkan kebijakan pembatalan yang berlaku, DP tidak dapat  │
│  dikembalikan sesuai dengan ketentuan yang telah disepakati    │
│  saat booking.                                                 │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  📝 Alasan Pembatalan                                            │
│  Pembatalan kurang dari 3 hari sebelum acara sesuai kebijakan... │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### Scenario 3: Small Refund (50% Penalty)

**Setup:**
- DP Dibayarkan: Rp 10.000.000
- Potongan: 50% = Rp 5.000.000
- Refund Diterima: Rp 5.000.000

**Expected UI:**

```
┌─────────────────────────────────────────────────────────────────┐
│ Rincian Refund DP                                    ℹ️          │
│ Booking dibatalkan pada 10 Jun 2026, 09:45                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐           │
│  │ 💰 DP Dibayarkan       │ 📉 Potongan (50%)    │ ✓ Refund Diterima     │
│  │ Rp 10.000.000│  │ Rp 5.000.000 │  │ Rp 5.000.000 │           │
│  └──────────────┘  └──────────────┘  └──────────────┘           │
│                                                                  │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  ✓ Refund Sedang Diproses                                       │
│  Dana refund sebesar Rp 5.000.000 akan ditransfer ke rekening   │
│  Anda dalam 3-5 hari kerja.                                    │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## ✅ Testing Checklist

### Layout Testing
- [ ] Component renders ketika `status_pembayaran = 'refunded'`
- [ ] Component tidak render ketika status bukan 'refunded'
- [ ] Responsive di mobile (< 640px)
- [ ] Responsive di tablet (640px - 1024px)
- [ ] Responsive di desktop (> 1024px)

### Data Display Testing
- [ ] DP amount menampilkan value dari `invoice.dp_dibayar`
- [ ] Penalty amount calculated benar: `dp - finalRefund`
- [ ] Penalty percent calculated benar: `(penalty / dp) * 100`
- [ ] Final refund menampilkan value dari `pesanan.jumlah_refund`
- [ ] Tanggal pembatalan menampilkan `pesanan.dibatalkan_at`
- [ ] Alasan pembatalan menampilkan `pesanan.alasan_pembatalan`

### Formatting Testing
- [ ] Currency format dengan thousand separator (Rp X.XXX.XXX)
- [ ] Percentage format dengan 2 decimal (XX.XX%)
- [ ] Date format translated ke Bahasa Indonesia

### Conditional Logic Testing
- [ ] Jika finalRefund = 0, tampil warning message
- [ ] Jika finalRefund > 0, tampil success message
- [ ] Tooltip hanya tampil pada hover (tidak default visible)
- [ ] All cards visible dan readable

### Interactive Testing
- [ ] Hover tooltip muncul dan hilang correctly
- [ ] Tooltip content readable dan not overflow
- [ ] FAQ link di bawah berfungsi (atau placeholder aman)
- [ ] Cards tidak ada hover state yang mengganggu

### Color Testing
- [ ] Blue card untuk DP (dp_amount)
- [ ] Red card untuk penalty
- [ ] Green card untuk refund
- [ ] Green message untuk refund > 0
- [ ] Amber message untuk refund = 0

### Browser Testing
- [ ] Chrome/Chromium
- [ ] Firefox
- [ ] Safari (jika available)
- [ ] Mobile Chrome/Safari (iOS/Android)

---

## 🧪 Test Cases

### Test Case 1: Render Component
```bash
Given: Booking dengan status_pembayaran = 'refunded'
       dan alasan_pembatalan ada
When: Akses halaman detail booking
Then: Refund Summary Component ditampilkan
      dengan data yang benar
```

### Test Case 2: Calculate Refund Breakdown
```bash
Given: DP = 5.000.000, Final Refund = 4.000.000
When: Component renders
Then: Penalty = 1.000.000
      Penalty Percent = 20%
      Refund Rate = 80%
```

### Test Case 3: No Refund Message
```bash
Given: Final Refund = 0
When: Component renders
Then: Warning message tampil:
      "Maaf, DP Tidak Dapat Dikembalikan"
```

### Test Case 4: Refund Available Message
```bash
Given: Final Refund > 0
When: Component renders
Then: Success message tampil:
      "Refund Sedang Diproses"
      dengan nominal refund
```

### Test Case 5: Tooltip Information
```bash
Given: Component rendered
When: Hover on info icon
Then: Tooltip tampil dengan breakdown
      - DP amount
      - Penalty percent
      - Penalty amount
      - Final refund
```

### Test Case 6: Security - Only Owner Can View
```bash
Given: Booking milik User A
When: User B akses halaman detail
Then: 403 Forbidden (in controller level)
      atau tidak tampil refund summary (in view level)
```

---

## 📊 Database State for Testing

### Test Data Setup

```sql
-- Setup test booking
INSERT INTO pesanans (
    user_id, paket_id, nomor_pesanan, nama_pasangan, 
    tanggal_acara, jam_acara, lokasi, tema, jumlah_tamu,
    status, status_pembayaran, status_booking, status_pemesanan,
    alasan_pembatalan, jumlah_refund, dibatalkan_at, 
    verified_admin_id, verified_by_admin_at,
    created_at, updated_at
) VALUES (
    1,                                    -- user_id (client)
    1,                                    -- paket_id
    'WO-2026-001',                       -- nomor_pesanan
    'John & Jane Doe',                   -- nama_pasangan
    '2026-12-25',                        -- tanggal_acara
    '18:00',                             -- jam_acara
    'Jakarta',                           -- lokasi
    'Modern Garden',                     -- tema
    200,                                 -- jumlah_tamu
    'Dibatalkan',                        -- status
    'refunded',                          -- status_pembayaran
    'cancelled',                         -- status_booking
    'canceled',                          -- status_pemesanan
    'Terjadi hal mendadak yang memaksa pembatalan', -- alasan
    4000000,                             -- jumlah_refund (DP - penalty)
    '2026-06-04 10:30:00',              -- dibatalkan_at
    1,                                   -- verified_admin_id
    '2026-06-04 10:30:00',              -- verified_by_admin_at
    NOW(),                               -- created_at
    NOW()                                -- updated_at
);

-- Setup invoice dengan DP 5 juta
INSERT INTO invoices (
    pesanan_id, nomor_invoice, total_biaya, dp_dibayar, 
    sisa_pembayaran, status, metode_pembayaran,
    tanggal_invoice, jatuh_tempo_dp, jatuh_tempo_pelunasan,
    created_at, updated_at
) VALUES (
    [pesanan_id],                        -- pesanan_id
    'INV/2026/001',                      -- nomor_invoice
    10000000,                            -- total_biaya (10 juta)
    5000000,                             -- dp_dibayar (5 juta)
    5000000,                             -- sisa_pembayaran
    'Refund',                            -- status (updated by RefundService)
    'Transfer Bank',                     -- metode_pembayaran
    '2026-05-01',                        -- tanggal_invoice
    '2026-05-15',                        -- jatuh_tempo_dp
    '2026-12-01',                        -- jatuh_tempo_pelunasan
    NOW(),                               -- created_at
    NOW()                                -- updated_at
);
```

### SQL Query untuk Verification

```sql
-- Check refund data
SELECT 
    p.id, p.nomor_pesanan, p.nama_pasangan,
    p.status_pembayaran, p.status_booking, 
    p.jumlah_refund, p.alasan_pembatalan, p.dibatalkan_at,
    i.dp_dibayar, i.total_biaya,
    (i.dp_dibayar - p.jumlah_refund) as penalty_amount,
    ROUND(((i.dp_dibayar - p.jumlah_refund) / i.dp_dibayar * 100), 2) as penalty_percent
FROM pesanans p
LEFT JOIN invoices i ON p.id = i.pesanan_id
WHERE p.status_pembayaran = 'refunded'
AND p.user_id = 1;  -- Filter by user
```

---

## 🚀 Manual Testing Steps

1. **Setup Test Data**
   ```bash
   # Copy SQL queries above dan run di database
   ```

2. **Clear Cache** (optional)
   ```bash
   php artisan cache:clear
   php artisan view:clear
   ```

3. **Access Test Page**
   ```
   URL: http://localhost/customer/pesanan/detail/[pesanan_id]
   Login as User ID 1 (owner of booking)
   ```

4. **Verify Component**
   - [ ] Component visible
   - [ ] All data displayed correctly
   - [ ] Calculations match expectation
   - [ ] Styling looks good
   - [ ] Responsive in different screen sizes
   - [ ] Tooltip works on hover

5. **Check Browser Console**
   - [ ] No JavaScript errors
   - [ ] No CSS warnings
   - [ ] Network requests successful

6. **Test Scenarios**
   - [ ] Refund 20% case (5M DP, 1M penalty, 4M refund)
   - [ ] No refund case (5M DP, 5M penalty, 0 refund)
   - [ ] 50% refund case (10M DP, 5M penalty, 5M refund)

---

## 📝 Notes for QA

- Component is read-only di frontend (data hanya dari DB)
- Calculation dilakukan di backend (secure)
- All user data properly scoped (authorization)
- Responsive design tested di 3 breakpoints
- Color contrast memenuhi WCAG AA standard
- Tooltip accessible dengan keyboard (jika future enhancement)

---

## 🔗 Related Files

- Component: `resources/views/customer/components/refund-summary.blade.php`
- Controller: `app/Http/Controllers/CustomerController.php::detailPesanan()`
- View: `resources/views/customer/modules/pesanan/show.blade.php`
- Service: `app/Services/RefundService.php::processRefund()`
- Docs: `CUSTOMER_REFUND_UI_GUIDE.md`
