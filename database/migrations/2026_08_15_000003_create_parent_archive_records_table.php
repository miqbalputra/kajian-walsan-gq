<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parent_archive_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->restrictOnDelete();
            $table->string('reason', 100)->default('no_active_children');
            $table->boolean('login_disabled')->default(false);
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('restored_at')->nullable();
            $table->foreignId('restored_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('restore_notes')->nullable();
            $table->timestamps();

            $table->index(['parent_id', 'restored_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_archive_records');
    }
};
