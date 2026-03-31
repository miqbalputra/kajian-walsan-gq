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
        Schema::create('kajian_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('speaker', 100)->nullable(); // Pemateri/Ustadz
            $table->string('location', 200)->nullable();
            $table->date('date');
            $table->time('time_start');
            $table->time('time_end');
            $table->enum('status', ['draft', 'open', 'ongoing', 'closed'])->default('draft');
            $table->string('qr_code_image')->nullable(); // Generated QR image path
            $table->integer('attendance_count')->default(0); // Cached count
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('academic_year_id');
            $table->index('date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kajian_events');
    }
};
