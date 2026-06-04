<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vendor_meetings', function (Blueprint $table) {
            if (! Schema::hasColumn('vendor_meetings', 'vendor_id')) {
                $table->foreignId('vendor_id')
                    ->nullable()
                    ->after('booking_id')
                    ->constrained('vendors')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('vendor_meetings', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_meetings', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
        });
    }
};
