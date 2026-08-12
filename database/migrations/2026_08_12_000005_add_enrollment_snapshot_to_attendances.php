<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('student_enrollment_id')->nullable()->after('student_id')
                ->constrained('student_enrollments')->nullOnDelete();
            $table->index('student_enrollment_id');
        });

        // Use a portable select-then-update backfill. The previous joined
        // UPDATE form is accepted by MariaDB but not by SQLite, which is
        // used by the automated test suite and local smoke tests.
        $backfillRows = DB::table('attendances')
            ->select('attendances.id as attendance_id', 'student_enrollments.id as enrollment_id')
            ->join('kajian_events', 'kajian_events.id', '=', 'attendances.kajian_event_id')
            ->join('student_enrollments', function ($join) {
                $join->on('student_enrollments.student_id', '=', 'attendances.student_id')
                    ->on('student_enrollments.academic_year_id', '=', 'kajian_events.academic_year_id');
            })
            ->whereNull('attendances.student_enrollment_id')
            ->get();

        foreach ($backfillRows as $row) {
            DB::table('attendances')
                ->where('id', $row->attendance_id)
                ->whereNull('student_enrollment_id')
                ->update(['student_enrollment_id' => $row->enrollment_id]);
        }
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['student_enrollment_id']);
            $table->dropIndex(['student_enrollment_id']);
            $table->dropColumn('student_enrollment_id');
        });
    }
};
