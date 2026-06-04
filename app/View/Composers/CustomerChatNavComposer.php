<?php

namespace App\View\Composers;

use App\Services\CustomerBookingChatService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerChatNavComposer
{
    public function __construct(
        protected CustomerBookingChatService $customerChat
    ) {}

    public function compose(View $view): void
    {
        $user = Auth::user();

        if (! $user || $user->role !== 'client') {
            $view->with(['customerChatUnread' => 0]);

            return;
        }

        $view->with([
            'customerChatUnread' => $this->customerChat->unreadCountForCustomer($user),
        ]);
    }
}
