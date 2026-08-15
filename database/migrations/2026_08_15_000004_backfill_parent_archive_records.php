<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('parents')
            ->join('users', 'users.id', '=', 'parents.user_id')
            ->whereIn('parents.type', ['father', 'mother'])
            ->where(function ($query) {
                $query->whereNull('parents.is_teacher')->orWhere('parents.is_teacher', false);
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('parent_student')
                    ->join('students', 'students.id', '=', 'parent_student.student_id')
                    ->whereColumn('parent_student.parent_id', 'parents.id')
                    ->where('students.is_active', true)
                    ->where(function ($statusQuery) {
                        $statusQuery->whereNull('students.student_status')
                            ->orWhere('students.student_status', 'active');
                    });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('parent_archive_records')
                    ->whereColumn('parent_archive_records.parent_id', 'parents.id')
                    ->whereNull('parent_archive_records.restored_at');
            })
            ->select([
                'parents.id as parent_id',
                'users.id as user_id',
                'users.is_active as user_is_active',
            ])
            ->orderBy('parents.id')
            ->chunkById(500, function ($parents) {
                $now = now();
                foreach ($parents as $parent) {
                    $loginDisabled = (bool) $parent->user_is_active;

                    DB::table('parent_archive_records')->insert([
                        'parent_id' => $parent->parent_id,
                        'reason' => 'legacy_no_active_children',
                        'login_disabled' => $loginDisabled,
                        'archived_at' => $now,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);

                    if ($loginDisabled) {
                        DB::table('users')->where('id', $parent->user_id)->update([
                            'is_active' => false,
                            'updated_at' => $now,
                        ]);
                    }
                }
            }, 'parents.id', 'parent_id');
    }

    public function down(): void
    {
        // Archive records are historical data. Rolling back the schema must
        // not reactivate accounts or attempt to infer prior account state.
    }
};
