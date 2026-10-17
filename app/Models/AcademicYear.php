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
        'is_active',
        'registration_start',
        'registration_end',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'registration_start' => 'date',
            'registration_end' => 'date',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
