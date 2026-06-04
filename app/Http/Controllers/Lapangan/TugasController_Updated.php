<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TaskChecklist;
use App\Models\Pesanan;
use App\Models\ProgressPersiapan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TugasController extends Controller
{
    /**
     * Display list of tasks assigned to current Korlap
     */
    public function index()
    {
        $tugas = Tugas::with(['pesanan', 'pic', 'user', 'checklists'])
            ->where('user_id', auth()->id())
            ->orderBy('deadline')
            ->get();

        return view('lapangan.modules.tugas', [
            'activeMenu' => 'tugas',
            'tugas' => $tugas,
        ]);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $acara = Pesanan::aktifLapangan()
            ->where('korlap_id', auth()->id())  // ✅ Filter by current Korlap
            ->with('paket')
            ->get();

        $users = User::where('role', 'lapangan')
            ->get();

        return view('lapangan.modules.tugas_form', [
            'activeMenu' => 'tugas',
            'acara' => $acara,
            'users' => $users,
        ]);
    }

    /**
     * Store new task
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'pesanan_id' => 'required|exists:pesanans,id',
            'kategori' => 'required|string',
            'prioritas' => 'required|in:high,medium,low',
            'deadline_date' => 'required|date',
            'deadline_time' => 'required',
            'pic_id' => 'required|exists:users,id',
            'checklists_text' => 'array|nullable',
            'checklists_completed' => 'array|nullable',
            'catatan' => 'nullable|string|max:500',
        ]);

        // ✅ Verify Korlap owns this pesanan
        $pesanan = Pesanan::find($validated['pesanan_id']);
        if ($pesanan->korlap_id !== auth()->id()) {
            return back()->withErrors(['pesanan_id' => 'Unauthorized']);
        }

        $deadline = Carbon::createFromFormat('Y-m-d H:i', 
            $validated['deadline_date'] . ' ' . $validated['deadline_time']);

        $tugas = Tugas::create([
            'user_id' => auth()->id(),
            'pesanan_id' => $validated['pesanan_id'],
            'pic_id' => $validated['pic_id'],
            'nama_tugas' => $validated['nama_tugas'],
            'kategori' => $validated['kategori'],
            'prioritas' => $validated['prioritas'],
            'deadline' => $deadline,
            'catatan' => $validated['catatan'],
            'status' => 'pending',
        ]);

        // Create checklists from form data
        $checklistTexts = $request->get('checklists_text', []);
        $checklistCompleted = $request->get('checklists_completed', []);
        
        foreach ($checklistTexts as $index => $text) {
            if (!empty(trim($text))) {
                $isCompleted = isset($checklistCompleted[$index]) && $checklistCompleted[$index] == '1';
                TaskChecklist::create([
                    'tugas_id' => $tugas->id,
                    'deskripsi' => trim($text),
                    'is_completed' => $isCompleted,
                    'urutan' => $index,
                    'completed_at' => $isCompleted ? now() : null,
                ]);
            }
        }

        return redirect()->route('lapangan.tugas.index')
            ->with('success', 'Tugas berhasil ditambahkan');
    }

    /**
     * Show edit form
     */
    public function edit(Tugas $tugas)
    {
        $this->authorize('update', $tugas);

        $acara = Pesanan::aktifLapangan()
            ->where('korlap_id', auth()->id())  // ✅ Filter by current Korlap
            ->with('paket')
            ->get();

        $users = User::where('role', 'lapangan')->get();

        return view('lapangan.modules.tugas_form', [
            'activeMenu' => 'tugas',
            'tugas' => $tugas->load('checklists'),
            'acara' => $acara,
            'users' => $users,
        ]);
    }

    /**
     * Update task
     */
    public function update(Request $request, Tugas $tugas)
    {
        $this->authorize('update', $tugas);

        $validated = $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'pesanan_id' => 'required|exists:pesanans,id',
            'kategori' => 'required|string',
            'prioritas' => 'required|in:high,medium,low',
            'deadline_date' => 'required|date',
            'deadline_time' => 'required',
            'pic_id' => 'required|exists:users,id',
            'checklists_text' => 'array|nullable',
            'checklists_completed' => 'array|nullable',
            'catatan' => 'nullable|string|max:500',
        ]);

        $deadline = Carbon::createFromFormat('Y-m-d H:i',
            $validated['deadline_date'] . ' ' . $validated['deadline_time']);

        $tugas->update([
            'pesanan_id' => $validated['pesanan_id'],
            'pic_id' => $validated['pic_id'],
            'nama_tugas' => $validated['nama_tugas'],
            'kategori' => $validated['kategori'],
            'prioritas' => $validated['prioritas'],
            'deadline' => $deadline,
            'catatan' => $validated['catatan'],
        ]);

        // Update checklists
        $tugas->checklists()->delete();

        $checklistTexts = $request->get('checklists_text', []);
        $checklistCompleted = $request->get('checklists_completed', []);
        
        foreach ($checklistTexts as $index => $text) {
            if (!empty(trim($text))) {
                $isCompleted = isset($checklistCompleted[$index]) && $checklistCompleted[$index] == '1';
                TaskChecklist::create([
                    'tugas_id' => $tugas->id,
                    'deskripsi' => trim($text),
                    'is_completed' => $isCompleted,
                    'urutan' => $index,
                    'completed_at' => $isCompleted ? now() : null,
                ]);
            }
        }

        return redirect()->route('lapangan.tugas.index')
            ->with('success', 'Tugas berhasil diperbarui');
    }

    /**
     * Update checklist item (with progress sync to booking)
     * This is the CRITICAL method for real-time progress tracking
     */
    public function updateChecklist(Request $request, Tugas $tugas, TaskChecklist $checklist): JsonResponse
    {
        $this->authorize('update', $tugas);

        $validated = $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        // Update checklist status
        $checklist->update([
            'is_completed' => $validated['is_completed'],
            'completed_at' => $validated['is_completed'] ? now() : null,
        ]);

        // Auto-complete task if all checklists are done
        $tugas->autoCompleteIfReady();

        // ✅ SYNC TASK PROGRESS TO BOOKING PROGRESS TABLE
        $this->syncTaskProgressToBooking($tugas);

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

    /**
     * Get task detail with all checklists (for Kanban modal)
     */
    public function detail(Tugas $tugas): JsonResponse
    {
        $this->authorize('view', $tugas);

        $tugas->load('checklists', 'pic', 'pesanan');

        return response()->json([
            'id' => $tugas->id,
            'nama_tugas' => $tugas->nama_tugas,
            'kategori' => $tugas->kategori,
            'prioritas' => $tugas->prioritas,
            'status' => $tugas->status,
            'progress_percent' => $tugas->progress,
            'deadline' => $tugas->deadline->format('d M Y H:i'),
            'pic' => $tugas->pic?->name,
            'pesanan' => $tugas->pesanan?->nama_pasangan,
            'checklists' => $tugas->checklists->map(fn($c) => [
                'id' => $c->id,
                'deskripsi' => $c->deskripsi,
                'is_completed' => $c->is_completed,
                'urutan' => $c->urutan,
            ]),
        ]);
    }

    /**
     * Update task status (Kanban drag-drop)
     */
    public function updateStatus(Request $request, Tugas $tugas): JsonResponse
    {
        $this->authorize('update', $tugas);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,completed',
        ]);

        $tugas->update(['status' => $validated['status']]);

        // Sync to booking progress
        $this->syncTaskProgressToBooking($tugas);

        return response()->json([
            'success' => true,
            'message' => 'Status tugas berhasil diubah',
            'task' => [
                'id' => $tugas->id,
                'status' => $tugas->status,
            ]
        ]);
    }

    /**
     * CRITICAL: Sync task progress to booking progress tracking
     * This ensures Customer can see real-time progress on their dashboard
     */
    private function syncTaskProgressToBooking(Tugas $tugas): void
    {
        $pesanan = $tugas->pesanan;
        if (!$pesanan) return;

        // Get ALL tasks for this pesanan
        $allTasks = $pesanan->tugas()->with('checklists')->get();
        
        if ($allTasks->isEmpty()) return;

        // Calculate average progress across all tasks
        $totalProgress = 0;
        foreach ($allTasks as $task) {
            $totalProgress += $task->progress;
        }
        
        $avgProgress = (int) ($totalProgress / $allTasks->count());

        // Update ProgressPersiapan table (used by Customer dashboard)
        $progress = ProgressPersiapan::firstOrCreate(
            ['pesanan_id' => $pesanan->id],
            ['persentase' => 0]
        );

        $progress->update(['persentase' => $avgProgress]);
    }

    /**
     * Delete task
     */
    public function destroy(Tugas $tugas)
    {
        $this->authorize('delete', $tugas);

        $tugas->checklists()->delete();
        $tugas->delete();

        return redirect()->route('lapangan.tugas.index')
            ->with('success', 'Tugas berhasil dihapus');
    }
}
