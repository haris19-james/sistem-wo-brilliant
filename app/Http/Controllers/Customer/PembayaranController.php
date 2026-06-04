<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PembayaranKonfirmasi;
use App\Support\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    public function create(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        if ($invoice->status === 'Lunas') {
            return redirect()
                ->route('client.pembayaran')
                ->with('error', 'Invoice ini sudah lunas.');
        }

        if ($invoice->konfirmasiPending) {
            return redirect()
                ->route('client.pembayaran')
                ->with('error', 'Anda masih memiliki konfirmasi pembayaran yang menunggu persetujuan admin.');
        }

        if (! $invoice->jatuh_tempo_dp) {
            $invoice->applyPaymentSchedule();
            $invoice->save();
        }

        $invoice->load(['pesanan.paket']);

        return view('customer.modules.pembayaran.create', [
            'activeMenu' => 'pembayaran',
            'invoice' => $invoice,
            'rekening' => config('pembayaran.rekening', []),
            'dpMinimum' => $invoice->dp_minimum,
            'jadwal' => $invoice->jadwal_pembayaran_ringkas,
            'buktiMaxKb' => (int) config('pembayaran.bukti_max_kb', 10240),
            'uploadMaxPhp' => ini_get('upload_max_filesize'),
        ]);
    }

    public function store(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        if ($invoice->status === 'Lunas') {
            return back()->with('error', 'Invoice sudah lunas.');
        }

        if ($invoice->konfirmasiPending()->exists()) {
            return back()->with('error', 'Konfirmasi sebelumnya masih menunggu admin.');
        }

        $sisa = (float) $invoice->sisa_pembayaran;
        $maxKb = (int) config('pembayaran.bukti_max_kb', 10240);

        if (! $request->hasFile('bukti_transfer')) {
            $postMax = ini_get('post_max_size');
            $uploadMax = ini_get('upload_max_filesize');

            throw ValidationException::withMessages([
                'bukti_transfer' => "File bukti wajib diunggah. Jika sudah memilih foto, kemungkinan ukurannya melebihi batas server ({$uploadMax}). Kompres gambar atau hubungi admin.",
            ]);
        }

        $file = $request->file('bukti_transfer');
        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'bukti_transfer' => ImageHelper::uploadErrorMessage($file),
            ]);
        }

        $validated = $request->validate([
            'jenis_pembayaran' => 'required|in:DP,Pelunasan,Cicilan',
            'jumlah' => 'required|numeric|min:1000|max:'.number_format($sisa, 2, '.', ''),
            'bank_pengirim' => 'required|string|max:100',
            'nama_pengirim' => 'required|string|max:150',
            'tanggal_transfer' => 'required|date|before_or_equal:today',
            'bukti_transfer' => [
                'required',
                'file',
                'max:'.$maxKb,
            ],
            'catatan' => 'nullable|string|max:500',
        ], [
            'bukti_transfer.max' => 'Ukuran bukti maksimal '.round($maxKb / 1024, 1).' MB.',
            'bukti_transfer.mimes' => 'Format bukti: JPG, PNG, WEBP, atau GIF.',
            'jumlah.max' => 'Jumlah tidak boleh melebihi sisa tagihan.',
        ]);

        if ($validated['jenis_pembayaran'] === 'DP' && (float) $invoice->dp_dibayar === 0) {
            $dpMin = $invoice->dp_minimum;
            if ((float) $validated['jumlah'] < $dpMin) {
                return back()
                    ->withInput()
                    ->withErrors(['jumlah' => 'DP minimal Rp '.number_format($dpMin, 0, ',', '.').' ('.config('pembayaran.dp_persen', 30).'%).']);
            }
        }

        try {
            $bukti = ImageHelper::storeBuktiTransfer($file);
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages([
                'bukti_transfer' => $e->getMessage(),
            ]);
        }

        PembayaranKonfirmasi::create([
            'invoice_id' => $invoice->id,
            'user_id' => Auth::id(),
            'jenis_pembayaran' => $validated['jenis_pembayaran'],
            'jumlah' => $validated['jumlah'],
            'bank_pengirim' => $validated['bank_pengirim'],
            'nama_pengirim' => $validated['nama_pengirim'],
            'tanggal_transfer' => $validated['tanggal_transfer'],
            'bukti_transfer' => $bukti,
            'catatan' => $validated['catatan'] ?? null,
            'status' => 'Menunggu Konfirmasi',
            'status_verifikasi' => 'pending',
        ]);

        $invoice->loadMissing('pesanan');
        if ($invoice->pesanan) {
            app(\App\Services\NotificationCenterService::class)->paymentSubmittedToAdmins($invoice->pesanan);
        }

        return redirect()
            ->route('client.pembayaran.pesanan', $invoice->pesanan_id)
            ->with('success', 'Bukti pembayaran terkirim. Tim admin akan memverifikasi dalam 1×24 jam.');
    }

    private function authorizeInvoice(Invoice $invoice): void
    {
        $invoice->loadMissing('pesanan');

        if ($invoice->pesanan?->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
