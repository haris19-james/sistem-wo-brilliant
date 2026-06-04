<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            $table->boolean('is_kustom')->default(false)->after('harga');
        });

        Schema::table('pesanans', function (Blueprint $table) {
            $table->text('detail_paket_kustom')->nullable()->after('catatan_khusus');
            $table->decimal('estimasi_budget', 15, 2)->nullable()->after('detail_paket_kustom');
        });
    }

    public function down(): void
    {
        Schema::table('pesanans', function (Blueprint $table) {
            $table->dropColumn(['detail_paket_kustom', 'estimasi_budget']);
        });

        Schema::table('pakets', function (Blueprint $table) {
            $table->dropColumn('is_kustom');
        });
    }
};
