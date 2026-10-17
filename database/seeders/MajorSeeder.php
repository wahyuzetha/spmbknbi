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
                'code' => 'TKR',
                'name' => 'Teknik Kendaraan Ringan',
                'industry_class' => 'Kelas Daihatsu',
                'quota' => 108,
                'description' => 'Mempelajari perawatan dan perbaikan mesin kendaraan bermotor roda empat berstandar industri Daihatsu.',
                'icon' => 'fa-car',
            ],
            [
                'code' => 'TBO',
                'name' => 'Teknik Body Otomotif',
                'industry_class' => 'Kelas Laksana',
                'quota' => 72,
                'description' => 'Fokus pada teknik perbaikan bodi kendaraan, pengecatan profesional, dan karoseri bus bermitra dengan Karoseri Laksana.',
                'icon' => 'fa-truck',
            ],
            [
                'code' => 'TSM',
                'name' => 'Teknik Sepeda Motor',
                'industry_class' => 'Kelas Yamaha',
                'quota' => 108,
                'description' => 'Mempelajari teknologi sepeda motor terkini, sistem injeksi, dan manajemen bengkel resmi Yamaha.',
                'icon' => 'fa-motorcycle',
            ],
            [
                'code' => 'TKJ-AXIOO',
                'name' => 'Teknik Komputer & Jaringan',
                'industry_class' => 'Kelas Axioo',
                'quota' => 72,
                'description' => 'Program pendidikan kejuruan berstandar Axioo Class Program meliputi perakitan laptop, jaringan komputer, dan mikrotik.',
                'icon' => 'fa-laptop',
            ],
            [
                'code' => 'TKJ-TELKOM',
                'name' => 'Teknik Komputer dan Jaringan',
                'industry_class' => 'Kelas Telkom',
                'quota' => 72,
                'description' => 'Fokus pada jaringan fiber optik, telekomunikasi modern, dan infrastruktur cloud bekerja sama dengan Telkom.',
                'icon' => 'fa-network-wired',
            ],
            [
                'code' => 'TKJ-TB',
                'name' => 'Teknik Komputer & Jaringan Plus',
                'industry_class' => 'Tata Busana',
                'quota' => 36,
                'description' => 'Kombinasi keahlian teknologi informatika dengan desain mode digital dan kewirausahaan tata busana.',
                'icon' => 'fa-tshirt',
            ],
            [
                'code' => 'LPS',
                'name' => 'Layanan Perbankan Syariah',
                'industry_class' => 'Kelas BSI',
                'quota' => 36,
                'description' => 'Mempersiapkan tenaga profesional di bidang administrasi perbankan syariah, akuntansi, dan customer service bermitra dengan Bank Syariah Indonesia.',
                'icon' => 'fa-money-bill-wave',
            ],
        ];

        foreach ($majors as $major) {
            Major::updateOrCreate(['code' => $major['code']], $major);
        }
    }
}
