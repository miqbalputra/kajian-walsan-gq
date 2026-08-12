<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class PromotionBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_academic_year_id',
        'target_academic_year_id',
        'initiated_by',
        'status',
        'class_mapping',
        'summary',
        'applied_at',
        'rolled_back_at',
    ];

    protected $casts = [
        'class_mapping' => 'array',
        'summary' => 'array',
        'applied_at' => 'datetime',
        'rolled_back_at' => 'datetime',
    ];

    public function sourceAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'source_academic_year_id');
    }

    public function targetAcademicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'target_academic_year_id');
    }

    public function initiator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }

    public function changes(): HasMany
    {
        return $this->hasMany(PromotionChange::class);
    }

    public function isApplied(): bool
    {
        return $this->status === 'applied';
    }
}
