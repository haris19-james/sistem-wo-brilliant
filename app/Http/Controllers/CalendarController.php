<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pesanan;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function downloadIcs(Pesanan $pesanan)
    {
        $title = 'Acara - ' . ($pesanan->nomor_pesanan ?? 'Booking');
        $start = Carbon::parse($pesanan->tanggal_acara . ' ' . ($pesanan->jam_acara ?? '00:00'))->format('Ymd\THis');
        $end = Carbon::parse($pesanan->tanggal_acara . ' ' . ($pesanan->jam_berakhir ?? ($pesanan->jam_acara ?? '00:00')))->addHours(2)->format('Ymd\THis');
        $uid = 'pesanan-' . $pesanan->id . '@brilliant.local';
        $location = $pesanan->lokasi ?? '';
        $description = 'Nomor Pesanan: ' . ($pesanan->nomor_pesanan ?? '-') . '\nVendor: ' . ($pesanan->vendor_nama ?? ($pesanan->vendor->name ?? '-'));

        $ics = "BEGIN:VCALENDAR\r\nVERSION:2.0\r\nPRODID:-//Brilliant WO//EN\r\nBEGIN:VEVENT\r\nUID:{$uid}\r\nDTSTAMP:" . Carbon::now()->format('Ymd\THis') . "\r\nDTSTART:{$start}\r\nDTEND:{$end}\r\nSUMMARY:{$title}\r\nLOCATION:{$location}\r\nDESCRIPTION:{$description}\r\nEND:VEVENT\r\nEND:VCALENDAR";

        $fileName = 'brilliant-event-' . $pesanan->id . '.ics';

        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
        ]);
    }
}
