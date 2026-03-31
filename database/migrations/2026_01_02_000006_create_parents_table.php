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
        Schema::create('parents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['father', 'mother']); // Ayah / Ibu
            $table->string('qr_code_string', 100)->unique(); // Unique QR identifier
            $table->string('nik', 20)->nullable(); // Nomor Induk Kependudukan
            $table->string('occupation', 100)->nullable(); // Pekerjaan
            $table->text('address')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('qr_code_string');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parents');
    }
};
