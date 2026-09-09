<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table): void {
            $table->uuid('data_induk_id')->nullable()->unique()->after('id');
        });
        Schema::table('classes', function (Blueprint $table): void {
            $table->uuid('data_induk_id')->nullable()->unique()->after('id');
        });
        Schema::create('data_induk_sync_states', function (Blueprint $table): void {
            $table->id();
            $table->string('scope', 100)->unique();
            $table->string('cursor', 50)->nullable();
            $table->timestamp('last_success_at')->nullable();
            $table->text('last_error')->nullable();
            $table->timestamps();
        });
        Schema::create('data_induk_sync_conflicts', function (Blueprint $table): void {
            $table->id();
            $table->string('entity_type', 50);
            $table->uuid('master_id')->nullable();
            $table->unsignedBigInteger('local_id')->nullable();
            $table->string('reason', 255);
            $table->json('payload')->nullable();
            $table->string('status', 20)->default('open');
            $table->timestamps();
            $table->index(['status', 'entity_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_induk_sync_conflicts');
        Schema::dropIfExists('data_induk_sync_states');
        Schema::table('classes', fn (Blueprint $table) => $table->dropUnique(['data_induk_id'])->dropColumn('data_induk_id'));
        Schema::table('parents', fn (Blueprint $table) => $table->dropUnique(['data_induk_id'])->dropColumn('data_induk_id'));
    }
};
