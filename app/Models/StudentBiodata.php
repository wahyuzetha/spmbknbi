<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentBiodata extends Model
{
    use HasFactory;

    protected $table = 'biodata_siswa';

    protected $fillable = [
        'registration_id',
        'nik',
        'no_kk',
        'nama_lengkap',
        'jenis_kelamin',
        'agama',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'email',
        'alamat_jalan',
        'desa',
        'kecamatan',
        'kabupaten',
        'provinsi',
        'kode_pos',
        'anak_ke',
        'jumlah_saudara',
        'tinggi_badan',
        'berat_badan',
        'riwayat_penyakit',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date',
            'anak_ke' => 'integer',
            'jumlah_saudara' => 'integer',
            'tinggi_badan' => 'integer',
            'berat_badan' => 'integer',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }
}
