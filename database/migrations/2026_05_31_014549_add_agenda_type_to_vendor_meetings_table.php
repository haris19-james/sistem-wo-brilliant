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
        Schema::table('vendor_meetings', function (Blueprint $table) {
            // Tipe agenda: 'technical_meeting' (meeting dengan vendor), 'self_preparation' (persiapan mandiri)
            if (!Schema::hasColumn('vendor_meetings', 'agenda_type')) {
                $table->enum('agenda_type', ['technical_meeting', 'self_preparation'])
                    ->default('technical_meeting')
                    ->after('location')
                    ->comment('Tipe agenda: technical_meeting (meeting vendor), self_preparation (persiapan mandiri customer)');
            }

            // Flag untuk tracking apakah agenda ini auto-generated atau manual
            if (!Schema::hasColumn('vendor_meetings', 'is_auto_generated')) {
                $table->boolean('is_auto_generated')
                    ->default(false)
                    ->after('agenda_type')
                    ->comment('True jika agenda ini auto-generated saat booking status menjadi dp_paid');
            }

            // Untuk tracking H-X (berapa hari sebelum event acara)
            if (!Schema::hasColumn('vendor_meetings', 'days_before_event')) {
                $table->unsignedInteger('days_before_event')
                    ->nullable()
                    ->after('is_auto_generated')
                    ->comment('Tracking: hari ke-X sebelum tanggal acara (e.g., 60 untuk H-60)');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendor_meetings', function (Blueprint $table) {
            if (Schema::hasColumn('vendor_meetings', 'agenda_type')) {
                $table->dropColumn('agenda_type');
            }
            if (Schema::hasColumn('vendor_meetings', 'is_auto_generated')) {
                $table->dropColumn('is_auto_generated');
            }
            if (Schema::hasColumn('vendor_meetings', 'days_before_event')) {
                $table->dropColumn('days_before_event');
            }
        });
    }
};
