<?php

namespace App\Services;

use App\Models\Pesanan;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class BookingAvailabilityService
{
    public const CONFLICT_MESSAGE = 'Maaf, tanggal pernikahan yang Anda pilih sudah dibooking oleh klien lain. Silakan pilih tanggal alternatif.';

    /**
     * Tanggal acara yang sudah dibooking sah (minimal DP / lunas).
     *
     * @return list<string> Format Y-m-d
     */
    public function disabledDates(): array
    {
        return Pesanan::query()
            ->blocksEventDate()
            ->whereNotNull('tanggal_acara')
            ->selectRaw('DATE(tanggal_acara) as event_date')
            ->distinct()
            ->orderBy('event_date')
            ->pluck('event_date')
            ->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function isDateAvailable(string $date, ?int $excludePesananId = null): bool
    {
        $normalized = Carbon::parse($date)->format('Y-m-d');

        $query = Pesanan::query()
            ->blocksEventDate()
            ->whereDate('tanggal_acara', $normalized);

        if ($excludePesananId) {
            $query->where('id', '!=', $excludePesananId);
        }

        return ! $query->exists();
    }

    /**
     * @throws ValidationException
     * @throws HttpResponseException
     */
    public function assertDateAvailable(Request $request, string $date, ?int $excludePesananId = null): void
    {
        if ($this->isDateAvailable($date, $excludePesananId)) {
            return;
        }

        if ($request->wantsJson() || $request->ajax()) {
            throw new HttpResponseException(response()->json([
                'message' => self::CONFLICT_MESSAGE,
                'errors' => ['tanggal_acara' => [self::CONFLICT_MESSAGE]],
            ], 400));
        }

        throw ValidationException::withMessages([
            'tanggal_acara' => self::CONFLICT_MESSAGE,
        ]);
    }
}
