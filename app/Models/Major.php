<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Major extends Model
{
    use HasFactory;

    protected $table = 'jurusan';

    protected $fillable = [
        'code',
        'slug',
        'name',
        'industry_class',
        'quota',
        'description',
        'tagline',
        'head_of_major',
        'banner_image',
        'video_url',
        'advantages',
        'career_opportunities',
        'competencies',
        'facilities',
        'icon',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'quota' => 'integer',
            'is_active' => 'boolean',
            'advantages' => 'array',
            'career_opportunities' => 'array',
            'competencies' => 'array',
            'facilities' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Major $major) {
            if (empty($major->slug)) {
                $baseSlug = Str::slug($major->code.'-'.$major->name);
                $major->slug = $baseSlug ?: Str::slug($major->name);
            }
        });
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'major_id');
    }

    public function secondaryRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'major_choice_2_id');
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(MajorGallery::class, 'major_id')->orderBy('sort_order')->orderBy('id');
    }

    public function getRemainingQuotaAttribute(): int
    {
        $acceptedCount = $this->registrations()->where('status', 'accepted')->count();

        return max(0, $this->quota - $acceptedCount);
    }

    /**
     * Konversi URL YouTube (Watch / Short / Share) menjadi Embed URL yang aman.
     */
    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (empty($this->video_url)) {
            return null;
        }

        $url = trim($this->video_url);

        // Jika sudah berbentuk embed
        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        // Format: youtube.com/watch?v=XXXX
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/shorts\/|youtube\.com\/embed\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1].'?rel=0&modestbranding=1';
        }

        return $url;
    }

    /**
     * Dapatkan daftar keunggulan default jika admin belum mengisinya secara khusus.
     */
    public function getEffectiveAdvantagesAttribute(): array
    {
        if (! empty($this->advantages) && is_array($this->advantages)) {
            return $this->advantages;
        }

        return $this->getDefaultAdvantages();
    }

    /**
     * Dapatkan daftar prospek karir default jika admin belum mengisinya.
     */
    public function getEffectiveCareerOpportunitiesAttribute(): array
    {
        if (! empty($this->career_opportunities) && is_array($this->career_opportunities)) {
            return $this->career_opportunities;
        }

        return $this->getDefaultCareerOpportunities();
    }

    /**
     * Dapatkan daftar kompetensi keahlian default jika admin belum mengisinya.
     */
    public function getEffectiveCompetenciesAttribute(): array
    {
        if (! empty($this->competencies) && is_array($this->competencies)) {
            return $this->competencies;
        }

        return $this->getDefaultCompetencies();
    }

    /**
     * Dapatkan fasilitas unggulan default jika admin belum mengisinya.
     */
    public function getEffectiveFacilitiesAttribute(): array
    {
        if (! empty($this->facilities) && is_array($this->facilities)) {
            return $this->facilities;
        }

        return $this->getDefaultFacilities();
    }

    /**
     * Data keunggulan kurikulum & kelas industri terkurasi untuk masing-masing 7 jurusan.
     */
    public function getDefaultAdvantages(): array
    {
        $code = strtoupper($this->code ?? '');
        $class = $this->industry_class ?? '';

        if (str_contains($code, 'PSY') || str_contains($code, 'BSI')) {
            return [
                [
                    'title' => 'Sinkronisasi Kurikulum Bank Syariah Indonesia (BSI)',
                    'description' => 'Materi pembelajaran langsung disinkronkan dengan SOP dan standar perbankan syariah BSI.',
                    'icon' => 'fa-building-columns',
                ],
                [
                    'title' => 'Laboratorium Mini Bank Berbasis Real-System',
                    'description' => 'Praktik langsung simulasi teller, customer service, pembukuan giro, dan tabungan syariah.',
                    'icon' => 'fa-cash-register',
                ],
                [
                    'title' => 'Sertifikasi Kompetensi Keuangan Syariah',
                    'description' => 'Lulusan dibekali sertifikat kompetensi resmi yang diakui industri perbankan nasional.',
                    'icon' => 'fa-certificate',
                ],
                [
                    'title' => 'Peluang Magang & Penyaluran Kerja di BSI',
                    'description' => 'Akses magang industri prioritas serta peluang rekrutmen kerja langsung di jaringan BSI.',
                    'icon' => 'fa-briefcase',
                ],
            ];
        }

        if (str_contains($code, 'TBO') || str_contains($code, 'LAKSANA')) {
            return [
                [
                    'title' => 'Kelas Industri Resmi Karoseri Laksana',
                    'description' => 'Bekerja sama resmi dengan Karoseri Laksana, manufaktur bus terbesar dan termodern di Indonesia.',
                    'icon' => 'fa-bus',
                ],
                [
                    'title' => 'Bengkel Praktikum Standar Pabrikan Otomotif',
                    'description' => 'Dilengkapi spray booth modern, alat ketok bodi canggih, dan mesin las argon standar pabrik.',
                    'icon' => 'fa-wrench',
                ],
                [
                    'title' => 'Instruktur Tamu Ahli Karoseri Laksana',
                    'description' => 'Mendapatkan pelatihan langsung dari para teknisi profesional Karoseri Laksana.',
                    'icon' => 'fa-chalkboard-user',
                ],
                [
                    'title' => 'Jaminan Penyerapan Kerja Industri Karoseri',
                    'description' => 'Lulusan terbaik memiliki jalur karir prioritas di pabrik karoseri dan bengkel bodi terkemuka.',
                    'icon' => 'fa-handshake',
                ],
            ];
        }

        if (str_contains($code, 'TKJ AXIOO') || str_contains($code, 'AXIOO')) {
            return [
                [
                    'title' => 'Axioo Class Program Resmi Terakreditasi A',
                    'description' => 'Kurikulum hardware, laptop repair, dan IT support resmi standar internasional PT Tera Data Indonusa (Axioo).',
                    'icon' => 'fa-laptop-code',
                ],
                [
                    'title' => 'Lab Perakitan & Service Point Axioo',
                    'description' => 'Siswa praktik langsung membongkar, mendiagnosis, dan memperbaiki notebook serta gadget modern.',
                    'icon' => 'fa-microchip',
                ],
                [
                    'title' => 'Sertifikasi Internasional Mikrotik & Axioo',
                    'description' => 'Siswa dibekali sertifikasi resmi MTCNA (MikroTik) dan ACP Certified Technician.',
                    'icon' => 'fa-award',
                ],
                [
                    'title' => 'Teaching Factory & IT Business Unit',
                    'description' => 'Mengasah jiwa kewirausahaan siswa melalui unit bisnis servis laptop dan instalasi jaringan.',
                    'icon' => 'fa-chart-line',
                ],
            ];
        }

        if (str_contains($code, 'TKJ+') || str_contains($code, 'BUSANA')) {
            return [
                [
                    'title' => 'Perpaduan Teknologi Jaringan & Tata Busana Kreatif',
                    'description' => 'Kombinasi unik keahlian teknologi komputer dengan industri fesyen digital & e-commerce apparel.',
                    'icon' => 'fa-shirt',
                ],
                [
                    'title' => 'Studio Desain Mode Digital & Mesin Jahit Industri',
                    'description' => 'Praktik membuat pola busana digital (CAD) dan produksi fesyen dengan mesin jahit otomatis.',
                    'icon' => 'fa-scissors',
                ],
                [
                    'title' => 'E-Commerce & Digital Marketing Apparel',
                    'description' => 'Siswa diajarkan memasarkan hasil karya busana melalui platform digital dan media online.',
                    'icon' => 'fa-bullhorn',
                ],
                [
                    'title' => 'Peluang Wirausaha Mandiri & Konveksi Modern',
                    'description' => 'Mencetak wirausahawan muda mandiri yang siap membuka butik, konveksi, atau agensi IT.',
                    'icon' => 'fa-shop',
                ],
            ];
        }

        if (str_contains($code, 'TKR') || str_contains($code, 'DAIHATSU')) {
            return [
                [
                    'title' => 'Kelas Industri Pintar Bersama Daihatsu (PBD)',
                    'description' => 'Kurikulum otomotif roda empat disinkronkan langsung dengan standar PT Astra Daihatsu Motor.',
                    'icon' => 'fa-car',
                ],
                [
                    'title' => 'Bengkel Praktik Berstandar Dealer Resmi Astra',
                    'description' => 'Dilengkapi car lift hidrolik, engine scanner OBD-II, wheel alignment, dan unit mobil praktik.',
                    'icon' => 'fa-gear',
                ],
                [
                    'title' => 'Sertifikasi Teknisi Daihatsu Basic & Advance',
                    'description' => 'Siswa diuji dan memperoleh sertifikat kompetensi resmi dari Astra Daihatsu Motor.',
                    'icon' => 'fa-certificate',
                ],
                [
                    'title' => 'Rekrutmen Kerja Langsung di Jaringan Bengkel Resmi',
                    'description' => 'Kesempatan rekrutmen teknisi di bengkel resmi Astra Daihatsu, Toyota, dan bengkel modern.',
                    'icon' => 'fa-users-gear',
                ],
            ];
        }

        if (str_contains($code, 'TSM') || str_contains($code, 'YAMAHA')) {
            return [
                [
                    'title' => 'Kelas Khusus Yamaha (Yamaha Technical Academy)',
                    'description' => 'Bekerja sama resmi dengan PT Yamaha Indonesia Motor Mfg untuk kurikulum teknologi sepeda motor terkini.',
                    'icon' => 'fa-motorcycle',
                ],
                [
                    'title' => 'Laboratorium Injeksi Yamaha FI & Diagnostic Tool',
                    'description' => 'Praktik langsung teknologi Fuel Injection (YDT), rem ABS, Variable Valve Actuation (VVA), dan motor listrik.',
                    'icon' => 'fa-screwdriver-wrench',
                ],
                [
                    'title' => 'Sertifikat YTA (Yamaha Technical Academy) Level 1',
                    'description' => 'Sertifikat keahlian resmi Yamaha yang diakui bengkel resmi di seluruh Indonesia.',
                    'icon' => 'fa-medal',
                ],
                [
                    'title' => 'Jalur Rekrutmen Mekanik Dealer Resmi Yamaha',
                    'description' => 'Penyaluran kerja langsung sebagai mekanik andal di jaringan bengkel resmi Yamaha seluruh nusantara.',
                    'icon' => 'fa-handshake-angle',
                ],
            ];
        }

        if (str_contains($code, 'TTT') || str_contains($code, 'TELKOM')) {
            return [
                [
                    'title' => 'Kelas Industri Telekomunikasi Modern PT Telkom',
                    'description' => 'Kurikulum telekomunikasi masa depan bekerja sama langsung dengan PT Telkom Indonesia.',
                    'icon' => 'fa-network-wired',
                ],
                [
                    'title' => 'Lab Praktik Fiber Optik (Splicing & OTDR)',
                    'description' => 'Siswa menguasai penyambungan kabel fiber optik modern (fusion splicer) dan pengukuran sinyal laser.',
                    'icon' => 'fa-tower-broadcast',
                ],
                [
                    'title' => 'Infrastruktur Cloud & Jaringan Seluler 5G',
                    'description' => 'Mempelajari routing enterprise, server cloud, sistem nirkabel, dan perangkat telekomunikasi industri.',
                    'icon' => 'fa-cloud',
                ],
                [
                    'title' => 'Kerjasama Penempatan Kerja di Telkom Group & Provider',
                    'description' => 'Peluang karir langsung di Telkom Akses, vendor telekomunikasi, dan ISP terkemuka.',
                    'icon' => 'fa-satellite-dish',
                ],
            ];
        }

        return [
            [
                'title' => 'Kurikulum Berbasis Industri Nasional',
                'description' => 'Pembelajaran aplikatif dengan proporsi 70% praktik dan 30% teori siap kerja.',
                'icon' => 'fa-award',
            ],
            [
                'title' => 'Fasilitas Bengkel & Laboratorium Lengkap',
                'description' => 'Peralatan praktikum modern yang memenuhi standar kompetensi dunia usaha dan dunia industri.',
                'icon' => 'fa-flask-vial',
            ],
            [
                'title' => 'Sertifikasi Kompetensi BNSP',
                'description' => 'Lulusan diuji oleh Lembaga Sertifikasi Profesi (LSP) P1 berlisensi BNSP.',
                'icon' => 'fa-certificate',
            ],
            [
                'title' => 'Penyaluran Kerja BKK Mitra Industri',
                'description' => 'Bursa Kerja Khusus (BKK) aktif menyalurkan alumni ke perusahaan bonafide sebelum wisuda.',
                'icon' => 'fa-briefcase',
            ],
        ];
    }

    public function getDefaultCareerOpportunities(): array
    {
        $code = strtoupper($this->code ?? '');

        if (str_contains($code, 'PSY') || str_contains($code, 'BSI')) {
            return ['Customer Service Perbankan', 'Teller Bank Syariah', 'Back Office & Administrasi Finansial', 'Account Officer Pembiayaan', 'Staf Koperasi Simpan Pinjam Syariah', 'Wirausahawan Jasa Keuangan'];
        }

        if (str_contains($code, 'TBO') || str_contains($code, 'LAKSANA')) {
            return ['Teknisi Bodi & Rangka Bus/Truk', 'Spesialis Pengecatan (Automotive Painter)', 'Quality Control (QC) Karoseri', 'Teknisi Perakitan Interior Otomotif', 'Estimator Klaim Asuransi Kendaraan', 'Wirausaha Bengkel Cat & Ketok Magic'];
        }

        if (str_contains($code, 'TKJ AXIOO') || str_contains($code, 'AXIOO')) {
            return ['IT Support & Hardware Technician', 'Teknisi Resmi Axioo Service Center', 'Network Administrator (MikroTik/Cisco)', 'Teknisi Jaringan & Server', 'Helpdesk IT Perusahaan', 'Teknopreneur / Wirausaha Servis Komputer'];
        }

        if (str_contains($code, 'TKJ+') || str_contains($code, 'BUSANA')) {
            return ['Perancang Busana Digital (Fashion Designer)', 'Operator Mesin Jahit Industri (Garment)', 'Pengelola Toko Online / E-Commerce Fesyen', 'Staf Administrasi & IT Butik', 'Wirausaha Modiste & Konveksi', 'Content Creator Fesyen'];
        }

        if (str_contains($code, 'TKR') || str_contains($code, 'DAIHATSU')) {
            return ['Mekanik Bengkel Resmi Mobil (Astra Daihatsu/Toyota)', 'Teknisi Tune-Up & Overhaul Mesin', 'Service Advisor Bengkel Otomotif', 'Operator Perakitan Otomotif Pabrikan', 'Teknisi Spooring & Balancing', 'Pengusaha Bengkel Mobil Mandiri'];
        }

        if (str_contains($code, 'TSM') || str_contains($code, 'YAMAHA')) {
            return ['Mekanik Resmi Bengkel Yamaha', 'Teknisi Injeksi Sepeda Motor', 'Kepala Regu / Service Advisor Dealer', 'Teknisi Perakitan Pabrik Motor', 'Pembalap & Mekanik Racing Team', 'Wirausahawan Bengkel Motor & Modifikasi'];
        }

        if (str_contains($code, 'TTT') || str_contains($code, 'TELKOM')) {
            return ['Teknisi Fiber Optik (Splicer & Jointer)', 'Teknisi Instalasi Jaringan Telkom Akses', 'Site Engineer Seluler (BTS)', 'Network Operations Center (NOC) Staff', 'Teknisi ISP & TV Kabel', 'Wirausaha Mitra Jaringan Telekomunikasi'];
        }

        return ['Teknisi Profesional Industri', 'Supervisor Operasional Lapangan', 'Staff Quality Control (QC)', 'Wirausahawan Mandiri', 'Lanjut Kuliah Perguruan Tinggi'];
    }

    public function getDefaultCompetencies(): array
    {
        $code = strtoupper($this->code ?? '');

        if (str_contains($code, 'PSY') || str_contains($code, 'BSI')) {
            return ['Pengelolaan Kas & Teller Otomatis', 'Akuntansi Perbankan Syariah & MYOB', 'Simulasi Akad-Akad Syariah (Mudharabah/Musyarakah)', 'Pelayanan Prima Nasabah (Service Excellence)', 'Digital Banking & Administrasi Kliring'];
        }

        if (str_contains($code, 'TBO') || str_contains($code, 'LAKSANA')) {
            return ['Teknik Ketok Bodi & Perbaikan Rangka Kendaraan', 'Pengecatan Duco & Mixing Warna Standar Pabrik', 'Pengelasan Bodi (Argon, MIG/MAG, TIG)', 'Pemasangan Panel & Kaca Kendaraan Komersial', 'Poles Finis & Detailing Eksterior'];
        }

        if (str_contains($code, 'TKJ AXIOO') || str_contains($code, 'AXIOO')) {
            return ['Perakitan & Perbaikan Komponen Laptop/PC', 'Instalasi Sistem Operasi & Troubleshooting Hardware', 'Konfigurasi Routing MikroTik & Switching', 'Pemasangan Jaringan LAN, WiFi & Mikrotik', 'Administrasi Server Linux & Cloud Computing'];
        }

        if (str_contains($code, 'TKJ+') || str_contains($code, 'BUSANA')) {
            return ['Pembuatan Pola Busana Manual & CAD', 'Teknik Menjahit Busana Wanita, Pria & Anak', 'Penyelesaian Jahitan & Finishing Fesyen', 'Desain Grafis Promosi Produk Busana', 'Pengelolaan Marketplace & Digital Branding'];
        }

        if (str_contains($code, 'TKR') || str_contains($code, 'DAIHATSU')) {
            return ['Perawatan & Overhaul Mesin Mobil (EFI/Bensin/Diesel)', 'Pemeliharaan Sistem Sasis & Pemindah Tenaga', 'Diagnosa Kelistrikan Bodi & AC Mobil Modern', 'Penggunaan Diagnostic Scanner OBD-II & Engine Analyzer', 'Spooring Balancing & Servis Rem ABS'];
        }

        if (str_contains($code, 'TSM') || str_contains($code, 'YAMAHA')) {
            return ['Servis Berkala Sepeda Motor Karburator & Injeksi (FI)', 'Troubleshooting Menggunakan Yamaha Diagnostic Tool (YDT)', 'Perawatan Sistem Kelistrikan & Starter Otomatis', 'Pemeliharaan Sistem CVT Motor Matic', 'Overhaul Mesin 4-Tak & Sistem Pendingin Cair'];
        }

        if (str_contains($code, 'TTT') || str_contains($code, 'TELKOM')) {
            return ['Penyambungan Kabel Fiber Optik (Fusion Splicing)', 'Pengukuran Redaman Kabel Optik dengan OTDR & OPM', 'Instalasi Jaringan Akses Tembaga & Nirkabel', 'Konfigurasi Perangkat Router & Switch Telekomunikasi', 'Pemeliharaan Tower Antena & Perangkat Transmisi Seluler'];
        }

        return ['Kompetensi Teori Kejuruan Terpadu', 'Praktikum Bengkel & Laboratorium Berstandar Industri', 'Keselamatan & Kesehatan Kerja (K3LH)', 'Budaya Kerja Industri 5S / 5R'];
    }

    public function getDefaultFacilities(): array
    {
        $code = strtoupper($this->code ?? '');

        if (str_contains($code, 'PSY') || str_contains($code, 'BSI')) {
            return ['Laboratorium Mini Bank BSI Ber-AC', 'Ruang Simulasi Teller & Customer Service', 'Perangkat Hitung Uang & Detektor Uang Palsu', 'Komputer Kasir Perbankan Terintegrasi'];
        }

        if (str_contains($code, 'TBO') || str_contains($code, 'LAKSANA')) {
            return ['Spray Booth Pengecatan Berstandar Pabrik', 'Mesin Las Argon & Spot Welding Modern', 'Area Ketok Bodi & Perataan Panel', 'Bahan Cat & Kompresor Tekanan Tinggi Berkapasitas Besar'];
        }

        if (str_contains($code, 'TKJ AXIOO') || str_contains($code, 'AXIOO')) {
            return ['Axioo Smart Classroom & Service Center', 'Meja Praktik Perakitan Antistatik ESD', 'Rack Server & Routerboard MikroTik Enterprise', 'Alat Diagnostik Motherboard & Solder Stasiun Canggih'];
        }

        if (str_contains($code, 'TKJ+') || str_contains($code, 'BUSANA')) {
            return ['Studio Busana Kreatif & Meja Pola Luas', 'Mesin Jahit Industri High-Speed & Obras Otomatis', 'Mesin Bordir Komputerisasi', 'Laboratorium Desain Mode & Komputer Multimedia'];
        }

        if (str_contains($code, 'TKR') || str_contains($code, 'DAIHATSU')) {
            return ['Bengkel Otomotif Standar Bengkel Resmi Astra', 'Car Lift 2-Post & 4-Post Canggih', 'Engine Scanner OBD-II Multi-Brand', 'Unit Mobil Praktik Lengkap (Daihatsu & Toyota)'];
        }

        if (str_contains($code, 'TSM') || str_contains($code, 'YAMAHA')) {
            return ['Bengkel Praktik Standar Yamaha Technical Academy', 'Bike Lift Pneumatik 8 Unit', 'Yamaha Diagnostic Tool (YDT) Komputerisasi', 'Unit Sepeda Motor Praktik Injeksi & Matic Terkini'];
        }

        if (str_contains($code, 'TTT') || str_contains($code, 'TELKOM')) {
            return ['Laboratorium Fiber Optik Berstandar PT Telkom', 'Fusion Splicer Fiber Optik Presisi Tinggi', 'Optical Time Domain Reflectometer (OTDR)', 'Miniatur Tower Seluler & Jaringan FTTH (Fiber to the Home)'];
        }

        return ['Bengkel Praktikum Mandiri Terlengkap', 'Ruang Teori Ber-AC & Proyektor Multimedia', 'Alat Pengaman Praktikum (APD) Lengkap', 'Akses Internet WiFi Cepat untuk Riset Belajar'];
    }
}
