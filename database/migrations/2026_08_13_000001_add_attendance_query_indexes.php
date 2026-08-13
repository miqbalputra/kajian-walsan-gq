<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasIndex('attendances', 'attendances_event_validation_status_idx')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->index(
                    ['kajian_event_id', 'validation_status', 'status'],
                    'attendances_event_validation_status_idx'
                );
            });
        }

        if (! Schema::hasIndex('attendances', 'attendances_method_validation_created_idx')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->index(
                    ['method', 'validation_status', 'created_at'],
                    'attendances_method_validation_created_idx'
                );
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasIndex('attendances', 'attendances_event_validation_status_idx')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropIndex('attendances_event_validation_status_idx');
            });
        }

        if (Schema::hasIndex('attendances', 'attendances_method_validation_created_idx')) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dropIndex('attendances_method_validation_created_idx');
            });
        }
    }
};
