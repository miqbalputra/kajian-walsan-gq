<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Pivot table for many-to-many relationship:
     * - One parent (father/mother) can have multiple children (siblings)
     * - One student can have multiple parents (father + mother = 2 accounts)
     */
    public function up(): void
    {
        Schema::create('parent_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->enum('relationship', ['biological', 'guardian', 'step'])->default('biological');
            $table->boolean('is_primary_contact')->default(false); // Primary contact for this student
            $table->timestamps();

            // Prevent duplicate parent-student combinations
            $table->unique(['parent_id', 'student_id']);

            $table->index('parent_id');
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parent_student');
    }
};
