<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotification extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'message',
        'is_read',
        'link_redirect',
        'priority',
        'category',
        'reference_id',
        'reference_type',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }

    /**
     * @return array<string, mixed>
     */
    public function toBellArray(): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'action_title' => $this->actionTitle(),
            'display_message' => $this->displayHtml(),
            'category' => $this->category ?? 'general',
            'category_icon' => $this->categoryIcon(),
            'priority' => $this->priority,
            'link_redirect' => $this->link_redirect,
            'reference_id' => $this->reference_id,
            'reference_type' => $this->reference_type,
            'created_at' => $this->created_at?->toIso8601String(),
            'formatted_time' => $this->relativeTimeLabel(),
            'is_read' => (bool) $this->is_read,
            'is_urgent' => $this->isUrgent(),
        ];
    }

    public function actionTitle(): string
    {
        $message = strtolower($this->message ?? '');

        return match ($this->category) {
            'payment' => str_contains($message, 'tolak') || str_contains($message, 'rejected')
                ? 'Payment Rejected!'
                : (str_contains($message, 'konfirmasi') || str_contains($message, 'received') || str_contains($message, 'dibayar') || str_contains($message, 'pelunasan')
                    ? 'Payment Received!'
                    : 'Payment Update!'),
            'booking' => str_contains($message, 'baru') || str_contains($message, 'new') || str_contains($message, 'created')
                ? 'New Booking!'
                : 'Booking Update!',
            'task' => 'New Task Assigned!',
            'issue' => 'Issue Reported!',
            'chat' => 'New Message!',
            'vendor' => 'Vendor Update!',
            'cancellation' => 'Booking Cancelled!',
            'reminder' => 'Event Reminder!',
            'review' => 'Review Request!',
            'rundown' => 'Rundown Updated!',
            default => str_contains($message, 'item tambahan') || str_contains($message, 'item')
                ? 'New Item Added!'
                : 'Notification!',
        };
    }

    public function displayHtml(): string
    {
        $title = e($this->actionTitle());
        $body = $this->formatMessageBody($this->message ?? '');

        return "<strong>{$title}</strong> — {$body}";
    }

    private function formatMessageBody(string $message): string
    {
        $escaped = e($message);
        $escaped = preg_replace('/((?:Booking\s*#|WO-|Nomor:\s*)[\w\-#]+)/i', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(\([^)]{2,}\))/', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(Rp\s?[\d.,]+)/', '<strong>$1</strong>', $escaped) ?? $escaped;
        $escaped = preg_replace('/(Status:\s*)([^().]+)/i', '$1<strong>$2</strong>', $escaped) ?? $escaped;

        return $escaped;
    }

    public function relativeTimeLabel(): string
    {
        if (! $this->created_at) {
            return 'Just now';
        }

        return $this->created_at->diffForHumans();
    }

    public function categoryIcon(): string
    {
        return match ($this->category) {
            'payment' => 'credit-card',
            'booking' => 'calendar',
            'task' => 'clipboard',
            'chat' => 'chat',
            'issue', 'vendor', 'cancellation' => 'alert',
            default => 'dining',
        };
    }
}
