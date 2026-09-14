<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KajianNotificationDispatch extends Model
{
    public const TYPE_BEFORE_START = 'before_start';

    public const TYPE_AFTER_END = 'after_end';

    public const TYPE_LATE = 'late';

    public const STATUS_QUEUED = 'queued';

    public const STATUS_SENT = 'sent';

    public const STATUS_SKIPPED = 'skipped';

    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'kajian_event_id',
        'notification_type',
        'scheduled_for',
        'status',
        'attempts',
        'total_count',
        'sent_count',
        'failed_count',
        'last_error',
        'sent_at',
    ];

    protected $casts = [
        'scheduled_for' => 'datetime',
        'sent_at' => 'datetime',
        'attempts' => 'integer',
        'total_count' => 'integer',
        'sent_count' => 'integer',
        'failed_count' => 'integer',
    ];

    public function kajianEvent(): BelongsTo
    {
        return $this->belongsTo(KajianEvent::class);
    }
}
