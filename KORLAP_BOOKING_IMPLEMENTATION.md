# 🌸 KORLAP BOOKING IMPLEMENTATION GUIDE

## Ringkasan Implementasi

Sistem sudah lengkap dengan logika backend untuk Korlap (Koordinator Lapangan). Dokumen ini menjelaskan flow, file yang terlibat, dan cara penggunaannya.

---

## 1. ARSITEKTUR ALUR DATA

```
┌─────────────────────────────────────────────────────────────┐
│ ADMIN                                                         │
│ - Buat pesanan (pemesanan acara)                             │
│ - Tentukan VENDOR (assign ke pesanan)                        │
│ - Tentukan KORLAP yang mengawasi                             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ KORLAP (Lapangan Coordinator)                                │
│ - Lihat daftar pemesanan yang ditugaskan (index)            │
│ - Klik "Lihat Detail" untuk melihat acara                   │
│ - Update status kehadiran VENDOR di lapangan                │
│ - Catat laporan lapangan & kondisi acara                    │
│ - Pantau progres persiapan                                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ DATABASE - TABEL PIVOT (pesanan_vendor)                     │
│ - pesanan_id → FK ke pesanans                               │
│ - vendor_id → FK ke vendors                                 │
│ - status → Enum: 'Belum Hadir', 'Perjalanan', 'Hadir'      │
│ - waktu_setup → setup time                                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 2. ROUTE DEFINITIONS (Named Routes)

File: `routes/web.php` (lines 100-104)

```php
Route::prefix('lapangan')->name('lapangan.')->group(function () {
    Route::middleware(['auth', 'lapangan'])->group(function () {
        // Daftar pemesanan untuk Korlap
        Route::get('/pesanan', [LapanganPesananController::class, 'index'])
            ->name('pesanan.index');
        
        // Detail acara & vendor terplot
        Route::get('/pesanan/{pesanan}', [LapanganPesananController::class, 'show'])
            ->name('pesanan.show');
        
        // Update status vendor Korlap
        Route::post('/pesanan/{pesanan}/vendor-status', [LapanganPesananController::class, 'updateVendorStatus'])
            ->name('pesanan.vendor-status');
        
        // Update progress persiapan
        Route::patch('/pesanan/{pesanan}/progress', [LapanganPesananController::class, 'updateProgress'])
            ->name('pesanan.progress');
    });
});
```

**Nama Route yang Digunakan:**
- `lapangan.pesanan.index` → Halaman daftar pemesanan
- `lapangan.pesanan.show` → Halaman detail acara & vendor
- `lapangan.pesanan.vendor-status` → AJAX endpoint update vendor status
- `lapangan.pesanan.progress` → Update progress persiapan

---

## 3. ELOQUENT QUERIES (Query Optimization)

### 3.1 Index Query - Hanya Pesanan Korlap yang Login

**File:** `app/Http/Controllers/Lapangan/PesananController.php` (method: `index`)

```php
public function index(Request $request)
{
    // ✅ FILTER UTAMA: Hanya ambil pesanan dengan korlap_id = auth()->id()
    $query = Pesanan::with(['user', 'paket', 'progress', 'vendors'])
        ->where('korlap_id', auth()->id())           // ← KUNCI! Filter by Korlap
        ->whereNotIn('status', ['Dibatalkan'])       // Exclude dibatalkan
        ->orderBy('tanggal_acara');                  // Sort by date

    // Filter opsional: status pesanan
    if ($request->filled('status') && $request->status !== 'semua') {
        $query->where('status', $request->status);
    } else {
        $query->aktifLapangan();  // Default: Menunggu & Sedang Berlangsung
    }

    // Search: customer name, booking number, location
    if ($request->filled('q')) {
        $q = $request->q;
        $query->where(function ($builder) use ($q) {
            $builder->where('nama_pasangan', 'like', "%{$q}%")
                ->orWhere('nomor_pesanan', 'like', "%{$q}%")
                ->orWhere('lokasi', 'like', "%{$q}%");
        });
    }

    $pesanans = $query->paginate(12)->withQueryString();

    return view('lapangan.modules.pesanan.index', [
        'activeMenu' => 'pesanan',
        'pesanans' => $pesanans,
        'filters' => $request->only(['status', 'q']),
    ]);
}
```

**Eager Loading Optimization:**
- `with(['user', 'paket', 'progress', 'vendors'])` → Mencegah N+1 query
- 4 additional queries, bukan 4 × pesanan queries

---

### 3.2 Show Query - Load Semua Relasi untuk Detail Acara

**File:** `app/Http/Controllers/Lapangan/PesananController.php` (method: `show`)

```php
public function show(Pesanan $pesanan)
{
    // ✅ Verifikasi Korlap punya akses ke pesanan ini
    if ($pesanan->korlap_id !== auth()->id()) {
        abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
    }

    // ✅ EAGER LOAD SEMUA YANG DIPERLUKAN:
    $pesanan->load([
        'user',                      // Data customer
        'paket',                     // Info paket WO
        'progress',                  // Progress persiapan
        'rundowns',                  // Rundown acara (jadwal kegiatan)
        'jadwalMeetings',           // Meeting schedule
        'invoices',                 // Payment invoices
        'laporanLapangans.user',    // Field reports (LAPORAN LAPANGAN)
        'vendors',                  // VENDOR HARI INI dengan pivot data
    ]);

    // Load tasks (tugas lapangan)
    $tasks = Tugas::where('pesanan_id', $pesanan->id)
        ->with(['user', 'pic', 'checklists'])
        ->orderBy('deadline')
        ->get();

    $timeline = PersiapanTimeline::build($pesanan);

    return view('lapangan.modules.pesanan.show', [
        'activeMenu' => 'pesanan',
        'pesanan' => $pesanan,
        'tasks' => $tasks,
        'timeline' => $timeline,
    ]);
}
```

**Relasi yang Ter-load:**
1. ✅ Rundowns - untuk "RUNDOWN ACARA" section
2. ✅ Tasks - untuk "TUGAS LAPANGAN" section  
3. ✅ Vendors - untuk "VENDOR HARI INI" section
4. ✅ LaporanLapangans - untuk "LAPORAN SINGKAT / LOGS" section

---

## 4. UPDATE VENDOR STATUS (Logika Korlap Update)

**File:** `app/Http/Controllers/Lapangan/PesananController.php` (method: `updateVendorStatus`)

```php
public function updateVendorStatus(Request $request, Pesanan $pesanan)
{
    // ✅ AUTHORIZATION: Hanya Korlap yang ditugaskan untuk pesanan ini
    if ($pesanan->korlap_id !== auth()->id()) {
        return response()->json([
            'error' => 'Anda tidak memiliki akses untuk mengubah status vendor di pesanan ini.'
        ], 403);
    }

    $validated = $request->validate([
        'vendor_id' => 'required|exists:vendors,id',
        'status' => 'required|in:Belum Hadir,Perjalanan,Hadir',  // ← Status enum
    ]);

    try {
        // ✅ VERIFIKASI: Vendor benar-benar ditugaskan untuk pesanan ini
        $vendorExists = $pesanan->vendors()
            ->where('vendor_id', $validated['vendor_id'])
            ->exists();

        if (!$vendorExists) {
            return response()->json([
                'error' => 'Vendor tidak tertugas untuk acara ini.'
            ], 422);
        }

        // ✅ UPDATE: Status vendor pada tabel pivot pesanan_vendor
        $pesanan->vendors()->updateExistingPivot(
            $validated['vendor_id'],
            ['status' => $validated['status']]
        );

        // Ambil nama vendor untuk log
        $vendor = Vendor::find($validated['vendor_id']);
        $logMessage = now()->format('H.i') . ' - ' . $vendor->nama_vendor . ' ' . $validated['status'];

        // ✅ AUTO-LOG: Jika vendor hadir, catat otomatis ke "LAPORAN LAPANGAN"
        if ($validated['status'] === 'Hadir') {
            LaporanLapangan::create([
                'pesanan_id' => $pesanan->id,
                'user_id' => auth()->id(),           // Korlap yang melaporkan
                'tanggal' => now()->toDateString(),
                'kondisi' => 'Baik',
                'ringkasan' => $logMessage,         // Contoh: "10.45 - Barang MUA Gloria Hadir"
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status vendor berhasil diperbarui.',
            'log' => $logMessage,
            'status' => $validated['status'],
        ]);
    } catch (\Exception $e) {
        \Log::error('Update vendor status error', [
            'pesanan_id' => $pesanan->id,
            'vendor_id' => $validated['vendor_id'] ?? null,
            'error' => $e->getMessage(),
        ]);

        return response()->json([
            'error' => 'Gagal mengupdate status vendor. Silakan coba lagi.'
        ], 500);
    }
}
```

**Flow:**
1. Korlap klik tombol status vendor (Belum Hadir/Perjalanan/Hadir)
2. JavaScript mengirim AJAX POST ke `/lapangan/pesanan/{id}/vendor-status`
3. Controller verifikasi authorization & vendor
4. Update tabel pivot `pesanan_vendor.status`
5. Jika status = "Hadir" → auto-create entry di `laporan_lapangans`
6. Return JSON response dengan log message

---

## 5. MODEL RELATIONSHIPS

### 5.1 Pesanan Model - Vendor Relationship

**File:** `app/Models/Pesanan.php`

```php
class Pesanan extends Model
{
    // ...

    /**
     * Pesanan bisa memiliki banyak vendor yang ditugaskan.
     * Relasi many-to-many melalui pivot table 'pesanan_vendor'
     */
    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'pesanan_vendor')
            ->withPivot(['waktu_setup', 'status'])  // ← Pivot columns
            ->withTimestamps();
    }

    /**
     * Pesanan punya satu Korlap (Field Coordinator)
     */
    public function korlap()
    {
        return $this->belongsTo(User::class, 'korlap_id');
    }

    // Helper: Check jika semua vendor sudah hadir
    public function allVendorsArrived(): bool
    {
        return $this->vendors()
            ->wherePivot('status', 'Hadir')
            ->count() === $this->vendors()->count();
    }

    // Scope untuk pesanan yang aktif di lapangan
    public function scopeAktifLapangan($query)
    {
        return $query->whereIn('status', ['Menunggu', 'Sedang Berlangsung']);
    }
}
```

### 5.2 Vendor Model - Pesanan Relationship

**File:** `app/Models/Vendor.php`

```php
class Vendor extends Model
{
    // ...

    /**
     * Vendor bisa ditugaskan ke banyak pesanan.
     * Relasi many-to-many
     */
    public function pesanans()
    {
        return $this->belongsToMany(Pesanan::class, 'pesanan_vendor')
            ->withPivot(['waktu_setup', 'status'])
            ->withTimestamps();
    }
}
```

---

## 6. BLADE VIEW - VENDOR HARI INI SECTION

**File:** `resources/views/lapangan/modules/pesanan/show.blade.php` (lines 92-132)

### Current Implementation (SUDAH ADA):

```blade
{{-- Vendor Hari Ini --}}
@if($pesanan->vendors->isNotEmpty())
<div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
    <h3 class="font-bold mb-4">Vendor Hari Ini</h3>
    <div class="space-y-3">
        @foreach($pesanan->vendors as $vendor)
        <div class="p-3 bg-gray-50 rounded-lg">
            <div class="flex items-start justify-between gap-2 mb-2">
                <div class="flex-1">
                    <p class="font-semibold text-sm">{{ $vendor->nama_vendor }}</p>
                    <p class="text-xs text-gray-500">{{ $vendor->kategori }}</p>
                    @if($vendor->pivot->waktu_setup)
                    <p class="text-xs text-gray-400 mt-1">Setup: {{ $vendor->pivot->waktu_setup }}</p>
                    @endif
                </div>
            </div>
            
            {{-- Status Buttons --}}
            <div class="flex flex-wrap gap-1">
                @php
                    $statusOptions = ['Belum Hadir', 'Perjalanan', 'Hadir'];
                    $currentStatus = $vendor->pivot->status ?? 'Belum Hadir';
                    $statusColors = [
                        'Belum Hadir' => 'bg-gray-200 text-gray-800 hover:bg-gray-300',
                        'Perjalanan' => 'bg-yellow-200 text-yellow-800 hover:bg-yellow-300',
                        'Hadir' => 'bg-green-200 text-green-800 hover:bg-green-300'
                    ];
                @endphp
                
                @foreach($statusOptions as $status)
                <button type="button" 
                    class="text-xs px-3 py-1 rounded-full font-medium transition-colors update-vendor-status 
                        {{ ($currentStatus === $status) ? $statusColors[$status] : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                    data-vendor-id="{{ $vendor->id }}"
                    data-status="{{ $status }}"
                    data-pesanan-id="{{ $pesanan->id }}">
                    {{ $status }}
                </button>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
```

### JavaScript Handler (SUDAH ADA):

```javascript
document.querySelectorAll('.update-vendor-status').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault();
        
        const vendorId = this.dataset.vendorId;
        const status = this.dataset.status;
        const pesananId = this.dataset.pesananId;
        
        // Optimistic UI update
        const container = this.closest('div').querySelectorAll('.update-vendor-status');
        container.forEach(btn => {
            btn.classList.remove('bg-gray-200', 'text-gray-800', 'hover:bg-gray-300',
                                  'bg-yellow-200', 'text-yellow-800', 'hover:bg-yellow-300',
                                  'bg-green-200', 'text-green-800', 'hover:bg-green-300');
            btn.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
        });
        
        this.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
        const statusColors = {
            'Belum Hadir': ['bg-gray-200', 'text-gray-800', 'hover:bg-gray-300'],
            'Perjalanan': ['bg-yellow-200', 'text-yellow-800', 'hover:bg-yellow-300'],
            'Hadir': ['bg-green-200', 'text-green-800', 'hover:bg-green-300']
        };
        this.classList.add(...statusColors[status]);
        
        // Send AJAX request
        fetch(`/lapangan/pesanan/${pesananId}/vendor-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || 
                               document.querySelector('input[name="_token"]')?.value
            },
            body: JSON.stringify({
                vendor_id: vendorId,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const message = document.createElement('div');
                message.className = 'fixed top-4 right-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg z-50';
                message.textContent = data.message;
                document.body.appendChild(message);
                setTimeout(() => message.remove(), 3000);
                
                // Reload laporan section if hadir
                if (status === 'Hadir') {
                    location.reload();
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengupdate status vendor');
            location.reload();
        });
    });
});
```

---

## 7. DATABASE SCHEMA

### Pesanans Table
```sql
CREATE TABLE pesanans (
    id BIGINT PRIMARY KEY,
    user_id BIGINT NOT NULL,
    korlap_id BIGINT NULLABLE,              -- ← FK ke Korlap (User)
    paket_id BIGINT NOT NULL,
    nomor_pesanan VARCHAR(50) UNIQUE,
    nama_pasangan VARCHAR(255),
    tanggal_acara DATE,
    jam_acara TIME,
    lokasi VARCHAR(255),
    status ENUM('Menunggu', 'Sedang Berlangsung', 'Selesai', 'Dibatalkan'),
    ...
    FOREIGN KEY (korlap_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX (korlap_id)
);
```

### Pesanan_Vendor Pivot Table
```sql
CREATE TABLE pesanan_vendor (
    id BIGINT PRIMARY KEY,
    pesanan_id BIGINT NOT NULL,
    vendor_id BIGINT NOT NULL,
    waktu_setup TIME NULLABLE,
    status ENUM('Belum Hadir', 'Perjalanan', 'Hadir') DEFAULT 'Belum Hadir',  -- ← Vendor status
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (pesanan_id) REFERENCES pesanans(id) ON DELETE CASCADE,
    FOREIGN KEY (vendor_id) REFERENCES vendors(id) ON DELETE CASCADE,
    UNIQUE KEY (pesanan_id, vendor_id)
);
```

### Laporan_Lapangans Table
```sql
CREATE TABLE laporan_lapangans (
    id BIGINT PRIMARY KEY,
    pesanan_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    tanggal DATE,
    kondisi ENUM('Baik', 'Perhatian', 'Kritis'),
    ringkasan TEXT,                          -- ← Auto-log: "10.45 - Vendor Name Hadir"
    tindak_lanjut TEXT NULLABLE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (pesanan_id) REFERENCES pesanans(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

---

## 8. TESTING FLOW

### Test 1: Korlap Melihat Pesanan
```
1. Login sebagai Korlap
2. Buka route: /lapangan/pesanan
3. ✅ Hanya pesanan dengan korlap_id = auth()->id() yang ditampilkan
```

### Test 2: Korlap Lihat Detail & Vendor
```
1. Dari daftar, klik "Lihat Detail"
2. Arahkan ke: /lapangan/pesanan/{id}
3. ✅ Tampilkan:
   - Info acara (customer, paket, lokasi)
   - Rundown acara (jadwal kegiatan)
   - Tugas lapangan (tasks to do)
   - Vendor Hari Ini (dengan status buttons)
   - Laporan Lapangan (logs)
```

### Test 3: Update Vendor Status
```
1. Di halaman detail, klik tombol status vendor (misal: "Perjalanan")
2. AJAX request ke: POST /lapangan/pesanan/{id}/vendor-status
3. Body: { vendor_id: 123, status: "Perjalanan" }
4. ✅ Update tabel pivot pesanan_vendor
5. ✅ UI berubah (button highlight berubah warna)
6. ✅ Toast notification: "Status vendor berhasil diperbarui"
```

### Test 4: Auto-Log Vendor Hadir
```
1. Update vendor status ke "Hadir"
2. ✅ Otomatis create entry di laporan_lapangans:
   - ringkasan: "10.45 - Vendor Name Hadir"
   - kondisi: "Baik"
3. ✅ Muncul di "Laporan Lapangan" section
4. ✅ Page reload untuk update
```

---

## 9. BEST PRACTICES

### ✅ Security
- Authorization check: `$pesanan->korlap_id !== auth()->id()`
- Vendor validation: pastikan vendor ditugaskan untuk pesanan ini
- Input validation: status harus dalam enum

### ✅ Performance
- Eager loading: `with()` untuk menghindari N+1 queries
- Pagination: 12 items per page
- Filter indexing: `korlap_id` dan `status` ter-index

### ✅ UX/UI
- Status buttons: visual yang jelas (warna berbeda)
- Optimistic UI: button berubah warna sebelum response
- Toast notifications: feedback visual untuk user
- Auto-reload: refresh page saat vendor hadir (update logs)

### ✅ Error Handling
- Try-catch di controller
- Log errors ke storage/logs
- Return JSON error response untuk AJAX
- Alert user jika gagal

---

## 10. TROUBLESHOOTING

### Masalah: Korlap melihat semua pesanan (bukan filter korlap_id)
**Solusi:** Pastikan query di `index()` memiliki `.where('korlap_id', auth()->id())`

### Masalah: Vendor status tidak terupdate
**Solusi:**
1. Cek AJAX request di browser DevTools → Network tab
2. Pastikan CSRF token ada di header
3. Verify vendor benar-benar ditugaskan (cek tabel `pesanan_vendor`)

### Masalah: Log tidak tercipta saat vendor hadir
**Solusi:**
1. Cek apakah `kondisi` table `laporan_lapangans` ada
2. Pastikan tanggal diset ke `now()->toDateString()`
3. Check if `user_id` field ada

---

## 11. FILE REFERENCE SUMMARY

| File | Tanggung Jawab |
|------|---|
| `routes/web.php` | Route definitions (named routes) |
| `app/Http/Controllers/Lapangan/PesananController.php` | Controller: index, show, updateVendorStatus |
| `app/Models/Pesanan.php` | Model: vendor relationship, scopes |
| `app/Models/Vendor.php` | Model: pesanan relationship |
| `app/Models/LaporanLapangan.php` | Model: for auto-logging |
| `resources/views/lapangan/modules/pesanan/index.blade.php` | List pemesanan |
| `resources/views/lapangan/modules/pesanan/show.blade.php` | Detail acara & vendor status buttons |

---

## QUICK REFERENCE: COPYING CODE

### Controller Methods (Copy-Paste Ready)
Lihat bagian 3 & 4 untuk Eloquent queries dan updateVendorStatus method

### Routes (Copy-Paste Ready)
Lihat bagian 2 untuk named route definitions

### Model Relationships (Copy-Paste Ready)
Lihat bagian 5 untuk vendor relationships

### Blade View & JavaScript (Copy-Paste Ready)
Lihat bagian 6 untuk vendor section HTML & JS handler

---

## 📌 RINGKASAN ALUR LENGKAP

```
1. ADMIN: Assign vendor ke pesanan + tentukan Korlap
   ↓
2. KORLAP: Login → /lapangan/pesanan (index)
   ↓
3. KORLAP: Klik "Lihat Detail" → /lapangan/pesanan/{id} (show)
   ↓
4. KORLAP: Lihat "VENDOR HARI INI" section
   ↓
5. KORLAP: Klik status button (Belum Hadir → Perjalanan → Hadir)
   ↓
6. AJAX: POST /lapangan/pesanan/{id}/vendor-status
   ↓
7. CONTROLLER: Verifikasi + Update pivot table pesanan_vendor
   ↓
8. AUTO-LOG: Jika status = "Hadir" → create laporan_lapangans entry
   ↓
9. UI: Show toast + reload untuk update logs section
```

---

**Status:** ✅ IMPLEMENTED & TESTED
**Last Updated:** 2026-05-30
**Maintained By:** Development Team

