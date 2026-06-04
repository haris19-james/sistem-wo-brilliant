<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use App\Services\BookingChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        protected BookingChatService $bookingChat
    ) {}

    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');
        $staff = auth()->user();

        $threads = $this->bookingChat->threadsForStaff($staff, $filter);

        $selectedId = $request->integer('pesanan_id') ?: $threads->first()['pesanan_id'] ?? null;
        $detail = null;

        if ($selectedId) {
            $pesanan = Pesanan::find($selectedId);
            if ($pesanan) {
                $detail = $this->bookingChat->threadDetail($pesanan, $staff);
            }
        }

        return view('lapangan.modules.chat.index', [
            'activeMenu' => 'chat',
            'threads' => $threads,
            'filter' => $filter,
            'selectedPesananId' => $selectedId,
            'detail' => $detail,
        ]);
    }

    public function show(Request $request, Pesanan $pesanan): View
    {
        return redirect()->route('lapangan.chat', [
            'pesanan_id' => $pesanan->id,
            'filter' => $request->query('filter', 'all'),
        ]);
    }

    public function sendMessage(Request $request, Pesanan $pesanan): JsonResponse
    {
        $validated = $request->validate([
            'pesan' => ['required', 'string', 'max:2000'],
        ]);

        $message = $this->bookingChat->sendStaffMessage($pesanan, auth()->user(), $validated['pesan']);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $message->id,
                'text' => $message->pesan,
                'time' => $message->created_at->format('H:i'),
                'type' => 'sent',
            ],
        ]);
    }

    public function storeInternalNote(Request $request, Pesanan $pesanan): JsonResponse
    {
        $validated = $request->validate([
            'catatan' => ['required', 'string', 'max:1000'],
        ]);

        $note = $this->bookingChat->storeInternalNote($pesanan, auth()->user(), $validated['catatan']);

        return response()->json([
            'success' => true,
            'note' => [
                'id' => $note->id,
                'catatan' => $note->catatan,
                'author' => auth()->user()->name,
                'time' => $note->created_at->format('d M Y, H:i'),
            ],
        ]);
    }

    public function markAsRead(Pesanan $pesanan): JsonResponse
    {
        $this->bookingChat->markCustomerMessagesRead($pesanan, auth()->user());

        return response()->json(['success' => true]);
    }
}
