<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

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
        'qr_code_image',
        'attendance_count',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'time_start' => 'datetime:H:i',
        'time_end' => 'datetime:H:i',
        'attendance_count' => 'integer',
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
        return $this->date->isPast() && !$this->isToday();
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
}
