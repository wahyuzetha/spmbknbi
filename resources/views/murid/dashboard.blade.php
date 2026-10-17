<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Calon Siswa | SPMB SMK Karya Nugraha Boyolali</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 min-h-screen flex flex-col antialiased selection:bg-blue-600 selection:text-white">

    <!-- NAVBAR DASHBOARD -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- BRAND IDENTITAS -->
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-700 to-indigo-600 p-0.5 shadow-md flex items-center justify-center overflow-hidden">
                        @if(!empty($schoolLogo))
                            <img src="{{ asset($schoolLogo) }}" alt="Logo" class="max-w-full max-h-full object-contain p-0.5 bg-white rounded-lg">
                        @else
                            <svg viewBox="0 0 100 100" class="w-8 h-8">
                                <polygon points="50,5 95,38 78,92 22,92 5,38" fill="#ffffff"/>
                                <circle cx="50" cy="50" r="24" fill="#1e40af"/>
                                <path d="M50,34 L54,48 L46,48 Z" fill="#facc15"/>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black text-slate-900">SPMB KNBI</span>
                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded bg-blue-100 text-blue-800">Calon Siswa</span>
                        </div>
                        <p class="text-[11px] font-semibold text-slate-500 uppercase tracking-tight">SMK Karya Nugraha Boyolali</p>
                    </div>
                </div>

                <!-- USER MENU & LOGOUT -->
                <div class="flex items-center gap-3">
                    <div class="hidden sm:block text-right">
                        <div class="text-xs font-bold text-slate-900">{{ $user->name }}</div>
                        <div class="text-[10px] text-slate-500 font-medium">NISN: {{ $user->nisn ?? '-' }}</div>
                    </div>

                    <a href="{{ route('murid.print.card') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition">
                        <i class="fa-solid fa-print"></i> Cetak Bukti
                    </a>

                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Keluar">
                            <i class="fa-solid fa-arrow-right-from-bracket text-base"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </header>

    <!-- CONTENT WRAPPER -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        <!-- NOTIFIKASI SUKSES / ERROR -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm font-semibold flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-lg"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        <!-- ================= 1. KARTU PROFIL & STATUS PENDAFTARAN ================= -->
        <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-800 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-blue-900/15 relative overflow-hidden">
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-xs font-bold backdrop-blur-md">
                        <i class="fa-solid fa-receipt text-amber-300"></i> No. Pendaftaran: <strong>{{ $registration->registration_number ?? '-' }}</strong>
                    </div>

                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                        Selamat Datang, {{ $user->name }}!
                    </h1>

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-xs sm:text-sm text-blue-100 font-medium">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-graduation-cap text-amber-300"></i> Jurusan: <strong>{{ $registration->major->name ?? '-' }} ({{ $registration->major->industry_class ?? '' }})</strong>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-cyan-300"></i> TA: {{ $registration->academicYear->name ?? '2027/2028' }}
                        </span>
                    </div>
                </div>

                <!-- BADGE STATUS PENDAFTARAN -->
                <div class="bg-white/10 border border-white/20 p-5 rounded-2xl backdrop-blur-md min-w-[240px] text-center md:text-right">
                    <span class="text-xs uppercase font-bold tracking-wider text-blue-200 block mb-1">Status Verifikasi</span>
                    
                    @if(($registration->status ?? '') === 'accepted')
                        <div class="text-emerald-400 font-extrabold text-lg flex items-center justify-center md:justify-end gap-2">
                            <i class="fa-solid fa-circle-check"></i> DITERIMA
                        </div>
                        <p class="text-[11px] text-emerald-200 mt-1">Selamat! Anda telah dinyatakan lolos seleksi.</p>
                    @elseif(($registration->status ?? '') === 'verified')
                        <div class="text-cyan-300 font-extrabold text-lg flex items-center justify-center md:justify-end gap-2">
                            <i class="fa-solid fa-shield-check"></i> BERKAS TERVERIFIKASI
                        </div>
                        <p class="text-[11px] text-cyan-100 mt-1">Berkas valid. Menunggu pengumuman akhir.</p>
                    @elseif(($registration->status ?? '') === 'revision')
                        <div class="text-amber-300 font-extrabold text-lg flex items-center justify-center md:justify-end gap-2">
                            <i class="fa-solid fa-triangle-exclamation"></i> BUTUH REVISI
                        </div>
                        <p class="text-[11px] text-amber-100 mt-1">Ada berkas yang perlu Anda perbaiki.</p>
                    @elseif(($registration->status ?? '') === 'rejected')
                        <div class="text-rose-400 font-extrabold text-lg flex items-center justify-center md:justify-end gap-2">
                            <i class="fa-solid fa-circle-xmark"></i> BELUM LOLOS
                        </div>
                    @else
                        <div class="text-amber-300 font-extrabold text-lg flex items-center justify-center md:justify-end gap-2">
                            <i class="fa-solid fa-clock-rotate-left"></i> MENUNGGU VERIFIKASI
                        </div>
                        <p class="text-[11px] text-blue-200 mt-1">Panitia sedang meninjau data Anda.</p>
                    @endif

                    @if($registration && $registration->verification_notes)
                        <div class="mt-3 p-2.5 rounded-lg bg-black/20 text-xs text-left text-amber-200 border border-amber-300/30">
                            <strong>Catatan Panitia:</strong> {{ $registration->verification_notes }}
                        </div>
                    @endif

                    <div class="mt-4 pt-3 border-t border-white/10">
                        <a href="{{ route('murid.print.card') }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-white hover:bg-slate-50 text-blue-900 font-extrabold rounded-xl shadow-md text-xs transition">
                            <i class="fa-solid fa-print text-blue-600"></i> Cetak Bukti Pendaftaran (F4)
                        </a>
                    </div>
                </div>

            </div>

        </div>

        <!-- ================= 2. INFORMASI ADMINISTRASI & STATUS PEMBAYARAN SISWA ================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl flex-shrink-0 shadow-2xs">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-slate-900">Informasi & Riwayat Pembayaran SPMB</h3>
                            @if(($totalPaid ?? 0) > 0)
                                <span class="px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[11px] font-black border border-emerald-200 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i> Terverifikasi Bendahara
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[11px] font-bold border border-amber-200">
                                    Menunggu Pembayaran Loket
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5 font-medium">
                            Besaran nominal uang yang telah Anda bayarkan secara tunai ke petugas bendahara sekolah dan status verifikasinya.
                        </p>
                    </div>
                </div>

                @if(($totalPaid ?? 0) > 0)
                    <div class="flex items-center gap-2">
                        <a href="{{ route('murid.payment.globalReceipt') }}" target="_blank" 
                           class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm">
                            <i class="fa-solid fa-print"></i> Cetak Kwitansi Rekapitulasi
                        </a>
                    </div>
                @endif
            </div>

            <!-- KARTU METRIK NOMINAL PEMBAYARAN SISWA -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                
                <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-50 to-teal-50/60 border border-emerald-200 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-emerald-800 uppercase tracking-wider block">TOTAL TERBAYAR (GLOBAL)</span>
                        <div class="text-2xl font-black text-slate-900 font-mono mt-1 text-emerald-800">
                            Rp {{ number_format($totalPaid ?? 0, 0, ',', '.') }}
                        </div>
                        <span class="text-[10px] text-emerald-700 mt-0.5 block">Akumulasi seluruh transaksi tunai</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-200/60 text-emerald-800 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-money-bill-wave"></i>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">FREKUENSI TRANSAKSI</span>
                        <div class="text-2xl font-black text-slate-900 mt-1">
                            {{ $transactionCount ?? 0 }}x <span class="text-xs font-bold text-slate-500">Setoran</span>
                        </div>
                        <span class="text-[10px] text-slate-500 mt-0.5 block">
                            {{ ($transactionCount ?? 0) > 1 ? 'Otomatis di-global menjadi 1' : 'Tercatat di sistem kasir' }}
                        </span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50/70 border border-blue-200 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-blue-800 uppercase tracking-wider block">METODE PEMBAYARAN</span>
                        <div class="text-base font-black text-blue-900 mt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-hand-holding-dollar text-emerald-600"></i> Tunai (Cash)
                        </div>
                        <span class="text-[10px] text-blue-700 mt-0.5 block">Loket Bendahara SPMB SMK KNBI</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-blue-200/60 text-blue-800 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                </div>

            </div>

            <!-- TABEL HISTORI TRANSAKSI SISWA (TRANSPARAN TANPA HILANG) -->
            @if(isset($studentPayments) && $studentPayments->count() > 0)
                <div class="space-y-2 pt-2">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i> Rincian Histori Transaksi Pembayaran:
                    </h4>
                    <div class="overflow-x-auto border border-slate-200 rounded-2xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700">
                                    <th class="py-2.5 px-3 text-center w-8">#</th>
                                    <th class="py-2.5 px-3">Tanggal Bayar</th>
                                    <th class="py-2.5 px-3">No. Invoice</th>
                                    <th class="py-2.5 px-3">Jenis Pembayaran</th>
                                    <th class="py-2.5 px-3 text-right">Nominal</th>
                                    <th class="py-2.5 px-3">Petugas Bendahara</th>
                                    <th class="py-2.5 px-3 text-center w-28">Status</th>
                                    <th class="py-2.5 px-3 text-center w-24">Kwitansi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @foreach($studentPayments as $idx => $p)
                                    <tr class="hover:bg-slate-50/70 transition">
                                        <td class="py-3 px-3 text-center text-slate-500 font-semibold">{{ $idx + 1 }}</td>
                                        <td class="py-3 px-3 font-mono text-slate-800">
                                            {{ \Carbon\Carbon::parse($p->payment_date ?? $p->created_at)->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="py-3 px-3 font-mono font-bold text-blue-700">{{ $p->invoice_number }}</td>
                                        <td class="py-3 px-3 font-bold text-slate-900">
                                            {{ $p->payment_type }}
                                            @if($p->notes)
                                                <span class="block text-[10px] text-slate-500 font-normal italic">"{{ $p->notes }}"</span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-right font-mono font-bold text-emerald-700 text-sm">
                                            Rp {{ number_format($p->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-slate-700">
                                            <span class="inline-flex items-center gap-1 font-semibold">
                                                <i class="fa-solid fa-user-check text-emerald-600 text-[10px]"></i>
                                                {{ $p->confirmedBy->name ?? 'Bendahara SPMB' }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            @if($p->status === 'confirmed')
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                                    Terverifikasi
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold text-[10px]">
                                                    {{ strtoupper($p->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <a href="{{ route('murid.payment.receipt', $p->id) }}" target="_blank"
                                               class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-300 rounded text-slate-700 text-[11px] font-bold inline-flex items-center gap-1 shadow-2xs transition"
                                               title="Cetak Bukti Pembayaran">
                                                <i class="fa-solid fa-print text-emerald-600"></i> Cetak
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="p-4 bg-slate-50 rounded-2xl border border-dashed border-slate-300 text-center text-xs text-slate-500 space-y-1">
                    <i class="fa-solid fa-info-circle text-slate-400 text-base mb-1"></i>
                    <p class="font-bold text-slate-700">Belum ada catatan pembayaran tunai yang masuk.</p>
                    <p class="text-[11px]">Silakan lakukan pembayaran biaya pendaftaran secara tunai langsung di Loket Panitia / Bendahara SPMB SMK Karya Nugraha Boyolali. Pembayaran Anda akan otomatis terverifikasi dan muncul di tabel ini.</p>
                </div>
            @endif

        </div>

        <!-- ================= 2. ALUR PROGRES PERSYARATAN ================= -->
        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Kelengkapan Dokumen Berkas</h3>
                    <p class="text-xs text-slate-500 font-medium">Unggah seluruh dokumen wajib di bawah ini agar berkas dapat diverifikasi panitia.</p>
                </div>
                <div class="text-right">
                    <span class="text-lg font-black text-blue-600">{{ $progressPercentage }}%</span>
                    <span class="text-xs text-slate-500 block">Selesai</span>
                </div>
            </div>

            <!-- PROGRESS BAR -->
            <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-gradient-to-r from-blue-600 to-emerald-500 transition-all duration-500" style="width: {{ $progressPercentage }}%;"></div>
            </div>
        </div>

        <!-- ================= 3. GRID UTAMA: UPLOAD BERKAS & DATA BIODATA ================= -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- KOLOM KIRI: FORM UPLOAD BERKAS (7 COLS) -->
            <div class="lg:col-span-7 space-y-6">
                
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 font-bold flex items-center justify-center text-sm">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                            </span>
                            <h3 class="text-base font-extrabold text-slate-900">Unggah Berkas Persyaratan</h3>
                        </div>
                        <span class="text-xs text-slate-500">Format: JPG, PNG, atau PDF (Maks. 3MB)</span>
                    </div>

                    <!-- DAFTAR PERSYARATAN DOKUMEN -->
                    <div class="space-y-4">
                        @php
                            $docTypes = [
                                'kartu_keluarga' => ['label' => 'Kartu Keluarga (KK)', 'wajib' => true],
                                'akta_kelahiran' => ['label' => 'Akta Kelahiran', 'wajib' => true],
                                'ijazah_skl' => ['label' => 'Ijazah / Surat Keterangan Lulus (SKL)', 'wajib' => true],
                                'raport' => ['label' => 'Scan Buku Raport', 'wajib' => false],
                                'pas_foto' => ['label' => 'Pas Foto Berwarna (3x4)', 'wajib' => false],
                            ];
                        @endphp

                        @foreach ($docTypes as $key => $info)
                            @php
                                $doc = $registration ? $registration->documents->firstWhere('document_type', $key) : null;
                            @endphp

                            <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <h4 class="text-sm font-bold text-slate-900">{{ $info['label'] }}</h4>
                                        @if($info['wajib'])
                                            <span class="text-[10px] uppercase font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-200">Wajib</span>
                                        @endif
                                    </div>
                                    
                                    @if($doc)
                                        <p class="text-xs text-emerald-600 font-semibold flex items-center gap-1.5">
                                            <i class="fa-solid fa-circle-check"></i> {{ $doc->original_filename }} ({{ $doc->file_size }} KB)
                                        </p>
                                    @else
                                        <p class="text-xs text-slate-400">Belum diunggah</p>
                                    @endif
                                </div>

                                <div>
                                    @if($doc)
                                        <div class="flex items-center gap-2">
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="px-3 py-1.5 text-xs font-bold text-slate-700 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition">
                                                <i class="fa-solid fa-eye"></i> Lihat
                                            </a>
                                            <!-- Form Re-upload -->
                                            <label class="px-3 py-1.5 text-xs font-bold text-blue-600 bg-blue-50 border border-blue-200 rounded-lg hover:bg-blue-100 cursor-pointer transition">
                                                <i class="fa-solid fa-arrows-rotate"></i> Ganti
                                                <input type="file" class="hidden" onchange="autoUploadDoc('{{ $key }}', this)">
                                            </label>
                                        </div>
                                    @else
                                        <label class="px-4 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl cursor-pointer shadow-sm hover:shadow transition flex items-center gap-1.5">
                                            <i class="fa-solid fa-arrow-up-from-bracket"></i> Unggah File
                                            <input type="file" class="hidden" onchange="autoUploadDoc('{{ $key }}', this)">
                                        </label>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Hidden Form untuk Auto Upload via JavaScript -->
                    <form id="doc-upload-form" action="{{ route('murid.upload.document') }}" method="POST" enctype="multipart/form-data" class="hidden">
                        @csrf
                        <input type="hidden" name="document_type" id="upload_doc_type">
                        <input type="file" name="file" id="upload_file_input">
                    </form>

                </div>

            </div>

            <!-- KOLOM KANAN: RINGKASAN DATA PENDAFTAR & CETAK (5 COLS) -->
            <div class="lg:col-span-5 space-y-6">
                
                <!-- KARTU CETAK BUKTI -->
                <div class="bg-gradient-to-br from-indigo-900 to-slate-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center text-xl text-amber-300 mb-4">
                        <i class="fa-solid fa-print"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-white">Kartu Bukti Pendaftaran</h3>
                    <p class="text-xs text-slate-300 mt-1 leading-relaxed">
                        Unduh dan cetak kartu bukti pendaftaran untuk dibawa saat verifikasi langsung atau pelaksanaan tes wawancara di sekolah.
                    </p>
                    <a href="{{ route('murid.print.card') }}" target="_blank" class="mt-5 w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white font-bold rounded-xl shadow-lg shadow-blue-500/30 transition text-sm">
                        <i class="fa-solid fa-file-pdf"></i> Cetak Kartu Pendaftaran
                    </a>
                </div>

                <!-- RINGKASAN BIODATA PENDAFTAR -->
                <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-sm">
                    <h3 class="text-sm font-extrabold text-slate-900 pb-3 mb-4 border-b border-slate-100 flex items-center gap-2">
                        <i class="fa-solid fa-user-check text-blue-600"></i> Ringkasan Identitas
                    </h3>

                    <dl class="space-y-3 text-xs">
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <dt class="text-slate-500">Nama Lengkap</dt>
                            <dd class="font-bold text-slate-900">{{ $user->name }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <dt class="text-slate-500">NISN</dt>
                            <dd class="font-bold text-slate-900">{{ $user->nisn ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <dt class="text-slate-500">No. WhatsApp</dt>
                            <dd class="font-bold text-slate-900">{{ $user->phone }}</dd>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <dt class="text-slate-500">Asal Sekolah</dt>
                            <dd class="font-bold text-slate-900">{{ $registration->school->asal_sekolah ?? '-' }}</dd>
                        </div>
                        <div class="flex justify-between py-1">
                            <dt class="text-slate-500">Alamat</dt>
                            <dd class="font-bold text-slate-900 text-right">{{ $registration->biodata->desa ?? '' }}, {{ $registration->biodata->kabupaten ?? '' }}</dd>
                        </div>
                    </dl>

                    <div class="mt-6 pt-4 border-t border-slate-100 space-y-2">
                        <a href="{{ route('murid.print.card') }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs shadow-sm transition">
                            <i class="fa-solid fa-print"></i> Cetak Bukti Pendaftaran (F4)
                        </a>
                        <a href="https://wa.me/6285290944090" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold rounded-xl border border-emerald-200 text-xs transition">
                            <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i> Bantuan Panitia SPMB
                        </a>
                    </div>
                </div>

            </div>

        </div>

    </main>

    <!-- FOOTER DASHBOARD -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500 font-medium">
        &copy; {{ date('Y') }} SMK KARYA NUGRAHA BOYOLALI. All rights reserved.
    </footer>

    <!-- JS SCRIPT AUTO UPLOAD -->
    <script>
        function autoUploadDoc(docType, inputElement) {
            if (inputElement.files && inputElement.files[0]) {
                const hiddenForm = document.getElementById('doc-upload-form');
                const hiddenType = document.getElementById('upload_doc_type');
                const hiddenFile = document.getElementById('upload_file_input');

                hiddenType.value = docType;
                
                // Copy file to hidden form
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(inputElement.files[0]);
                hiddenFile.files = dataTransfer.files;

                hiddenForm.submit();
            }
        }
    </script>

</body>
</html>
