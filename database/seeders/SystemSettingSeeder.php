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
            // General & Profile Settings (Matching User Screenshot)
            ['key' => 'school_npsn', 'value' => '20308458', 'group' => 'school_profile', 'type' => 'text', 'label' => 'NPSN'],
            ['key' => 'school_name', 'value' => 'SMK KARYA NUGRAHA BOYOLALI', 'group' => 'school_profile', 'type' => 'text', 'label' => 'Nama Sekolah'],
            ['key' => 'school_level', 'value' => 'SMK', 'group' => 'school_profile', 'type' => 'text', 'label' => 'Jenjang Sekolah'],
            ['key' => 'school_status', 'value' => 'Swasta', 'group' => 'school_profile', 'type' => 'text', 'label' => 'Status Sekolah'],
            ['key' => 'school_tagline', 'value' => 'SMK PK (Pusat Keunggulan)', 'group' => 'school_profile', 'type' => 'text', 'label' => 'Tagline Sekolah'],
            ['key' => 'school_address', 'value' => 'Jl. Tentara Pelajar No. 1, Boyolali, Jawa Tengah', 'group' => 'school_profile', 'type' => 'textarea', 'label' => 'Alamat Sekolah'],
            ['key' => 'school_phone', 'value' => '085290944090', 'group' => 'school_profile', 'type' => 'text', 'label' => 'No. Telepon / WhatsApp'],
            ['key' => 'school_email', 'value' => 'smkknbi@gmail.com', 'group' => 'school_profile', 'type' => 'text', 'label' => 'Email Resmi'],
            ['key' => 'school_website', 'value' => 'https://spmb.smkknbi.sch.id', 'group' => 'school_profile', 'type' => 'text', 'label' => 'Website'],
            ['key' => 'school_principal_name', 'value' => 'Drs. H. Mulyadi, M.Pd.', 'group' => 'school_profile', 'type' => 'text', 'label' => 'Nama Kepala Sekolah'],
            ['key' => 'school_principal_nip', 'value' => '19680512 199403 1 007', 'group' => 'school_profile', 'type' => 'text', 'label' => 'NIP Kepala Sekolah'],
            ['key' => 'registration_is_open', 'value' => '1', 'group' => 'general', 'type' => 'boolean', 'label' => 'Status Pendaftaran Dibuka'],
            
            // Landing Page Content
            ['key' => 'landing_hero_title', 'value' => 'Selamat Datang Di SPMB ONLINE', 'group' => 'landing_page', 'type' => 'text', 'label' => 'Judul Hero Landing'],
            ['key' => 'landing_hero_subtitle', 'value' => 'Pendaftaran Peserta Didik Baru SMK Karya Nugraha Boyolali telah dibuka. Siapkan masa depanmu bersama SMK Pusat Keunggulan.', 'group' => 'landing_page', 'type' => 'textarea', 'label' => 'Subjudul Hero Landing'],
            ['key' => 'announcement_whatsapp_info', 'value' => 'Untuk calon siswa baru pada saat mendaftar diwajibkan menggunakan nomor WhatsApp yang AKTIF karena Panitia SPMB akan menghubungi melalui WhatsApp.', 'group' => 'landing_page', 'type' => 'textarea', 'label' => 'Pengumuman WhatsApp'],
            
            // Kop Surat Settings (Untuk Cetak Bukti Pendaftaran & Surat Pengumuman)
            ['key' => 'cop_school_name', 'value' => "YAYASAN PENDIDIKAN KARYA NUGRAHA BOYOLALI\nSMK KARYA NUGRAHA BOYOLALI", 'group' => 'cop_surat', 'type' => 'textarea', 'label' => 'Header Kop Surat'],
            ['key' => 'cop_school_status', 'value' => 'SMK PUSAT KEUNGGULAN (SMK PK) • STATUS TERAKREDITASI "A" • NPSN: 20308458', 'group' => 'cop_surat', 'type' => 'text', 'label' => 'Sub-kop Akreditasi & Status'],
            ['key' => 'cop_school_address', 'value' => 'Jl. Tentara Pelajar No. 1, Boyolali, Jawa Tengah 57314', 'group' => 'cop_surat', 'type' => 'textarea', 'label' => 'Alamat Kop Surat'],
            ['key' => 'cop_school_contact', 'value' => 'Telp/WA: 0852-9094-4090 | Email: smkknbi@gmail.com | Website: spmb.smkknbi.sch.id', 'group' => 'cop_surat', 'type' => 'text', 'label' => 'Kontak Kop Surat'],
            ['key' => 'cop_image_banner', 'value' => null, 'group' => 'cop_surat', 'type' => 'file', 'label' => 'Banner Kop Surat Jadi (Opsional)'],
            
            // Panitia & Tanda Tangan Digital
            ['key' => 'hotline_panitia', 'value' => '085290944090', 'group' => 'committee', 'type' => 'text', 'label' => 'Nomor Hotline WhatsApp Panitia'],
            ['key' => 'hotline_panitia_name', 'value' => 'Customer Service SPMB KNBI', 'group' => 'committee', 'type' => 'text', 'label' => 'Nama Layanan Hotline'],
            ['key' => 'hotline_panitia_hours', 'value' => 'Senin - Sabtu (08.00 - 15.00 WIB)', 'group' => 'committee', 'type' => 'text', 'label' => 'Jam Layanan Hotline'],
            ['key' => 'chairman_name', 'value' => 'H. Bambang Wahyudi, S.Pd., M.Kom.', 'group' => 'committee', 'type' => 'text', 'label' => 'Nama Ketua Panitia SPMB'],
            ['key' => 'chairman_nip', 'value' => 'NIP. 19780514 200501 1 008', 'group' => 'committee', 'type' => 'text', 'label' => 'NIP/NBM Ketua Panitia'],
            ['key' => 'chairman_signature', 'value' => null, 'group' => 'committee', 'type' => 'file', 'label' => 'File TTD Digital Ketua Panitia'],
            ['key' => 'school_logo', 'value' => null, 'group' => 'branding', 'type' => 'file', 'label' => 'Logo Resmi Sekolah'],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
