<?php

namespace App\Models;

use App\Support\ImageHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembayaranKonfirmasi extends Model
{
    use HasFactory;

    /** @var list<string> Field audit — tidak boleh diubah setelah customer submit */
    public const IMMUTABLE_AFTER_CREATE = [
        'invoice_id',
        'user_id',
        'jenis_pembayaran',
        'jumlah',
        'bank_pengirim',
        'nama_pengirim',
        'tanggal_transfer',
        'bukti_transfer',
        'catatan',
    ];

    protected $fillable = [
        'invoice_id',
        'user_id',
        'jenis_pembayaran',
        'urutan_cicilan',
        'jumlah',
        'bank_pengirim',
        'nama_pengirim',
        'tanggal_transfer',
        'tanggal_jatuh_tempo',
        'bukti_transfer',
        'catatan',
        'status',
        'status_verifikasi',
        'catatan_admin',
        'alasan_penolakan',
        'confirmed_by',
        'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'tanggal_transfer' => 'date',
            'tanggal_jatuh_tempo' => 'date',
            'urutan_cicilan' => 'integer',
            'confirmed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (PembayaranKonfirmasi $model) {
            foreach (self::IMMUTABLE_AFTER_CREATE as $field) {
                if ($model->isDirty($field)) {
                    $model->{$field} = $model->getOriginal($field);
                }
            }
        });
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function adminKonfirmasi()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public function isPending(): bool
    {
        return ($this->status_verifikasi ?? 'pending') === 'pending'
            || $this->status === 'Menunggu Konfirmasi';
    }

    public function resolveStatusVerifikasiAfterApprove(bool $invoiceLunas): string
    {
        if ($invoiceLunas || $this->jenis_pembayaran === 'Pelunasan') {
            return 'approved_lunas';
        }

        return 'approved_dp';
    }

    public function getBuktiUrlAttribute(): ?string
    {
        if (! $this->bukti_transfer) {
            return null;
        }

        if ($this->id) {
            return route('pembayaran.bukti', $this);
        }

        return ImageHelper::publicPath($this->bukti_transfer);
    }

    public function getStatusVerifikasiLabelAttribute(): string
    {
        return match ($this->status_verifikasi ?? 'pending') {
            'approved_dp' => 'Terverifikasi DP',
            'approved_lunas' => 'Terverifikasi Lunas',
            'rejected' => 'Ditolak',
            default => 'Menunggu Verifikasi',
        };
    }

    public function getStatusVerifikasiBadgeClassAttribute(): string
    {
        return match ($this->status_verifikasi ?? 'pending') {
            'approved_dp' => 'bg-yellow-50 text-yellow-800 border border-yellow-200',
            'approved_lunas' => 'bg-green-50 text-green-800 border border-green-200',
            'rejected' => 'bg-red-50 text-red-700 border border-red-200',
            default => 'bg-amber-50 text-amber-800 border border-amber-200',
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return $this->status_verifikasi_badge_class;
    }

    public function getNomorTransaksiAttribute(): string
    {
        return 'TRX-'.str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }
}
