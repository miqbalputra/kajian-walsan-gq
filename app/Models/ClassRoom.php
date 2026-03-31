<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'level',
        'capacity',
        'is_active',
        'teacher_id',
    ];

    /**
     * Get the teacher assigned to this class.
     */
    public function teacher(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get all students in this class.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    /**
     * Get active students count.
     */
    public function getActiveStudentsCountAttribute(): int
    {
        return $this->students()->where('is_active', true)->count();
    }

    /**
     * Scope for active classes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to order by level then name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level')->orderBy('name');
    }
}
