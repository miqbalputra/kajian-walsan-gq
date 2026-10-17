<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuardianFollowUpEntry extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['contacted_at' => 'datetime', 'next_follow_up_at' => 'date'];

    public function followUpCase(): BelongsTo
    {
        return $this->belongsTo(GuardianFollowUpCase::class, 'guardian_follow_up_case_id');
    }
}
