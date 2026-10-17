<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapitulasi Pembayaran Global - {{ $registration->registration_number }} - {{ $registration->user->name ?? 'Calon Siswa' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-container {
                box-shadow: none !important;
                border: 1px solid #999 !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 1.5cm !important;
            }
        }
        @page {
            size: A4 portrait;
            margin: 1cm;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 p-4 sm:p-8 min-h-screen flex flex-col items-center">

    <!-- ACTION TOOLBAR (HANYA MUNCUL DI LAYAR BUKAN SAAT PRINT) -->
    <div class="no-print w-full max-w-4xl mb-4 flex items-center justify-between bg-white px-5 py-3 rounded-xl shadow-sm border border-slate-200">
        <div class="flex items-center gap-3">
            <a href="javascript:window.history.back()" class="px-3.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <span class="text-xs text-slate-500 font-medium">Rekapitulasi Global Seluruh Pembayaran Administrasi SPMB</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-5 py-2 rounded-lg bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-black shadow transition flex items-center gap-2">
                <i class="fa-solid fa-print text-sm"></i> Cetak Rekapitulasi (Print)
            </button>
        </div>
    </div>

    <!-- LEMBAR REKAPITULASI RESMI (GLOBAL) -->
    <div class="print-container w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200 p-8 sm:p-12 relative overflow-hidden">
        
        <!-- ================= KOP SURAT SEKOLAH ================= -->
        @if(!empty($settings['cop_image_banner']))
            <div class="w-full mb-6 pb-4 border-b-2 border-slate-800">
                <img src="{{ asset($settings['cop_image_banner']) }}" alt="Kop Surat Resmi" class="w-full max-h-36 object-contain mx-auto">
            </div>
        @else
            <div class="flex items-center gap-5 pb-5 mb-6 border-b-4 border-double border-slate-800">
                <div class="w-20 h-20 flex-shrink-0 flex items-center justify-center">
                    @if(!empty($settings['school_logo']))
                        <img src="{{ asset($settings['school_logo']) }}" alt="Logo Sekolah" class="max-w-full max-h-full object-contain">
                    @else
                        <div class="w-16 h-16 rounded-full bg-blue-800 text-white flex items-center justify-center font-bold text-xl">KNBI</div>
                    @endif
                </div>
                <div class="flex-1 text-center">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-600">PANITIA PENERIMAAN PESERTA DIDIK BARU (SPMB)</h3>
                    <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight uppercase">SMK KARYA NUGRAHA BOYOLALI</h1>
                    <p class="text-xs text-slate-600 font-medium mt-0.5">{{ $settings['school_address'] ?? 'Jl. Raya Boyolali - Solo KM. 02, Mojosongo, Boyolali, Jawa Tengah 57322' }}</p>
                    <p class="text-[11px] text-slate-500 font-mono mt-0.5">Hotline SPMB: {{ $settings['hotline_spmb'] ?? '0858-7609-8129' }} | Email: smkknbi@gmail.com</p>
                </div>
                <div class="w-20 h-20 flex-shrink-0"></div>
            </div>
        @endif

        <!-- JUDUL DOKUMEN -->
        <div class="text-center my-4 font-sans">
            <h2 class="text-base sm:text-lg font-black uppercase tracking-wider underline text-slate-900">
                REKAPITULASI BUKTI PEMBAYARAN KEUANGAN SPMB
            </h2>
            <p class="text-xs text-slate-500 mt-1">Tahun Ajaran: <strong>{{ $academicYear->name ?? '2026/2027' }}</strong> | Status: <strong>TERVERIFIKASI BENDAHARA</strong></p>
        </div>

        <!-- BIODATA CALON SISWA -->
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 my-5 text-xs grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <table class="w-full text-left font-medium">
                    <tr>
                        <td class="w-32 py-1 text-slate-500 font-bold">No. Pendaftaran</td>
                        <td class="py-1 font-mono font-bold text-emerald-700 text-sm">: {{ $registration->registration_number }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-slate-500 font-bold">Nama Calon Siswa</td>
                        <td class="py-1 font-bold text-slate-900 uppercase">: {{ $registration->user->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-slate-500 font-bold">NISN / No. HP</td>
                        <td class="py-1 text-slate-800">: {{ $registration->user->nisn ?? '-' }} / {{ $registration->user->phone ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full text-left font-medium">
                    <tr>
                        <td class="w-32 py-1 text-slate-500 font-bold">Jurusan Dipilih</td>
                        <td class="py-1 font-bold text-slate-900">
                            : {{ $registration->major->name ?? '-' }}
                            @if(!empty($registration->major->industry_class))
                                <span class="text-teal-700">({{ $registration->major->industry_class }})</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="py-1 text-slate-500 font-bold">Asal Sekolah</td>
                        <td class="py-1 text-slate-800">: {{ $registration->school->asal_sekolah ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 text-slate-500 font-bold">Total Transaksi</td>
                        <td class="py-1 font-bold text-blue-700">: {{ $confirmedPayments->count() }}x Pembayaran Tunai</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- TABEL RINCIAN RIWAYAT PEMBAYARAN (TRANSPARAN TANPA MENGHILANGKAN HISTORY) -->
        <div class="my-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-emerald-600"></i> Rincian Seluruh Transaksi Pembayaran:
            </h3>
            <table class="w-full text-left text-xs border border-slate-300 border-collapse">
                <thead>
                    <tr class="bg-slate-100 border-b border-slate-300 font-bold text-slate-700 uppercase text-[11px]">
                        <th class="py-2.5 px-3 border-r border-slate-300 text-center w-10">No</th>
                        <th class="py-2.5 px-3 border-r border-slate-300 text-center w-28">Tanggal Bayar</th>
                        <th class="py-2.5 px-3 border-r border-slate-300">No. Invoice</th>
                        <th class="py-2.5 px-3 border-r border-slate-300">Jenis Pembayaran</th>
                        <th class="py-2.5 px-3 border-r border-slate-300">Keterangan</th>
                        <th class="py-2.5 px-3 border-r border-slate-300">Petugas Bendahara</th>
                        <th class="py-2.5 px-3 text-right">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 font-medium">
                    @forelse($confirmedPayments as $idx => $p)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2 px-3 border-r border-slate-200 text-center text-slate-500">{{ $idx + 1 }}</td>
                            <td class="py-2 px-3 border-r border-slate-200 text-center font-mono">
                                {{ \Carbon\Carbon::parse($p->payment_date ?? $p->created_at)->format('d/m/Y') }}
                            </td>
                            <td class="py-2 px-3 border-r border-slate-200 font-mono font-bold text-blue-700">
                                {{ $p->invoice_number }}
                            </td>
                            <td class="py-2 px-3 border-r border-slate-200 font-bold text-slate-900">
                                {{ $p->payment_type }}
                            </td>
                            <td class="py-2 px-3 border-r border-slate-200 text-slate-600 text-[11px] italic">
                                {{ $p->notes ?: '-' }}
                            </td>
                            <td class="py-2 px-3 border-r border-slate-200 text-slate-700 text-[11px]">
                                {{ $p->confirmedBy->name ?? 'Bendahara SPMB' }}
                            </td>
                            <td class="py-2 px-3 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($p->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Belum ada riwayat transaksi pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-emerald-50 border-t-2 border-slate-300 font-bold text-xs">
                        <td colspan="6" class="py-3 px-4 text-right uppercase text-slate-800 tracking-wider">
                            TOTAL AKUMULASI PEMBAYARAN (GLOBAL) :
                        </td>
                        <td class="py-3 px-3 text-right font-mono font-black text-base text-emerald-800">
                            Rp {{ number_format($totalPaid, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- TERBILANG -->
        <div class="py-2.5 px-4 bg-slate-50 rounded-xl border border-slate-200 text-xs my-4">
            <span class="font-bold text-slate-700 uppercase">Terbilang : </span>
            <span class="font-bold italic text-slate-900 capitalize">
                "{{ \App\Models\Payment::terbilang($totalPaid) }} Rupiah"
            </span>
        </div>

        <!-- TANDA TANGAN LEGALITAS -->
        <div class="mt-10 pt-6 border-t border-slate-200 grid grid-cols-2 gap-6 text-xs text-center">
            
            <!-- KIRI: KETUA PANITIA -->
            <div class="space-y-1">
                <p class="text-slate-500 font-medium">Mengetahui,</p>
                <p class="font-bold text-slate-800">Ketua Panitia SPMB</p>
                <div class="h-20 flex items-center justify-center">
                    @if(!empty($settings['chairman_signature']))
                        <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD Ketua" class="max-h-16 object-contain">
                    @else
                        <div class="h-16 flex items-end justify-center pb-2">
                            <div class="w-36 border-b border-slate-400"></div>
                        </div>
                    @endif
                </div>
                <p class="font-bold uppercase text-slate-900 underline">
                    {{ $settings['chairman_name'] ?? 'H. Bambang Wahyudi, S.Pd., M.Kom.' }}
                </p>
                <p class="text-[10px] text-slate-500 font-mono">NIP. {{ $settings['chairman_nip'] ?? '-' }}</p>
            </div>

            <!-- KANAN: BENDAHARA SEKOLAH -->
            <div class="space-y-1">
                <p class="text-slate-500 font-medium">Boyolali, {{ date('d F Y') }}</p>
                <p class="font-bold text-slate-800">Petugas Bendahara Sekolah,</p>
                <div class="h-20 flex items-end justify-center pb-2">
                    <div class="w-36 border-b border-slate-400"></div>
                </div>
                <p class="font-bold uppercase text-slate-900 underline">
                    {{ auth()->user()->name ?? 'Bendahara SPMB KNBI' }}
                </p>
                <p class="text-[10px] text-slate-500 font-mono">Seksi Keuangan SPMB</p>
            </div>

        </div>

        <!-- CATATAN KAKI -->
        <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 font-mono">
            <span>* Rekapitulasi ini sah sebagai bukti pelunasan / pencatatan pembayaran administrasi SPMB SMK Karya Nugraha Boyolali.</span>
            <span>Waktu Cetak: {{ date('d/m/Y H:i:s') }}</span>
        </div>

    </div>

</body>
</html>
