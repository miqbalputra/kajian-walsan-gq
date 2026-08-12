<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_ai_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained('attendances')->cascadeOnDelete();
            $table->string('proof_hash', 64);
            $table->string('provider', 40)->default('rapidocr');
            $table->string('model')->nullable();
            $table->string('status', 24)->default('queued');
            $table->string('decision', 24)->nullable();
            $table->string('reason_code', 60)->nullable();
            $table->unsignedTinyInteger('confidence')->nullable();
            $table->unsignedInteger('text_chars')->nullable();
            $table->unsignedInteger('text_boxes')->nullable();
            $table->string('language', 20)->nullable();
            $table->string('document_signal', 24)->nullable();
            $table->text('reason')->nullable();
            $table->text('raw_text_preview')->nullable();
            $table->json('payload')->nullable();
            $table->text('error')->nullable();
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['attendance_id', 'proof_hash', 'provider'], 'attendance_ai_reviews_idempotency');
            $table->index(['status', 'created_at']);
            $table->index(['decision', 'reason_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_ai_reviews');
    }
};
