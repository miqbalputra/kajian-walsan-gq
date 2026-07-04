<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class KajianEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'title',
        'description',
        'speaker',
        'location',
        'date',
        'time_start',
        'time_end',
        'status',
        'category',
        'policy_overrides',
        'qr_code_image',
        'attendance_count',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'time_start' => 'datetime:H:i',
        'time_end' => 'datetime:H:i',
        'attendance_count' => 'integer',
        'policy_overrides' => 'array',
    ];

    /**
     * Get the academic year for this event.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Get the user who created this event.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get classes targeted by this event.
     * Empty pivot means all classes for backward compatibility.
     */
    public function targetClasses(): BelongsToMany
    {
        return $this->belongsToMany(ClassRoom::class, 'kajian_event_class', 'kajian_event_id', 'class_id')
            ->withTimestamps();
    }

    /**
     * Get all attendance records for this event.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'kajian_event_id');
    }

    /**
     * Get all feedbacks for this event.
     */
    public function feedbacks(): HasMany
    {
        return $this->hasMany(KajianFeedback::class, 'kajian_event_id');
    }

    /**
     * Check if the event is currently open for attendance.
     */
    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    /**
     * Get the event currently accepting attendance/uploads.
     */
    public static function activeForAttendance(): ?self
    {
        return static::openForAttendance()->first();
    }

    /**
     * Check if the event is today.
     */
    public function isToday(): bool
    {
        return $this->date->isToday();
    }

    /**
     * Check if the event is in the past.
     */
    public function isPast(): bool
    {
        return $this->date->isPast() && ! $this->isToday();
    }

    /**
     * Check if the event is upcoming.
     */
    public function isUpcoming(): bool
    {
        return $this->date->isFuture();
    }

    /**
     * Get formatted date.
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->translatedFormat('l, d F Y');
    }

    /**
     * Get formatted time range.
     */
    public function getTimeRangeAttribute(): string
    {
        $start = Carbon::parse($this->time_start)->format('H:i');
        $end = Carbon::parse($this->time_end)->format('H:i');

        return "{$start} - {$end} WIB";
    }

    /**
     * Open the event for attendance.
     */
    public function open(): bool
    {
        return $this->update(['status' => 'open']);
    }

    /**
     * Close the event.
     */
    public function close(): bool
    {
        return $this->update(['status' => 'closed']);
    }

    /**
     * Update the attendance count cache.
     */
    public function updateAttendanceCount(): int
    {
        $count = $this->attendances()
            ->whereIn('status', ['hadir_fisik', 'hadir_online'])
            ->where('validation_status', 'approved')
            ->count();

        $this->update(['attendance_count' => $count]);

        return $count;
    }

    /**
     * Scope for open events.
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for events that admin has opened for attendance/uploads.
     */
    public function scopeOpenForAttendance($query)
    {
        return $query->where('status', 'open')
            ->orderByDesc('date')
            ->orderByDesc('time_start')
            ->orderByDesc('id');
    }

    /**
     * Scope for upcoming events.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
            ->orderBy('date')
            ->orderBy('time_start');
    }

    /**
     * Scope for current academic year.
     */
    public function scopeCurrentYear($query)
    {
        $activeYear = AcademicYear::active();
        if ($activeYear) {
            return $query->where('academic_year_id', $activeYear->id);
        }

        return $query;
    }

    /**
     * Event target class IDs. Empty = all classes.
     */
    public function targetClassIds(): Collection
    {
        if ($this->relationLoaded('targetClasses')) {
            return $this->targetClasses->pluck('id')->values();
        }

        return $this->targetClasses()->pluck('classes.id')->values();
    }

    public function targetsAllClasses(): bool
    {
        return $this->targetClassIds()->isEmpty();
    }

    /**
     * Check whether a parent/guardian is counted for this event.
     * Pure teachers are not scoped by wali class targets.
     */
    public function targetsParent(ParentModel $parent): bool
    {
        if ($parent->isPureTeacher() || $this->targetsAllClasses()) {
            return true;
        }

        return $this->targetedStudentsForParent($parent)->isNotEmpty();
    }

    /**
     * Students from this parent that match event target classes.
     */
    public function targetedStudentsForParent(ParentModel $parent): Collection
    {
        $students = $parent->relationLoaded('students')
            ? $parent->students
            : $parent->students()->with('classRoom')->get();

        if ($this->targetsAllClasses()) {
            return $students->values();
        }

        $targetClassIds = $this->targetClassIds()->map(fn ($id) => (int) $id)->all();

        return $students
            ->filter(fn ($student) => in_array((int) $student->class_id, $targetClassIds, true))
            ->values();
    }

    public function getTargetClassesDisplayAttribute(): string
    {
        if ($this->targetsAllClasses()) {
            return 'Semua kelas';
        }

        return $this->relationLoaded('targetClasses')
            ? $this->targetClasses->pluck('name')->join(', ')
            : $this->targetClasses()->pluck('name')->join(', ');
    }

    /**
     * Get the category display name.
     */
    public function getCategoryDisplayAttribute(): string
    {
        return config('event_categories.'.$this->category.'.label', ucfirst($this->category ?? 'kajian'));
    }

    /**
     * Get policy config for this event's category.
     */
    public function getPolicyAttribute(): array
    {
        $category = $this->category ?? 'kajian';
        $defaults = config('event_categories.'.$category, config('event_categories.kajian'));
        $overrides = $this->policy_overrides ?? [];

        return array_merge($defaults, $overrides);
    }

    /**
     * Scope to filter by category.
     */
    public function scopeInCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
