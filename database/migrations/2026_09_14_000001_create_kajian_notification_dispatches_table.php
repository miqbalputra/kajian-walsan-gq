<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kajian_notification_dispatches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kajian_event_id')->constrained('kajian_events')->cascadeOnDelete();
            $table->string('notification_type', 40);
            $table->timestamp('scheduled_for');
            $table->string('status', 20)->default('queued');
            $table->unsignedInteger('attempts')->default(0);
            $table->unsignedInteger('total_count')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['kajian_event_id', 'notification_type'], 'kajian_notification_event_type_unique');
            $table->index(['status', 'scheduled_for'], 'kajian_notification_status_schedule_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kajian_notification_dispatches');
    }
};
