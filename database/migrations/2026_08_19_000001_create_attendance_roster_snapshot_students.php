<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // MariaDB DDL is not rolled back when a deployment is interrupted
        // after CREATE TABLE succeeds but before Laravel records the batch.
        // Treat an already-created table as applied so a container restart can
        // finish the migration without dropping any existing snapshot rows.
        if (Schema::hasTable('attendance_roster_snapshot_students')) {
            return;
        }

        Schema::create('attendance_roster_snapshot_students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_roster_snapshot_id')
                ->constrained('attendance_roster_snapshots')
                ->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('student_enrollment_id')->nullable()->constrained('student_enrollments')->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('student_nis')->nullable();
            $table->string('class_name')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_type', 20)->nullable();
            $table->timestamps();

            $table->unique(['attendance_roster_snapshot_id', 'parent_id', 'student_id'], 'roster_snapshot_student_unique');
            $table->index(['attendance_roster_snapshot_id', 'class_id'], 'roster_snapshot_class_index');
            $table->index(['student_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_roster_snapshot_students');
    }
};
