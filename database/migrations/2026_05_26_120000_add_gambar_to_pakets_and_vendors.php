<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('gambar_url');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->string('gambar')->nullable()->after('status');
            $table->string('gambar_url')->nullable()->after('gambar');
            $table->string('lokasi')->nullable()->after('kategori');
            $table->string('harga_info')->nullable()->after('lokasi');
        });
    }

    public function down(): void
    {
        Schema::table('pakets', function (Blueprint $table) {
            $table->dropColumn('gambar');
        });

        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['gambar', 'gambar_url', 'lokasi', 'harga_info']);
        });
    }
};
