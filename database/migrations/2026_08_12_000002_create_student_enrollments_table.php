<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->restrictOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('class_name', 100)->nullable();
            $table->string('class_level', 20)->nullable();
            $table->string('status', 20)->default('enrolled');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id']);
            $table->index(['academic_year_id', 'class_id']);
            $table->index(['student_id', 'status']);
        });

        // Snapshot the current class before any promotion takes place.
        $activeYear = DB::table('academic_years')->where('is_active', true)->first();
        if (! $activeYear) {
            return;
        }

        DB::table('students')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->select([
                'students.id as student_id',
                'students.class_id',
                'students.student_status',
                'students.is_active',
                'classes.name as class_name',
                'classes.level as class_level',
            ])
            ->orderBy('students.id')
            ->chunkById(500, function ($students) use ($activeYear) {
                $rows = $students->map(fn ($student) => [
                    'student_id' => $student->student_id,
                    'academic_year_id' => $activeYear->id,
                    'class_id' => $student->class_id,
                    'class_name' => $student->class_name,
                    'class_level' => $student->class_level,
                    'status' => $student->student_status ?? ($student->is_active ? 'enrolled' : 'withdrawn'),
                    'started_at' => $activeYear->start_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all();

                if ($rows) {
                    DB::table('student_enrollments')->insertOrIgnore($rows);
                }
            }, 'students.id', 'student_id');
    }

    public function down(): void
    {
        Schema::dropIfExists('student_enrollments');
    }
};
