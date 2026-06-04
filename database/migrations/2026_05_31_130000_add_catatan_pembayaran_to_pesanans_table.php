<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('pesanans') && ! Schema::hasColumn('pesanans', 'catatan_pembayaran')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->text('catatan_pembayaran')->nullable()->after('status_pembayaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('pesanans') && Schema::hasColumn('pesanans', 'catatan_pembayaran')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dropColumn('catatan_pembayaran');
            });
        }
    }
};
