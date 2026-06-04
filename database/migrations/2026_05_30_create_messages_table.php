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
        // Add columns to existing chat_messages table if needed
        // atau buat table baru 'messages' untuk struktur yang lebih baik
        
        Schema::table('chat_messages', function (Blueprint $table) {
            // Cek apakah kolom sudah ada sebelum menambahkan
            if (!Schema::hasColumn('chat_messages', 'sender_id')) {
                $table->foreignId('sender_id')->nullable()->constrained('users')->cascadeOnDelete()->after('user_id');
            }
            
            if (!Schema::hasColumn('chat_messages', 'receiver_id')) {
                $table->foreignId('receiver_id')->nullable()->constrained('users')->cascadeOnDelete()->after('sender_id');
            }
            
            if (!Schema::hasColumn('chat_messages', 'is_read')) {
                $table->boolean('is_read')->default(false)->after('pesan');
            }
            
            if (!Schema::hasColumn('chat_messages', 'booking_id')) {
                $table->foreignId('booking_id')->nullable()->constrained('pesanans')->cascadeOnDelete()->after('pesanan_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('chat_messages', 'sender_id')) {
                $table->dropForeignIdFor(\App\Models\User::class, 'sender_id');
            }
            if (Schema::hasColumn('chat_messages', 'receiver_id')) {
                $table->dropForeignIdFor(\App\Models\User::class, 'receiver_id');
            }
            if (Schema::hasColumn('chat_messages', 'is_read')) {
                $table->dropColumn('is_read');
            }
            if (Schema::hasColumn('chat_messages', 'booking_id')) {
                $table->dropForeignIdFor(\App\Models\Pesanan::class, 'booking_id');
            }
        });
    }
};
