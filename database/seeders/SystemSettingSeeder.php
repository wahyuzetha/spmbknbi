<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'school_name', 'value' => 'SMK KARYA NUGRAHA BOYOLALI', 'group' => 'general', 'type' => 'text', 'label' => 'Nama Sekolah'],
            ['key' => 'school_tagline', 'value' => 'SMK PK (Pusat Keunggulan)', 'group' => 'general', 'type' => 'text', 'label' => 'Tagline Sekolah'],
            ['key' => 'school_email', 'value' => 'smkknbi@gmail.com', 'group' => 'general', 'type' => 'text', 'label' => 'Email Resmi'],
            ['key' => 'school_phone', 'value' => '085290944090', 'group' => 'general', 'type' => 'text', 'label' => 'No. Telepon / WhatsApp'],
            ['key' => 'school_address', 'value' => 'Jl. Pandanaran No. 123, Boyolali, Jawa Tengah', 'group' => 'general', 'type' => 'textarea', 'label' => 'Alamat Sekolah'],
            ['key' => 'registration_is_open', 'value' => '1', 'group' => 'general', 'type' => 'boolean', 'label' => 'Status Pendaftaran Dibuka'],
            
            // Landing Page Content
            ['key' => 'landing_hero_title', 'value' => 'Selamat Datang Di SPMB ONLINE', 'group' => 'landing_page', 'type' => 'text', 'label' => 'Judul Hero Landing'],
            ['key' => 'landing_hero_subtitle', 'value' => 'Pendaftaran Peserta Didik Baru SMK Karya Nugraha Boyolali telah dibuka. Siapkan masa depanmu bersama SMK Pusat Keunggulan.', 'group' => 'landing_page', 'type' => 'textarea', 'label' => 'Subjudul Hero Landing'],
            ['key' => 'announcement_whatsapp_info', 'value' => 'Untuk calon siswa baru pada saat mendaftar diwajibkan menggunakan nomor WhatsApp yang AKTIF karena Panitia SPMB akan menghubungi melalui WhatsApp.', 'group' => 'landing_page', 'type' => 'textarea', 'label' => 'Pengumuman WhatsApp'],
            
            // Kop Surat Settings (Untuk Cetak Bukti Pendaftaran & Surat Pengumuman)
            ['key' => 'cop_school_name', 'value' => 'YAYASAN KARYA NUGRAHA BOYOLALI\nSMK KARYA NUGRAHA BOYOLALI', 'group' => 'cop_surat', 'type' => 'textarea', 'label' => 'Header Kop Surat'],
            ['key' => 'cop_school_address', 'value' => 'Jl. Tentara Pelajar No. 1, Boyolali, Jawa Tengah 57314 | Telp/WA: 0852-9094-4090 | Website: smkknbi.sch.id', 'group' => 'cop_surat', 'type' => 'textarea', 'label' => 'Alamat & Kontak Kop Surat'],
            ['key' => 'cop_letterhead_code', 'value' => 'NPSN: 20307718 | Akreditasi: A', 'group' => 'cop_surat', 'type' => 'text', 'label' => 'Informasi Akreditasi/NPSN'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
