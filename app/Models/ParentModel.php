<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ParentModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'parents';

    protected $fillable = [
		'data_induk_id',
        'user_id',
        'type',
        'is_teacher',
        'is_single_parent',
        'qr_code_string',
        'nik',
        'occupation',
        'address',
    ];

    protected $casts = [
        'is_teacher' => 'boolean',
        'teacher_archived_at' => 'datetime',
        'is_single_parent' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate QR code string on creation
        static::creating(function ($parent) {
            if (empty($parent->qr_code_string)) {
                $parent->qr_code_string = static::generateForParent($parent);
            }
        });

        static::updating(function (ParentModel $parent) {
            $stored = static::select(['id', 'type', 'teacher_archived_at'])->find($parent->id);
            if ($stored?->isTeacherArchived()
                && ($stored->isPureTeacher()
                    || $parent->isDirty(['type', 'is_teacher', 'teacher_archived_at']))) {
                throw ValidationException::withMessages(['archive' => 'Data guru arsip hanya dapat dibaca.']);
            }
        });
        static::deleting(function (ParentModel $parent) {
            if (static::whereKey($parent->id)->whereNotNull('teacher_archived_at')->exists()) {
                throw ValidationException::withMessages(['archive' => 'Data guru arsip tidak dapat dihapus.']);
            }
        });

    }

    /**
     * Generate the initial parent-owned QR code.
     * Child NIS values are aliases managed by ParentQrCodeService.
     */
    public static function generateForParent($parent): string
    {
        // Jika dipanggil saat creating, relasi mungkin belum ada.
        // Kita butuh murid pertama.
        $student = $parent->students()->first();

        if ($student && ! empty($student->nis)) {
            $prefix = match ($parent->type) {
                'father' => 'A',
                'mother' => 'B',
                'teacher' => 'T',
                default => 'X',
            };

            return static::uniqueQrCode($prefix.$student->nis, $parent->id ?? null);
        }

        // Parent-owned QR for new records. Existing legacy QR values are never
        // replaced; aliases are managed separately by ParentQrCodeService.
        do {
            $code = 'P-'.strtoupper(Str::random(12));
        } while (static::where('qr_code_string', $code)->exists());

        return $code;
    }

    private static function uniqueQrCode(string $baseCode, ?int $ignoreParentId = null): string
    {
        $baseCode = Str::limit($baseCode, 90, '');
        $candidate = $baseCode;
        $counter = 1;

        while (static::where('qr_code_string', $candidate)
            ->when($ignoreParentId, fn ($query) => $query->where('id', '!=', $ignoreParentId))
            ->exists()) {
            $suffix = '-'.($ignoreParentId ? $ignoreParentId.'-'.$counter : $counter);
            $candidate = Str::limit($baseCode, 100 - strlen($suffix), '').$suffix;
            $counter++;
        }

        return $candidate;
    }

    /**
     * Get the user account for this parent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all children (students) of this parent.
     * Supports siblings - one parent can have multiple children.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot(['relationship', 'is_primary_contact'])
            ->withTimestamps();
    }

    /**
     * Alias for students - get all children.
     */
    public function children(): BelongsToMany
    {
        return $this->students();
    }

    /**
     * Get all attendance records for this parent.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'parent_id');
    }

    public function qrCodes(): HasMany
    {
        return $this->hasMany(ParentQrCode::class, 'parent_id');
    }

    public function archiveRecords(): HasMany
    {
        return $this->hasMany(ParentArchiveRecord::class, 'parent_id');
    }

    public function hasActiveChildren(): bool
    {
        return $this->students()->active()->exists();
    }

    public function isArchivedGuardian(): bool
    {
        return $this->isGuardian() && ! $this->hasActiveChildren();
    }

    /**
     * Check if this parent is the father.
     */
    public function isFather(): bool
    {
        return $this->type === 'father';
    }

    /**
     * Check if this parent is the mother.
     */
    public function isMother(): bool
    {
        return $this->type === 'mother';
    }

    /**
     * Check if this parent is a teacher.
     */
    public function isTeacher(): bool
    {
        return ! $this->isTeacherArchived() && ($this->type === 'teacher' || (bool) $this->is_teacher);
    }

    public function isTeacherArchived(): bool
    {
        return $this->teacher_archived_at !== null;
    }

    public function scopeActiveTeachers($query)
    {
        return $query->whereNull('teacher_archived_at')->where(function ($query) {
            $query->where('type', 'teacher')->orWhere('is_teacher', true);
        });
    }

    public function scopeAvailableParticipants($query)
    {
        return $query->where(function ($query) {
            $query->whereIn('type', ['father', 'mother'])->orWhereNull('teacher_archived_at');
        });
    }

    /**
     * Check if this parent is a wali santri record.
     */
    public function isGuardian(): bool
    {
        return in_array($this->type, ['father', 'mother'], true);
    }

    /**
     * Check if this wali santri is also a teacher.
     */
    public function isWaliTeacher(): bool
    {
        return $this->isGuardian() && $this->isTeacher();
    }

    /**
     * Check if this is a teacher-only profile, not a wali santri.
     */
    public function isPureTeacher(): bool
    {
        return $this->type === 'teacher';
    }

    /**
     * Get the type display name.
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'father' => 'Ayah',
            'mother' => 'Ibu',
            'teacher' => 'Guru',
            default => 'Unknown',
        };
    }

    /**
     * Get the spouse (other parent of the same children).
     */
    public function getSpouseAttribute(): ?self
    {
        $childIds = $this->students()->pluck('students.id');

        if ($childIds->isEmpty()) {
            return null;
        }

        return static::whereHas('students', function ($query) use ($childIds) {
            $query->whereIn('students.id', $childIds);
        })
            ->where('id', '!=', $this->id)
            ->where('type', '!=', $this->type)
            ->first();
    }

    /**
     * Scope to find by QR code.
     */
    public function scopeByQrCode($query, string $qrCode)
    {
        return $query->where(function ($query) use ($qrCode) {
            $query->where('qr_code_string', $qrCode)
                ->orWhereHas('qrCodes', function ($qrQuery) use ($qrCode) {
                    $qrQuery->active()->where('code', $qrCode);
                });
        });
    }

    /**
     * Scope guardians/parents that are counted for a class-targeted event.
     * Empty event targets mean all classes.
     */
    public function scopeTargetedByEvent($query, KajianEvent $event)
    {
        if ($event->targetsAllClasses()) {
            return $query->whereHas('students', function ($query) {
                $query->where('students.is_active', true)
                    ->where(function ($statusQuery) {
                        $statusQuery->whereNull('students.student_status')
                            ->orWhere('students.student_status', 'active');
                    });
            });
        }

        $targetClassIds = $event->targetClassIds()->all();

        return $query->whereHas('students', function ($query) use ($targetClassIds) {
            $query->whereIn('students.class_id', $targetClassIds)
                ->where('students.is_active', true)
                ->where(function ($statusQuery) {
                    $statusQuery->whereNull('students.student_status')
                        ->orWhere('students.student_status', 'active');
                });
        });
    }

    /**
     * Scope to actual wali santri profiles. Teacher-only profiles are not
     * counted in guardian attendance metrics.
     */
    public function scopeGuardians($query)
    {
        return $query->whereIn('type', ['father', 'mother']);
    }

    public function scopeWithActiveChild($query)
    {
        return $query->whereHas('students', fn ($studentQuery) => $studentQuery->active());
    }

    public function scopeArchivedGuardians($query)
    {
        return $query->guardians()->whereDoesntHave('students', fn ($studentQuery) => $studentQuery->active());
    }

    /**
     * Find parent by QR code string.
     */
    public static function findByQrCode(string $qrCode): ?self
    {
        return app(\App\Services\ParentQrCodeService::class)->resolve($qrCode);
    }

    /**
     * Legacy method name retained for compatibility. It now only syncs
     * aliases and never replaces the parent-owned canonical QR.
     */
    public function regenerateQrCode(): bool
    {
        // QR is parent-owned now. Keep the canonical code and only reconcile
        // aliases for linked children.
        return $this->syncQrCode();
    }

    /**
     * Reconcile aliases for linked students without changing the canonical
     * parent QR code.
     */
    public function syncQrCode(): bool
    {
        $before = $this->qr_code_string;
        app(\App\Services\ParentQrCodeService::class)->syncForParent($this);

        return $before !== $this->qr_code_string;
    }

    /**
     * Check if QR code is old and might need regeneration.
     * Returns true if QR code hasn't been updated in specified months.
     */
    public function isQrCodeOld(int $months = 12): bool
    {
        return $this->updated_at->diffInMonths(now()) >= $months;
    }
}
