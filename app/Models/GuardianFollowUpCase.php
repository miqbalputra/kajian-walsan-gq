<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class GuardianFollowUpCase extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['next_follow_up_at' => 'date'];

    public const STATUSES = ['open' => 'Belum ditindaklanjuti', 'in_progress' => 'Dalam proses', 'completed' => 'Selesai'];

    public const CHANNELS = ['whatsapp' => 'WhatsApp', 'phone' => 'Telepon', 'meeting' => 'Tatap muka', 'note' => 'Catatan internal', 'other' => 'Lainnya'];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ParentModel::class);
    }

    public function kajianEvent(): BelongsTo
    {
        return $this->belongsTo(KajianEvent::class);
    }

    public function entries(): HasMany
    {
        return $this->hasMany(GuardianFollowUpEntry::class)->orderByDesc('id');
    }

    public function latestEntry(): HasOne
    {
        return $this->hasOne(GuardianFollowUpEntry::class)->latestOfMany();
    }
}
