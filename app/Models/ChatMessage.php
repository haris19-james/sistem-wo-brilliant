<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    use HasFactory;

    protected $table = 'chat_messages';

    protected $fillable = [
        'pesanan_id',
        'booking_id',
        'user_id',
        'sender_id',
        'receiver_id',
        'pesan',
        'dari_admin',
        'is_read',
        'is_internal',
    ];

    protected function casts(): array
    {
        return [
            'dari_admin' => 'boolean',
            'is_read' => 'boolean',
            'is_internal' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    // ========================
    // RELATIONSHIPS
    // ========================

    /**
     * Chat message belongs to a Pesanan/Booking
     */
    public function pesanan(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class);
    }

    /**
     * Chat message belongs to a Booking (alias)
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Pesanan::class, 'booking_id');
    }

    /**
     * Chat message is sent by a user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Chat message sent by a specific user (sender)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Chat message received by a specific user (receiver)
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // ========================
    // SCOPES
    // ========================

    /**
     * Get unread messages for a user
     */
    public function scopeUnread($query, $userId)
    {
        return $query->where('receiver_id', $userId)
            ->where('is_read', false);
    }

    /**
     * Get messages between two users
     */
    public function scopeBetweenUsers($query, $userId1, $userId2)
    {
        return $query->where(function ($q) use ($userId1, $userId2) {
            $q->where('sender_id', $userId1)->where('receiver_id', $userId2)
                ->orWhere('sender_id', $userId2)->where('receiver_id', $userId1);
        })->orderBy('created_at');
    }

    /**
     * Get latest message for each contact
     */
    public function scopeLatestPerContact($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_id', $userId)
                ->orWhere('receiver_id', $userId);
        })->latest();
    }

    /**
     * Get conversations for dashboard (latest message from each contact)
     */
    public function scopeConversationsFor($query, $userId)
    {
        return $query->select('chat_messages.*')
            ->where(function ($q) use ($userId) {
                $q->where('sender_id', $userId)
                    ->orWhere('receiver_id', $userId);
            })
            ->orderByDesc('created_at');
    }

    // ========================
    // ACCESSORS & MUTATORS
    // ========================

    /**
     * Get contact user (sender jika receiver adalah current user, sebaliknya)
     */
    public function getContactUserAttribute()
    {
        $currentUserId = auth()->id();

        if ($this->sender_id === $currentUserId) {
            return $this->receiver;
        }

        return $this->sender;
    }

    /**
     * Get formatted time (e.g., 10:24, Kemarin, 2 Hari lalu)
     */
    public function getFormattedTimeAttribute(): string
    {
        $now = now();
        $createdAt = $this->created_at;

        // Today
        if ($createdAt->isToday()) {
            return $createdAt->format('H:i');
        }

        // Yesterday
        if ($createdAt->isYesterday()) {
            return 'Kemarin';
        }

        // This week
        if ($createdAt->diffInDays($now) < 7) {
            return $createdAt->diffInDays($now) . ' Hari lalu';
        }

        // Default
        return $createdAt->format('d M');
    }

    /**
     * Get user initials for avatar
     */
    public function getContactInitialsAttribute(): string
    {
        $contact = $this->contactUser;
        if (!$contact) {
            return '??';
        }

        $names = explode(' ', trim($contact->name));
        $initials = '';

        foreach ($names as $name) {
            $initials .= strtoupper($name[0]);
        }

        return substr($initials, 0, 2);
    }

    /**
     * Get role label (Admin, Vendor, Korlap)
     */
    public function getContactRoleAttribute(): string
    {
        $contact = $this->contactUser;
        if (!$contact) {
            return 'Unknown';
        }

        $roleMap = [
            'admin' => 'Admin',
            'client' => 'Client',
            'lapangan' => 'Korlap',
            'vendor' => 'Vendor',
        ];

        return $roleMap[$contact->role] ?? ucfirst($contact->role);
    }
}
