<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Siswa;
use Tests\TestCase;

class RegistrationFlowTest extends TestCase
{
    public function test_registration_and_login_flow()
    {
        $this->artisan('migrate');
        $this->artisan('db:seed');

        $major = Major::first();
        $this->assertNotNull($major, 'Major should exist');

        // Delete test user if exists
        Siswa::where('nisn', '0098765432')->orWhere('email', 'bintang@example.com')->delete();

        $payload = [
            'major_id' => $major->id,
            'jenis_pendaftaran' => 'siswa_baru',
            'nohp' => '085811223344',
            'nama_siswa' => 'Bintang Pratama',
            'nisn' => '0098765432',
            'email' => 'bintang@example.com',
            'password' => 'rahasia123',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'tempat_lahir' => 'Boyolali',
            'tanggal_lahir' => '2009-08-17',
            'kabupaten' => 'Boyolali',
            'kecamatan' => 'Boyolali',
            'desa' => 'Kemiri',
            'alamat_jalan' => 'Jl. Pandanaran No. 45',
            'nama_ayah' => 'Bambang Supriyanto',
            'pekerjaan_ayah' => 'Wiraswasta',
            'no_hp_ayah' => '081234567890',
            'nama_ibu' => 'Sri Wahyuni',
            'pekerjaan_ibu' => 'Ibu Rumah Tangga',
            'asal_sekolah' => 'SMP Negeri 1 Boyolali',
        ];

        // 1. Submit Registration via AJAX
        $response = $this->postJson('/daftar', $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $responseData = $response->json('data');
        $this->assertEquals('Bintang Pratama', $responseData['name']);
        $this->assertEquals('rahasia123', $responseData['raw_password']);

        // 2. Test Login using credentials from confirmation modal
        $loginResponse = $this->post('/login', [
            'login_identity' => $responseData['nisn'],
            'password' => 'rahasia123',
        ]);

        $loginResponse->assertRedirect('/murid/dashboard');

        // 3. Verify accessing murid dashboard
        $user = Siswa::where('nisn', '0098765432')->first();
        $this->assertNotNull($user);

        $dashboardResponse = $this->actingAs($user)->get('/murid/dashboard');
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee('Bintang Pratama');
        $dashboardResponse->assertSee($responseData['registration_number']);

        echo "\n[SUCCESS] Registration flow tested successfully!\n";
    }
}
