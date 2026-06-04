<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone_number', 'address', 'avatar_url'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return \App\Support\Role::isClient($this->role);
    }

    /**
     * Get the user's avatar URL
     * Returns stored avatar or placeholder from UI Avatars service
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar_url) {
            return asset('storage/' . $this->avatar_url);
        }

        // Generate placeholder avatar with initials
        $initials = collect(explode(' ', $this->name))
            ->map(fn($word) => substr($word, 0, 1))
            ->take(2)
            ->implode('')
            ->toUpper();

        return sprintf(
            'https://ui-avatars.com/api/?name=%s&background=00A32A&color=fff&size=128&bold=true&rounded=true',
            urlencode($initials)
        );
    }

    /** @deprecated Gunakan isClient() */
    public function isCustomer(): bool
    {
        return $this->isClient();
    }

    public function isLapangan(): bool
    {
        return $this->role === 'lapangan';
    }

    public function laporanLapangans()
    {
        return $this->hasMany(LaporanLapangan::class);
    }

    /**
     * Korlap (User dengan role 'lapangan') bisa memiliki banyak jadwal meeting vendor.
     */
    public function vendorMeetings()
    {
        return $this->hasMany(VendorMeeting::class, 'korlap_id');
    }

    public function korlapPesanans()
    {
        return $this->hasMany(Pesanan::class, 'korlap_id');
    }
}
