<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublicAttendanceLink extends Model
{
    use HasFactory;

    public const AUDIENCE_MUSTAWA_ONE_NEW = 'mustawa_1_new';

    protected $fillable = [
        'audience',
        'token',
        'is_active',
        'created_by',
        'revoked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'revoked_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForMustawaOneNew($query)
    {
        return $query->where('audience', self::AUDIENCE_MUSTAWA_ONE_NEW);
    }
}
