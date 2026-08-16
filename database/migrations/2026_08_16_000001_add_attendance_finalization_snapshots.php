<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('kajian_events', function (Blueprint $table) {
            $table->timestamp('closed_at')->nullable()->after('status');
            $table->foreignId('closed_by')->nullable()->after('closed_at')->constrained('users')->nullOnDelete();
        });

        Schema::create('attendance_roster_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_event_id')->constrained('kajian_events')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('student_enrollment_id')->nullable()->constrained('student_enrollments')->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('student_name')->nullable();
            $table->string('class_name')->nullable();
            $table->timestamps();

            $table->unique(['kajian_event_id', 'parent_id']);
            $table->index(['kajian_event_id', 'class_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_roster_snapshots');

        Schema::table('kajian_events', function (Blueprint $table) {
            $table->dropConstrainedForeignId('closed_by');
            $table->dropColumn('closed_at');
        });
    }
};
