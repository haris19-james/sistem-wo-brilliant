<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('instagram')->nullable()->after('kontak');
            $table->string('whatsapp')->nullable()->after('instagram');
            $table->string('website')->nullable()->after('whatsapp');
            $table->json('portfolio_images')->nullable()->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['instagram', 'whatsapp', 'website', 'portfolio_images']);
        });
    }
};
