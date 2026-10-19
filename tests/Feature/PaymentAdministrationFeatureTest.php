<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
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
}
