<?php

namespace App\Events;

use App\Models\Pesanan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event untuk broadcast notifikasi real-time ke semua client yang terkoneksi
 * 
 * Gunakan:
 * broadcast(new NotificationBroadcast('booking_confirmed', ['admin', 'client', 'korlap'], $message, $pesanan));
 */
class NotificationBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $eventType;
    public array $roles;
    public string $message;
    public ?Pesanan $pesanan;
    public string $category;
    public string $priority;
    public ?string $linkRedirect;
    public array $broadcastData;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $eventType,
        array $roles,
        string $message,
        ?Pesanan $pesanan = null,
        string $category = 'general',
        string $priority = 'normal',
        ?string $linkRedirect = null,
        array $metadata = []
    ) {
        $this->eventType = $eventType;
        $this->roles = $roles;
        $this->message = $message;
        $this->pesanan = $pesanan;
        $this->category = $category;
        $this->priority = $priority;
        $this->linkRedirect = $linkRedirect;
        
        $this->broadcastData = [
            'event_type' => $eventType,
            'roles' => $roles,
            'message' => $message,
            'category' => $category,
            'priority' => $priority,
            'link_redirect' => $linkRedirect,
            'booking_id' => $pesanan?->id,
            'nomor_pesanan' => $pesanan?->nomor_pesanan,
            'nama_pasangan' => $pesanan?->nama_pasangan,
            'notification_id' => $metadata['notification_id'] ?? null,
            'timestamp' => now()->toIso8601String(),
            'metadata' => $metadata,
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Broadcast ke channel untuk setiap role
        $channels = [];
        
        foreach ($this->roles as $role) {
            if ($role === 'korlap') {
                $channels[] = new Channel('notifications.korlap');
                $channels[] = new Channel('notifications.lapangan');
            } else {
                $channels[] = new Channel("notifications.{$role}");
            }
        }

        // Jika ada pesanan, broadcast juga ke channel pesanan-specific
        if ($this->pesanan) {
            $channels[] = new Channel("notifications.booking.{$this->pesanan->id}");
        }

        return $channels;
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return $this->broadcastData;
    }

    /**
     * Get the name of the event to broadcast as.
     */
    public function broadcastAs(): string
    {
        return 'notification.received';
    }
}
