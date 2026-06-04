<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Models\TaskChecklist;
use App\Models\Tugas;
use App\Models\User;
use App\Models\Vendor;
use App\Services\PaymentDeadlineService;
use App\Services\VendorFieldTaskService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TugasController extends Controller
{
    public function __construct(
        protected VendorFieldTaskService $vendorFieldTaskService
    ) {}

    public function index(Request $request)
    {
        $korlapId = (int) auth()->id();

        $acaraList = Pesanan::query()
            ->visibleToKorlap($korlapId)
            ->aktifLapangan()
            ->orderBy('tanggal_acara')
            ->get(['id', 'nama_pasangan', 'nomor_pesanan', 'tanggal_acara']);

        $selectedPesananId = $request->filled('pesanan_id') ? (int) $request->pesanan_id : null;

        $query = Tugas::with(['pesanan', 'vendor', 'pic', 'user', 'checklists'])
            ->forKorlap($korlapId)
            ->orderBy('deadline');

        if ($selectedPesananId) {
            $query->where('pesanan_id', $selectedPesananId);
        }

        $tugas = $query->get();

        $acaraForDrawer = Pesanan::query()
            ->visibleToKorlap($korlapId)
            ->aktifLapangan()
            ->orderBy('tanggal_acara')
            ->get(['id', 'nama_pasangan', 'nomor_pesanan', 'tanggal_acara', 'jam_acara']);

        $users = User::where('role', 'lapangan')->orderBy('name')->get(['id', 'name', 'role']);

        $acaraMeta = $acaraForDrawer->mapWithKeys(function ($a) {
            $jam = $a->jam_acara ? substr((string) $a->jam_acara, 0, 5) : '12:00';

            return [
                $a->id => [
                    'tanggal' => $a->tanggal_acara?->format('Y-m-d') ?? '',
                    'jam' => $jam,
                ],
            ];
        });

        return view('lapangan.modules.tugas', [
            'activeMenu' => 'tugas',
            'tugas' => $tugas,
            'acaraList' => $acaraList,
            'acaraForDrawer' => $acaraForDrawer,
            'acaraMeta' => $acaraMeta,
            'users' => $users,
            'selectedPesananId' => $selectedPesananId,
            'openDrawer' => $request->boolean('open_drawer'),
        ]);
    }

    public function create(Request $request)
    {
        return redirect()->route('lapangan.tugas.index', array_filter([
            'pesanan_id' => $request->integer('pesanan_id') ?: null,
            'open_drawer' => 1,
        ]));
    }

    public function vendorsForPesanan(Pesanan $pesanan): JsonResponse
    {
        if ($pesanan->korlap_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $vendors = $pesanan->vendors()
            ->orderBy('nama_vendor')
            ->get(['vendors.id', 'nama_vendor', 'kategori']);

        return response()->json([
            'vendors' => $vendors->map(fn (Vendor $v) => [
                'id' => $v->id,
                'nama_vendor' => $v->nama_vendor,
                'kategori' => $v->kategori,
            ])->values(),
        ]);
    }

    public function store(Request $request)
    {
        $wantsJson = $this->wantsJsonResponse($request);

        Log::info('[TugasController@store] Mulai simpan tugas', [
            'user_id' => auth()->id(),
            'wants_json' => $wantsJson,
            'pesanan_id' => $request->input('pesanan_id'),
        ]);

        try {
            $validated = $this->validateTask($request);
            Log::info('[TugasController@store] Validasi lolos', ['pesanan_id' => $validated['pesanan_id']]);

            $pesanan = Pesanan::findOrFail($validated['pesanan_id']);
            $this->authorizePesanan($pesanan);

            if (PaymentDeadlineService::isKorlapFrozen($pesanan)) {
                $message = 'Akses dibekukan karena customer melewati batas waktu pelunasan.';

                return $wantsJson
                    ? response()->json(['message' => $message], 403)
                    : back()->with('error', $message);
            }

            if (! $pesanan->vendors()->where('vendor_id', (int) $validated['vendor_id'])->exists()) {
                $message = 'Vendor harus terdaftar pada acara yang dipilih.';
                Log::warning('[TugasController@store] Vendor tidak ada di pesanan', [
                    'pesanan_id' => $pesanan->id,
                    'vendor_id' => $validated['vendor_id'],
                ]);

                return $wantsJson
                    ? response()->json(['message' => $message], 422)
                    : back()->withInput()->withErrors(['vendor_id' => $message]);
            }

            $deadline = $this->parseDeadline($validated['deadline_date'], $validated['deadline_time'], $pesanan);

            $tugas = Tugas::create([
                'user_id' => auth()->id(),
                'pesanan_id' => $validated['pesanan_id'],
                'vendor_id' => $validated['vendor_id'],
                'pic_id' => $validated['pic_id'],
                'nama_tugas' => $validated['nama_tugas'],
                'kategori' => $validated['kategori'],
                'prioritas' => $validated['prioritas'],
                'deadline' => $deadline,
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'pending',
                'is_auto_generated' => false,
            ]);

            Log::info('[TugasController@store] Tugas tersimpan di database', ['tugas_id' => $tugas->id]);

            $this->syncChecklists($request, $tugas);

            if ($wantsJson) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tugas berhasil ditambahkan.',
                    'tugas_id' => $tugas->id,
                    'pesanan_id' => $pesanan->id,
                ]);
            }

            return redirect()
                ->route('lapangan.tugas.index', ['pesanan_id' => $pesanan->id])
                ->with('success', 'Tugas ad-hoc berhasil ditambahkan.');
        } catch (ValidationException $e) {
            Log::warning('[TugasController@store] Validasi gagal', ['errors' => $e->errors()]);

            throw $e;
        } catch (\Throwable $e) {
            Log::error('[TugasController@store] Gagal menyimpan tugas', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($wantsJson) {
                return response()->json([
                    'message' => 'Gagal menyimpan tugas: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Gagal menyimpan tugas. Silakan coba lagi.');
        }
    }

    public function edit(Tugas $tugas)
    {
        $this->authorize('update', $tugas);

        $korlapId = (int) auth()->id();

        $acara = Pesanan::query()
            ->visibleToKorlap($korlapId)
            ->aktifLapangan()
            ->with('vendors')
            ->orderBy('tanggal_acara')
            ->get(['id', 'nama_pasangan', 'nomor_pesanan', 'tanggal_acara', 'jam_acara', 'korlap_id']);

        $users = User::where('role', 'lapangan')->orderBy('name')->get();
        $tugas->load(['checklists', 'vendor']);

        $vendors = $tugas->pesanan?->vendors ?? collect();

        return view('lapangan.modules.tugas_form', [
            'activeMenu' => 'tugas',
            'tugas' => $tugas,
            'acara' => $acara,
            'users' => $users,
            'vendors' => $vendors,
            'preselectedPesanan' => $tugas->pesanan_id,
        ]);
    }

    public function update(Request $request, Tugas $tugas)
    {
        $this->authorize('update', $tugas);

        if ($tugas->pesanan && PaymentDeadlineService::isKorlapFrozen($tugas->pesanan)) {
            return back()->with('error', 'Akses dibekukan karena customer melewati batas waktu pelunasan.');
        }

        $validated = $this->validateTask($request);
        $pesanan = Pesanan::findOrFail($validated['pesanan_id']);
        $this->authorizePesanan($pesanan);
        $this->assertVendorOnPesanan($pesanan, (int) $validated['vendor_id']);

        $deadline = $this->parseDeadline($validated['deadline_date'], $validated['deadline_time'], $pesanan);

        $tugas->update([
            'pesanan_id' => $validated['pesanan_id'],
            'vendor_id' => $validated['vendor_id'],
            'pic_id' => $validated['pic_id'],
            'nama_tugas' => $validated['nama_tugas'],
            'kategori' => $validated['kategori'],
            'prioritas' => $validated['prioritas'],
            'deadline' => $deadline,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        $tugas->checklists()->delete();
        $this->syncChecklists($request, $tugas);

        return redirect()
            ->route('lapangan.tugas.index', ['pesanan_id' => $pesanan->id])
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    public function destroy(Tugas $tugas)
    {
        $this->authorize('delete', $tugas);

        if ($tugas->is_auto_generated) {
            return back()->with('error', 'Tugas rutin otomatis tidak dapat dihapus. Gunakan verifikasi penyelesaian.');
        }

        $pesananId = $tugas->pesanan_id;
        $tugas->delete();

        return redirect()
            ->route('lapangan.tugas.index', ['pesanan_id' => $pesananId])
            ->with('success', 'Tugas berhasil dihapus.');
    }

    public function updateStatus(Request $request, Tugas $tugas): JsonResponse
    {
        $this->authorize('update', $tugas);

        if ($tugas->pesanan && PaymentDeadlineService::isKorlapFrozen($tugas->pesanan)) {
            return response()->json(['message' => 'Akses dibekukan.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,awaiting_verification',
        ]);

        if ($validated['status'] === 'awaiting_verification') {
            $total = $tugas->checklists()->count();
            $done = $tugas->checklists()->where('is_completed', true)->count();
            if ($total === 0 || $done < $total) {
                return response()->json([
                    'message' => 'Semua checklist harus selesai sebelum menunggu verifikasi.',
                ], 422);
            }
        }

        $tugas->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'status' => $tugas->status,
            'status_label' => $tugas->status_label,
            'progress' => $tugas->progress,
        ]);
    }

    public function verifyComplete(Tugas $tugas): JsonResponse
    {
        $this->authorize('verify', $tugas);

        if ($tugas->pesanan && PaymentDeadlineService::isKorlapFrozen($tugas->pesanan)) {
            return response()->json(['message' => 'Akses dibekukan.'], 403);
        }

        $tugas->update([
            'status' => 'completed',
            'korlap_verified_at' => now(),
        ]);

        if ($tugas->pesanan && $tugas->vendor) {
            $this->vendorFieldTaskService->syncVendorPerformance($tugas->pesanan, $tugas->vendor);
        }

        return response()->json([
            'success' => true,
            'status' => $tugas->status,
            'status_label' => $tugas->status_label,
            'message' => 'Tugas diverifikasi selesai oleh Korlap.',
        ]);
    }

    public function updateChecklist(Request $request, Tugas $tugas, TaskChecklist $checklist): JsonResponse
    {
        $this->authorize('update', $tugas);

        if ($tugas->pesanan && PaymentDeadlineService::isKorlapFrozen($tugas->pesanan)) {
            return response()->json(['message' => 'Akses dibekukan.'], 403);
        }

        if ($tugas->status === 'completed') {
            return response()->json(['message' => 'Tugas sudah diverifikasi selesai.'], 422);
        }

        $validated = $request->validate([
            'is_completed' => 'required|boolean',
        ]);

        $checklist->update([
            'is_completed' => $validated['is_completed'],
            'completed_at' => $validated['is_completed'] ? now() : null,
        ]);

        if ($validated['is_completed'] && $tugas->status === 'pending') {
            $tugas->update(['status' => 'in_progress']);
        }

        $tugas->refresh();
        $tugas->autoCompleteIfReady();
        $tugas->refresh();

        return response()->json([
            'success' => true,
            'is_completed' => $checklist->is_completed,
            'progress' => $tugas->progress,
            'task_status' => $tugas->status,
            'task_status_label' => $tugas->status_label,
        ]);
    }

    public function detail(Tugas $tugas): JsonResponse
    {
        $this->authorize('view', $tugas);

        $tugas->load(['checklists', 'pesanan', 'vendor', 'pic', 'user']);

        return response()->json([
            'id' => $tugas->id,
            'nama_tugas' => $tugas->nama_tugas,
            'status' => $tugas->status,
            'status_label' => $tugas->status_label,
            'progress' => $tugas->progress,
            'prioritas' => $tugas->prioritas,
            'kategori' => $tugas->kategori,
            'is_auto_generated' => $tugas->is_auto_generated,
            'vendor_nama' => $tugas->vendor?->nama_vendor,
            'deadline' => $tugas->deadline?->format('Y-m-d H:i'),
            'pic_name' => $tugas->pic?->name,
            'catatan' => $tugas->catatan,
            'pesanan_nama' => $tugas->pesanan?->nama_pasangan,
            'checklists' => $tugas->checklists->map(fn ($c) => [
                'id' => $c->id,
                'deskripsi' => $c->deskripsi,
                'is_completed' => $c->is_completed,
                'urutan' => $c->urutan,
            ])->values(),
        ]);
    }

    protected function validateTask(Request $request): array
    {
        return $request->validate([
            'nama_tugas' => 'required|string|max:255',
            'pesanan_id' => 'required|exists:pesanans,id',
            'vendor_id' => 'required|exists:vendors,id',
            'kategori' => 'required|string',
            'prioritas' => 'required|in:high,medium,low',
            'deadline_date' => 'required|date',
            'deadline_time' => 'required',
            'pic_id' => 'required|exists:users,id',
            'checklists_text' => 'array|nullable',
            'checklists_completed' => 'array|nullable',
            'catatan' => 'nullable|string|max:500',
        ]);
    }

    protected function authorizePesanan(Pesanan $pesanan): void
    {
        if ($pesanan->korlap_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke acara ini.');
        }
    }

    protected function wantsJsonResponse(Request $request): bool
    {
        return $request->expectsJson()
            || $request->ajax()
            || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    protected function assertVendorOnPesanan(Pesanan $pesanan, int $vendorId): void
    {
        if (! $pesanan->vendors()->where('vendor_id', $vendorId)->exists()) {
            abort(422, 'Vendor harus terdaftar pada acara yang dipilih.');
        }
    }

    protected function parseDeadline(string $date, string $time, Pesanan $pesanan): Carbon
    {
        try {
            return Carbon::createFromFormat('Y-m-d H:i', $date.' '.$time);
        } catch (\Throwable) {
            if ($pesanan->tanggal_acara) {
                $jam = $pesanan->jam_acara ? substr((string) $pesanan->jam_acara, 0, 5) : '12:00';

                return Carbon::parse($pesanan->tanggal_acara->format('Y-m-d').' '.$jam);
            }

            return now()->addDay();
        }
    }

    protected function syncChecklists(Request $request, Tugas $tugas): void
    {
        $checklistTexts = $request->get('checklists_text', []);
        $checklistCompleted = $request->get('checklists_completed', []);

        foreach ($checklistTexts as $index => $text) {
            if (! empty(trim($text))) {
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
    }
}
