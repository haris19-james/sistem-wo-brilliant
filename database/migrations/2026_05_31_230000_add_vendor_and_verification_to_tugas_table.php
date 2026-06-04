<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tugas', function (Blueprint $table) {
            $table->foreignId('vendor_id')->nullable()->after('pesanan_id')->constrained('vendors')->cascadeOnDelete();
            $table->boolean('is_auto_generated')->default(false)->after('status');
            $table->timestamp('korlap_verified_at')->nullable()->after('is_auto_generated');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tugas MODIFY COLUMN status ENUM('pending', 'in_progress', 'awaiting_verification', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE tugas MODIFY COLUMN status ENUM('pending', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('tugas', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropColumn(['vendor_id', 'is_auto_generated', 'korlap_verified_at']);
        });
    }
};
