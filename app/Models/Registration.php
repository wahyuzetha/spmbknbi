<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_number',
        'user_id',
        'academic_year_id',
        'major_id',
        'major_choice_2_id',
        'registration_type',
        'recommendation_by',
        'status',
        'verified_by',
        'verified_at',
        'verification_notes',
        'printed_card_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
            'printed_card_at' => 'datetime',
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function major(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_id');
    }

    public function secondChoiceMajor(): BelongsTo
    {
        return $this->belongsTo(Major::class, 'major_choice_2_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function biodata(): HasOne
    {
        return $this->hasOne(StudentBiodata::class);
    }

    public function parents(): HasOne
    {
        return $this->hasOne(StudentParent::class);
    }

    public function school(): HasOne
    {
        return $this->hasOne(StudentSchool::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // Helper method to generate unique registration number
    public static function generateNumber(string $year = null): string
    {
        $year = $year ?? date('Y');
        $count = static::whereYear('created_at', $year)->count() + 1;
        return 'SPMB-' . $year . '-' . str_pad((string)$count, 4, '0', STR_PAD_LEFT);
    }
}
