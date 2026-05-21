<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('parents', 'is_teacher')) {
            Schema::table('parents', function (Blueprint $table) {
                $table->boolean('is_teacher')->default(false)->after('type');
                $table->index('is_teacher');
            });
        }

        DB::table('parents')
            ->where('type', 'teacher')
            ->update(['is_teacher' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('parents', 'is_teacher')) {
            Schema::table('parents', function (Blueprint $table) {
                $table->dropIndex(['is_teacher']);
                $table->dropColumn('is_teacher');
            });
        }
    }
};
