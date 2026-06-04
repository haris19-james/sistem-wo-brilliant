# 🌸 VISUAL FLOWCHART - KORLAP BOOKING SYSTEM

## ALUR 1: ADMIN SETUP

```
┌──────────────────────────────────────────────────────────┐
│                     ADMIN PORTAL                          │
└──────────────────────────────────────────────────────────┘
                            ↓
        ┌───────────────────────────────────┐
        │  1. Create Pesanan (Pemesanan)    │
        │     - Customer name               │
        │     - Tanggal acara               │
        │     - Lokasi                      │
        │     - Paket WO                    │
        └───────────────────────────────────┘
                            ↓
        ┌───────────────────────────────────┐
        │  2. Add Vendors (Assign vendors)  │
        │     - MUA (makeup artist)         │
        │     - Catering                    │
        │     - Dekorasi                    │
        │     - Fotografer                  │
        │     - dll                         │
        └───────────────────────────────────┘
                    Tabel: pesanan_vendor
                            ↓
        ┌───────────────────────────────────┐
        │  3. Assign Korlap                 │
        │     Pilih user dengan role        │
        │     'lapangan' untuk mengawasi    │
        │     Set: pesanans.korlap_id       │
        └───────────────────────────────────┘
                   Tabel: pesanans
                            ↓
                    ✅ SIAP untuk Korlap
```

---

## ALUR 2: KORLAP - MELIHAT DAFTAR PEMESANAN

```
┌──────────────────────────────────────────────────────────┐
│                     KORLAP LOGIN                          │
│            /lapangan (tahap persiapan)                    │
└──────────────────────────────────────────────────────────┘
                            ↓
                ┌───────────────────┐
                │ Click "Pemesanan" │
                └───────────────────┘
                            ↓
        ┌───────────────────────────────────────────┐
        │   Route: /lapangan/pesanan                │
        │   Method: GET                             │
        │   Controller: index()                     │
        └───────────────────────────────────────────┘
                            ↓
        ┌───────────────────────────────────────────┐
        │   QUERY: Pesanan::with([...])             │
        │          ->where('korlap_id', auth()->id) │
        └───────────────────────────────────────────┘
                            ↓
        ┌────────────────────────────────────────────────────┐
        │  📋 DAFTAR PESANAN (hanya milik Korlap ini)       │
        │                                                    │
        │  • Pesanan #001 - Pernikahan Budi & Ani          │
        │    Tanggal: 30 Mei 2026                          │
        │    Lokasi: Gedung Sari                           │
        │    Status: ✅ Sedang Berlangsung                 │
        │                                                    │
        │  • Pesanan #002 - Pernikahan Rudi & Siti         │
        │    Tanggal: 31 Mei 2026                          │
        │    Lokasi: Ballroom Permata                      │
        │    Status: ⏳ Menunggu                            │
        │                                                    │
        │  [Cari: ____] [Filter: Semua ▼]                  │
        └────────────────────────────────────────────────────┘
                            ↓
                ┌───────────────────┐
                │ Click "Detail ➜"  │
                └───────────────────┘
```

---

## ALUR 3: KORLAP - MELIHAT DETAIL ACARA & VENDOR

```
┌──────────────────────────────────────────────────────────┐
│        Route: /lapangan/pesanan/{pesanan_id}             │
│        Method: GET                                       │
│        Controller: show()                                │
└──────────────────────────────────────────────────────────┘
                            ↓
        ┌───────────────────────────────────────────┐
        │ QUERY: $pesanan->load([                   │
        │   'user', 'paket', 'progress',           │
        │   'rundowns', 'jadwalMeetings',          │
        │   'invoices', 'laporanLapangans.user',   │
        │   'vendors' ← WITH PIVOT DATA            │
        │ ])                                        │
        └───────────────────────────────────────────┘
                            ↓
        ┌────────────────────────────────────────────────────┐
        │  📄 DETAIL ACARA                                   │
        │                                                    │
        │  ┌─────────────────────────────────────────────┐  │
        │  │ Customer: Budi & Ani                        │  │
        │  │ Paket: Paket Gold (50 Undangan)            │  │
        │  │ Tanggal: 30 Mei 2026                       │  │
        │  │ Jam: 17:00 WIB                             │  │
        │  │ Lokasi: Gedung Sari, Jl. Merdeka No. 42   │  │
        │  └─────────────────────────────────────────────┘  │
        │                                                    │
        │  ┌─────────────────────────────────────────────┐  │
        │  │ 📊 PROGRESS PERSIAPAN: 75%                 │  │
        │  │ ████████████████░░ 75/100                  │  │
        │  │                                             │  │
        │  │ Venue: ✅ Selesai                          │  │
        │  │ Makeup: ✅ Selesai                         │  │
        │  │ Catering: ⏳ Proses                         │  │
        │  │ Dekorasi: ⏳ Proses                         │  │
        │  │ Dokumentasi: ⏳ Proses                      │  │
        │  └─────────────────────────────────────────────┘  │
        │                                                    │
        │  ┌─────────────────────────────────────────────┐  │
        │  │ 🕐 RUNDOWN ACARA                           │  │
        │  │                                             │  │
        │  │ PEMBERKATAN:                               │  │
        │  │  17:00–17:30 · Tiba tamu                   │  │
        │  │  17:30–18:00 · Upacara pemberkatan         │  │
        │  │  18:00–18:30 · Foto-foto keluarga         │  │
        │  │                                             │  │
        │  │ PESTA:                                      │  │
        │  │  18:30–19:00 · Pembukaan & sambutan        │  │
        │  │  19:00–20:00 · Makan & minum              │  │
        │  │  20:00–21:00 · Hiburan & tarian           │  │
        │  └─────────────────────────────────────────────┘  │
        └────────────────────────────────────────────────────┘
                            ↓
        ┌─── VENDOR HARI INI ────────────────────────────┐
        │                                                │
        │ 👥 4 vendor                                   │
        │                                                │
        │ ┌────────────────────────────────────────────┐│
        │ │ MUA Gloria Pratama              ❌          ││
        │ │ Makeup & Busana                            ││
        │ │ Setup: 16:00                               ││
        │ │                                            ││
        │ │ [❌ Belum] [🚗 Perjalanan] [✅ Hadir]      ││
        │ └────────────────────────────────────────────┘│
        │                                                │
        │ ┌────────────────────────────────────────────┐│
        │ │ Catering Maju Jaya              ✅          ││
        │ │ Catering                                    ││
        │ │ Setup: 17:00                               ││
        │ │                                            ││
        │ │ [❌ Belum] [🚗 Perjalanan] [✅ Hadir]      ││
        │ └────────────────────────────────────────────┘│
        │                                                │
        │ ┌────────────────────────────────────────────┐│
        │ │ Dekorasi Bunga Indah            🚗          ││
        │ │ Dekorasi                                    ││
        │ │ Setup: 14:00                               ││
        │ │                                            ││
        │ │ [❌ Belum] [🚗 Perjalanan] [✅ Hadir]      ││
        │ └────────────────────────────────────────────┘│
        │                                                │
        │ ┌────────────────────────────────────────────┐│
        │ │ Studio Foto Abadi               ❌          ││
        │ │ Dokumentasi                                ││
        │ │ Setup: 16:30                               ││
        │ │                                            ││
        │ │ [❌ Belum] [🚗 Perjalanan] [✅ Hadir]      ││
        │ └────────────────────────────────────────────┘│
        │                                                │
        └────────────────────────────────────────────────┘
```

---

## ALUR 4: KORLAP - UPDATE VENDOR STATUS

```
┌──────────────────────────────────────────────────────────┐
│         KORLAP KLIK TOMBOL STATUS VENDOR                 │
│   (Misalnya: "Perjalanan" pada MUA Gloria)               │
└──────────────────────────────────────────────────────────┘
                            ↓
            ┌───────────────────────────┐
            │   JAVASCRIPT EVENT:       │
            │   .update-vendor-status   │
            │   click listener          │
            └───────────────────────────┘
                            ↓
        ┌──────────────────────────────────────────┐
        │  OPTIMISTIC UI UPDATE:                   │
        │  Button warna berubah sebelum response   │
        │  (Smooth user experience)                │
        └──────────────────────────────────────────┘
                            ↓
        ┌──────────────────────────────────────────┐
        │  AJAX REQUEST (Fetch API)                │
        │  POST /lapangan/pesanan/1/vendor-status  │
        │                                          │
        │  Headers:                                │
        │  - Content-Type: application/json        │
        │  - X-CSRF-TOKEN: <token>                │
        │                                          │
        │  Body:                                   │
        │  {                                       │
        │    "vendor_id": 5,                      │
        │    "status": "Perjalanan"               │
        │  }                                       │
        └──────────────────────────────────────────┘
                            ↓
        ┌──────────────────────────────────────────┐
        │  CONTROLLER: updateVendorStatus()        │
        │                                          │
        │  1️⃣ Verify Authorization:               │
        │     korlap_id === auth()->id()           │
        │                                          │
        │  2️⃣ Validate Input:                     │
        │     - vendor_id exists                   │
        │     - status in enum                     │
        │                                          │
        │  3️⃣ Check Vendor in Pesanan:            │
        │     vendors()->where('vendor_id', ...)  │
        │     ->exists()                          │
        │                                          │
        │  4️⃣ Update Pivot Table:                 │
        │     updateExistingPivot(                 │
        │       vendor_id,                        │
        │       ['status' => 'Perjalanan']        │
        │     )                                    │
        │                                          │
        │  5️⃣ IF Status = 'Hadir':                │
        │     → Auto-create LaporanLapangan       │
        │       kondisi: 'Baik'                   │
        │       ringkasan: '14.30 - MUA Gloria    │
        │                   Hadir'                │
        │                                          │
        │  6️⃣ Log to storage/logs/laravel.log     │
        │                                          │
        │  7️⃣ Return JSON:                        │
        │     {                                   │
        │       "success": true,                  │
        │       "message": "...",                 │
        │       "log": "14.30 - MUA Gloria...",  │
        │       "status": "Perjalanan"           │
        │     }                                   │
        └──────────────────────────────────────────┘
                            ↓
                    ┌─── DATABASE ───┐
                    │                │
                    │  pesanan_vendor│
                    │  ┌────────────┐│
                    │  │ pesanan_id ││
                    │  │ vendor_id  ││
                    │  │ status: ✅ ││ ← UPDATED
                    │  │ waktu_setup││
                    │  └────────────┘│
                    │                │
                    │  laporan_lapangans (if Hadir)
                    │  ┌────────────┐│
                    │  │ pesanan_id ││
                    │  │ user_id    ││
                    │  │ tanggal    ││
                    │  │ kondisi    ││
                    │  │ ringkasan  ││ ← AUTO CREATED
                    │  └────────────┘│
                    │                │
                    └────────────────┘
                            ↓
        ┌──────────────────────────────────────────┐
        │  BROWSER: Handle JSON Response           │
        │                                          │
        │  ✅ Success:                             │
        │     - Show toast notification            │
        │     - "Status vendor berhasil diubah"    │
        │     - Update UI (button highlight)       │
        │                                          │
        │     IF status = 'Hadir':                │
        │     - Wait 1.5s                         │
        │     - Reload page                       │
        │       (untuk update Laporan section)    │
        │                                          │
        │  ❌ Error:                               │
        │     - Show error message                │
        │     - Revert button UI                  │
        │     - Reload page after 2s              │
        └──────────────────────────────────────────┘
                            ↓
        ┌────────────────────────────────────────────────┐
        │  KORLAP LIHAT UPDATE:                          │
        │                                                │
        │  ✅ MUA Gloria Pratama → Status: "Perjalanan" │
        │     Button: [Belum] [🚗 Perjalanan ✓] [Hadir] │
        │                                                │
        │  📋 LAPORAN LAPANGAN (Jika status 'Hadir'):   │
        │     14.45 - MUA Gloria Hadir       [Baik]    │
        │     14.30 - Dekorasi Bunga Hadir   [Baik]    │
        │     14.15 - Catering Maju Hadir    [Baik]    │
        └────────────────────────────────────────────────┘
```

---

## ALUR 5: DATABASE TRANSACTION DETAIL

```
┌─── SEBELUM UPDATE ───────────────────────────┐
│                                              │
│  pesanan_vendor table:                       │
│  ┌──────────┬──────────┬──────────┐         │
│  │pesanan_id│vendor_id │ status   │         │
│  ├──────────┼──────────┼──────────┤         │
│  │    1     │    5     │Belum     │ ← MUA  │
│  │    1     │    6     │Hadir     │ ← Catr │
│  │    1     │    7     │Belum     │ ← Dek  │
│  │    1     │    8     │Belum     │ ← Foto │
│  └──────────┴──────────┴──────────┘         │
│                                              │
└──────────────────────────────────────────────┘
                    ↓
            Korlap klik: "Perjalanan"
            (vendor_id = 5, pesanan_id = 1)
                    ↓
┌─── UPDATE QUERY ─────────────────────────────┐
│                                              │
│  pesanan_vendor                              │
│    ->where('pesanan_id', 1)                 │
│    ->where('vendor_id', 5)                  │
│    ->update(['status' => 'Perjalanan'])    │
│                                              │
│  ↓ Result:                                   │
│                                              │
│  ┌──────────┬──────────┬──────────┐         │
│  │pesanan_id│vendor_id │ status   │         │
│  ├──────────┼──────────┼──────────┤         │
│  │    1     │    5     │Perjalanan│ ← UPD! │
│  │    1     │    6     │Hadir     │         │
│  │    1     │    7     │Belum     │         │
│  │    1     │    8     │Belum     │         │
│  └──────────┴──────────┴──────────┘         │
│                                              │
└──────────────────────────────────────────────┘
                    ↓
        IF status = 'Hadir':
        Create entry di laporan_lapangans
                    ↓
│ laporan_lapangans table:                    │
│ ┌──────────┬─────────┬────────┬──────────┐ │
│ │pesanan_id│ user_id │tanggal │ringkasan │ │
│ ├──────────┼─────────┼────────┼──────────┤ │
│ │    1     │    3    │2026-05 │14.45 -   │ │
│ │          │         │-30     │MUA Gloria│ │
│ │          │         │        │Hadir     │ │
│ └──────────┴─────────┴────────┴──────────┘ │
│              ↑ AUTO CREATED                 │
```

---

## ALUR 6: ERROR HANDLING FLOW

```
┌─────────────────────────────────────────────────────┐
│  POST /lapangan/pesanan/{pesanan}/vendor-status     │
└─────────────────────────────────────────────────────┘
                        ↓
            ┌───────────────────────┐
            │ Check Authorization   │
            └───────────────────────┘
                        ↓
        ┌───────────────────────────┐
        │ korlap_id !== auth()->id()│
        │         ↓                 │
        │     YES → 403             │ ← Error
        │     NO → Continue         │
        └───────────────────────────┘
                        ↓
            ┌───────────────────────┐
            │ Validate Input        │
            └───────────────────────┘
                        ↓
        ┌──────────────────────────────────┐
        │ vendor_id exists in vendors?     │
        │         ↓                        │
        │     NO → 404 Not Found           │ ← Error
        │     YES → Continue               │
        └──────────────────────────────────┘
                        ↓
        ┌──────────────────────────────────┐
        │ status in enum?                  │
        │ ('Belum Hadir', 'Perjalanan',   │
        │  'Hadir')                        │
        │         ↓                        │
        │     NO → 422 Invalid             │ ← Error
        │     YES → Continue               │
        └──────────────────────────────────┘
                        ↓
            ┌───────────────────────┐
            │ Verify Vendor in      │
            │ Pesanan               │
            └───────────────────────┘
                        ↓
        ┌──────────────────────────────────┐
        │ vendors()->where('vendor_id',...)│
        │ ->exists()                       │
        │         ↓                        │
        │     NO → 422 Not Assigned        │ ← Error
        │     YES → Continue               │
        └──────────────────────────────────┘
                        ↓
            ┌───────────────────────┐
            │ Update Pivot Table    │
            └───────────────────────┘
                        ↓
        ┌─── TRY-CATCH BLOCK ───┐
        │                       │
        │ Database Error?       │
        │     ↓                 │
        │  NO → Success 200     │
        │  YES → 500 Error      │ ← Error
        │        Log to file    │
        │                       │
        └───────────────────────┘
                        ↓
        ┌──────────────────────────────────┐
        │ Return JSON Response             │
        │                                  │
        │ {                                │
        │   "success": true/false,         │
        │   "message": "...",              │
        │   "error": "..." (if error),     │
        │   "log": "14.45 - Vendor Hadir"  │
        │ }                                │
        └──────────────────────────────────┘
```

---

## ALUR 7: PAGE RELOAD & LOG UPDATE

```
Status Updated to 'Hadir'
        ↓
Return success JSON
        ↓
Browser: Show toast "Berhasil"
        ↓
Wait 1.5 seconds (setTimeout)
        ↓
        ┌─────────────────────────────┐
        │ location.reload()            │
        │                              │
        │ GET /lapangan/pesanan/{id}  │
        │ → show() method              │
        └─────────────────────────────┘
        ↓
Page load again with fresh data
        ↓
        ┌─── VIEW RENDERS ───────────────┐
        │                                │
        │ 1. Vendor Status (updated)     │
        │    ✅ Status = "Hadir"         │
        │                                │
        │ 2. Laporan Section (NEW!)      │
        │    📋 14.45 - Vendor Hadir     │
        │       Kondisi: Baik             │
        │                                │
        │ 3. Rundown (same)              │
        │ 4. Tasks (same)                │
        │ 5. Meetings (same)             │
        │                                │
        └────────────────────────────────┘
        ↓
Korlap melihat log yang baru ter-update!
```

---

## ALUR 8: MULTIPLE VENDORS (Kompleks Scenario)

```
┌────────────────────────────────────────────────┐
│ PESANAN #001: 4 VENDOR TERTUGAS                │
├────────────────────────────────────────────────┤
│                                                │
│ ① MUA Gloria: Belum Hadir ❌                   │
│ ② Catering Maju: Hadir ✅        ← Auto-log   │
│ ③ Dekorasi Bunga: Perjalanan 🚗               │
│ ④ Studio Foto: Belum Hadir ❌                 │
│                                                │
├────────────────────────────────────────────────┤
│ 🕐 WAKTU BERJALAN (Hari H)                     │
├────────────────────────────────────────────────┤
│                                                │
│ 14:00 → Korlap update ③ "Perjalanan" → ✅    │
│ Laporan: "14.00 - Dekorasi Bunga Perjalanan"  │
│                                                │
│ 14:15 → Korlap update ① "Perjalanan" → ✅    │
│ Laporan: "14.15 - MUA Gloria Perjalanan"      │
│                                                │
│ 14:30 → Korlap update ③ "Hadir" → ✅ AUTO-LOG│
│ Laporan: "14.30 - Dekorasi Bunga Hadir"       │
│ (Page reload)                                 │
│                                                │
│ 14:45 → Korlap update ① "Hadir" → ✅ AUTO-LOG│
│ Laporan: "14.45 - MUA Gloria Hadir"           │
│ (Page reload)                                 │
│                                                │
│ 14:55 → Korlap update ④ "Perjalanan" → ✅   │
│ Laporan: "14.55 - Studio Foto Perjalanan"     │
│                                                │
│ 15:10 → Korlap update ④ "Hadir" → ✅ AUTO-LOG│
│ Laporan: "15.10 - Studio Foto Hadir"          │
│ (Page reload)                                 │
│                                                │
├────────────────────────────────────────────────┤
│ ✅ SEMUA VENDOR HADIR!                        │
│                                                │
│ 📋 LAPORAN SINGKAT (LOGS):                    │
│   14.00 - Dekorasi Bunga Perjalanan           │
│   14.15 - MUA Gloria Perjalanan               │
│   14.30 - Dekorasi Bunga Hadir                │
│   14.45 - MUA Gloria Hadir                    │
│   14.55 - Studio Foto Perjalanan              │
│   15.10 - Studio Foto Hadir                   │
│                                                │
└────────────────────────────────────────────────┘
```

---

## 📊 STATE DIAGRAM

```
                    ┌─────────────────┐
                    │   Status Menu   │
                    │ (Belum Hadir,   │
                    │ Perjalanan,     │
                    │ Hadir)          │
                    └─────────────────┘
                           ↓
        ┌──────────────────────────────────┐
        │  Belum Hadir                     │
        │  (default status)                │
        │  ❌ Icon                         │
        └──────────────────────────────────┘
              ↓              ↓
         Perjalanan      Hadir
              ↓              ↓
        Sedan jalan    📍 Sampai lokasi
        🚗 Icon        ✅ Icon
                       Auto-log!
                       Page reload
```

---

## 🔄 COMPLETE FLOW SUMMARY

```
ADMIN Setup
    ↓
[Pesanan] ← Assign → [Vendor] ← Assign → [Korlap]
    ↓
Korlap Login
    ↓
View Pesanan List (filter by korlap_id)
    ↓
Click Detail
    ↓
View Vendor + Status Buttons
    ↓
Click Status Button
    ↓
AJAX POST /vendor-status
    ↓
Controller: Verify + Validate + Update
    ↓
Update pesanan_vendor.status
    ↓
If "Hadir": Auto-create laporan_lapangans
    ↓
Return JSON
    ↓
Optimistic UI + Toast Notification
    ↓
If "Hadir": Reload page after 1.5s
    ↓
View updated logs in "LAPORAN SINGKAT"
    ↓
✅ COMPLETE
```

---

**Created:** 2026-05-30  
**Version:** 1.0  
**Status:** ✅ Reference Complete

