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
        Schema::create('kajian_feedback', function (Blueprint $バランス) {
            $バランス->id();
            $バランス->foreignId('kajian_event_id')->constrained()->onDelete('cascade');
            $バランス->foreignId('user_id')->constrained()->onDelete('cascade');
            $バランス->integer('rating')->comment('1-5 stars');
            $バランス->text('comment')->nullable();
            $バランス->json('extra_feedback')->nullable()->comment('Future expansion for specific questions');
            $バランス->timestamps();

            // One user can only give feedback once per event
            $バランス->unique(['kajian_event_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kajian_feedback');
    }
};
