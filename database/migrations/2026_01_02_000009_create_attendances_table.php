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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_event_id')->constrained('kajian_events')->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete(); // Snapshot: which child represented

            // Attendance status
            $table->enum('status', ['hadir_fisik', 'hadir_online', 'izin', 'alpha'])->default('hadir_fisik');

            // Method of recording
            $table->enum('method', ['scan_qr', 'manual', 'upload'])->default('scan_qr');

            // For upload proof (izin letter, online screenshot, etc)
            $table->string('proof_file')->nullable();
            $table->text('notes')->nullable(); // Additional notes/reason

            // Validation for manual/upload entries
            $table->enum('validation_status', ['approved', 'pending', 'rejected'])->default('approved');
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->text('rejection_reason')->nullable();

            // Scan metadata
            $table->timestamp('scanned_at')->nullable();
            $table->string('scan_location')->nullable(); // GPS coordinates if available
            $table->string('device_info')->nullable(); // Browser/device info

            $table->timestamps();

            // Prevent duplicate attendance for same event & parent
            $table->unique(['kajian_event_id', 'parent_id']);

            $table->index('kajian_event_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('validation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
