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
                'slug' => 'layanan-perbankan-syariah-bsi',
                'name' => 'Layanan Perbankan Syariah (Kelas BSI)',
                'industry_class' => 'Kelas BSI',
                'quota' => 100,
                'tagline' => 'Mencetak Tenaga Profesional Perbankan & Lembaga Keuangan Syariah Modern Berstandar BSI',
                'description' => 'Mempelajari perbankan modern dan syariah bekerja sama langsung dengan Bank Syariah Indonesia (BSI), meliputi manajemen teller, customer service, produk perbankan, dan sistem akuntansi digital.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'icon' => 'fa-building-columns',
                'is_active' => true,
            ],
            [
                'code' => 'TBO',
                'slug' => 'teknik-body-otomotif-laksana',
                'name' => 'Teknik Body Otomotif (Kelas Laksana)',
                'industry_class' => 'Kelas Laksana',
                'quota' => 100,
                'tagline' => 'Keahlian Rekayasa Karoseri, Pengecatan Canggih & Perbaikan Rangka Bodi Bus Modern',
                'description' => 'Fokus pada teknik perbaikan bodi kendaraan, pengelasan presisi, pengecatan profesional standar pabrik, dan manufaktur karoseri bus bermitra dengan Karoseri Laksana.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'icon' => 'fa-truck',
                'is_active' => true,
            ],
            [
                'code' => 'TKJ Axioo',
                'slug' => 'teknik-komputer-jaringan-axioo',
                'name' => 'Teknik Komputer & Jaringan (Kelas Axioo)',
                'industry_class' => 'Kelas Axioo',
                'quota' => 130,
                'tagline' => 'Pusat Keunggulan Hardware, Laptop Repair & Jaringan Komputer Standar Axioo Class Program',
                'description' => 'Program pendidikan kejuruan berstandar Axioo Class Program meliputi perakitan laptop, perbaikan motherboard, jaringan komputer MikroTik, dan sistem komputasi awan.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'icon' => 'fa-laptop',
                'is_active' => true,
            ],
            [
                'code' => 'TKJ+',
                'slug' => 'teknik-komputer-jaringan-plus-busana',
                'name' => 'Teknik Komputer & Jaringan Plus ( Tata Busana)',
                'industry_class' => 'Tata Busana',
                'quota' => 100,
                'tagline' => 'Integrasi Keahlian IT Digital Kreatif & Desain Mode Fesyen Apparel Siap Wirausaha Mandiri',
                'description' => 'Kombinasi keahlian teknologi informatika jaringan dengan keahlian tata busana kreatif, desain mode CAD, e-commerce fesyen, dan produksi garmen industri modern.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'icon' => 'fa-shirt',
                'is_active' => true,
            ],
            [
                'code' => 'TKR',
                'slug' => 'teknik-kendaraan-ringan-daihatsu',
                'name' => 'Teknik Kendaraan Ringan (Kelas Daihatsu)',
                'industry_class' => 'Kelas Daihatsu',
                'quota' => 100,
                'tagline' => 'Mencetak Teknisi Ahli Otomotif Mobil Berstandar PT Astra Daihatsu Motor',
                'description' => 'Mempelajari perawatan berkala, tune-up, overhaul mesin, kelistrikan bodi, dan sistem injeksi EFI kendaraan bermotor roda empat berstandar dealer resmi Daihatsu.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'icon' => 'fa-car',
                'is_active' => true,
            ],
            [
                'code' => 'TSM',
                'slug' => 'teknik-sepeda-motor-yamaha',
                'name' => 'Teknik Sepeda Motor (Kelas Yamaha)',
                'industry_class' => 'Kelas Yamaha',
                'quota' => 150,
                'tagline' => 'Laboratorium Yamaha Technical Academy (YTA) Resmi, Ahli Teknologi Injeksi & Motor Modern',
                'description' => 'Mempelajari teknologi sepeda motor terkini, diagnostic tools Yamaha (YDT), sistem injeksi canggih, kelistrikan bodi, dan manajemen bengkel resmi Yamaha.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'icon' => 'fa-motorcycle',
                'is_active' => true,
            ],
            [
                'code' => 'TTT',
                'slug' => 'teknik-komputer-jaringan-telkom',
                'name' => 'Teknik Komputer dan Jaringan (Kelas Telkom)',
                'industry_class' => 'Kelas Telkom',
                'quota' => 80,
                'tagline' => 'Spesialis Jaringan Fiber Optik, Infrastruktur Telekomunikasi 5G & Cloud PT Telkom Indonesia',
                'description' => 'Fokus pada penyambungan kabel fiber optik modern, telekomunikasi nirkabel, instalasi jaringan FTTH, dan infrastruktur enterprise bekerja sama dengan Telkom Indonesia.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'icon' => 'fa-network-wired',
                'is_active' => true,
            ],
        ];

        foreach ($majors as $data) {
            $major = Major::where('code', $data['code'])->first();
            if ($major) {
                $major->update([
                    'slug' => $data['slug'],
                    'tagline' => $major->tagline ?: $data['tagline'],
                    'description' => $data['description'],
                    'industry_class' => $data['industry_class'],
                    'video_url' => $major->video_url ?: $data['video_url'],
                    'icon' => $data['icon'],
                    'is_active' => true,
                ]);
            } else {
                Major::create($data);
            }
        }
    }
}
