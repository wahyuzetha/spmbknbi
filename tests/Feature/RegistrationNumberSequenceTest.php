<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Registration;
use Tests\TestCase;

class RegistrationNumberSequenceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_registration_number_starts_at_001_per_major()
    {
        // Pastikan tahun ajaran aktif 2027/2028 (2 digit: '27')
        $year = AcademicYear::where('is_active', true)->first();
        $this->assertNotNull($year);

        // Ambil jurusan
        $tkr = Major::where('code', 'TKR')->first();
        $tbo = Major::where('code', 'TBO')->first();
        $tsm = Major::where('code', 'TSM')->first();

        $this->assertNotNull($tkr);
        $this->assertNotNull($tbo);
        $this->assertNotNull($tsm);

        // Bersihkan pendaftaran pengujian sebelumnya jika ada
        Registration::whereIn('major_id', [$tkr->id, $tbo->id, $tsm->id])->delete();

        // 1. Pendaftar 1 di TKR -> Harus 271001
        $noTkr1 = Registration::generateNumber($tkr->id, null, $year);
        $this->assertEquals('271001', $noTkr1);

        // Simpan pendaftar 1 TKR
        Registration::create([
            'registration_number' => $noTkr1,
            'user_id' => 9991,
            'academic_year_id' => $year->id,
            'major_id' => $tkr->id,
            'status' => 'pending',
        ]);

        // 2. Pendaftar 2 di TKR -> Harus 271002
        $noTkr2 = Registration::generateNumber($tkr->id, null, $year);
        $this->assertEquals('271002', $noTkr2);

        Registration::create([
            'registration_number' => $noTkr2,
            'user_id' => 9992,
            'academic_year_id' => $year->id,
            'major_id' => $tkr->id,
            'status' => 'pending',
        ]);

        // 3. Pendaftar 1 di TBO -> Harus 272001 (tidak terpengaruh nomor urut TKR)
        $noTbo1 = Registration::generateNumber($tbo->id, null, $year);
        $this->assertEquals('272001', $noTbo1);

        Registration::create([
            'registration_number' => $noTbo1,
            'user_id' => 9993,
            'academic_year_id' => $year->id,
            'major_id' => $tbo->id,
            'status' => 'pending',
        ]);

        // 4. Pendaftar 2 di TBO -> Harus 272002
        $noTbo2 = Registration::generateNumber($tbo->id, null, $year);
        $this->assertEquals('272002', $noTbo2);

        // 5. Pendaftar 1 di TSM -> Harus 273001
        $noTsm1 = Registration::generateNumber($tsm->id, null, $year);
        $this->assertEquals('273001', $noTsm1);

        Registration::create([
            'registration_number' => $noTsm1,
            'user_id' => 9994,
            'academic_year_id' => $year->id,
            'major_id' => $tsm->id,
            'status' => 'pending',
        ]);

        // 6. Pendaftar 2 di TSM -> Harus 273002
        $noTsm2 = Registration::generateNumber($tsm->id, null, $year);
        $this->assertEquals('273002', $noTsm2);
    }

    public function test_registration_number_when_changing_major()
    {
        $year = AcademicYear::where('is_active', true)->first();
        $tkr = Major::where('code', 'TKR')->first();
        $tbo = Major::where('code', 'TBO')->first();

        // Bersihkan pendaftaran pengujian
        Registration::whereIn('major_id', [$tkr->id, $tbo->id])->delete();

        // Buat pendaftar di TKR (271001)
        $reg = Registration::create([
            'registration_number' => Registration::generateNumber($tkr->id, null, $year),
            'user_id' => 9995,
            'academic_year_id' => $year->id,
            'major_id' => $tkr->id,
            'status' => 'pending',
        ]);

        $this->assertEquals('271001', $reg->registration_number);

        // Ubah jurusan ke TBO -> Otomatis nomor pendaftaran berubah jadi 272001
        $newNumber = $reg->updateMajorWithNumberChange($tbo);
        $this->assertEquals('272001', $newNumber);
        $this->assertEquals('272001', $reg->fresh()->registration_number);
    }
}
