<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSchool extends Model
{
    use HasFactory;

    protected $fillable = [
        'registration_id',
        'asal_sekolah',
        'npsn_sekolah',
        'jenis_sekolah',
        'status_sekolah',
        'kabupaten_sekolah',
        'tahun_lulus',
        'no_ijazah',
        'nilai_rata_raport',
    ];

    protected function casts(): array
    {
        return [
            'nilai_rata_raport' => 'decimal:2',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
