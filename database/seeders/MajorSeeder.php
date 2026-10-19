<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $majors = [
            [
                'code' => 'PSY',
                'name' => 'Layanan Perbankan Syariah (Kelas BSI)',
                'industry_class' => 'Kelas BSI',
                'quota' => 100,
                'description' => 'Mempelajari perbankan modern dan syariah bekerja sama dengan Bank Syariah Indonesia.',
                'icon' => 'fa-building-columns',
                'is_active' => true,
            ],
            [
                'code' => 'TBO',
                'name' => 'Teknik Body Otomotif (Kelas Laksana)',
                'industry_class' => 'Kelas Laksana',
                'quota' => 100,
                'description' => 'Fokus pada teknik perbaikan bodi kendaraan, pengecatan profesional, dan karoseri bus bermitra dengan Karoseri Laksana.',
                'icon' => 'fa-truck',
                'is_active' => true,
            ],
            [
                'code' => 'TKJ Axioo',
                'name' => 'Teknik Komputer & Jaringan (Kelas Axioo)',
                'industry_class' => 'Kelas Axioo',
                'quota' => 500,
                'description' => 'Program pendidikan kejuruan berstandar Axioo Class Program meliputi perakitan laptop, jaringan komputer, dan mikrotik.',
                'icon' => 'fa-laptop',
                'is_active' => true,
            ],
            [
                'code' => 'TKJ+',
                'name' => 'Teknik Komputer & Jaringan Plus ( Tata Busana)',
                'industry_class' => 'Tata Busana',
                'quota' => 500,
                'description' => 'Kombinasi keahlian teknologi informatika jaringan dengan keahlian tata busana kreatif.',
                'icon' => 'fa-shirt',
                'is_active' => true,
            ],
            [
                'code' => 'TKR',
                'name' => 'Teknik Kendaraan Ringan (Kelas Daihatsu)',
                'industry_class' => 'Kelas Daihatsu',
                'quota' => 800,
                'description' => 'Mempelajari perawatan dan perbaikan mesin kendaraan bermotor roda empat berstandar industri Daihatsu.',
                'icon' => 'fa-car',
                'is_active' => true,
            ],
            [
                'code' => 'TSM',
                'name' => 'Teknik Sepeda Motor (Kelas Yamaha)',
                'industry_class' => 'Kelas Yamaha',
                'quota' => 500,
                'description' => 'Mempelajari teknologi sepeda motor terkini, sistem injeksi, dan manajemen bengkel resmi Yamaha.',
                'icon' => 'fa-motorcycle',
                'is_active' => true,
            ],
            [
                'code' => 'TTT',
                'name' => 'Teknik Komputer dan Jaringan (Kelas Telkom)',
                'industry_class' => 'Kelas Telkom',
                'quota' => 500,
                'description' => 'Fokus pada jaringan fiber optik, telekomunikasi modern, dan infrastruktur cloud bekerja sama dengan Telkom.',
                'icon' => 'fa-network-wired',
                'is_active' => true,
            ],
        ];

        foreach ($majors as $major) {
            Major::firstOrCreate(
                ['code' => $major['code']],
                $major
            );
        }
    }
}
