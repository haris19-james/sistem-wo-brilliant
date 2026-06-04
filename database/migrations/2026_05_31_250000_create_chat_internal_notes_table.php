<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('chat_internal_notes')) {
            Schema::create('chat_internal_notes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pesanan_id')->constrained('pesanans')->cascadeOnDelete();
                $table->foreignId('author_id')->constrained('users')->cascadeOnDelete();
                $table->text('catatan');
                $table->timestamps();
            });
        }

        if (Schema::hasTable('chat_messages') && ! Schema::hasColumn('chat_messages', 'is_internal')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->boolean('is_internal')->default(false)->after('dari_admin');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_internal_notes');

        if (Schema::hasTable('chat_messages') && Schema::hasColumn('chat_messages', 'is_internal')) {
            Schema::table('chat_messages', function (Blueprint $table) {
                $table->dropColumn('is_internal');
            });
        }
    }
};
