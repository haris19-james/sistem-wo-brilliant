# QUICK REFERENCE - Backend Synchronization
**Format**: Cheat Sheet untuk Senior Dev
**Purpose**: Cepat refer saat implementasi

---

## 🎯 CORE CONCEPT

```
ADMIN → [Assign Korlap] → PESANAN.korlap_id = Korlap ID
              ↓
        KORLAP (sees pesanan where korlap_id = auth()->id())
              ↓
        [Create Tugas] → PESANAN has many TUGAS
              ↓
        [Update Checklist] → TUGAS.progress = (completed/total) * 100
              ↓
        [Sync to ProgressPersiapan] → Customer sees progress
              ↓
        [Upload Photo] → /storage/documentations/
              ↓
        [Admin sees Report + Photos]
```

---

## 🗂️ FILE MAPPING

| Purpose | File | Action |
|---------|------|--------|
| **Migration** | `2026_05_29_add_korlap_id_to_pesanans.php` | CREATE NEW ✅ |
| **Migration** | `2026_05_29_add_dokumentasi_path_to_laporan_lapangans.php` | CREATE NEW ✅ |
| **Model** | `Pesanan.php` | Add `korlap()` & `tugas()` relationships |
| **Model** | `Tugas.php` | Add `getProgressAttribute()` ✅ (already exists) |
| **Model** | `LaporanLapangan.php` | Add `dokumentasi_path` to fillable |
| **Controller** | `Lapangan/PengaturanController.php` | Add `apiProfile()` + avatar upload |
| **Controller** | `Lapangan/PesananController.php` | Add `korlap_id` filter + `getProgressMetrics()` |
| **Controller** | `Lapangan/TugasController.php` | Add `updateChecklist()` + `syncTaskProgressToBooking()` |
| **Controller** | `Lapangan/JadwalController.php` | Add `getRundownDetail()` |
| **Controller** | `Lapangan/LaporanController.php` | Add `storeKendala()` + `uploadDokumentasi()` |
| **Controller** | `Admin/PesananController.php` | Add `assignKorlap()` |
| **Routes** | `web.php` | Add AJAX endpoints |
| **JavaScript** | `jadwal-interactive.js` | NEW - Interactive rundown |
| **JavaScript** | `kanban-checklist.js` | NEW - Checklist handler |
| **JavaScript** | `dokumentasi-upload.js` | NEW - File upload |

---

## 🔑 KEY METHODS

**Kanban (3)**
```
PATCH /lapangan/tugas/{id}/status
PATCH /lapangan/tugas/{id}/checklists/{id}
GET /lapangan/tugas/{id}/detail
```

**Reporting (4)**
```
GET /lapangan/laporan/metrics
GET /lapangan/laporan/progress
POST /lapangan/laporan/kendala
GET /lapangan/laporan/kendala/{pesanan}
```

### Controllers
```
TugasController ✅
  ├─ store() - form submit
  ├─ update() - form submit
  ├─ updateStatus() - AJAX
  ├─ updateChecklist() - AJAX
  └─ detail() - AJAX

LaporanController ✅
  ├─ index() - view
  ├─ metrics() - AJAX
  ├─ progressByPesanan() - AJAX
  ├─ storeKendala() - AJAX
  └─ kendalaList() - AJAX
```

### Routes
```
routes/web.php ✅
  ├─ resource 'tugas' (CRUD)
  ├─ PATCH tugas/{id}/status
  ├─ PATCH tugas/{id}/checklists/{id}
  ├─ GET tugas/{id}/detail
  ├─ GET laporan/metrics
  ├─ GET laporan/progress
  ├─ POST laporan/kendala
  └─ GET laporan/kendala/{pesanan}
```

---

## ✅ Implementation Checklist

- [x] Create task_checklists migration
- [x] Create TaskChecklist model
- [x] Update Tugas model (relationships + progress accessor)
- [x] Add updateStatus() to TugasController
- [x] Add updateChecklist() to TugasController
- [x] Add detail() to TugasController
- [x] Add metrics() to LaporanController
- [x] Add progressByPesanan() to LaporanController
- [x] Add storeKendala() to LaporanController
- [x] Add kendalaList() to LaporanController
- [x] Register 7 AJAX routes
- [x] Run migrations
- [x] Create documentation

**Total**: 12/12 ✅

---

## 🧪 How to Test

### 1. Create Test Task
```
Go to: /lapangan/tugas/create
Fill form with 2-3 checklist items
Submit
```

### 2. Test Toggle Checklist
```
curl -X PATCH http://localhost:8000/lapangan/tugas/1/checklists/1 \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: token" \
  -d '{"is_completed": true}'

Expected response:
{
  "success": true,
  "is_completed": true,
  "progress": 50,
  "task_status": "in_progress"
}
```

### 3. Test Update Status
```
curl -X PATCH http://localhost:8000/lapangan/tugas/1/status \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: token" \
  -d '{"status": "in_progress"}'

Expected response:
{
  "success": true,
  "status": "in_progress",
  "progress": 50
}
```

### 4. Test Get Detail
```
curl http://localhost:8000/lapangan/tugas/1/detail

Expected response:
{
  "id": 1,
  "nama_tugas": "Setup Dekorasi",
  "status": "in_progress",
  "progress": 50,
  "checklists": [
    {"id": 1, "deskripsi": "...", "is_completed": true},
    {"id": 2, "deskripsi": "...", "is_completed": false}
  ]
}
```

### 5. Test Metrics
```
curl http://localhost:8000/lapangan/laporan/metrics?pesanan_id=1

Expected response:
{
  "total_tasks": 5,
  "completed_tasks": 2,
  "overall_progress": 40,
  "by_status": {
    "pending": 2,
    "in_progress": 1,
    "completed": 2
  }
}
```

---

## 💡 Key Concepts

### Progress Calculation
```
Formula: (is_completed count / total count) × 100

Example:
- 1/3 checklists done → 33%
- 2/3 checklists done → 66%
- 3/3 checklists done → 100% (auto-complete!)
```

### Auto-Complete Logic
```
When toggle checklist to is_completed=true:

1. Update checklist.is_completed = true
2. Calculate task.progress
3. Check if ALL checklists are completed
4. If yes → Update task.status = 'completed'
5. Return in response: task_status = 'completed'
```

### Status Flow
```
pending → in_progress → completed
  ↓           ↓             ↓
Manual    Manual/Auto    Auto (when all checklists done)
update    update         + can update manually
```

---

## 📱 Frontend Integration Summary

### For Checklist Toggles
```javascript
// Listen to checkbox changes
document.querySelectorAll('.toggle-checklist').forEach(cb => {
  cb.addEventListener('change', async (e) => {
    // PATCH /lapangan/tugas/{id}/checklists/{id}
    // Update progress bar with data.progress
  });
});
```

### For Drag-Drop
```javascript
// Listen to drop event
element.addEventListener('drop', async (e) => {
  // PATCH /lapangan/tugas/{id}/status
  // Verify response before removing from old column
});
```

### For Master-Detail
```javascript
// Listen to task selection
taskItem.addEventListener('click', async (e) => {
  // GET /lapangan/tugas/{id}/detail
  // Render returned JSON into detail panel
});
```

### For Metrics
```javascript
// Poll every 30 seconds
setInterval(async () => {
  // GET /lapangan/laporan/metrics
  // Update dashboard numbers
}, 30000);
```

---

## 🔐 Security Notes

### CSRF Protection
- All POST/PATCH requests require `X-CSRF-TOKEN` header
- Get token from: `<input name="_token">` in form
- Or: `<meta name="csrf-token">` tag

### Authorization
- All endpoints check `auth()->id()` (must be logged in)
- TugasController has `$this->authorize()` checks
- LaporanController filters by `auth()->id()`

### Input Validation
- All inputs validated in controller
- Invalid requests return 422 with error details
- Frontend should display error messages

---

## 📊 Performance Tips

### Good
```javascript
// Eager load relationships
Tugas::with('checklists')->find($id)

// Use pagination
Task::paginate(20)

// Cache metrics (1 minute)
cache()->remember('metrics', 60, fn() => ...)
```

### Bad
```javascript
// N+1 queries
$tasks = Task::all();
foreach ($tasks as $task) {
  $task->checklists; // Extra query per task!
}

// Loading everything
Task::all() // Don't do on production!

// No caching
// Recalculate metrics on every request
```

---

## 🐛 Common Issues & Fixes

### "Column not found: is_completed"
- Migration didn't run
- Fix: `php artisan migrate`

### "Model Tugas has no relation checklists()"
- Model not updated
- Fix: Check `app/Models/Tugas.php` has `checklists()` method

### "CSRF token mismatch"
- Missing X-CSRF-TOKEN header in AJAX
- Fix: Add header `'X-CSRF-TOKEN': getCsrfToken()`

### "Unauthorized" (403)
- User not logged in or wrong role
- Fix: Login as 'lapangan' user, check middleware

### "404 Not found"
- Route not registered
- Fix: Check routes/web.php has all 7 routes

---

## 📞 Need Help?

Check these files in order:
1. **IMPLEMENTATION_STATUS.md** - Architecture overview
2. **BACKEND_IMPLEMENTATION_COMPLETE.md** - API reference
3. **FRONTEND_INTEGRATION_GUIDE.md** - JavaScript examples

Then:
- Test with cURL commands above
- Check Laravel logs: `storage/logs/laravel.log`
- Verify database: `php artisan tinker` → `Tugas::first();`

---

## 🎯 Next: Frontend Integration

1. Add event listeners to existing Blade templates
2. Connect toggles/buttons to AJAX endpoints
3. Update UI with returned data
4. Test workflows end-to-end
5. Add loading spinner during requests
6. Add error messages

**Time to integrate**: ~2-4 hours for full implementation

---

**Status**: ✅ Backend Complete - Ready for Frontend!
