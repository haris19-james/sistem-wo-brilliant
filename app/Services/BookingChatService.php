<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Pesanan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BookingChatService
{
    public const FILTERS = ['all', 'unread', 'active'];

    public const MESSAGE_PAGE_SIZE = 50;

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function threadsForStaff(User $staff, string $filter = 'all'): Collection
    {
        $filter = in_array($filter, self::FILTERS, true) ? $filter : 'all';

        $query = Pesanan::query()
            ->select([
                'id', 'user_id', 'paket_id', 'nomor_pesanan', 'nama_pasangan',
                'status_pemesanan', 'korlap_id', 'updated_at',
            ])
            ->with(['user:id,name,email', 'paket:id,nama_paket']);

        if ($staff->role === 'lapangan') {
            $query->where('korlap_id', $staff->id);
        }

        $query->where(function ($q) {
            $q->whereHas('chatMessages', fn ($m) => $m->where('is_internal', false))
                ->orWhereIn('status_pemesanan', ['confirmed', 'on_progress', 'success', 'completed', 'pending']);
        });

        $bookings = $query->orderByDesc('updated_at')->limit(80)->get();

        $pesananIds = $bookings->pluck('id');
        $lastMessages = $this->bulkLastMessages($pesananIds);
        $unreadCounts = $this->bulkUnreadCounts($pesananIds, $bookings->pluck('user_id', 'id'));

        return $bookings->map(fn (Pesanan $p) => $this->mapThread($p, $staff, $lastMessages, $unreadCounts))
            ->filter(function (array $thread) use ($filter) {
                if ($filter === 'unread') {
                    return $thread['unread_count'] > 0;
                }
                if ($filter === 'active') {
                    return in_array($thread['status_code'], ['confirmed', 'on_progress'], true);
                }

                return true;
            })
            ->sortByDesc('last_message_at')
            ->values();
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @return array<int, ChatMessage>
     */
    protected function bulkLastMessages(Collection $pesananIds): array
    {
        if ($pesananIds->isEmpty()) {
            return [];
        }

        $rows = ChatMessage::query()
            ->select(['id', 'pesanan_id', 'pesan', 'created_at'])
            ->whereIn('pesanan_id', $pesananIds)
            ->where('is_internal', false)
            ->whereIn('id', function ($q) use ($pesananIds) {
                $q->select(DB::raw('MAX(id)'))
                    ->from('chat_messages')
                    ->whereIn('pesanan_id', $pesananIds)
                    ->where('is_internal', false)
                    ->groupBy('pesanan_id');
            })
            ->get()
            ->keyBy('pesanan_id');

        return $rows->all();
    }

    /**
     * @param  Collection<int, int>  $pesananIds
     * @param  Collection<int, int|null>  $customerIdsByPesanan  pesanan_id => user_id
     * @return array<int, int>
     */
    protected function bulkUnreadCounts(Collection $pesananIds, Collection $customerIdsByPesanan): array
    {
        if ($pesananIds->isEmpty()) {
            return [];
        }

        return ChatMessage::query()
            ->select('chat_messages.pesanan_id', DB::raw('COUNT(*) as total'))
            ->join('pesanans', 'pesanans.id', '=', 'chat_messages.pesanan_id')
            ->whereIn('chat_messages.pesanan_id', $pesananIds)
            ->where('chat_messages.is_internal', false)
            ->where('chat_messages.is_read', false)
            ->whereColumn('chat_messages.sender_id', 'pesanans.user_id')
            ->groupBy('chat_messages.pesanan_id')
            ->pluck('total', 'chat_messages.pesanan_id')
            ->map(fn ($c) => (int) $c)
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function mapThread(
        Pesanan $p,
        User $staff,
        ?array $lastMessages = null,
        ?array $unreadCounts = null
    ): array {
        if ($lastMessages !== null) {
            $lastMessage = $lastMessages[$p->id] ?? null;
        } else {
            $lastMessage = ChatMessage::query()
                ->where('pesanan_id', $p->id)
                ->where('is_internal', false)
                ->latest()
                ->first(['id', 'pesan', 'created_at']);
        }

        if ($unreadCounts !== null) {
            $unreadCount = (int) ($unreadCounts[$p->id] ?? 0);
        } else {
            $unreadCount = (int) ChatMessage::query()
                ->where('pesanan_id', $p->id)
                ->where('is_internal', false)
                ->where('is_read', false)
                ->where('sender_id', $p->user_id)
                ->count();
        }

        $status = $this->bookingStatusMeta($p);

        return [
            'pesanan_id' => $p->id,
            'nomor_pesanan' => $p->nomor_pesanan,
            'nama_pasangan' => $p->nama_pasangan,
            'client_name' => $p->user?->name ?? '—',
            'client_email' => $p->user?->email,
            'paket_nama' => $p->paket?->nama_paket ?? '—',
            'status_code' => $p->status_pemesanan,
            'status_label' => $status['label'],
            'status_class' => $status['class'],
            'last_message' => $lastMessage?->pesan ?? 'Belum ada pesan',
            'last_message_at' => $lastMessage?->created_at?->timestamp ?? 0,
            'last_message_time' => $lastMessage?->created_at?->format('H:i') ?? '—',
            'unread_count' => (int) $unreadCount,
            'is_completed' => $status['is_completed'],
            'has_unread' => $unreadCount > 0,
        ];
    }

    public function authorizeStaffAccess(User $staff, Pesanan $pesanan): void
    {
        if ($staff->role === 'admin') {
            return;
        }

        if ($staff->role === 'lapangan' && (int) $pesanan->korlap_id === (int) $staff->id) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke chat booking ini.');
    }

    /**
     * @return array<string, mixed>
     */
    public function threadDetail(Pesanan $pesanan, User $staff): array
    {
        $this->authorizeStaffAccess($staff, $pesanan);

        $pesanan->load([
            'user:id,name,email',
            'paket:id,nama_paket,harga',
            'rundowns' => fn ($q) => $q->orderBy('waktu_mulai')->limit(6),
            'chatInternalNotes.author:id,name',
        ]);

        $messages = ChatMessage::query()
            ->select(['id', 'pesanan_id', 'user_id', 'sender_id', 'pesan', 'dari_admin', 'is_read', 'created_at'])
            ->where('pesanan_id', $pesanan->id)
            ->where('is_internal', false)
            ->with(['sender:id,name,role', 'user:id,name,role', 'pesanan:id,user_id'])
            ->orderByDesc('created_at')
            ->limit(self::MESSAGE_PAGE_SIZE)
            ->get()
            ->reverse()
            ->values()
            ->map(fn (ChatMessage $m) => $this->mapMessage($m, $staff));

        $this->markCustomerMessagesRead($pesanan, $staff);

        $status = $this->bookingStatusMeta($pesanan);

        return [
            'thread' => $this->mapThread($pesanan, $staff),
            'booking' => [
                'id' => $pesanan->id,
                'nomor' => $pesanan->nomor_pesanan,
                'nama_pasangan' => $pesanan->nama_pasangan,
                'client_name' => $pesanan->user?->name,
                'client_email' => $pesanan->user?->email,
                'paket' => $pesanan->paket?->nama_paket ?? '—',
                'tanggal_acara' => $pesanan->tanggal_acara?->translatedFormat('d M Y'),
                'jam_acara' => $pesanan->jam_acara ? substr((string) $pesanan->jam_acara, 0, 5) : null,
                'lokasi' => $pesanan->lokasi,
                'status_label' => $status['label'],
                'status_class' => $status['class'],
                'status_code' => $pesanan->status_pemesanan,
                'is_completed' => $status['is_completed'],
                'rundown' => $pesanan->rundowns->map(fn ($r) => [
                    'waktu' => $r->waktu_mulai ? substr((string) $r->waktu_mulai, 0, 5) : '—',
                    'kegiatan' => $r->kegiatan ?? '—',
                ])->values()->all(),
            ],
            'messages' => $messages,
            'internal_notes' => $pesanan->chatInternalNotes->map(fn ($n) => [
                'id' => $n->id,
                'catatan' => $n->catatan,
                'author' => $n->author?->name ?? 'Tim',
                'time' => $n->created_at->format('d M Y, H:i'),
            ])->values()->all(),
            'show_review_banner' => $status['is_completed'],
        ];
    }

    public function sendStaffMessage(Pesanan $pesanan, User $staff, string $pesan): ChatMessage
    {
        $this->authorizeStaffAccess($staff, $pesanan);

        $customerId = $pesanan->user_id;
        if (! $customerId) {
            abort(422, 'Booking tidak memiliki klien terdaftar.');
        }

        return ChatMessage::create([
            'pesanan_id' => $pesanan->id,
            'booking_id' => $pesanan->id,
            'user_id' => $staff->id,
            'sender_id' => $staff->id,
            'receiver_id' => $customerId,
            'pesan' => $pesan,
            'dari_admin' => $staff->role === 'admin',
            'is_internal' => false,
            'is_read' => false,
        ]);
    }

    public function storeInternalNote(Pesanan $pesanan, User $staff, string $catatan): \App\Models\ChatInternalNote
    {
        $this->authorizeStaffAccess($staff, $pesanan);

        return \App\Models\ChatInternalNote::create([
            'pesanan_id' => $pesanan->id,
            'author_id' => $staff->id,
            'catatan' => $catatan,
        ]);
    }

    public function markCustomerMessagesRead(Pesanan $pesanan, User $staff): void
    {
        $this->authorizeStaffAccess($staff, $pesanan);

        ChatMessage::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('is_internal', false)
            ->where('sender_id', $pesanan->user_id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    /**
     * @return array{label: string, class: string, is_completed: bool}
     */
    public function bookingStatusMeta(Pesanan $p): array
    {
        $resolved = \App\Support\BookingDynamicStatus::resolve($p);

        return [
            'label' => $resolved['label'],
            'class' => $resolved['badge_class'],
            'is_completed' => $resolved['is_completed'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapMessage(ChatMessage $m, User $staff): array
    {
        $isMine = (int) $m->sender_id === (int) $staff->id
            || ((int) $m->user_id === (int) $staff->id && $m->dari_admin);

        $customerId = $m->pesanan?->user_id;
        $isCustomer = ($customerId && (int) $m->sender_id === (int) $customerId)
            || ($customerId && (int) $m->user_id === (int) $customerId && ! $m->dari_admin);

        if (! $m->sender_id && $m->user_id) {
            $isMine = in_array($m->user?->role, ['admin', 'lapangan'], true) && (int) $m->user_id === (int) $staff->id;
            $isCustomer = $m->user?->role === 'client';
        }

        if ($isCustomer) {
            $isMine = false;
        }

        return [
            'id' => $m->id,
            'text' => $m->pesan,
            'time' => $m->created_at->format('H:i'),
            'date' => $m->created_at->format('d M Y'),
            'type' => $isMine ? 'sent' : 'received',
            'sender_name' => $m->sender?->name ?? $m->user?->name ?? '—',
            'is_customer' => $isCustomer,
        ];
    }
}
