<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('laporan_lapangans', function (Blueprint $table) {
            if (!Schema::hasColumn('laporan_lapangans', 'dokumentasi_path')) {
                if (Schema::hasColumn('laporan_lapangans', 'foto_path')) {
                    $table->string('dokumentasi_path')->nullable()->after('foto_path');
                } else {
                    $table->string('dokumentasi_path')->nullable();
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('laporan_lapangans', function (Blueprint $table) {
            $table->dropColumn('dokumentasi_path');
        });
    }
};
