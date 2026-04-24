<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role_id',
        'phone',
        'avatar',
        'is_active',
        'google_id',
        'google_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    /**
     * Check if user has linked Google account.
     */
    public function hasGoogleLinked(): bool
    {
        return !is_null($this->google_id);
    }

    /**
     * Get the role for this user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the parent profile if user is wali_santri.
     */
    public function parentProfile(): HasOne
    {
        return $this->hasOne(ParentModel::class, 'user_id');
    }

    /**
     * Get kajian events created by this user.
     */
    public function createdKajianEvents(): HasMany
    {
        return $this->hasMany(KajianEvent::class, 'created_by');
    }

    /**
     * Check if user has a specific role.
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role?->name === $roleName;
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is panitia.
     */
    public function isPanitia(): bool
    {
        return $this->hasRole('panitia');
    }

    /**
     * Check if user is kepala sekolah.
     */
    public function isKepsek(): bool
    {
        return $this->hasRole('kepsek');
    }

    /**
     * Check if user is wali kelas.
     */
    public function isWaliKelas(): bool
    {
        return $this->hasRole('wali_kelas');
    }

    /**
     * Check if user is guru.
     */
    public function isGuru(): bool
    {
        return $this->hasRole('guru');
    }

    /**
     * Check if user is wali santri.
     */
    public function isWaliSantri(): bool
    {
        return $this->hasRole('wali_santri');
    }

    /**
     * Check if user can manage events.
     */
    public function canManageEvents(): bool
    {
        return $this->isAdmin() || $this->isPanitia();
    }

    /**
     * Check if user can view reports.
     */
    public function canViewReports(): bool
    {
        return $this->isAdmin() || $this->isPanitia() || $this->isKepsek() || $this->isWaliKelas() || $this->isGuru();
    }

    /**
     * Get the class managed by this teacher.
     */
    public function managedClass(): HasOne
    {
        return $this->hasOne(ClassRoom::class, 'teacher_id');
    }

    /**
     * Get user's role display name.
     */
    public function getRoleDisplayAttribute(): string
    {
        return $this->role?->display_name ?? 'No Role';
    }

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by role.
     */
    public function scopeWithRole($query, string $roleName)
    {
        return $query->whereHas('role', fn($q) => $q->where('name', $roleName));
    }
}
