# Lapangan Backend Architecture Diagram

## System Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                         FRONTEND (Blade Views)                  │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐          │
│  │ Kanban Board │  │Master-Detail │  │  Dashboard   │          │
│  │   Tasks      │  │   View       │  │  Metrics     │          │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘          │
│         │                 │                 │                   │
│         └─────────┬───────┴─────────┬───────┘                   │
│                   │                 │                           │
├───────────────────┼─────────────────┼───────────────────────────┤
│                   │ AJAX Fetch API  │                           │
│                   │   (JSON over    │                           │
│                   │   HTTP)         │                           │
│                   ▼                 ▼                           │
├──────────────────────────────────────────────────────────────────┤
│                    LARAVEL API ENDPOINTS                        │
│                                                                 │
│  ┌────────────────────┐        ┌────────────────────┐          │
│  │   KANBAN SYSTEM    │        │    REPORTING       │          │
│  ├────────────────────┤        ├────────────────────┤          │
│  │ PATCH /tugas/{id}/ │        │ GET  /laporan/     │          │
│  │        status      │        │      metrics       │          │
│  │                    │        │                    │          │
│  │ PATCH /tugas/{id}/ │        │ POST /laporan/     │          │
│  │ checklists/{id}    │        │      kendala       │          │
│  │                    │        │                    │          │
│  │ GET  /tugas/{id}/  │        │ GET  /laporan/     │          │
│  │      detail        │        │      kendala/{id}  │          │
│  │                    │        │                    │          │
│  │ (CRUD included)    │        │ GET /laporan/      │          │
│  │ POST /tugas        │        │     progress       │          │
│  │ PUT  /tugas/{id}   │        └────────────────────┘          │
│  │ DELETE /tugas/{id} │                                         │
│  └────────────────────┘                                         │
│                                                                 │
│    TugasController + LaporanController                          │
│    (7 AJAX + Legacy CRUD methods)                              │
├──────────────────────────────────────────────────────────────────┤
│                         ELOQUENT MODELS                         │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Tugas                                                    │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │ - id                                                     │  │
│  │ - nama_tugas                                             │  │
│  │ - status: 'pending' | 'in_progress' | 'completed'       │  │
│  │ - prioritas: 'high' | 'medium' | 'low'                 │  │
│  │ - deadline                                               │  │
│  │ - pesanan_id (FK)                                        │  │
│  │ - pic_id (FK)                                            │  │
│  │ - catatan                                                │  │
│  │                                                          │  │
│  │ ┌─ Relationships ──────────────────────────────────┐    │  │
│  │ │ • belongsTo(Pesanan)                             │    │  │
│  │ │ • belongsTo(User, 'pic_id')  ← PIC              │    │  │
│  │ │ • belongsTo(User) ← Creator                      │    │  │
│  │ │ • hasMany(TaskChecklist) ← Checklists           │    │  │
│  │ └──────────────────────────────────────────────────┘    │  │
│  │                                                          │  │
│  │ ┌─ Accessors ──────────────────────────────────────┐    │  │
│  │ │ • $progress (calculated 0-100%)                  │    │  │
│  │ └──────────────────────────────────────────────────┘    │  │
│  │                                                          │  │
│  │ ┌─ Methods ────────────────────────────────────────┐    │  │
│  │ │ • autoCompleteIfReady() (auto-complete logic)   │    │  │
│  │ └──────────────────────────────────────────────────┘    │  │
│  └──────────────────────────────────────────────────────────┘  │
│                           ▲                                     │
│                           │ HasMany                             │
│                           │                                     │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ TaskChecklist                                            │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │ - id                                                     │  │
│  │ - tugas_id (FK)                                          │  │
│  │ - deskripsi                                              │  │
│  │ - is_completed: boolean                                  │  │
│  │ - urutan: int (sort order)                               │  │
│  │ - completed_at: timestamp                                │  │
│  │                                                          │  │
│  │ ┌─ Relationships ──────────────────────────────────┐    │  │
│  │ │ • belongsTo(Tugas)                               │    │  │
│  │ └──────────────────────────────────────────────────┘    │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ LaporanLapangan                                          │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │ - id                                                     │  │
│  │ - pesanan_id (FK)                                        │  │
│  │ - user_id (FK)                                           │  │
│  │ - kondisi: 'baik' | 'perhatian' | 'kritis'             │  │
│  │ - deskripsi                                              │  │
│  │ - created_at / updated_at                                │  │
│  │                                                          │  │
│  │ (For challenge/kendala reporting)                        │  │
│  └──────────────────────────────────────────────────────────┘  │
│                                                                 │
│  ┌──────────────────────────────────────────────────────────┐  │
│  │ Pesanan (Order - existing)                               │  │
│  ├──────────────────────────────────────────────────────────┤  │
│  │ - id                                                     │  │
│  │ - nama_acara                                             │  │
│  │ - tanggal_acara                                          │  │
│  │ - ... (other fields)                                     │  │
│  │ • hasMany(Tugas)                                         │  │
│  │ • hasMany(LaporanLapangan)                               │  │
│  └──────────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────────┘
         │                                         │
         │ INSERT/UPDATE/DELETE                    │ SELECT queries
         │                                         │
         ▼                                         ▼
┌──────────────────────────────────────────────────────────────────┐
│                         MYSQL DATABASE                           │
│                                                                  │
│  tugas                   task_checklists        laporan_lapangan │
│  ├─ id (PK)             ├─ id (PK)              ├─ id (PK)      │
│  ├─ nama_tugas          ├─ tugas_id (FK) ◄──┐  ├─ pesanan_id    │
│  ├─ status              ├─ deskripsi       │  ├─ user_id        │
│  ├─ deadline            ├─ is_completed    │  ├─ kondisi        │
│  ├─ pesanan_id (FK)     ├─ urutan          │  ├─ deskripsi      │
│  ├─ pic_id (FK)         ├─ completed_at    │  └─ timestamps     │
│  ├─ prioritas           └─ timestamps      │                    │
│  ├─ kategorii                             │                    │
│  ├─ catatan                               │                    │
│  └─ timestamps          ◄─────────────────┘                    │
│                         INDEX(tugas_id, urutan)                 │
│                                                                  │
│  ┌─────────────────────────────────────────────────────────┐   │
│  │ QUERY EXAMPLES:                                         │   │
│  │                                                         │   │
│  │ Progress Calculation:                                   │   │
│  │ SELECT                                                  │   │
│  │   COUNT(CASE WHEN is_completed=1 THEN 1 END) /          │   │
│  │   COUNT(*) * 100                                        │   │
│  │ FROM task_checklists                                    │   │
│  │ WHERE tugas_id = ?                                      │   │
│  │                                                         │   │
│  │ Auto-Complete Check:                                    │   │
│  │ SELECT COUNT(*) FROM task_checklists                    │   │
│  │ WHERE tugas_id = ? AND is_completed = 0                 │   │
│  │ (if result = 0, complete the task)                      │   │
│  │                                                         │   │
│  │ Metrics:                                                │   │
│  │ SELECT COUNT(*) FROM tugas WHERE pesanan_id=? ...       │   │
│  │ SELECT COUNT(*) FROM tugas WHERE pesanan_id=?           │   │
│  │   AND status='completed'                                │   │
│  └─────────────────────────────────────────────────────────┘   │
└──────────────────────────────────────────────────────────────────┘
```

---

## Data Flow: Toggle Checklist Example

```
┌─────────────────────────────────────────────────────────────────┐
│ USER CLICKS CHECKBOX (Frontend)                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ JavaScript Event Handler                                        │
│                                                                 │
│ checkbox.addEventListener('change', async (e) => {             │
│   const tupasId = 1, checklistId = 5;                          │
│   const isCompleted = e.target.checked;                        │
│                                                                 │
│   fetch('/lapangan/tugas/1/checklists/5', {                    │
│     method: 'PATCH',                                           │
│     body: {is_completed: true}                                 │
│   })                                                            │
│ })                                                              │
└────────────────────────┬────────────────────────────────────────┘
                         │ HTTP PATCH REQUEST
                         │ {is_completed: true}
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ TugasController::updateChecklist()                              │
│                                                                 │
│ public function updateChecklist($tugas, $checklist, Request $r) │
│ {                                                               │
│   // 1. Validate                                               │
│   $validated = $r->validate([                                 │
│     'is_completed' => 'required|boolean'                      │
│   ]);                                                          │
│                                                                 │
│   // 2. Update database                                       │
│   $checklist->update([                                        │
│     'is_completed' => true,                                   │
│     'completed_at' => now()                                   │
│   ]);                                                          │
│                                                                 │
│   // 3. Auto-complete logic                                   │
│   $tugas->autoCompleteIfReady();                               │
│                                                                 │
│   // 4. Return JSON                                           │
│   return {                                                     │
│     'success': true,                                          │
│     'is_completed': true,                                     │
│     'progress': 75,     ← Auto-calculated from accessor       │
│     'task_status': 'completed'  ← If auto-completed          │
│   }                                                            │
│ }                                                               │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         │ Database UPDATE
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ task_checklists table                                           │
│                                                                 │
│ UPDATE task_checklists                                          │
│ SET is_completed = 1, completed_at = NOW()                     │
│ WHERE id = 5 AND tugas_id = 1;                                 │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         │ JSON Response (200 OK)
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ Frontend JavaScript Handler                                     │
│                                                                 │
│ const data = await response.json();                            │
│ // data = {success: true, progress: 75, ...}                   │
│                                                                 │
│ // 1. Update progress bar                                     │
│ progressBar.style.width = data.progress + '%';                │
│                                                                 │
│ // 2. Show toast                                              │
│ showToast(`Progress: ${data.progress}%`);                     │
│                                                                 │
│ // 3. Move to completed if auto-completed                    │
│ if (data.task_status === 'completed') {                       │
│   moveTaskCard(tugas_id, 'completed');                        │
│ }                                                              │
└────────────────────────┬────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────────┐
│ UI UPDATES IN REAL-TIME                                         │
│ ✓ Checkbox stays checked                                        │
│ ✓ Progress bar increases                                        │
│ ✓ Toast notification appears                                    │
│ ✓ Card might move to "Completed" column                         │
└─────────────────────────────────────────────────────────────────┘
```

---

## State Diagram: Task Status Flow

```
            ┌─────────────┐
            │   PENDING   │  (Initial state when created)
            │   (Belum)   │
            └──────┬──────┘
                   │ Manually drag or PATCH /status
                   │ OR first checklist completed
                   ▼
            ┌─────────────────┐
            │  IN_PROGRESS    │
            │ (Sedang Dikerjakan)
            └──────┬──────────┘
                   │ Manually drag or PATCH /status
                   │ OR all checklists completed
                   ▼
            ┌──────────────────┐
            │   COMPLETED      │
            │ (Selesai)        │
            │ ┌──────────────┐ │
            │ │ AUTO-COMPLETE│ │ ← Only if all checklists done
            │ │ (if triggered)
            │ └──────────────┘ │
            └──────────────────┘

Progress Bar Fills:
Pending 0%  →  In Progress 0-99%  →  Completed 100%
         ▓▓▓▓▓▓▓▓▓░░░░░░░░░░░░░░░░░░░  (50%)
         ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░░░░  (80%)
         ▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓  (100%) → Auto-complete!
```

---

## Progress Calculation Logic Flow

```
┌────────────────────────────────────────────────────┐
│ Task: Setup Dekorasi                              │
├────────────────────────────────────────────────────┤
│ Checklist 1: Bunga            is_completed: TRUE  │ ✓
│ Checklist 2: Lampu            is_completed: FALSE │
│ Checklist 3: Kursi            is_completed: FALSE │
└────────────────────────────────────────────────────┘
                      │
                      ▼
        ┌─────────────────────────┐
        │ $tugas->progress        │
        │ (Accessor)              │
        └─────────┬───────────────┘
                  │
        ┌─────────▼────────────────────────────┐
        │ Get all checklists (ordered by       │
        │ urutan)                              │
        │ Count: 3 total                       │
        │ Completed: 1                         │
        └─────────┬────────────────────────────┘
                  │
        ┌─────────▼─────────────────────────────┐
        │ Calculate: (1 / 3) × 100              │
        │         = 0.333 × 100                 │
        │         = 33 (int cast)               │
        └─────────┬──────────────────────────────┘
                  │
        ┌─────────▼──────────────────────────────┐
        │ Return: 33                             │
        │ echo $tugas->progress; // Output: 33   │
        └─────────┬──────────────────────────────┘
                  │
        ┌─────────▼──────────────────────────────┐
        │ Frontend receives in JSON:             │
        │ "progress": 33                         │
        │                                        │
        │ Update UI:                             │
        │ <progress value="33" max="100">        │
        │ ▓▓░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░   │
        └────────────────────────────────────────┘

When user toggles all to complete:
┌──────────────────────────────────────────┐
│ Checklist 1: TRUE  ✓                     │
│ Checklist 2: TRUE  ✓                     │
│ Checklist 3: TRUE  ✓                     │
│ Progress: (3/3) × 100 = 100%             │
│                                          │
│ AUTO-COMPLETE TRIGGERED:                │
│ Task.status = 'completed'                │
│ Task.updated_at = NOW()                  │
│                                          │
│ Frontend response:                       │
│ {                                        │
│   "progress": 100,                       │
│   "task_status": "completed"  ← Signal!  │
│ }                                        │
│                                          │
│ UI Action:                               │
│ Move card to "Completed" column          │
│ Show toast: "Task completed!"            │
└──────────────────────────────────────────┘
```

---

## API Request/Response Flow

```
REQUEST:
┌──────────────────────────────────────────────────┐
│ PATCH /lapangan/tugas/1/checklists/5             │
│ Content-Type: application/json                   │
│ X-CSRF-TOKEN: abc123...                          │
│                                                  │
│ {                                                │
│   "is_completed": true                           │
│ }                                                │
└──────────────────────────────────────────────────┘

PROCESSING:
┌──────────────────────────────────────────────────┐
│ 1. Route matches:                                │
│    PATCH /lapangan/tugas/{tugas}/checklists/{id} │
│                                                  │
│ 2. Controller called:                            │
│    TugasController@updateChecklist($tugas, $id)  │
│                                                  │
│ 3. Authorization checked:                        │
│    $this->authorize('update', $tugas)            │
│                                                  │
│ 4. Validation:                                   │
│    'is_completed' => 'required|boolean'          │
│                                                  │
│ 5. Database updated:                             │
│    TaskChecklist::find($id)->update(...)         │
│                                                  │
│ 6. Auto-complete check:                          │
│    $tugas->autoCompleteIfReady()                 │
│                                                  │
│ 7. Progress recalculated:                        │
│    $tugas->progress (accessor called)            │
│                                                  │
│ 8. JSON response built                           │
│    response()->json([...])                       │
└──────────────────────────────────────────────────┘

RESPONSE:
┌──────────────────────────────────────────────────┐
│ HTTP/1.1 200 OK                                  │
│ Content-Type: application/json                   │
│                                                  │
│ {                                                │
│   "success": true,                               │
│   "is_completed": true,                          │
│   "progress": 66,                                │
│   "task_status": "in_progress"                   │
│ }                                                │
└──────────────────────────────────────────────────┘
```

---

## Kanban Column Management

```
┌─────────────────────────────────────────────────────────────────┐
│                      KANBAN BOARD UI                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌────────────────┐  ┌────────────────┐  ┌────────────────┐   │
│  │ PENDING        │  │ IN_PROGRESS    │  │ COMPLETED      │   │
│  │ (Belum)        │  │ (Sedang)       │  │ (Selesai)      │   │
│  ├────────────────┤  ├────────────────┤  ├────────────────┤   │
│  │ Task 1         │  │ Task 2         │  │ Task 4         │   │
│  │ ▓░░░░░░░░░░░░░│  │ ▓▓▓▓▓░░░░░░░░░│  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓│   │
│  │ 0%             │  │ 33%            │  │ 100% ✓         │   │
│  │ PIC: Budi      │  │ PIC: Siti      │  │ PIC: Roni      │   │
│  │                │  │                │  │                │   │
│  │ Task 3         │  │                │  │ Task 5         │   │
│  │ ░░░░░░░░░░░░░░│  │                │  │ ▓▓▓▓▓▓▓▓▓▓▓▓▓▓│   │
│  │ 0%             │  │                │  │ 100% ✓         │   │
│  │ PIC: Rina      │  │                │  │ PIC: Ahmad     │   │
│  │                │  │                │  │                │   │
│  └────────────────┘  └────────────────┘  └────────────────┘   │
│                                                                 │
│  DRAG & DROP:                                                   │
│  ┌────────────────────────────────────────────────────────┐   │
│  │ User drags Task 1 from PENDING to IN_PROGRESS         │   │
│  │                                                        │   │
│  │ 1. Drag starts:  dataTransfer.setData('tugas-id', 1) │   │
│  │ 2. Drop triggers: Column drop handler fires          │   │
│  │ 3. PATCH sent:   /lapangan/tugas/1/status            │   │
│  │ 4. Status saved: task.status = 'in_progress'         │   │
│  │ 5. Response:     {success: true, ...}                │   │
│  │ 6. UI updates:   Card animates to new column         │   │
│  └────────────────────────────────────────────────────────┘   │
│                                                                 │
│  AUTO-COMPLETE:                                                 │
│  ┌────────────────────────────────────────────────────────┐   │
│  │ When user toggles last checklist in Task 2:            │   │
│  │                                                        │   │
│  │ 1. PATCH /lapangan/tugas/2/checklists/6               │   │
│  │ 2. Response includes: task_status: 'completed'        │   │
│  │ 3. JavaScript auto-moves card to COMPLETED column     │   │
│  │ 4. Progress bar becomes 100% ✓                        │   │
│  └────────────────────────────────────────────────────────┘   │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

**This diagram shows the complete flow from UI through database and back!**
