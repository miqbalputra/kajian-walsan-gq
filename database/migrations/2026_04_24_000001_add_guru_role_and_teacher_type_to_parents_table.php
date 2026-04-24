<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add guru role
        DB::table('roles')->insertOrIgnore([
            'name' => 'guru',
            'display_name' => 'Guru',
            'description' => 'Pengajar yang wajib ikut kajian wali santri',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Add teacher to parents.type enum
        // MariaDB/MySQL specific
        if (config('database.default') === 'mysql' || config('database.default') === 'mariadb') {
            DB::statement("ALTER TABLE parents MODIFY COLUMN type ENUM('father', 'mother', 'teacher') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql' || config('database.default') === 'mariadb') {
            DB::statement("ALTER TABLE parents MODIFY COLUMN type ENUM('father', 'mother') NOT NULL");
        }
        
        DB::table('roles')->where('name', 'guru')->delete();
    }
};
