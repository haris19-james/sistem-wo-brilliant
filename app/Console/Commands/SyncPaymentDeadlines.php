<?php

namespace App\Console\Commands;

use App\Services\PaymentDeadlineService;
use Illuminate\Console\Command;

class SyncPaymentDeadlines extends Command
{
    protected $signature = 'payment:sync-deadlines';

    protected $description = 'Sinkronkan tanggal jatuh tempo pelunasan (H-14 event) dan status_deadline semua pesanan aktif';

    public function handle(): int
    {
        $count = PaymentDeadlineService::syncAllActive();

        $this->info("Berhasil memperbarui {$count} pesanan.");

        return self::SUCCESS;
    }
}
