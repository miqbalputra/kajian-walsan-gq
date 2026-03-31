<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ParentModel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'parents';

    protected $fillable = [
        'user_id',
        'type',
        'is_single_parent',
        'qr_code_string',
        'nik',
        'occupation',
        'address',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-generate QR code string on creation
        static::creating(function ($parent) {
            if (empty($parent->qr_code_string)) {
                $parent->qr_code_string = static::generateUniqueQrCode();
            }
        });
    }

    /**
     * Generate a unique QR code string.
     */
    public static function generateUniqueQrCode(): string
    {
        do {
            $code = 'WS-' . strtoupper(Str::random(8)) . '-' . now()->format('Y');
        } while (static::where('qr_code_string', $code)->exists());

        return $code;
    }

    /**
     * Get the user account for this parent.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all children (students) of this parent.
     * Supports siblings - one parent can have multiple children.
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id')
            ->withPivot(['relationship', 'is_primary_contact'])
            ->withTimestamps();
    }

    /**
     * Alias for students - get all children.
     */
    public function children(): BelongsToMany
    {
        return $this->students();
    }

    /**
     * Get all attendance records for this parent.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'parent_id');
    }

    /**
     * Check if this parent is the father.
     */
    public function isFather(): bool
    {
        return $this->type === 'father';
    }

    /**
     * Check if this parent is the mother.
     */
    public function isMother(): bool
    {
        return $this->type === 'mother';
    }

    /**
     * Get the type display name.
     */
    public function getTypeDisplayAttribute(): string
    {
        return $this->isFather() ? 'Ayah' : 'Ibu';
    }

    /**
     * Get the spouse (other parent of the same children).
     */
    public function getSpouseAttribute(): ?self
    {
        $childIds = $this->students()->pluck('students.id');

        if ($childIds->isEmpty()) {
            return null;
        }

        return static::whereHas('students', function ($query) use ($childIds) {
            $query->whereIn('students.id', $childIds);
        })
            ->where('id', '!=', $this->id)
            ->where('type', '!=', $this->type)
            ->first();
    }

    /**
     * Scope to find by QR code.
     */
    public function scopeByQrCode($query, string $qrCode)
    {
        return $query->where('qr_code_string', $qrCode);
    }

    /**
     * Find parent by QR code string.
     */
    public static function findByQrCode(string $qrCode): ?self
    {
        return static::byQrCode($qrCode)->first();
    }

    /**
     * Regenerate QR code string for security purposes.
     * Can be called manually by admin or periodically.
     */
    public function regenerateQrCode(): bool
    {
        $this->qr_code_string = static::generateUniqueQrCode();
        return $this->save();
    }

    /**
     * Check if QR code is old and might need regeneration.
     * Returns true if QR code hasn't been updated in specified months.
     */
    public function isQrCodeOld(int $months = 12): bool
    {
        return $this->updated_at->diffInMonths(now()) >= $months;
    }
}
