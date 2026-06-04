<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pesanans')) {
            return;
        }

        Schema::table('pesanans', function (Blueprint $table) {
            if (! Schema::hasColumn('pesanans', 'tanggal_jatuh_tempo')) {
                $table->date('tanggal_jatuh_tempo')->nullable()->after('tanggal_acara');
            }
            if (! Schema::hasColumn('pesanans', 'status_deadline')) {
                $table->enum('status_deadline', ['safe', 'warning', 'overdue'])
                    ->default('safe')
                    ->after('tanggal_jatuh_tempo');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pesanans')) {
            return;
        }

        Schema::table('pesanans', function (Blueprint $table) {
            if (Schema::hasColumn('pesanans', 'status_deadline')) {
                $table->dropColumn('status_deadline');
            }
            if (Schema::hasColumn('pesanans', 'tanggal_jatuh_tempo')) {
                $table->dropColumn('tanggal_jatuh_tempo');
            }
        });
    }
};
