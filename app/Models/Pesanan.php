<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Support\BookingDynamicStatus;

class Pesanan extends Model
{
    use HasFactory;

    protected $table = 'pesanans';

    protected $fillable = [
        'user_id',
        'paket_id',
        'korlap_id',  // ✅ ADD THIS - Korlap (Field Coordinator) assignment
        'nomor_pesanan',
        'nama_pasangan',
        'tanggal_acara',
        'tanggal_jatuh_tempo',
        'status_deadline',
        'jam_acara',
        'lokasi',
        'google_maps_url',
        'tema',
        'jumlah_tamu',
        'status',
        'status_pembayaran',  // ✅ Payment verification status
        'status_booking',     // pending | approved_dp | approved_lunas | cancelled
        'akses_jadwal',       // none | partial | full
        'catatan_pembayaran',
        'status_pemesanan',   // ✅ Order workflow status
        'catatan_khusus',
        'detail_paket_kustom',
        'estimasi_budget',
        'alasan_pembatalan',
        'jumlah_refund',
        'pembatalan_diminta_at',
        'dibatalkan_at',
        'expired_at',
        'laporan_korlap',
        'verified_admin_id',
        'verified_by_admin_at',
        'fully_paid_by_admin_at',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_acara' => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'status_deadline' => 'string',
            'pembatalan_diminta_at' => 'datetime',
            'dibatalkan_at' => 'datetime',
            'jumlah_refund' => 'decimal:2',
            'expired_at' => 'datetime',
            'verified_by_admin_at' => 'datetime',
            'fully_paid_by_admin_at' => 'datetime',
            'estimasi_budget' => 'decimal:2',
            'laporan_korlap' => 'string',
            'status_pembayaran' => 'string',  // 'unpaid', 'dp_paid', 'fully_paid'
            'akses_jadwal' => 'string',       // 'none', 'partial', 'full'
            'catatan_pembayaran' => 'string',
            'status_pemesanan' => 'string',   // ENUM: pending, confirmed, on_progress, completed, canceled, pending_cancellation, expired
        ];
    }

    // ========================
    // RELATIONSHIPS
    // ========================

    /**
     * Pesanan ini dimiliki oleh satu user/klien.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pesanan ditugaskan kepada satu Korlap (Tim Lapangan / Field Coordinator).
     * ✅ CRITICAL: Hubungan ini menentukan siapa yang bisa melihat pesanan
     */
    public function korlap()
    {
        return $this->belongsTo(User::class, 'korlap_id')
            ->where('role', 'lapangan');  // Hanya User dengan role 'lapangan'
    }

    /**
     * Admin yang melakukan verifikasi pembayaran DP.
     */
    public function verifiedByAdmin()
    {
        return $this->belongsTo(User::class, 'verified_admin_id');
    }

    /**
     * Pesanan ini menggunakan satu paket WO.
     */
    public function paket()
    {
        return $this->belongsTo(Paket::class);
    }

    /**
     * Pesanan bisa memiliki banyak invoice.
     */
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Pesanan bisa memiliki banyak item rundown jadwal.
     */
    public function rundowns()
    {
        return $this->hasMany(Rundown::class)->orderBy('waktu_mulai');
    }

    /**
     * Pesanan memiliki satu data progress persiapan.
     */
    public function progress()
    {
        return $this->hasOne(ProgressPersiapan::class);
    }

    public function jadwalMeetings()
    {
        return $this->hasMany(JadwalMeeting::class)
            ->orderBy('tanggal_meeting')
            ->orderBy('waktu_meeting');
    }

    public function chatMessages()
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at');
    }

    public function chatInternalNotes()
    {
        return $this->hasMany(ChatInternalNote::class)->latest();
    }

    public function laporanLapangans()
    {
        return $this->hasMany(LaporanLapangan::class)->latest('tanggal');
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'pesanan_vendor')
            ->withPivot(['waktu_setup', 'status', 'nama_pic', 'kontak_pic'])
            ->withTimestamps();
    }

    public function vendorAttendances()
    {
        return $this->hasMany(VendorAttendance::class);
    }

    public function tugas()
    {
        return $this->hasMany(Tugas::class);
    }

    /**
     * Pesanan bisa memiliki banyak jadwal meeting vendor.
     */
    public function vendorMeetings()
    {
        return $this->hasMany(VendorMeeting::class, 'booking_id')
            ->orderBy('meeting_date')
            ->orderBy('meeting_time');
    }

    public function kuaChecklist()
    {
        return $this->hasOne(KuaChecklist::class, 'booking_id');
    }

    public function operasionalLapangan()
    {
        return $this->hasMany(OperasionalLapangan::class);
    }

    public function vendorAnggarans()
    {
        return $this->hasMany(VendorAnggaran::class);
    }

    public function realisasiOperasional()
    {
        return $this->hasMany(RealisasiOperasional::class);
    }

    /**
     * Relasi untuk mengambil hanya agenda yang auto-generated (milestone/persiapan).
     */
    public function autoGeneratedAgendas()
    {
        return $this->hasMany(VendorMeeting::class, 'booking_id')
            ->where('is_auto_generated', true)
            ->orderBy('meeting_date', 'asc');
    }

    /**
     * Relasi untuk mengambil agenda persiapan mandiri saja.
     */
    public function selfPreparationAgendas()
    {
        return $this->hasMany(VendorMeeting::class, 'booking_id')
            ->where('agenda_type', 'self_preparation')
            ->orderBy('meeting_date', 'asc');
    }

    /**
     * Relasi untuk mengambil agenda technical meeting saja.
     */
    public function technicalMeetingAgendas()
    {
        return $this->hasMany(VendorMeeting::class, 'booking_id')
            ->where('agenda_type', 'technical_meeting')
            ->orderBy('meeting_date', 'asc');
    }

    public function itemTambahan()
    {
        return $this->hasMany(ItemTambahan::class);
    }

    /**
     * Item tambahan — tabel baru (item_tambahan) atau legacy (booking_addons).
     *
     * @deprecated Gunakan itemTambahan() di kode baru
     */
    public function bookingAddons()
    {
        if (Schema::hasTable('item_tambahan')) {
            return $this->hasMany(ItemTambahan::class);
        }

        return $this->hasMany(BookingAddon::class);
    }

    public function paidItemTambahan()
    {
        return $this->hasMany(ItemTambahan::class)->where('status', 'paid');
    }

    /** @deprecated */
    public function paidBookingAddons()
    {
        if (Schema::hasTable('item_tambahan')) {
            return $this->paidItemTambahan();
        }

        return $this->hasMany(BookingAddon::class)->where('status', 'paid');
    }

    public function bookingReview()
    {
        return $this->hasOne(BookingReview::class, 'booking_id');
    }

    public function scopeAktifLapangan($query)
    {
        return $query->whereIn('status', ['Menunggu', 'Sedang Berlangsung']);
    }

    // ========================
    // SCOPES UNTUK PAYMENT WORKFLOW
    // ========================

    /**
     * Scope untuk filter booking yang visible ke Korlap.
     * Hanya menampilkan pesanan yang sudah diverifikasi pembayaran DP atau lunas.
     * Ini mencegah Korlap melihat pesanan yang belum dibayar.
     */
    public function scopeVisibleToKorlap($query, $korlapId = null)
    {
        if (!$korlapId) {
            $korlapId = auth()->id();
        }

        return $query->where('korlap_id', $korlapId)
            ->whereIn('status_pembayaran', ['dp_paid', 'fully_paid'])
            ->confirmedForLapangan();
    }

    /**
     * Booking sudah diverifikasi admin (DP/Lunas) — tim lapangan boleh melihat tugas & acara.
     * Pending = menunggu DP; Confirmed = dp_paid/fully_paid + status_pemesanan aktif.
     */
    public function scopeConfirmedForLapangan($query)
    {
        return $query
            ->whereIn('status_pembayaran', ['dp_paid', 'fully_paid'])
            ->whereIn('status_pemesanan', ['confirmed', 'on_progress', 'completed']);
    }

    public function isConfirmedForLapangan(): bool
    {
        return in_array($this->status_pembayaran, ['dp_paid', 'fully_paid'], true)
            && in_array($this->status_pemesanan, ['confirmed', 'on_progress', 'completed'], true);
    }

    public function getWorkflowStatusLabelAttribute(): string
    {
        if (in_array($this->status_pemesanan, ['completed', 'success'], true)
            || $this->status === 'Selesai') {
            return 'Completed';
        }

        if ($this->isConfirmedForLapangan()) {
            return 'Confirmed';
        }

        return 'Pending';
    }

    /**
     * Scope untuk filter by payment status.
     * Contoh: Pesanan::byPaymentStatus(['dp_paid', 'fully_paid'])->get()
     */
    public function scopeByPaymentStatus($query, array $statuses)
    {
        return $query->whereIn('status_pembayaran', $statuses);
    }

    /**
     * Pesanan eligible untuk dropdown jadwal meeting vendor:
     * minimal DP terverifikasi (dp_paid) ATAU lunas — termasuk sinkronisasi via invoice.
     */
    public function scopeEligibleForVendorMeeting($query)
    {
        return $query
            ->where('status', '!=', 'Dibatalkan')
            ->when(Schema::hasColumn('pesanans', 'status_booking'), function ($q) {
                $q->where(function ($inner) {
                    $inner->whereNull('status_booking')
                        ->orWhere('status_booking', '!=', 'cancelled');
                });
            })
            ->whereNotIn('status_pemesanan', ['cancelled', 'canceled', 'expired', 'pending_cancellation'])
            ->where(function ($q) {
                $q->whereIn('status_pembayaran', ['dp_paid', 'fully_paid'])
                    ->orWhereRaw('LOWER(TRIM(status_pembayaran)) IN (?, ?)', ['lunas', 'fully_paid'])
                    ->orWhereHas('invoices', function ($inv) {
                        $inv->whereIn('status', ['DP Lunas', 'Lunas'])
                            ->orWhere('dp_dibayar', '>', 0);
                    });
            });
    }

    /**
     * Pesanan yang menempati tanggal acara (anti double-booking): minimal DP atau lunas.
     */
    public function scopeBlocksEventDate($query)
    {
        return $query->eligibleForVendorMeeting();
    }

    /**
     * Sudah bayar minimal DP (termasuk lunas penuh).
     */
    public function hasMinimalDpPaid(): bool
    {
        $status = strtolower(trim((string) ($this->status_pembayaran ?? '')));

        if (in_array($status, ['dp_paid', 'fully_paid', 'lunas'], true)) {
            return true;
        }

        if ($this->isPembayaranLunas()) {
            return true;
        }

        if (! Schema::hasTable('invoices')) {
            return false;
        }

        if ($this->relationLoaded('invoices')) {
            return $this->invoices->contains(fn ($invoice) => in_array($invoice->status, ['DP Lunas', 'Lunas'], true)
                || (float) $invoice->dp_dibayar > 0);
        }

        return $this->invoices()
            ->where(function ($q) {
                $q->whereIn('status', ['DP Lunas', 'Lunas'])
                    ->orWhere('dp_dibayar', '>', 0);
            })
            ->exists();
    }

    /**
     * Scope untuk filter by order status.
     * Contoh: Pesanan::byOrderStatus(['on_progress'])->get()
     */
    public function scopeByOrderStatus($query, array $statuses)
    {
        return $query->whereIn('status_pemesanan', $statuses);
    }

    public function scopeExpired($query)
    {
        return $query->where('status_pemesanan', 'expired');
    }

    public function scopePendingCancellation($query)
    {
        return $query->where('status_pemesanan', 'pending_cancellation');
    }

    public function scopeNotExpired($query)
    {
        return $query->whereNotIn('status_pemesanan', ['expired']);
    }

    public function scopeWaitingForFullPayment($query)
    {
        return $query->where('status_pembayaran', 'dp_paid');
    }

    /**
     * Scope untuk menampilkan pesanan yang sudah lunas penuh.
     */
    public function scopeFullyPaid($query)
    {
        return $query->where('status_pembayaran', 'fully_paid');
    }

    /**
     * Scope untuk menampilkan pesanan yang belum bayar.
     */
    public function scopeUnpaid($query)
    {
        return $query->where('status_pembayaran', 'unpaid');
    }

    // ========================
    // ACCESSORS
    // ========================

    /**
     * Label status booking (dinamis: progress + tanggal acara).
     */
    public function getStatusLabelAttribute(): string
    {
        return BookingDynamicStatus::resolve($this)['label'];
    }

    /**
     * Format status badge warna (untuk Tailwind classes).
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return BookingDynamicStatus::resolve($this)['badge_class'];
    }

    public function getStatusIsCompletedAttribute(): bool
    {
        return BookingDynamicStatus::resolve($this)['is_completed'];
    }

    public function syncDynamicStatus(): self
    {
        BookingDynamicStatus::sync($this);

        return $this;
    }

    /**
     * Accessor: format status pembayaran dengan badge Tailwind.
     */
    public function getStatusPembayaranBadgeClassAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'unpaid' => 'bg-red-50 text-red-700 border border-red-200',
            'dp_paid' => 'bg-yellow-50 text-yellow-700 border border-yellow-200',
            'fully_paid' => 'bg-green-50 text-green-700 border border-green-200',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * Accessor: format label status pembayaran dalam Bahasa Indonesia.
     */
    public function getStatusPembayaranLabelAttribute(): string
    {
        return match ($this->status_pembayaran) {
            'unpaid' => 'Belum Bayar',
            'dp_paid' => 'DP Terverifikasi',
            'fully_paid' => 'Lunas Penuh',
            default => 'Unknown',
        };
    }

    /**
     * Accessor: format label status pemesanan dalam Bahasa Indonesia.
     */
    public function getStatusPemesananLabelAttribute(): string
    {
        return match ($this->status_pemesanan) {
            'pending' => 'Menunggu Verifikasi',
            'confirmed' => 'Dikonfirmasi',
            'on_progress' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'success' => 'Selesai',
            'canceled', 'cancelled' => 'Dibatalkan',
            'pending_cancellation' => 'Menunggu Pembatalan',
            'expired' => 'Hangus',
            default => 'Unknown',
        };
    }

    /**
     * Jam mulai acara (dari jam_acara / waktu_mulai operasional).
     */
    public function getJamMulaiFormattedAttribute(): string
    {
        return $this->formatTimeValue($this->jam_acara);
    }

    /**
     * Jam selesai acara (dari rundown terakhir atau estimasi dari jam_acara).
     */
    public function getJamSelesaiFormattedAttribute(): string
    {
        if ($this->relationLoaded('rundowns') && $this->rundowns->isNotEmpty()) {
            $selesai = $this->rundowns
                ->filter(fn ($r) => $r->waktu_selesai)
                ->max('waktu_selesai');

            if ($selesai) {
                return $this->formatTimeValue($selesai);
            }

            $mulaiTerakhir = $this->rundowns->max('waktu_mulai');
            if ($mulaiTerakhir) {
                return $this->formatTimeValue($mulaiTerakhir);
            }
        }

        if ($this->jam_acara) {
            return \Carbon\Carbon::parse($this->jam_acara)->addHours(5)->format('H:i');
        }

        return '—';
    }

    protected function formatTimeValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('H:i');
        }

        $str = (string) $value;

        return strlen($str) >= 5 ? substr($str, 0, 5) : $str;
    }

    /**
     * Accessor: format tanggal acara ke bahasa Indonesia.
     */
    public function getTanggalFormattedAttribute(): string
    {
        return $this->tanggal_acara
            ? $this->tanggal_acara->translatedFormat('d F Y')
            : '-';
    }

    /**
     * Accessor: determine cancellation type based on payment and order status
     * Returns: 'free' (unpaid), 'pending_refund' (waiting approval), 'refund_denied', or null
     */
    public function getCancellationTypeAttribute(): ?string
    {
        if (!in_array($this->status_pemesanan, ['canceled', 'cancelled', 'pending_cancellation'])) {
            return null;
        }

        if ($this->status_pembayaran === 'unpaid') {
            return 'free';
        }

        if ($this->status_pemesanan === 'pending_cancellation') {
            return 'pending_refund';
        }

        // For canceled with payment: check if it was marked for refund
        return $this->status_pembayaran === 'unpaid' ? 'refund_approved' : 'refund_denied';
    }

    public function isDibatalkan(): bool
    {
        return $this->status === 'Dibatalkan';
    }

    public function isExpired(): bool
    {
        return $this->status_pemesanan === 'expired';
    }

    public function isPendingCancellation(): bool
    {
        return $this->status_pemesanan === 'pending_cancellation';
    }

    public static function expireOverdueBookingsIfDue(): void
    {
        Cache::remember('pesanan.expire_overdue_ran', now()->addMinutes(5), function () {
            static::expireOverdueBookings();

            return true;
        });

        BookingDynamicStatus::syncDueBookingsIfNeeded();
    }

    public static function expireOverdueBookings(): int
    {
        // Protect against running when migration hasn't been applied yet
        if (! Schema::hasColumn('pesanans', 'expired_at')) {
            return 0;
        }

        return self::where('status_pembayaran', 'unpaid')
            ->whereNotIn('status_pemesanan', ['expired', 'pending_cancellation', 'cancelled', 'canceled'])
            ->whereNotNull('expired_at')
            ->where('expired_at', '<', now())
            ->update(array_filter([
                'status_pemesanan' => 'expired',
                'status' => 'Dibatalkan',
                'dibatalkan_at' => now(),
                'status_booking' => Schema::hasColumn('pesanans', 'status_booking') ? 'cancelled' : null,
            ], fn ($v) => $v !== null));
    }

    public function canCancelByCustomer(): bool
    {
        if ($this->status_booking === 'cancelled' || $this->isDibatalkan()) {
            return false;
        }

        return in_array($this->status, ['Menunggu', 'Sedang Berlangsung'], true)
            && ! in_array($this->status_pemesanan, ['pending_cancellation', 'expired', 'cancelled', 'canceled'], true);
    }

    public function getStatusBookingLabelAttribute(): string
    {
        return match ($this->status_booking ?? '') {
            'pending' => 'Menunggu Pembayaran',
            'approved_dp' => 'DP Terverifikasi',
            'approved_lunas' => 'Lunas Penuh',
            'cancelled' => 'Dibatalkan',
            default => '—',
        };
    }

    public function isPaketKustom(): bool
    {
        return $this->paket?->isPaketKustom() ?? false;
    }

    /**
     * Cek apakah pembayaran dianggap lunas (case-insensitive).
     * Mendukung nilai kolom: fully_paid, Lunas, lunas, serta fallback status invoice.
     */
    public function isPembayaranLunas(): bool
    {
        $status = strtolower(trim((string) ($this->status_pembayaran ?? '')));

        if (in_array($status, ['fully_paid', 'lunas'], true)) {
            return true;
        }

        if (! Schema::hasTable('invoices')) {
            return false;
        }

        if ($this->relationLoaded('invoices')) {
            return $this->invoices->contains(function ($invoice) {
                return in_array(strtolower(trim((string) ($invoice->status ?? ''))), ['lunas'], true);
            });
        }

        return $this->invoices()
            ->whereRaw('LOWER(status) = ?', ['lunas'])
            ->exists();
    }

    /**
     * Pesanan boleh dijadwalkan meeting jika sudah lunas ATAU sudah punya Korlap.
     */
    public function allowsVendorMeetingScheduling(): bool
    {
        return $this->isPembayaranLunas() || (bool) $this->korlap_id;
    }

    public function hasFullScheduleAccess(): bool
    {
        return ($this->akses_jadwal ?? 'none') === 'full' || $this->isPembayaranLunas();
    }

    public function hasPartialScheduleAccess(): bool
    {
        return in_array($this->akses_jadwal ?? 'none', ['partial', 'full'], true)
            || in_array($this->status_pembayaran, ['dp_paid', 'fully_paid'], true);
    }

    public function getAksesJadwalLabelAttribute(): string
    {
        return match ($this->akses_jadwal ?? 'none') {
            'full' => 'Akses Penuh (Lunas)',
            'partial' => 'Akses Parsial (DP)',
            default => 'Terkunci',
        };
    }

    public function hasGoogleMapsUrl(): bool
    {
        return filled($this->google_maps_url);
    }

    /**
     * URL Google Maps siap dibuka (dengan skema https).
     */
    public function getGoogleMapsHrefAttribute(): ?string
    {
        $raw = trim((string) ($this->google_maps_url ?? ''));

        if ($raw === '') {
            return null;
        }

        if (! preg_match('/^https?:\/\//i', $raw)) {
            $raw = 'https://'.$raw;
        }

        return $raw;
    }
}
