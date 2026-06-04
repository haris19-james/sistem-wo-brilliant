# Backend Synchronization & Perfection Guide
**Status**: Pedoman Lengkap untuk Menyempurnakan Logika Backend
**Created**: May 29, 2026

---

## 📋 ISSUE ANALYSIS & SOLUTIONS

### 1️⃣ INTEGRASI DATA AUTHENTICATION & HEADER (Global State)

#### ❌ MASALAH SAAT INI:
- **PengaturanController** hanya update field `name`, `email`, `phone`
- **Header UI** tidak memiliki mechanism untuk real-time sync user data
- Ketika Korlap update profil, tidak ada trigger untuk update header di semua halaman
- Foto profil (`avatar_url`) tidak ter-handle dalam update flow

#### ✅ SOLUSI:

**A. Update `PengaturanController`:**
```php
// app/Http/Controllers/Lapangan/PengaturanController.php
public function update(Request $request)
{
    $user = Auth::user();
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone_number' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'avatar_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
    ]);

    // Handle avatar upload
    if ($request->hasFile('avatar_url')) {
        $path = $request->file('avatar_url')->store('avatars', 'public');
        $validated['avatar_url'] = '/storage/' . $path;
    }

    $user->update([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'phone_number' => $validated['phone_number'] ?? $user->phone_number,
        'address' => $validated['address'] ?? $user->address,
        'avatar_url' => $validated['avatar_url'] ?? $user->avatar_url,
    ]);

    // Return JSON untuk AJAX response
    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Pengaturan akun berhasil diperbarui',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'avatar_url' => $user->avatar_url,
                'phone_number' => $user->phone_number,
                'address' => $user->address,
            ]
        ]);
    }

    return redirect()->route('lapangan.pengaturan')
        ->with('success', 'Pengaturan akun berhasil diperbarui');
}
```

**B. Create API Endpoint untuk Header Data:**
```php
// Add to routes/web.php (Lapangan group)
Route::get('/api/user-profile', [LapanganPengaturanController::class, 'apiProfile'])->name('api.profile');
```

```php
// Add method to PengaturanController
public function apiProfile()
{
    $user = Auth::user();
    return response()->json([
        'name' => $user->name,
        'email' => $user->email,
        'avatar_url' => $user->avatar_url ?? '/images/default-avatar.png',
        'role' => $user->role,
    ]);
}
```

---

### 2️⃣ SINKRONISASI UTAMA: ADMIN → KORLAP → CUSTOMER

#### A. TABEL 'PESANAN' - RELASI KORLAP

**❌ MASALAH:**
- Model `Pesanan` tidak memiliki kolom `korlap_id`
- Tidak ada cara track siapa Korlap yang handle pesanan ini
- Query di `PesananController (Lapangan)` tidak bisa filter pesanan yang assign ke user

**✅ SOLUSI:**

**Step 1: Create Migration untuk add `korlap_id`**
```php
// database/migrations/YYYY_MM_DD_add_korlap_id_to_pesanans.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->unsignedBigInteger('korlap_id')->nullable()->after('user_id');
            $table->foreign('korlap_id')->references('id')->on('users')->onDelete('set null');
            $table->index('korlap_id');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropForeign(['korlap_id']);
            $table->dropColumn('korlap_id');
        });
    }
};
```

**Step 2: Update Model `Pesanan`**
```php
// app/Models/Pesanan.php
protected $fillable = [
    'user_id',
    'paket_id',
    'korlap_id',  // ADD THIS
    // ... existing fields ...
];

// Tambah relationship
public function korlap()
{
    return $this->belongsTo(User::class, 'korlap_id')
        ->where('role', 'lapangan');
}
```

**Step 3: Update Admin Controller - Assign Korlap**
```php
// app/Http/Controllers/Admin/PesananController.php
public function assignKorlap(Request $request, Pesanan $pesanan)
{
    $validated = $request->validate([
        'korlap_id' => 'required|exists:users,id',
    ]);

    $pesanan->update(['korlap_id' => $validated['korlap_id']]);

    return back()->with('success', 'Korlap berhasil ditunjuk untuk pesanan ini');
}
```

Add route:
```php
Route::patch('/booking/{pesanan}/assign-korlap', [AdminPesananController::class, 'assignKorlap'])
    ->name('booking.assignKorlap');
```

**Step 4: Fix `LapanganPesananController` - Query Filter**
```php
// app/Http/Controllers/Lapangan/PesananController.php
public function index(Request $request)
{
    $query = Pesanan::with(['user', 'paket', 'progress', 'korlap'])
        ->where('korlap_id', auth()->id())  // ✅ FILTER BY LOGGED-IN KORLAP
        ->whereNotIn('status', ['Dibatalkan'])
        ->orderBy('tanggal_acara');

    if ($request->filled('status') && $request->status !== 'semua') {
        $query->where('status', $request->status);
    } else {
        $query->aktifLapangan();
    }

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

---

#### B. TABEL 'TASKS' - KANBAN PROGRESS SYNCHRONIZATION

**❌ MASALAH:**
- `Tugas` model punya method `getProgressAttribute()` tapi tidak di-trigger saat checklist update
- Progress tidak ter-sync ke `ProgressPersiapan` tabel
- Customer tidak bisa lihat real-time progress

**✅ SOLUSI:**

**Step 1: Update `TugasController` - Add API Endpoint untuk Checklist Update**
```php
// app/Http/Controllers/Lapangan/TugasController.php

public function updateChecklist(Request $request, Tugas $tugas, TaskChecklist $checklist)
{
    $this->authorize('update', $tugas);

    $validated = $request->validate([
        'is_completed' => 'required|boolean',
    ]);

    $checklist->update([
        'is_completed' => $validated['is_completed'],
        'completed_at' => $validated['is_completed'] ? now() : null,
    ]);

    // Hitung progress berdasarkan checklists yang sudah dicentang
    $progress = $tugas->progress;
    
    // Auto-complete task jika semua checklist selesai
    $tugas->autoCompleteIfReady();

    // UPDATE ProgressPersiapan untuk tracking customer
    $this->syncTaskProgressToBooking($tugas);

    // Return JSON untuk AJAX
    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'task' => [
                'id' => $tugas->id,
                'status' => $tugas->status,
                'progress_percent' => $tugas->progress,
            ],
            'message' => 'Checklist berhasil diperbarui',
        ]);
    }

    return back()->with('success', 'Checklist berhasil diperbarui');
}

/**
 * Sync task progress ke tabel ProgressPersiapan untuk customer visibility
 */
private function syncTaskProgressToBooking(Tugas $tugas)
{
    $pesanan = $tugas->pesanan;
    if (!$pesanan) return;

    // Get all tasks untuk pesanan ini
    $allTasks = $tugas->pesanan->tugas()->with('checklists')->get();
    
    $totalProgress = 0;
    foreach ($allTasks as $task) {
        $totalProgress += $task->progress;
    }
    
    $avgProgress = $allTasks->count() > 0 
        ? (int) ($totalProgress / $allTasks->count()) 
        : 0;

    // Update ProgressPersiapan
    $progress = ProgressPersiapan::firstOrCreate(
        ['pesanan_id' => $pesanan->id],
        ['persentase' => 0]
    );

    $progress->update(['persentase' => $avgProgress]);
}
```

**Step 2: Update Route untuk AJAX Checklist Update**
```php
// routes/web.php - Lapangan group
Route::patch('/tugas/{tugas}/checklist/{checklist}', [LapanganTugasController::class, 'updateChecklist'])
    ->name('tugas.checklist.update');
```

**Step 3: Create Model Observer untuk auto-complete on progress**
```bash
# Terminal
php artisan make:observer TugasObserver --model=Tugas
```

```php
// app/Observers/TugasObserver.php
<?php

namespace App\Observers;

use App\Models\Tugas;

class TugasObserver
{
    public function updated(Tugas $tugas): void
    {
        // If progress reaches 100%, auto-complete
        if ($tugas->progress === 100 && $tugas->status !== 'completed') {
            $tugas->update(['status' => 'completed']);
        }
    }
}
```

Register di `AppServiceProvider`:
```php
// app/Providers/AppServiceProvider.php
use App\Models\Tugas;
use App\Observers\TugasObserver;

public function boot(): void
{
    Tugas::observe(TugasObserver::class);
}
```

---

### 3️⃣ ALUR TIMELINE JADWAL ACARA & LIVEWIRE INTERACTIVE

**❌ MASALAH:**
- `JadwalController` hanya return static view
- Tidak ada AJAX/Livewire endpoint untuk switch rundown
- Timeline status (Selesai/Berlangsung/Akan Datang) tidak calculated real-time

**✅ SOLUSI:**

**Step 1: Create API Endpoint untuk Rundown Details**
```php
// app/Http/Controllers/Lapangan/JadwalController.php

public function getRundownDetail(Pesanan $pesanan)
{
    // Validate Korlap access
    if ($pesanan->korlap_id !== auth()->id()) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }

    $rundowns = $pesanan->rundowns()
        ->select('id', 'kategori_acara', 'waktu_mulai', 'waktu_selesai', 'kegiatan')
        ->orderBy('waktu_mulai')
        ->get()
        ->map(function($rundown) {
            $now = now()->format('H:i');
            $mulai = $rundown->waktu_mulai?->format('H:i') ?? '-';
            $selesai = $rundown->waktu_selesai?->format('H:i') ?? '-';
            
            // Determine timeline status
            if ($now < $mulai) {
                $status = 'akan_datang';
                $status_label = 'Akan Datang';
                $status_class = 'text-gray-500';
            } elseif ($now >= $mulai && $now < ($selesai ?: $mulai)) {
                $status = 'berlangsung';
                $status_label = 'Berlangsung';
                $status_class = 'text-blue-600';
            } else {
                $status = 'selesai';
                $status_label = 'Selesai';
                $status_class = 'text-green-600';
            }

            return [
                'id' => $rundown->id,
                'kategori' => $rundown->kategori_acara,
                'waktu_mulai' => $mulai,
                'waktu_selesai' => $selesai,
                'kegiatan' => $rundown->kegiatan,
                'status' => $status,
                'status_label' => $status_label,
                'status_class' => $status_class,
            ];
        });

    return response()->json([
        'pesanan' => [
            'id' => $pesanan->id,
            'nama_pasangan' => $pesanan->nama_pasangan,
            'tanggal_acara' => $pesanan->tanggal_acara->format('d F Y'),
            'lokasi' => $pesanan->lokasi,
            'tema' => $pesanan->tema,
        ],
        'rundowns' => $rundowns,
    ]);
}
```

**Step 2: Add Route untuk API**
```php
// routes/web.php - Lapangan group
Route::get('/jadwal/rundown/{pesanan}', [LapanganJadwalController::class, 'getRundownDetail'])
    ->name('jadwal.rundown');
```

**Step 3: Update Frontend dengan AJAX**
Gunakan fetch untuk update rundown panel tanpa reload:
```javascript
// resources/js/jadwal-interactive.js
document.querySelectorAll('[data-pesanan-id]').forEach(el => {
    el.addEventListener('click', async (e) => {
        e.preventDefault();
        const pesananId = el.dataset.pesananId;
        
        try {
            const response = await fetch(`/lapangan/jadwal/rundown/${pesananId}`);
            const data = await response.json();
            
            // Update panel detail di sebelah kanan
            updateRundownPanel(data);
        } catch (error) {
            console.error('Error loading rundown:', error);
        }
    });
});

function updateRundownPanel(data) {
    const panel = document.querySelector('[data-rundown-panel]');
    // ... update DOM dengan data.rundowns ...
}
```

---

### 4️⃣ ALUR REAL-TIME REPORTING: KORLAP → ADMIN

**❌ MASALAH:**
- `LaporanController` tidak handle upload file
- Tidak ada API untuk store kendala lapangan
- Documentation photos tidak ada storage path

**✅ SOLUSI:**

**Step 1: Create Model `LaporanLapangan` yang lengkap (jika belum)**
```php
// app/Models/LaporanLapangan.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LaporanLapangan extends Model
{
    use HasFactory;

    protected $table = 'laporan_lapangans';

    protected $fillable = [
        'pesanan_id',
        'user_id',
        'ringkasan',
        'kondisi',
        'foto_path',
        'dokumentasi_path',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

**Step 2: Update `LaporanController` - Add methods untuk kendala & dokumentasi**
```php
// app/Http/Controllers/Lapangan/LaporanController.php

public function storeKendala(Request $request)
{
    $validated = $request->validate([
        'pesanan_id' => 'required|exists:pesanans,id',
        'ringkasan' => 'required|string|max:500',
        'kondisi' => 'required|in:Baik,Perhatian,Kritis',
        'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
    ]);

    $laporan = new LaporanLapangan([
        'pesanan_id' => $validated['pesanan_id'],
        'user_id' => auth()->id(),
        'ringkasan' => $validated['ringkasan'],
        'kondisi' => $validated['kondisi'],
    ]);

    // Handle foto upload
    if ($request->hasFile('foto')) {
        $path = $request->file('foto')->store('kendala', 'public');
        $laporan->foto_path = '/storage/' . $path;
    }

    $laporan->save();

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Kendala berhasil dilaporkan',
            'laporan' => $laporan,
        ]);
    }

    return back()->with('success', 'Kendala berhasil dilaporkan');
}

public function uploadDokumentasi(Request $request)
{
    $validated = $request->validate([
        'pesanan_id' => 'required|exists:pesanans,id',
        'foto' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        'keterangan' => 'nullable|string|max:255',
    ]);

    $path = $request->file('foto')->store('documentations', 'public');

    $laporan = LaporanLapangan::create([
        'pesanan_id' => $validated['pesanan_id'],
        'user_id' => auth()->id(),
        'ringkasan' => $validated['keterangan'] ?? 'Dokumentasi lapangan',
        'kondisi' => 'Baik',
        'dokumentasi_path' => '/storage/' . $path,
    ]);

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Foto dokumentasi berhasil diunggah',
            'photo' => [
                'url' => $laporan->dokumentasi_path,
                'keterangan' => $laporan->ringkasan,
            ],
        ]);
    }

    return back()->with('success', 'Foto dokumentasi berhasil diunggah');
}
```

**Step 3: Add Routes**
```php
// routes/web.php - Lapangan group
Route::post('/laporan/kendala', [LapanganLaporanController::class, 'storeKendala'])
    ->name('laporan.kendala.store');
Route::post('/laporan/dokumentasi', [LapanganLaporanController::class, 'uploadDokumentasi'])
    ->name('laporan.dokumentasi.upload');
```

**Step 4: Update Admin Controller - Get Laporan dengan Dokumentasi**
```php
// app/Http/Controllers/AdminController.php

public function laporanDetail(Pesanan $pesanan)
{
    $laporan = $pesanan->laporanLapangans()
        ->with('user')
        ->latest('created_at')
        ->get();

    $kendala = $laporan->filter(fn($l) => $l->foto_path)->map(fn($l) => [
        'icon' => $l->kondisi === 'Kritis' ? 'alert-circle' : 'info',
        'title' => $l->kondisi,
        'event' => $l->ringkasan,
        'time' => $l->created_at->format('H:i'),
        'foto' => $l->foto_path,
    ]);

    $dokumentasi = $laporan->filter(fn($l) => $l->dokumentasi_path)
        ->map(fn($l) => [
            'url' => $l->dokumentasi_path,
            'title' => $l->ringkasan,
            'time' => $l->created_at->format('d M H:i'),
        ]);

    return view('admin.laporan.detail', compact('pesanan', 'kendala', 'dokumentasi'));
}
```

---

## 🔧 IMPLEMENTATION CHECKLIST

### Phase 1: Database & Models ✅
- [ ] Create migration untuk add `korlap_id` ke `pesanans`
- [ ] Update `Pesanan` model dengan `korlap()` relationship
- [ ] Update `LaporanLapangan` model dengan proper fillable & storage paths
- [ ] Create migration untuk add `dokumentasi_path` ke `laporan_lapangans` (jika belum ada)

### Phase 2: Controllers & API Endpoints ✅
- [ ] Update `LapanganPengaturanController::update()` dengan avatar upload
- [ ] Add `LapanganPengaturanController::apiProfile()` endpoint
- [ ] Add `AdminPesananController::assignKorlap()` method
- [ ] Fix `LapanganPesananController::index()` dengan korlap filter
- [ ] Add `LapanganTugasController::updateChecklist()` dengan progress sync
- [ ] Add `LapanganJadwalController::getRundownDetail()` API endpoint
- [ ] Add `LapanganLaporanController::storeKendala()` & `uploadDokumentasi()` methods

### Phase 3: Routes ✅
- [ ] Add route untuk `/api/user-profile` (GET)
- [ ] Add route untuk `/booking/{pesanan}/assign-korlap` (PATCH)
- [ ] Add route untuk `/tugas/{tugas}/checklist/{checklist}` (PATCH)
- [ ] Add route untuk `/jadwal/rundown/{pesanan}` (GET)
- [ ] Add route untuk `/laporan/kendala` (POST)
- [ ] Add route untuk `/laporan/dokumentasi` (POST)

### Phase 4: Observers & Events ✅
- [ ] Create & register `TugasObserver`
- [ ] Ensure `autoCompleteIfReady()` method works properly

### Phase 5: Frontend AJAX/Livewire Integration ⏳
- [ ] Create JavaScript untuk interactive rundown panel
- [ ] Add AJAX handler untuk checklist updates
- [ ] Add drag-drop untuk Kanban cards (optional Sortable.js)
- [ ] Add file upload handler untuk dokumentasi

---

## 📊 DATA FLOW DIAGRAM

```
ADMIN PANEL
    ↓
[Create Pesanan] → DB pesanans (status: Menunggu)
    ↓
[Assign Korlap] → UPDATE pesanans.korlap_id
    ↓
KORLAP PANEL
    ↓
[View Pesanan Saya] ← Query: WHERE korlap_id = auth()->id()
    ↓
[Create Tugas Master] → DB tugas (status: pending)
    ↓
[Update Checklist] → Trigger progress calculation
    ↓
SYNC: tugas.progress → progress_persiapans.persentase
    ↓
REAL-TIME DISPLAY:
- Korlap: Kanban card dengan progress bar
- Admin: Dashboard monitoring
- Customer: Jadwal acara progress tracker
    ↓
[Upload Kendala/Foto] → Storage + DB laporan_lapangans
    ↓
ADMIN VISIBILITY:
- Badge merah dengan kendala count
- Grid galeri dokumentasi
- Laporan real-time
```

---

## 🎨 TAILWIND RECOMMENDATIONS

### Progress Bar Component
```html
<!-- Responsive progress bar untuk Kanban card -->
<div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-full transition-all duration-300 ease-out"
         style="width: {{ $task->progress }}%"></div>
</div>
<span class="text-xs font-medium text-gray-600">{{ $task->progress }}%</span>
```

### Alert Badge Dengan Notifikasi
```html
<!-- Kendala badge di dashboard admin -->
<div class="relative">
    <button class="relative p-2 text-gray-600 hover:text-gray-900">
        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path d="M10.5 1.5H9.5V.5h1v1zM4 4l-.707-.707.707.707zm12 0l.707-.707-.707.707z"/>
        </svg>
        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
            {{ $kendalaCount }}
        </span>
    </button>
</div>
```

---

## ⚠️ CRITICAL NOTES

1. **Always run migrations** after database changes
2. **Test korlap_id filter** thoroughly - jangan sampai Korlap bisa lihat pesanan orang lain
3. **File upload security**: Validate MIME types & file size di server-side
4. **Real-time updates**: Consider Redis Queue untuk large operations
5. **Backup existing data** sebelum migration ke production

---

**Siap untuk diimplementasikan!** 🚀
