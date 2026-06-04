<?php

namespace App\Policies;

use App\Models\Tugas;
use App\Models\User;

class TugasPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'lapangan';
    }

    public function view(User $user, Tugas $tugas): bool
    {
        return $this->managesTask($user, $tugas);
    }

    public function create(User $user): bool
    {
        return $user->role === 'lapangan';
    }

    public function update(User $user, Tugas $tugas): bool
    {
        return $this->managesTask($user, $tugas);
    }

    public function delete(User $user, Tugas $tugas): bool
    {
        return $this->managesTask($user, $tugas);
    }

    public function verify(User $user, Tugas $tugas): bool
    {
        return $tugas->pesanan?->korlap_id === $user->id
            && $tugas->status === 'awaiting_verification';
    }

    protected function managesTask(User $user, Tugas $tugas): bool
    {
        if ($user->role !== 'lapangan') {
            return false;
        }

        if ($user->id === $tugas->user_id || $user->id === $tugas->pic_id) {
            return true;
        }

        return $tugas->pesanan?->korlap_id === $user->id;
    }
}
