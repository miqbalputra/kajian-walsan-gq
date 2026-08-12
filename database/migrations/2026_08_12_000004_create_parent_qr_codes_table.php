<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parent_qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->constrained('parents')->restrictOnDelete();
            $table->string('code', 100)->unique();
            $table->string('kind', 20)->default('child_alias');
            $table->foreignId('source_student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index(['parent_id', 'is_active']);
            $table->index(['source_student_id', 'kind']);
        });

        $parents = DB::table('parents')->select(['id', 'type', 'qr_code_string'])->orderBy('id')->get();

        foreach ($parents as $parent) {
            if (! empty($parent->qr_code_string)) {
                DB::table('parent_qr_codes')->insertOrIgnore([
                    'parent_id' => $parent->id,
                    'code' => $parent->qr_code_string,
                    'kind' => 'canonical',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $prefix = match ($parent->type) {
                'father' => 'A',
                'mother' => 'B',
                'teacher' => 'T',
                default => 'X',
            };

            $students = DB::table('parent_student')
                ->join('students', 'students.id', '=', 'parent_student.student_id')
                ->where('parent_student.parent_id', $parent->id)
                ->select(['students.id', 'students.nis'])
                ->get();

            foreach ($students as $student) {
                $base = Str::limit($prefix.$student->nis, 90, '');
                $candidate = $base;
                $counter = 1;

                while (DB::table('parent_qr_codes')->where('code', $candidate)->where('parent_id', '!=', $parent->id)->exists()) {
                    $suffix = '-'.$parent->id.'-'.$counter++;
                    $candidate = Str::limit($base, 100 - strlen($suffix), '').$suffix;
                }

                DB::table('parent_qr_codes')->insertOrIgnore([
                    'parent_id' => $parent->id,
                    'code' => $candidate,
                    'kind' => $candidate === $parent->qr_code_string ? 'canonical' : 'child_alias',
                    'source_student_id' => $student->id,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_qr_codes');
    }
};
