<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            if (! Schema::hasColumn('user_notifications', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('category');
            }
            if (! Schema::hasColumn('user_notifications', 'reference_type')) {
                $table->string('reference_type', 50)->nullable()->after('reference_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_notifications', function (Blueprint $table) {
            if (Schema::hasColumn('user_notifications', 'reference_type')) {
                $table->dropColumn('reference_type');
            }
            if (Schema::hasColumn('user_notifications', 'reference_id')) {
                $table->dropColumn('reference_id');
            }
        });
    }
};
