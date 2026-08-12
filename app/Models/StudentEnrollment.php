<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class StudentEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'academic_year_id',
        'class_id',
        'class_name',
        'class_level',
        'status',
        'started_at',
        'ended_at',
        'notes',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function isGraduated(): bool
    {
        return $this->status === 'graduated';
    }

    public function scopeForYear($query, int $academicYearId)
    {
        return $query->where('academic_year_id', $academicYearId);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['enrolled', 'retained']);
    }

    public static function forStudentAndYear(?int $studentId, ?int $academicYearId): ?self
    {
        if (! $studentId || ! $academicYearId) {
            return null;
        }

        return static::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->first();
    }

    public static function ensureForEvent(?Student $student, ?KajianEvent $event): ?self
    {
        if (! $student || ! $event?->academic_year_id) {
            return null;
        }

        $existing = static::forStudentAndYear($student->id, $event->academic_year_id);
        if ($existing) {
            return $existing;
        }

        $student->loadMissing('classRoom');

        return static::create([
            'student_id' => $student->id,
            'academic_year_id' => $event->academic_year_id,
            'class_id' => $student->class_id,
            'class_name' => $student->classRoom?->name,
            'class_level' => $student->classRoom?->level,
            'status' => $student->student_status === 'graduated' ? 'graduated' : 'enrolled',
            'started_at' => $event->academicYear?->start_date,
        ]);
    }
}
