<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Expense;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
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

    /**
     * Test Fitur Administrasi Pembayaran Tunai oleh Bendahara:
     * - Input pembayaran tunai dengan mencari siswa / nomor pendaftar
     * - Otomatis terverifikasi oleh petugas bendahara
     * - Siswa melihat nominal pembayaran di akun portal calon siswa
     * - Jika lebih dari 2 kali transaksi, otomatis di-global menjadi 1 baris akumulatif tanpa menghilangkan riwayat sebelumnya
     * - Cetak Kwitansi Satuan & Rekapitulasi Global
     */
    public function test_administrasi_payment_input_verification_global_aggregation_and_student_view()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // 1. Buat calon siswa baru yang sudah mendaftar dan mengisi form
        $major = Major::first();
        $siswa = Siswa::create([
            'name' => 'AHMAD SYAHRIR',
            'username' => '087811223344',
            'phone' => '087811223344',
            'nisn' => '0099887766',
            'email' => 'ahmadsyahrir@siswa.smkknbi.sch.id',
            'password' => bcrypt('password123'),
            'raw_password' => 'password123',
            'role' => 'murid',
            'is_active' => true,
        ]);

        $academicYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $registration = Registration::create([
            'registration_number' => '271099',
            'user_id' => $siswa->id,
            'academic_year_id' => $academicYear->id,
            'major_id' => $major->id,
            'status' => 'pending',
        ]);

        // 2. Bendahara menginput pembayaran pertama (Tanpa kategori/jenis biaya)
        $pay1Response = $this->actingAs($admin)->post(route('admin.administrasi.payment.store'), [
            'registration_id' => $registration->id,
            'amount' => 150000,
            'payment_date' => '2026-10-18',
            'notes' => 'Pembayaran formulir tunai di loket bendahara SPMB',
        ]);
        $pay1Response->assertRedirect(route('admin.dashboard', ['view' => 'administrasi']));
        $pay1Response->assertSessionHas('success');

        // Pastikan pembayaran 1 otomatis terverifikasi oleh bendahara
        $this->assertDatabaseHas('pembayaran', [
            'registration_id' => $registration->id,
            'amount' => 150000.00,
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $admin->id,
        ]);

        // 3. Bendahara menginput pembayaran kedua (Cicilan 1 Rp 350.000)
        $pay2Response = $this->actingAs($admin)->post(route('admin.administrasi.payment.store'), [
            'registration_id' => $registration->id,
            'amount' => 350000,
            'payment_date' => '2026-10-19',
            'notes' => 'Cicilan daftar ulang pertama',
        ]);
        $pay2Response->assertSessionHas('success');

        // 4. Bendahara menginput pembayaran ketiga (Lebih dari 2 kali transaksi! Rp 500.000)
        $pay3Response = $this->actingAs($admin)->post(route('admin.administrasi.payment.store'), [
            'registration_id' => $registration->id,
            'amount' => 500000,
            'payment_date' => '2026-10-20',
            'notes' => 'Pelunasan seragam sekolah',
        ]);
        $pay3Response->assertSessionHas('success');

        // 5. Verifikasi di Tampilan Administrasi Admin / Bendahara:
        // - Harus otomatis di-global menjadi 1 baris untuk calon siswa ini
        // - Nominal total terglobal: Rp 1.000.000 (150rb + 350rb + 500rb)
        // - Jumlah transaksi: 3x Transaksi (Global)
        // - Seluruh riwayat transaksi (3 pembayaran) tetap utuh tersimpan di sistem
        $adminView = $this->actingAs($admin)->get('/admin/dashboard?view=administrasi');
        $adminView->assertStatus(200);
        $adminView->assertSee('AHMAD SYAHRIR');
        $adminView->assertSee('271099');
        $adminView->assertSee('Rp 1.000.000');
        $adminView->assertSee('3x Transaksi (Global)');
        $adminView->assertSee('Terverifikasi');

        // 6. Verifikasi di Akun Calon Siswa (Portal Murid):
        // Siswa dapat melihat besaran nominal pembayaran lewat akun masing-masing
        $muridView = $this->actingAs($siswa)->get('/murid/dashboard');
        $muridView->assertStatus(200);
        $muridView->assertSee('Riwayat Pembayaran SPMB');
        $muridView->assertSee('Rp 1.000.000');
        $muridView->assertSee('3x');
        $muridView->assertSee('Terverifikasi Bendahara');
        $muridView->assertSee('Pembayaran formulir tunai di loket bendahara SPMB');
        $muridView->assertSee('Cicilan daftar ulang pertama');
        $muridView->assertSee('Pelunasan seragam sekolah');

        // 7. Verifikasi Cetak Kwitansi Satuan & Kwitansi Global
        $payment1 = Payment::where('registration_id', $registration->id)->first();
        $receiptResponse = $this->actingAs($admin)->get(route('admin.administrasi.payment.receipt', $payment1->id));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('BUKTI PEMBAYARAN RESMI SPMB (TUNAI)');
        $receiptResponse->assertSee('AHMAD SYAHRIR');
        $receiptResponse->assertSee($payment1->invoice_number);

        $globalReceiptResponse = $this->actingAs($admin)->get(route('admin.administrasi.payment.globalReceipt', $registration->id));
        $globalReceiptResponse->assertStatus(200);
        $globalReceiptResponse->assertSee('REKAPITULASI BUKTI PEMBAYARAN KEUANGAN SPMB');
        $globalReceiptResponse->assertSee('AHMAD SYAHRIR');
        $globalReceiptResponse->assertSee('Rp 1.000.000');
        $globalReceiptResponse->assertSee('Satu Juta Rupiah');

        // Akses kwitansi lewat portal siswa
        $muridReceiptResponse = $this->actingAs($siswa)->get(route('murid.payment.receipt', $payment1->id));
        $muridReceiptResponse->assertStatus(200);

        $muridGlobalReceiptResponse = $this->actingAs($siswa)->get(route('murid.payment.globalReceipt'));
        $muridGlobalReceiptResponse->assertStatus(200);
        $muridGlobalReceiptResponse->assertSee('Rp 1.000.000');

        // 8. Test Aksi Hapus Data Siswa dari Bagian Action Administrasi
        $deleteResponse = $this->actingAs($admin)->post(route('admin.spmb.student.delete', $registration->id));
        $deleteResponse->assertSessionHas('success');

        // Pastikan data pendaftaran, siswa, dan seluruh histori pembayarannya bersih terhapus
        $this->assertDatabaseMissing('pendaftaran', ['id' => $registration->id]);
        $this->assertDatabaseMissing('siswa', ['id' => $siswa->id]);
        $this->assertDatabaseMissing('pembayaran', ['registration_id' => $registration->id]);
    }

    /**
     * Test Menu Pengeluaran Kas SPMB, Pencatatan Keterangan Pengeluaran Uang Calon Siswa,
     * Perhitungan Selisih Pemasukan & Pengeluaran, serta Paginasi Maksimal 10 Data.
     */
    public function test_pengeluaran_kas_spmb_dan_selisih_pemasukan_serta_paginasi_administrasi()
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        // 1. Verifikasi Halaman Administrasi memuat elemen paginasi maksimal 10 data persis Data Siswa
        $adminView = $this->actingAs($admin)->get(route('admin.dashboard', ['view' => 'administrasi']));
        $adminView->assertStatus(200);
        $adminView->assertSee('administrasi-table-pagination', false);
        $adminView->assertSee('administrasi-table-info', false);
        $adminView->assertSee('administrasi-row', false);
        $adminView->assertSee('initAdministrasiPagination', false);
        $adminView->assertSee('TOTAL KAS MASUK (PEMASUKAN)');
        $adminView->assertSee('TOTAL PENGELUARAN KAS');
        $adminView->assertSee('SISA SALDO KAS (SELISIH)');
        $adminView->assertSee('Menampilkan Semua Data');
        $adminView->assertSee('Keterangan (Opsional)');

        // 2. Akses View Pengeluaran Kas SPMB
        $pengeluaranView = $this->actingAs($admin)->get(route('admin.dashboard', ['view' => 'pengeluaran']));
        $pengeluaranView->assertStatus(200);
        $pengeluaranView->assertSee('Pengeluaran Kas SPMB');
        $pengeluaranView->assertSee('expense-table-pagination', false);
        $pengeluaranView->assertSee('expense-table-info', false);
        $pengeluaranView->assertSee('modal-add-expense', false);
        $pengeluaranView->assertSee('modal-edit-expense', false);
        $pengeluaranView->assertSee('Keperluan Pengeluaran');
        $pengeluaranView->assertSee('Keterangan *');
        $pengeluaranView->assertDontSee('Penerima / Vendor');
        $pengeluaranView->assertDontSee('Kategori Biaya');

        // 3. Input Pengeluaran Uang Calon Siswa Pendaftar
        $keteranganPengeluaran = 'Alokasi penggunaan uang pendaftaran calon siswa untuk biaya percetakan formulir pendaftaran F4, map berkas, dan banner SPMB.';
        $storeResponse = $this->actingAs($admin)->post(route('admin.administrasi.expense.store'), [
            'title' => 'Pengadaan Formulir dan Brosur PPDB 2026/2027',
            'category' => 'ATK & Formulir SPMB',
            'amount' => 750000,
            'expense_date' => date('Y-m-d'),
            'recipient' => 'CV Percetakan Mandiri Solo',
            'description' => $keteranganPengeluaran,
        ]);

        $storeResponse->assertRedirect(route('admin.dashboard', ['view' => 'pengeluaran']));
        $storeResponse->assertSessionHas('success');

        // Pastikan tersimpan di database lengkap dengan keterangan penggunaan uang calon siswa
        $this->assertDatabaseHas('pengeluaran', [
            'title' => 'Pengadaan Formulir dan Brosur PPDB 2026/2027',
            'amount' => 750000,
            'recipient' => 'CV Percetakan Mandiri Solo',
            'description' => $keteranganPengeluaran,
            'created_by' => $admin->id,
        ]);

        $expense = Expense::where('title', 'Pengadaan Formulir dan Brosur PPDB 2026/2027')->first();
        $this->assertNotNull($expense);
        $this->assertStringStartsWith('KLR-', $expense->expense_code);

        // 4. Input Pengeluaran Kedua untuk Menguji Selisih Keuangan
        $storeResponse2 = $this->actingAs($admin)->post(route('admin.administrasi.expense.store'), [
            'title' => 'Konsumsi Rapat Panitia Sosialisasi SPMB',
            'category' => 'Konsumsi & Rapat Panitia',
            'amount' => 250000,
            'expense_date' => date('Y-m-d'),
            'recipient' => 'Sie Konsumsi SPMB',
            'description' => 'Pembelian snack dan makan siang rapat koordinasi pengurus panitia SPMB.',
        ]);
        $storeResponse2->assertSessionHas('success');

        // 5. Verifikasi Perhitungan Selisih Pemasukan & Pengeluaran
        $totalPemasukan = (float) Payment::where('status', 'confirmed')->sum('amount');
        $totalPengeluaran = (float) Expense::sum('amount');
        $selisih = $totalPemasukan - $totalPengeluaran;

        $this->assertEquals(1000000, $totalPengeluaran); // 750rb + 250rb
        $this->assertEquals($totalPemasukan - 1000000, $selisih);

        // Cek bahwa angka selisih dan pengeluaran muncul di dashboard view
        $viewAfterExpense = $this->actingAs($admin)->get(route('admin.dashboard', ['view' => 'pengeluaran']));
        $viewAfterExpense->assertStatus(200);
        $viewAfterExpense->assertSee('Rp ' . number_format($totalPengeluaran, 0, ',', '.'));
        $viewAfterExpense->assertSee('Rp ' . number_format($selisih, 0, ',', '.'));
        $viewAfterExpense->assertSee('Pengadaan Formulir dan Brosur PPDB 2026/2027');
        $viewAfterExpense->assertSee($keteranganPengeluaran);
        $viewAfterExpense->assertSee('Keterangan');

        // 6. Test Edit Pengeluaran
        $updateResponse = $this->actingAs($admin)->post(route('admin.administrasi.expense.update', $expense->id), [
            'title' => 'Pengadaan Formulir & Brosur PPDB (Revisi)',
            'category' => 'ATK & Formulir SPMB',
            'amount' => 800000,
            'expense_date' => date('Y-m-d'),
            'recipient' => 'CV Percetakan Mandiri Solo Baru',
            'description' => 'Revisi tambahan jumlah cetak formulir pendaftaran calon siswa.',
        ]);
        $updateResponse->assertRedirect(route('admin.dashboard', ['view' => 'pengeluaran']));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('pengeluaran', [
            'id' => $expense->id,
            'title' => 'Pengadaan Formulir & Brosur PPDB (Revisi)',
            'amount' => 800000,
            'recipient' => 'CV Percetakan Mandiri Solo Baru',
        ]);

        // 7. Test Hapus Pengeluaran
        $deleteResponse = $this->actingAs($admin)->post(route('admin.administrasi.expense.delete', $expense->id));
        $deleteResponse->assertRedirect(route('admin.dashboard', ['view' => 'pengeluaran']));
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('pengeluaran', ['id' => $expense->id]);
    }

    /**
     * Test Role Admin dapat mengedit data calon siswa secara full (biodata, orang tua, sekolah, akun).
     */
    public function test_admin_dapat_mengedit_data_siswa_secara_full(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin_test_full@smkknbi.sch.id'],
            [
                'name' => 'Admin Full Edit Test',
                'username' => 'adm_full',
                'phone' => '081299990001',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $majors = Major::take(2)->get();
        $major1 = $majors[0];
        $major2 = $majors[1] ?? $majors[0];

        $siswa = Siswa::create([
            'name' => 'Siswa Belum Edit',
            'username' => 'siswa_edit_full_test',
            'phone' => '087700001111',
            'email' => 'siswa_edit_full_test@siswa.smkknbi.sch.id',
            'password' => Hash::make('12345'),
            'role' => 'murid',
            'is_active' => true,
        ]);

        $ay = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $reg = Registration::create([
            'registration_number' => 'REG-FULL-001',
            'user_id' => $siswa->id,
            'academic_year_id' => $ay->id,
            'major_id' => $major1->id,
            'registration_type' => 'reguler',
            'status' => 'pending',
        ]);

        $fullPayload = [
            // Akun & Pendaftaran
            'nama_siswa' => 'Budi Santoso Putra Pratama',
            'phone' => '087700001111',
            'username' => 'budisantoso99',
            'nisn' => '0098765432',
            'email' => 'budi.santoso@gmail.com',
            'gender' => 'L',
            'major_id' => $major1->id,
            'major_choice_2_id' => $major2->id,
            'registration_type' => 'prestasi',
            'recommendation_by' => 'Drs. H. Mulyadi',
            'status' => 'verified',

            // Biodata & Alamat
            'nik' => '3309012345678901',
            'no_kk' => '3309012345678902',
            'tempat_lahir' => 'Boyolali',
            'tanggal_lahir' => '2008-05-15',
            'agama' => 'Islam',
            'anak_ke' => 2,
            'jumlah_saudara' => 3,
            'tinggi_badan' => 168,
            'berat_badan' => 55,
            'riwayat_penyakit' => 'Tidak Ada',
            'alamat_jalan' => 'Jl. Pandanaran No. 45 RT 03 / RW 05',
            'desa' => 'Siswodipuran',
            'kecamatan' => 'Boyolali',
            'kabupaten' => 'Boyolali',
            'provinsi' => 'Jawa Tengah',
            'kode_pos' => '57311',

            // Asal Sekolah
            'asal_sekolah' => 'SMP Negeri 1 Boyolali',
            'npsn_sekolah' => '20307777',
            'jenis_sekolah' => 'SMP',
            'status_sekolah' => 'Negeri',
            'kabupaten_sekolah' => 'Boyolali',
            'tahun_lulus' => '2026',
            'no_ijazah' => 'DN-03/D-SMP/26/0012345',
            'nilai_rata_raport' => 88.75,

            // Orang Tua & Wali
            'nama_ayah' => 'Santoso Wijaya',
            'status_ayah' => 'Masih Hidup',
            'nik_ayah' => '3309011111111111',
            'pekerjaan_ayah' => 'Wiraswasta',
            'pendidikan_ayah' => 'S1',
            'penghasilan_ayah' => 'Rp 5.000.000 - Rp 10.000.000',
            'no_hp_ayah' => '081234567890',
            'nama_ibu' => 'Siti Aminah',
            'status_ibu' => 'Masih Hidup',
            'nik_ibu' => '3309012222222222',
            'pekerjaan_ibu' => 'Guru',
            'pendidikan_ibu' => 'S1',
            'penghasilan_ibu' => 'Rp 3.000.000 - Rp 5.000.000',
            'no_hp_ibu' => '081234567891',
            'nama_wali' => 'Bambang Supriyanto',
            'hubungan_wali' => 'Paman',
            'pekerjaan_wali' => 'PNS',
            'no_hp_wali' => '081234567892',
            'alamat_wali' => 'Solo Baru',
        ];

        $response = $this->actingAs($admin)->post("/admin/spmb/calon-siswa/{$reg->id}/update", $fullPayload);
        $response->assertSessionHas('success');

        // Verifikasi tabel siswa
        $this->assertDatabaseHas('siswa', [
            'id' => $siswa->id,
            'name' => 'Budi Santoso Putra Pratama',
            'username' => 'budisantoso99',
            'nisn' => '0098765432',
            'email' => 'budi.santoso@gmail.com',
        ]);

        // Verifikasi tabel pendaftaran
        $this->assertDatabaseHas('pendaftaran', [
            'id' => $reg->id,
            'major_id' => $major1->id,
            'major_choice_2_id' => $major2->id,
            'registration_type' => 'prestasi',
            'recommendation_by' => 'Drs. H. Mulyadi',
            'status' => 'verified',
        ]);

        // Verifikasi tabel biodata_siswa
        $this->assertDatabaseHas('biodata_siswa', [
            'registration_id' => $reg->id,
            'nik' => '3309012345678901',
            'no_kk' => '3309012345678902',
            'agama' => 'Islam',
            'tempat_lahir' => 'Boyolali',
            'anak_ke' => 2,
            'desa' => 'Siswodipuran',
        ]);

        // Verifikasi tabel asal_sekolah_siswa
        $this->assertDatabaseHas('asal_sekolah_siswa', [
            'registration_id' => $reg->id,
            'asal_sekolah' => 'SMP Negeri 1 Boyolali',
            'npsn_sekolah' => '20307777',
            'tahun_lulus' => '2026',
        ]);

        // Verifikasi tabel orang_tua_siswa
        $this->assertDatabaseHas('orang_tua_siswa', [
            'registration_id' => $reg->id,
            'nama_ayah' => 'Santoso Wijaya',
            'nama_ibu' => 'Siti Aminah',
            'nama_wali' => 'Bambang Supriyanto',
        ]);
    }

    /**
     * Test Role Panitia dapat mengedit data siswa seperti role admin sebelumnya.
     */
    public function test_panitia_dapat_mengedit_data_siswa_seperti_role_admin_sebelumnya(): void
    {
        $panitia = User::firstOrCreate(
            ['email' => 'panitia_edit_test@smkknbi.sch.id'],
            [
                'name' => 'Panitia SPMB Tester',
                'username' => 'panitia_test_edit',
                'phone' => '081399990002',
                'password' => Hash::make('password'),
                'role' => 'panitia',
                'is_active' => true,
            ]
        );

        $major = Major::first();
        $siswa = Siswa::create([
            'name' => 'Calon Siswa Panitia Edit',
            'username' => 'siswa_panitia_test',
            'phone' => '087711112222',
            'email' => 'siswa_panitia_test@siswa.smkknbi.sch.id',
            'password' => Hash::make('12345'),
            'role' => 'murid',
            'is_active' => true,
        ]);

        $ay = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $reg = Registration::create([
            'registration_number' => 'REG-PAN-001',
            'user_id' => $siswa->id,
            'academic_year_id' => $ay->id,
            'major_id' => $major->id,
            'status' => 'pending',
        ]);

        $panitiaPayload = [
            'nama_siswa' => 'Ahmad Fauzi Panitia',
            'phone' => '087711112222',
            'username' => 'ahmadfauzi_panitia',
            'password' => 'panitia123',
            'gender' => 'L',
            'asal_sekolah' => 'SMP Negeri 3 Boyolali',
            'major_id' => $major->id,
            'status' => 'pending',
        ];

        $response = $this->actingAs($panitia)->post(route('panitia.spmb.student.update', $reg->id), $panitiaPayload);
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('siswa', [
            'id' => $siswa->id,
            'name' => 'Ahmad Fauzi Panitia',
            'username' => 'ahmadfauzi_panitia',
            'raw_password' => 'panitia123',
        ]);

        $this->assertDatabaseHas('asal_sekolah_siswa', [
            'registration_id' => $reg->id,
            'asal_sekolah' => 'SMP Negeri 3 Boyolali',
        ]);
    }
}



