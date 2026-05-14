<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->string('ai_validation_status')->nullable()->after('rejection_reason');
            $table->unsignedTinyInteger('ai_validation_confidence')->nullable()->after('ai_validation_status');
            $table->text('ai_validation_reason')->nullable()->after('ai_validation_confidence');
            $table->string('ai_validation_model')->nullable()->after('ai_validation_reason');
            $table->json('ai_validation_payload')->nullable()->after('ai_validation_model');
            $table->timestamp('ai_validated_at')->nullable()->after('ai_validation_payload');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'ai_validation_status',
                'ai_validation_confidence',
                'ai_validation_reason',
                'ai_validation_model',
                'ai_validation_payload',
                'ai_validated_at',
            ]);
        });
    }
};
