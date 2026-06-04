<?php

namespace App\Http\Controllers;

use App\Models\PembayaranKonfirmasi;
use Illuminate\Support\Facades\Storage;

class BuktiPembayaranController extends Controller
{
    public function show(PembayaranKonfirmasi $konfirmasi)
    {
        $user = auth()->user();

        if ($user->role === 'client' && $konfirmasi->user_id !== $user->id) {
            abort(403);
        }

        if (! in_array($user->role, ['admin', 'client', 'customer'], true)) {
            abort(403);
        }

        if (! $konfirmasi->bukti_transfer || ! Storage::disk('public')->exists($konfirmasi->bukti_transfer)) {
            abort(404, 'File bukti transfer tidak ditemukan.');
        }

        return Storage::disk('public')->response($konfirmasi->bukti_transfer);
    }
}
