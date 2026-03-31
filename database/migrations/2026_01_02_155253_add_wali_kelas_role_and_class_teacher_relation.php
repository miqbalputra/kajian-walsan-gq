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
        // Add wali_kelas role
        DB::table('roles')->insert([
            'name' => 'wali_kelas',
            'display_name' => 'Wali Kelas',
            'description' => 'Class teacher with access to class data',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Add teacher_id to classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        });

        DB::table('roles')->where('name', 'wali_kelas')->delete();
    }
};
