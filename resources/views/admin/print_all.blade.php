<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Data Calon Siswa SPMB - SMK Karya Nugraha Boyolali</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 10pt; background: #fff !important; }
            @page { margin: 1cm; size: landscape; }
        }
    </style>
</head>
<body class="bg-slate-100 p-4 sm:p-8 font-sans text-slate-800">

    <!-- FLOATING ACTIONS UNTUK SCREEN -->
    <div class="no-print max-w-7xl mx-auto mb-6 flex items-center justify-between bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="javascript:history.back()" class="px-3.5 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span class="text-xs text-slate-500 font-medium">Pratinjau Cetak Rekapitulasi Pendaftar</span>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.spmb.exportExcel', request()->all()) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition flex items-center gap-2 shadow">
                <i class="fa-solid fa-file-excel"></i> Export Excel
            </a>
            <button type="button" onclick="window.print()" class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-lg text-xs font-bold transition flex items-center gap-2 shadow">
                <i class="fa-solid fa-print"></i> Cetak Dokumen (Print)
            </button>
        </div>
    </div>

    <!-- KERTAS CETAK LAPORAN -->
    <div class="max-w-7xl mx-auto bg-white p-8 rounded-xl shadow-md border border-slate-200 print:border-none print:shadow-none print:p-0">
        
        <!-- KOP SURAT RESMI (BENTUK GAMBAR) -->
        @if(!empty($settings['cop_image_banner'] ?? $copImageBanner))
            <div class="border-b-2 border-slate-900 pb-3 mb-6">
                <img src="{{ asset($settings['cop_image_banner'] ?? $copImageBanner) }}" alt="Kop Surat Resmi" class="w-full h-auto max-h-36 object-contain mx-auto">
            </div>
        @else
            <div class="border-b-4 border-double border-slate-900 pb-4 mb-6 flex items-center gap-5">
                <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center p-1 overflow-hidden">
                    @if(!empty($settings['school_logo'] ?? $schoolLogo))
                        <img src="{{ asset($settings['school_logo'] ?? $schoolLogo) }}" alt="Logo Sekolah" class="max-w-full max-h-full object-contain">
                    @else
                        <div class="w-16 h-16 flex items-center justify-center bg-slate-900 text-emerald-400 rounded-xl font-black text-2xl border border-slate-700">
                            KNBI
                        </div>
                    @endif
                </div>
                <div class="flex-1 text-center font-sans">
                    <h3 class="text-xs uppercase tracking-widest font-extrabold text-slate-500">PANITIA PENERIMAAN PESERTA DIDIK BARU (SPMB)</h3>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-wide uppercase">{{ $settings['school_name'] ?? 'SMK KARYA NUGRAHA BOYOLALI' }}</h1>
                    <p class="text-xs text-slate-600 font-medium mt-0.5">
                        Tahun Ajaran {{ $academicYear->name ?? '2026/2027' }} &bull; NPSN: {{ $settings['school_npsn'] ?? '20308458' }}
                    </p>
                </div>
            </div>
        @endif

        <!-- JUDUL DOKUMEN -->
        <div class="text-center mb-6">
            <h2 class="text-base font-black text-slate-900 uppercase underline tracking-wider">
                REKAPITULASI DATA CALON SISWA BARU (SPMB)
            </h2>
            <p class="text-xs text-slate-600 mt-1">
                Tahun Ajaran {{ $academicYear->name ?? '2026/2027' }} &bull; Dicetak pada: {{ date('d F Y, H:i') }} WIB
            </p>
        </div>

        <!-- TABEL DATA LENGKAP -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border border-slate-400 border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-400 font-extrabold text-slate-800 text-[11px]">
                        <th class="py-2.5 px-2 border border-slate-400 text-center w-8">No</th>
                        <th class="py-2.5 px-2.5 border border-slate-400 text-center w-24">No. Pendaftar</th>
                        <th class="py-2.5 px-3 border border-slate-400">Nama Calon Siswa</th>
                        <th class="py-2.5 px-2 border border-slate-400 text-center w-10">L/P</th>
                        <th class="py-2.5 px-2.5 border border-slate-400 text-center">NISN</th>
                        <th class="py-2.5 px-3 border border-slate-400">Asal Sekolah</th>
                        <th class="py-2.5 px-2.5 border border-slate-400 text-center">No. HP/WA</th>
                        <th class="py-2.5 px-3 border border-slate-400">Kompetensi Keahlian (Jurusan)</th>
                        <th class="py-2.5 px-2.5 border border-slate-400 text-center w-20">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300 font-medium">
                    @forelse($registrations as $idx => $r)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-2 border border-slate-300 text-center text-slate-600">{{ $idx + 1 }}</td>
                            <td class="py-2 px-2.5 border border-slate-300 text-center font-mono font-bold text-slate-900">{{ $r->registration_number }}</td>
                            <td class="py-2 px-3 border border-slate-300 font-bold text-slate-900 uppercase">{{ $r->biodata->nama_lengkap ?? $r->user->name ?? '-' }}</td>
                            <td class="py-2 px-2 border border-slate-300 text-center font-bold">{{ $r->biodata->jenis_kelamin ?? 'L' }}</td>
                            <td class="py-2 px-2.5 border border-slate-300 text-center font-mono text-slate-700">{{ $r->user->nisn ?? $r->user->username ?? '-' }}</td>
                            <td class="py-2 px-3 border border-slate-300 text-slate-700 uppercase">{{ $r->school->asal_sekolah ?? '-' }}</td>
                            <td class="py-2 px-2.5 border border-slate-300 text-center font-mono text-slate-700">{{ $r->user->phone ?? '-' }}</td>
                            <td class="py-2 px-3 border border-slate-300 font-semibold text-slate-800">
                                {{ $r->major->name ?? '-' }}
                                @if(!empty($r->major->industry_class))
                                    <span class="text-[10px] text-slate-500">({{ $r->major->industry_class }})</span>
                                @endif
                            </td>
                            <td class="py-2 px-2.5 border border-slate-300 text-center font-bold">
                                @if($r->status === 'accepted')
                                    <span class="text-emerald-700">DITERIMA</span>
                                @elseif($r->status === 'verified')
                                    <span class="text-indigo-700">TERVERIFIKASI</span>
                                @elseif($r->status === 'rejected')
                                    <span class="text-rose-700">DITOLAK</span>
                                @else
                                    <span class="text-amber-700">PENDING</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-6 text-center text-slate-400">Tidak ada data calon siswa terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- RINGKASAN & TANDA TANGAN -->
        <div class="mt-8 pt-4 flex flex-col sm:flex-row justify-between gap-6 text-xs text-slate-700">
            <div>
                <p class="font-bold">Total Rekapitulasi: {{ $registrations->count() }} Calon Siswa</p>
                <p class="text-slate-500 text-[11px] mt-0.5">Dokumen ini merupakan arsip resmi Panitia SPMB {{ $settings['school_name'] ?? 'SMK Karya Nugraha Boyolali' }}.</p>
                @if(!empty($settings['hotline_panitia']))
                    <p class="text-slate-500 text-[10px] mt-0.5">Hotline SPMB: {{ $settings['hotline_panitia'] }} ({{ $settings['hotline_panitia_name'] ?? 'Panitia' }})</p>
                @endif
            </div>
            <div class="text-center sm:text-right flex flex-col items-center sm:items-end">
                <p>Boyolali, {{ date('d F Y') }}</p>
                <p class="font-bold">Ketua Panitia SPMB,</p>
                <div class="h-16 flex items-center justify-center my-1">
                    @if(!empty($settings['chairman_signature']))
                        <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD" class="max-h-14 object-contain">
                    @else
                        <div class="h-12 w-32 border-b border-dashed border-slate-300"></div>
                    @endif
                </div>
                <div>
                    <p class="font-bold underline text-slate-900 uppercase">{{ $settings['chairman_name'] ?? 'H. Bambang Wahyudi, S.Pd., M.Kom.' }}</p>
                    <p class="text-[11px] text-slate-500 font-mono">NIP. {{ $settings['chairman_nip'] ?? '19780514 200501 1 008' }}</p>
                </div>
            </div>
        </div>

    </div>

</body>
</html>
