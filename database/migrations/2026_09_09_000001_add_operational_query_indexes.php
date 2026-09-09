<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexes = [
            'attendances' => [
                'attendances_parent_validation_updated_idx' => ['parent_id', 'validation_status', 'updated_at'],
                'attendances_parent_created_idx' => ['parent_id', 'created_at'],
            ],
            'kajian_events' => [
                'kajian_events_status_date_time_idx' => ['status', 'date', 'time_start'],
            ],
            'students' => [
                'students_class_active_status_idx' => ['class_id', 'is_active', 'student_status'],
            ],
            'kajian_feedback' => [
                'kajian_feedback_created_at_idx' => ['created_at'],
            ],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $name => $columns) {
                if (! Schema::hasIndex($table, $name)) {
                    Schema::table($table, function (Blueprint $blueprint) use ($columns, $name): void {
                        $blueprint->index($columns, $name);
                    });
                }
            }
        }
    }

    public function down(): void
    {
        $indexes = [
            'attendances' => ['attendances_parent_validation_updated_idx', 'attendances_parent_created_idx'],
            'kajian_events' => ['kajian_events_status_date_time_idx'],
            'students' => ['students_class_active_status_idx'],
            'kajian_feedback' => ['kajian_feedback_created_at_idx'],
        ];

        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $name) {
                if (Schema::hasIndex($table, $name)) {
                    Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
                }
            }
        }
    }
};
