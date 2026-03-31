<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'kajian_event_id',
        'parent_id',
        'student_id',
        'status',
        'method',
        'proof_file',
        'notes',
        'validation_status',
        'validated_by',
        'validated_at',
        'rejection_reason',
        'scanned_at',
        'scan_location',
        'device_info',
    ];

    protected $casts = [
        'validated_at' => 'datetime',
        'scanned_at' => 'datetime',
    ];

    /**
     * Status options.
     */
    public const STATUS_HADIR_FISIK = 'hadir_fisik';
    public const STATUS_HADIR_ONLINE = 'hadir_online';
    public const STATUS_IZIN = 'izin';
    public const STATUS_ALPHA = 'alpha';

    /**
     * Method options.
     */
    public const METHOD_SCAN_QR = 'scan_qr';
    public const METHOD_MANUAL = 'manual';
    public const METHOD_UPLOAD = 'upload';

    /**
     * Validation status options.
     */
    public const VALIDATION_APPROVED = 'approved';
    public const VALIDATION_PENDING = 'pending';
    public const VALIDATION_REJECTED = 'rejected';

    /**
     * Get the kajian event for this attendance.
     */
    public function kajianEvent(): BelongsTo
    {
        return $this->belongsTo(KajianEvent::class, 'kajian_event_id');
    }

    /**
     * Get the parent who attended.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class, 'parent_id');
    }

    /**
     * Get the student snapshot (which child was represented).
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who validated this attendance.
     */
    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    /**
     * Check if attendance is approved.
     */
    public function isApproved(): bool
    {
        return $this->validation_status === self::VALIDATION_APPROVED;
    }

    /**
     * Check if attendance is pending validation.
     */
    public function isPending(): bool
    {
        return $this->validation_status === self::VALIDATION_PENDING;
    }

    /**
     * Check if attendance is rejected.
     */
    public function isRejected(): bool
    {
        return $this->validation_status === self::VALIDATION_REJECTED;
    }

    /**
     * Check if physically present.
     */
    public function isHadirFisik(): bool
    {
        return $this->status === self::STATUS_HADIR_FISIK;
    }

    /**
     * Get status display name.
     */
    public function getStatusDisplayAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_HADIR_FISIK => 'Hadir Fisik',
            self::STATUS_HADIR_ONLINE => 'Hadir Online',
            self::STATUS_IZIN => 'Izin',
            self::STATUS_ALPHA => 'Alpha',
            default => '-',
        };
    }

    /**
     * Get method display name.
     */
    public function getMethodDisplayAttribute(): string
    {
        return match ($this->method) {
            self::METHOD_SCAN_QR => 'Scan QR',
            self::METHOD_MANUAL => 'Input Manual',
            self::METHOD_UPLOAD => 'Upload Bukti',
            default => '-',
        };
    }

    /**
     * Get validation status display.
     */
    public function getValidationDisplayAttribute(): string
    {
        return match ($this->validation_status) {
            self::VALIDATION_APPROVED => 'Disetujui',
            self::VALIDATION_PENDING => 'Menunggu',
            self::VALIDATION_REJECTED => 'Ditolak',
            default => '-',
        };
    }

    /**
     * Approve this attendance.
     */
    public function approve(User $validator): bool
    {
        return $this->update([
            'validation_status' => self::VALIDATION_APPROVED,
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'rejection_reason' => null,
        ]);
    }

    /**
     * Reject this attendance.
     */
    public function reject(User $validator, string $reason): bool
    {
        return $this->update([
            'validation_status' => self::VALIDATION_REJECTED,
            'validated_by' => $validator->id,
            'validated_at' => now(),
            'rejection_reason' => $reason,
        ]);
    }

    /**
     * Record attendance via QR scan.
     */
    public static function recordFromQrScan(
        KajianEvent $event,
        ParentModel $parent,
        ?Student $student = null,
        ?string $location = null,
        ?string $deviceInfo = null
    ): self {
        return static::create([
            'kajian_event_id' => $event->id,
            'parent_id' => $parent->id,
            'student_id' => $student?->id,
            'status' => self::STATUS_HADIR_FISIK,
            'method' => self::METHOD_SCAN_QR,
            'validation_status' => self::VALIDATION_APPROVED,
            'scanned_at' => now(),
            'scan_location' => $location,
            'device_info' => $deviceInfo,
        ]);
    }

    /**
     * Scope for approved attendances.
     */
    public function scopeApproved($query)
    {
        return $query->where('validation_status', self::VALIDATION_APPROVED);
    }

    /**
     * Scope for pending attendances.
     */
    public function scopePending($query)
    {
        return $query->where('validation_status', self::VALIDATION_PENDING);
    }

    /**
     * Scope for present (hadir) attendances.
     */
    public function scopePresent($query)
    {
        return $query->whereIn('status', [self::STATUS_HADIR_FISIK, self::STATUS_HADIR_ONLINE]);
    }
}
