<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRosterSnapshotStudent extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_roster_snapshot_id',
        'parent_id',
        'student_id',
        'student_enrollment_id',
        'class_id',
        'student_name',
        'student_nis',
        'class_name',
        'parent_name',
        'parent_type',
    ];

    public function rosterSnapshot(): BelongsTo
    {
        return $this->belongsTo(AttendanceRosterSnapshot::class, 'attendance_roster_snapshot_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function studentEnrollment(): BelongsTo
    {
        return $this->belongsTo(StudentEnrollment::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
