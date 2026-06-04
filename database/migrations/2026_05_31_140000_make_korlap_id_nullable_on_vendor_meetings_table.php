<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_meetings', function (Blueprint $table) {
            $table->dropForeign(['korlap_id']);
        });

        Schema::table('vendor_meetings', function (Blueprint $table) {
            $table->unsignedBigInteger('korlap_id')->nullable()->change();
            $table->foreign('korlap_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('vendor_meetings', function (Blueprint $table) {
            $table->dropForeign(['korlap_id']);
        });

        Schema::table('vendor_meetings', function (Blueprint $table) {
            $table->unsignedBigInteger('korlap_id')->nullable(false)->change();
            $table->foreign('korlap_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });
    }
};
