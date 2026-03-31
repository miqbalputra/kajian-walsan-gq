<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KajianFeedback extends Model
{
    use HasFactory;

    protected $table = 'kajian_feedback';

    protected $fillable = [
        'kajian_event_id',
        'user_id',
        'rating',
        'comment',
        'extra_feedback'
    ];

    protected $casts = [
        'extra_feedback' => 'json'
    ];

    /**
     * Get the event this feedback belongs to.
     */
    public function kajianEvent(): BelongsTo
    {
        return $this->belongsTo(KajianEvent::class);
    }

    /**
     * Get the user who provided the feedback.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
