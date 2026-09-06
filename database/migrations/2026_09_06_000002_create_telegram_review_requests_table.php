<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_review_requests', function (Blueprint $table) {
            $table->id();
            $table->ulid('token')->unique();
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->string('proof_file');
            $table->string('chat_id', 32);
            $table->unsignedBigInteger('message_id')->nullable();
            $table->enum('delivery_status', ['queued', 'sent', 'failed', 'expired', 'resolved'])->default('queued');
            $table->unsignedTinyInteger('delivery_attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->enum('decision', ['approved', 'rejected'])->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reviewed_by_telegram_id', 32)->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->index(['attendance_id', 'delivery_status']);
            $table->index(['attendance_id', 'proof_file']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_review_requests');
    }
};
