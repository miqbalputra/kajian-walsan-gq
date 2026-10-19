<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\TeacherArchive;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TeacherArchiveService
{
    public function archive(ParentModel $teacher, string $departedOn, string $reason, User $actor): TeacherArchive
    {
        abort_unless($actor->isAdmin(), 403);
        $data = Validator::make(['departed_on' => $departedOn, 'reason' => trim($reason)], [
            'departed_on' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
            'reason' => ['required', 'string', 'max:2000'],
        ])->validate();

        return DB::transaction(function () use ($teacher, $data, $actor) {
            $teacher = ParentModel::with(['user', 'students.classRoom'])->lockForUpdate()->findOrFail($teacher->id);
            if ($teacher->isTeacherArchived()) {
                return TeacherArchive::where('parent_id', $teacher->id)->firstOrFail();
            }
            if (! $teacher->isTeacher() || $teacher->user_id === $actor->id) {
                throw ValidationException::withMessages(['archive' => 'Pilih guru aktif selain akun Anda sendiri.']);
            }

            $history = Attendance::withTrashed()
                ->with(['kajianEvent' => fn ($query) => $query->withTrashed(), 'validator'])
                ->where('parent_id', $teacher->id)
                ->orderByDesc('id')->get()
                ->map(fn (Attendance $attendance) => [
                    'id' => $attendance->id,
                    'event_title' => $attendance->kajianEvent?->title ?? 'Kajian dihapus',
                    'event_date' => $attendance->kajianEvent?->date?->toDateString(),
                    'status' => $attendance->status,
                    'status_label' => $attendance->status_display,
                    'validation_status' => $attendance->validation_status,
                    'validated_by' => $attendance->validator?->name,
                    'proof_url' => $attendance->proof_file ? CloudinaryService::getDisplayUrl($attendance->proof_file) : null,
                    'notes' => $attendance->notes,
                    'deleted_at' => $attendance->deleted_at?->toISOString(),
                ])->all();

            $archive = TeacherArchive::create([
                'parent_id' => $teacher->id,
                'teacher_name' => $teacher->user?->name ?? '-',
                'departed_on' => $data['departed_on'],
                'reason' => $data['reason'],
                'archived_by' => $actor->id,
                'profile_snapshot' => [
                    'username' => $teacher->user?->username,
                    'email' => $teacher->user?->email,
                    'phone' => $teacher->user?->phone,
                    'type' => $teacher->type,
                    'nik' => $teacher->nik,
                    'occupation' => $teacher->occupation,
                    'address' => $teacher->address,
                    'children' => $teacher->students->map(fn ($student) => [
                        'name' => $student->name, 'class' => $student->classRoom?->name,
                    ])->all(),
                ],
                'attendance_snapshot' => $history,
            ]);

            // Retain a guardian's access independently of their former job.
            $keepGuardianAccess = $teacher->isGuardian() && $teacher->hasActiveChildren();
            $user = User::lockForUpdate()->find($teacher->user_id);
            if ($user) {
                $changes = ['remember_token' => null];
                if (! $keepGuardianAccess) {
                    $changes['is_active'] = false;
                } elseif ($user->isGuru() || $user->isWaliKelas()) {
                    $changes['role_id'] = Role::where('name', 'wali_santri')->value('id');
                }
                $user->update($changes);
                if (! $keepGuardianAccess) {
                    $user->loginAliases()->update(['is_active' => false]);
                }
                ClassRoom::where('teacher_id', $user->id)->update(['teacher_id' => null]);
            }

            $teacher->forceFill(['teacher_archived_at' => now()])->save();

            return $archive;
        });
    }
}
