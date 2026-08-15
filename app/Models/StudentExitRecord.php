<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentExitRecord extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exit_type',
        'academic_year_id',
        'from_class_id',
        'from_class_name',
        'effective_date',
        'reason',
        'destination',
        'notes',
        'evidence_path',
        'is_legacy',
        'archived_by',
        'archived_at',
        'restored_at',
        'restored_by',
        'restored_academic_year_id',
        'restored_class_id',
        'restore_notes',
    ];

    protected $casts = [
        'effective_date' => 'date',
        'is_legacy' => 'boolean',
        'archived_at' => 'datetime',
        'restored_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'from_class_id');
    }

    public function restoredAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'restored_academic_year_id');
    }

    public function restoredClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'restored_class_id');
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function restoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restored_by');
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('restored_at');
    }
}
