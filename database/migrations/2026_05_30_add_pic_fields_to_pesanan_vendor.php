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
        Schema::table('pesanan_vendor', function (Blueprint $table) {
            $table->string('nama_pic', 100)->nullable()->after('status');
            $table->string('kontak_pic', 15)->nullable()->after('nama_pic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesanan_vendor', function (Blueprint $table) {
            $table->dropColumn(['nama_pic', 'kontak_pic']);
        });
    }
};
