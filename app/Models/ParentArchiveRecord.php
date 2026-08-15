<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParentArchiveRecord extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'reason',
        'login_disabled',
        'archived_by',
        'archived_at',
        'restored_at',
        'restored_by',
        'restore_notes',
    ];

    protected $casts = [
        'login_disabled' => 'boolean',
        'archived_at' => 'datetime',
        'restored_at' => 'datetime',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function restoredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'restored_by');
    }

    public function scopeOpen($query)
    {
        return $query->whereNull('restored_at');
    }
}
