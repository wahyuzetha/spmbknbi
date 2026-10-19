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
            margin: 12mm 16mm 12mm 16mm;
        }

        * {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: #0f172a; /* Slate 900 */
        }

        .font-mono-code {
            font-family: 'JetBrains Mono', monospace;
        }

        @media print {
            html, body {
                width: 215mm !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #ffffff !important;
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
                page-break-inside: avoid;
            }
            .page-break {
                page-break-before: always !important;
                break-before: page !important;
                height: 0;
                margin: 0;
                padding: 0;
                border: none;
            }
        }

        @media screen {
            body {
                background-color: #0f172a;
                background-image: radial-gradient(#1e293b 1px, transparent 1px);
                background-size: 24px 24px;
                padding: 32px 16px;
            }
            .sheet {
                background: #ffffff;
                width: 215mm;
                min-height: 330mm;
                margin: 0 auto 36px auto;
                padding: 14mm 18mm;
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
                box-sizing: border-box;
                border-radius: 6px;
                position: relative;
            }
        }

        .dotted-subline {
            border-bottom: 1px dashed #cbd5e1;
        }
    </style>
</head>
<body class="antialiased">

    @php
        // Pastikan settings tersedia
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
        
        // Alamat
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
    @endphp

    <!-- FLOATING TOP BAR MODERN (HANYA MUNCUL DI LAYAR SCREEN) -->
    <div class="no-print max-w-[215mm] mx-auto mb-6 flex flex-wrap items-center justify-between gap-3 bg-slate-900/90 backdrop-blur-md p-4 rounded-2xl border border-slate-700/80 shadow-2xl text-white">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition flex items-center gap-1.5 border border-slate-700">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <div>
                <span class="text-xs font-bold text-white block">
                    Pratinjau Bukti Pendaftaran (Standar Kertas F4)
                </span>
                <span class="text-[10px] text-slate-400">
                    2 Halaman &bull; Rasio 215mm x 330mm &bull; Siap Dicetak / Disimpan ke PDF
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-900/30">
                <i class="fa-solid fa-print"></i> Cetak / Simpan PDF (F4)
            </button>
        </div>
    </div>

    <!-- ========================================================================================= -->
    <!-- LEMBAR F4: HALAMAN 1 (BUKTI PENDAFTARAN & BIODATA SISWA)                                  -->
    <!-- ========================================================================================= -->
    <div class="sheet">

        <!-- KOP SURAT (DARI HASIL PENGATURAN ADMIN) -->
        <div class="kop-header mb-3 pb-2 border-b-2 border-slate-900">
            @if(!empty($kopBanner))
                <img src="{{ asset($kopBanner) }}" alt="Kop Surat Resmi" class="w-full h-auto max-h-32 object-contain mx-auto">
            @else
                <!-- Kop Surat Dinamis dari Pengaturan Admin -->
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
                            <h2 class="text-base font-black tracking-wide text-slate-900 uppercase my-0.5 whitespace-pre-line">{!! nl2br(e($settings['cop_school_name'])) !!}</h2>
                        @else
                            <h2 class="text-xl font-black tracking-wide text-slate-900 uppercase my-0.5">{{ $schoolName }}</h2>
                        @endif
                        @if(!empty($settings['cop_school_status']))
                            <h3 class="text-xs font-black tracking-wider uppercase text-slate-800">{{ $settings['cop_school_status'] }}</h3>
                        @endif
                        <p class="text-[9px] text-slate-600 mt-0.5 leading-tight">
                            Alamat: {{ $settings['cop_school_address'] ?? $schoolAddress }}<br>
                            Telp/WA: {{ $settings['hotline_panitia'] ?? ($settings['school_phone'] ?? '085290944090') }} | Email: {{ $settings['school_email'] ?? 'smkknbi@gmail.com' }}
                        </p>
                    </div>
                    <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                        @if(!empty($settings['school_tagline']))
                            <div class="px-2 py-1 bg-blue-700 text-white font-extrabold text-[10px] rounded text-center">
                                {{ $settings['school_tagline'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- ================= JUDUL DOKUMEN & BADGE NOMOR + JURUSAN ================= -->
        <div class="text-center my-3">
            <h1 class="text-base sm:text-lg font-black uppercase text-slate-900 leading-snug tracking-tight">
                BUKTI PENDAFTARAN CALON<br>
                SISWA BARU {{ strtoupper($schoolName) }}<br>
                SISTEM ONLINE<br>
                TAHUN PELAJARAN {{ $academicYearName }}
            </h1>
            
            <!-- NOMOR PENDAFTARAN & JURUSAN DI BAWAHNYA -->
            <div class="mt-2.5 inline-flex flex-col items-center bg-slate-50 border border-slate-300 rounded-xl px-6 py-2 shadow-2xs">
                <div class="text-[13px] font-extrabold text-slate-900 tracking-wide">
                    Nomor Peserta : <span class="font-mono-code font-black text-slate-950 text-sm tracking-wider">{{ $registration->registration_number }}</span>
                </div>
                <div class="mt-1 pt-1 border-t border-slate-200/90 w-full flex items-center justify-center gap-1.5 text-[11px] font-bold text-slate-800">
                    <span class="text-slate-500 font-semibold">Kompetensi Keahlian :</span>
                    <span class="font-extrabold text-blue-900">{{ $jurusanWithClass }}</span>
                </div>
            </div>
        </div>

        <!-- ================= TABEL DATA LENGKAP SISWA (1 - 13) ================= -->
        <div class="mt-3 text-[11.5px] leading-relaxed text-slate-900">
            <table class="w-full border-collapse">
                <tbody>
                    <tr class="hover:bg-slate-50/50">
                        <td class="w-6 py-0.5 align-top font-semibold text-slate-500">1.</td>
                        <td class="w-52 py-0.5 align-top font-semibold text-slate-700">Nama</td>
                        <td class="w-4 py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-bold text-slate-950 uppercase tracking-tight">{{ $namaSiswa }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">2.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">Tempat Lahir</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium capitalize">{{ $tempatLahir }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">3.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">Tanggal Lahir</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium">{{ $tanggalLahir }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">4.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">Jenis Kelamin</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium">{{ $jenisKelamin }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">5.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">NISN</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-mono-code font-bold text-slate-900">{{ $nisn }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">6.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">Agama</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium">{{ $agama }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">7.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">Sekolah Asal(SMP/MTs)</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-bold uppercase text-slate-900">{{ $asalSekolah }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 align-top font-semibold text-slate-500">8.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700" colspan="3">Nama Orang Tua</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td></td>
                        <td class="py-0.5 pl-5 align-top font-medium text-slate-600 flex items-center gap-1.5">
                            <span class="text-slate-400 text-[10px]">&bull;</span> Ayah
                        </td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium">{{ $namaAyah }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td></td>
                        <td class="py-0.5 pl-5 align-top font-medium text-slate-600 flex items-center gap-1.5">
                            <span class="text-slate-400 text-[10px]">&bull;</span> Ibu
                        </td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium">{{ $namaIbu }}</td>
                    </tr>
                    <tr>
                        <td class="py-0.5 align-top font-semibold text-slate-500">9.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700" colspan="3">Pekerjaan Orang Tua/Wali</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td></td>
                        <td class="py-0.5 pl-5 align-top font-medium text-slate-600 flex items-center gap-1.5">
                            <span class="text-slate-400 text-[10px]">&bull;</span> Ayah
                        </td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium">{{ $pekerjaanAyah }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td></td>
                        <td class="py-0.5 pl-5 align-top font-medium text-slate-600 flex items-center gap-1.5">
                            <span class="text-slate-400 text-[10px]">&bull;</span> Ibu
                        </td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium">{{ $pekerjaanIbu }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">10.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">Alamat</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top leading-relaxed font-medium">{{ $alamatLengkap }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">11.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">No.WA/ HP Aktif</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-mono-code font-bold text-slate-900">{{ $noHp }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">12.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">Email</td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium text-slate-800">{{ $email }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-0.5 align-top font-semibold text-slate-500">13.</td>
                        <td class="py-0.5 align-top font-semibold text-slate-700">
                            Informasi/ Rekomendasi<br>Dari
                        </td>
                        <td class="py-0.5 align-top text-center font-bold text-slate-400">:</td>
                        <td class="py-0.5 align-top font-medium text-slate-900">{{ $rekomendasi }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- ================= BAGIAN BAWAH HALAMAN 1 (PAS FOTO & TTD / QR CODE) ================= -->
        <div class="mt-6 pt-3 flex items-end justify-between text-xs">
            
            <!-- KIRI: KOTAK PAS FOTO 3X4 MODERN -->
            <div class="flex flex-col items-center">
                <div class="w-24 h-32 border-2 border-slate-300 rounded-xl bg-slate-50 flex flex-col items-center justify-center p-1 overflow-hidden shadow-2xs relative">
                    @if($pasFoto)
                        <img src="{{ asset('storage/' . $pasFoto->file_path) }}" alt="Pas Foto" class="w-full h-full object-cover rounded-lg">
                    @else
                        <div class="w-full h-full border border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center text-slate-400 p-2 text-center bg-white">
                            <i class="fa-solid fa-camera text-slate-300 text-xl mb-1"></i>
                            <span class="text-[9px] font-bold text-slate-400 uppercase tracking-wider leading-tight">Pas Foto<br>3 x 4</span>
                        </div>
                    @endif
                </div>
                <span class="text-[9px] font-semibold text-slate-400 mt-1 uppercase tracking-wider">Foto Calon Siswa</span>
            </div>

            <!-- KANAN: TITIMANGSA & TTD RESMI KETUA PANITIA -->
            <div class="text-center w-64">
                <p class="text-xs text-slate-700 font-medium">{{ $schoolCity }}, {{ $tanggalCetak }}</p>
                <p class="text-xs font-bold text-slate-900 mt-0.5 tracking-wider">Panitia SPMB,</p>
                
                <div class="h-20 flex items-center justify-center my-1 relative">
                    @if(!empty($settings['chairman_signature']))
                        <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD" class="max-h-20 object-contain">
                    @else
                        <div class="h-16 w-36"></div>
                    @endif
                </div>

                <p class="text-xs font-black text-slate-950 uppercase tracking-wide underline underline-offset-2">{{ $chairmanName }}</p>
            </div>

        </div>

    </div>

    <!-- ========================================================================================= -->
    <!-- LEMBAR F4: HALAMAN 2 (SURAT PEMBERITAHUAN SELEKSI & DITERIMA)                             -->
    <!-- ========================================================================================= -->
    <div class="page-break"></div>
    <div class="sheet">

        <!-- KOP SURAT (DARI HASIL PENGATURAN ADMIN) -->
        <div class="kop-header mb-3 pb-2 border-b-2 border-slate-900">
            @if(!empty($kopBanner))
                <img src="{{ asset($kopBanner) }}" alt="Kop Surat Resmi" class="w-full h-auto max-h-32 object-contain mx-auto">
            @else
                <!-- Kop Surat Dinamis dari Pengaturan Admin -->
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
                            <h2 class="text-base font-black tracking-wide text-slate-900 uppercase my-0.5 whitespace-pre-line">{!! nl2br(e($settings['cop_school_name'])) !!}</h2>
                        @else
                            <h2 class="text-xl font-black tracking-wide text-slate-900 uppercase my-0.5">{{ $schoolName }}</h2>
                        @endif
                        @if(!empty($settings['cop_school_status']))
                            <h3 class="text-xs font-black tracking-wider uppercase text-slate-800">{{ $settings['cop_school_status'] }}</h3>
                        @endif
                        <p class="text-[9px] text-slate-600 mt-0.5 leading-tight">
                            Alamat: {{ $settings['cop_school_address'] ?? $schoolAddress }}<br>
                            Telp/WA: {{ $settings['hotline_panitia'] ?? ($settings['school_phone'] ?? '085290944090') }} | Email: {{ $settings['school_email'] ?? 'smkknbi@gmail.com' }}
                        </p>
                    </div>
                    <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                        @if(!empty($settings['school_tagline']))
                            <div class="px-2 py-1 bg-blue-700 text-white font-extrabold text-[10px] rounded text-center">
                                {{ $settings['school_tagline'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- ISI SURAT PEMBERITAHUAN RESMI -->
        <div class="mt-4 space-y-3.5 text-[11.5px] leading-relaxed text-slate-900">
            
            <div class="flex items-center gap-2">
                <span class="w-12 font-bold text-slate-700">Hal</span>
                <span class="w-3 font-bold text-center text-slate-400">:</span>
                <span class="font-extrabold uppercase tracking-wider text-slate-950 bg-slate-100 px-2 py-0.5 rounded border border-slate-200">
                    Pemberitahuan
                </span>
            </div>

            <div class="p-3 bg-slate-50/80 rounded-xl border border-slate-200/80 space-y-0.5">
                <p class="font-bold text-slate-800">Kepada Yth :</p>
                <p>Bp/Ibu/Sdr : <strong class="text-slate-950">{{ $namaAyah ?: ($namaIbu ?: 'Orang Tua / Wali') }}</strong></p>
                <p class="text-slate-600 text-[11px]">Orang Tua/ Wali murid dari Calon siswa :</p>
                <p class="text-sm font-black text-slate-950 uppercase pt-0.5 tracking-tight">{{ $namaSiswa }}</p>
                <div class="pt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs">
                    <span class="font-bold text-slate-800">
                        No.PMDK-<span class="font-mono-code font-bold">{{ $registration->registration_number }}</span>
                    </span>
                    <span class="text-slate-300">&bull;</span>
                    <span class="font-bold text-slate-800">
                        Kompetensi: <strong class="text-blue-900 font-extrabold">{{ $jurusanWithClass }}</strong>
                    </span>
                </div>
                <p class="pt-1 text-slate-600 font-semibold italic text-[11px]">Ditempat</p>
            </div>

            <div class="pt-1 font-bold text-slate-950">
                Assalamualaikum Wr.Wb
            </div>

            <p class="leading-relaxed">
                Berdasarkan Hasil Seleksi Dari PMDK {{ strtoupper($schoolName) }} Putra/Putri Bpk/Ibu Pada :
            </p>

            <div class="pl-4 py-1.5 border-l-2 border-emerald-500 bg-emerald-50/40 rounded-r-xl space-y-1">
                <div class="flex">
                    <span class="w-32 text-slate-700 font-medium">Hari / Tanggal</span>
                    <span class="w-4 text-center font-bold text-slate-400">:</span>
                    <span class="font-bold text-slate-950">{{ $hariTanggalSeleksi }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-700 font-medium">Tempat</span>
                    <span class="w-4 text-center font-bold text-slate-400">:</span>
                    <span class="font-bold text-slate-950 uppercase">{{ $schoolName }}</span>
                </div>
            </div>

            <p class="leading-relaxed pt-1">
                Maka siswa tersebut dinyatakan <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-900 font-black border border-emerald-300">DITERIMA</span> Sebagai calon siswa di Kompetensi Keahlian : <strong class="text-slate-950 font-black">{{ $jurusanWithClass }}</strong>, {{ $schoolName }} Tahun Pelajaran <strong class="text-slate-950">{{ $academicYearName }}</strong>.
            </p>

            <p class="leading-relaxed">
                Sebagai tindak lanjut dimohon untuk kembali ke {{ $schoolName }} Beserta Orang Tua Pada :
            </p>

            <div class="pl-4 py-2 border-l-2 border-blue-500 bg-blue-50/40 rounded-r-xl space-y-1">
                <div class="flex">
                    <span class="w-32 text-slate-700 font-medium">Hari, Tanggal</span>
                    <span class="w-4 text-center font-bold text-slate-400">:</span>
                    <span class="font-bold text-slate-950">{{ $hariTanggalTindakLanjut }}</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-700 font-medium">Waktu</span>
                    <span class="w-4 text-center font-bold text-slate-400">:</span>
                    <span class="font-bold text-slate-950">Jam Kerja ({{ $settings['hotline_panitia_hours'] ?? '08.00 - 14.00 WIB' }})</span>
                </div>
                <div class="flex">
                    <span class="w-32 text-slate-700 font-medium">Tempat</span>
                    <span class="w-4 text-center font-bold text-slate-400">:</span>
                    <span class="font-bold text-slate-950">Ruang SPMB {{ $schoolName }}</span>
                </div>
            </div>

            <p class="leading-relaxed pt-1">
                Dengan Membawa Persyaratan & Biaya titipan untuk pengambilan Seragam. Demikian Pemberitahuan ini kami sampaikan, agar menjadikan perhatian.
            </p>

            <div class="font-bold text-slate-950 pt-1">
                Waalaikumsalam Wr.Wb
            </div>

            <!-- TTD HALAMAN 2 -->
            <div class="mt-6 pt-3 flex justify-end text-xs">
                <div class="text-center w-64">
                    <p class="text-xs text-slate-700 font-medium">{{ $schoolCity }}, {{ $tanggalCetak }}</p>
                    <p class="text-xs font-bold text-slate-900 mt-0.5 tracking-wider">Panitia PMDK/SPMB,</p>
                    
                    <div class="h-20 flex items-center justify-center my-1 relative">
                        @if(!empty($settings['chairman_signature']))
                            <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD" class="max-h-20 object-contain">
                        @else
                            <div class="h-16 w-36"></div>
                        @endif
                    </div>

                    <p class="text-xs font-black text-slate-950 uppercase tracking-wide underline underline-offset-2">{{ $chairmanName }}</p>
                </div>
            </div>

        </div>

    </div>

</body>
</html>
