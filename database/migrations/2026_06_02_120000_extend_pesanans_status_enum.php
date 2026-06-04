<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pesanans') || ! Schema::hasColumn('pesanans', 'status')) {
            return;
        }

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE pesanans MODIFY COLUMN status ENUM(
            'Menunggu',
            'Sedang Berlangsung',
            'Mendesak',
            'Expired',
            'Selesai',
            'Dibatalkan'
        ) NOT NULL DEFAULT 'Menunggu'");
    }

    public function down(): void
    {
        if (! Schema::hasTable('pesanans') || DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::table('pesanans')->where('status', 'Mendesak')->update(['status' => 'Sedang Berlangsung']);
        DB::table('pesanans')->where('status', 'Expired')->update(['status' => 'Sedang Berlangsung']);

        DB::statement("ALTER TABLE pesanans MODIFY COLUMN status ENUM(
            'Menunggu',
            'Sedang Berlangsung',
            'Selesai',
            'Dibatalkan'
        ) NOT NULL DEFAULT 'Menunggu'");
    }
};
