<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\User;
use Tests\TestCase;

class AdminPanitiaPortalTest extends TestCase
{
    public function test_admin_and_panitia_dashboards()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed');

        $admin = User::where('role', 'admin')->first();
        $panitia = User::where('role', 'panitia')->first();
        $murid = Siswa::where('role', 'murid')->first();
        $registration = Registration::first();

        $this->assertNotNull($admin, 'Admin should exist');
        $this->assertNotNull($panitia, 'Panitia should exist');
        $this->assertNotNull($murid, 'Murid should exist');
        $this->assertNotNull($registration, 'Registration should exist');

        // 1. Landing Page Loads OK with 200
        $landingRes = $this->get('/');
        $landingRes->assertStatus(200);
        $landingRes->assertSee('Login Petugas');
        $landingRes->assertSee('Administrator');
        $landingRes->assertSee('Panitia SPMB');

        // 2. Login Page Loads OK with 200
        $loginRes = $this->get('/login');
        $loginRes->assertStatus(200);
        $loginRes->assertSee('Portal Masuk SPMB');

        // 3. Admin Login and Access Admin Dashboard
        $adminLogin = $this->post('/login', [
            'login_identity' => 'admin@smkknbi.sch.id',
            'password' => 'password',
        ]);
        $adminLogin->assertRedirect('/admin/dashboard');

        $adminDash = $this->actingAs($admin)->get('/admin/dashboard');
        $adminDash->assertStatus(200);
        $adminDash->assertSee('Dashboard');
        $adminDash->assertSee('SELURUH PENDAFTAR');
        $adminDash->assertSee('SISWA DITERIMA');
        $adminDash->assertSee('BELUM UJIAN/TEST');
        $adminDash->assertSee('SUDAH MEMBAYAR');
        $adminDash->assertSee('NPSN');
        $adminDash->assertSee('Save Changes');

        // 4. Test Update School Settings (Profile)
        $settingsRes = $this->actingAs($admin)->post('/admin/settings/update', [
            'school_npsn' => '20308458',
            'school_name' => 'SMK KARYA NUGRAHA BOYOLALI',
            'school_level' => 'SMK',
            'school_status' => 'Swasta',
        ]);
        $settingsRes->assertSessionHas('success');

        // 5. Admin Update Registration Status
        $updateRes = $this->actingAs($admin)->post("/admin/registrations/{$registration->id}/status", [
            'status' => 'verified',
            'verification_notes' => 'Diverifikasi oleh Admin Test',
        ]);
        $updateRes->assertSessionHas('success');
        $this->assertEquals('verified', $registration->fresh()->status);

        // 6. Panitia Login and Access Panitia Dashboard
        $panitiaLogin = $this->post('/login', [
            'login_identity' => 'panitia@smkknbi.sch.id',
            'password' => 'password',
        ]);
        $panitiaLogin->assertRedirect('/panitia/dashboard');

        $panitiaDash = $this->actingAs($panitia)->get('/panitia/dashboard');
        $panitiaDash->assertStatus(200);
        $panitiaDash->assertSee('Dashboard');
        $panitiaDash->assertSee('SELURUH PENDAFTAR');
        $panitiaDash->assertSee('Profile');

        // 6. Panitia Verify Registration
        $verifyRes = $this->actingAs($panitia)->post("/panitia/verify/{$registration->id}", [
            'status' => 'accepted',
            'verification_notes' => 'Selamat, Anda diterima di SMK Karya Nugraha Boyolali',
        ]);
        $verifyRes->assertSessionHas('success');
        $this->assertEquals('accepted', $registration->fresh()->status);

        // 7. Role Security checks: Murid cannot access admin or panitia dashboards
        $unauthorizedAdmin = $this->actingAs($murid)->get('/admin/dashboard');
        $unauthorizedAdmin->assertStatus(403);

        $unauthorizedPanitia = $this->actingAs($murid)->get('/panitia/dashboard');
        $unauthorizedPanitia->assertStatus(403);

        // Panitia cannot access Admin dashboard
        $panitiaUnauthorized = $this->actingAs($panitia)->get('/admin/dashboard');
        $panitiaUnauthorized->assertStatus(403);

        // 8. Test Data Jurusan View and CRUD
        $jurusanViewRes = $this->actingAs($admin)->get('/admin/dashboard?view=jurusan');
        $jurusanViewRes->assertStatus(200);
        $jurusanViewRes->assertSee('Data Jurusan');
        $jurusanViewRes->assertSee('Tambah Data');
        $jurusanViewRes->assertSee('PSY');
        $jurusanViewRes->assertSee('Layanan Perbankan Syariah (Kelas BSI)');
        $jurusanViewRes->assertSee('TBO');
        $jurusanViewRes->assertSee('TKJ Axioo');
        $jurusanViewRes->assertSee('TKJ+');
        $jurusanViewRes->assertSee('TKR');
        $jurusanViewRes->assertSee('TSM');
        $jurusanViewRes->assertSee('TTT');

        // Store new major
        $storeMajorRes = $this->actingAs($admin)->post('/admin/sekolah/jurusan/store', [
            'code' => 'DKV',
            'name' => 'Desain Komunikasi Visual',
            'quota' => 72,
            'is_active' => 1,
        ]);
        $storeMajorRes->assertSessionHas('success');
        $this->assertDatabaseHas('jurusan', ['code' => 'DKV']);

        $dkv = Major::where('code', 'DKV')->first();
        // Update major
        $updateMajorRes = $this->actingAs($admin)->post("/admin/sekolah/jurusan/{$dkv->id}/update", [
            'code' => 'DKV-ANIM',
            'name' => 'Desain Komunikasi Visual & Animasi',
            'quota' => 80,
            'is_active' => 1,
        ]);
        $updateMajorRes->assertSessionHas('success');
        $this->assertDatabaseHas('jurusan', ['code' => 'DKV-ANIM']);

        // Delete major
        $deleteMajorRes = $this->actingAs($admin)->post("/admin/sekolah/jurusan/{$dkv->id}/delete");
        $deleteMajorRes->assertSessionHas('success');
        $this->assertDatabaseMissing('jurusan', ['id' => $dkv->id]);

        // 9. Test Data Pengguna View and CRUD
        $penggunaViewRes = $this->actingAs($admin)->get('/admin/dashboard?view=pengguna');
        $penggunaViewRes->assertStatus(200);
        $penggunaViewRes->assertSee('Data Pengguna');
        $penggunaViewRes->assertSee('Hapus Data');
        $penggunaViewRes->assertSee('Tambah Data Pengguna');
        $penggunaViewRes->assertSee('smkknbi');
        $penggunaViewRes->assertSee('kepala sekolah');
        $penggunaViewRes->assertSee('kssmkknbi');

        // Store new staff user (Panitia SPMB)
        $storeUserRes = $this->actingAs($admin)->post('/admin/sekolah/pengguna/store', [
            'name' => 'Panitia Baru 2027',
            'username' => 'panitiabaru',
            'password' => 'password123',
            'role' => 'panitia',
        ]);
        $storeUserRes->assertSessionHas('success');
        $this->assertDatabaseHas('petugas', ['username' => 'panitiabaru']);

        // Panitia logs in with admin-created credentials
        $panitiaLoginNew = $this->post('/login', [
            'login_identity' => 'panitiabaru',
            'password' => 'password123',
        ]);
        $panitiaLoginNew->assertRedirect('/panitia/dashboard');

        $panitiaUser = User::where('username', 'panitiabaru')->first();

        // Admin updates Panitia user and changes password
        $updateUserRes = $this->actingAs($admin)->post("/admin/sekolah/pengguna/{$panitiaUser->id}/update", [
            'name' => 'Panitia Baru Diedit',
            'username' => 'panitiabaru',
            'password' => 'newpassword456',
            'role' => 'panitia',
        ]);
        $updateUserRes->assertSessionHas('success');

        // Old password must fail
        $oldPwdFail = $this->post('/login', [
            'login_identity' => 'panitiabaru',
            'password' => 'password123',
        ]);
        $oldPwdFail->assertSessionHasErrors();

        // New password must succeed
        $newPwdSuccess = $this->post('/login', [
            'login_identity' => 'panitiabaru',
            'password' => 'newpassword456',
        ]);
        $newPwdSuccess->assertRedirect('/panitia/dashboard');

        // Admin deletes the user using single delete
        $singleDeleteRes = $this->actingAs($admin)->post("/admin/sekolah/pengguna/{$panitiaUser->id}/delete");
        $singleDeleteRes->assertSessionHas('success');
        $this->assertDatabaseMissing('petugas', ['id' => $panitiaUser->id]);

        // Deleted panitia cannot login anymore
        $deletedLoginFail = $this->post('/login', [
            'login_identity' => 'panitiabaru',
            'password' => 'newpassword456',
        ]);
        $deletedLoginFail->assertSessionHasErrors();

        echo "\n[SUCCESS] Admin and Panitia Portals, Data Jurusan, and Data Pengguna verified successfully!\n";
    }
}
