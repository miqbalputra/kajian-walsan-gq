<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_login_aliases')) {
            return;
        }

        Schema::create('user_login_aliases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('source_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('kind', 30)->default('child_alias');
            $table->boolean('is_active')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->index(['source_student_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_login_aliases');
    }
};
