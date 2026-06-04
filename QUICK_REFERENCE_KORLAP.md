# 🌸 QUICK REFERENCE - KORLAP BOOKING SYSTEM

## NAMED ROUTES (Gunakan di link/form)

```blade
{{-- Daftar pemesanan --}}
route('lapangan.pesanan.index')  → /lapangan/pesanan

{{-- Detail acara --}}
route('lapangan.pesanan.show', $pesanan)  → /lapangan/pesanan/{id}

{{-- Update vendor status (AJAX) --}}
route('lapangan.pesanan.vendor-status', $pesanan)  → /lapangan/pesanan/{id}/vendor-status

{{-- Update progress (form) --}}
route('lapangan.pesanan.progress', $pesanan)  → /lapangan/pesanan/{id}/progress
```

---

## CONTROLLER METHODS

### 1️⃣ INDEX - Daftar Pemesanan Korlap

```php
// Hanya pesanan dengan korlap_id = auth()->id()
Pesanan::with(['user', 'paket', 'progress', 'vendors'])
    ->where('korlap_id', auth()->id())
    ->whereNotIn('status', ['Dibatalkan'])
    ->orderBy('tanggal_acara')
    ->paginate(12)
```

**Query Result:**
- ✅ Eager load 4 relasi
- ✅ Filter by Korlap
- ✅ Exclude dibatalkan
- ✅ Sort by date

---

### 2️⃣ SHOW - Detail Acara & Vendor Terplot

```php
// Load semua relasi untuk detail page
$pesanan->load([
    'user',                    // Customer
    'paket',                   // Paket WO
    'progress',               // Progress persiapan
    'rundowns',               // RUNDOWN ACARA
    'jadwalMeetings',         // Meeting schedule
    'invoices',               // Payment
    'laporanLapangans.user',  // LAPORAN SINGKAT / LOGS
    'vendors',                // VENDOR HARI INI (with pivot status)
]);

// Load tasks (tugas lapangan)
$tasks = Tugas::where('pesanan_id', $pesanan->id)
    ->with(['user', 'pic', 'checklists'])
    ->orderBy('deadline')
    ->get();
```

**Tampilkan di View:**
- ✅ Vendor Hari Ini (section)
- ✅ Status buttons (Belum Hadir/Perjalanan/Hadir)
- ✅ Rundown acara
- ✅ Laporan lapangan (auto-updated)

---

### 3️⃣ UPDATE VENDOR STATUS - Main Method

```php
// POST /lapangan/pesanan/{pesanan}/vendor-status
// Body: { vendor_id, status }

1. Check: korlap_id === auth()->id() ✅
2. Validate: vendor ditugaskan untuk pesanan ✅
3. Update: pesanan_vendor.status = "Hadir" ✅
4. Auto-Log: jika "Hadir" → create laporan_lapangans ✅
5. Return: JSON response dengan success flag
```

---

## BLADE TEMPLATES

### Component: Vendor Card

```blade
@foreach($pesanan->vendors as $vendor)
    {{-- Status badge + buttons --}}
    <div class="p-4 rounded-xl">
        <p>{{ $vendor->nama_vendor }}</p>
        <p>{{ $vendor->kategori }}</p>
        
        {{-- 3 buttons: Belum Hadir | Perjalanan | Hadir --}}
        @foreach(['Belum Hadir', 'Perjalanan', 'Hadir'] as $status)
            <button class="update-vendor-status"
                data-vendor-id="{{ $vendor->id }}"
                data-status="{{ $status }}"
                data-pesanan-id="{{ $pesanan->id }}">
                {{ $status }}
            </button>
        @endforeach
    </div>
@endforeach
```

---

## JAVASCRIPT - AJAX HANDLER

```javascript
// Klik vendor status button
document.querySelectorAll('.update-vendor-status').forEach(btn => {
    btn.addEventListener('click', async function() {
        const vendorId = this.dataset.vendorId;
        const status = this.dataset.status;
        const pesananId = this.dataset.pesananId;

        const response = await fetch(`/lapangan/pesanan/${pesananId}/vendor-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },
            body: JSON.stringify({
                vendor_id: vendorId,
                status: status
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Show toast: "Status vendor berhasil diperbarui"
            // Reload page jika status "Hadir"
        }
    });
});
```

---

## DATABASE SCHEMA

### Table: pesanan_vendor (Pivot)
```sql
CREATE TABLE pesanan_vendor (
    id BIGINT PRIMARY KEY,
    pesanan_id BIGINT,              -- FK ke pesanans
    vendor_id BIGINT,               -- FK ke vendors
    waktu_setup TIME,               -- Setup time
    status ENUM(                     -- ← PENTING!
        'Belum Hadir',
        'Perjalanan',
        'Hadir'
    ) DEFAULT 'Belum Hadir',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## KEY RELATIONSHIPS

```
PESANAN (1) ──── (Many) VENDOR
    ↓
    └─ Korlap (User)        ← korlap_id field
    └─ Rundowns             ← Jadwal kegiatan
    └─ Tasks                ← Tugas lapangan
    └─ LaporanLapangans     ← Auto-logs
    └─ Vendors (via pivot)  ← Status kehadiran
```

---

## STATUS FLOW

```
Belum Hadir  →  Perjalanan  →  Hadir
     ↓              ↓             ↓
  Tunggu      Sedang Jalan   Tiba ✅
             (di jalan raya)  Auto-Log
```

**Auto-Log Entry (saat "Hadir"):**
```
Tanggal: 2026-05-30
Jam: 10.45
Ringkasan: "10.45 - Barang MUA Gloria Hadir"
Kondisi: "Baik"
```

---

## ERROR HANDLING

| Kode | Kesalahan | Solusi |
|------|-----------|--------|
| 403 | Unauthorized | Korlap bukan yang ditugaskan |
| 422 | Vendor tidak tertugas | Vendor tidak di pivot table |
| 500 | DB error | Check logs di `storage/logs` |

---

## TESTING CHECKLIST

- [ ] Login sebagai Korlap
- [ ] Index: Hanya lihat pesanan dengan korlap_id saya
- [ ] Show: Lihat detail acara & vendor
- [ ] Update status: Klik "Perjalanan" → button highlight berubah
- [ ] Auto-log: Update ke "Hadir" → auto-create laporan
- [ ] Authorization: Coba akses pesanan Korlap lain → 403
- [ ] Toast notification: Muncul pesan sukses/error

---

## QUERY OPTIMIZATION

✅ **Eager Loading** (4 queries, bukan N+1)
```php
->with(['user', 'paket', 'progress', 'vendors'])
```

✅ **Indexed Fields**
- `pesanans.korlap_id` (indexed)
- `pesanans.status` (indexed)

✅ **Pagination**
- 12 items per page
- Prevents huge query results

---

## FILE LOCATIONS

```
📁 PROJECT
├── app/
│   ├── Http/Controllers/Lapangan/
│   │   └── PesananController.php      ← index, show, updateVendorStatus
│   └── Models/
│       ├── Pesanan.php                ← vendors(), korlap() relationships
│       ├── Vendor.php                 ← pesanans() relationship
│       └── LaporanLapangan.php        ← auto-log model
├── routes/
│   └── web.php                        ← named routes
└── resources/views/lapangan/modules/pesanan/
    ├── index.blade.php                ← daftar pemesanan
    └── show.blade.php                 ← detail + vendor card + logs
```

---

## QUICK COMMANDS

```bash
# Test routes
php artisan route:list | grep lapangan

# Check Korlap assignment
php artisan tinker
>>> Pesanan::find(1)->korlap_id;

# Check vendor in pivottable
>>> Pesanan::find(1)->vendors()->pluck('nama_vendor');

# Check logs
>>> LaporanLapangan::where('pesanan_id', 1)->latest()->get();

# Clear cache
php artisan view:clear
php artisan cache:clear
```

---

## STATUS COLORS (Tailwind)

| Status | Warna | Kelas |
|--------|-------|-------|
| Belum Hadir | Gray | `bg-gray-200 text-gray-800` |
| Perjalanan | Amber | `bg-amber-200 text-amber-800` |
| Hadir | Green | `bg-green-200 text-green-800` |

---

## TIMESTAMP LOGGING

```
Format: "H.i" → "10.45"
Contoh: "10.45 - Barang MUA Gloria Hadir"

Jika:
- 10:30:00 AM → "10.30"
- 02:45:30 PM → "14.45"
```

---

## 📋 ALUR LENGKAP

```
1. ADMIN:
   Buat pesanan → Assign vendor → Set Korlap
   
2. KORLAP Login:
   /lapangan/pesanan → Lihat daftar pesanan
   
3. KORLAP Klik Detail:
   /lapangan/pesanan/{id} → Lihat vendor + rundown
   
4. KORLAP Update Status:
   Klik "Perjalanan" → AJAX → Update DB → Auto-log
   
5. UI Update:
   Toast notification → Reload (jika "Hadir")
   
6. LOGS:
   "10.45 - Vendor Name Hadir" di LAPORAN SINGKAT
```

---

## 💡 PRO TIPS

1. **Optimistic UI**: Update button warna sebelum response
2. **Auto-reload**: Reload page setelah "Hadir" untuk update logs
3. **CSRF token**: Jangan lupa di AJAX header
4. **Error logging**: Check `storage/logs/laravel.log`
5. **Mobile UX**: Buttons > 44px height untuk touch-friendly

---

**Last Updated:** 2026-05-30  
**Version:** 1.0  
**Status:** ✅ Production Ready

