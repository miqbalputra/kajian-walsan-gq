<?php

namespace App\Services;

use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ClassTeacherAssignmentService
{
    public function assign(User $teacher, ?int $classId, bool $replaceClassTeacher = false, bool $moveTeacher = true): void
    {
        DB::transaction(function () use ($teacher, $classId, $replaceClassTeacher, $moveTeacher) {
            $teacher = User::lockForUpdate()->findOrFail($teacher->id);
            if ($classId === null) {
                ClassRoom::where('teacher_id', $teacher->id)->update(['teacher_id' => null]);

                return;
            }

            if (! $teacher->is_active || ! $teacher->isTeachingStaff()) {
                throw ValidationException::withMessages(['teacherClassId' => 'Wali kelas harus merupakan guru aktif yang belum diarsipkan.']);
            }

            $class = ClassRoom::where('is_active', true)->lockForUpdate()->find($classId);
            if (! $class) {
                throw ValidationException::withMessages(['teacherClassId' => 'Pilih kelas yang masih aktif.']);
            }
            if ($class->teacher_id && (int) $class->teacher_id !== (int) $teacher->id && ! $replaceClassTeacher) {
                throw ValidationException::withMessages(['teacherClassId' => 'Kelas ini sudah memiliki wali kelas. Ubah penugasan melalui menu Kelas terlebih dahulu.']);
            }
            if (! $moveTeacher && ClassRoom::where('teacher_id', $teacher->id)->where('is_active', true)->whereKeyNot($class->id)->exists()) {
                throw ValidationException::withMessages(['teacherClassId' => 'Guru ini sudah menjadi wali kelas lain. Ubah kelasnya melalui Data Guru.']);
            }

            ClassRoom::where('teacher_id', $teacher->id)->whereKeyNot($class->id)->update(['teacher_id' => null]);
            $class->update(['teacher_id' => $teacher->id]);
        });
    }

    public function assignToClass(ClassRoom $class, ?User $teacher): void
    {
        if (! $teacher) {
            $class->update(['teacher_id' => null]);

            return;
        }

        $this->assign($teacher, $class->id, replaceClassTeacher: true, moveTeacher: false);
    }
}
