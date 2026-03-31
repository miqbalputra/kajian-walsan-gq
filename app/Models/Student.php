<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'name',
        'class_id',
        'gender',
        'birth_date',
        'birth_place',
        'address',
        'guardian_name',
        'guardian_phone',
        'guardian_relationship',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get the class this student belongs to.
     */
    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    /**
     * Get all parents (father & mother) of this student.
     * One student can have up to 2 parent accounts.
     */
    public function parents(): BelongsToMany
    {
        return $this->belongsToMany(ParentModel::class, 'parent_student', 'student_id', 'parent_id')
            ->withPivot(['relationship', 'is_primary_contact'])
            ->withTimestamps();
    }

    /**
     * Get all attendances for this student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the father of this student.
     */
    public function father(): ?ParentModel
    {
        return $this->parents()->where('type', 'father')->first();
    }

    /**
     * Get the mother of this student.
     */
    public function mother(): ?ParentModel
    {
        return $this->parents()->where('type', 'mother')->first();
    }

    /**
     * Get siblings (other students with same parents).
     */
    public function getSiblingsAttribute()
    {
        $parentIds = $this->parents()->pluck('parents.id');

        if ($parentIds->isEmpty()) {
            return collect();
        }

        return Student::whereHas('parents', function ($query) use ($parentIds) {
            $query->whereIn('parents.id', $parentIds);
        })
            ->where('id', '!=', $this->id)
            ->get();
    }

    /**
     * Scope for active students.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by class.
     */
    public function scopeInClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Get full name with class.
     */
    public function getFullDisplayAttribute(): string
    {
        $class = $this->classRoom?->name ?? '-';
        return "{$this->name} ({$class})";
    }
}
