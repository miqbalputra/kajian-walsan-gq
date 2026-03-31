<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Get all kajian events for this academic year.
     */
    public function kajianEvents(): HasMany
    {
        return $this->hasMany(KajianEvent::class);
    }

    /**
     * Get the currently active academic year.
     */
    public static function active(): ?self
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Scope for active academic year.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Set this academic year as active and deactivate others.
     */
    public function setAsActive(): bool
    {
        static::where('is_active', true)->update(['is_active' => false]);
        return $this->update(['is_active' => true]);
    }
}
