<?php

namespace App\View\Composers;

use App\Models\Pesanan;
use App\Services\ScheduleAccessService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SidebarJadwalNavComposer
{
    public function compose(View $view): void
    {
        $panel = match (true) {
            str_contains($view->name(), 'customer') => 'client',
            str_contains($view->name(), 'lapangan') => 'lapangan',
            default => null,
        };

        if ($panel === null) {
            return;
        }

        $pesanan = $this->resolveContextPesanan($panel);

        if (! $pesanan) {
            $view->with([
                'jadwalNavRundownLocked' => true,
                'jadwalNavMeetingLocked' => true,
                'jadwalNavLockHint' => 'Belum ada pesanan aktif',
            ]);

            return;
        }

        $view->with([
            'jadwalNavRundownLocked' => ! ScheduleAccessService::canAccessRundown($pesanan),
            'jadwalNavMeetingLocked' => ! ScheduleAccessService::canAccessVendorMeeting($pesanan),
            'jadwalNavLockHint' => ScheduleAccessService::lockLabel($pesanan),
        ]);
    }

    protected function resolveContextPesanan(?string $panel): ?Pesanan
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        if ($panel === 'client') {
            return Pesanan::query()
                ->where('user_id', $user->id)
                ->whereNotIn('status_pemesanan', ['cancelled', 'canceled', 'expired'])
                ->orderByDesc('created_at')
                ->first();
        }

        if ($panel === 'lapangan') {
            return Pesanan::query()
                ->where('korlap_id', $user->id)
                ->whereNotIn('status_pemesanan', ['cancelled', 'canceled', 'expired'])
                ->orderByDesc('created_at')
                ->first();
        }

        return null;
    }
}
