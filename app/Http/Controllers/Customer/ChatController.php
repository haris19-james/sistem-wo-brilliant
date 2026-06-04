<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Pesanan;
use App\Services\CustomerBookingChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function __construct(
        protected CustomerBookingChatService $customerChat
    ) {}

    public function index(Request $request): View
    {
        $customer = Auth::user();
        $bookings = $this->customerChat->customerBookings($customer);
        $pesananId = $request->integer('pesanan_id') ?: null;

        $selected = $this->customerChat->resolveBooking($customer, $pesananId, $bookings);
        if (! $selected && $bookings->isNotEmpty()) {
            $selected = $bookings->first();
        }
        $thread = $selected ? $this->customerChat->threadForCustomer($selected, $customer) : null;

        return view('customer.modules.chat.index', [
            'activeMenu' => 'chat',
            'bookings' => $bookings,
            'selectedPesanan' => $selected,
            'thread' => $thread,
        ]);
    }

    public function show(Pesanan $pesanan)
    {
        $this->authorizePesanan($pesanan);

        return redirect()->route('client.chat', ['pesanan_id' => $pesanan->id]);
    }

    public function store(Request $request, Pesanan $pesanan): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $this->authorizePesanan($pesanan);

        $validated = $request->validate([
            'pesan' => ['required', 'string', 'max:2000'],
        ]);

        $customer = Auth::user();
        $staffReceiver = $pesanan->korlap_id;

        $message = ChatMessage::create([
            'pesanan_id' => $pesanan->id,
            'booking_id' => $pesanan->id,
            'user_id' => $customer->id,
            'sender_id' => $customer->id,
            'receiver_id' => $staffReceiver,
            'pesan' => $validated['pesan'],
            'dari_admin' => false,
            'is_internal' => false,
            'is_read' => false,
        ]);

        app(\App\Services\NotificationCenterService::class)->chatFromCustomerForStaff($pesanan);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $message->id,
                    'text' => $message->pesan,
                    'time' => $message->created_at->format('H:i'),
                    'type' => 'sent',
                    'read_receipt' => 'sent',
                ],
            ]);
        }

        return redirect()
            ->route('client.chat', ['pesanan_id' => $pesanan->id])
            ->with('success', 'Pesan terkirim.');
    }

    private function authorizePesanan(Pesanan $pesanan): void
    {
        if ($pesanan->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
