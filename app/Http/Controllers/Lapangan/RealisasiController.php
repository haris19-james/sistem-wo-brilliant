<?php

namespace App\Http\Controllers\Lapangan;

use App\Http\Controllers\Controller;
use App\Models\OperasionalLapangan;
use App\Models\Pesanan;
use App\Models\RealisasiOperasional;
use App\Support\ImageHelper;
use Illuminate\Http\Request;

class RealisasiController extends Controller
{
    public function index(Pesanan $pesanan)
    {
        $this->authorizeKorlap($pesanan);

        $operasional = OperasionalLapangan::query()
            ->where('pesanan_id', $pesanan->id)
            ->where('korlap_id', auth()->id())
            ->with('realisasi')
            ->latest()
            ->get();

        return view('lapangan.modules.realisasi.index', [
            'activeMenu' => 'pesanan',
            'pesanan' => $pesanan,
            'operasional' => $operasional,
        ]);
    }

    public function store(Request $request, Pesanan $pesanan, OperasionalLapangan $operasional)
    {
        $this->authorizeKorlap($pesanan);

        if ($operasional->pesanan_id !== $pesanan->id || $operasional->korlap_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'judul' => ['required', 'string', 'max:255'],
            'jumlah' => ['required', 'numeric', 'min:1', 'max:'.$operasional->sisaAnggaran()],
            'tanggal_pengeluaran' => ['required', 'date', 'before_or_equal:today'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'bukti_nota' => ['required', 'file', 'max:5120', 'mimes:jpg,jpeg,png,webp,pdf'],
        ]);

        $bukti = ImageHelper::storeUploaded($request->file('bukti_nota'), 'realisasi-nota');

        RealisasiOperasional::create([
            'operasional_lapangan_id' => $operasional->id,
            'pesanan_id' => $pesanan->id,
            'korlap_id' => auth()->id(),
            'judul' => $validated['judul'],
            'jumlah' => $validated['jumlah'],
            'tanggal_pengeluaran' => $validated['tanggal_pengeluaran'],
            'keterangan' => $validated['keterangan'] ?? null,
            'bukti_nota' => $bukti,
            'status' => 'Menunggu Review',
        ]);

        $operasional->increment('jumlah_terpakai', $validated['jumlah']);

        return back()->with('success', 'Laporan realisasi penggunaan dana berhasil dikirim.');
    }

    private function authorizeKorlap(Pesanan $pesanan): void
    {
        if ($pesanan->korlap_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        if (! in_array($pesanan->status_pembayaran, ['dp_paid', 'fully_paid'], true)) {
            abort(403, 'Pesanan belum diverifikasi pembayaran.');
        }
    }
}
