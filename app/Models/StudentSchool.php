<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentSchool extends Model
{
    use HasFactory;

    protected $table = 'asal_sekolah_siswa';

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

    /**
     * Normalisasi nama asal sekolah (SMP / MTs) ke satu kosakata default standar
     * Contoh: "SMP N 1 Boyolali", "SMPN 1 BOYOLALI", "SMP 1 Boyolali" => "SMP NEGERI 1 BOYOLALI"
     */
    public static function normalizeSchoolName(?string $name): string
    {
        if (empty($name) || trim($name) === '-' || trim($name) === '') {
            return 'TIDAK DIKETAHUI';
        }

        $name = strtoupper(trim($name));
        // Bersihkan tanda titik, koma, strip dekoratif
        $name = str_replace(['.', ',', '-'], ' ', $name);
        // Rapikan spasi ganda
        $name = preg_replace('/\s+/', ' ', $name);

        // Standarisasi variasi SMP Negeri
        $name = preg_replace('/^SMP\s*(?:N(?:EGERI)?|N)?\s*(\d+)/i', 'SMP NEGERI $1', $name);
        $name = preg_replace('/^SMPN\s*(\d+)/i', 'SMP NEGERI $1', $name);
        $name = preg_replace('/^SMP\s+N\s+/i', 'SMP NEGERI ', $name);

        // Standarisasi variasi MTs Negeri
        $name = preg_replace('/^MTS\s*(?:N(?:EGERI)?|N)?\s*(\d+)/i', 'MTS NEGERI $1', $name);
        $name = preg_replace('/^MTSN\s*(\d+)/i', 'MTS NEGERI $1', $name);
        $name = preg_replace('/^MTS\s+N\s+/i', 'MTS NEGERI ', $name);

        return trim($name);
    }
}
