<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->timestamp('teacher_archived_at')->nullable()->index();
        });

        Schema::create('teacher_archives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->unique()->constrained('parents')->restrictOnDelete();
            $table->string('teacher_name');
            $table->date('departed_on');
            $table->text('reason');
            $table->json('profile_snapshot');
            $table->json('attendance_snapshot');
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_archives');
        Schema::table('parents', fn (Blueprint $table) => $table->dropColumn('teacher_archived_at'));
    }
};
