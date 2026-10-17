<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Major extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'industry_class',
        'quota',
        'description',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'major_id');
    }

    public function secondaryRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'major_choice_2_id');
    }

    public function getRemainingQuotaAttribute(): int
    {
        $acceptedCount = $this->registrations()->where('status', 'accepted')->count();
        return max(0, $this->quota - $acceptedCount);
    }
}
