# 🎯 KANBAN SYSTEM - COMPLETE IMPLEMENTATION

## Bagian 1: DATABASE MIGRATION

### File: `database/migrations/[timestamp]_create_task_checklists_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tugas_id');
            $table->string('deskripsi', 500);
            $table->boolean('is_completed')->default(false);
            $table->integer('urutan')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('tugas_id')
                ->references('id')
                ->on('tugas')
                ->onDelete('cascade');

            // Index untuk sorting
            $table->index('urutan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_checklists');
    }
};
```

### File: `database/migrations/[timestamp]_update_tugas_table.php`

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            // Tambah kolom yang belum ada
            $table->enum('status', ['pending', 'in_progress', 'completed'])
                  ->default('pending')
                  ->change(); // Jika sudah ada, ubah type enum
        });
    }

    public function down(): void
    {
        // Rollback
    }
};
```

---

## Bagian 2: MODEL STRUCTURE

### File: `app/Models/Tugas.php` (Updated)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    use HasFactory;

    protected $fillable = [
        'pesanan_id',
        'user_id',
        'pic_id',
        'nama_tugas',
        'kategori',
        'prioritas',
        'status',
        'deadline',
        'catatan',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    };

    // ==================== RELATIONSHIPS ====================

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    /**
     * One Tugas has many checklists
     */
    public function checklists(): HasMany
    {
        return $this->hasMany(TaskChecklist::class)
                    ->orderBy('urutan', 'asc');
    }

    // ==================== COMPUTED ATTRIBUTES ====================

    /**
     * Hitung persentase progress dari checklist
     * 
     * @return float (0-100)
     */
    public function getProgressPercentageAttribute(): float
    {
        $total = $this->checklists()->count();
        
        if ($total === 0) {
            return 0;
        }

        $completed = $this->checklists()
                          ->where('is_completed', true)
                          ->count();

        return ($completed / $total) * 100;
    }

    /**
     * Get badge color berdasarkan prioritas
     */
    public function getPriorityColorAttribute(): string
    {
        return match ($this->prioritas) {
            'critical' => 'bg-red-100 text-red-800',
            'high'     => 'bg-orange-100 text-orange-800',
            'medium'   => 'bg-yellow-100 text-yellow-800',
            'low'      => 'bg-green-100 text-green-800',
            default    => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get status badge
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'      => 'bg-gray-100 text-gray-800',
            'in_progress'  => 'bg-blue-100 text-blue-800',
            'completed'    => 'bg-green-100 text-green-800',
            default        => 'bg-gray-100 text-gray-800',
        };
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check apakah semua checklist sudah completed
     */
    public function isAllChecklistCompleted(): bool
    {
        $total = $this->checklists()->count();
        if ($total === 0) {
            return false;
        }

        $completed = $this->checklists()
                          ->where('is_completed', true)
                          ->count();

        return $total === $completed;
    }

    /**
     * Auto-mark as completed jika semua checklist done
     */
    public function autoCompleteIfReady(): void
    {
        if ($this->isAllChecklistCompleted() && $this->status !== 'completed') {
            $this->update(['status' => 'completed']);
        }
    }
}
```

### File: `app/Models/TaskChecklist.php` (New)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'tugas_id',
        'deskripsi',
        'is_completed',
        'urutan',
        'completed_at',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class);
    }
}
```

---

## Bagian 3: CONTROLLER IMPLEMENTATION

### File: `app/Http/Controllers/Lapangan/TugasController.php`

```php
<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TaskChecklist;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TugasController extends Controller
{
    /**
     * Display Kanban board dengan tugas-tugas
     * GET /lapangan/tugas
     */
    public function index()
    {
        $activeMenu = 'tugas';
        $pesanans = Pesanan::where('status', '!=', 'dibatalkan')->get();
        
        // Ambil tugas per status
        $pending = Tugas::where('status', 'pending')
                        ->with(['pic', 'pesanan', 'checklists'])
                        ->get();
        
        $inProgress = Tugas::where('status', 'in_progress')
                           ->with(['pic', 'pesanan', 'checklists'])
                           ->get();
        
        $completed = Tugas::where('status', 'completed')
                          ->with(['pic', 'pesanan', 'checklists'])
                          ->get();

        return view('lapangan.modules.tugas', compact(
            'activeMenu',
            'pesanans',
            'pending',
            'inProgress',
            'completed'
        ));
    }

    /**
     * Store tugas baru
     * POST /lapangan/tugas
     */
    public function store(Request $request): JsonResponse
    {
        // Validasi
        $validated = $request->validate([
            'pesanan_id'    => 'required|exists:pesanans,id',
            'nama_tugas'    => 'required|string|max:255',
            'kategori'      => 'required|in:vendor_coordination,team_coordination,venue_setup,catering,documentation',
            'prioritas'     => 'required|in:low,medium,high,critical',
            'deadline'      => 'required|date',
            'catatan'       => 'nullable|string',
            'checklists'    => 'nullable|array',
            'checklists.*'  => 'string|max:500',
        ]);

        // Create tugas
        $tugas = Tugas::create([
            'pesanan_id' => $validated['pesanan_id'],
            'user_id'    => auth()->id(),
            'pic_id'     => $request->input('pic_id', auth()->id()),
            'nama_tugas' => $validated['nama_tugas'],
            'kategori'   => $validated['kategori'],
            'prioritas'  => $validated['prioritas'],
            'deadline'   => $validated['deadline'],
            'catatan'    => $validated['catatan'] ?? null,
            'status'     => 'pending',
        ]);

        // Create checklists jika ada
        if (!empty($validated['checklists'])) {
            foreach ($validated['checklists'] as $index => $checklist) {
                TaskChecklist::create([
                    'tugas_id'  => $tugas->id,
                    'deskripsi' => $checklist,
                    'urutan'    => $index,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Tugas berhasil dibuat',
            'data'    => $tugas->load(['checklists']),
        ]);
    }

    /**
     * Update status tugas (drag-drop)
     * PATCH /lapangan/tugas/{id}/status
     */
    public function updateStatus(Tugas $tugas, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $tugas->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Status tugas diperbarui',
            'data'    => $tugas->load(['checklists']),
        ]);
    }

    /**
     * Toggle checklist item
     * PATCH /lapangan/tugas/{tugas}/checklist/{checklistId}
     */
    public function updateChecklist(
        Tugas $tugas,
        TaskChecklist $checklist,
        Request $request
    ): JsonResponse
    {
        // Pastikan checklist milik tugas ini
        if ($checklist->tugas_id !== $tugas->id) {
            return response()->json([
                'success' => false,
                'message' => 'Checklist tidak ditemukan',
            ], 404);
        }

        $validated = $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        // Update checklist
        $checklist->update([
            'is_completed' => $validated['is_completed'],
            'completed_at' => $validated['is_completed'] 
                ? now() 
                : null,
        ]);

        // Auto-complete tugas jika semua checklist done
        $tugas->autoCompleteIfReady();

        // Return updated tugas dengan persentase
        $tugas->refresh()->load(['checklists']);

        return response()->json([
            'success'              => true,
            'message'              => 'Checklist diperbarui',
            'progress_percentage'  => $tugas->progress_percentage,
            'is_all_completed'     => $tugas->isAllChecklistCompleted(),
            'data'                 => $tugas,
        ]);
    }

    /**
     * Delete tugas
     * DELETE /lapangan/tugas/{id}
     */
    public function destroy(Tugas $tugas): JsonResponse
    {
        $tugas->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tugas dihapus',
        ]);
    }

    /**
     * Get detailed view (untuk detail modal atau section)
     * GET /lapangan/tugas/{id}/detail
     */
    public function detail(Tugas $tugas)
    {
        return view('lapangan.modules.tugas-detail', [
            'tugas' => $tugas->load(['pic', 'pesanan', 'checklists', 'user']),
        ]);
    }
}
```

---

## Bagian 4: ROUTES

### File: `routes/web.php` (Add to lapangan group)

```php
// TUGAS KANBAN ROUTES
Route::middleware(['auth', 'role:lapangan'])->group(function () {
    Route::prefix('lapangan')->group(function () {
        
        // Tugas Kanban
        Route::get('/tugas', [TugasController::class, 'index'])
             ->name('tugas.index');
        
        Route::post('/tugas', [TugasController::class, 'store'])
             ->name('tugas.store');
        
        Route::patch('/tugas/{tugas}/status', [TugasController::class, 'updateStatus'])
             ->name('tugas.updateStatus');
        
        Route::patch('/tugas/{tugas}/checklist/{checklist}', [TugasController::class, 'updateChecklist'])
             ->name('tugas.updateChecklist');
        
        Route::delete('/tugas/{tugas}', [TugasController::class, 'destroy'])
             ->name('tugas.destroy');
        
        Route::get('/tugas/{tugas}/detail', [TugasController::class, 'detail'])
             ->name('tugas.detail');
    });
});
```

---

## Bagian 5: FRONTEND INTEGRATION EXAMPLE

### HTML/Blade untuk Kanban Board

```blade
<!-- resources/views/lapangan/modules/tugas.blade.php -->

@extends('layouts.lapangan')
@section('title', 'Tugas Lapangan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Tugas Lapangan</h1>
            <p class="text-sm text-gray-600 mt-1">Kelola tugas dengan sistem Kanban</p>
        </div>
        <button id="openAddTaskModal" class="px-4 py-2 bg-field text-white rounded-lg hover:bg-fieldHover transition">
            + Tambah Tugas
        </button>
    </div>

    <!-- Kanban Board (3 Kolom) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- PENDING COLUMN -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h2 class="font-bold text-gray-900 mb-4">Belum Dikerjakan ({{ $pending->count() }})</h2>
            <div class="space-y-3" id="kanban-pending" data-status="pending">
                @foreach($pending as $task)
                    <x-task-card :task="$task" />
                @endforeach
            </div>
        </div>

        <!-- IN PROGRESS COLUMN -->
        <div class="bg-blue-50 rounded-lg p-4">
            <h2 class="font-bold text-gray-900 mb-4">Sedang Dikerjakan ({{ $inProgress->count() }})</h2>
            <div class="space-y-3" id="kanban-in_progress" data-status="in_progress">
                @foreach($inProgress as $task)
                    <x-task-card :task="$task" />
                @endforeach
            </div>
        </div>

        <!-- COMPLETED COLUMN -->
        <div class="bg-green-50 rounded-lg p-4">
            <h2 class="font-bold text-gray-900 mb-4">Selesai ({{ $completed->count() }})</h2>
            <div class="space-y-3" id="kanban-completed" data-status="completed">
                @foreach($completed as $task)
                    <x-task-card :task="$task" />
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- TASK CARD COMPONENT -->
<div id="task-template" style="display:none;">
    <div class="bg-white rounded-lg p-4 border border-gray-200 cursor-move hover:shadow-md transition" draggable="true">
        <div class="flex items-start justify-between mb-2">
            <h3 class="font-semibold text-gray-900">{{ task.nama_tugas }}</h3>
            <span class="px-2 py-1 text-xs rounded {{ task.priority_color }}">{{ task.prioritas }}</span>
        </div>
        
        <!-- Progress Bar -->
        <div class="mb-3">
            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full bg-field transition-all" style="width:{{ task.progress_percentage }}%"></div>
            </div>
            <p class="text-xs text-gray-600 mt-1">{{ task.progress_percentage }}% selesai</p>
        </div>
        
        <!-- Checklists preview -->
        <div class="space-y-1 mb-3 text-sm">
            @foreach(task.checklists as $check)
            <div class="flex items-center gap-2">
                <input type="checkbox" {{ $check['is_completed'] ? 'checked' : '' }} 
                       onchange="updateChecklist({{ task.id }}, {{ $check['id'] }})">
                <span class="{{ $check['is_completed'] ? 'line-through text-gray-500' : 'text-gray-700' }}">
                    {{ $check['deskripsi'] }}
                </span>
            </div>
            @endforeach
        </div>
        
        <div class="text-xs text-gray-500">
            PIC: {{ task.pic.name }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Drag & Drop implementation
    document.querySelectorAll('[data-status]').forEach(column => {
        column.addEventListener('dragover', e => {
            e.preventDefault();
            column.classList.add('bg-opacity-50');
        });

        column.addEventListener('drop', e => {
            e.preventDefault();
            const taskId = e.dataTransfer.getData('taskId');
            const status = column.getAttribute('data-status');
            
            // AJAX update status
            fetch(`/lapangan/tugas/${taskId}/status`, {
                method: 'PATCH',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh untuk update
                }
            });
        });
    });

    // Checklist toggle
    function updateChecklist(taskId, checklistId) {
        const checkbox = event.target;
        fetch(`/lapangan/tugas/${taskId}/checklist/${checklistId}`, {
            method: 'PATCH',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ is_completed: checkbox.checked })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Update progress bar
                document.querySelector(`#task-${taskId} .progress-bar`)
                    .style.width = data.progress_percentage + '%';
            }
        });
    }
</script>
@endpush
```

---

## Quick Reference

**Alur Kanban:**
1. User buka `/lapangan/tugas` → Lihat 3 kolom kanban
2. Klik "+ Tambah Tugas" → Form dengan input nama, kategori, deadline, checklists
3. Submit → POST /lapangan/tugas → Tugas masuk ke "Belum Dikerjakan"
4. Drag tugas → PATCH /lapangan/tugas/{id}/status → Update status
5. Klik checklist → PATCH /lapangan/tugas/{id}/checklist/{id} → Toggle, auto-hitung %
6. Semua checklist done? → Auto set status "completed"

**Database:**
- `tugas` table: id, pesanan_id, user_id, pic_id, nama_tugas, kategori, prioritas, status, deadline, catatan
- `task_checklists` table: id, tugas_id, deskripsi, is_completed, urutan, completed_at

**Controllers:**
- `TugasController@index` → Display kanban
- `TugasController@store` → Create task + checklists
- `TugasController@updateStatus` → Drag-drop status
- `TugasController@updateChecklist` → Toggle checklist + calc %
- `TugasController@destroy` → Delete task

**Progress = (completed_checklists / total_checklists) × 100**

---

Done! ✅ Ini adalah implementasi Kanban yang lengkap dan siap diintegrasikan dengan UI Tailwind yang sudah Anda buat.
