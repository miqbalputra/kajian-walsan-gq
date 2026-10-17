<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guardian_follow_up_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('parents')->cascadeOnDelete();
            $table->foreignId('kajian_event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('status', 20)->default('open');
            $table->date('next_follow_up_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'parent_id', 'kajian_event_id'], 'guardian_follow_up_unique');
            $table->index(['student_id', 'status']);
        });

        Schema::create('guardian_follow_up_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_follow_up_case_id')->constrained()->cascadeOnDelete();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('author_name');
            $table->dateTime('contacted_at');
            $table->string('channel', 20);
            $table->text('result');
            $table->text('next_action')->nullable();
            $table->string('status_after', 20);
            $table->date('next_follow_up_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guardian_follow_up_entries');
        Schema::dropIfExists('guardian_follow_up_cases');
    }
};
