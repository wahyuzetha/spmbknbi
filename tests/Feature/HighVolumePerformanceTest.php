<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HighVolumePerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    /**
     * Seed 510 calon siswa secara bulk untuk simulasi beban tinggi (> 500 calon siswa).
     */
    private function seedBulkStudents(int $count = 510): array
    {
        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $majors = Major::where('is_active', true)->pluck('id')->all();
        $now = now()->toDateTimeString();
        $passwordHash = bcrypt('123456');

        $startId = (int) Siswa::max('id') + 1;
        $startRegId = (int) Registration::max('id') + 1;

        $siswaRows = [];
        $regRows = [];
        $bioRows = [];
        $parentRows = [];
        $schoolRows = [];

        for ($i = 1; $i <= $count; $i++) {
            $siswaId = $startId + $i;
            $regId = $startRegId + $i;
            $majorId = $majors[$i % count($majors)];
            $seq = str_pad((string) ($i + 50), 3, '0', STR_PAD_LEFT);
            $regNumber = "26.27.01.{$seq}";

            $siswaRows[] = [
                'id' => $siswaId,
                'name' => "Calon Siswa Bulk {$i}",
                'username' => "2026{$seq}",
                'nisn' => str_pad((string) (1000000000 + $i), 10, '0', STR_PAD_LEFT),
                'email' => "bulk_siswa_{$i}@smkknbi.sch.id",
                'phone' => '0819'.str_pad((string) $i, 8, '0', STR_PAD_LEFT),
                'password' => $passwordHash,
                'raw_password' => '123456',
                'role' => 'murid',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $regRows[] = [
                'id' => $regId,
                'registration_number' => $regNumber,
                'user_id' => $siswaId,
                'academic_year_id' => $academicYear->id,
                'major_id' => $majorId,
                'registration_type' => 'siswa_baru',
                'status' => $i % 3 === 0 ? 'accepted' : ($i % 2 === 0 ? 'verified' : 'pending'),
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $bioRows[] = [
                'registration_id' => $regId,
                'nama_lengkap' => "Calon Siswa Bulk {$i}",
                'jenis_kelamin' => $i % 2 === 0 ? 'L' : 'P',
                'agama' => 'Islam',
                'tempat_lahir' => 'Boyolali',
                'tanggal_lahir' => '2010-05-15',
                'no_hp' => '0819'.str_pad((string) $i, 8, '0', STR_PAD_LEFT),
                'email' => "bulk_siswa_{$i}@smkknbi.sch.id",
                'alamat_jalan' => "Jl. Merbabu No. {$i}",
                'desa' => 'Siswodipuran',
                'kecamatan' => 'Boyolali',
                'kabupaten' => 'Boyolali',
                'provinsi' => 'Jawa Tengah',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $parentRows[] = [
                'registration_id' => $regId,
                'nama_ayah' => "Ayah Siswa {$i}",
                'pekerjaan_ayah' => 'Wiraswasta',
                'no_hp_ayah' => '0819'.str_pad((string) $i, 8, '0', STR_PAD_LEFT),
                'status_ayah' => 'hidup',
                'nama_ibu' => "Ibu Siswa {$i}",
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'status_ibu' => 'hidup',
                'created_at' => $now,
                'updated_at' => $now,
            ];

            $schoolRows[] = [
                'registration_id' => $regId,
                'asal_sekolah' => 'SMP NEGERI '.(($i % 10) + 1).' BOYOLALI',
                'jenis_sekolah' => 'SMP',
                'tahun_lulus' => '2026',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        foreach (array_chunk($siswaRows, 150) as $chunk) {
            DB::table('siswa')->insert($chunk);
        }
        foreach (array_chunk($regRows, 150) as $chunk) {
            DB::table('pendaftaran')->insert($chunk);
        }
        foreach (array_chunk($bioRows, 150) as $chunk) {
            DB::table('biodata_siswa')->insert($chunk);
        }
        foreach (array_chunk($parentRows, 150) as $chunk) {
            DB::table('orang_tua_siswa')->insert($chunk);
        }
        foreach (array_chunk($schoolRows, 150) as $chunk) {
            DB::table('asal_sekolah_siswa')->insert($chunk);
        }

        return [
            'last_siswa_id' => $startId + $count,
            'last_reg_id' => $startRegId + $count,
        ];
    }

    public function test_cetak_bukti_pendaftaran_tetap_cepat_dan_bebas_bottleneck_saat_data_lebih_dari_500_calon_siswa()
    {
        $seeded = $this->seedBulkStudents(510);
        $this->assertGreaterThan(500, Registration::count());

        $admin = User::where('role', 'admin')->first();
        $panitia = User::where('role', 'panitia')->first();
        $murid = Siswa::find($seeded['last_siswa_id']);
        $regId = $seeded['last_reg_id'];

        // Warm cache pengaturan sistem
        SystemSetting::allCached();

        // 1. Cetak Bukti Pendaftaran dari sisi Calon Siswa (Murid)
        DB::enableQueryLog();
        DB::flushQueryLog();

        $resMurid = $this->actingAs($murid, 'web')->get(route('murid.print.card'));
        $muridQueries = count(DB::getQueryLog());

        $resMurid->assertStatus(200);
        $resMurid->assertSee('Calon Siswa Bulk 510', false);
        $resMurid->assertSee('215mm 330mm', false);
        $resMurid->assertSee('triggerPrintDocument()', false);
        // Pastikan jumlah query konstan & sangat kecil (<= 10 query meskipun ada > 500 siswa)
        $this->assertLessThanOrEqual(10, $muridQueries, "Query cetak bukti pendaftaran murid terlalu banyak: {$muridQueries}");

        // 2. Cetak Bukti Pendaftaran dari sisi Admin
        DB::flushQueryLog();
        $resAdmin = $this->actingAs($admin)->get(route('admin.spmb.printCard', $regId));
        $adminQueries = count(DB::getQueryLog());

        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Calon Siswa Bulk 510', false);
        $this->assertLessThanOrEqual(10, $adminQueries, "Query cetak bukti pendaftaran admin terlalu banyak: {$adminQueries}");

        // 3. Cetak Bukti Pendaftaran dari sisi Panitia
        if ($panitia) {
            DB::flushQueryLog();
            $resPanitia = $this->actingAs($panitia)->get(route('panitia.spmb.printCard', $regId));
            $panitiaQueries = count(DB::getQueryLog());

            $resPanitia->assertStatus(200);
            $resPanitia->assertSee('Calon Siswa Bulk 510', false);
            $this->assertLessThanOrEqual(10, $panitiaQueries, "Query cetak bukti pendaftaran panitia terlalu banyak: {$panitiaQueries}");
        }
    }

    public function test_dashboard_admin_panitia_dan_pendaftaran_baru_berjalan_lancar_tanpa_n_plus_1_saat_lebih_dari_500_siswa()
    {
        $this->seedBulkStudents(510);
        $this->assertGreaterThan(500, Registration::count());

        $admin = User::where('role', 'admin')->first();
        $panitia = User::where('role', 'panitia')->first();

        SystemSetting::allCached();

        // 1. Admin Dashboard (view=calon_siswa) yang merender tombol WhatsApp untuk setiap baris
        DB::enableQueryLog();
        DB::flushQueryLog();

        $resAdmin = $this->actingAs($admin)->get(route('admin.dashboard', ['view' => 'calon_siswa']));
        $adminQueryCount = count(DB::getQueryLog());

        $resAdmin->assertStatus(200);
        $resAdmin->assertSee('Calon Siswa Bulk 510', false);
        // Sebelum optimasi: > 1.500 query (karena N+1 SystemSetting::formatWaMessage). Sesudah optimasi: < 35 query!
        $this->assertLessThan(35, $adminQueryCount, "Terjadi N+1 query pada Admin Dashboard: {$adminQueryCount} queries");

        // 2. Panitia Dashboard (view=verifikasi)
        if ($panitia) {
            DB::flushQueryLog();
            $resPanitia = $this->actingAs($panitia)->get(route('panitia.dashboard', ['view' => 'verifikasi']));
            $panitiaQueryCount = count(DB::getQueryLog());

            $resPanitia->assertStatus(200);
            $this->assertLessThan(30, $panitiaQueryCount, "Terjadi N+1 query pada Panitia Dashboard: {$panitiaQueryCount} queries");
        }

        // 3. Pendaftaran siswa ke-511+ tetap menghasilkan nomor urut akurat dan cepat
        $major = Major::where('is_active', true)->first();
        $newRegResponse = $this->postJson(route('daftar.store'), [
            'major_id' => $major->id,
            'jenis_pendaftaran' => 'siswa_baru',
            'nama_siswa' => 'Siswa Ke Lima Ratus Sebelas',
            'nohp' => '081999888777',
            'nisn' => '9988776655',
            'email' => 'siswa511@smkknbi.sch.id',
            'password' => 'rahasia123',
            'jenis_kelamin' => 'L',
            'agama' => 'Islam',
            'tempat_lahir' => 'Boyolali',
            'tanggal_lahir' => '2010-01-01',
            'kabupaten' => 'Boyolali',
            'kecamatan' => 'Boyolali',
            'desa' => 'Banaran',
            'alamat_jalan' => 'Jl. Pandanaran 10',
            'nama_ayah' => 'Bapak 511',
            'pekerjaan_ayah' => 'Petani',
            'nama_ibu' => 'Ibu 511',
            'pekerjaan_ibu' => 'Pedagang',
            'asal_sekolah' => 'SMPN 1 BOYOLALI',
        ]);

        $newRegResponse->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_database_manager_browser_dapat_mereset_data_siswa_dan_pembayaran_saat_ganti_tahun_ajaran_baru()
    {
        $admin = User::where('role', 'admin')->first();
        $this->seedBulkStudents(20);

        DB::table('pembayaran')->insert([
            'registration_id' => Registration::first()->id,
            'invoice_number' => 'INV-RESET-001',
            'payment_type' => 'pendaftaran',
            'amount' => 250000,
            'payment_method' => 'cash',
            'status' => 'confirmed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertGreaterThan(0, Siswa::count());
        $this->assertGreaterThan(0, Registration::count());
        $this->assertGreaterThan(0, DB::table('pembayaran')->count());

        // 1. Buka halaman Database Manager Browser & pastikan tombol reset tampil
        $dbView = $this->actingAs($admin)->get(route('admin.database'));
        $dbView->assertStatus(200);
        $dbView->assertSee('Reset Data Siswa & Pembayaran', false);
        $dbView->assertSee('Kembalikan ke Default Awal / Kosong', false);

        // 2. Uji konfirmasi salah -> data tidak boleh terhapus
        $invalidRes = $this->actingAs($admin)->post(route('admin.database.reset'), [
            'reset_scope' => 'all',
            'confirm_text' => 'SALAH',
        ]);
        $invalidRes->assertSessionHas('error');
        $this->assertGreaterThan(0, Registration::count());

        // 3. Uji Reset Pembayaran Saja -> pembayaran jadi 0, siswa tetap ada
        $payResetRes = $this->actingAs($admin)->post(route('admin.database.reset'), [
            'reset_scope' => 'payments_only',
            'confirm_text' => 'RESET',
        ]);
        $payResetRes->assertSessionHas('success');
        $this->assertEquals(0, DB::table('pembayaran')->count());
        $this->assertGreaterThan(0, Registration::count());

        // 4. Uji Reset Semua (Siswa & Pembayaran) + Ganti Tahun Ajaran Baru
        $allResetRes = $this->actingAs($admin)->post(route('admin.database.reset'), [
            'reset_scope' => 'all',
            'confirm_text' => 'RESET',
            'new_academic_year' => '2027/2028',
        ]);
        $allResetRes->assertSessionHas('success');

        $this->assertEquals(0, Siswa::count());
        $this->assertEquals(0, Registration::count());
        $this->assertEquals(0, DB::table('biodata_siswa')->count());
        $this->assertEquals(0, DB::table('orang_tua_siswa')->count());
        $this->assertEquals(0, DB::table('asal_sekolah_siswa')->count());
        $this->assertEquals(0, DB::table('dokumen_siswa')->count());
        $this->assertEquals(0, DB::table('pembayaran')->count());

        // Pastikan data Master (Admin/Panitia & Jurusan) tetap aman dan TP baru aktif
        $this->assertGreaterThan(0, User::count());
        $this->assertGreaterThan(0, Major::count());
        $this->assertEquals('2027/2028', AcademicYear::where('is_active', true)->value('name'));
    }
}
