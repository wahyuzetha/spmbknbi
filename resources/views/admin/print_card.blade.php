<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran | {{ $registration->biodata->nama_lengkap ?? $registration->user->name ?? 'Calon Siswa' }}</title>
    
    <!-- Google Fonts: Plus Jakarta Sans untuk Tipografi Modern & Tajam -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Standar Ukuran Kertas F4 / Folio Indonesia: 215mm x 330mm */
        @page {
            size: 215mm 330mm;
            margin: 10mm 14mm 10mm 14mm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #090d16; /* Deep black untuk cetak ekstra tajam */
            line-height: 1.45;
        }

        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }

        .font-doc-serif {
            font-family: 'Times New Roman', Times, Cambria, Georgia, serif;
        }

        @media print {
            html, body {
                width: 215mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
                font-size: 13px !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none !important;
            }
            .sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                min-height: auto !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
            .page-break {
                page-break-before: always !important;
                break-before: page !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
                visibility: hidden !important;
            }
        }

        @media screen {
            body {
                background-color: #0b1120;
                background-image: radial-gradient(#1e293b 1px, transparent 1px);
                background-size: 24px 24px;
                padding: 32px 16px;
            }
            .sheet {
                background: #ffffff;
                width: 215mm;
                min-height: 330mm;
                margin: 0 auto 36px auto;
                padding: 12mm 16mm;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.1);
                box-sizing: border-box;
                border-radius: 8px;
                position: relative;
            }
        }

        .dotted-fill {
            border-bottom: 1px dotted #64748b;
            display: inline-block;
            min-height: 1.2em;
            vertical-align: bottom;
        }
    </style>
</head>
<body class="antialiased">

    @php
        // Pastikan settings selalu berupa array / key-value
        $settings = $settings ?? $globalSettings ?? [];

        // Helper Data Siswa
        $bio = $registration->biodata;
        $sch = $registration->school;
        $prt = $registration->parent ?? $registration->parents;
        $usr = $registration->user ?? $registration->siswa;
        $academicYearName = $registration->academicYear->name ?? $academicYear->name ?? ($settings['academic_year'] ?? '2025/2026');
        $pasFoto = $registration->documents ? $registration->documents->where('document_type', 'pas_foto')->first() : null;

        // Data Tanggal & Bahasa Indonesia
        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];
        $bulanIndo = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];

        $formatTgl = function($date) use ($bulanIndo) {
            if(!$date) return '-';
            try {
                $d = \Carbon\Carbon::parse($date);
                return $d->day . ' ' . ($bulanIndo[$d->month] ?? $d->format('F')) . ' ' . $d->year;
            } catch (\Exception $e) {
                return $date;
            }
        };

        $formatHariTgl = function($date) use ($hariIndo, $bulanIndo) {
            if(!$date) return '-';
            try {
                $d = \Carbon\Carbon::parse($date);
                $h = $hariIndo[$d->format('l')] ?? $d->format('l');
                $b = $bulanIndo[$d->month] ?? $d->format('F');
                return $h . ', ' . $d->day . ' ' . $b . ' ' . $d->year;
            } catch (\Exception $e) {
                return $date;
            }
        };

        $namaSiswa = $bio->nama_lengkap ?? $usr->name ?? '-';
        $tempatLahir = $bio->tempat_lahir ?? '-';
        $tanggalLahir = $bio?->tanggal_lahir ? $formatTgl($bio->tanggal_lahir) : '-';
        $jenisKelamin = ($bio?->jenis_kelamin ?? 'L') === 'L' ? 'Laki-laki' : 'Perempuan';
        $nisn = $usr?->nisn ?? $bio?->nik ?? '-';
        $agama = $bio?->agama ?? '-';
        $asalSekolah = $sch?->asal_sekolah ?? '-';
        $namaAyah = $prt?->nama_ayah ?? '-';
        $namaIbu = $prt?->nama_ibu ?? '-';
        $pekerjaanAyah = $prt?->pekerjaan_ayah ?? '-';
        $pekerjaanIbu = $prt?->pekerjaan_ibu ?? '-';
        
        // Alamat Lengkap
        $alamatArray = array_filter([
            $bio?->alamat_jalan,
            $bio?->desa ? 'Ds. ' . $bio->desa : null,
            $bio?->kecamatan ? 'Kec. ' . $bio->kecamatan : null,
            $bio?->kabupaten
        ]);
        $alamatLengkap = !empty($alamatArray) ? implode(', ', $alamatArray) : ($bio?->alamat ?? '-');

        $noHp = $bio->no_hp ?? $usr->phone ?? '-';
        $email = $bio->email ?? $usr->email ?? '-';
        $rekomendasi = $registration->recommendation_by ?? '-';

        // Tanggal Dokumen
        $tglDaftar = $registration->created_at ?? now();
        $tanggalCetak = $formatTgl($tglDaftar);
        $hariTanggalSeleksi = $formatHariTgl($tglDaftar);
        
        // Tanggal Tindak Lanjut (1 minggu setelah seleksi)
        $tglTindakLanjut = \Carbon\Carbon::parse($tglDaftar)->addDays(7);
        $hariTanggalTindakLanjut = $formatHariTgl($tglTindakLanjut);

        // Jurusan & Kelas Industri
        $jurusanNama = $registration->major->name ?? '-';
        $rawKelas = $registration->major->industry_class ?? '';
        $jurusanKelas = (!empty($rawKelas) && !str_contains($jurusanNama, $rawKelas)) ? ' (' . $rawKelas . ')' : '';
        $jurusanWithClass = $jurusanNama . $jurusanKelas;

        // Data Resmi Sekolah & Panitia dari Pengaturan Admin
        $schoolName = $settings['school_name'] ?? 'SMK KARYA NUGRAHA BOYOLALI';
        $schoolAddress = $settings['school_address'] ?? 'Jl. Tentara Pelajar No. 1, Boyolali, Jawa Tengah';
        $schoolCity = $settings['school_city'] ?? 'Boyolali';
        $kopBanner = $settings['cop_image_banner'] ?? $copImageBanner ?? null;
        $chairmanName = $settings['chairman_name'] ?? 'Ketua Panitia SPMB';

        // Format Surat Pernyataan (Lembar 3) dari Pengaturan Admin
        $statementTitle = $settings['statement_letter_title'] ?? 'SURAT PERNYATAAN';
        $statementParentHeader = $settings['statement_letter_parent_header'] ?? 'Yang bertanda tangan dibawah ini, saya orang tua :';
        $statementStudentHeader = $settings['statement_letter_student_header'] ?? 'Sebagai orang tua/ wali murid dari calon siswa';
        $statementIntroRaw = $settings['statement_letter_intro'] ?? 'Sehubungan dengan diberikannya kesempatan kepada anak saya sebagai calon siswa Jalur PMDK SMK Karya Nugraha Tahun {tahun_pelajaran}, saya menyatakan setuju dan titip administrasi keuangan dengan ketentuan apabila anak saya:';
        $statementIntro = strtr($statementIntroRaw, [
            '{tahun_pelajaran}' => $academicYearName,
            '{sekolah}' => $schoolName,
        ]);
        $statementRules = $settings['statement_letter_rules'] ?? \App\Models\SystemSetting::defaultStatementLetterRules();
        $statementClosing = $settings['statement_letter_closing'] ?? 'Demikian pernyataan ini kami buat, dengan penuh kesadaran dan tanpa paksaan dari pihak manapun, untuk dapat digunakan sebagaimana mestinya.';
        $statementCity = $settings['statement_letter_city'] ?? ($schoolCity ?: 'Boyolali');
        $statementDate = $settings['statement_letter_date'] ?? '_____________ 20….';
        $statementPrincipalTitle = $settings['statement_letter_principal_title'] ?? 'Kepala Sekolah';
        $statementPrincipalName = $settings['statement_letter_principal_name'] ?? 'NUR WAKHID ROMDHONI, SE';
        $statementNotes = $settings['statement_letter_notes'] ?? \App\Models\SystemSetting::defaultStatementLetterNotes();
    @endphp

    <!-- FLOATING TOP BAR MODERN (HANYA MUNCUL DI LAYAR SCREEN) -->
    <div class="no-print max-w-[215mm] mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 bg-slate-900/95 backdrop-blur-md p-4 rounded-2xl border border-slate-700/80 shadow-2xl text-white">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div>
                <span class="text-xs font-bold text-white block">
                    Pratinjau Bukti Pendaftaran (Standar Kertas F4)
                </span>
                <span class="text-[10px] text-slate-400">
                    3 Halaman (Termasuk Lembar Surat Pernyataan) &bull; Rasio 215mm x 330mm &bull; Siap Dicetak / Disimpan ke PDF
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-900/30 cursor-pointer">
                <i class="fa-solid fa-print text-sm"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- LEMBAR F4: HALAMAN 1 (BUKTI PENDAFTARAN & BIODATA SISWA)                                  -->
    <!-- ========================================================================================= -->
    <div class="sheet">

        <!-- KOP SURAT RESMI -->
        <div class="kop-header mb-3.5 pb-2.5 border-b-[3px] border-double border-slate-900">
            @if(!empty($kopBanner))
                <img src="{{ asset($kopBanner) }}" alt="Kop Surat Resmi" class="w-full h-auto max-h-32 object-contain mx-auto">
            @else
                <div class="flex items-center justify-between gap-4 py-1">
                    <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                        @if(!empty($settings['school_logo'] ?? $schoolLogo))
                            <img src="{{ asset($settings['school_logo'] ?? $schoolLogo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        @else
                            <div class="w-16 h-16 rounded-full border-2 border-emerald-600 flex items-center justify-center font-black text-emerald-700 text-sm">
                                SPMB
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 text-center font-sans">
                        @if(!empty($settings['cop_school_name']))
                            <h2 class="text-[18px] font-black tracking-wide text-slate-950 uppercase my-0.5 whitespace-pre-line leading-tight">{!! nl2br(e($settings['cop_school_name'])) !!}</h2>
                        @else
                            <h2 class="text-[19px] font-black tracking-wide text-slate-950 uppercase my-0.5 leading-tight">{{ $schoolName }}</h2>
                        @endif
                        @if(!empty($settings['cop_school_status']))
                            <h3 class="text-xs font-black tracking-wider uppercase text-slate-800">{{ $settings['cop_school_status'] }}</h3>
                        @endif
                        <p class="text-[11.5px] font-medium text-slate-700 mt-1 leading-snug">
                            Alamat: {{ $settings['cop_school_address'] ?? $schoolAddress }}<br>
                            Telp/WA: {{ $settings['hotline_panitia'] ?? ($settings['school_phone'] ?? '085290944090') }} | Email: {{ $settings['school_email'] ?? 'smkknbi@gmail.com' }}
                        </p>
                    </div>
                    <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                        @if(!empty($settings['school_tagline']))
                            <div class="px-2.5 py-1.5 bg-blue-700 text-white font-extrabold text-[11px] rounded text-center tracking-tight shadow-xs">
                                {{ $settings['school_tagline'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- ================= JUDUL DOKUMEN & BADGE NOMOR + JURUSAN ================= -->
        <div class="text-center my-3">
            <h1 class="text-[16.5px] font-black uppercase text-slate-950 leading-snug tracking-tight">
                BUKTI PENDAFTARAN CALON<br>
                SISWA BARU {{ strtoupper($schoolName) }}<br>
                SISTEM ONLINE &bull; TAHUN PELAJARAN {{ $academicYearName }}
            </h1>
            
            <!-- NOMOR PENDAFTARAN & JURUSAN DI BAWAHNYA -->
            <div class="mt-2.5 inline-flex flex-col items-center bg-slate-50 border border-slate-300 rounded-xl px-7 py-2.5 shadow-xs min-w-[340px]">
                <div class="text-[13.5px] font-bold text-slate-800 tracking-wide">
                    Nomor Peserta : <span class="font-mono-code font-black text-slate-950 text-[15px] tracking-wider">{{ $registration->registration_number }}</span>
                </div>
                <div class="mt-1.5 pt-1.5 border-t border-slate-200 w-full flex items-center justify-center gap-2 text-xs font-bold text-slate-800">
                    <span class="text-slate-600 font-medium">Kompetensi Keahlian :</span>
                    <span class="font-black text-blue-900 uppercase">{{ $jurusanWithClass }}</span>
                </div>
            </div>
        </div>

        <!-- ================= TABEL DATA LENGKAP SISWA (1 - 13) ================= -->
        <div class="mt-3.5 text-[13px] text-slate-950">
            <table class="w-full border-collapse">
                <tbody>
                    <tr class="border-b border-slate-100/80">
                        <td class="w-7 py-1 align-top font-bold text-slate-600">1.</td>
                        <td class="w-56 py-1 align-top font-semibold text-slate-800">Nama Lengkap</td>
                        <td class="w-4 py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-black text-slate-950 uppercase tracking-tight text-[13.5px]">{{ $namaSiswa }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">2.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">Tempat Lahir</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold capitalize">{{ $tempatLahir }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">3.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">Tanggal Lahir</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold">{{ $tanggalLahir }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">4.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">Jenis Kelamin</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold">{{ $jenisKelamin }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">5.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">NISN / NIK</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-mono-code font-bold text-slate-950 tracking-wide">{{ $nisn }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">6.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">Agama</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold">{{ $agama }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">7.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">Sekolah Asal(SMP/MTs)</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-bold uppercase text-slate-950">{{ $asalSekolah }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 align-top font-bold text-slate-600">8.</td>
                        <td class="py-1 align-top font-bold text-slate-800" colspan="3">Nama Orang Tua</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td></td>
                        <td class="py-1 pl-6 align-top font-medium text-slate-700 flex items-center gap-2">
                            <span class="text-slate-400 text-xs">&bull;</span> Ayah
                        </td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold text-slate-900">{{ $namaAyah }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td></td>
                        <td class="py-1 pl-6 align-top font-medium text-slate-700 flex items-center gap-2">
                            <span class="text-slate-400 text-xs">&bull;</span> Ibu
                        </td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold text-slate-900">{{ $namaIbu }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 align-top font-bold text-slate-600">9.</td>
                        <td class="py-1 align-top font-bold text-slate-800" colspan="3">Pekerjaan Orang Tua/Wali</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td></td>
                        <td class="py-1 pl-6 align-top font-medium text-slate-700 flex items-center gap-2">
                            <span class="text-slate-400 text-xs">&bull;</span> Ayah
                        </td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold text-slate-900">{{ $pekerjaanAyah }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td></td>
                        <td class="py-1 pl-6 align-top font-medium text-slate-700 flex items-center gap-2">
                            <span class="text-slate-400 text-xs">&bull;</span> Ibu
                        </td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold text-slate-900">{{ $pekerjaanIbu }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">10.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">Alamat Tempat Tinggal</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top leading-relaxed font-semibold text-slate-900">{{ $alamatLengkap }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">11.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">No. WhatsApp / HP Aktif</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-mono-code font-bold text-slate-950">{{ $noHp }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">12.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">Alamat Email</td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold text-slate-800">{{ $email }}</td>
                    </tr>
                    <tr class="border-b border-slate-100/80">
                        <td class="py-1 align-top font-bold text-slate-600">13.</td>
                        <td class="py-1 align-top font-semibold text-slate-800">
                            Informasi/ Rekomendasi<br>Dari
                        </td>
                        <td class="py-1 align-top text-center font-bold text-slate-500">:</td>
                        <td class="py-1 align-top font-semibold text-slate-900">{{ $rekomendasi }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ================= BAGIAN BAWAH HALAMAN 1 (PAS FOTO & TTD) ================= -->
        <div class="mt-5 pt-3 flex items-end justify-between border-t border-slate-200">
            
            <!-- KIRI: KOTAK PAS FOTO 3X4 -->
            <div class="flex flex-col items-center">
                <div class="w-28 h-36 border-2 border-slate-300 rounded-xl bg-slate-50 flex flex-col items-center justify-center p-1.5 overflow-hidden shadow-xs relative">
                    @if($pasFoto)
                        <img src="{{ asset('storage/' . $pasFoto->file_path) }}" alt="Pas Foto" class="w-full h-full object-cover rounded-lg">
                    @else
                        <div class="w-full h-full border border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center text-slate-400 p-2 text-center bg-white">
                            <i class="fa-solid fa-camera text-slate-300 text-2xl mb-1.5"></i>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider leading-tight">Pas Foto<br>3 x 4</span>
                        </div>
                    @endif
                </div>
                <span class="text-[10px] font-bold text-slate-500 mt-1.5 uppercase tracking-wider">Foto Calon Siswa</span>
            </div>

            <!-- KANAN: TITIMANGSA & TTD RESMI KETUA PANITIA -->
            <div class="text-center w-64 pb-1">
                <p class="text-[13px] text-slate-800 font-semibold">{{ $schoolCity }}, {{ $tanggalCetak }}</p>
                <p class="text-[13px] font-bold text-slate-950 mt-0.5 tracking-wide">Panitia SPMB,</p>
                
                <div class="h-20 flex items-center justify-center my-1 relative">
                    @if(!empty($settings['chairman_signature']))
                        <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD" class="max-h-20 object-contain">
                    @else
                        <div class="h-16 w-36"></div>
                    @endif
                </div>

                <p class="text-[13px] font-black text-slate-950 uppercase tracking-wide underline underline-offset-4">{{ $chairmanName }}</p>
            </div>

        </div>

    </div>

    <!-- ========================================================================================= -->
    <!-- LEMBAR F4: HALAMAN 2 (SURAT PEMBERITAHUAN SELEKSI & DITERIMA)                             -->
    <!-- ========================================================================================= -->
    <div class="page-break"></div>
    <div class="sheet">

        <!-- KOP SURAT RESMI (SAMA PERSIS DENGAN HALAMAN 1) -->
        <div class="kop-header mb-3.5 pb-2.5 border-b-[3px] border-double border-slate-900">
            @if(!empty($kopBanner))
                <img src="{{ asset($kopBanner) }}" alt="Kop Surat Resmi" class="w-full h-auto max-h-32 object-contain mx-auto">
            @else
                <div class="flex items-center justify-between gap-4 py-1">
                    <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                        @if(!empty($settings['school_logo'] ?? $schoolLogo))
                            <img src="{{ asset($settings['school_logo'] ?? $schoolLogo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        @else
                            <div class="w-16 h-16 rounded-full border-2 border-emerald-600 flex items-center justify-center font-black text-emerald-700 text-sm">
                                SPMB
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 text-center font-sans">
                        @if(!empty($settings['cop_school_name']))
                            <h2 class="text-[18px] font-black tracking-wide text-slate-950 uppercase my-0.5 whitespace-pre-line leading-tight">{!! nl2br(e($settings['cop_school_name'])) !!}</h2>
                        @else
                            <h2 class="text-[19px] font-black tracking-wide text-slate-950 uppercase my-0.5 leading-tight">{{ $schoolName }}</h2>
                        @endif
                        @if(!empty($settings['cop_school_status']))
                            <h3 class="text-xs font-black tracking-wider uppercase text-slate-800">{{ $settings['cop_school_status'] }}</h3>
                        @endif
                        <p class="text-[11.5px] font-medium text-slate-700 mt-1 leading-snug">
                            Alamat: {{ $settings['cop_school_address'] ?? $schoolAddress }}<br>
                            Telp/WA: {{ $settings['hotline_panitia'] ?? ($settings['school_phone'] ?? '085290944090') }} | Email: {{ $settings['school_email'] ?? 'smkknbi@gmail.com' }}
                        </p>
                    </div>
                    <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                        @if(!empty($settings['school_tagline']))
                            <div class="px-2.5 py-1.5 bg-blue-700 text-white font-extrabold text-[11px] rounded text-center tracking-tight shadow-xs">
                                {{ $settings['school_tagline'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- ISI SURAT PEMBERITAHUAN RESMI -->
        <div class="mt-4 space-y-4 text-[13.5px] leading-relaxed text-slate-950">
            
            <div class="flex items-center gap-2">
                <span class="w-14 font-bold text-slate-800">Hal</span>
                <span class="w-3 font-bold text-center text-slate-500">:</span>
                <span class="font-black uppercase tracking-wider text-slate-950 bg-slate-100 px-3 py-0.5 rounded border border-slate-300">
                    Pemberitahuan
                </span>
            </div>

            <div class="p-4 bg-slate-50/90 rounded-xl border border-slate-200 space-y-1">
                <p class="font-bold text-slate-800 text-xs uppercase tracking-wider">Kepada Yth :</p>
                <p class="text-[13.5px]">Bp/Ibu/Sdr : <strong class="text-slate-950 font-bold">{{ $namaAyah ?: ($namaIbu ?: 'Orang Tua / Wali') }}</strong></p>
                <p class="text-slate-700 text-xs">Orang Tua/ Wali murid dari Calon siswa :</p>
                <p class="text-[15px] font-black text-slate-950 uppercase pt-0.5 tracking-tight">{{ $namaSiswa }}</p>
                <div class="pt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                    <span class="font-bold text-slate-800">
                        No. Peserta: <span class="font-mono-code font-black text-slate-950 text-[13px]">{{ $registration->registration_number }}</span>
                    </span>
                    <span class="text-slate-400">&bull;</span>
                    <span class="font-bold text-slate-800">
                        Kompetensi: <strong class="text-blue-900 font-extrabold uppercase">{{ $jurusanWithClass }}</strong>
                    </span>
                </div>
                <p class="pt-1 text-slate-700 font-semibold italic text-xs">Di Tempat</p>
            </div>

            <div class="pt-1 font-bold text-slate-950 text-[14px]">
                Assalamualaikum Wr.Wb
            </div>

            <p class="leading-relaxed">
                Berdasarkan Hasil Seleksi Dari PMDK {{ strtoupper($schoolName) }} Putra/Putri Bpk/Ibu Pada :
            </p>

            <div class="pl-4 py-2 border-l-4 border-emerald-500 bg-emerald-50/50 rounded-r-xl space-y-1 text-[13px]">
                <div class="flex">
                    <span class="w-36 text-slate-700 font-semibold">Hari / Tanggal</span>
                    <span class="w-4 text-center font-bold text-slate-500">:</span>
                    <span class="font-bold text-slate-950">{{ $hariTanggalSeleksi }}</span>
                </div>
                <div class="flex">
                    <span class="w-36 text-slate-700 font-semibold">Tempat</span>
                    <span class="w-4 text-center font-bold text-slate-500">:</span>
                    <span class="font-bold text-slate-950 uppercase">{{ $schoolName }}</span>
                </div>
            </div>

            <p class="leading-relaxed pt-1">
                Maka calon siswa tersebut dinyatakan <span class="px-2.5 py-0.5 rounded bg-emerald-100 text-emerald-900 font-black border border-emerald-300 text-xs tracking-wider">DITERIMA</span> Sebagai calon siswa di Kompetensi Keahlian : <strong class="text-slate-950 font-black">{{ $jurusanWithClass }}</strong>, {{ $schoolName }} Tahun Pelajaran <strong class="text-slate-950">{{ $academicYearName }}</strong>.
            </p>

            <p class="leading-relaxed">
                Sebagai tindak lanjut dimohon untuk hadir kembali ke kampus {{ $schoolName }} beserta Orang Tua / Wali pada :
            </p>

            <div class="pl-4 py-2.5 border-l-4 border-blue-500 bg-blue-50/50 rounded-r-xl space-y-1.5 text-[13px]">
                <div class="flex">
                    <span class="w-36 text-slate-700 font-semibold">Hari, Tanggal</span>
                    <span class="w-4 text-center font-bold text-slate-500">:</span>
                    <span class="font-bold text-slate-950">{{ $hariTanggalTindakLanjut }}</span>
                </div>
                <div class="flex">
                    <span class="w-36 text-slate-700 font-semibold">Waktu</span>
                    <span class="w-4 text-center font-bold text-slate-500">:</span>
                    <span class="font-bold text-slate-950">Jam Kerja ({{ $settings['hotline_panitia_hours'] ?? '08.00 - 14.00 WIB' }})</span>
                </div>
                <div class="flex">
                    <span class="w-36 text-slate-700 font-semibold">Tempat</span>
                    <span class="w-4 text-center font-bold text-slate-500">:</span>
                    <span class="font-bold text-slate-950">Ruang Sekretariat SPMB {{ $schoolName }}</span>
                </div>
            </div>

            <p class="leading-relaxed pt-1">
                Dengan membawa berkas persyaratan pendaftaran & biaya titipan untuk pengambilan seragam sekolah. Demikian surat pemberitahuan ini kami sampaikan, agar dapat menjadi perhatian.
            </p>

            <div class="font-bold text-slate-950 pt-1 text-[14px]">
                Wassalamualaikum Wr. Wb.
            </div>

            <!-- TTD HALAMAN 2 -->
            <div class="mt-8 pt-4 flex justify-end">
                <div class="text-center w-64 pb-1">
                    <p class="text-[13px] text-slate-800 font-semibold">{{ $schoolCity }}, {{ $tanggalCetak }}</p>
                    <p class="text-[13px] font-bold text-slate-950 mt-0.5 tracking-wide">Panitia PMDK/SPMB,</p>
                    
                    <div class="h-20 flex items-center justify-center my-1 relative">
                        @if(!empty($settings['chairman_signature']))
                            <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD" class="max-h-20 object-contain">
                        @else
                            <div class="h-16 w-36"></div>
                        @endif
                    </div>

                    <p class="text-[13px] font-black text-slate-950 uppercase tracking-wide underline underline-offset-4">{{ $chairmanName }}</p>
                </div>
            </div>

        </div>

    </div>

    <!-- ========================================================================================= -->
    <!-- LEMBAR F4: HALAMAN 3 (SURAT PERNYATAAN ORANG TUA / WALI SISWA)                            -->
    <!-- ========================================================================================= -->
    <div class="page-break"></div>
    <div class="sheet text-slate-950 font-doc-serif leading-normal text-[13px]">

        <!-- KOP SURAT RESMI (SAMA PERSIS DENGAN HALAMAN 1 & 2) -->
        <div class="kop-header mb-3 pb-2 border-b-[3px] border-double border-slate-900 font-sans">
            @if(!empty($kopBanner))
                <img src="{{ asset($kopBanner) }}" alt="Kop Surat Resmi" class="w-full h-auto max-h-32 object-contain mx-auto">
            @else
                <div class="flex items-center justify-between gap-4 py-1">
                    <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                        @if(!empty($settings['school_logo'] ?? $schoolLogo))
                            <img src="{{ asset($settings['school_logo'] ?? $schoolLogo) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        @else
                            <div class="w-16 h-16 rounded-full border-2 border-emerald-600 flex items-center justify-center font-black text-emerald-700 text-sm">
                                SPMB
                            </div>
                        @endif
                    </div>
                    <div class="flex-1 text-center font-sans">
                        @if(!empty($settings['cop_school_name']))
                            <h2 class="text-[18px] font-black tracking-wide text-slate-950 uppercase my-0.5 whitespace-pre-line leading-tight">{!! nl2br(e($settings['cop_school_name'])) !!}</h2>
                        @else
                            <h2 class="text-[19px] font-black tracking-wide text-slate-950 uppercase my-0.5 leading-tight">{{ $schoolName }}</h2>
                        @endif
                        @if(!empty($settings['cop_school_status']))
                            <h3 class="text-xs font-black tracking-wider uppercase text-slate-800">{{ $settings['cop_school_status'] }}</h3>
                        @endif
                        <p class="text-[11.5px] font-medium text-slate-700 mt-1 leading-snug">
                            Alamat: {{ $settings['cop_school_address'] ?? $schoolAddress }}<br>
                            Telp/WA: {{ $settings['hotline_panitia'] ?? ($settings['school_phone'] ?? '085290944090') }} | Email: {{ $settings['school_email'] ?? 'smkknbi@gmail.com' }}
                        </p>
                    </div>
                    <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                        @if(!empty($settings['school_tagline']))
                            <div class="px-2.5 py-1.5 bg-blue-700 text-white font-extrabold text-[11px] rounded text-center tracking-tight shadow-xs">
                                {{ $settings['school_tagline'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- JUDUL DOKUMEN -->
        <div class="text-center my-3">
            <h1 class="text-[16px] font-bold uppercase tracking-wider underline underline-offset-4 decoration-2 text-slate-950">
                {{ $statementTitle }}
            </h1>
        </div>

        <!-- 1. IDENTITAS ORANG TUA -->
        <div class="mb-2">
            <p class="font-medium text-slate-950 mb-1">
                {{ $statementParentHeader }}
            </p>
            <table class="w-full border-collapse">
                <tr>
                    <td class="w-48 py-1">Nama Orang Tua / Wali</td>
                    <td class="w-4 py-1 text-center font-bold">:</td>
                    <td class="py-1 font-bold uppercase text-slate-950">
                        @if(!empty($namaAyah) && $namaAyah !== '-')
                            {{ $namaAyah }}
                        @elseif(!empty($namaIbu) && $namaIbu !== '-')
                            {{ $namaIbu }}
                        @else
                            <span class="dotted-fill w-72"></span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="w-48 py-1">Umur</td>
                    <td class="w-4 py-1 text-center font-bold">:</td>
                    <td class="py-1 text-slate-700">
                        <span class="dotted-fill w-36"></span> Tahun
                    </td>
                </tr>
                <tr>
                    <td class="w-48 py-1 align-top">Alamat Rumah</td>
                    <td class="w-4 py-1 align-top text-center font-bold">:</td>
                    <td class="py-1 align-top leading-snug">
                        @if(!empty($alamatLengkap) && $alamatLengkap !== '-')
                            {{ $alamatLengkap }}
                        @else
                            <span class="dotted-fill w-full max-w-lg mb-1"></span><br>
                            <span class="dotted-fill w-full max-w-lg"></span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- 2. IDENTITAS CALON SISWA -->
        <div class="mb-3">
            <p class="font-medium text-slate-950 mb-1">
                {{ $statementStudentHeader }}
            </p>
            <table class="w-full border-collapse">
                <tr>
                    <td class="w-48 py-1">Nama Siswa</td>
                    <td class="w-4 py-1 text-center font-bold">:</td>
                    <td class="py-1 font-bold uppercase text-slate-950">
                        @if(!empty($namaSiswa) && $namaSiswa !== '-')
                            {{ $namaSiswa }}
                        @else
                            <span class="dotted-fill w-72"></span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="w-48 py-1">Tempat, Tanggal Lahir</td>
                    <td class="w-4 py-1 text-center font-bold">:</td>
                    <td class="py-1">
                        @if(!empty($tempatLahir) && $tempatLahir !== '-' && !empty($tanggalLahir) && $tanggalLahir !== '-')
                            {{ $tempatLahir }}, {{ $tanggalLahir }}
                        @else
                            <span class="dotted-fill w-72"></span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="w-48 py-1">Jenis Kelamin</td>
                    <td class="w-4 py-1 text-center font-bold">:</td>
                    <td class="py-1 font-bold">
                        @php
                            $jk = $bio?->jenis_kelamin ?? '';
                        @endphp
                        @if($jk === 'L')
                            <strong>L</strong> / <span class="text-slate-400">P</span> (Laki-laki)
                        @elseif($jk === 'P')
                            <span class="text-slate-400">L</span> / <strong>P</strong> (Perempuan)
                        @else
                            L / P
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="w-48 py-1">Nomor Peserta PMDK</td>
                    <td class="w-4 py-1 text-center font-bold">:</td>
                    <td class="py-1 font-bold font-mono text-[13px]">
                        {{ $registration->registration_number ?? '____________' }}
                    </td>
                </tr>
                <tr>
                    <td class="w-48 py-1 align-top">Alamat Rumah</td>
                    <td class="w-4 py-1 align-top text-center font-bold">:</td>
                    <td class="py-1 align-top leading-snug">
                        @if(!empty($alamatLengkap) && $alamatLengkap !== '-')
                            {{ $alamatLengkap }}
                        @else
                            <span class="dotted-fill w-full max-w-lg mb-1"></span><br>
                            <span class="dotted-fill w-full max-w-lg"></span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="w-48 py-1">Asal Sekolah</td>
                    <td class="w-4 py-1 text-center font-bold">:</td>
                    <td class="py-1 font-bold">
                        @if(!empty($asalSekolah) && $asalSekolah !== '-')
                            {{ $asalSekolah }}
                        @else
                            <span class="dotted-fill w-72"></span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <!-- 3. PARAGRAF PENGANTAR PERNYATAAN & TITIPAN KEUANGAN -->
        <p class="leading-relaxed text-justify mb-2.5">
            {!! nl2br(e($statementIntro)) !!}
        </p>

        <!-- 4. BUTIR-BUTIR KETENTUAN (1-4) -->
        <div class="leading-relaxed mb-3 pl-1 space-y-1.5">
            @foreach(explode("\n", str_replace("\r", "", $statementRules)) as $ruleLine)
                @if(trim($ruleLine) !== '')
                    <div class="flex items-start">
                        <div class="text-justify leading-relaxed">
                            {!! strip_tags($ruleLine, '<u><b><strong><i><em><span><strike>') !!}
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- 5. PARAGRAF PENUTUP -->
        <p class="leading-relaxed text-justify mb-4">
            {!! nl2br(e($statementClosing)) !!}
        </p>

        <!-- 6. TITIMANGSA & TANDA TANGAN (KIRI: KEPALA SEKOLAH, KANAN: ORANG TUA / WALI) -->
        <div class="grid grid-cols-2 gap-8 text-[13px] text-slate-950 mb-3 pt-1">
            <!-- SEBELAH KIRI: MENGETAHUI KEPALA SEKOLAH -->
            <div class="text-left pl-2">
                <div class="invisible mb-1 text-xs">Placeholder</div>
                <p class="font-normal text-slate-900">Mengetahui,</p>
                <p class="font-bold text-slate-950">{{ $statementPrincipalTitle }}</p>
                
                <div class="h-20 flex items-end">
                    <!-- Spasi tanda tangan resmi -->
                </div>

                <p class="font-bold uppercase tracking-wide text-slate-950">
                    {{ $statementPrincipalName }}
                </p>
            </div>

            <!-- SEBELAH KANAN: ORANG TUA / WALI -->
            <div class="text-left pl-8">
                <p class="mb-1 text-slate-900 font-normal">
                    {{ $statementCity }}, {{ $statementDate }}
                </p>
                <p class="font-normal text-slate-900">Yang membuat pernyataan</p>
                <p class="font-bold text-slate-950">Orang Tua / Wali</p>
                
                <div class="h-20 flex items-end">
                    <!-- Spasi tanda tangan resmi -->
                </div>

                <p class="font-normal text-slate-800 tracking-wider">
                    ( ____________________________ )
                </p>
            </div>
        </div>

        <!-- 7. BAGIAN CATATAN (NB) -->
        <div class="border-t border-slate-300 pt-2 text-[11.5px] text-slate-800 leading-normal space-y-1">
            <p class="font-bold text-slate-950">NB :</p>
            @foreach(explode("\n", str_replace("\r", "", $statementNotes)) as $noteLine)
                @if(trim($noteLine) !== '')
                    <div class="pl-3">
                        {!! strip_tags($noteLine, '<u><b><strong><i><em><span>') !!}
                    </div>
                @endif
            @endforeach
        </div>

    </div>

</body>
</html>
