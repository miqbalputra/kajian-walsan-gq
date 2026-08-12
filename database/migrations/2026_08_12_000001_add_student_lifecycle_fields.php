<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('student_status', 20)->default('active')->after('is_active')->index();
            $table->timestamp('graduated_at')->nullable()->after('student_status');
            $table->foreignId('graduation_academic_year_id')->nullable()->after('graduated_at')
                ->constrained('academic_years')->nullOnDelete();
        });

        DB::table('students')->where('is_active', false)->update(['student_status' => 'withdrawn']);
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['graduation_academic_year_id']);
            $table->dropColumn(['student_status', 'graduated_at', 'graduation_academic_year_id']);
        });
    }
};
