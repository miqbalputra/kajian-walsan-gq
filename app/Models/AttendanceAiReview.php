<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceAiReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'proof_hash',
        'provider',
        'model',
        'status',
        'decision',
        'reason_code',
        'confidence',
        'text_chars',
        'text_boxes',
        'language',
        'document_signal',
        'reason',
        'raw_text_preview',
        'payload',
        'error',
        'attempt',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }
}
