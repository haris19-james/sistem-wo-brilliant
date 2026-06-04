# 🏗️ BACKEND ARCHITECTURE DESIGN - Lapangan Management System

## DAFTAR ISI
1. [Alur Manajemen Tugas Kanban](#1-alur-manajemen-tugas-kanban)
2. [Alur Real-Time Monitoring](#2-alur-real-time-monitoring)
3. [Alur Master-Detail Acara](#3-alur-master-detail-acara)
4. [Database Schema](#4-database-schema)
5. [Controller Implementation](#5-controller-implementation)
6. [Routes Configuration](#6-routes-configuration)

---

## 1. ALUR MANAJEMEN TUGAS KANBAN

### 1.1 Konsep Alur Logis

```
┌─────────────────────────────────────────────────────────┐
│ FRONTEND: User buka halaman "Tugas" (kanban board)     │
│ 3 kolom: Belum Dikerjakan | Sedang Dikerjakan | Selesai│
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 1: User klik "Tambah Tugas"                        │
│ - Modal form terbuka                                    │
│ - Input: nama_tugas, kategori, prioritas, deadline     │
│ - Sub-tugas (checklist) bisa ditambah dinamis          │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 2: Submit form                                      │
│ - Route POST /lapangan/tugas (create)                   │
│ - Controller: TugasController@store                     │
│ - Insert ke tabel `tugas`                              │
│ - Insert sub-tugas ke `task_checklists`                │
│ - Status default: "pending" (Belum Dikerjakan)         │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 3: User drag & drop kartu ke kolom lain           │
│ - AJAX call: PATCH /lapangan/tugas/{id}/status         │
│ - Body: { status: "in_progress" atau "completed" }     │
│ - Controller: TugasController@updateStatus             │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 4: Real-time progress bar update                  │
│ - Persentase = (checked_checklists / total_checklists) │
│ - Jika semua checklist ✓: bisa set status "completed"  │
│ - AJAX call: PATCH /lapangan/tugas/{id}/checklist/{id} │
│ - Toggle checklist, sistem hitung ulang persentase     │
└─────────────────────────────────────────────────────────┘
```

### 1.2 Struktur Tabel

#### Tabel `tugas` (Main Tasks)
```
id          | integer | PK
pesanan_id  | foreign | Link ke event
user_id     | foreign | Pembuat tugas
pic_id      | foreign | PIC (orang yang ditugasi)
nama_tugas  | string  | Nama tugas
kategori    | enum    | [vendor_coordination, team_coordination, venue_setup, catering, documentation]
prioritas   | enum    | [low, medium, high, critical]
status      | enum    | [pending, in_progress, completed] → Default: pending
deadline    | datetime| Target selesai
catatan     | text    | Deskripsi detail
created_at  | datetime|
updated_at  | datetime|
```

#### Tabel `task_checklists` (Sub-Tasks/Detail Items)
```
id          | integer | PK
tugas_id    | foreign | Relation ke tugas
deskripsi   | string  | Item checklist
is_completed| boolean | Default: false
urutan      | integer | Untuk sorting
created_at  | datetime|
```

**Rumus Persentase Progress:**
```
progress_percentage = (completed_checklists / total_checklists) × 100
```

**Logika untuk Selesai:**
- User bisa set status ke "completed" HANYA jika semua checklist sudah dicentang
- Atau bisa force completed meski ada checklist yang belum (untuk tugas urgent)

---

## 2. ALUR REAL-TIME MONITORING

### 2.1 Konsep Alur Logis

```
┌─────────────────────────────────────────────────────────┐
│ FRONTEND: Buka halaman "Laporan"                        │
│ Menampilkan:                                            │
│ - Total Acara Aktif: 5                                  │
│ - Vendor Hadir: 12/15 (80%)                             │
│ - Tugas Selesai: 24/32 (75%)                            │
│ - Kendala Hari Ini: 2                                   │
│ - Progress Persiapan (Step Indicator): 80%             │
│ - Daftar Kendala: [item1, item2, ...]                 │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ BACKEND CALCULATION LOGIC                              │
│ Route: GET /lapangan/laporan                           │
│ Controller: LaporanLapanganController@index            │
│                                                         │
│ 1. Total Acara Aktif:                                  │
│    = Pesanan::where('status', '!=', 'dibatalkan')     │
│      ->where('tanggal_acara', date('Y-m-d'))          │
│      ->count()                                         │
│                                                         │
│ 2. Vendor Hadir:                                       │
│    = PesananVendor::where('status', 'hadir')->count()  │
│      / PesananVendor::count()                          │
│                                                         │
│ 3. Tugas Selesai:                                      │
│    = Tugas::where('status', 'completed')->count()      │
│      / Tugas::count()                                  │
│                                                         │
│ 4. Kendala Lapangan:                                   │
│    = LaporanLapangan::where('kondisi', '!=', 'baik')  │
│      ->where('tanggal', date('Y-m-d'))                │
│      ->get()                                           │
│                                                         │
│ 5. Progress Persiapan:                                 │
│    = ProgressPersiapan::where('pesanan_id', $id)      │
│      ->avg('progress_persen')                         │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 1: Korlap input "Kendala Lapangan"               │
│ - Form: nama_kendala, tingkat_kritis, deskripsi       │
│ - Route: POST /lapangan/laporan/kendala               │
│ - Controller: LaporanLapanganController@storeKendala  │
│ - Insert ke `laporan_lapangan` table                  │
│ - kondisi enum: [baik, perhatian, kritis]            │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ STEP 2: Data muncul di halaman Laporan               │
│ Opsi:                                                   │
│ A. Auto-refresh (polling) every 5 detik              │
│ B. Manual refresh (F5 / tombol refresh)              │
│ C. Real-time dengan WebSocket (advanced)             │
│                                                         │
│ Untuk MVP: Gunakan manual refresh + polling 30 detik  │
└─────────────────────────────────────────────────────────┘
```

### 2.2 Struktur Tabel Tambahan

#### Update Tabel `laporan_lapangan` (Enhanced)
```
id          | integer | PK
pesanan_id  | foreign | Link ke event
user_id     | foreign | Pembuat laporan (Korlap/Tim)
tanggal     | date    | Tanggal laporan
kondisi     | enum    | [baik, perhatian, kritis]
ringkasan   | text    | Ringkasan kendala
tindak_lanjut| text   | Aksi yang diambil
status_penyelesaian | enum | [pending, resolved]
created_at  | datetime|
```

#### Tabel `progress_persiapans` (Existing - Optimize)
```
id              | integer | PK
pesanan_id      | foreign |
step_name       | string  | [vendor_confirm, venue_setup, decoration, catering, ...]
progress_persen | integer | 0-100
status          | enum    | [pending, in_progress, completed]
updated_at      | datetime|
```

---

## 3. ALUR MASTER-DETAIL ACARA

### 3.1 Konsep Alur Logis

```
┌─────────────────────────────────────────────────────────┐
│ FRONTEND: Halaman "Jadwal Acara"                       │
│ Layout 3-kolom:                                         │
│ - Kolom Kiri: List Acara (tanggal, nama pasangan)     │
│ - Kolom Tengah: Detail Acara (rundown timeline)       │
│ - Kolom Kanan: Info Vendor & Tim                       │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ OPTION 1: Traditional Laravel Routing (Page Reload)   │
│                                                         │
│ User klik item di list kiri:                          │
│ <a href="/lapangan/pesanan/{{ $pesanan->id }}">       │
│   Nama Acara                                           │
│ </a>                                                    │
│                                                         │
│ Route: GET /lapangan/pesanan/{id}                     │
│ Controller: PesananController@show                    │
│ - Query pesanan details + rundown + vendor           │
│ - Return view dengan 3-kolom layout                  │
│ - Full page reload (sederhana, cocok untuk MVP)      │
│                                                         │
│ PROS: Mudah, SEO-friendly, stable                    │
│ CONS: Page reload slow, bukan real-time              │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ OPTION 2: AJAX (Best Balance)                         │
│                                                         │
│ User klik item di list kiri:                          │
│ <div onclick="loadPesananDetail({{ $id }})">          │
│   Nama Acara                                           │
│ </div>                                                  │
│                                                         │
│ JavaScript AJAX:                                       │
│ fetch('/lapangan/pesanan/{{ $id }}/detail')           │
│   .then(r => r.json())                                 │
│   .then(data => updateDetailPanel(data))             │
│                                                         │
│ Route: GET /lapangan/pesanan/{id}/detail (return JSON)│
│ Controller: PesananController@getDetail               │
│ - Query pesanan + rundown + vendor                   │
│ - Return JSON response                               │
│ - Update right panel tanpa reload                     │
│                                                         │
│ PROS: Smooth UX, data-driven, modern                 │
│ CONS: Butuh AJAX knowledge, perlu loading indicator │
└─────────────────────────────────────────────────────────┘
                           ↓
┌─────────────────────────────────────────────────────────┐
│ OPTION 3: Laravel Livewire (Recommended untuk Pemula) │
│                                                         │
│ User klik item di list kiri:                          │
│ <wire:click="selectPesanan({{ $pesanan->id }})">      │
│   Nama Acara                                           │
│ </wire:click>                                          │
│                                                         │
│ Livewire Component (@livewire)                        │
│ - Property: selectedPesananId                         │
│ - Method: selectPesanan($id)                         │
│ - Auto-update detail panel reaktif (magic!)          │
│                                                         │
│ PROS: Reactive, mudah, seperti Vue.js tapi di Laravel│
│ CONS: Perlu install Livewire (1 package)             │
│                                                         │
│ ⭐ REKOMENDASI: AJAX (middle ground antara simple &   │
│    powerful) ATAU Livewire (paling mudah)            │
└─────────────────────────────────────────────────────────┘
```

### 3.2 Perbandingan 3 Approach

| Aspek | Traditional Routing | AJAX | Livewire |
|-------|-------------------|------|----------|
| **Kompleksitas** | ⭐ Mudah | ⭐⭐ Sedang | ⭐⭐ Sedang |
| **Performance** | ❌ Slow | ✅ Cepat | ✅ Cepat |
| **Learning Curve** | ✅ Cepat | ⭐ Medium | ⭐ Medium |
| **Real-time** | ❌ Tidak | ❌ Tidak | ✅ Ya |
| **SEO** | ✅ Good | ❌ Tidak | ⚠️ Kompleks |
| **Untuk MVP** | ✅ OK | ✅ Terbaik | ✅ Alternatif |

### 3.3 Struktur Data Query

**Pesanan Detail:**
```
- id, nama_pasangan, tanggal_acara, lokasi, tema
- rundowns (timeline acara)
- pesanan_vendors (vendor terbit)
- tugas_tugas (tasks terkait)
- jadwal_meetings (rapat koordinasi)
```

---

## 4. DATABASE SCHEMA

### 4.1 Relations Diagram

```
Pesanan (Event)
    ├── Tugas (Task Kanban)
    │   └── TaskChecklist (Sub-items)
    ├── ProgressPersiapan (Step tracking)
    ├── LaporanLapangan (Field reports)
    ├── Rundown (Timeline)
    ├── PesananVendor (Vendor assignment)
    ├── JadwalMeeting (Coordination meetings)
    └── ChatMessage (Communication)

User
    ├── Tugas (sebagai pembuat/PIC)
    └── LaporanLapangan
```

### 4.2 Catatan Penting

**Sudah ada model/table:**
- ✅ `pesanans` - Events
- ✅ `tugas` - Tasks (needs checklist relation)
- ✅ `progress_persiapans` - Progress tracking
- ✅ `laporan_lapangan` - Field reports
- ✅ `rundowns` - Timelines
- ✅ `pesanan_vendor` - Vendor assignments

**Yang perlu ditambah:**
- ❌ `task_checklists` - Belum ada, perlu migration baru
- Enhance existing model relations

---

## 5. CONTROLLER IMPLEMENTATION

### Ringkas di sini, detail di file terpisah

**Controllers needed:**
1. `TugasController` - Kanban task management
2. `LaporanLapanganController` - Monitoring & reporting
3. `PesananController` - Master-detail event viewing

**Key methods:**
- TugasController: store, update, updateStatus, updateChecklist
- LaporanLapanganController: index, store, getMetrics
- PesananController: show, getDetail (JSON)

---

## 6. ROUTES CONFIGURATION

**Group:** `/lapangan/*` middleware [auth, role:lapangan]

### Tugas Routes
```
POST   /lapangan/tugas              → TugasController@store
PATCH  /lapangan/tugas/{id}/status  → TugasController@updateStatus
PATCH  /lapangan/tugas/{id}/checklist/{cid} → TugasController@updateChecklist
DELETE /lapangan/tugas/{id}         → TugasController@destroy
GET    /lapangan/tugas              → TugasController@index (Kanban view)
```

### Laporan Routes
```
GET    /lapangan/laporan            → LaporanLapanganController@index
POST   /lapangan/laporan/kendala    → LaporanLapanganController@storeKendala
GET    /lapangan/laporan/metrics    → LaporanLapanganController@getMetrics (AJAX/JSON)
```

### Pesanan Routes
```
GET    /lapangan/pesanan            → PesananController@index
GET    /lapangan/pesanan/{id}       → PesananController@show
GET    /lapangan/pesanan/{id}/detail → PesananController@getDetail (AJAX/JSON)
```

---

## 📋 Ringkasan

**Tugas 3 subsistem:**

1. **Kanban Tasks**
   - Tabel: `tugas`, `task_checklists`
   - Alur: Create → Assign → Progress → Complete
   - Progress: Auto-calculated dari checklist
   - Controller: TugasController

2. **Monitoring Reports**
   - Tabel: `laporan_lapangan`, `progress_persiapans`
   - Alur: Input kendala → Metrics display
   - Metrics: Calculated on-demand
   - Controller: LaporanLapanganController

3. **Master-Detail**
   - Approach: AJAX (recommended) atau Livewire
   - Alur: Click list → Fetch detail → Display
   - No page reload, smooth UX
   - Controller: PesananController (with getDetail method)

**Next: Lihat file-file terpisah untuk kode lengkap:**
- `KANBAN_IMPLEMENTATION.md` - Detailed code
- `MONITORING_IMPLEMENTATION.md` - Detailed code
- `MASTERDETAIL_IMPLEMENTATION.md` - Detailed code

---

Last Updated: 2026-05-28
Status: Architecture Design Complete ✅
