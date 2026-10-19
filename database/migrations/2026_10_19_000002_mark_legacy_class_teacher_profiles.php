<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('parents')->whereNull('teacher_archived_at')
            ->whereIn('user_id', DB::table('users')->whereIn('role_id', DB::table('roles')->where('name', 'wali_kelas')->select('id'))->select('id'))
            ->update(['is_teacher' => true]);
    }

    public function down(): void
    {
        // Teacher identities may have been updated since this backfill.
    }
};
