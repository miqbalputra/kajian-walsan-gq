<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TelegramReviewRequest extends Model
{
    use HasFactory;

    public const DELIVERY_QUEUED = 'queued';
    public const DELIVERY_SENT = 'sent';
    public const DELIVERY_FAILED = 'failed';
    public const DELIVERY_EXPIRED = 'expired';
    public const DELIVERY_RESOLVED = 'resolved';

    public const DECISION_APPROVED = 'approved';
    public const DECISION_REJECTED = 'rejected';

    protected $fillable = [
        'token',
        'attendance_id',
        'proof_file',
        'chat_id',
        'message_id',
        'delivery_status',
        'delivery_attempts',
        'last_error',
        'decision',
        'rejection_reason',
        'reviewed_by_user_id',
        'reviewed_by_telegram_id',
        'sent_at',
        'reviewed_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    public function isActive(): bool
    {
        return in_array($this->delivery_status, [
            self::DELIVERY_QUEUED,
            self::DELIVERY_SENT,
            self::DELIVERY_FAILED,
        ], true);
    }
}
