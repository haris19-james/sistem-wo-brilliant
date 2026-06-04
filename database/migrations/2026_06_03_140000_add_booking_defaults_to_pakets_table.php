<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            if (! Schema::hasColumn('pakets', 'default_lokasi')) {
                $table->string('default_lokasi')->nullable()->after('deskripsi');
            }
            if (! Schema::hasColumn('pakets', 'kapasitas_tamu')) {
                $table->unsignedInteger('kapasitas_tamu')->nullable()->after('default_lokasi');
            }
            if (! Schema::hasColumn('pakets', 'harga_tambahan_per_tamu')) {
                $table->unsignedInteger('harga_tambahan_per_tamu')->default(0)->after('kapasitas_tamu');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            $cols = ['default_lokasi', 'kapasitas_tamu', 'harga_tambahan_per_tamu'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('pakets', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
