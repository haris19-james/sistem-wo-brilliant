<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\CustomerVendorMeetingService;
use Illuminate\Support\Facades\Auth;

class VendorMeetingController extends Controller
{
    public function index(CustomerVendorMeetingService $meetingService)
    {
        $userId = Auth::id();
        $grouped = $meetingService->groupedByBooking($userId);
        $upcoming = $meetingService->upcomingForDashboard($userId, 20);

        return view('customer.modules.vendor-meetings.index', [
            'activeMenu' => 'jadwal-meeting',
            'groupedMeetings' => $grouped,
            'upcomingMeetings' => $upcoming,
            'pesanan' => $grouped->pluck('pesanan'),
        ]);
    }
}
