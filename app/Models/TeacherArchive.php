<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class TeacherArchive extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'departed_on' => 'date',
        'profile_snapshot' => 'array',
        'attendance_snapshot' => 'array',
    ];

    protected static function booted(): void
    {
        $readOnly = function () {
            throw ValidationException::withMessages(['archive' => 'Arsip guru hanya dapat dibaca dan tidak dapat diubah atau dihapus.']);
        };
        static::updating($readOnly);
        static::deleting($readOnly);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }
}
