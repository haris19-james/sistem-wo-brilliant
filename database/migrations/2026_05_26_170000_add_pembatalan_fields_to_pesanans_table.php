<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->text('alasan_pembatalan')->nullable()->after('catatan_khusus');
            $table->timestamp('pembatalan_diminta_at')->nullable()->after('alasan_pembatalan');
            $table->timestamp('dibatalkan_at')->nullable()->after('pembatalan_diminta_at');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn(['alasan_pembatalan', 'pembatalan_diminta_at', 'dibatalkan_at']);
        });
    }
};
