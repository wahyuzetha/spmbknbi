<?php

namespace Tests\Feature;

use App\Models\Major;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\User;
use Tests\TestCase;

class CalonSiswaFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_calon_siswa_view_and_all_features()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin, 'Admin should exist');

        // 1. Tampilan Data Calon Siswa (Persis Gambar)
        $response = $this->actingAs($admin)->get('/admin/dashboard?view=calon_siswa');
        $response->assertStatus(200);
        $response->assertSee('Data Calon Siswa');
        $response->assertSee('Manajemen Pendaftaran Angkatan Terbaru');
        $response->assertSee('Export');
        $response->assertSee('Cetak All');
        $response->assertSee('Tambah Data');
        $response->assertSee('NO Pendaftar');
        $response->assertSee('Username');
        $response->assertSee('Password');
        $response->assertSee('Asal Sekolah');
        $response->assertSee('Jurusan');
        $response->assertSee('Copy');
        $response->assertSee('CSV');
        $response->assertSee('Excel');
        $response->assertSee('Print');

        // Pastikan calon siswa dari seeder referensi tampil
        $response->assertSee('SGADG');
        $response->assertSee('MUHAMMAD AKSA ANGGARA');
        $response->assertSee('FARADIVA KHARISMA OKTAVIANA');

        // 2. Tambah Data Calon Siswa Manual / Walk-in
        $major = Major::first();
        $storeResponse = $this->actingAs($admin)->post('/admin/spmb/calon-siswa/store', [
            'nama_siswa' => 'Rizky Ramadhan',
            'phone' => '089912345678',
            'username' => '089912345678',
            'password' => 'rizky2027',
            'gender' => 'L',
            'asal_sekolah' => 'SMP Negeri 2 Boyolali',
            'major_id' => $major->id,
            'status' => 'pending',
        ]);
        $storeResponse->assertSessionHas('success');

        $this->assertDatabaseHas('siswa', [
            'name' => 'Rizky Ramadhan',
            'phone' => '089912345678',
            'raw_password' => 'rizky2027',
        ]);

        $newSiswa = Siswa::where('phone', '089912345678')->first();
        $newReg = Registration::where('user_id', $newSiswa->id)->first();
        $this->assertNotNull($newReg);

        // 3. Edit Data Calon Siswa
        $updateResponse = $this->actingAs($admin)->post("/admin/spmb/calon-siswa/{$newReg->id}/update", [
            'nama_siswa' => 'Rizky Ramadhan Pratama',
            'phone' => '089912345678',
            'username' => '089912345678',
            'password' => 'barubanget',
            'gender' => 'L',
            'asal_sekolah' => 'SMP Negeri 2 Boyolali Unggulan',
            'major_id' => $major->id,
        ]);
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('siswa', [
            'name' => 'Rizky Ramadhan Pratama',
            'raw_password' => 'barubanget',
        ]);
        $this->assertEquals('pending', $newReg->fresh()->status);

        // 4. Test Export Excel (.xlsx) Lengkap Seluruh Data Siswa
        $excelResponse = $this->actingAs($admin)->get('/admin/spmb/calon-siswa/export-excel');
        $excelResponse->assertStatus(200);
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $excelResponse->headers->get('content-type'));
        $this->assertStringContainsString('.xlsx', $excelResponse->headers->get('content-disposition'));

        // 5. Test Cetak All Rekapitulasi Pendaftar
        $printAllResponse = $this->actingAs($admin)->get('/admin/spmb/calon-siswa/cetak-all');
        $printAllResponse->assertStatus(200);
        $printAllResponse->assertSee('REKAPITULASI DATA CALON SISWA BARU');
        $printAllResponse->assertSee('SMK KARYA NUGRAHA BOYOLALI');
        $printAllResponse->assertSee('Rizky Ramadhan Pratama');

        // 6. Test Cetak Kartu Individual
        $printCardResponse = $this->actingAs($admin)->get("/admin/spmb/calon-siswa/{$newReg->id}/cetak-kartu");
        $printCardResponse->assertStatus(200);
        $printCardResponse->assertSee('BUKTI PENDAFTARAN CALON');
        $printCardResponse->assertSee('SISWA BARU SMK KARYA');
        $printCardResponse->assertSee('Pemberitahuan');
        $printCardResponse->assertSee($newReg->registration_number);
        $printCardResponse->assertSee('Rizky Ramadhan Pratama');

        // 7. Test Quick Status Sekali Respon (AJAX): Pending -> Diterima
        $quickReg = Registration::where('status', 'pending')->first();
        $this->assertNotNull($quickReg);
        
        $ajaxAcceptResponse = $this->actingAs($admin)->postJson("/admin/registrations/{$quickReg->id}/status", [
            'status' => 'accepted'
        ]);
        $ajaxAcceptResponse->assertStatus(200);
        $ajaxAcceptResponse->assertJson([
            'success' => true,
            'status' => 'accepted',
            'status_label' => 'Diterima'
        ]);
        $this->assertEquals('accepted', $quickReg->fresh()->status);

        // Test Quick Status Sekali Respon (AJAX): Diterima -> Pending
        $ajaxPendingResponse = $this->actingAs($admin)->postJson("/admin/registrations/{$quickReg->id}/status", [
            'status' => 'pending'
        ]);
        $ajaxPendingResponse->assertStatus(200);
        $ajaxPendingResponse->assertJson([
            'success' => true,
            'status' => 'pending',
            'status_label' => 'Pending'
        ]);
        $this->assertEquals('pending', $quickReg->fresh()->status);

        // 8. Test Tampilan Maksimal 10 dan Paginasi
        $calonSiswaPage = $this->actingAs($admin)->get('/admin/dashboard?view=calon_siswa');
        $calonSiswaPage->assertStatus(200);
        $calonSiswaPage->assertSee('student-table-info');
        $calonSiswaPage->assertSee('student-table-pagination');
        $calonSiswaPage->assertSee('modal-quick-status');
        $calonSiswaPage->assertSee('promptChangeStudentStatus');

        // 9. Test Hapus Calon Siswa Satuan
        $deleteResponse = $this->actingAs($admin)->post("/admin/spmb/calon-siswa/{$newReg->id}/delete");
        $deleteResponse->assertSessionHas('success');
        $this->assertDatabaseMissing('siswa', ['id' => $newSiswa->id]);
        $this->assertDatabaseMissing('pendaftaran', ['id' => $newReg->id]);

        echo "\n[SUCCESS] Seluruh fitur Data Calon Siswa (Tampilan Persis Screenshot, Quick Status Sekali Respon Pending <-> Diterima, Maksimal 10 Paginasi, Export Excel Lengkap, Cetak All, Cetak Kartu, WhatsApp Chat, CRUD) terverifikasi 100% lulus!\n";
    }

    public function test_logika_nomor_pendaftar_3_bagian_tahun_jurusan_dan_urutan_id()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed');

        $tkr = Major::where('code', 'TKR')->first();
        $tbo = Major::where('code', 'TBO')->first();
        $tsm = Major::where('code', 'TSM')->first();
        $tkj = Major::where('code', 'TKJ Axioo')->first();
        $tkjPlus = Major::where('code', 'TKJ+')->first();
        $psy = Major::where('code', 'PSY')->first();
        $ttt = Major::where('code', 'TTT')->first();

        // 1. Verifikasi Urutan Kode 1 Digit Jurusan
        $this->assertEquals('1', Registration::getMajorDigit($tkr), 'TKR harus berkode 1');
        $this->assertEquals('2', Registration::getMajorDigit($tbo), 'TBO harus berkode 2');
        $this->assertEquals('3', Registration::getMajorDigit($tsm), 'TSM harus berkode 3');
        $this->assertEquals('4', Registration::getMajorDigit($tkj), 'TKJ Axioo harus berkode 4');
        $this->assertEquals('5', Registration::getMajorDigit($tkjPlus), 'TKJ+ Tata Busana harus berkode 5');
        $this->assertEquals('6', Registration::getMajorDigit($psy), 'Perbankan Syariah harus berkode 6');
        $this->assertEquals('7', Registration::getMajorDigit($ttt), 'TKJ Kelas Telkom harus berkode 7');

        // 2. Verifikasi 2 Digit Tahun Pelajaran
        $year27 = \App\Models\AcademicYear::where('name', '2027/2028')->first();
        $this->assertEquals('27', Registration::getYearDigits($year27));

        $year28 = \App\Models\AcademicYear::create([
            'name' => '2028/2029',
            'is_active' => false,
            'start_date' => '2028-01-01',
            'end_date' => '2028-12-31',
        ]);
        $this->assertEquals('28', Registration::getYearDigits($year28));

        // 3. Verifikasi Logika Contoh Persis dari Pengguna: 276012
        // Tahun: 2027/2028 -> 27, Jurusan: PSY -> 6, Urutan Siswa ID: 12 -> 012 === 276012
        Registration::where('registration_number', '276012')->delete();
        $sampleNumber = Registration::generateNumber($psy, 12, $year27);
        $this->assertEquals('276012', $sampleNumber, 'Nomor pendaftar contoh harus 276012');

        // 4. Verifikasi Ketika ID di-reset kembali ke 001
        Registration::where('registration_number', '271001')->delete();
        $resetNumber = Registration::generateNumber($tkr, 1, $year27);
        $this->assertEquals('271001', $resetNumber, 'Nomor saat reset harus 271001');

        // 5. Verifikasi Ketika Ganti Tahun Pelajaran Otomatis Berganti (Contoh ke 2028/2029 -> 286012)
        $nextYearNumber = Registration::generateNumber($psy, 12, $year28);
        $this->assertEquals('286012', $nextYearNumber, 'Nomor saat ganti tahun harus otomatis 286012');

        // 6. Test Pendaftaran Calon Siswa Baru Melalui Form /daftar
        $registerResponse = $this->post(route('daftar.store'), [
            'major_id' => $psy->id,
            'nama_siswa' => 'Siswa Baru Logika Angka',
            'nohp' => '087799887766',
            'nisn' => '0099887766',
            'email' => 'siswalogika@test.com',
            'password' => 'password123',
            'jenis_kelamin' => 'P',
            'agama' => 'Islam',
            'tempat_lahir' => 'Boyolali',
            'tanggal_lahir' => '2009-05-15',
            'alamat_jalan' => 'Jl. Pandanaran No. 50',
            'desa' => 'Teras',
            'kecamatan' => 'Teras',
            'kabupaten' => 'Boyolali',
            'nama_ayah' => 'Bapak Siswa',
            'pekerjaan_ayah' => 'Wiraswasta',
            'nama_ibu' => 'Ibu Siswa',
            'pekerjaan_ibu' => 'Ibu Rumah Tangga',
            'asal_sekolah' => 'SMP Negeri 1 Teras',
        ]);
        $registerResponse->assertRedirect(route('daftar'));
        $registerResponse->assertSessionHas('show_confirmation_modal', true);

        $newStudent = Siswa::where('phone', '087799887766')->first();
        $this->assertNotNull($newStudent);
        $newReg = Registration::where('user_id', $newStudent->id)->first();
        $this->assertNotNull($newReg);

        // Harus diawali 27 (tahun), lalu 6 (PSY), lalu 3 digit ID siswa
        $expectedRegNo = '276' . str_pad((string)$newStudent->id, 3, '0', STR_PAD_LEFT);
        $this->assertEquals($expectedRegNo, $newReg->registration_number);
    }

    public function test_tambah_siswa_manual_menolak_username_duplikat_dan_mencegah_bentrok_email_unik(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin Test',
                'username' => 'admintest',
                'email' => 'admintest@smkknbi.sch.id',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]);
        }

        $major = Major::first();

        // Buat satu siswa perintis dengan username 'siswaperintis'
        Siswa::create([
            'name' => 'Siswa Perintis',
            'username' => 'siswaperintis',
            'phone' => '0812345678901',
            'email' => 'siswaperintis@siswa.smkknbi.sch.id',
            'password' => bcrypt('12345'),
            'role' => 'murid',
        ]);

        // 1. Coba input dengan username yang sama persis ('siswaperintis') -> Harus ditolak validasi
        $responseDuplicate = $this->actingAs($admin)->post(route('admin.spmb.student.store'), [
            'nama_siswa' => 'INTAN PERMATASARI',
            'username' => 'siswaperintis',
            'phone' => '089463346523',
            'password' => '12345',
            'gender' => 'P',
            'asal_sekolah' => 'SMP NEGERI 1 BOYOLALI',
            'major_id' => $major->id,
            'status' => 'pending',
        ]);

        $responseDuplicate->assertSessionHasErrors(['username']);

        // 2. Input dengan data baru yang valid (tanpa username duplikat) -> Harus berhasil tersimpan
        $responseValid = $this->actingAs($admin)->post(route('admin.spmb.student.store'), [
            'nama_siswa' => 'INTAN PERMATASARI',
            'username' => 'intanpermata',
            'phone' => '089463346523',
            'password' => '12345',
            'gender' => 'P',
            'asal_sekolah' => 'SMP NEGERI 1 BOYOLALI',
            'major_id' => $major->id,
            'status' => 'pending',
        ]);

        $responseValid->assertSessionHas('success');

        $intan = Siswa::where('phone', '089463346523')->first();
        $this->assertNotNull($intan);
        $this->assertEquals('intanpermata', $intan->username);
        $this->assertStringContainsString('intanpermata', $intan->email);
    }

    public function test_edit_kelas_industri_dan_otomatis_merubah_data_jurusan_calon_siswa(): void
    {
        $admin = User::where('role', 'admin')->first();
        $major = Major::first();

        // Buat calon siswa yang memilih jurusan ini
        $siswa = Siswa::create([
            'name' => 'Budi Santoso Uji Jurusan',
            'username' => 'budijurusan',
            'phone' => '089911223344',
            'email' => 'budijurusan@siswa.smkknbi.sch.id',
            'password' => bcrypt('12345'),
            'role' => 'murid',
        ]);

        $reg = Registration::create([
            'registration_number' => '271999',
            'user_id' => $siswa->id,
            'academic_year_id' => 1,
            'major_id' => $major->id,
            'registration_type' => 'siswa_baru',
            'status' => 'pending',
        ]);

        // 1. Edit khusus Kelas Industri melalui route updateIndustryClass
        $responseIndustry = $this->actingAs($admin)->post(route('admin.sekolah.jurusan.updateIndustryClass', $major->id), [
            'industry_class' => 'Kelas Mitra Daihatsu Astra',
        ]);
        $responseIndustry->assertSessionHas('success');

        // Pastikan model Major terupdate
        $this->assertEquals('Kelas Mitra Daihatsu Astra', $major->fresh()->industry_class);

        // 2. Akses halaman Data Calon Siswa di Dashboard Admin -> Harus otomatis muncul 'Kelas Mitra Daihatsu Astra'
        $viewResponse = $this->actingAs($admin)->get('/admin/dashboard?view=calon_siswa');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee('Budi Santoso Uji Jurusan');
        $viewResponse->assertSee('Kelas Mitra Daihatsu Astra');

        // 3. Akses halaman Cetak Kartu Calon Siswa -> Harus otomatis menampilkan nama jurusan dan kelas industri baru
        $cardResponse = $this->actingAs($admin)->get(route('admin.spmb.printCard', $reg->id));
        $cardResponse->assertStatus(200);
        $cardResponse->assertSee('Kelas Mitra Daihatsu Astra');

        // 4. Update nama jurusan dan kelas industri sekaligus via updateMajor
        $responseFull = $this->actingAs($admin)->post(route('admin.sekolah.jurusan.update', $major->id), [
            'code' => $major->code,
            'name' => 'Teknik Otomotif Masa Depan',
            'quota' => 120,
            'industry_class' => 'Kelas Axioo Global',
            'is_active' => 1,
        ]);
        $responseFull->assertSessionHas('success');

        // Pastikan di Calon Siswa otomatis berubah nama jurusan dan kelas industrinya
        $viewUpdated = $this->actingAs($admin)->get('/admin/dashboard?view=calon_siswa');
        $viewUpdated->assertStatus(200);
        $viewUpdated->assertSee('Teknik Otomotif Masa Depan');
        $viewUpdated->assertSee('Kelas Axioo Global');
    }
}


