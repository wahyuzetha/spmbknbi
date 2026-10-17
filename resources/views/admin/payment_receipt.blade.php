<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kwitansi Pembayaran - {{ $payment->invoice_number }} - {{ $registration->user->name ?? 'Calon Siswa' }}</title>
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
            <span class="text-xs text-slate-500 font-medium">Pratinjau Resmi Kwitansi Pembayaran Kasir/Bendahara SPMB</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="px-5 py-2 rounded-lg bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-black shadow transition flex items-center gap-2">
                <i class="fa-solid fa-print text-sm"></i> Cetak Kwitansi (Print)
            </button>
        </div>
    </div>

    <!-- LEMBAR KWITANSI RESMI (FORMAT STANDAR KEDINASAN / SEKOLAH) -->
    <div class="print-container w-full max-w-4xl bg-white rounded-2xl shadow-xl border border-slate-200 p-8 sm:p-12 relative overflow-hidden">
        
        <!-- WATERMARK LUNAS DI LATAR BELAKANG -->
        <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none opacity-5">
            <span class="text-8xl sm:text-9xl font-black text-emerald-800 rotate-[-25deg] uppercase tracking-widest border-8 border-dashed border-emerald-800 p-6 rounded-3xl">
                LUNAS
            </span>
        </div>

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

        <!-- HEADER KWITANSI -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 mb-6 border-b border-slate-200">
            <div>
                <span class="inline-block px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider rounded-md border border-emerald-300">
                    BUKTI PEMBAYARAN RESMI SPMB (TUNAI)
                </span>
                <div class="text-xs text-slate-500 mt-1">Tahun Ajaran: <strong>{{ $academicYear->name ?? '2026/2027' }}</strong></div>
            </div>
            <div class="text-left sm:text-right font-mono text-xs">
                <div class="font-bold text-slate-900 text-sm">No. Kwitansi: <span class="text-blue-700 font-black">{{ $payment->invoice_number }}</span></div>
                <div class="text-slate-600 mt-0.5">Tanggal: {{ \Carbon\Carbon::parse($payment->payment_date ?? $payment->created_at)->translatedFormat('d F Y') }}</div>
            </div>
        </div>

        <!-- ISI DETAIL KWITANSI PEMBAYARAN -->
        <div class="space-y-4 text-xs font-medium leading-relaxed">
            
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 py-1.5 border-b border-slate-100">
                <div class="sm:col-span-3 font-bold text-slate-600 uppercase">Telah Terima Dari</div>
                <div class="sm:col-span-9 font-bold text-slate-900 uppercase text-sm flex items-center gap-2">
                    <span>{{ $registration->user->name ?? '-' }}</span>
                    <span class="text-xs font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">
                        No. Pendaftar: {{ $registration->registration_number }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 py-1.5 border-b border-slate-100">
                <div class="sm:col-span-3 font-bold text-slate-600 uppercase">Pilihan Jurusan</div>
                <div class="sm:col-span-9 font-bold text-slate-800">
                    {{ $registration->major->name ?? '-' }}
                    @if(!empty($registration->major->industry_class))
                        <span class="text-teal-700">({{ $registration->major->industry_class }})</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 py-1.5 border-b border-slate-100">
                <div class="sm:col-span-3 font-bold text-slate-600 uppercase">Asal Sekolah</div>
                <div class="sm:col-span-9 text-slate-800">
                    {{ $registration->school->asal_sekolah ?? '-' }}
                </div>
            </div>


            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 py-1.5 border-b border-slate-100">
                <div class="sm:col-span-3 font-bold text-slate-600 uppercase">Keterangan / Catatan</div>
                <div class="sm:col-span-9 text-slate-700 italic">
                    "{{ $payment->notes ?: 'Pembayaran tunai diterima langsung oleh Bendahara SPMB' }}"
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 py-1.5 border-b border-slate-100">
                <div class="sm:col-span-3 font-bold text-slate-600 uppercase">Metode Pembayaran</div>
                <div class="sm:col-span-9 font-bold text-emerald-800 flex items-center gap-1.5">
                    <i class="fa-solid fa-money-bill-wave text-emerald-600"></i>
                    <span>Tunai (Cash di Loket Bendahara Sekolah) - Status: TERVERIFIKASI OTOMATIS</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 py-2.5 bg-slate-50 px-4 rounded-xl border border-slate-200">
                <div class="sm:col-span-3 font-bold text-slate-700 uppercase self-center">Jumlah Pembayaran</div>
                <div class="sm:col-span-9">
                    <div class="text-2xl font-black text-slate-900 font-mono tracking-tight text-emerald-700">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2 py-2 px-4 bg-emerald-50/50 rounded-xl border border-emerald-100">
                <div class="sm:col-span-3 font-bold text-emerald-900 uppercase text-[11px] self-center">Terbilang</div>
                <div class="sm:col-span-9 font-bold italic text-slate-900 capitalize text-xs">
                    "{{ \App\Models\Payment::terbilang($payment->amount) }} Rupiah"
                </div>
            </div>

        </div>

        <!-- TANDA TANGAN & LEGALITAS RESMI -->
        <div class="mt-10 pt-6 border-t border-slate-200 grid grid-cols-2 gap-6 text-xs text-center">
            
            <!-- KOLOM KIRI: TANDA TERIMA SISWA / WALI -->
            <div class="space-y-1">
                <p class="text-slate-500 font-medium">Tanda Tangan Penyetor,</p>
                <p class="font-bold text-slate-700">Calon Siswa / Wali Murid</p>
                <div class="h-20 flex items-end justify-center pb-2">
                    <div class="w-36 border-b border-slate-400"></div>
                </div>
                <p class="font-bold uppercase text-slate-900">
                    {{ $registration->user->name ?? '-' }}
                </p>
            </div>

            <!-- KOLOM KANAN: BENDAHARA / KASIR SPMB -->
            <div class="space-y-1">
                <p class="text-slate-500 font-medium">Boyolali, {{ \Carbon\Carbon::parse($payment->payment_date ?? $payment->created_at)->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900">Petugas Bendahara SPMB,</p>
                <div class="h-20 flex items-center justify-center">
                    @if(!empty($settings['chairman_signature']))
                        <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD Verifikasi" class="max-h-16 object-contain">
                    @else
                        <div class="h-16 flex items-end justify-center pb-2">
                            <div class="w-36 border-b border-slate-400"></div>
                        </div>
                    @endif
                </div>
                <p class="font-bold uppercase text-slate-900 underline">
                    {{ $payment->confirmedBy->name ?? auth()->user()->name ?? 'Bendahara Sekolah' }}
                </p>
                <p class="text-[10px] text-slate-500 font-mono">Petugas Verifikator Resmi SPMB</p>
            </div>

        </div>

        <!-- CATATAN KAKI RESMI -->
        <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-[10px] text-slate-400 font-mono">
            <span>* Kwitansi ini sah dan dicetak secara komputerisasi dari Sistem Informasi SPMB SMK KNBI.</span>
            <span>Waktu Cetak: {{ date('d/m/Y H:i:s') }}</span>
        </div>

    </div>

</body>
</html>
