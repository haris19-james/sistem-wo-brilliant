<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pesanans', 'google_maps_url')) {
            return;
        }

        Schema::table('pesanans', function (Blueprint $table) {
            $table->text('google_maps_url')->nullable()->after('lokasi');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('pesanans', 'google_maps_url')) {
            Schema::table('pesanans', function (Blueprint $table) {
                $table->dropColumn('google_maps_url');
            });
        }
    }
};
