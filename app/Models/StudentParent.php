<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentParent extends Model
{
    use HasFactory;

    protected $table = 'orang_tua_siswa';

    protected $fillable = [
        'registration_id',
        'nama_ayah',
        'status_ayah',
        'nik_ayah',
        'pekerjaan_ayah',
        'pendidikan_ayah',
        'penghasilan_ayah',
        'no_hp_ayah',
        'nama_ibu',
        'status_ibu',
        'nik_ibu',
        'pekerjaan_ibu',
        'pendidikan_ibu',
        'penghasilan_ibu',
        'no_hp_ibu',
        'nama_wali',
        'hubungan_wali',
        'pekerjaan_wali',
        'no_hp_wali',
        'alamat_wali',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
