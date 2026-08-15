<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class UserLoginAlias extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'source_student_id',
        'username',
        'password',
        'kind',
        'is_active',
        'revoked_at',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
        'revoked_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
