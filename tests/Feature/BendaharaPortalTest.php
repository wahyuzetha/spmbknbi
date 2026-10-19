<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Expense;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\StudentBiodata;
use App\Models\StudentParent;
use App\Models\StudentSchool;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BendaharaPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $bendahara;
    protected User $admin;
    protected User $panitia;
    protected Siswa $siswa;
    protected Registration $registration;
    protected Major $majorTKR;
    protected Major $majorTKJ;

    protected function setUp(): void
    {
        parent::setUp();

        SystemSetting::set('school_name', 'SMK Karya Nugraha Boyolali', 'school_profile');

        AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $this->majorTKR = Major::create([
            'code' => 'TKR',
            'name' => 'Teknik Kendaraan Ringan',
            'quota' => 100,
            'is_active' => true,
        ]);

        $this->majorTKJ = Major::create([
            'code' => 'TKJ',
            'name' => 'Teknik Komputer dan Jaringan',
            'industry_class' => 'Axioo Class Program',
            'quota' => 70,
            'is_active' => true,
        ]);

        $this->admin = User::create([
            'name' => 'Admin SPMB',
            'username' => 'admin',
            'email' => 'admin@smkknbi.sch.id',
            'role' => 'admin',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $this->panitia = User::create([
            'name' => 'Panitia Verifikasi',
            'username' => 'panitia1',
            'email' => 'panitia1@smkknbi.sch.id',
            'role' => 'panitia',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $this->bendahara = User::create([
            'name' => 'Ibu Bendahara SPMB',
            'username' => 'bendahara1',
            'email' => 'bendahara1@smkknbi.sch.id',
            'role' => 'bendahara',
            'password' => Hash::make('password'),
            'is_active' => true,
        ]);

        $this->siswa = Siswa::create([
            'name' => 'Budi Santoso',
            'username' => '081234567890',
            'phone' => '081234567890',
            'nisn' => '0012345678',
            'email' => 'budi@siswa.sch.id',
            'password' => Hash::make('password'),
            'raw_password' => 'password',
            'role' => 'murid',
            'is_active' => true,
        ]);

        $this->registration = Registration::create([
            'registration_number' => '271001',
            'user_id' => $this->siswa->id,
            'academic_year_id' => 1,
            'major_id' => $this->majorTKR->id,
            'registration_type' => 'siswa_baru',
            'status' => 'pending',
        ]);

        StudentSchool::create([
            'registration_id' => $this->registration->id,
            'asal_sekolah' => 'SMP Negeri 1 Boyolali',
            'jenis_sekolah' => 'SMP',
            'tahun_lulus' => 2026,
        ]);
    }

    public function test_bendahara_login_and_redirect_to_bendahara_dashboard()
    {
        $response = $this->post(route('login.post'), [
            'login_identity' => 'bendahara1',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('bendahara.dashboard'));
        $this->assertAuthenticatedAs($this->bendahara);

        // Test shortcut /dashboard redirect
        $dashboardResponse = $this->actingAs($this->bendahara)->get('/dashboard');
        $dashboardResponse->assertRedirect(route('bendahara.dashboard'));
    }

    public function test_admin_and_bendahara_can_login_through_unified_petugas_portal()
    {
        // 1. Bendahara login
        $resBendahara = $this->post(route('login.post'), [
            'login_identity' => 'bendahara1',
            'password' => 'password',
        ]);
        $resBendahara->assertRedirect(route('bendahara.dashboard'));
        $this->assertAuthenticatedAs($this->bendahara);

        auth()->logout();

        // 2. Admin login
        $resAdmin = $this->post(route('login.post'), [
            'login_identity' => 'admin',
            'password' => 'password',
        ]);
        $resAdmin->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($this->admin);

        auth()->logout();

        // 3. Panitia login
        $resPanitia = $this->post(route('login.post'), [
            'login_identity' => 'panitia1',
            'password' => 'password',
        ]);
        $resPanitia->assertRedirect(route('panitia.dashboard'));
        $this->assertAuthenticatedAs($this->panitia);
    }

    public function test_bendahara_can_access_bendahara_dashboard_and_see_metrics()
    {
        $response = $this->actingAs($this->bendahara)->get(route('bendahara.dashboard'));

        $response->assertOk();
        $response->assertSee('Portal Bendahara SPMB');
        $response->assertSee('Rekapitulasi Seluruh Pembayaran Calon Siswa');
        $response->assertSee('Total Kas Masuk');
        $response->assertSee('Ibu Bendahara SPMB');
        $response->assertSee($this->registration->registration_number);
        $response->assertSee('Budi Santoso');
    }

    public function test_bendahara_can_input_cash_payment_for_registered_student()
    {
        $response = $this->actingAs($this->bendahara)->post(route('bendahara.administrasi.payment.store'), [
            'registration_id' => $this->registration->id,
            'amount' => 150000,
            'payment_date' => '2026-10-19',
            'payment_type' => 'Biaya Pendaftaran & Seragam',
            'notes' => 'Lunas tunai di meja bendahara',
        ]);

        $response->assertRedirect(route('bendahara.dashboard', ['view' => 'administrasi']));
        $response->assertSessionHas('success');
        $response->assertSessionHas('last_payment_id');

        $this->assertDatabaseHas('pembayaran', [
            'registration_id' => $this->registration->id,
            'amount' => 150000,
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $this->bendahara->id,
        ]);

        $payment = Payment::where('registration_id', $this->registration->id)->first();
        $this->assertNotNull($payment);

        // Test bendahara can print receipt
        $receiptResponse = $this->actingAs($this->bendahara)->get(route('bendahara.administrasi.payment.receipt', $payment->id));
        $receiptResponse->assertOk();
        $receiptResponse->assertSee($payment->invoice_number);

        // Test bendahara can print global recap receipt
        $globalReceiptResponse = $this->actingAs($this->bendahara)->get(route('bendahara.administrasi.payment.globalReceipt', $this->registration->id));
        $globalReceiptResponse->assertOk();
        $globalReceiptResponse->assertSee('Rp 150.000');
    }

    public function test_bendahara_can_export_payments_to_excel()
    {
        Payment::create([
            'registration_id' => $this->registration->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'payment_type' => 'Daftar Ulang',
            'amount' => 200000,
            'payment_date' => now(),
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'confirmed_by' => $this->bendahara->id,
        ]);

        $response = $this->actingAs($this->bendahara)->get(route('bendahara.administrasi.payment.exportExcel'));

        $response->assertOk();
        $this->assertTrue(
            str_contains($response->headers->get('content-type') ?? '', 'spreadsheetml') ||
            str_contains($response->headers->get('content-disposition') ?? '', '.xlsx')
        );
    }

    public function test_bendahara_can_update_student_major_at_payment_desk()
    {
        $response = $this->actingAs($this->bendahara)->post(route('bendahara.administrasi.payment.updateMajor', $this->registration->id), [
            'major_id' => $this->majorTKJ->id,
        ]);

        $response->assertSessionHas('success');
        $this->registration->refresh();
        $this->assertEquals($this->majorTKJ->id, $this->registration->major_id);
    }

    public function test_bendahara_can_record_update_and_delete_expenses()
    {
        // 1. Create expense
        $storeRes = $this->actingAs($this->bendahara)->post(route('bendahara.administrasi.expense.store'), [
            'title' => 'Pembelian Map & ATK SPMB',
            'amount' => 75000,
            'expense_date' => '2026-10-19',
            'category' => 'Operasional SPMB',
            'recipient' => 'Toko Buku Berkah',
            'description' => 'Pembelian map pendaftaran 500 lembar',
        ]);

        $storeRes->assertRedirect(route('bendahara.dashboard', ['view' => 'pengeluaran']));
        $this->assertDatabaseHas('pengeluaran', [
            'title' => 'Pembelian Map & ATK SPMB',
            'amount' => 75000,
            'created_by' => $this->bendahara->id,
        ]);

        $expense = Expense::first();

        // 2. Update expense
        $updateRes = $this->actingAs($this->bendahara)->post(route('bendahara.administrasi.expense.update', $expense->id), [
            'title' => 'Pembelian Map & ATK SPMB Revisi',
            'amount' => 80000,
            'expense_date' => '2026-10-19',
            'category' => 'Operasional SPMB',
            'recipient' => 'Toko Buku Berkah',
            'description' => 'Revisi tambahan spidol',
        ]);

        $updateRes->assertRedirect(route('bendahara.dashboard', ['view' => 'pengeluaran']));
        $this->assertDatabaseHas('pengeluaran', [
            'id' => $expense->id,
            'title' => 'Pembelian Map & ATK SPMB Revisi',
            'amount' => 80000,
        ]);

        // 3. Delete expense
        $delRes = $this->actingAs($this->bendahara)->post(route('bendahara.administrasi.expense.delete', $expense->id));
        $delRes->assertRedirect(route('bendahara.dashboard', ['view' => 'pengeluaran']));
        $this->assertDatabaseMissing('pengeluaran', ['id' => $expense->id]);
    }

    public function test_route_access_controls_for_bendahara()
    {
        // Guest cannot access bendahara portal
        $guestRes = $this->get(route('bendahara.dashboard'));
        $guestRes->assertRedirect(route('login'));

        // Murid cannot access bendahara portal (403)
        $muridRes = $this->actingAs($this->siswa)->get(route('bendahara.dashboard'));
        $muridRes->assertForbidden();

        // Panitia cannot access bendahara portal (403)
        $panitiaRes = $this->actingAs($this->panitia)->get(route('bendahara.dashboard'));
        $panitiaRes->assertForbidden();

        // Bendahara cannot access admin-only routes (e.g. database viewer or user store)
        $bendaharaToAdminRes = $this->actingAs($this->bendahara)->get(route('admin.dashboard'));
        $bendaharaToAdminRes->assertForbidden();
    }

    public function test_admin_can_create_new_bendahara_user()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.panitia.store'), [
            'name' => 'Bendahara Kedua',
            'username' => 'bendahara2',
            'password' => 'secret123',
            'role' => 'bendahara',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('petugas', [
            'username' => 'bendahara2',
            'name' => 'Bendahara Kedua',
            'role' => 'bendahara',
        ]);
    }
}
