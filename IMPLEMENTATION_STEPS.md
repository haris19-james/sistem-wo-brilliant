# IMPLEMENTASI STEP-BY-STEP
**Status**: Panduan Lengkap untuk Implementasi Backend Synchronization
**Updated**: May 29, 2026

---

## 📝 DAFTAR FILE YANG PERLU DIUPDATE

### A. MIGRATION FILES (Database)
```
✅ database/migrations/2026_05_29_add_korlap_id_to_pesanans.php
✅ database/migrations/2026_05_29_add_dokumentasi_path_to_laporan_lapangans.php
```

### B. MODEL FILES (Eloquent ORM)
```
✅ app/Models/Pesanan_Updated.php → COPY ke app/Models/Pesanan.php
✅ app/Models/Tugas_Updated.php → COPY ke app/Models/Tugas.php
✅ app/Models/LaporanLapangan_Updated.php → COPY ke app/Models/LaporanLapangan.php
```

### C. CONTROLLER FILES
```
✅ app/Http/Controllers/Lapangan/PengaturanController_Updated.php → app/Http/Controllers/Lapangan/PengaturanController.php
✅ app/Http/Controllers/Lapangan/PesananController_Updated.php → app/Http/Controllers/Lapangan/PesananController.php
✅ app/Http/Controllers/Lapangan/TugasController_Updated.php → app/Http/Controllers/Lapangan/TugasController.php
✅ app/Http/Controllers/Lapangan/JadwalController_Updated.php → app/Http/Controllers/Lapangan/JadwalController.php
✅ app/Http/Controllers/Lapangan/LaporanController_Updated.php → app/Http/Controllers/Lapangan/LaporanController.php
✅ app/Http/Controllers/Admin/PesananController_Updated.php → app/Http/Controllers/Admin/PesananController.php
```

### D. ROUTES FILE
```
✅ routes/web_updated.php → MERGE into routes/web.php
```

### E. JAVASCRIPT FILES (Frontend)
```
✅ resources/js/jadwal-interactive.js → NEW FILE
✅ resources/js/kanban-checklist.js → NEW FILE
✅ resources/js/dokumentasi-upload.js → NEW FILE
```

---

## 🚀 LANGKAH-LANGKAH IMPLEMENTASI

### PHASE 1: Database Preparation (15 minutes)

#### Step 1.1: Run Migrations
```bash
php artisan migrate
```

**Perubahan database yang terjadi:**
- Tambah kolom `korlap_id` ke tabel `pesanans` dengan foreign key ke `users`
- Tambah kolom `dokumentasi_path` ke tabel `laporan_lapangans`

#### Step 1.2: Verify Database Structure
```bash
# Check in database
DESCRIBE pesanans; -- Should show korlap_id column
DESCRIBE laporan_lapangans; -- Should show dokumentasi_path column
```

---

### PHASE 2: Model Updates (10 minutes)

#### Step 2.1: Update Pesanan Model
**File:** `app/Models/Pesanan.php`

**Action:** 
- Copy semua content dari `Pesanan_Updated.php`
- Fokus pada:
  - Add `'korlap_id'` ke `$fillable`
  - Add `korlap()` relationship method
  - Add `tugas()` relationship method

**Expected Result:**
```php
$pesanan = Pesanan::find(1);
$pesanan->korlap; // Returns User object dengan role 'lapangan'
$pesanan->tugas; // Returns Collection of Tugas
```

#### Step 2.2: Update Tugas Model
**File:** `app/Models/Tugas.php`

**Key Changes:**
- Method `getProgressAttribute()` sudah ada ✅
- Method `autoCompleteIfReady()` sudah ada ✅
- Add `getPriorityBadgeAttribute()` - NEW
- Add `getStatusBadgeAttribute()` - NEW

**Expected Result:**
```php
$tugas = Tugas::find(1);
echo $tugas->progress; // e.g., 50 (50%)
$tugas->autoCompleteIfReady(); // Auto-complete if all checklists done
```

#### Step 2.3: Update LaporanLapangan Model
**File:** `app/Models/LaporanLapangan.php`

**Key Changes:**
- Add `'dokumentasi_path'` ke `$fillable`
- Add helper methods untuk scopes dan badges

**Expected Result:**
```php
$laporan = LaporanLapangan::dokumentasi()->get(); // Get only documentation
$laporan->status_badge; // Returns Tailwind class string
```

---

### PHASE 3: Controller Updates (30 minutes)

#### Step 3.1: Update PengaturanController
**File:** `app/Http/Controllers/Lapangan/PengaturanController.php`

**Key Methods:**
1. `index()` - Show settings page (no change)
2. `update()` - ✅ UPDATE: Add avatar upload handling + JSON response
3. `apiProfile()` - ✅ NEW: Return user profile as JSON

**Test:**
```bash
# Test avatar upload
POST /lapangan/pengaturan
- name: "Korlap Name"
- email: "korlap@example.com"
- avatar_url: <image file>

# Test API endpoint
GET /lapangan/api/user-profile
# Response: { name, email, avatar_url, role }
```

#### Step 3.2: Update PesananController (Lapangan)
**File:** `app/Http/Controllers/Lapangan/PesananController.php`

**Key Changes:**
1. `index()` - ✅ ADD: `where('korlap_id', auth()->id())` filter
2. `show()` - ✅ ADD: Authorization check
3. `getProgressMetrics()` - ✅ NEW: Return JSON metrics

**Critical Line:**
```php
// BEFORE (buggy - shows ALL pesanan)
$query = Pesanan::with(['user', 'paket', 'progress'])

// AFTER (fixed - shows only assigned to current Korlap)
$query = Pesanan::with(['user', 'paket', 'progress', 'korlap'])
    ->where('korlap_id', auth()->id())
```

**Test:**
```bash
# Login as Korlap A
GET /lapangan/pesanan
# Should only show pesanans where korlap_id = Korlap A's ID

# Login as Korlap B
GET /lapangan/pesanan
# Should show different pesanans (where korlap_id = Korlap B's ID)
```

#### Step 3.3: Update TugasController
**File:** `app/Http/Controllers/Lapangan/TugasController.php`

**Key New Methods:**
1. `updateChecklist()` - ✅ NEW: Handle checklist update + progress sync
2. `detail()` - ✅ NEW: Return task detail as JSON
3. `updateStatus()` - ✅ NEW: Update task status via AJAX
4. `syncTaskProgressToBooking()` - ✅ NEW: Sync progress to ProgressPersiapan

**Most Important:** `updateChecklist()` method
```php
// This method:
// 1. Updates checklist is_completed status
// 2. Calls autoCompleteIfReady() on task
// 3. Calls syncTaskProgressToBooking() to update customer's progress view
// 4. Returns JSON for AJAX
```

**Test:**
```bash
# AJAX Checklist Update
PATCH /lapangan/tugas/1/checklist/5
{ "is_completed": true }

# Response:
{
  "success": true,
  "task": {
    "id": 1,
    "status": "in_progress",
    "progress_percent": 60
  }
}
```

#### Step 3.4: Update JadwalController
**File:** `app/Http/Controllers/Lapangan/JadwalController.php`

**Key Changes:**
1. `index()` - ✅ ADD: `where('korlap_id', auth()->id())`
2. `getRundownDetail()` - ✅ NEW: Return JSON rundown with timeline status

**Important Logic in `getRundownDetail()`:**
```php
// Determine timeline status berdasarkan waktu saat ini
if ($eventDate->isToday()) {
    if ($now < $mulai) {
        $status = 'akan_datang';
    } elseif ($now >= $mulai && $now < $selesai) {
        $status = 'berlangsung'; // Real-time status!
    } else {
        $status = 'selesai';
    }
}
```

**Test:**
```bash
GET /lapangan/jadwal/rundown/1
# Response: JSON dengan rundowns dan real-time status
```

#### Step 3.5: Update LaporanController
**File:** `app/Http/Controllers/Lapangan/LaporanController.php`

**Key New Methods:**
1. `storeKendala()` - ✅ NEW: Handle kendala upload
2. `uploadDokumentasi()` - ✅ NEW: Handle documentation photo upload
3. `metrics()` - ✅ NEW: Return metrics as JSON
4. `kendalaList()` - ✅ NEW: Return kendala list for pesanan
5. `updateCatatan()` - ✅ NEW: Update pesanan catatan
6. `progressByPesanan()` - ✅ NEW: Return progress metrics

**File Handling:**
```php
// Upload path
$path = $request->file('foto')->store('kendala', 'public');
// Result: /storage/kendala/filename.jpg

$path = $request->file('foto')->store('documentations', 'public');
// Result: /storage/documentations/filename.jpg
```

**Test:**
```bash
# Upload kendala
POST /lapangan/laporan/kendala
- pesanan_id: 1
- ringkasan: "Kursi kurang 10 buah"
- kondisi: "Kritis"
- foto: <file>

# Upload dokumentasi
POST /lapangan/laporan/dokumentasi
- pesanan_id: 1
- keterangan: "Setup dekorasi"
- foto: <file>
```

#### Step 3.6: Update Admin PesananController
**File:** `app/Http/Controllers/Admin/PesananController.php`

**Key New Methods:**
1. `assignKorlap()` - ✅ NEW: Assign Korlap to pesanan
2. `getAvailableKorlap()` - ✅ NEW: Get list of available Korlap
3. `getMetrics()` - ✅ NEW: Return pesanan metrics

**Critical Method: `assignKorlap()`**
```php
// Admin dapat assign Korlap ke pesanan
PATCH /admin/booking/1/assign-korlap
{ "korlap_id": 5 }

// Setelah ini, pesanan hanya visible di Korlap 5's panel
```

---

### PHASE 4: Routes Updates (10 minutes)

#### Step 4.1: Update routes/web.php

**Action:** Merge all routes dari `web_updated.php` ke `routes/web.php`

**Key New Routes (Lapangan Group):**
```php
// API endpoints
Route::get('/api/user-profile', [PengaturanController::class, 'apiProfile'])->name('api.profile');

// Jadwal interactive
Route::get('/jadwal/rundown/{pesanan}', [JadwalController::class, 'getRundownDetail'])->name('jadwal.rundown');

// Tugas Kanban
Route::patch('/tugas/{tugas}/checklist/{checklist}', [TugasController::class, 'updateChecklist'])->name('tugas.updateChecklist');
Route::get('/tugas/{tugas}/detail', [TugasController::class, 'detail'])->name('tugas.detail');

// Laporan
Route::post('/laporan/kendala', [LaporanController::class, 'storeKendala'])->name('laporan.kendala.store');
Route::post('/laporan/dokumentasi', [LaporanController::class, 'uploadDokumentasi'])->name('laporan.dokumentasi.upload');
```

**Key New Routes (Admin Group):**
```php
Route::patch('/booking/{pesanan}/assign-korlap', [PesananController::class, 'assignKorlap'])->name('booking.assignKorlap');
```

**Test:**
```bash
php artisan route:list | grep lapangan
php artisan route:list | grep admin/booking
```

---

### PHASE 5: JavaScript Integration (20 minutes)

#### Step 5.1: Add JavaScript Files

**Files to create:**
1. `resources/js/jadwal-interactive.js` - Interactive rundown panel
2. `resources/js/kanban-checklist.js` - Checklist real-time updates
3. `resources/js/dokumentasi-upload.js` - File upload handlers

#### Step 5.2: Update Vite Config (if needed)
**File:** `vite.config.js`

Pastikan JavaScript files ter-compile:
```bash
npm run dev
# or
npm run build
```

#### Step 5.3: Add Scripts ke Views

**Di jadwal view:**
```html
@push('scripts')
    <script src="{{ asset('js/jadwal-interactive.js') }}"></script>
@endpush
```

**Di tugas (kanban) view:**
```html
@push('scripts')
    <script src="{{ asset('js/kanban-checklist.js') }}"></script>
@endpush
```

**Di laporan view:**
```html
@push('scripts')
    <script src="{{ asset('js/dokumentasi-upload.js') }}"></script>
@endpush
```

#### Step 5.4: Update HTML Templates

**Jadwal Interactive - List pesanan:**
```html
<div data-pesanan-id="{{ $pesanan->id }}" class="cursor-pointer p-3 hover:bg-gray-50 rounded">
    {{ $pesanan->nama_pasangan }}
</div>
```

**Jadwal Interactive - Rundown panel:**
```html
<div data-rundown-panel class="space-y-4">
    <div data-panel-header></div>
    <div data-progress-bar></div>
    <div data-rundown-list></div>
</div>
```

**Kanban Checklist:**
```html
<div data-task-id="{{ $tugas->id }}" class="card">
    <input type="checkbox" 
           data-checklist-checkbox 
           data-checklist-id="{{ $checklist->id }}"
           data-tugas-id="{{ $tugas->id }}"
           {{ $checklist->is_completed ? 'checked' : '' }}>
    
    <div data-progress-bar>
        <div class="progress-fill" style="width: {{ $tugas->progress }}%"></div>
    </div>
    <span data-progress-text>{{ $tugas->progress }}%</span>
</div>
```

**Dokumentasi Upload:**
```html
<form data-upload-form data-pesanan-id="{{ $pesanan->id }}" data-form-type="dokumentasi">
    <input type="file" name="foto" accept="image/*" required>
    <textarea name="keterangan" placeholder="Deskripsi foto..."></textarea>
    <img data-image-preview style="display:none; max-width: 200px;">
    <button type="submit">Unggah Foto</button>
</form>

<div data-photo-gallery class="grid grid-cols-4 gap-4">
    <!-- Photos added here via JS -->
</div>
```

---

### PHASE 6: Testing (30 minutes)

#### Test Scenario 1: Korlap Assignment
```
1. Login as Admin
2. Go to /admin/booking
3. Click on a pesanan
4. Click "Assign Korlap" button
5. Select Korlap from dropdown
6. Click Submit
✅ Expected: Korlap assigned, visible in Korlap's pesanan list
```

#### Test Scenario 2: Checklist Update & Progress Sync
```
1. Login as Korlap
2. Go to /lapangan/tugas
3. Find a task with checklists
4. Check a checklist item
✅ Expected: 
   - Checkbox updates immediately
   - Progress bar increases
   - No page reload
   - Pesanan progress table updates
```

#### Test Scenario 3: Jadwal Interactive
```
1. Login as Korlap
2. Go to /lapangan/jadwal
3. Click on a pesanan name from left list
✅ Expected:
   - Right panel updates with rundown details
   - Timeline status shows correctly (Akan Datang/Berlangsung/Selesai)
   - No page reload
```

#### Test Scenario 4: Dokumentasi Upload
```
1. Login as Korlap
2. Go to /lapangan/laporan
3. Upload a foto from documentation form
4. Check browser network tab
✅ Expected:
   - File uploads as multipart/form-data
   - Response is JSON
   - Photo appears in gallery grid immediately
   - File size validated (max 5MB)
```

#### Test Scenario 5: Real-time Header Sync
```
1. Login as Korlap
2. Go to /lapangan/pengaturan
3. Update profile (name, avatar)
4. Submit form
5. Check header in other tabs
✅ Expected:
   - Header updates automatically every 10 seconds
   - Avatar changes in all pages
   - Name changes in greeting
```

---

## ⚠️ COMMON MISTAKES TO AVOID

### ❌ Mistake 1: Forgetting Migration
```bash
# WRONG - Model references korlap_id but column doesn't exist
php artisan tinker
>>> $pesanan = Pesanan::first();
>>> $pesanan->korlap_id; // Null or error

# RIGHT
php artisan migrate
```

### ❌ Mistake 2: Not Filtering by korlap_id
```php
// WRONG - Shows ALL pesanan
$pesanans = Pesanan::all();

// RIGHT - Shows only Korlap's pesanan
$pesanans = Pesanan::where('korlap_id', auth()->id())->get();
```

### ❌ Mistake 3: File Upload Path Issues
```php
// WRONG - Path is relative, not accessible via web
$path = 'documentations/file.jpg';
// Can't access: /storage/documentations/file.jpg

// RIGHT - Use storage path
$path = $request->file('foto')->store('documentations', 'public');
// Result: /storage/documentations/file.jpg ✅
```

### ❌ Mistake 4: Forgetting Authorization Check
```php
// WRONG - Anyone can update anyone's task
public function updateChecklist($tugas) {
    $tugas->update(...);
}

// RIGHT - Check ownership
public function updateChecklist($tugas) {
    $this->authorize('update', $tugas); // ✅ Check policy
    $tugas->update(...);
}
```

### ❌ Mistake 5: AJAX Without CSRF Token
```javascript
// WRONG
fetch('/endpoint', {
    method: 'POST',
    body: JSON.stringify(data)
})

// RIGHT
fetch('/endpoint', {
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify(data)
})
```

---

## 📊 VERIFICATION CHECKLIST

### Database Level
- [ ] Migration files created
- [ ] `php artisan migrate` runs successfully
- [ ] `pesanans` table has `korlap_id` column
- [ ] `laporan_lapangans` table has `dokumentasi_path` column

### Model Level
- [ ] `Pesanan` model has `korlap()` relationship
- [ ] `Pesanan` model has `tugas()` relationship
- [ ] `Tugas` model has `autoCompleteIfReady()` method
- [ ] `LaporanLapangan` model has dokumentasi_path in fillable

### Controller Level
- [ ] All controller methods moved to correct locations
- [ ] Authorization checks in place
- [ ] API endpoints return JSON
- [ ] File uploads handled with validation

### Routes Level
- [ ] All new routes registered
- [ ] Routes tested with `php artisan route:list`
- [ ] AJAX endpoints return correct status codes

### Frontend Level
- [ ] JavaScript files created
- [ ] Data attributes added to HTML elements
- [ ] No JavaScript errors in console
- [ ] AJAX requests include CSRF token

### Integration Level
- [ ] Korlap assignment works
- [ ] Pesanan filtering works
- [ ] Progress sync works
- [ ] File uploads work
- [ ] Header sync works

---

## 🔧 TROUBLESHOOTING

### Error: "Column 'korlap_id' doesn't exist"
```bash
# Solution: Run migrations
php artisan migrate
```

### Error: "Method 'korlap' not found"
```bash
# Solution: Update Pesanan model
# Make sure you added: public function korlap() { ... }
```

### AJAX returns 419 error
```bash
# Solution: Add CSRF token to request
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
}
```

### File upload fails with 413 error
```bash
# Solution: Check file size limit in php.ini
# Increase:
post_max_size = 20M
upload_max_filesize = 20M
```

### JavaScript console: "Uncaught TypeError"
```bash
# Solution: Check data attributes match exactly
# Make sure: data-pesanan-id, data-checklist-id, etc.
```

---

## ✅ SUCCESS INDICATORS

Jika semua berjalan dengan baik, Anda akan melihat:

1. ✅ **Korlap Assignment**: Admin dapat assign pesanan ke Korlap
2. ✅ **Data Isolation**: Setiap Korlap hanya lihat pesanan mereka
3. ✅ **Real-time Progress**: Checklist updates langsung update progress bar
4. ✅ **Auto-completion**: Task otomatis "Selesai" saat semua checklist done
5. ✅ **Interactive Timeline**: Rundown panel update tanpa reload
6. ✅ **File Upload**: Dokumentasi foto upload dan muncul di gallery
7. ✅ **Header Sync**: Profile changes sync ke header di semua pages
8. ✅ **Customer Visibility**: Customer bisa lihat real-time progress

---

**Ready to implement? Start with Phase 1 now!** 🚀
