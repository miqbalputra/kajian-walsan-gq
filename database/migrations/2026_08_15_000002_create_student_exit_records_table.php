<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_exit_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->restrictOnDelete();
            $table->string('exit_type', 20)->index();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('from_class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('from_class_name', 100)->nullable();
            $table->date('effective_date')->nullable();
            $table->string('reason', 500)->nullable();
            $table->string('destination', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('evidence_path')->nullable();
            $table->boolean('is_legacy')->default(false)->index();
            $table->foreignId('archived_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('archived_at')->nullable();
            $table->timestamp('restored_at')->nullable();
            $table->foreignId('restored_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('restored_academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('restored_class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->text('restore_notes')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'restored_at']);
            $table->index(['exit_type', 'effective_date']);
        });

        // Preserve existing inactive/withdrawn records in a searchable archive
        // without guessing their historical reason or date.
        $legacyAcademicYear = DB::table('academic_years')->where('is_active', true)->value('id');
        DB::table('students')
            ->leftJoin('classes', 'classes.id', '=', 'students.class_id')
            ->where(function ($query) {
                $query->where('students.student_status', 'withdrawn')
                    ->orWhere(function ($query) {
                        $query->where('students.is_active', false)
                            ->where(function ($statusQuery) {
                                $statusQuery->whereNull('students.student_status')
                                    ->orWhere('students.student_status', '!=', 'graduated');
                            });
                    });
            })
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('student_exit_records')
                    ->whereColumn('student_exit_records.student_id', 'students.id');
            })
            ->select([
                'students.id as student_id',
                'students.class_id as from_class_id',
                'classes.name as from_class_name',
            ])
            ->orderBy('students.id')
            ->chunkById(500, function ($students) use ($legacyAcademicYear) {
                $now = now();
                $rows = $students->map(fn ($student) => [
                    'student_id' => $student->student_id,
                    'exit_type' => 'withdrawn',
                    'academic_year_id' => $legacyAcademicYear,
                    'from_class_id' => $student->from_class_id,
                    'from_class_name' => $student->from_class_name,
                    'reason' => 'Histori sebelum fitur arsip',
                    'notes' => 'Data lama ditandai sebagai arsip legacy; tanggal dan alasan asli tidak tersedia.',
                    'is_legacy' => true,
                    'archived_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ])->all();

                if ($rows) {
                    DB::table('student_exit_records')->insert($rows);
                }
            }, 'students.id', 'student_id');
    }

    public function down(): void
    {
        Schema::dropIfExists('student_exit_records');
    }
};
