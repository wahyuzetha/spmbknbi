<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\StudentBiodata;
use App\Models\StudentParent;
use App\Models\StudentSchool;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Akun Super Admin
        User::firstOrCreate(
            ['email' => 'admin@smkknbi.sch.id'],
            [
                'name' => 'Administrator SPMB',
                'username' => 'admin',
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 2. Akun Panitia PPDB 1 (smkknbi)
        User::firstOrCreate(
            ['email' => 'panitia@smkknbi.sch.id'],
            [
                'name' => 'smkknbi',
                'username' => 'smkknbi',
                'phone' => '081234567891',
                'role' => 'panitia',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 3. Akun Panitia PPDB 2 (kepala sekolah / kssmkknbi)
        User::firstOrCreate(
            ['username' => 'kssmkknbi'],
            [
                'name' => 'kepala sekolah',
                'email' => 'kssmkknbi@sch.id',
                'phone' => '081234567892',
                'role' => 'panitia',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 4. Akun Bendahara SPMB (Kasir & Rekap Pembayaran)
        User::firstOrCreate(
            ['username' => 'bendahara'],
            [
                'name' => 'Bendahara SPMB',
                'email' => 'bendahara@smkknbi.sch.id',
                'phone' => '081234567893',
                'role' => 'bendahara',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 3. Akun Murid & Data Pendaftaran Lengkap (Sesuai Referensi Gambar Pengguna)
        $studentsData = [
            [
                'reg_no' => '274001',
                'name' => 'SGADG',
                'username' => '149790780',
                'password' => '12345',
                'gender' => 'P',
                'school' => 'SMP NEGERI 3 MOJOSONGO',
                'phone' => '081497907800',
                'major_code' => 'TBO',
                'status' => 'pending',
                'desa' => 'Mojosongo',
                'kecamatan' => 'Mojosongo',
                'ayah' => 'Joko Susilo',
                'ibu' => 'Sri Utami',
            ],
            [
                'reg_no' => '271006',
                'name' => 'MUHAMMAD AKSA ANGGARA',
                'username' => '085876098129',
                'password' => 'berkah2024',
                'gender' => 'L',
                'school' => 'MTS AL MANSHUR POPONGAN KLATEN',
                'phone' => '085876098129',
                'major_code' => 'TKR',
                'status' => 'pending',
                'desa' => 'Popongan',
                'kecamatan' => 'Klaten Utara',
                'ayah' => 'Agus Anggara',
                'ibu' => 'Nurul Hidayah',
            ],
            [
                'reg_no' => '274005',
                'name' => 'FARADIVA KHARISMA OKTAVIANA',
                'username' => '089648093000',
                'password' => 'faradiva',
                'gender' => 'P',
                'school' => 'SMP N 5 BOYOLALI',
                'phone' => '089648093000',
                'major_code' => 'TKJ Axioo',
                'status' => 'pending',
                'desa' => 'Penggung',
                'kecamatan' => 'Boyolali',
                'ayah' => 'Bambang Irawan',
                'ibu' => 'Endang Sulastri',
            ],
            [
                'reg_no' => '273004',
                'name' => 'ARFAN YUANDA',
                'username' => '087702011280',
                'password' => 'panzz19',
                'gender' => 'L',
                'school' => 'SMP NEGERI 3 MOJOSONGO',
                'phone' => '087702011280',
                'major_code' => 'TSM',
                'status' => 'pending',
                'desa' => 'Kemiri',
                'kecamatan' => 'Mojosongo',
                'ayah' => 'Suryanto',
                'ibu' => 'Wahyuni',
            ],
            [
                'reg_no' => '271003',
                'name' => 'ALIF RENGGA SAPUTRA',
                'username' => '0816687808',
                'password' => '12345678',
                'gender' => 'L',
                'school' => 'SMP PESANTREN AFADA',
                'phone' => '0816687808',
                'major_code' => 'TKR',
                'status' => 'pending',
                'desa' => 'Kebonbimo',
                'kecamatan' => 'Boyolali',
                'ayah' => 'Hadi Saputra',
                'ibu' => 'Siti Rohmah',
            ],
            [
                'reg_no' => '272002',
                'name' => 'ANANDA REY ALTEZA',
                'username' => '083725031645',
                'password' => 'alteza123',
                'gender' => 'L',
                'school' => 'MTSN 3 BOYOLALI',
                'phone' => '083725031645',
                'major_code' => 'TBO',
                'status' => 'pending',
                'desa' => 'Siswodipuran',
                'kecamatan' => 'Boyolali',
                'ayah' => 'Dwi Purnomo',
                'ibu' => 'Rina Kartika',
            ],
            [
                'reg_no' => '276001',
                'name' => 'VENIA CITRA PUTRI',
                'username' => '082258772434',
                'password' => '082258772434',
                'gender' => 'P',
                'school' => 'SMP N 1 CEPOGO',
                'phone' => '082258772434',
                'major_code' => 'PSY',
                'status' => 'pending',
                'desa' => 'Cepogo',
                'kecamatan' => 'Cepogo',
                'ayah' => 'Supriyadi',
                'ibu' => 'Tri Hastuti',
            ],
            [
                'reg_no' => '271008',
                'name' => 'BAGAS PRATAMA PUTRA',
                'username' => '085712345601',
                'password' => 'bagas2026',
                'gender' => 'L',
                'school' => 'SMP N 2 BOYOLALI',
                'phone' => '085712345601',
                'major_code' => 'TKR',
                'status' => 'pending',
                'desa' => 'Karanggeneng',
                'kecamatan' => 'Boyolali',
                'ayah' => 'Sugeng Pratama',
                'ibu' => 'Sri Wahyuni',
            ],
            [
                'reg_no' => '274009',
                'name' => 'DITA AYU LESTARI',
                'username' => '081298765402',
                'password' => 'ditaayu',
                'gender' => 'P',
                'school' => 'SMP N 1 MOJOSONGO',
                'phone' => '081298765402',
                'major_code' => 'TKJ Axioo',
                'status' => 'pending',
                'desa' => 'Mojosongo',
                'kecamatan' => 'Mojosongo',
                'ayah' => 'Budi Lestari',
                'ibu' => 'Sulastri',
            ],
            [
                'reg_no' => '273010',
                'name' => 'GILANG RAMADHAN',
                'username' => '087811223303',
                'password' => 'gilang123',
                'gender' => 'L',
                'school' => 'MTS N 1 BOYOLALI',
                'phone' => '087811223303',
                'major_code' => 'TSM',
                'status' => 'pending',
                'desa' => 'Banyudono',
                'kecamatan' => 'Banyudono',
                'ayah' => 'Ahmad Fauzi',
                'ibu' => 'Siti Aminah',
            ],
            [
                'reg_no' => '272011',
                'name' => 'HAFIDZ NUR HIDAYAT',
                'username' => '089533445504',
                'password' => 'hafidz2026',
                'gender' => 'L',
                'school' => 'SMP NEGERI 4 BOYOLALI',
                'phone' => '089533445504',
                'major_code' => 'TBO',
                'status' => 'pending',
                'desa' => 'Ampel',
                'kecamatan' => 'Ampel',
                'ayah' => 'Nur Hidayat',
                'ibu' => 'Kustini',
            ],
            [
                'reg_no' => '276012',
                'name' => 'INTAN PERMATASARI',
                'username' => '082155667705',
                'password' => 'intanpermata',
                'gender' => 'P',
                'school' => 'SMP NEGERI 1 TERAS',
                'phone' => '082155667705',
                'major_code' => 'PSY',
                'status' => 'pending',
                'desa' => 'Teras',
                'kecamatan' => 'Teras',
                'ayah' => 'Joko Permata',
                'ibu' => 'Hartini',
            ],
        ];

        $academicYear = AcademicYear::where('is_active', true)->first();

        foreach ($studentsData as $st) {
            $email = $st['username'] . '@siswa.smkknbi.sch.id';
            
            $siswa = Siswa::firstOrCreate(
                ['phone' => $st['phone']],
                [
                    'name' => $st['name'],
                    'username' => $st['username'],
                    'nisn' => strlen($st['username']) >= 10 ? $st['username'] : '00' . rand(10000000, 99999999),
                    'phone' => $st['phone'],
                    'email' => $email,
                    'password' => Hash::make($st['password']),
                    'raw_password' => $st['password'],
                    'role' => 'murid',
                    'is_active' => true,
                ]
            );

            $major = Major::where('code', $st['major_code'])->first() ?? Major::first();

            if ($academicYear && $major) {
                $registration = Registration::firstOrCreate(
                    ['registration_number' => $st['reg_no']],
                    [
                        'user_id' => $siswa->id,
                        'academic_year_id' => $academicYear->id,
                        'major_id' => $major->id,
                        'registration_type' => 'siswa_baru',
                        'recommendation_by' => 'Alumni SMK KNBI',
                        'status' => $st['status'],
                    ]
                );

                StudentBiodata::firstOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'nama_lengkap' => $st['name'],
                        'nik' => '3309' . rand(100000000000, 999999999999),
                        'no_kk' => '3309' . rand(100000000000, 999999999999),
                        'jenis_kelamin' => $st['gender'],
                        'agama' => 'Islam',
                        'tempat_lahir' => 'Boyolali',
                        'tanggal_lahir' => '2009-' . rand(1, 12) . '-' . rand(1, 28),
                        'no_hp' => $st['phone'],
                        'email' => $email,
                        'alamat_jalan' => 'Jl. Merbabu No. ' . rand(1, 100),
                        'desa' => $st['desa'],
                        'kecamatan' => $st['kecamatan'],
                        'kabupaten' => 'Boyolali',
                        'provinsi' => 'Jawa Tengah',
                        'kode_pos' => '57311',
                        'anak_ke' => rand(1, 3),
                        'jumlah_saudara' => rand(1, 4),
                        'tinggi_badan' => rand(155, 175),
                        'berat_badan' => rand(45, 65),
                    ]
                );

                StudentParent::firstOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'nama_ayah' => $st['ayah'],
                        'status_ayah' => 'hidup',
                        'pekerjaan_ayah' => 'Wiraswasta',
                        'pendidikan_ayah' => 'SMA/SMK',
                        'penghasilan_ayah' => '2.500.000 - 5.000.000',
                        'no_hp_ayah' => $st['phone'],
                        'nama_ibu' => $st['ibu'],
                        'status_ibu' => 'hidup',
                        'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                        'pendidikan_ibu' => 'SMA/SMK',
                        'penghasilan_ibu' => 'Tidak Berpenghasilan',
                        'no_hp_ibu' => $st['phone'],
                    ]
                );

                StudentSchool::firstOrCreate(
                    ['registration_id' => $registration->id],
                    [
                        'asal_sekolah' => $st['school'],
                        'npsn_sekolah' => '2030' . rand(1000, 9999),
                        'jenis_sekolah' => str_starts_with($st['school'], 'MTS') ? 'MTs' : 'SMP',
                        'status_sekolah' => 'Negeri',
                        'kabupaten_sekolah' => 'Boyolali',
                        'tahun_lulus' => 2026,
                        'nilai_rata_raport' => 84.50,
                    ]
                );
            }
        }
    }
}
