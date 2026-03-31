<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->unique(); // admin, panitia, kepsek, wali_santri
            $table->string('display_name', 100)->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default roles
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrator', 'description' => 'Full access to system', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'panitia', 'display_name' => 'Panitia', 'description' => 'Event committee member', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kepsek', 'display_name' => 'Kepala Sekolah', 'description' => 'School principal with view access', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'wali_santri', 'display_name' => 'Wali Santri', 'description' => 'Student guardian/parent', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
