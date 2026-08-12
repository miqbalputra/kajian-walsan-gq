<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // MariaDB can commit a CREATE TABLE before a later statement in the
        // same migration fails. Keep retries idempotent so a redeploy can
        // continue from the table that was already created.
        if (! Schema::hasTable('promotion_batches')) {
            Schema::create('promotion_batches', function (Blueprint $table) {
                $table->id();
                $table->foreignId('source_academic_year_id')->constrained('academic_years')->restrictOnDelete();
                $table->foreignId('target_academic_year_id')->constrained('academic_years')->restrictOnDelete();
                $table->foreignId('initiated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('status', 20)->default('draft');
                $table->json('class_mapping')->nullable();
                $table->json('summary')->nullable();
                $table->timestamp('applied_at')->nullable();
                $table->timestamp('rolled_back_at')->nullable();
                $table->timestamps();

                $table->unique(['source_academic_year_id', 'target_academic_year_id']);
                $table->index('status');
            });
        }

        if (! Schema::hasTable('promotion_changes')) {
            Schema::create('promotion_changes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('promotion_batch_id')->constrained('promotion_batches')->cascadeOnDelete();
                $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
                $table->foreignId('before_class_id')->nullable()->constrained('classes')->nullOnDelete();
                $table->foreignId('after_class_id')->nullable()->constrained('classes')->nullOnDelete();
                $table->string('before_class_name', 100)->nullable();
                $table->string('after_class_name', 100)->nullable();
                $table->string('before_status', 20)->nullable();
                $table->string('after_status', 20)->nullable();
                $table->boolean('before_is_active')->default(true);
                $table->boolean('after_is_active')->default(true);
                $table->string('action', 30);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['promotion_batch_id', 'student_id']);
                $table->index(['student_id', 'action']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('promotion_changes');
        Schema::dropIfExists('promotion_batches');
    }
};
