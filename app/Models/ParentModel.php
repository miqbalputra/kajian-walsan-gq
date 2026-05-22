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
        'is_teacher',
        'is_single_parent',
        'qr_code_string',
        'nik',
        'occupation',
        'address',
    ];

    protected $casts = [
        'is_teacher' => 'boolean',
        'is_single_parent' => 'boolean',
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
                $parent->qr_code_string = static::generateForParent($parent);
            }
        });

        // Paksa perbaikan kode jika masih menggunakan format lama (WS-) saat di-update
        static::saving(function ($parent) {
            if (str_starts_with($parent->qr_code_string, 'WS-')) {
                $parent->qr_code_string = static::generateForParent($parent);
            }
        });
    }

    /**
     * Generate a QR code string based on parent type and child NIS.
     * Fallback to random if no child is linked yet.
     */
    public static function generateForParent($parent): string
    {
        // Jika dipanggil saat creating, relasi mungkin belum ada.
        // Kita butuh murid pertama.
        $student = $parent->students()->first();

        if ($student && !empty($student->nis)) {
            $prefix = match ($parent->type) {
                'father' => 'A',
                'mother' => 'B',
                'teacher' => 'T',
                default => 'X',
            };
            return $prefix . $student->nis;
        }

        // Fallback jika belum ada murid terhubung
        do {
            $code = 'TMP-' . strtoupper(Str::random(8));
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
     * Check if this parent is a teacher.
     */
    public function isTeacher(): bool
    {
        return $this->type === 'teacher' || (bool) $this->is_teacher;
    }

    /**
     * Check if this parent is a wali santri record.
     */
    public function isGuardian(): bool
    {
        return in_array($this->type, ['father', 'mother'], true);
    }

    /**
     * Check if this wali santri is also a teacher.
     */
    public function isWaliTeacher(): bool
    {
        return $this->isGuardian() && (bool) $this->is_teacher;
    }

    /**
     * Check if this is a teacher-only profile, not a wali santri.
     */
    public function isPureTeacher(): bool
    {
        return $this->type === 'teacher';
    }

    /**
     * Get the type display name.
     */
    public function getTypeDisplayAttribute(): string
    {
        return match ($this->type) {
            'father' => 'Ayah',
            'mother' => 'Ibu',
            'teacher' => 'Guru',
            default => 'Unknown',
        };
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
     * Regenerate QR code string based on child NIS.
     */
    public function regenerateQrCode(): bool
    {
        $this->qr_code_string = static::generateForParent($this);
        return $this->save();
    }

    /**
     * Sync QR code string with linked student NIS.
     * Useful when a student is newly linked or NIS is updated.
     */
    public function syncQrCode(): bool
    {
        $newCode = static::generateForParent($this);
        
        // Hanya update jika formatnya berubah (misal dari TMP ke NIS)
        if ($this->qr_code_string !== $newCode) {
            $this->qr_code_string = $newCode;
            return $this->save();
        }

        return false;
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
