<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\StudentBiodata;
use App\Models\StudentSchool;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class SpmbSettingsFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_admin_dapat_mengakses_menu_pengaturan_spmb_dan_melihat_seluruh_fiturnya()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get(route('admin.dashboard', ['view' => 'pengaturan']));

        $response->assertStatus(200);
        $response->assertSee('Pengaturan SPMB', false);
        $response->assertSee('Pergantian & Pengaturan Tahun Ajaran SPMB', false);
        $response->assertSee('Logo Sekolah & Identitas Lembaga', false);
        $response->assertSee('Nomor Hotline Panitia SPMB', false);
        $response->assertSee('Tanda Tangan (TTD) Ketua Panitia', false);
        $response->assertSee('Format Kop Surat Resmi & Simulasi Cetak A4', false);
    }

    public function test_admin_dapat_memperbarui_pengaturan_teks_spmb_hotline_ttd_dan_kop_surat()
    {
        $admin = User::where('role', 'admin')->first();

        $payload = [
            'school_name' => 'SMK KARYA NUGRAHA BOYOLALI (UNGGULAN)',
            'school_npsn' => '20308458',
            'hotline_panitia' => '089999888777',
            'hotline_panitia_name' => 'Call Center SPMB KNBI',
            'hotline_panitia_hours' => '24 Jam Nonstop',
            'chairman_name' => 'Dr. H. Sulistyo Raharjo, M.Pd.',
            'chairman_nip' => '19800101 200801 1 005',
            'cop_school_name' => 'YAYASAN PENDIDIKAN DAN KEJURUAN KARYA NUGRAHA',
            'cop_school_status' => 'Terakreditasi A Plus',
            'cop_school_address' => 'Jl. Pandanaran Boyolali No. 99',
            'cop_school_contact' => 'Telp: 0276-321123 • WA: 089999888777',
        ];

        $response = $this->actingAs($admin)->post(route('admin.settings.spmb.update'), $payload);

        $response->assertRedirect(route('admin.dashboard', ['view' => 'pengaturan']));
        $response->assertSessionHas('success');

        $this->assertEquals('SMK KARYA NUGRAHA BOYOLALI (UNGGULAN)', SystemSetting::get('school_name'));
        $this->assertEquals('089999888777', SystemSetting::get('hotline_panitia'));
        $this->assertEquals('Dr. H. Sulistyo Raharjo, M.Pd.', SystemSetting::get('chairman_name'));
        $this->assertEquals('YAYASAN PENDIDIKAN DAN KEJURUAN KARYA NUGRAHA', SystemSetting::get('cop_school_name'));
    }

    public function test_admin_dapat_mengunggah_file_logo_sekolah_ttd_ketua_dan_banner_kop_surat()
    {
        $admin = User::where('role', 'admin')->first();

        $logo = UploadedFile::fake()->image('test_logo.png', 200, 200);
        $ttd = UploadedFile::fake()->image('test_ttd.png', 150, 100);
        $kop = UploadedFile::fake()->image('test_kop.jpg', 800, 150);

        $response = $this->actingAs($admin)->post(route('admin.settings.spmb.update'), [
            'school_name' => 'SMK KNBI Upload Test',
            'school_logo' => $logo,
            'chairman_signature' => $ttd,
            'cop_image_banner' => $kop,
        ]);

        $response->assertRedirect(route('admin.dashboard', ['view' => 'pengaturan']));
        $response->assertSessionHas('success');

        $savedLogo = SystemSetting::get('school_logo');
        $savedTtd = SystemSetting::get('chairman_signature');
        $savedKop = SystemSetting::get('cop_image_banner');

        $this->assertNotNull($savedLogo);
        $this->assertNotNull($savedTtd);
        $this->assertNotNull($savedKop);

        $this->assertTrue(File::exists(public_path($savedLogo)));
        $this->assertTrue(File::exists(public_path($savedTtd)));
        $this->assertTrue(File::exists(public_path($savedKop)));

        // Cleanup
        File::delete(public_path($savedLogo));
        File::delete(public_path($savedTtd));
        File::delete(public_path($savedKop));
    }

    public function test_admin_dapat_menambah_tahun_ajaran_baru_dan_mengaktifkannya()
    {
        $admin = User::where('role', 'admin')->first();
        $uniqueYear = '2029/2030';
        AcademicYear::where('name', $uniqueYear)->delete();

        $activeCurrent = AcademicYear::where('is_active', true)->first();

        $response = $this->actingAs($admin)->post(route('admin.academicYears.store'), [
            'name' => $uniqueYear,
            'start_date' => '2029-01-01',
            'end_date' => '2029-12-31',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.dashboard', ['view' => 'pengaturan']));
        $response->assertSessionHas('success');

        $newYear = AcademicYear::where('name', $uniqueYear)->first();
        $this->assertNotNull($newYear);
        $this->assertTrue($newYear->is_active);

        // Tahun ajaran sebelumnya otomatis dinonaktifkan
        if ($activeCurrent) {
            $this->assertFalse((bool)$activeCurrent->fresh()->is_active);

            // Coba aktifkan kembali tahun ajaran lama
            $switchResponse = $this->actingAs($admin)->post(route('admin.academicYears.activate', $activeCurrent->id));
            $switchResponse->assertRedirect(route('admin.dashboard', ['view' => 'pengaturan']));

            $this->assertTrue((bool)$activeCurrent->fresh()->is_active);
            $this->assertFalse((bool)$newYear->fresh()->is_active);
        }

        // Hapus tahun ajaran baru karena belum ada pendaftar
        $delResponse = $this->actingAs($admin)->post(route('admin.academicYears.delete', $newYear->id));
        $delResponse->assertRedirect(route('admin.dashboard', ['view' => 'pengaturan']));
        $this->assertFalse(AcademicYear::where('id', $newYear->id)->exists());
    }

    public function test_halaman_cetak_rekapitulasi_dan_cetak_kartu_menampilkan_kop_surat_dan_ttd_ketua_yang_tersinkronisasi()
    {
        $admin = User::where('role', 'admin')->first();
        $major = Major::first();
        $academicYear = AcademicYear::where('is_active', true)->first();

        $siswa = Siswa::firstOrCreate(
            ['phone' => '087711223344'],
            [
                'name' => 'Siswa Cetak Test',
                'username' => 'siswacetak',
                'email' => 'siswacetak@siswa.smkknbi.sch.id',
                'password' => bcrypt('12345'),
                'role' => 'murid',
                'is_active' => true,
            ]
        );

        $reg = Registration::firstOrCreate(
            ['registration_number' => 'REG-TEST-PRINT-01'],
            [
                'user_id' => $siswa->id,
                'academic_year_id' => $academicYear->id,
                'major_id' => $major->id,
                'registration_type' => 'siswa_baru',
                'status' => 'verified',
            ]
        );

        StudentBiodata::firstOrCreate(
            ['registration_id' => $reg->id],
            ['nama_lengkap' => 'Siswa Cetak Test', 'jenis_kelamin' => 'L']
        );

        StudentSchool::firstOrCreate(
            ['registration_id' => $reg->id],
            ['asal_sekolah' => 'SMP TEST NEGERI']
        );

        // Set data kop surat dan ttd
        SystemSetting::set('school_name', 'SMK KARYA NUGRAHA BOYOLALI TEST');
        SystemSetting::set('chairman_name', 'H. Bambang Wahyudi, S.Pd., M.Kom.');
        SystemSetting::set('hotline_panitia', '085876098129');

        // 1. Cek cetak rekapitulasi
        $rekapResponse = $this->actingAs($admin)->get(route('admin.spmb.printAll'));
        $rekapResponse->assertStatus(200);
        $rekapResponse->assertSee('SMK KARYA NUGRAHA BOYOLALI TEST');
        $rekapResponse->assertSee('H. Bambang Wahyudi, S.Pd., M.Kom.');
        $rekapResponse->assertSee('085876098129');

        // 2. Cek cetak kartu peserta
        $cardResponse = $this->actingAs($admin)->get(route('admin.spmb.printCard', $reg->id));
        $cardResponse->assertStatus(200);
        $cardResponse->assertSee('SMK KARYA NUGRAHA BOYOLALI TEST');
        $cardResponse->assertSee('H. Bambang Wahyudi, S.Pd., M.Kom.');
        $cardResponse->assertSee('085876098129');
    }

    public function test_logo_sekolah_tersinkronisasi_secara_global_pada_seluruh_antarmuka_dan_role()
    {
        $admin = User::where('role', 'admin')->first();
        $panitia = User::where('role', 'panitia')->first();
        $murid = Siswa::where('role', 'murid')->first();

        // 1. Upload logo baru
        $fakeLogo = UploadedFile::fake()->image('custom_school_logo.png', 200, 200);
        $res = $this->actingAs($admin)->post(route('admin.settings.spmb.update'), [
            'school_name' => 'SMK KNBI GLOBAL LOGO TEST',
            'school_logo' => $fakeLogo,
        ]);
        $res->assertSessionHasNoErrors();

        $logoPath = SystemSetting::get('school_logo');
        $this->assertNotNull($logoPath);

        // 2. Admin Dashboard merender logo baru
        $adminDash = $this->actingAs($admin)->get(route('admin.dashboard'));
        $adminDash->assertStatus(200);
        $adminDash->assertSee(asset($logoPath), false);

        // 3. Panitia Dashboard merender logo baru
        $panitiaDash = $this->actingAs($panitia)->get(route('panitia.dashboard'));
        $panitiaDash->assertStatus(200);
        $panitiaDash->assertSee(asset($logoPath), false);

        // 4. Murid Dashboard merender logo baru
        $muridDash = $this->actingAs($murid, 'web')->get(route('murid.dashboard'));
        $muridDash->assertStatus(200);
        $muridDash->assertSee(asset($logoPath), false);

        // 5. Logout untuk menguji Halaman Publik / Guest
        auth()->logout();

        // 6. Landing Page merender logo baru
        $landingRes = $this->get(route('home'));
        $landingRes->assertStatus(200);
        $landingRes->assertSee(asset($logoPath), false);

        // 7. Login Page merender logo baru
        $loginRes = $this->get(route('login'));
        $loginRes->assertStatus(200);
        $loginRes->assertSee(asset($logoPath), false);

        // 8. Register / Daftar Page merender logo baru
        $regRes = $this->get(route('daftar'));
        $regRes->assertStatus(200);
        $regRes->assertSee(asset($logoPath), false);

        // Cleanup
        if (File::exists(public_path($logoPath))) {
            File::delete(public_path($logoPath));
        }
    }

    public function test_kop_surat_hanya_berupa_gambar_dan_tidak_ada_input_teks_format_surat()
    {
        $admin = User::where('role', 'admin')->first();

        // 1. Cek halaman pengaturan tidak lagi memiliki input form teks format kop surat
        $settingView = $this->actingAs($admin)->get(route('admin.dashboard', ['view' => 'pengaturan']));
        $settingView->assertStatus(200);
        $settingView->assertDontSee('name="cop_school_name"', false);
        $settingView->assertDontSee('name="cop_school_status"', false);
        $settingView->assertDontSee('name="cop_school_address"', false);
        $settingView->assertDontSee('name="cop_school_contact"', false);
        $settingView->assertSee('name="cop_image_banner"', false);

        // 2. Upload banner gambar kop surat
        $kopBanner = UploadedFile::fake()->image('custom_kop_banner.jpg', 1000, 200);
        $this->actingAs($admin)->post(route('admin.settings.spmb.update'), [
            'school_name' => 'SMK KNBI KOP TEST',
            'cop_image_banner' => $kopBanner,
        ]);

        $kopPath = SystemSetting::get('cop_image_banner');
        $this->assertNotNull($kopPath);

        // 3. Rekap cetak merender banner gambar kop surat
        $printAll = $this->actingAs($admin)->get(route('admin.spmb.printAll'));
        $printAll->assertStatus(200);
        $printAll->assertSee(asset($kopPath), false);

        // Cleanup
        if (File::exists(public_path($kopPath))) {
            File::delete(public_path($kopPath));
        }
    }

    public function test_bukti_pendaftaran_calon_siswa_dalam_format_f4_dua_halaman_dengan_kop_surat_admin()
    {
        $admin = User::where('role', 'admin')->first();
        $murid = Siswa::where('role', 'murid')->first();
        $registration = Registration::where('user_id', $murid->id)->first();

        // 1. Upload Banner Kop Surat di Admin Settings
        $kopBanner = UploadedFile::fake()->image('kop_f4_test.jpg', 1200, 250);
        $this->actingAs($admin)->post(route('admin.settings.spmb.update'), [
            'school_name' => 'SMK KARYA NUGRAHA BOYOLALI',
            'chairman_name' => 'Suparman, ST',
            'cop_image_banner' => $kopBanner,
        ]);

        $kopPath = SystemSetting::get('cop_image_banner');
        $this->assertNotNull($kopPath);

        // 2. Akses Bukti Pendaftaran oleh Admin
        $resAdmin = $this->actingAs($admin)->get(route('admin.spmb.printCard', $registration->id));
        $resAdmin->assertStatus(200);
        
        // Halaman 1: Ukuran Kertas F4, Judul Dokumen, Data Siswa
        $resAdmin->assertSee('215mm 330mm', false); // Standar Kertas F4 / Folio
        $resAdmin->assertSee('Plus Jakarta Sans', false);
        $resAdmin->assertSee('BUKTI PENDAFTARAN CALON', false);
        $resAdmin->assertSee('SISWA BARU SMK KARYA', false);
        $resAdmin->assertSee('Nomor Peserta :', false);
        $resAdmin->assertSee($registration->registration_number, false);
        $resAdmin->assertSee('Kompetensi Keahlian :', false);
        $resAdmin->assertSee('Sekolah Asal(SMP/MTs)', false);
        $resAdmin->assertSee('Nama Orang Tua', false);
        $resAdmin->assertSee('Pekerjaan Orang Tua/Wali', false);
        $resAdmin->assertSee('Informasi/ Rekomendasi', false);
        $resAdmin->assertSee('Suparman, ST', false);
        $resAdmin->assertSee(asset($kopPath), false);

        // Halaman 2: Surat Pemberitahuan Diterima
        $resAdmin->assertSee('page-break', false);
        $resAdmin->assertSee('Hal', false);
        $resAdmin->assertSee('Pemberitahuan', false);
        $resAdmin->assertSee('Kepada Yth :', false);
        $resAdmin->assertSee('Assalamualaikum Wr.Wb', false);
        $resAdmin->assertSee('Berdasarkan Hasil Seleksi Dari PMDK SMK KARYA NUGRAHA BOYOLALI', false);
        $resAdmin->assertSee('DITERIMA', false);
        $resAdmin->assertSee('Panitia PMDK/SPMB,', false);

        // 3. Akses Bukti Pendaftaran oleh Murid dari Dashboardnya
        $resMurid = $this->actingAs($murid, 'web')->get(route('murid.print.card'));
        $resMurid->assertStatus(200);
        $resMurid->assertSee('BUKTI PENDAFTARAN CALON', false);
        $resMurid->assertSee('DITERIMA', false);
        $resMurid->assertSee(asset($kopPath), false);

        // Cleanup
        if (File::exists(public_path($kopPath))) {
            File::delete(public_path($kopPath));
        }
    }
}
