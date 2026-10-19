<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'pengaturan_sistem';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
    ];

    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text', ?string $label = null): self
    {
        return static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'label' => $label ?? ucwords(str_replace('_', ' ', $key)),
            ]
        );
    }

    /**
     * Template default pesan WhatsApp calon siswa.
     */
    public static function defaultWaTemplate(): string
    {
        return "Halo {nama}, kami dari Panitia SPMB {sekolah}.\n\nBerikut rincian pendaftaran Anda:\n- No. Pendaftaran: {no_pendaftaran}\n- Jurusan: {jurusan}\n- Asal Sekolah: {asal_sekolah}\n- Status: {status}\n\nAkun Login Anda:\n- Username: {username}\n- Password: {password}\n\nUntuk informasi selengkapnya atau konfirmasi berkas, silakan hubungi hotline: {hotline}.\nTerima kasih!";
    }

    /**
     * Format template pesan WhatsApp otomatis ke calon siswa dengan variabel dinamis.
     */
    public static function formatWaMessage(?Registration $registration = null, ?string $template = null): string
    {
        $defaultTemplate = static::defaultWaTemplate();
        $template = !empty($template) ? $template : static::get('wa_message_template', $defaultTemplate);

        $schoolName = static::get('school_name', 'SMK KARYA NUGRAHA BOYOLALI');
        $studentName = $registration?->biodata?->nama_lengkap ?? $registration?->user?->name ?? 'Calon Siswa';
        $majorName = $registration?->major?->name ?? '-';
        $industry = !empty($registration?->major?->industry_class) ? " ({$registration->major->industry_class})" : '';
        $asalSekolah = $registration?->school?->asal_sekolah ?? '-';
        $statusText = match ($registration?->status) {
            'accepted' => 'Diterima',
            'verified' => 'Terverifikasi',
            'rejected' => 'Ditolak / Perlu Perbaikan',
            default => 'Pending (Menunggu Seleksi)',
        };

        $replacements = [
            '{nama}' => $studentName,
            '[NAMA]' => $studentName,
            '{no_pendaftaran}' => $registration?->registration_number ?? '-',
            '[NO_PENDAFTARAN]' => $registration?->registration_number ?? '-',
            '{jurusan}' => $majorName . $industry,
            '[JURUSAN]' => $majorName . $industry,
            '{status}' => $statusText,
            '[STATUS]' => $statusText,
            '{asal_sekolah}' => $asalSekolah,
            '[ASAL_SEKOLAH]' => $asalSekolah,
            '{sekolah}' => $schoolName,
            '[SEKOLAH]' => $schoolName,
            '{username}' => $registration?->user?->username ?? $registration?->user?->phone ?? '-',
            '[USERNAME]' => $registration?->user?->username ?? $registration?->user?->phone ?? '-',
            '{password}' => $registration?->user?->raw_password ?? '12345',
            '[PASSWORD]' => $registration?->user?->raw_password ?? '12345',
            '{hotline}' => static::get('hotline_panitia', '085290944090'),
            '[HOTLINE]' => static::get('hotline_panitia', '085290944090'),
        ];

        return strtr($template, $replacements);
    }

    /**
     * Format template pesan WhatsApp calon siswa ke Hotline Panitia SPMB.
     */
    public static function formatWaHotlineMessage(?string $template = null): string
    {
        $defaultTemplate = "Halo Panitia SPMB {sekolah}, saya ingin menanyakan perihal informasi pendaftaran siswa baru.";
        $template = !empty($template) ? $template : static::get('wa_hotline_template', $defaultTemplate);
        $schoolName = static::get('school_name', 'SMK KARYA NUGRAHA BOYOLALI');

        return strtr($template, [
            '{sekolah}' => $schoolName,
            '[SEKOLAH]' => $schoolName,
        ]);
    }
}

