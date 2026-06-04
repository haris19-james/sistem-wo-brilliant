<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperasionalLapangan;
use App\Models\Pesanan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OperasionalLapanganController extends Controller
{
    public function store(Request $request, Pesanan $pesanan)
    {
        $validated = $request->validate([
            'korlap_id' => ['required', 'integer', Rule::exists('users', 'id')->where('role', 'lapangan')],
            'jumlah_dialokasikan' => ['required', 'numeric', 'min:1000'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        OperasionalLapangan::create([
            'pesanan_id' => $pesanan->id,
            'korlap_id' => $validated['korlap_id'],
            'allocated_by' => auth()->id(),
            'jumlah_dialokasikan' => $validated['jumlah_dialokasikan'],
            'jumlah_terpakai' => 0,
            'sumber' => 'manual',
            'status' => 'disalurkan',
            'catatan' => $validated['catatan'] ?? null,
        ]);

        if (! $pesanan->korlap_id) {
            $pesanan->update(['korlap_id' => $validated['korlap_id']]);
        }

        return back()->with('success', 'Uang operasional lapangan berhasil dialokasikan.');
    }

    public function updateStatus(Request $request, OperasionalLapangan $operasional)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:draft,disalurkan,selesai'],
        ]);

        $operasional->update(['status' => $validated['status']]);

        return back()->with('success', 'Status operasional diperbarui.');
    }
}
