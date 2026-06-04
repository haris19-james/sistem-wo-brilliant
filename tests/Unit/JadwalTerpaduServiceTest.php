<?php

namespace Tests\Unit;

use App\Models\Pesanan;
use App\Support\JadwalTerpaduService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JadwalTerpaduServiceTest extends TestCase
{
    #[Test]
    public function resolve_main_event_prioritizes_ongoing_event_on_today(): void
    {
        $today = now()->startOfDay();

        $pesanans = collect([
            $this->makePesanan(1, 'A & B', $today->copy()->addMonth(), 'Menunggu'),
            $this->makePesanan(2, 'Niki & Abdi', $today, 'Sedang Berlangsung'),
        ]);

        $main = JadwalTerpaduService::resolveMainEvent($pesanans);

        $this->assertNotNull($main);
        $this->assertSame(2, $main->id);
    }

    #[Test]
    public function resolve_main_event_picks_nearest_future_when_no_ongoing(): void
    {
        $pesanans = collect([
            $this->makePesanan(1, 'A & B', now()->addMonths(2), 'Menunggu'),
            $this->makePesanan(2, 'C & D', now()->addWeek(), 'Menunggu'),
        ]);

        $main = JadwalTerpaduService::resolveMainEvent($pesanans);

        $this->assertSame(2, $main->id);
    }

    #[Test]
    public function main_event_status_returns_sedang_berlangsung_for_today_event(): void
    {
        $pesanan = $this->makePesanan(1, 'Niki & Abdi', now()->startOfDay(), 'Sedang Berlangsung');

        $status = JadwalTerpaduService::mainEventStatus($pesanan);

        $this->assertSame('Sedang Berlangsung', $status['label']);
    }

    #[Test]
    public function can_add_vendor_meeting_when_fully_paid_without_korlap(): void
    {
        $pesanan = $this->makePesanan(1, 'Niki & Abdi', now(), 'Sedang Berlangsung');
        $pesanan->status_pembayaran = 'fully_paid';
        $pesanan->korlap_id = null;

        $this->assertTrue(JadwalTerpaduService::canAddVendorMeeting($pesanan));
        $this->assertTrue($pesanan->allowsVendorMeetingScheduling());
    }

    #[Test]
    public function can_add_vendor_meeting_when_status_lunas_label_without_korlap(): void
    {
        $pesanan = $this->makePesanan(1, 'Niki & Abdi', now(), 'Sedang Berlangsung');
        $pesanan->status_pembayaran = 'Lunas';
        $pesanan->korlap_id = null;

        $this->assertTrue($pesanan->isPembayaranLunas());
        $this->assertTrue($pesanan->allowsVendorMeetingScheduling());
    }

    #[Test]
    public function cannot_add_vendor_meeting_when_unpaid_and_no_korlap(): void
    {
        $pesanan = $this->makePesanan(1, 'A & B', now()->addMonth(), 'Menunggu');
        $pesanan->status_pembayaran = 'dp_paid';
        $pesanan->korlap_id = null;

        $this->assertFalse(JadwalTerpaduService::canAddVendorMeeting($pesanan));
        $this->assertFalse($pesanan->allowsVendorMeetingScheduling());
    }

    private function makePesanan(int $id, string $nama, Carbon $tanggal, string $status): Pesanan
    {
        $pesanan = new Pesanan([
            'nama_pasangan' => $nama,
            'tanggal_acara' => $tanggal,
            'status' => $status,
        ]);
        $pesanan->id = $id;

        return $pesanan;
    }
}
