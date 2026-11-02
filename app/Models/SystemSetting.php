<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    public const CACHE_KEY = 'spmb_system_settings_all';

    /**
     * In-memory memoization untuk mencegah N+1 query saat render > 500 calon siswa.
     *
     * @var array<string, mixed>|null
     */
    protected static ?array $memoizedSettings = null;

    protected $table = 'pengaturan_sistem';

    protected $fillable = [
        'key',
        'value',
        'group',
        'type',
        'label',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            static::clearCache();
        });

        static::deleted(function (): void {
            static::clearCache();
        });
    }

    /**
     * Ambil seluruh pengaturan sistem dari memori / cache (0 query berulang).
     *
     * @return array<string, mixed>
     */
    public static function allCached(): array
    {
        if (static::$memoizedSettings !== null) {
            return static::$memoizedSettings;
        }

        try {
            static::$memoizedSettings = Cache::remember(static::CACHE_KEY, 3600, function (): array {
                return static::query()->pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            static::$memoizedSettings = [];
        }

        return static::$memoizedSettings;
    }

    /**
     * Bersihkan cache pengaturan sistem saat ada pembaruan.
     */
    public static function clearCache(): void
    {
        static::$memoizedSettings = null;

        try {
            Cache::forget(static::CACHE_KEY);
        } catch (\Throwable $e) {
            // Abaikan jika tabel cache belum siap saat migrasi awal
        }
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allCached();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general', string $type = 'text', ?string $label = null): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $value,
                'group' => $group,
                'type' => $type,
                'label' => $label ?? ucwords(str_replace('_', ' ', $key)),
            ]
        );

        static::clearCache();

        return $setting;
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
        $template = ! empty($template) ? $template : static::get('wa_message_template', $defaultTemplate);

        $schoolName = static::get('school_name', 'SMK KARYA NUGRAHA BOYOLALI');
        $studentName = $registration?->biodata?->nama_lengkap ?? $registration?->user?->name ?? 'Calon Siswa';
        $majorName = $registration?->major?->name ?? '-';
        $industry = ! empty($registration?->major?->industry_class) ? " ({$registration->major->industry_class})" : '';
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
            '{jurusan}' => $majorName.$industry,
            '[JURUSAN]' => $majorName.$industry,
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
        $defaultTemplate = 'Halo Panitia SPMB {sekolah}, saya ingin menanyakan perihal informasi pendaftaran siswa baru.';
        $template = ! empty($template) ? $template : static::get('wa_hotline_template', $defaultTemplate);
        $schoolName = static::get('school_name', 'SMK KARYA NUGRAHA BOYOLALI');

        return strtr($template, [
            '{sekolah}' => $schoolName,
            '[SEKOLAH]' => $schoolName,
        ]);
    }

    /**
     * Default ketentuan Surat Pernyataan (Lembar 3 Bukti Pendaftaran).
     */
    public static function defaultStatementLetterRules(): string
    {
        return "1. Lulus SMP / MTs menempati SMK Karya Nugraha Boyolali segera melengkapi segala kekurangan administrasi daftar ulang sesuai dengan program sekolah.\n2. Lulus SMP / MTs tidak menempati SMK Karya Nugraha Boyolali uang titipan tidak kembali.\n3. Tidak Lulus SMP / MTs, uang titipan kembali 100 % dan siap mengundurkan diri.\n4. Besarnya uang titipan Rp .....................................,- ( ......................................................................... ).";
    }

    /**
     * Default catatan / NB Surat Pernyataan (Lembar 3 Bukti Pendaftaran).
     */
    public static function defaultStatementLetterNotes(): string
    {
        return "1. Minimal titipan sebanyak Rp. 500.000,- ( lima ratus ribu rupiah )\n2. Pembayaran Titipan bagi yang diberi kesempatan melanjutkan tanggal _______________ 20___ jam. 08.00 – 12.00 WIB.\n3. Rincian Keuangan dan lain-lain akan diumumkan dikemudian hari.\n4. Khusus yang diterima di TSM, saat Prakerin/PKL siswa wajib/bersedia ditempatkan di wilayah Solo, Yogyakarta dan Semarang.";
    }
}
