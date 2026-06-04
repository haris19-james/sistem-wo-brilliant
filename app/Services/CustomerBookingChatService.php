<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CustomerBookingChatService
{
    public function __construct(
        protected BookingChatService $bookingChat
    ) {}

    /**
     * @return Collection<int, Pesanan>
     */
    public function customerBookings(User $customer): Collection
    {
        return Pesanan::query()
            ->where('user_id', $customer->id)
            ->with(['paket:id,nama_paket'])
            ->orderByDesc('tanggal_acara')
            ->orderByDesc('created_at')
            ->get();
    }

    public function resolveBooking(User $customer, ?int $pesananId, ?Collection $bookings = null): ?Pesanan
    {
        $bookings ??= $this->customerBookings($customer);

        if ($bookings->isEmpty()) {
            return null;
        }

        if ($pesananId) {
            return $bookings->firstWhere('id', $pesananId);
        }

        $ids = $bookings->pluck('id');
        $unreadPesananId = ChatMessage::query()
            ->select('pesanan_id')
            ->whereIn('pesanan_id', $ids)
            ->where('is_internal', false)
            ->where('is_read', false)
            ->where(function ($q) use ($customer) {
                $q->where('dari_admin', true)
                    ->orWhere(fn ($q2) => $q2->whereNotNull('sender_id')->where('sender_id', '!=', $customer->id));
            })
            ->orderByDesc('id')
            ->value('pesanan_id');

        if ($unreadPesananId) {
            return $bookings->firstWhere('id', $unreadPesananId);
        }

        return $bookings->first();
    }

    public function unreadCountForCustomer(User $customer): int
    {
        return (int) Cache::remember(
            'customer.chat.unread.'.$customer->id,
            now()->addSeconds(30),
            fn () => ChatMessage::query()
                ->where('is_internal', false)
                ->where('is_read', false)
                ->whereHas('pesanan', fn ($q) => $q->where('user_id', $customer->id))
                ->where(function ($q) use ($customer) {
                    $q->where('dari_admin', true)
                        ->orWhere(fn ($q2) => $q2->whereNotNull('sender_id')->where('sender_id', '!=', $customer->id));
                })
                ->count()
        );
    }

    public function unreadCountForBooking(Pesanan $pesanan, User $customer): int
    {
        if ((int) $pesanan->user_id !== (int) $customer->id) {
            return 0;
        }

        return $this->staffToCustomerQuery($customer->id, collect([$pesanan->id]))->count();
    }

    /**
     * @return array<string, mixed>|null
     */
    public function threadForCustomer(Pesanan $pesanan, User $customer): ?array
    {
        if ((int) $pesanan->user_id !== (int) $customer->id) {
            return null;
        }

        $pesanan->load(['paket:id,nama_paket', 'user:id,name']);

        $this->markStaffMessagesRead($pesanan, $customer);

        $messages = ChatMessage::query()
            ->select(['id', 'pesanan_id', 'user_id', 'sender_id', 'pesan', 'dari_admin', 'is_read', 'created_at'])
            ->where('pesanan_id', $pesanan->id)
            ->where('is_internal', false)
            ->with(['sender:id,name,role', 'user:id,name,role'])
            ->orderByDesc('created_at')
            ->limit(BookingChatService::MESSAGE_PAGE_SIZE)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $m) => $this->mapMessageForCustomer($m, $customer));

        $status = $this->bookingChat->bookingStatusMeta($pesanan);

        return [
            'pesanan_id' => $pesanan->id,
            'booking' => [
                'nomor' => $pesanan->nomor_pesanan,
                'nama_acara' => $pesanan->nama_pasangan,
                'paket' => $pesanan->paket?->nama_paket ?? '—',
                'tanggal' => $pesanan->tanggal_acara?->translatedFormat('d F Y'),
                'status_label' => $status['label'],
                'status_class' => $status['class'],
                'is_completed' => $status['is_completed'],
            ],
            'messages' => $messages,
            'review_url' => route('client.pesanan_detail', $pesanan->id).'#review-vendor',
            'show_review_cta' => $status['is_completed'],
        ];
    }

    public function markStaffMessagesRead(Pesanan $pesanan, User $customer): void
    {
        if ((int) $pesanan->user_id !== (int) $customer->id) {
            return;
        }

        $this->staffToCustomerQuery($customer->id, collect([$pesanan->id]))
            ->update(['is_read' => true]);
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     */
    protected function staffToCustomerQuery(int $customerId, Collection $pesananIds)
    {
        return ChatMessage::query()
            ->whereIn('pesanan_id', $pesananIds)
            ->where('is_internal', false)
            ->where('is_read', false)
            ->where(function ($q) use ($customerId) {
                $q->where('dari_admin', true)
                    ->orWhere(function ($q2) use ($customerId) {
                        $q2->whereNotNull('sender_id')
                            ->where('sender_id', '!=', $customerId);
                    });
            });
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapMessageForCustomer(ChatMessage $m, User $customer): array
    {
        $isMine = (int) $m->sender_id === (int) $customer->id
            || ((int) $m->user_id === (int) $customer->id && ! $m->dari_admin);

        if (! $m->sender_id && $m->dari_admin) {
            $isMine = false;
        }

        $staffName = 'Tim Brilliant WO';
        if ($m->sender?->role === 'lapangan') {
            $staffName = 'Koordinator Lapangan';
        } elseif ($m->sender?->role === 'admin') {
            $staffName = 'Admin Brilliant WO';
        }

        return [
            'id' => $m->id,
            'text' => $m->pesan,
            'time' => $m->created_at->format('H:i'),
            'date' => $m->created_at->format('d M Y'),
            'type' => $isMine ? 'sent' : 'received',
            'sender_label' => $isMine ? 'Anda' : $staffName,
            'is_read' => (bool) $m->is_read,
            'read_receipt' => $isMine ? ($m->is_read ? 'read' : 'sent') : null,
        ];
    }
}
