<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VendorMeeting;

class VendorMeetingPolicy
{
    /**
     * Korlap dan Admin boleh membuat jadwal meeting vendor baru.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isLapangan();
    }

    /**
     * Determine whether the user can view the model.
     * Admin bisa lihat semua, Korlap hanya lihat miliknya.
     */
    public function view(User $user, VendorMeeting $vendorMeeting): bool
    {
        // Admin bisa lihat semua
        if ($user->isAdmin()) {
            return true;
        }

        // Korlap hanya bisa lihat yang ditugaskan ke mereka
        if ($user->isLapangan() && $vendorMeeting->korlap_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can update the model.
     * Admin & Korlap yang ditugaskan bisa update.
     */
    public function update(User $user, VendorMeeting $vendorMeeting): bool
    {
        // Admin bisa update semua
        if ($user->isAdmin()) {
            return true;
        }

        // Korlap hanya bisa update yang ditugaskan ke mereka
        if ($user->isLapangan() && $vendorMeeting->korlap_id === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * Hanya Admin yang bisa delete.
     */
    public function delete(User $user, VendorMeeting $vendorMeeting): bool
    {
        return $user->isAdmin();
    }
}
