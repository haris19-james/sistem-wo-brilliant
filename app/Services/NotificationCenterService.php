<?php

namespace App\Services;

use App\Models\ItemTambahan;
use App\Models\Pesanan;
use App\Models\User;
use App\Models\UserNotification;
use App\Notifications\DatabaseUserNotification;
use Illuminate\Support\Collection;

class NotificationCenterService
{
    public function notifyUser(
        User|int $user,
        string $message,
        ?string $linkRedirect = null,
        string $priority = 'normal',
        ?string $category = null
    ): UserNotification {
        $userModel = $user instanceof User ? $user : User::findOrFail($user);

        $notification = UserNotification::create([
            'user_id' => $userModel->id,
            'role' => $userModel->role,
            'message' => $message,
            'is_read' => false,
            'link_redirect' => $linkRedirect,
            'priority' => $priority,
            'category' => $category,
        ]);

        try {
            broadcast(new \App\Events\NotificationBroadcast(
                'notification_created',
                [$userModel->role],
                $message,
                null,
                $category ?? 'general',
                $priority,
                $linkRedirect,
                ['notification_id' => $notification->id]
            ));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed to broadcast notification', [
                'user_id' => $userModel->id,
                'error' => $e->getMessage(),
            ]);
        }

        if ($priority === 'urgent' && auth()->id() === $userModel->id) {
            session()->flash('urgent_toast', [
                'message' => $message,
                'type' => 'urgent',
            ]);
        }

        try {
            $userModel->notify(new DatabaseUserNotification(
                $message,
                $linkRedirect,
                $category,
                $priority,
                [
                    'reference_type' => $category,
                    'reference_id' => $notification->id,
                ]
            ));
        } catch (\Throwable $e) {
            // If database notifications are unavailable, proceed with custom notification storage only.
            \Illuminate\Support\Facades\Log::warning('Failed to persist standard database notification', [
                'user_id' => $userModel->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $notification;
    }

    /**
     * @param  array<int, User|int>  $users
     */
    public function notifyMany(array $users, string $message, ?string $linkRedirect = null, string $priority = 'normal', ?string $category = null): void
    {
        foreach ($users as $user) {
            $this->notifyUser($user, $message, $linkRedirect, $priority, $category);
        }
    }

    public function notifyRole(string $role, string $message, ?string $linkRedirect = null, string $priority = 'normal', ?string $category = null): void
    {
        $ids = User::query()->where('role', $role)->pluck('id');
        foreach ($ids as $id) {
            $this->notifyUser((int) $id, $message, $linkRedirect, $priority, $category);
        }
    }

    public function notifyAdmins(string $message, ?string $linkRedirect = null, string $priority = 'normal', ?string $category = null): void
    {
        $this->notifyRole('admin', $message, $linkRedirect, $priority, $category);
    }

    public function notifyKorlapForPesanan(Pesanan $pesanan, string $message, ?string $linkRedirect = null, string $priority = 'normal', ?string $category = null): void
    {
        if (! $pesanan->korlap_id) {
            return;
        }

        $this->notifyUser((int) $pesanan->korlap_id, $message, $linkRedirect, $priority, $category);
    }

    public function unreadCount(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
    }

    /**
     * @return Collection<int, UserNotification>
     */
    public function latestForUser(User $user, int $limit = 15): Collection
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    public function markRead(UserNotification $notification, User $user): void
    {
        if ((int) $notification->user_id !== (int) $user->id) {
            abort(403);
        }

        $notification->update(['is_read' => true]);
    }

    public function markAllRead(User $user): int
    {
        return UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }

    // ——— Event helpers ———

    public function paymentSubmittedToAdmins(Pesanan $pesanan): void
    {
        $this->notifyAdmins(
            "Konfirmasi pembayaran baru: {$pesanan->nama_pasangan} ({$pesanan->nomor_pesanan}).",
            route('admin.pembayaran'),
            'normal',
            'payment'
        );
    }

    public function paymentApprovedForCustomer(Pesanan $pesanan, bool $lunas): void
    {
        if (! $pesanan->user_id) {
            return;
        }

        $msg = $lunas
            ? 'Pelunasan Anda telah dikonfirmasi. Akses jadwal acara penuh telah aktif.'
            : 'Pembayaran DP Anda telah dikonfirmasi.';

        $this->notifyUser(
            (int) $pesanan->user_id,
            $msg,
            route('client.pembayaran.pesanan', $pesanan->id),
            'normal',
            'payment'
        );
    }

    public function paymentRejectedForCustomer(Pesanan $pesanan, string $alasan): void
    {
        if (! $pesanan->user_id) {
            return;
        }

        $this->notifyUser(
            (int) $pesanan->user_id,
            'Pembayaran ditolak: '.$alasan,
            route('client.pembayaran.pesanan', $pesanan->id),
            'urgent',
            'payment'
        );
    }

    public function taskCreatedForKorlap(Pesanan $pesanan, string $taskName): void
    {
        $this->notifyKorlapForPesanan(
            $pesanan,
            "Tugas baru: {$taskName} — {$pesanan->nama_pasangan}.",
            route('lapangan.tugas.index', ['pesanan_id' => $pesanan->id]),
            'normal',
            'task'
        );
    }

    public function vendorCheckInForKorlap(Pesanan $pesanan, string $vendorName, bool $late): void
    {
        $this->notifyKorlapForPesanan(
            $pesanan,
            $late
                ? "Vendor terlambat: {$vendorName} pada {$pesanan->nama_pasangan}."
                : "Vendor hadir: {$vendorName} pada {$pesanan->nama_pasangan}.",
            route('lapangan.laporan', ['pesanan_id' => $pesanan->id]),
            $late ? 'urgent' : 'normal',
            'attendance'
        );

        if ($late) {
            $this->notifyAdmins(
                "Vendor bermasalah (terlambat): {$vendorName} — {$pesanan->nama_pasangan}.",
                route('admin.booking.show', $pesanan->id),
                'urgent',
                'vendor'
            );
        }
    }

    public function kendalaForStaff(Pesanan $pesanan, string $ringkasan, bool $kritis): void
    {
        $priority = $kritis ? 'urgent' : 'normal';
        $link = route('lapangan.laporan', ['pesanan_id' => $pesanan->id]);

        $this->notifyAdmins(
            "Kendala klien ({$pesanan->nomor_pesanan}): {$ringkasan}",
            route('admin.booking.show', $pesanan->id),
            $priority,
            'issue'
        );

        $this->notifyKorlapForPesanan(
            $pesanan,
            "Kendala lapangan: {$ringkasan}",
            $link,
            $priority,
            'issue'
        );
    }

    public function bookingStatusForCustomer(Pesanan $pesanan, string $label): void
    {
        if (! $pesanan->user_id) {
            return;
        }

        $this->notifyUser(
            (int) $pesanan->user_id,
            "Status booking diperbarui: {$label}.",
            route('client.pesanan_detail', $pesanan->id),
            'normal',
            'booking'
        );
    }

    public function itemTambahanRequestedForAdmins(Pesanan $pesanan, ItemTambahan $item): void
    {
        $this->triggerNotification(
            'item_tambahan_requested',
            'admin',
            "Klien menambahkan item tambahan: {$item->kategori} — {$item->deskripsi} ({$pesanan->nomor_pesanan}).",
            $pesanan,
            route('admin.booking.show', $pesanan->id),
            'normal',
            ['item_tambahan_id' => $item->id]
        );
    }

    public function itemTambahanApprovedForCustomer(Pesanan $pesanan, ItemTambahan $item): void
    {
        if (! $pesanan->user_id) {
            return;
        }

        $this->triggerNotification(
            'item_tambahan_approved',
            'client',
            "Item tambahan Anda disetujui: {$item->kategori} — {$item->deskripsi}. Total tagihan telah diperbarui.",
            $pesanan,
            route('client.pesanan_detail', $pesanan->id),
            'normal',
            ['item_tambahan_id' => $item->id]
        );
    }

    public function itemTambahanRejectedForCustomer(Pesanan $pesanan, ItemTambahan $item): void
    {
        if (! $pesanan->user_id) {
            return;
        }

        $this->triggerNotification(
            'item_tambahan_rejected',
            'client',
            "Pengajuan item tambahan ditolak: {$item->kategori} — {$item->deskripsi}.",
            $pesanan,
            route('client.pesanan_detail', $pesanan->id),
            'normal',
            ['item_tambahan_id' => $item->id]
        );
    }

    public function rundownChangedForKorlap(Pesanan $pesanan, string $label = 'diperbarui'): void
    {
        $this->notifyKorlapForPesanan(
            $pesanan,
            "Rundown {$label}: {$pesanan->nama_pasangan}.",
            route('lapangan.jadwal', ['pesanan_id' => $pesanan->id]),
            'normal',
            'rundown'
        );
    }

    public function bookingAssignedToKorlap(Pesanan $pesanan): void
    {
        $this->notifyKorlapForPesanan(
            $pesanan,
            "Booking baru ditugaskan: {$pesanan->nama_pasangan} ({$pesanan->nomor_pesanan}).",
            route('lapangan.pesanan.show', $pesanan->id),
            'normal',
            'booking'
        );
    }

    public function eventReminderForCustomer(Pesanan $pesanan, string $whenLabel): void
    {
        if (! $pesanan->user_id) {
            return;
        }

        $this->notifyUser(
            (int) $pesanan->user_id,
            "Pengingat acara {$whenLabel}: {$pesanan->nama_pasangan} pada ".$pesanan->tanggal_acara?->format('d M Y').'.',
            route('client.jadwal'),
            'normal',
            'reminder'
        );
    }

    public function reviewReminderForCustomer(Pesanan $pesanan): void
    {
        if (! $pesanan->user_id) {
            return;
        }

        $this->notifyUser(
            (int) $pesanan->user_id,
            'Acara selesai! Berikan ulasan dan rating untuk vendor Brilliant WO.',
            route('client.pesanan_detail', $pesanan->id).'#review-vendor',
            'normal',
            'review'
        );
    }

    public function chatFromCustomerForStaff(Pesanan $pesanan): void
    {
        $this->notifyAdmins(
            "Pesan baru dari klien — {$pesanan->nama_pasangan}.",
            route('admin.chat', ['pesanan_id' => $pesanan->id]),
            'normal',
            'chat'
        );

        $this->notifyKorlapForPesanan(
            $pesanan,
            "Pesan baru dari klien — {$pesanan->nama_pasangan}.",
            route('lapangan.chat', ['pesanan_id' => $pesanan->id]),
            'normal',
            'chat'
        );
    }

    /**
     * ===== UNIFIED NOTIFICATION TRIGGER SYSTEM =====
     * Fungsi utama untuk trigger notifikasi berdasarkan event_type dan target_role
     * 
     * @param string $eventType Event identifier (booking_confirmed, payment_approved, chat_new, etc)
     * @param string|array $targetRoles Target role(s): 'admin', 'client', 'korlap', or array of roles
     * @param string $message Notifikasi message
     * @param ?Pesanan $pesanan Booking reference (for context)
     * @param ?string $linkRedirect URL redirect untuk notifikasi
     * @param string $priority 'normal' or 'urgent'
     * @param ?array $metadata Additional data (booking_id, user_id, korlap_id)
     * @return void
     */
    public function triggerNotification(
        string $eventType,
        string|array $targetRoles,
        string $message,
        ?Pesanan $pesanan = null,
        ?string $linkRedirect = null,
        string $priority = 'normal',
        ?array $metadata = null
    ): void {
        $roles = is_array($targetRoles) ? $targetRoles : [$targetRoles];
        $metadata = $metadata ?? [];

        // Tentukan default link redirect berdasarkan event type & pesanan
        if (!$linkRedirect) {
            $linkRedirect = $this->getDefaultLinkForEventType($eventType, $pesanan);
        }

        // Tentukan kategori dari event type
        $category = $this->getCategoryFromEventType($eventType);

        // Notify berdasarkan role target
        foreach ($roles as $role) {
            match ($role) {
                'admin' => $this->notifyAdmins($message, $linkRedirect, $priority, $category),
                'client' => $pesanan && $pesanan->user_id ? $this->notifyUser((int) $pesanan->user_id, $message, $linkRedirect, $priority, $category) : null,
                'korlap' => $pesanan ? $this->notifyKorlapForPesanan($pesanan, $message, $linkRedirect, $priority, $category) : null,
                default => null,
            };
        }

        // Broadcast untuk real-time update
        $this->broadcastNotification($eventType, $roles, $message, $pesanan, $metadata, $category, $priority, $linkRedirect);
    }

    /**
     * Tentukan link redirect default berdasarkan event type
     */
    private function getDefaultLinkForEventType(string $eventType, ?Pesanan $pesanan): ?string
    {
        if (!$pesanan) {
            return null;
        }

        return match ($eventType) {
            'booking_confirmed' => route('admin.booking.show', $pesanan->id),
            'booking_assigned' => route('lapangan.pesanan.show', $pesanan->id),
            'payment_approved', 'payment_rejected' => route('client.pembayaran.pesanan', $pesanan->id),
            'chat_new' => route('admin.chat', ['pesanan_id' => $pesanan->id]),
            'status_changed' => route('client.pesanan_detail', $pesanan->id),
            'vendor_checkin' => route('lapangan.laporan', ['pesanan_id' => $pesanan->id]),
            default => null,
        };
    }

    /**
     * Tentukan kategori dari event type
     */
    private function getCategoryFromEventType(string $eventType): string
    {
        return match ($eventType) {
            'booking_confirmed', 'booking_assigned', 'status_changed' => 'booking',
            'payment_approved', 'payment_rejected', 'payment_submitted' => 'payment',
            'chat_new', 'chat_response' => 'chat',
            'vendor_checkin', 'vendor_checkout', 'vendor_late' => 'vendor',
            'item_tambahan_requested', 'item_tambahan_approved', 'item_tambahan_rejected' => 'payment',
            'task_created', 'task_completed' => 'task',
            'issue_reported', 'kendala_lapangan' => 'issue',
            'rundown_changed' => 'rundown',
            default => 'general',
        };
    }

    /**
     * Broadcast notifikasi untuk real-time update (polling/websocket)
     */
    private function broadcastNotification(
        string $eventType,
        array $roles,
        string $message,
        ?Pesanan $pesanan,
        array $metadata,
        string $category = 'general',
        string $priority = 'normal',
        ?string $linkRedirect = null
    ): void {
        // Data untuk broadcast
        $broadcastData = [
            'event_type' => $eventType,
            'roles' => $roles,
            'message' => $message,
            'category' => $category,
            'priority' => $priority,
            'link_redirect' => $linkRedirect,
            'booking_id' => $pesanan?->id,
            'nomor_pesanan' => $pesanan?->nomor_pesanan,
            'timestamp' => now()->toIso8601String(),
            'metadata' => $metadata,
        ];

        // Simpan ke cache untuk polling system
        $this->cacheNotificationEvent($broadcastData);

        // Broadcast event untuk WebSocket / Pusher
        broadcast(new \App\Events\NotificationBroadcast(
            $eventType,
            $roles,
            $message,
            $pesanan,
            $category,
            $priority,
            $linkRedirect,
            $metadata
        ));
    }

    /**
     * Cache notification event untuk polling (setiap polling ambil dari sini)
     */
    private function cacheNotificationEvent(array $data): void
    {
        $cacheKey = 'notification_events_' . now()->format('YmdH'); // Per-hour cache
        $events = \Illuminate\Support\Facades\Cache::get($cacheKey, []);
        $events[] = array_merge($data, ['id' => uniqid()]);
        \Illuminate\Support\Facades\Cache::put($cacheKey, $events, 3600); // 1 hour TTL
    }

    /**
     * ===== UNIFIED MULTI-ROLE NOTIFICATION HELPER =====
     * Helper function untuk mengirim notifikasi ke multiple roles sekaligus dengan satu call
     * 
     * Fungsi ini menyederhanakan proses mengirim notifikasi ke Admin, Client, dan Korlap
     * yang terkait dengan satu booking. Setiap notifikasi disimpan di database dan bisa
     * diambil via polling system untuk real-time update di frontend.
     * 
     * @param int $bookingId - ID dari pesanan/booking
     * @param string $eventType - Tipe event: 'refund_processed', 'booking_confirmed', 'payment_approved', dll
     * @param string $message - Pesan notifikasi (bisa menggunakan sprintf untuk format dinamis)
     * @param array $targetRoles - Array of roles: ['admin', 'client', 'korlap'] (optional: default semua)
     * @param ?string $linkRedirect - URL untuk link aksi di notifikasi (optional)
     * @param string $priority - 'normal' atau 'urgent' (default: 'normal')
     * @param ?array $metadata - Data tambahan untuk tracking & audit (booking_id, final_refund, dll)
     * 
     * @return array - Array of created UserNotification records
     * 
     * @example
     * // Kirim notifikasi refund ke semua role
     * $this->notificationService->sendNotification(
     *     bookingId: 123,
     *     eventType: 'refund_processed',
     *     message: 'Refund DP sebesar Rp 5.000.000 berhasil diproses',
     *     targetRoles: ['admin', 'client', 'korlap'],
     *     linkRedirect: route('customer.pesanan.show', 123),
     *     priority: 'high',
     *     metadata: ['final_refund' => 5000000, 'penalty' => 1000000]
     * );
     */
    public function sendNotification(
        int $bookingId,
        string $eventType,
        string $message,
        array $targetRoles = ['admin', 'client', 'korlap'],
        ?string $linkRedirect = null,
        string $priority = 'normal',
        ?array $metadata = null
    ): array {
        try {
            // Fetch pesanan/booking
            $pesanan = Pesanan::with('user', 'korlap')->findOrFail($bookingId);
            
            // Default link redirect jika tidak disediakan
            if (!$linkRedirect) {
                $linkRedirect = $this->getDefaultLinkForEventType($eventType, $pesanan);
            }

            // Tentukan kategori dari event type
            $category = $this->getCategoryFromEventType($eventType);

            // Array untuk track created notifications
            $createdNotifications = [];

            // Kirim notifikasi ke setiap role yang ditargetkan
            foreach ($targetRoles as $role) {
                $notification = null;

                if ($role === 'admin') {
                    // Kirim ke SEMUA admin
                    $admins = User::where('role', 'admin')->get();
                    foreach ($admins as $admin) {
                        $notification = $this->notifyUser(
                            $admin,
                            $message,
                            $linkRedirect,
                            $priority,
                            $category
                        );
                        $createdNotifications[] = $notification;
                    }
                } 
                elseif ($role === 'client' && $pesanan->user_id) {
                    // Kirim ke CLIENT pemilik booking
                    $notification = $this->notifyUser(
                        (int) $pesanan->user_id,
                        $message,
                        $linkRedirect,
                        $priority,
                        $category
                    );
                    $createdNotifications[] = $notification;
                } 
                elseif ($role === 'korlap' && $pesanan->korlap_id) {
                    // Kirim ke KORLAP yang ditugaskan ke booking ini
                    $notification = $this->notifyUser(
                        (int) $pesanan->korlap_id,
                        $message,
                        $linkRedirect,
                        $priority,
                        $category
                    );
                    $createdNotifications[] = $notification;
                }
            }

            // Broadcast untuk real-time polling dan websocket
            if (!empty($createdNotifications)) {
                $this->broadcastNotification(
                    $eventType,
                    $targetRoles,
                    $message,
                    $pesanan,
                    $metadata ?? [],
                    $category,
                    $priority,
                    $linkRedirect
                );
            }

            return [
                'success' => true,
                'notifications_sent' => count($createdNotifications),
                'target_roles' => $targetRoles,
                'created_notifications' => $createdNotifications,
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error sending notification', [
                'booking_id' => $bookingId,
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send notification: ' . $e->getMessage(),
                'error' => $e,
            ];
        }
    }
}
