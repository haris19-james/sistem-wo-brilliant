<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'client', 'lapangan') NOT NULL DEFAULT 'customer'");
        }

        DB::table('users')->where('role', 'customer')->update(['role' => 'client']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'client', 'lapangan') NOT NULL DEFAULT 'client'");
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'client', 'lapangan') NOT NULL DEFAULT 'client'");
        }

        DB::table('users')->where('role', 'client')->update(['role' => 'customer']);

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'customer', 'lapangan') NOT NULL DEFAULT 'customer'");
        }
    }
};
