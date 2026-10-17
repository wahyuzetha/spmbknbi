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

    protected $table = 'pendaftaran';

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
        return $this->belongsTo(Siswa::class, 'user_id');
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'user_id');
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

    public function majorChoice2(): BelongsTo
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

    public function parent(): HasOne
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

    /**
     * Generate Nomor Pendaftaran SPMB 6 digit sesuai logika 3 bagian:
     * - Bagian 1 (2 digit): Tahun Pelajaran aktif (contoh: 2027/2028 -> 27, ganti tahun otomatis ganti)
     * - Bagian 2 (1 digit): Urutan Kode Jurusan:
     *     1 = Teknik Kendaraan Ringan (TKR)
     *     2 = Teknik Body Otomotif (TBO)
     *     3 = Teknik Sepeda Motor (TSM)
     *     4 = Teknik Komputer dan Jaringan (TKJ Axioo)
     *     5 = Teknik Komputer dan Tata Busana (TKJ+)
     *     6 = Perbankan Syariah (PSY)
     *     7 = Teknik Komputer Jaringan (Kelas Telkom / TTT)
     * - Bagian 3 (3 digit): Urutan ID siswa di database (contoh: 001 s/d 012). Saat ID di-reset kembali ke 001.
     *
     * Contoh: 276012
     */
    public static function generateNumber($major = null, ?int $studentId = null, $academicYear = null): string
    {
        // 1. Digit Tahun Pelajaran (2 digit)
        $yearDigits = static::getYearDigits($academicYear);

        // 2. Digit Urutan Jurusan (1 digit)
        $majorDigit = static::getMajorDigit($major);

        // 3. Digit Urutan ID Siswa (3 digit)
        if ($studentId) {
            $seq = (int)$studentId;
        } else {
            $latestId = Siswa::max('id') ?? 0;
            $seq = $latestId + 1;
        }
        $seqDigits = str_pad((string)$seq, 3, '0', STR_PAD_LEFT);

        $regNumber = $yearDigits . $majorDigit . $seqDigits;

        // Pastikan unik jika nomor sudah ada sebelumnya
        while (static::where('registration_number', $regNumber)->exists()) {
            $seq++;
            $seqDigits = str_pad((string)$seq, 3, '0', STR_PAD_LEFT);
            $regNumber = $yearDigits . $majorDigit . $seqDigits;
        }

        return $regNumber;
    }

    /**
     * Dapatkan 2 digit tahun pelajaran dari tahun pelajaran aktif / yang diberikan.
     * Contoh: '2027/2028' -> '27', '2028/2029' -> '28'
     */
    public static function getYearDigits($academicYear = null): string
    {
        if (is_numeric($academicYear)) {
            $academicYear = AcademicYear::find($academicYear);
        }

        if (!$academicYear) {
            $academicYear = AcademicYear::where('is_active', true)->first();
        }

        $yearName = $academicYear?->name ?? '';

        if (preg_match('/(\d{4})/', $yearName, $matches)) {
            return substr($matches[1], -2);
        }

        if (preg_match('/^(\d{2})/', $yearName, $matches)) {
            return $matches[1];
        }

        if ($academicYear?->start_date) {
            return date('y', strtotime($academicYear->start_date));
        }

        return date('y');
    }

    /**
     * Dapatkan digit kode urutan jurusan (1 digit) sesuai aturan:
     * 1: Teknik Kendaraan Ringan
     * 2: Teknik Body Otomotif
     * 3: Teknik Sepeda Motor
     * 4: Teknik Komputer dan Jaringan
     * 5: Teknik Komputer dan Tata Busana
     * 6: Perbankan Syariah
     * 7: Teknik Komputer Jaringan (Kelas Telkom)
     */
    public static function getMajorDigit($major): string
    {
        if (is_numeric($major)) {
            $major = Major::find($major);
        }

        if (!$major) {
            return '1';
        }

        $code = strtoupper(trim($major->code ?? ''));
        $name = strtolower($major->name ?? '');
        $industry = strtolower($major->industry_class ?? '');

        // 1. Teknik Kendaraan Ringan
        if ($code === 'TKR' || str_contains($name, 'kendaraan ringan') || str_contains($industry, 'daihatsu')) {
            return '1';
        }

        // 2. Teknik Body Otomotif
        if ($code === 'TBO' || str_contains($name, 'body otomotif') || str_contains($name, 'bodi otomotif') || str_contains($industry, 'laksana')) {
            return '2';
        }

        // 3. Teknik Sepeda Motor
        if ($code === 'TSM' || str_contains($name, 'sepeda motor') || str_contains($industry, 'yamaha')) {
            return '3';
        }

        // 7. Teknik Komputer Jaringan (Kelas Telkom) -> Cek Telkom terlebih dahulu
        if ($code === 'TTT' || str_contains($name, 'telkom') || str_contains($industry, 'telkom')) {
            return '7';
        }

        // 5. Teknik Komputer dan Tata Busana
        if ($code === 'TKJ+' || str_contains($name, 'tata busana') || str_contains($industry, 'tata busana')) {
            return '5';
        }

        // 6. Perbankan Syariah
        if ($code === 'PSY' || str_contains($name, 'perbankan') || str_contains($name, 'syariah') || str_contains($industry, 'bsi')) {
            return '6';
        }

        // 4. Teknik Komputer dan Jaringan
        if (str_starts_with($code, 'TKJ') || str_contains($name, 'komputer') || str_contains($name, 'jaringan') || str_contains($industry, 'axioo')) {
            return '4';
        }

        return (string)($major->id ?? '1');
    }
}
