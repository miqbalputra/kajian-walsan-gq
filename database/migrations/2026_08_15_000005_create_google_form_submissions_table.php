<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('response_id', 191)->unique();
            $table->string('form_id', 191)->nullable()->index();
            $table->foreignId('kajian_event_id')->nullable()->constrained('kajian_events')->nullOnDelete();
            $table->foreignId('attendance_id')->nullable()->constrained('attendances')->nullOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete();
            $table->date('event_date')->nullable()->index();
            $table->string('requested_status', 30)->nullable();
            $table->string('processing_status', 30)->default('received')->index();
            $table->string('error_code', 60)->nullable();
            $table->text('error_message')->nullable();
            $table->json('payload');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('received_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['processing_status', 'event_date']);
            $table->index(['kajian_event_id', 'parent_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_form_submissions');
    }
};
