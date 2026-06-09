<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DatabaseUserNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $message,
        public ?string $linkRedirect = null,
        public ?string $category = null,
        public ?string $priority = null,
        public array $metadata = []
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'message' => $this->message,
            'link_redirect' => $this->linkRedirect,
            'category' => $this->category,
            'priority' => $this->priority,
            'metadata' => $this->metadata,
        ];
    }
}
