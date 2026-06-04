# ⚡ QUICK START - 3 HOURS IMPLEMENTATION
**Target**: Complete backend synchronization dari 0 menjadi working
**Format**: Copy-paste friendly checklist
**Updated**: May 29, 2026

---

## ⏰ TIMELINE & CHECKLIST

### HOUR 1️⃣: Setup & Migrations (15 min)

#### 1.1 Backup Database
```bash
# Make sure you have backup first!
# Check your backups are safe
```
- [ ] Done

#### 1.2 Copy Migration Files
```bash
# Create files di database/migrations/
# 1. 2026_05_29_add_korlap_id_to_pesanans.php
# 2. 2026_05_29_add_dokumentasi_path_to_laporan_lapangans.php
```
- [ ] Both migration files created

#### 1.3 Run Migration
```bash
php artisan migrate
```
- [ ] Migration successful
- [ ] No errors in console

#### 1.4 Verify Database
```bash
php artisan tinker
>>> Schema::hasColumn('pesanans', 'korlap_id');
// Should return: true
>>> exit
```
- [ ] Both columns verified

---

### HOUR 1️⃣ (continued): Models (20 min)

#### 1.5 Update Pesanan Model
```php
// app/Models/Pesanan.php
// 1. Add 'korlap_id' to $fillable
// 2. Add korlap() method
// 3. Add tugas() method
```
**Copy from**: Pesanan_Updated.php
- [ ] File updated

#### 1.6 Update Tugas Model  
```php
// app/Models/Tugas.php
// Add: getPriorityBadgeAttribute()
// Add: getStatusBadgeAttribute()
```
**Copy from**: Tugas_Updated.php
- [ ] File updated

#### 1.7 Update LaporanLapangan Model
```php
// app/Models/LaporanLapangan.php
// 1. Add 'dokumentasi_path' to $fillable
// 2. Add scopes & badge methods
```
**Copy from**: LaporanLapangan_Updated.php
- [ ] File updated

#### 1.8 Test Models
```bash
php artisan tinker
>>> $pesanan = Pesanan::first();
>>> $pesanan->korlap; // Should work
>>> $pesanan->tugas; // Should work
>>> $tugas = Tugas::first();
>>> $tugas->progress; // Should return int 0-100
>>> exit
```
- [ ] All model methods working

---

### HOUR 2️⃣: Controllers (45 min)

#### 2.1 Update PengaturanController
```bash
# app/Http/Controllers/Lapangan/PengaturanController.php
# Copy from: PengaturanController_Updated.php
# Key: update() + apiProfile() methods
```
- [ ] File updated
- [ ] No errors

#### 2.2 Update PesananController (Lapangan)
```bash
# app/Http/Controllers/Lapangan/PesananController.php
# Copy from: PesananController_Updated.php
# CRITICAL: where('korlap_id', auth()->id())
```
- [ ] File updated
- [ ] Authorization checks in place

#### 2.3 Update TugasController
```bash
# app/Http/Controllers/Lapangan/TugasController.php
# Copy from: TugasController_Updated.php
# CRITICAL: updateChecklist() + syncTaskProgressToBooking()
```
- [ ] File updated
- [ ] All new methods added

#### 2.4 Update JadwalController
```bash
# app/Http/Controllers/Lapangan/JadwalController.php
# Copy from: JadwalController_Updated.php
# Key: getRundownDetail() method
```
- [ ] File updated

#### 2.5 Update LaporanController
```bash
# app/Http/Controllers/Lapangan/LaporanController.php
# Copy from: LaporanController_Updated.php
# Key: storeKendala() + uploadDokumentasi()
```
- [ ] File updated
- [ ] File upload handling added

#### 2.6 Update Admin PesananController
```bash
# app/Http/Controllers/Admin/PesananController.php
# Copy from: Admin/PesananController_Updated.php
# Key: assignKorlap() method
```
- [ ] File updated

#### 2.7 Test Routes
```bash
php artisan route:list | grep lapangan
# Should show ~20+ routes now
```
- [ ] All routes registered

---

### HOUR 2️⃣-3️⃣: Routes & JavaScript (30 min)

#### 2.8 Update routes/web.php
**Option A: Complete Replace**
```bash
# Copy entire content from: web_updated.php
# Paste into: routes/web.php
```

**Option B: Merge Key Routes**
```php
// Add these routes to lapangan group:
Route::get('/api/user-profile', ...)->name('api.profile');
Route::get('/jadwal/rundown/{pesanan}', ...)->name('jadwal.rundown');
Route::patch('/tugas/{tugas}/checklist/{checklist}', ...)->name('tugas.updateChecklist');
// ... etc (check web_updated.php)
```

- [ ] Routes updated
- [ ] No duplicate routes
- [ ] `php artisan route:list` works

#### 2.9 Create JavaScript Files

**File 1: jadwal-interactive.js**
```bash
# Create: resources/js/jadwal-interactive.js
# Copy from: jadwal-interactive.js
```
- [ ] File created

**File 2: kanban-checklist.js**
```bash
# Create: resources/js/kanban-checklist.js
# Copy from: kanban-checklist.js
```
- [ ] File created

**File 3: dokumentasi-upload.js**
```bash
# Create: resources/js/dokumentasi-upload.js
# Copy from: dokumentasi-upload.js
```
- [ ] File created

#### 2.10 Compile JavaScript
```bash
npm run dev
# or for production:
npm run build
```
- [ ] Compilation successful
- [ ] No errors

#### 2.11 Add Scripts to Views

**In your view files, add:**
```html
@push('scripts')
    <script src="{{ asset('js/jadwal-interactive.js') }}"></script>
    <script src="{{ asset('js/kanban-checklist.js') }}"></script>
    <script src="{{ asset('js/dokumentasi-upload.js') }}"></script>
@endpush
```

- [ ] Scripts included in views

---

### HOUR 3️⃣: Testing & Verification (30 min)

#### 3.1 Storage Symlink
```bash
php artisan storage:link
# Creates: public/storage -> storage/app/public
```
- [ ] Symlink created

#### 3.2 Manual Testing: Korlap Assignment

**Test Flow:**
```
1. Login as Admin (http://localhost/admin/login)
2. Go to /admin/booking
3. Click on a pesanan card
4. Look for "Assign Korlap" button
5. Click it → select a Korlap
6. Click Submit
7. Should see success message
```

**Verify:**
```bash
php artisan tinker
>>> $pesanan = Pesanan::find(1);
>>> $pesanan->korlap_id; // Should be > 0
>>> $pesanan->korlap->name; // Should show Korlap name
>>> exit
```

- [ ] Admin can assign Korlap
- [ ] Database updated correctly

#### 3.3 Manual Testing: Checklist Update

**Test Flow:**
```
1. Login as Korlap
2. Go to /lapangan/tugas
3. Find a task with checklists
4. Check a checkbox
5. Watch progress bar update (NO reload!)
6. Open browser DevTools > Network tab
7. Should see PATCH request
```

**Verify:**
```bash
php artisan tinker
>>> $checklist = TaskChecklist::first();
>>> $checklist->is_completed; // Should reflect your check
>>> $checklist->tugas->progress; // Should be updated
>>> exit
```

- [ ] Checklist checkbox works
- [ ] No page reload
- [ ] Progress bar updates
- [ ] AJAX request successful

#### 3.4 Manual Testing: Jadwal Interactive

**Test Flow:**
```
1. Login as Korlap
2. Go to /lapangan/jadwal
3. Click on a pesanan name from left list
4. Right panel should update (NO reload!)
5. Check rundown items display
```

**Browser Console:**
```javascript
// Open DevTools Console and check for:
console.log('No errors?')
// Should show no red errors
```

- [ ] Panel updates on click
- [ ] No page reload
- [ ] Rundown displays correctly
- [ ] Timeline status shows

#### 3.5 Manual Testing: Documentation Upload

**Test Flow:**
```
1. Login as Korlap
2. Go to /lapangan/laporan
3. Find "Upload Dokumentasi" form
4. Select an image (< 5MB)
5. Add keterangan (optional)
6. Click "Unggah Foto"
7. Photo should appear in gallery grid immediately
```

**File System Check:**
```bash
# Check if file was stored
ls -la storage/app/public/documentations/
# Should show your uploaded image
```

- [ ] Photo upload works
- [ ] File stored in correct location
- [ ] Photo appears in gallery
- [ ] No page reload

#### 3.6 Manual Testing: Profile Sync

**Test Flow:**
```
1. Login as Korlap
2. Go to /lapangan/pengaturan
3. Update name or upload avatar
4. Click Submit
5. Wait a bit (or refresh page)
6. Check header → name/avatar should update
```

**API Test:**
```bash
curl http://localhost/lapangan/api/user-profile
# Should return JSON:
# {
#   "id": 1,
#   "name": "...",
#   "avatar_url": "...",
#   "role": "lapangan"
# }
```

- [ ] Profile updates
- [ ] API endpoint returns data
- [ ] Header syncs

---

### HOUR 3️⃣ (continued): Final Checks (10 min)

#### 3.7 Check All Migrations
```bash
php artisan migrate:status
# Should show all migrations as: Ran
```
- [ ] All migrations marked as "Ran"

#### 3.8 Check Route Count
```bash
php artisan route:list | wc -l
# Should have significantly more routes now
```
- [ ] Routes increased

#### 3.9 Check No Errors
```bash
# Start server and check for errors
php artisan serve
# Visit each page and check browser console (F12)
# Should see NO red errors
```
- [ ] No console errors
- [ ] No 500 errors
- [ ] All pages load

#### 3.10 Summary Report
```bash
echo "✅ All implementations complete!"
echo "Total time: ~3 hours"
echo "Ready for testing by actual users"
```

---

## 📋 PRE-FLIGHT CHECKLIST (Do This FIRST!)

Before starting, confirm:

- [ ] Laravel project runs without errors (`php artisan serve`)
- [ ] Database connection works (`php artisan tinker` → can connect)
- [ ] Composer installed (`composer --version`)
- [ ] npm installed (`npm --version`)
- [ ] All current tests pass (if you have tests)
- [ ] Database backup exists
- [ ] You read BACKEND_SYNCHRONIZATION_GUIDE.md

---

## 🚨 IF SOMETHING BREAKS

### Migrations fail
```bash
php artisan migrate:rollback
# Check the error message
# Fix the issue in migration file
# Run again: php artisan migrate
```

### Routes don't work
```bash
php artisan route:clear
php artisan route:cache
php artisan serve
```

### JavaScript errors
```bash
# Open browser console (F12 → Console tab)
# Look for red errors
# Check that script src= is correct
# Try: npm run dev again
```

### File upload fails
```bash
# Check:
php artisan storage:link
# Verify: ls public/storage/ exists

# Check permissions:
chmod -R 775 storage/
```

### Authorization fails (403 error)
```bash
# Check:
# 1. User is logged in with correct role
# 2. User role matches the route middleware
# 3. korlap_id is set in database
```

---

## ✅ SUCCESS SIGNALS

When everything works, you will see:

✅ Admin can assign Korlap to pesanan
✅ Korlap sees ONLY their assigned pesanan
✅ Checklist click updates progress bar (no reload)
✅ Task auto-completes at 100%
✅ Jadwal rundown updates on click (no reload)
✅ Photo upload works and appears in gallery
✅ Profile update syncs to header
✅ No console errors
✅ No 404 or 500 errors
✅ Database tables updated correctly

---

## 📞 DEBUGGING COMMANDS

Keep these ready:

```bash
# Check database
php artisan tinker
>>> DB::table('pesanans')->where('korlap_id', '!=', null)->count();
>>> exit

# Clear all caches
php artisan cache:clear && php artisan config:clear && php artisan route:clear && php artisan view:clear

# Check specific route
php artisan route:list | grep "jadwal.rundown"

# Watch logs in real-time
tail -f storage/logs/laravel.log

# Test API endpoint
curl -H "Authorization: Bearer YOUR_TOKEN" http://localhost/lapangan/api/user-profile

# Check storage
ls -la storage/app/public/documentations/
```

---

## 🎯 AFTER IMPLEMENTATION

Once everything is working:

1. **Run full test suite** (if you have automated tests)
2. **Have team test** on staging
3. **Fix any found issues**
4. **Document any customizations**
5. **Deploy to production**
6. **Monitor logs** for first day

---

**Ready? Start at "HOUR 1️⃣" section now!**
**Estimated: 3 hours to working system** ⏱️

**Good luck! 🚀**
