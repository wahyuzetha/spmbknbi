<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Registration;
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
        User::updateOrCreate(
            ['email' => 'admin@smkknbi.sch.id'],
            [
                'name' => 'Administrator SPMB',
                'phone' => '081234567890',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 2. Akun Panitia Verifikator
        User::updateOrCreate(
            ['email' => 'panitia@smkknbi.sch.id'],
            [
                'name' => 'Panitia Verifikasi SPMB',
                'phone' => '081234567891',
                'role' => 'panitia',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // 3. Akun Murid (Calon Siswa Demo)
        $murid = User::updateOrCreate(
            ['email' => 'calonsiswa@gmail.com'],
            [
                'name' => 'Budi Santoso',
                'phone' => '085861512412',
                'nisn' => '0081234567',
                'role' => 'murid',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // Buat Data Pendaftaran Demo untuk Murid
        $academicYear = AcademicYear::where('is_active', true)->first();
        $major1 = Major::where('code', 'TKJ-AXIOO')->first();
        $major2 = Major::where('code', 'TKR')->first();

        if ($academicYear && $major1) {
            $registration = Registration::updateOrCreate(
                ['user_id' => $murid->id],
                [
                    'registration_number' => 'SPMB-2027-0001',
                    'academic_year_id' => $academicYear->id,
                    'major_id' => $major1->id,
                    'major_choice_2_id' => $major2?->id,
                    'registration_type' => 'siswa_baru',
                    'recommendation_by' => 'Alumni SMK KNBI',
                    'status' => 'pending',
                ]
            );

            // Biodata Pribadi Siswa
            StudentBiodata::updateOrCreate(
                ['registration_id' => $registration->id],
                [
                    'nik' => '3309012304080001',
                    'no_kk' => '3309012304080002',
                    'nama_lengkap' => 'Budi Santoso',
                    'jenis_kelamin' => 'L',
                    'agama' => 'Islam',
                    'tempat_lahir' => 'Boyolali',
                    'tanggal_lahir' => '2008-05-14',
                    'no_hp' => '085861512412',
                    'email' => 'calonsiswa@gmail.com',
                    'alamat_jalan' => 'RT 03 / RW 02, Dusun Mawar',
                    'desa' => 'Siswodipuran',
                    'kecamatan' => 'Boyolali',
                    'kabupaten' => 'Boyolali',
                    'provinsi' => 'Jawa Tengah',
                    'kode_pos' => '57311',
                    'anak_ke' => 1,
                    'jumlah_saudara' => 2,
                    'tinggi_badan' => 165,
                    'berat_badan' => 55,
                ]
            );

            // Data Orang Tua
            StudentParent::updateOrCreate(
                ['registration_id' => $registration->id],
                [
                    'nama_ayah' => 'Slamet Riyadi',
                    'status_ayah' => 'hidup',
                    'pekerjaan_ayah' => 'Wiraswasta',
                    'pendidikan_ayah' => 'SMA/SMK',
                    'penghasilan_ayah' => '3.000.000 - 5.000.000',
                    'no_hp_ayah' => '081234567899',
                    'nama_ibu' => 'Siti Aminah',
                    'status_ibu' => 'hidup',
                    'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                    'pendidikan_ibu' => 'SMA/SMK',
                    'penghasilan_ibu' => 'Tidak Berpenghasilan',
                    'no_hp_ibu' => '081234567898',
                ]
            );

            // Data Asal Sekolah
            StudentSchool::updateOrCreate(
                ['registration_id' => $registration->id],
                [
                    'asal_sekolah' => 'SMP Negeri 1 Boyolali',
                    'npsn_sekolah' => '20307123',
                    'jenis_sekolah' => 'SMP',
                    'status_sekolah' => 'Negeri',
                    'kabupaten_sekolah' => 'Boyolali',
                    'tahun_lulus' => 2026,
                    'nilai_rata_raport' => 85.50,
                ]
            );
        }
    }
}
