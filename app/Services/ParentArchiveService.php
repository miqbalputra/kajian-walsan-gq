<?php

namespace App\Services;

use App\Models\ParentArchiveRecord;
use App\Models\ParentModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ParentArchiveService
{
    /**
     * Reconcile a parent's archive state from the current child statuses.
     * The parent profile, QR codes, relations, and attendance are never deleted.
     */
    public function syncForParent(ParentModel $parent, ?User $actor = null): ?ParentArchiveRecord
    {
        return DB::transaction(function () use ($parent, $actor) {
            $parent = ParentModel::with('user')->lockForUpdate()->findOrFail($parent->id);
            $openRecord = $parent->archiveRecords()->open()->latest('id')->first();
            $hasActiveChild = $parent->students()->active()->exists();

            // Teacher access is independent from guardian-child activity.
            $shouldArchive = $parent->isGuardian() && ! $parent->isTeacher() && ! $hasActiveChild;

            if (! $shouldArchive) {
                if ($openRecord) {
                    $openRecord->update([
                        'restored_at' => now(),
                        'restored_by' => $actor?->id,
                        'restore_notes' => $parent->isTeacher()
                            ? 'Akun tetap aktif karena wali juga memiliki akses guru.'
                            : 'Akun aktif kembali karena memiliki anak aktif.',
                    ]);

                    if ($openRecord->login_disabled) {
                        $parent->user?->update(['is_active' => true]);
                    }
                }

                return null;
            }

            if ($openRecord) {
                return $openRecord;
            }

            $loginDisabled = (bool) ($parent->user?->is_active);
            $record = $parent->archiveRecords()->create([
                'reason' => 'no_active_children',
                'login_disabled' => $loginDisabled,
                'archived_by' => $actor?->id,
                'archived_at' => now(),
            ]);

            if ($loginDisabled) {
                $parent->user?->update(['is_active' => false]);
            }

            return $record;
        });
    }

    public function syncForStudentParents(int $studentId, ?User $actor = null): void
    {
        ParentModel::whereHas('students', fn ($query) => $query->where('students.id', $studentId))
            ->get()
            ->each(fn (ParentModel $parent) => $this->syncForParent($parent, $actor));
    }

    public function restoreLogin(ParentModel $parent, ?User $actor = null, ?string $notes = null): void
    {
        DB::transaction(function () use ($parent, $actor, $notes) {
            $parent = ParentModel::with('user')->lockForUpdate()->findOrFail($parent->id);
            $openRecord = $parent->archiveRecords()->open()->latest('id')->first();

            if ($openRecord) {
                $openRecord->update([
                    'restored_at' => now(),
                    'restored_by' => $actor?->id,
                    'restore_notes' => $notes ?: 'Diaktifkan kembali oleh admin.',
                ]);

                if ($openRecord->login_disabled) {
                    $parent->user?->update(['is_active' => true]);
                }
            }
        });
    }
}
