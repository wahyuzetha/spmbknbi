<?php

namespace Tests\Feature;

use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\User;
use Tests\TestCase;

class PaymentAdministrationFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_admin_dan_panitia_dapat_mengakses_dan_mengunduh_export_excel_pembayaran()
    {
        $admin = User::where('role', 'admin')->first();
        $panitia = User::where('role', 'panitia')->first();

        // Admin export Excel
        $resAdmin = $this->actingAs($admin)->get(route('admin.administrasi.payment.exportExcel'));
        $resAdmin->assertStatus(200);
        $resAdmin->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->assertStringContainsString('Rekap_Administrasi_Pembayaran_SPMB_KNBI', $resAdmin->headers->get('Content-Disposition'));

        // Panitia export Excel
        $resPanitia = $this->actingAs($panitia)->get(route('panitia.administrasi.payment.exportExcel'));
        $resPanitia->assertStatus(200);
        $resPanitia->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_admin_dan_panitia_dapat_mengubah_khusus_jurusan_siswa_di_administrasi_pembayaran()
    {
        $admin = User::where('role', 'admin')->first();
        $panitia = User::where('role', 'panitia')->first();

        $registration = Registration::with('major')->first();
        $this->assertNotNull($registration);

        $majors = Major::where('id', '!=', $registration->major_id)->get();
        $this->assertGreaterThan(0, $majors->count());

        $oldNumber = $registration->registration_number;
        $targetMajor = $majors->first();

        // 1. Admin ubah jurusan
        $resAdmin = $this->actingAs($admin)->post(route('admin.administrasi.payment.updateMajor', $registration->id), [
            'major_id' => $targetMajor->id,
        ]);
        $resAdmin->assertSessionHas('success');
        $freshReg = $registration->fresh();
        $this->assertEquals($targetMajor->id, $freshReg->major_id);

        // Pastikan nomor pendaftaran ikut berubah dan digit ke-3 cocok dengan kode jurusan baru
        $this->assertNotEquals($oldNumber, $freshReg->registration_number);
        $expectedMajorDigit = Registration::getMajorDigit($targetMajor);
        $this->assertEquals($expectedMajorDigit, substr($freshReg->registration_number, 2, 1));

        // 2. Panitia ubah jurusan lagi
        $anotherMajor = $majors->skip(1)->first() ?? Major::first();
        $resPanitia = $this->actingAs($panitia)->post(route('panitia.administrasi.payment.updateMajor', $registration->id), [
            'major_id' => $anotherMajor->id,
        ]);
        $resPanitia->assertSessionHas('success');
        $freshReg2 = $registration->fresh();
        $this->assertEquals($anotherMajor->id, $freshReg2->major_id);

        // Pastikan nomor pendaftaran berubah lagi sesuai jurusan berikutnya
        $this->assertNotEquals($freshReg->registration_number, $freshReg2->registration_number);
        $expectedMajorDigit2 = Registration::getMajorDigit($anotherMajor);
        $this->assertEquals($expectedMajorDigit2, substr($freshReg2->registration_number, 2, 1));
    }

    public function test_tabel_administrasi_pembayaran_menampilkan_tombol_export_excel_dan_tidak_menampilkan_kontak_atau_nama_smp()
    {
        $admin = User::where('role', 'admin')->first();

        // Masukkan pembayaran jika belum ada
        $reg = Registration::with(['user', 'school'])->first();
        if ($reg) {
            Payment::firstOrCreate([
                'registration_id' => $reg->id,
            ], [
                'invoice_number' => 'INV-TEST-001',
                'payment_type' => 'Pembayaran Pendaftaran',
                'amount' => 150000,
                'payment_date' => now(),
                'payment_method' => 'tunai',
                'status' => 'confirmed',
                'confirmed_by' => $admin->id,
                'confirmed_at' => now(),
            ]);
        }

        $response = $this->actingAs($admin)->get(route('admin.dashboard', ['view' => 'administrasi']));
        $response->assertStatus(200);

        // Memastikan tombol Export Excel (.xlsx) ada
        $response->assertSee(route('admin.administrasi.payment.exportExcel'));
        $response->assertSee('Export Excel (.xlsx)');

        // Memastikan tombol Ubah Jurusan ada
        $response->assertSee('Ubah Jurusan');
        $response->assertSee('modal-change-major');

        // Memastikan kontak dan nama SMP tidak ada di dalam kolom calon siswa tabel pembayaran
        $response->assertDontSee('Calon Siswa & Kontak');
    }

    public function test_input_pembayaran_siswa_tetap_berhasil_setelah_ada_data_pembayaran_atau_siswa_yang_dihapus_serta_mendukung_format_rupiah()
    {
        $admin = User::where('role', 'admin')->first();
        $panitia = User::where('role', 'panitia')->first();

        $registrations = Registration::with('user')->take(3)->get();
        $this->assertGreaterThanOrEqual(2, $registrations->count());

        $reg1 = $registrations[0];
        $reg2 = $registrations[1];

        // Buat 2 transaksi awal lalu hapus transaksi pertama sehingga jumlah baris (count) berkurang
        $pay1 = Payment::create([
            'registration_id' => $reg1->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'payment_type' => 'Pembayaran Administrasi SPMB',
            'amount' => 100000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $admin->id,
            'confirmed_at' => now(),
        ]);

        $pay2 = Payment::create([
            'registration_id' => $reg2->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'payment_type' => 'Pembayaran Administrasi SPMB',
            'amount' => 150000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $admin->id,
            'confirmed_at' => now(),
        ]);

        // Hapus transaksi pertama untuk mensimulasikan kondisi gap urutan nomor invoice
        $pay1->delete();

        // Submit pembayaran baru dari Admin dengan nominal berformat titik ribuan ("250.000") dan payment_type panjang
        $resAdmin = $this->actingAs($admin)->post(route('admin.administrasi.payment.store'), [
            'registration_id' => $reg1->id,
            'amount' => '250.000',
            'payment_date' => now()->toDateString(),
            'payment_type' => 'Pembayaran Administrasi Pendaftaran dan Seragam Sekolah Lengkap Tahun Ajaran Baru',
            'notes' => 'Pembayaran tunai setelah penghapusan data',
        ]);

        $resAdmin->assertRedirect(route('admin.dashboard', ['view' => 'administrasi']));
        $resAdmin->assertSessionHas('success');
        $resAdmin->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pembayaran', [
            'registration_id' => $reg1->id,
            'amount' => 250000,
            'status' => 'confirmed',
        ]);

        // Submit pembayaran dari Panitia menggunakan pencarian nama/no pendaftar (student_search fallback) dan format "Rp 1.000.000"
        $resPanitia = $this->actingAs($panitia)->post(route('panitia.administrasi.payment.store'), [
            'student_search' => $reg2->registration_number,
            'amount' => 'Rp 1.000.000',
            'payment_date' => now()->toDateString(),
            'payment_type' => 'Pelunasan Administrasi SPMB',
            'notes' => 'Diterima oleh panitia',
        ]);

        $resPanitia->assertRedirect(route('panitia.dashboard', ['view' => 'administrasi']));
        $resPanitia->assertSessionHas('success');
        $resPanitia->assertSessionHasNoErrors();

        $this->assertDatabaseHas('pembayaran', [
            'registration_id' => $reg2->id,
            'amount' => 1000000,
            'confirmed_by' => $panitia->id,
        ]);
    }

    public function test_admin_dan_bendahara_dapat_menghapus_data_pembayaran_tanpa_menghapus_data_calon_siswa()
    {
        $admin = User::where('role', 'admin')->first();
        $bendahara = User::where('role', 'bendahara')->first();
        $registration = Registration::with('user')->first();

        // 1. Buat pembayaran untuk siswa
        $paymentAdmin = Payment::create([
            'registration_id' => $registration->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'payment_type' => 'Pembayaran SPMB Admin',
            'amount' => 150000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $admin->id,
            'confirmed_at' => now(),
        ]);

        // Hapus pembayaran oleh Admin
        $resAdmin = $this->actingAs($admin)->post(route('admin.administrasi.payment.delete', $paymentAdmin->id));
        $resAdmin->assertRedirect(route('admin.dashboard', ['view' => 'administrasi']));
        $resAdmin->assertSessionHas('success');

        // Pastikan tabel pembayaran terhapus, TETAPI pendaftaran dan akun siswa TETAP ADA
        $this->assertDatabaseMissing('pembayaran', ['id' => $paymentAdmin->id]);
        $this->assertDatabaseHas('pendaftaran', ['id' => $registration->id]);
        $this->assertDatabaseHas('siswa', ['id' => $registration->user_id]);

        // 2. Buat pembayaran kedua untuk dihapus oleh Bendahara
        $paymentBendahara = Payment::create([
            'registration_id' => $registration->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'payment_type' => 'Pembayaran SPMB Bendahara',
            'amount' => 300000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $bendahara->id,
            'confirmed_at' => now(),
        ]);

        $resBendahara = $this->actingAs($bendahara)->post(route('bendahara.administrasi.payment.delete', $paymentBendahara->id));
        $resBendahara->assertRedirect(route('bendahara.dashboard', ['view' => 'administrasi']));
        $resBendahara->assertSessionHas('success');

        $this->assertDatabaseMissing('pembayaran', ['id' => $paymentBendahara->id]);
        $this->assertDatabaseHas('pendaftaran', ['id' => $registration->id]);
        $this->assertDatabaseHas('siswa', ['id' => $registration->user_id]);
    }

    public function test_admin_dan_bendahara_dapat_menghapus_seluruh_pembayaran_siswa_tanpa_menghapus_data_calon_siswa()
    {
        $admin = User::where('role', 'admin')->first();
        $registration = Registration::with('user')->first();

        // Buat 2 pembayaran
        Payment::create([
            'registration_id' => $registration->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'payment_type' => 'Pembayaran Cicilan 1',
            'amount' => 100000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $admin->id,
            'confirmed_at' => now(),
        ]);

        Payment::create([
            'registration_id' => $registration->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'payment_type' => 'Pembayaran Cicilan 2',
            'amount' => 200000,
            'payment_date' => now()->toDateString(),
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $admin->id,
            'confirmed_at' => now(),
        ]);

        $this->assertEquals(2, Payment::where('registration_id', $registration->id)->count());

        // Hapus seluruh pembayaran untuk siswa ini
        $res = $this->actingAs($admin)->post(route('admin.administrasi.payment.deleteAll', $registration->id));
        $res->assertRedirect(route('admin.dashboard', ['view' => 'administrasi']));
        $res->assertSessionHas('success');

        // Pastikan seluruh pembayaran siswa terhapus, tetapi pendaftaran & siswa tetap utuh
        $this->assertEquals(0, Payment::where('registration_id', $registration->id)->count());
        $this->assertDatabaseHas('pendaftaran', ['id' => $registration->id]);
        $this->assertDatabaseHas('siswa', ['id' => $registration->user_id]);
    }
}
