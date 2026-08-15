<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GoogleFormSubmission extends Model
{
    use HasFactory;

    public const STATUS_RECEIVED = 'received';
    public const STATUS_PROCESSED = 'processed';
    public const STATUS_DUPLICATE = 'duplicate';
    public const STATUS_UNRESOLVED = 'unresolved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'response_id',
        'form_id',
        'kajian_event_id',
        'attendance_id',
        'student_id',
        'parent_id',
        'event_date',
        'requested_status',
        'processing_status',
        'error_code',
        'error_message',
        'payload',
        'submitted_at',
        'received_at',
        'processed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'event_date' => 'date',
        'submitted_at' => 'datetime',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(KajianEvent::class, 'kajian_event_id');
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function isRetryable(): bool
    {
        return in_array($this->processing_status, [
            self::STATUS_RECEIVED,
            self::STATUS_UNRESOLVED,
            self::STATUS_FAILED,
        ], true);
    }
}
