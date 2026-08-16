<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRosterSnapshot extends Model
{
    use HasFactory;

    protected $fillable = [
        'kajian_event_id',
        'parent_id',
        'student_id',
        'student_enrollment_id',
        'class_id',
        'student_name',
        'class_name',
    ];

    public function kajianEvent(): BelongsTo
    {
        return $this->belongsTo(KajianEvent::class);
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
}
