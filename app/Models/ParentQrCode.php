<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ParentQrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'code',
        'kind',
        'source_student_id',
        'is_active',
        'revoked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'revoked_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function sourceStudent(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'source_student_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->whereNull('revoked_at');
    }
}
