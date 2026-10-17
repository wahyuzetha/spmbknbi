<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AcademicYear::updateOrCreate(
            ['name' => '2027/2028'],
            [
                'is_active' => true,
                'registration_start' => now()->startOfYear(),
                'registration_end' => '2027-08-11',
                'description' => 'Penerimaan Peserta Didik Baru (PPDB) SMK Karya Nugraha Boyolali Tahun Pelajaran 2027/2028',
            ]
        );
    }
}
