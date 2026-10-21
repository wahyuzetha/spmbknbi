<?php

namespace Tests\Feature;

use App\Models\Major;
use App\Models\MajorGallery;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MajorContentFeatureTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
        $this->artisan('db:seed');
    }

    public function test_landing_page_has_detail_jurusan_links(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $major = Major::first();
        $this->assertNotNull($major);
        $this->assertNotNull($major->slug);

        $response->assertSee(route('jurusan.detail', $major->slug));
        $response->assertSee('Lihat Detail');
    }

    public function test_public_major_detail_page_loads_successfully(): void
    {
        $major = Major::where('code', 'PSY')->first() ?? Major::first();

        $response = $this->get('/jurusan/'.$major->slug);
        $response->assertStatus(200);
        $response->assertSee($major->name);
        $response->assertSee('Keunggulan Utama Program Keahlian');
        $response->assertSee('Foto-Foto Praktikum');
        $response->assertSee('Video Profil Jurusan');
        $response->assertSee('Daftar Sekarang');
    }

    public function test_public_major_detail_404_for_invalid_slug(): void
    {
        $response = $this->get('/jurusan/slug-jurusan-yang-tidak-ada-12345');
        $response->assertStatus(404);
    }

    public function test_admin_can_access_konten_jurusan_view(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->assertNotNull($admin);

        $response = $this->actingAs($admin)->get('/admin/dashboard?view=konten_jurusan');
        $response->assertStatus(200);
        $response->assertSee('Media Jurusan');
        $response->assertSee('Pilih Jurusan');
        $response->assertSee('Video YouTube Jurusan');
    }

    public function test_admin_can_update_major_content(): void
    {
        $admin = User::where('role', 'admin')->first();
        $major = Major::first();

        $payload = [
            'tagline' => 'Tagline Baru untuk Pengujian',
            'head_of_major' => 'Bapak Kepala Jurusan Baru',
            'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'advantages_raw' => "Keunggulan Test 1 | Deskripsi Keunggulan 1 | fa-award\nKeunggulan Test 2 | Deskripsi Keunggulan 2 | fa-laptop",
            'career_raw' => "Karir 1\nKarir 2",
            'competencies_raw' => "Kompetensi 1\nKompetensi 2",
            'facilities_raw' => "Fasilitas 1\nFasilitas 2",
        ];

        $response = $this->actingAs($admin)->post('/admin/sekolah/jurusan/'.$major->id.'/content', $payload);
        $response->assertRedirect('/admin/dashboard?view=konten_jurusan&major_id='.$major->id);
        $response->assertSessionHas('success');

        $major->refresh();
        $this->assertEquals('Tagline Baru untuk Pengujian', $major->tagline);
        $this->assertEquals('Bapak Kepala Jurusan Baru', $major->head_of_major);
        $this->assertEquals('https://www.youtube.com/watch?v=dQw4w9WgXcQ', $major->video_url);
        $this->assertIsArray($major->advantages);
        $this->assertEquals('Keunggulan Test 1', $major->advantages[0]['title']);
        $this->assertContains('Karir 1', $major->career_opportunities);
    }

    public function test_admin_can_upload_and_delete_gallery_photos(): void
    {
        Storage::fake('public');

        $admin = User::where('role', 'admin')->first();
        $major = Major::first();

        $file1 = UploadedFile::fake()->image('praktikum1.jpg');
        $file2 = UploadedFile::fake()->image('praktikum2.png');

        $uploadResponse = $this->actingAs($admin)->post('/admin/sekolah/jurusan/'.$major->id.'/gallery/upload', [
            'photos' => [$file1, $file2],
            'caption' => 'Praktikum Siswa Test',
        ]);

        $uploadResponse->assertRedirect('/admin/dashboard?view=konten_jurusan&major_id='.$major->id);
        $uploadResponse->assertSessionHas('success');

        $this->assertDatabaseHas('jurusan_galeri', [
            'major_id' => $major->id,
            'title' => 'Praktikum Siswa Test',
        ]);

        $galleryItem = MajorGallery::where('major_id', $major->id)->where('title', 'Praktikum Siswa Test')->first();
        $this->assertNotNull($galleryItem);
        $relativePath = str_replace('storage/', '', $galleryItem->image_path);
        Storage::disk('public')->assertExists($relativePath);

        // Delete test
        $deleteResponse = $this->actingAs($admin)->post('/admin/sekolah/jurusan/gallery/'.$galleryItem->id.'/delete');
        $deleteResponse->assertRedirect('/admin/dashboard?view=konten_jurusan&major_id='.$major->id);
        $deleteResponse->assertSessionHas('success');

        $this->assertDatabaseMissing('jurusan_galeri', [
            'id' => $galleryItem->id,
        ]);
        Storage::disk('public')->assertMissing($relativePath);
    }
}
