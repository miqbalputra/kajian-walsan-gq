<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PromotionChange extends Model
{
    use HasFactory;

    protected $fillable = [
        'promotion_batch_id',
        'student_id',
        'before_class_id',
        'after_class_id',
        'before_class_name',
        'after_class_name',
        'before_status',
        'after_status',
        'before_is_active',
        'after_is_active',
        'action',
        'notes',
    ];

    protected $casts = [
        'before_is_active' => 'boolean',
        'after_is_active' => 'boolean',
    ];

    public function batch(): BelongsTo
    {
        return $this->belongsTo(PromotionBatch::class, 'promotion_batch_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
