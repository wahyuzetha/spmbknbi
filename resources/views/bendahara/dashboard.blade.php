<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Bendahara SPMB | SMK Karya Nugraha Boyolali</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
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
                    },
                    colors: {
                        theme: {
                            teal: '#00a884',
                            tealDark: '#008f70',
                            sidebar: '#222d32',
                            sidebarHover: '#1e282c',
                            sidebarText: '#b8c7ce',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 4px;
        }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-800 flex h-screen overflow-hidden">

    <!-- ================= 1. SIDEBAR BENDAHARA ================= -->
    <aside id="sidebar" class="w-64 bg-[#222d32] text-[#b8c7ce] flex flex-col flex-shrink-0 transition-all duration-300 z-30">
        
        <!-- SIDEBAR HEADER: LOGO KNBI & STATUS ONLINE -->
        <div class="h-20 bg-[#1a2226] flex items-center gap-3 px-4 border-b border-black/20">
            <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center overflow-hidden">
                @if(!empty($schoolLogo ?? $settings['school_logo'] ?? ''))
                    <img src="{{ asset($schoolLogo ?? $settings['school_logo']) }}" alt="Logo KNBI" class="max-w-full max-h-full object-contain">
                @else
                    <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow">
                        <polygon points="50,5 95,38 78,92 22,92 5,38" fill="#1e40af" stroke="#ffffff" stroke-width="2"/>
                        <polygon points="50,10 90,40 75,87 25,87 10,40" fill="#2563eb"/>
                        <circle cx="50" cy="50" r="28" fill="#ffffff"/>
                        <circle cx="50" cy="50" r="22" fill="#1d4ed8"/>
                        <path d="M50,34 L54,48 L46,48 Z" fill="#facc15"/>
                        <rect x="48" y="48" width="4" height="16" fill="#ffffff"/>
                        <rect x="18" y="72" width="64" height="12" rx="3" fill="#1e3a8a"/>
                        <text x="50" y="81" font-size="6" font-weight="bold" fill="#ffffff" text-anchor="middle" font-family="sans-serif">SMK KARYA NUGRAHA</text>
                    </svg>
                @endif
            </div>
            <div>
                <div class="text-sm font-black text-white tracking-wide">KNBI SPMB</div>
                <div class="text-xs text-emerald-400 flex items-center gap-1.5 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Bendahara Online
                </div>
            </div>
        </div>

        <!-- PROFIL USER LOGIN -->
        <div class="p-3.5 bg-[#1e282c]/70 border-b border-slate-700/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-sm shadow">
                <i class="fa-solid fa-cash-register"></i>
            </div>
            <div class="overflow-hidden">
                <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name ?? 'Bendahara SPMB' }}</div>
                <div class="text-[10px] text-amber-400 font-semibold uppercase tracking-wider">Bendahara SPMB</div>
            </div>
        </div>

        <!-- SIDEBAR NAVIGATION MENU -->
        <nav class="flex-1 overflow-y-auto custom-scroll py-3 px-2 space-y-1.5 text-xs font-semibold">
            
            <div class="px-3 pt-2 pb-1 text-[10px] uppercase font-bold text-slate-400 tracking-wider">Menu Keuangan SPMB</div>

            <!-- MENU 1: REKAP PEMBAYARAN -->
            <a href="{{ route('bendahara.dashboard', ['view' => 'administrasi']) }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-lg transition {{ in_array($currentView, ['administrasi', 'dashboard']) ? 'bg-[#00a65a] text-white shadow-md font-bold' : 'hover:bg-[#1e282c] hover:text-white' }}">
                <i class="fa-solid fa-receipt text-sm w-4 text-center"></i>
                <span class="flex-1">Rekap Pembayaran Siswa</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] bg-white/20 font-mono">{{ $groupedPayments->count() }}</span>
            </a>

            <!-- MENU 2: PENGELUARAN KAS -->
            <a href="{{ route('bendahara.dashboard', ['view' => 'pengeluaran']) }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-lg transition {{ $currentView === 'pengeluaran' ? 'bg-[#00a65a] text-white shadow-md font-bold' : 'hover:bg-[#1e282c] hover:text-white' }}">
                <i class="fa-solid fa-money-bill-transfer text-sm w-4 text-center"></i>
                <span class="flex-1">Pengeluaran Kas SPMB</span>
                <span class="px-1.5 py-0.5 rounded text-[10px] bg-white/20 font-mono">{{ $allExpenses->count() }}</span>
            </a>

            <!-- TOMBOL CEPAT INPUT PEMBAYARAN -->
            <div class="pt-4 px-1">
                <button type="button" onclick="openInputPaymentModal()" 
                        class="w-full py-2.5 px-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-lg transition flex items-center justify-center gap-2 text-xs">
                    <i class="fa-solid fa-circle-plus"></i>
                    <span>+ Input Pembayaran</span>
                </button>
            </div>

            <div class="px-3 pt-5 pb-1 text-[10px] uppercase font-bold text-slate-400 tracking-wider">Akses Cepat Cetak</div>

            <a href="{{ route('bendahara.administrasi.payment.exportExcel') }}" 
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition text-slate-300">
                <i class="fa-solid fa-file-excel text-emerald-400 text-sm w-4 text-center"></i>
                <span>Export Excel Rekap</span>
            </a>

            <a href="{{ route('home') }}" target="_blank"
               class="flex items-center gap-3 px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition text-slate-300">
                <i class="fa-solid fa-globe text-blue-400 text-sm w-4 text-center"></i>
                <span>Lihat Website Utama</span>
            </a>

        </nav>

        <!-- FOOTER LOGOUT -->
        <div class="p-3 bg-[#1a2226] border-t border-black/30">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full py-2 px-3 rounded-lg bg-rose-600/80 hover:bg-rose-700 text-white text-xs font-bold transition flex items-center justify-center gap-2 shadow">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Keluar Aplikasi</span>
                </button>
            </form>
        </div>

    </aside>

    <!-- ================= 2. MAIN CONTENT AREA ================= -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        
        <!-- TOPBAR -->
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-20">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleSidebar()" class="text-slate-500 hover:text-slate-800 p-2 rounded-lg hover:bg-slate-100 transition">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <div class="hidden sm:block">
                    <h2 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <span>Portal Bendahara SPMB</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold border border-emerald-200">Kasir & Rekap</span>
                    </h2>
                    <p class="text-xs text-slate-400">SMK Karya Nugraha Boyolali &bull; Tahun Ajaran {{ $academicYear->name ?? '2026/2027' }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2.5">
                <!-- TOMBOL ACTION CEPAT -->
                <button type="button" onclick="openInputPaymentModal()" 
                        class="px-3.5 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-cash-register"></i>
                    <span class="hidden sm:inline">Input Pembayaran Siswa</span>
                    <span class="sm:hidden">+ Bayar</span>
                </button>

                <!-- EXPORT EXCEL -->
                <a href="{{ route('bendahara.administrasi.payment.exportExcel') }}" 
                   class="px-3 py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 font-bold text-xs rounded-lg transition flex items-center gap-1.5 cursor-pointer"
                   title="Export Rekap Pembayaran ke Excel">
                    <i class="fa-solid fa-file-excel text-emerald-600"></i>
                    <span class="hidden md:inline">Export Excel</span>
                </a>

                <!-- PROFIL LOGOUT -->
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-slate-100 transition" title="Logout">
                        <i class="fa-solid fa-power-off text-base"></i>
                    </button>
                </form>
            </div>
        </header>

        <!-- SUB HEADER / NOTIFIKASI -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-5 bg-[#f4f6f9]">
            
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    @if(session('last_payment_id'))
                        <a href="{{ route('bendahara.administrasi.payment.receipt', session('last_payment_id')) }}" target="_blank"
                           class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition flex items-center gap-1 shadow-sm">
                            <i class="fa-solid fa-print"></i> Cetak Kwitansi Sekarang
                        </a>
                    @endif
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2.5 shadow-xs">
                    <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-xs">
                    <div class="font-bold">Terjadi Kesalahan:</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- 4 KARTU METRIK KEUANGAN SPMB -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- KARTU 1: TOTAL KAS MASUK -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Kas Masuk</div>
                        <div class="text-xl font-extrabold text-emerald-600 mt-1">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Akumulasi pembayaran pendaftar</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>

                <!-- KARTU 2: SISWA SUDAH BAYAR -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Calon Siswa Bayar</div>
                        <div class="text-xl font-extrabold text-blue-600 mt-1">{{ $totalSiswaBayar }} Siswa</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">{{ $multiTransactionCount }} siswa mencicil bertahap</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                </div>

                <!-- KARTU 3: TOTAL TRANSAKSI KWITANSI -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</div>
                        <div class="text-xl font-extrabold text-indigo-600 mt-1">{{ $totalTransaksi }} Kwitansi</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Pembayaran tunai terkonfirmasi</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                </div>

                <!-- KARTU 4: SALDO KAS BERSIH -->
                <div class="bg-white rounded-xl p-4 border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Sisa Saldo Kas SPMB</div>
                        <div class="text-xl font-extrabold text-amber-600 mt-1">Rp {{ number_format($saldoKas, 0, ',', '.') }}</div>
                        <div class="text-[11px] text-slate-400 mt-0.5">Pengeluaran: Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</div>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-vault"></i>
                    </div>
                </div>

            </div>

            <!-- TABS SUB-MENU KEUANGAN -->
            <div class="flex items-center gap-2 border-b border-slate-200 pb-2">
                <a href="{{ route('bendahara.dashboard', ['view' => 'administrasi']) }}" 
                   class="px-4 py-2 rounded-lg font-bold text-xs transition flex items-center gap-2 {{ in_array($currentView, ['administrasi', 'dashboard']) ? 'bg-emerald-600 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                    <i class="fa-solid fa-table-list"></i>
                    <span>Rekapitulasi Pembayaran Siswa</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 font-mono">{{ $groupedPayments->count() }}</span>
                </a>
                <a href="{{ route('bendahara.dashboard', ['view' => 'pengeluaran']) }}" 
                   class="px-4 py-2 rounded-lg font-bold text-xs transition flex items-center gap-2 {{ $currentView === 'pengeluaran' ? 'bg-emerald-600 text-white shadow' : 'bg-white text-slate-600 hover:bg-slate-50 border border-slate-200' }}">
                    <i class="fa-solid fa-money-bill-transfer"></i>
                    <span>Pengeluaran Kas SPMB</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-white/20 font-mono">{{ $allExpenses->count() }}</span>
                </a>
            </div>

            <!-- ================= VIEW 1: REKAPITULASI PEMBAYARAN SISWA ================= -->
            @if(in_array($currentView, ['administrasi', 'dashboard']))
                
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <!-- HEADER TABEL & TOOLBAR -->
                    <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-cash-register text-emerald-600"></i>
                                <span>Rekapitulasi Seluruh Pembayaran Calon Siswa</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Seluruh transaksi pembayaran tunai dikelompokkan per calon siswa lengkap dengan riwayat bertahap.</p>
                        </div>

                        <!-- TOMBOL AKSI TOOLBAR -->
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" onclick="openInputPaymentModal()" 
                                    class="px-3.5 py-1.5 bg-[#00a65a] hover:bg-[#008f4c] text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-circle-plus"></i>
                                <span>Input Pembayaran Baru</span>
                            </button>
                            <button type="button" onclick="copyTable('table-administrasi')" 
                                    class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded text-xs font-semibold text-slate-700 transition flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-copy"></i>
                                <span>Copy</span>
                            </button>
                            <button type="button" onclick="exportTableToCSV('table-administrasi', 'rekap_pembayaran_spmb.csv')" 
                                    class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded text-xs font-semibold text-slate-700 transition flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-file-csv"></i>
                                <span>CSV</span>
                            </button>
                            <a href="{{ route('bendahara.administrasi.payment.exportExcel') }}" 
                               class="px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded text-xs font-bold text-emerald-800 transition flex items-center gap-1 cursor-pointer" title="Export Excel Resmi">
                                <i class="fa-solid fa-file-excel text-emerald-600"></i>
                                <span>Excel</span>
                            </a>
                        </div>
                    </div>

                    <!-- FILTER & PENCARIAN LIVE -->
                    <div class="p-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <div class="flex items-center gap-1.5 text-slate-600">
                                <span>Tampilkan:</span>
                                <select id="administrasi-page-size-select" onchange="changeAdministrasiPageSize(this.value)" 
                                        class="px-2.5 py-1.5 bg-white border border-slate-300 rounded font-semibold text-slate-700 focus:outline-none focus:border-teal-500">
                                    <option value="-1" selected>Semua Data</option>
                                    <option value="10">10 Baris</option>
                                    <option value="25">25 Baris</option>
                                    <option value="50">50 Baris</option>
                                    <option value="100">100 Baris</option>
                                </select>
                            </div>

                            <div class="flex items-center gap-1.5 text-slate-600">
                                <span>Jurusan:</span>
                                <select id="administrasi-major-filter" onchange="filterAdministrasiTable()" 
                                        class="px-2.5 py-1.5 bg-white border border-slate-300 rounded font-semibold text-slate-700 focus:outline-none focus:border-teal-500">
                                    <option value="">Semua Jurusan</option>
                                    @foreach($majors as $m)
                                        <option value="{{ strtolower($m->name) }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="relative w-full sm:w-72">
                            <input type="text" id="search-administrasi" onkeyup="filterAdministrasiTable()" 
                                   placeholder="Cari no. daftar, nama siswa, sekolah..." 
                                   class="w-full pl-8 pr-3 py-1.5 bg-white border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:border-teal-500">
                            <i class="fa-solid fa-magnifying-glass absolute left-2.5 top-2.5 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- TABEL DATA PEMBAYARAN GLOBAL -->
                    <div class="overflow-x-auto">
                        <table id="table-administrasi" class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                                    <th class="py-3 px-3 text-center w-12">No</th>
                                    <th class="py-3 px-3">No. Pendaftar</th>
                                    <th class="py-3 px-3">Nama Lengkap Calon Siswa</th>
                                    <th class="py-3 px-3">Asal Sekolah</th>
                                    <th class="py-3 px-3">Jurusan Pilihan</th>
                                    <th class="py-3 px-3 text-center">Riwayat</th>
                                    <th class="py-3 px-3 text-right">Total Masuk (Rp)</th>
                                    <th class="py-3 px-3">Tgl Terakhir</th>
                                    <th class="py-3 px-3">Penerima</th>
                                    <th class="py-3 px-3 text-center w-36">Aksi Cetak</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($groupedPayments as $idx => $item)
                                    @php
                                        $reg = $item->registration;
                                        $userSiswa = $reg?->user;
                                        $major = $reg?->major;
                                        $school = $reg?->school;
                                        $historyList = $item->payments->map(function($p) {
                                            return [
                                                'id' => $p->id,
                                                'invoice' => $p->invoice_number,
                                                'type' => $p->payment_type,
                                                'amount' => $p->amount,
                                                'amount_formatted' => 'Rp ' . number_format($p->amount, 0, ',', '.'),
                                                'date' => \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y'),
                                                'notes' => $p->notes,
                                                'confirmed_by' => $p->confirmedBy->name ?? 'Bendahara',
                                                'print_url' => route('bendahara.administrasi.payment.receipt', $p->id),
                                            ];
                                        });
                                    @endphp
                                    <tr class="hover:bg-slate-50/80 transition administrasi-row">
                                        <td class="py-3.5 px-3 text-center text-slate-500 font-semibold administrasi-row-no">{{ $idx + 1 }}</td>
                                        <td class="py-3.5 px-3 font-mono font-bold text-emerald-700">
                                            {{ $reg?->registration_number ?? '-' }}
                                        </td>
                                        <td class="py-3.5 px-3 font-bold text-slate-800">
                                            <div>{{ $userSiswa?->name ?? 'Calon Siswa' }}</div>
                                            <div class="text-[11px] text-slate-400 font-normal font-mono">{{ $userSiswa?->phone ?? '-' }}</div>
                                        </td>
                                        <td class="py-3.5 px-3 text-slate-600">
                                            {{ $school?->asal_sekolah ?? '-' }}
                                        </td>
                                        <td class="py-3.5 px-3">
                                            <div class="font-bold text-slate-800">{{ $major?->name ?? '-' }}</div>
                                            @if(!empty($major?->industry_class))
                                                <div class="text-[10px] text-indigo-600 font-semibold">Kelas: {{ $major->industry_class }}</div>
                                            @endif
                                            <button type="button" onclick="openChangeMajorModal('{{ $reg?->id }}', '{{ addslashes($userSiswa?->name ?? '') }}', '{{ $reg?->registration_number }}', '{{ $major?->id }}')"
                                                    class="text-[10px] text-blue-600 hover:text-blue-800 font-semibold underline mt-0.5 block">
                                                Ubah Jurusan
                                            </button>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            @if($item->is_multi)
                                                <button type="button" 
                                                        onclick='openPaymentHistoryModal("{{ addslashes($userSiswa?->name ?? 'Calon Siswa') }}", "{{ $reg?->registration_number }}", "Rp {{ number_format($item->total_amount, 0, ',', '.') }}", {{ json_encode($historyList) }}, "{{ route('bendahara.administrasi.payment.globalReceipt', $item->registration_id) }}")'
                                                        class="px-2.5 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 font-extrabold text-[11px] hover:bg-indigo-100 transition shadow-xs inline-flex items-center gap-1"
                                                        title="Klik untuk melihat rincian seluruh pembayaran">
                                                    <i class="fa-solid fa-list-check text-[10px]"></i>
                                                    <span>{{ $item->transaction_count }}x Bayar</span>
                                                </button>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold text-[10px]">
                                                    1x Pembayaran
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3.5 px-3 text-right font-bold text-emerald-700 font-mono text-sm">
                                            Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3.5 px-3 text-slate-600 text-[11px]">
                                            {{ \Carbon\Carbon::parse($item->last_payment_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3.5 px-3 text-slate-600 text-[11px]">
                                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-semibold">
                                                {{ $item->last_confirmed_by->name ?? 'Bendahara' }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <!-- CETAK SATUAN / BUKA HISTORI -->
                                                @if($item->is_multi)
                                                    <button type="button" 
                                                            onclick='openPaymentHistoryModal("{{ addslashes($userSiswa?->name ?? 'Calon Siswa') }}", "{{ $reg?->registration_number }}", "Rp {{ number_format($item->total_amount, 0, ',', '.') }}", {{ json_encode($historyList) }}, "{{ route('bendahara.administrasi.payment.globalReceipt', $item->registration_id) }}")'
                                                            class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-700 text-white rounded font-bold text-[11px] transition shadow flex items-center gap-1"
                                                            title="Buka rincian & kwitansi satuan">
                                                        <i class="fa-solid fa-receipt"></i> Rincian
                                                    </button>
                                                @else
                                                    @php $singlePayment = $item->payments->first(); @endphp
                                                    <a href="{{ route('bendahara.administrasi.payment.receipt', $singlePayment->id) }}" target="_blank"
                                                       class="px-2.5 py-1 bg-blue-600 hover:bg-blue-700 text-white rounded font-bold text-[11px] transition shadow flex items-center gap-1"
                                                       title="Cetak Kwitansi Satuan">
                                                        <i class="fa-solid fa-print"></i> Kwitansi
                                                    </a>
                                                @endif

                                                <!-- CETAK REKAPITULASI GLOBAL -->
                                                <a href="{{ route('bendahara.administrasi.payment.globalReceipt', $item->registration_id) }}" target="_blank"
                                                   class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded font-bold text-[11px] transition shadow flex items-center gap-1"
                                                   title="Cetak Kwitansi Rekapitulasi Global Resmi">
                                                    <i class="fa-solid fa-file-invoice"></i> Rekap
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="py-12 text-center text-slate-400">
                                            <i class="fa-solid fa-cash-register text-4xl mb-3 text-slate-300"></i>
                                            <p class="font-bold text-slate-600">Belum ada transaksi pembayaran calon siswa.</p>
                                            <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Input Pembayaran Baru" di pojok kanan atas untuk mulai mencatat pembayaran tunai.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER PAGINASI TABEL -->
                    <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                        <div id="administrasi-table-info" class="text-slate-600 font-medium">
                            Menampilkan {{ $groupedPayments->count() }} data calon siswa yang telah melakukan pembayaran.
                        </div>
                        <div id="administrasi-table-pagination" class="flex items-center gap-1 select-none">
                            <!-- Populated via JavaScript pagination -->
                        </div>
                    </div>

                </div>

            @endif

            <!-- ================= VIEW 2: PENGELUARAN KAS SPMB ================= -->
            @if($currentView === 'pengeluaran')
                
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <div class="p-4 sm:p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                                <i class="fa-solid fa-money-bill-transfer text-rose-600"></i>
                                <span>Pencatatan Pengeluaran Kas SPMB</span>
                            </h3>
                            <p class="text-xs text-slate-500 mt-0.5">Catatan seluruh penggunaan uang pendaftaran untuk operasional SPMB, seragam, konsumsi, dan kepanitiaan.</p>
                        </div>
                        <button type="button" onclick="openExpenseModal()" 
                                class="px-3.5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-2 cursor-pointer">
                            <i class="fa-solid fa-circle-plus"></i>
                            <span>Catat Pengeluaran Baru</span>
                        </button>
                    </div>

                    <!-- TABEL PENGELUARAN -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-100 text-slate-700 font-bold uppercase tracking-wider border-b border-slate-200 text-[11px]">
                                    <th class="py-3 px-3 text-center w-12">No</th>
                                    <th class="py-3 px-3">Kode Bukti</th>
                                    <th class="py-3 px-3">Nama Keperluan / Judul</th>
                                    <th class="py-3 px-3">Kategori</th>
                                    <th class="py-3 px-3 text-right">Nominal (Rp)</th>
                                    <th class="py-3 px-3">Tanggal</th>
                                    <th class="py-3 px-3">Penerima</th>
                                    <th class="py-3 px-3">Petugas Pencatat</th>
                                    <th class="py-3 px-3 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($allExpenses as $eIdx => $exp)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3 px-3 text-center text-slate-500">{{ $eIdx + 1 }}</td>
                                        <td class="py-3 px-3 font-mono font-bold text-rose-700">{{ $exp->expense_code }}</td>
                                        <td class="py-3 px-3 font-bold text-slate-800">
                                            <div>{{ $exp->title }}</div>
                                            <div class="text-[11px] text-slate-400 font-normal">{{ $exp->description }}</div>
                                        </td>
                                        <td class="py-3 px-3">
                                            <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-700">
                                                {{ $exp->category }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right font-bold text-rose-700 font-mono text-sm">
                                            Rp {{ number_format($exp->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="py-3 px-3 text-slate-600 text-[11px]">
                                            {{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3 px-3 text-slate-700">{{ $exp->recipient }}</td>
                                        <td class="py-3 px-3 text-slate-600">{{ $exp->creator->name ?? 'Bendahara' }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" 
                                                        onclick="openEditExpenseModal('{{ $exp->id }}', '{{ addslashes($exp->title) }}', '{{ $exp->amount }}', '{{ \Carbon\Carbon::parse($exp->expense_date)->format('Y-m-d') }}', '{{ addslashes($exp->category) }}', '{{ addslashes($exp->recipient) }}', '{{ addslashes($exp->description) }}')"
                                                        class="p-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded text-xs transition" title="Edit Pengeluaran">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                <form action="{{ route('bendahara.administrasi.expense.delete', $exp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengeluaran ini?')">
                                                    @csrf
                                                    <button type="submit" class="p-1.5 bg-rose-600 hover:bg-rose-700 text-white rounded text-xs transition" title="Hapus Pengeluaran">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="py-12 text-center text-slate-400">
                                            <i class="fa-solid fa-money-bill-transfer text-4xl mb-3 text-slate-300"></i>
                                            <p class="font-bold text-slate-600">Belum ada catatan pengeluaran kas SPMB.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

            @endif

        </main>
    </div>

    <!-- ================= MODAL INPUT PEMBAYARAN TUNAI (BENDAHARA) ================= -->
    <div id="modal-input-payment" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh] animate-in fade-in duration-150">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-base">
                    <i class="fa-solid fa-cash-register text-[#00a65a]"></i>
                    <h3>Input Pembayaran Tunai Calon Siswa (Bendahara)</h3>
                </div>
                <button type="button" onclick="closeInputPaymentModal()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('bendahara.administrasi.payment.store') }}" method="POST" autocomplete="off" class="space-y-4 text-xs font-medium text-slate-700">
                @csrf
                
                <!-- PENCARIAN & PEMILIHAN CALON SISWA -->
                <div class="space-y-1.5">
                    <label class="font-bold text-slate-800 flex items-center justify-between">
                        <span>Pilih Calon Siswa (Cari Nama / No. Pendaftar) *</span>
                        <span class="text-[11px] text-emerald-600 font-semibold" id="student-count-indicator">Menampilkan Semua Data ({{ count($candidatesForPayment ?? []) }} Calon Siswa)</span>
                    </label>
                    
                    <div class="relative">
                        <input type="text" id="picker-search-input" onkeyup="filterCandidateDropdown()" 
                               placeholder="Ketik nama siswa atau nomor pendaftar untuk mencari..." 
                               class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:border-teal-500 mb-1.5">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>

                    <select name="registration_id" id="payment-student-select" required onchange="onPaymentStudentSelected(this)"
                            class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-teal-500">
                        <option value="" disabled selected>-- Pilih Calon Siswa Terdaftar (Semua Data) --</option>
                        @foreach($candidatesForPayment ?? [] as $c)
                            <option value="{{ $c->id }}" 
                                    class="candidate-option"
                                    data-number="{{ $c->registration_number }}" 
                                    data-name="{{ $c->name }}" 
                                    data-major="{{ $c->major }}" 
                                    data-industry="{{ $c->industry_class }}" 
                                    data-school="{{ $c->school }}" 
                                    data-phone="{{ $c->phone }}">
                                {{ $c->registration_number }} - {{ $c->name }} ({{ $c->major }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- KARTU PREVIEW SISWA TERPILIH -->
                <div id="payment-student-preview" class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1 text-slate-700 hidden">
                    <div class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Informasi Pendaftar Terpilih:</div>
                    <div class="font-bold text-slate-900 text-sm" id="prev-name">-</div>
                    <div class="text-[11px] text-slate-600 flex items-center gap-2 flex-wrap">
                        <span>No. Pendaftar: <strong class="font-mono text-emerald-700" id="prev-number">-</strong></span>
                        <span>•</span>
                        <span>Jurusan: <strong id="prev-major">-</strong></span>
                        <span>•</span>
                        <span>Asal Sekolah: <strong id="prev-school">-</strong></span>
                    </div>
                </div>

                <!-- TANGGAL PEMBAYARAN -->
                <div class="space-y-1">
                    <label class="font-bold text-slate-800">Tanggal Pembayaran *</label>
                    <input type="date" name="payment_date" id="payment-input-date" required value="{{ date('Y-m-d') }}" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-teal-500">
                </div>

                <!-- INPUT BESARAN NOMINAL DENGAN QUICK BUTTONS -->
                <div class="space-y-2">
                    <label class="font-bold text-slate-800 flex items-center justify-between">
                        <span>Besaran Nominal Pembayaran (Rp) *</span>
                        <span class="text-[11px] text-slate-500 font-mono" id="nominal-preview">Rp 0</span>
                    </label>
                    <input type="number" name="amount" id="payment-input-amount" required min="1000" step="1000" placeholder="Contoh: 150000"
                           oninput="updateNominalPreview(this.value)"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:outline-none focus:border-teal-500 font-mono">
                    
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[10px] text-slate-400 font-semibold mr-1">Nominal Cepat:</span>
                        <button type="button" onclick="setNominal(50000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 50.000</button>
                        <button type="button" onclick="setNominal(100000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 100.000</button>
                        <button type="button" onclick="setNominal(150000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 150.000</button>
                        <button type="button" onclick="setNominal(200000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 200.000</button>
                        <button type="button" onclick="setNominal(500000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 500.000</button>
                        <button type="button" onclick="setNominal(1000000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 1.000.000</button>
                    </div>
                </div>

                <!-- JENIS TRANSAKSI & KETERANGAN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-800">Jenis Pembayaran</label>
                        <input type="text" name="payment_type" id="payment-input-type" value="Pembayaran Administrasi SPMB" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-teal-500">
                    </div>
                    <div class="space-y-1">
                        <label class="font-bold text-slate-800">Catatan / Bukti Tunai</label>
                        <input type="text" name="notes" placeholder="Contoh: Diterima tunai lunas di meja bendahara" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:border-teal-500">
                    </div>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2 border-t border-slate-100">
                    <button type="button" onclick="closeInputPaymentModal()" 
                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-[#00a65a] hover:bg-[#008f4c] text-white font-bold text-xs rounded-lg shadow-md transition flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i> Simpan Transaksi Pembayaran
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- ================= MODAL RIWAYAT PEMBAYARAN BERTAPAK (MULTI-TRANSAKSI) ================= -->
    <div id="modal-payment-history" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh] animate-in fade-in duration-150">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                        <span>Riwayat Pembayaran Siswa</span>
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5" id="history-modal-subtitle">-</p>
                </div>
                <button type="button" onclick="closePaymentHistoryModal()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- RINCIAN TRANSAKSI -->
            <div class="space-y-3">
                <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between text-xs">
                    <div>
                        <span class="text-slate-500 font-semibold">Total Pembayaran Terakumulasi:</span>
                        <div class="text-lg font-black text-emerald-800 font-mono" id="history-modal-total">Rp 0</div>
                    </div>
                    <a id="history-modal-global-receipt" href="#" target="_blank"
                       class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition flex items-center gap-1.5 shadow">
                        <i class="fa-solid fa-file-invoice"></i> Cetak Kwitansi Rekap Global
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-bold border-b border-slate-200 text-[11px]">
                                <th class="py-2.5 px-3">No. Kwitansi</th>
                                <th class="py-2.5 px-3">Tanggal</th>
                                <th class="py-2.5 px-3">Jenis Pembayaran</th>
                                <th class="py-2.5 px-3 text-right">Nominal</th>
                                <th class="py-2.5 px-3">Penerima</th>
                                <th class="py-2.5 px-3 text-center">Cetak</th>
                            </tr>
                        </thead>
                        <tbody id="history-modal-table-body" class="divide-y divide-slate-100 font-medium">
                            <!-- Populated via JS -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closePaymentHistoryModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-lg transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>

    <!-- ================= MODAL GANTI JURUSAN DI MEJA BENDAHARA ================= -->
    <div id="modal-change-major" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200 animate-in fade-in duration-150">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-arrows-rotate text-blue-600"></i>
                    <span>Ubah Pilihan Jurusan Siswa</span>
                </h3>
                <button type="button" onclick="closeChangeMajorModal()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="form-change-major" action="" method="POST" class="space-y-3.5 text-xs font-medium">
                @csrf
                <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl space-y-1">
                    <div class="text-[11px] font-bold text-slate-500 uppercase">Calon Siswa:</div>
                    <div class="font-bold text-slate-900 text-sm" id="change-major-name">-</div>
                    <div class="text-[11px] text-emerald-700 font-mono font-bold" id="change-major-number">-</div>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-800">Pilih Jurusan Baru *</label>
                    <select name="major_id" id="change-major-select" required
                            class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-teal-500">
                        @foreach($majors as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->name }} @if(!empty($m->industry_class)) ({{ $m->industry_class }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="p-2.5 bg-amber-50 border border-amber-200 rounded-lg text-[11px] text-amber-800">
                    * Nomor pendaftaran siswa akan otomatis diperbarui dan disesuaikan dengan kode jurusan baru.
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeChangeMajorModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- ================= MODAL TAMBAH PENGELUARAN ================= -->
    <div id="modal-expense" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 space-y-4 shadow-2xl border border-slate-200 animate-in fade-in duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-money-bill-transfer text-rose-600"></i>
                    <span id="expense-modal-title">Catat Pengeluaran Kas Baru</span>
                </h3>
                <button type="button" onclick="closeExpenseModal()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form id="form-expense" action="{{ route('bendahara.administrasi.expense.store') }}" method="POST" class="space-y-3.5 text-xs font-medium">
                @csrf
                <div class="space-y-1">
                    <label class="font-bold text-slate-800">Nama Pengeluaran / Keperluan *</label>
                    <input type="text" name="title" id="exp-input-title" required placeholder="Contoh: Pembelian Seragam Siswa Gelombang 1"
                           class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold focus:outline-none focus:border-teal-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-800">Besaran Pengeluaran (Rp) *</label>
                        <input type="number" name="amount" id="exp-input-amount" required min="100" step="100" placeholder="1000000"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-bold font-mono focus:outline-none focus:border-teal-500">
                    </div>
                    <div class="space-y-1">
                        <label class="font-bold text-slate-800">Tanggal Pengeluaran *</label>
                        <input type="date" name="expense_date" id="exp-input-date" required value="{{ date('Y-m-d') }}"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold focus:outline-none focus:border-teal-500">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-800">Kategori</label>
                        <input type="text" name="category" id="exp-input-category" value="Operasional SPMB"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold focus:outline-none focus:border-teal-500">
                    </div>
                    <div class="space-y-1">
                        <label class="font-bold text-slate-800">Penerima Dana</label>
                        <input type="text" name="recipient" id="exp-input-recipient" placeholder="Nama orang / vendor"
                               class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-semibold focus:outline-none focus:border-teal-500">
                    </div>
                </div>

                <div class="space-y-1">
                    <label class="font-bold text-slate-800">Keterangan / Rincian Pengeluaran *</label>
                    <textarea name="description" id="exp-input-desc" required rows="2" placeholder="Tuliskan keterangan detail pembelian atau penggunaan uang kas..."
                              class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs font-medium focus:outline-none focus:border-teal-500"></textarea>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeExpenseModal()" class="px-4 py-2 bg-slate-100 text-slate-700 font-bold rounded-lg hover:bg-slate-200 transition">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-lg shadow transition">
                        Simpan Pengeluaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT FITUR BENDAHARA -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('absolute');
        }

        // ================= MODAL INPUT PEMBAYARAN =================
        function openInputPaymentModal() {
            document.getElementById('modal-input-payment').classList.remove('hidden');
        }

        function closeInputPaymentModal() {
            document.getElementById('modal-input-payment').classList.add('hidden');
        }

        function setNominal(val) {
            const input = document.getElementById('payment-input-amount');
            input.value = val;
            updateNominalPreview(val);
        }

        function updateNominalPreview(val) {
            const num = parseInt(val) || 0;
            document.getElementById('nominal-preview').innerText = 'Rp ' + num.toLocaleString('id-ID');
        }

        function filterCandidateDropdown() {
            const input = document.getElementById('picker-search-input');
            const filter = input.value.toLowerCase().trim();
            const select = document.getElementById('payment-student-select');
            const options = select.getElementsByClassName('candidate-option');
            let visibleCount = 0;

            for (let i = 0; i < options.length; i++) {
                const opt = options[i];
                const text = (opt.dataset.number + ' ' + opt.dataset.name + ' ' + opt.dataset.major + ' ' + opt.dataset.school).toLowerCase();
                if (text.includes(filter)) {
                    opt.style.display = '';
                    visibleCount++;
                } else {
                    opt.style.display = 'none';
                }
            }

            document.getElementById('student-count-indicator').innerText = filter ? `Ditemukan ${visibleCount} Calon Siswa` : `Menampilkan Semua Data (${options.length} Calon Siswa)`;
        }

        function onPaymentStudentSelected(sel) {
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.dataset.number) return;

            document.getElementById('payment-student-preview').classList.remove('hidden');
            document.getElementById('prev-name').innerText = opt.dataset.name;
            document.getElementById('prev-number').innerText = opt.dataset.number;
            document.getElementById('prev-major').innerText = opt.dataset.major + (opt.dataset.industry ? ' (' + opt.dataset.industry + ')' : '');
            document.getElementById('prev-school').innerText = opt.dataset.school;
        }

        // ================= MODAL RIWAYAT MULTI-TRANSAKSI =================
        function openPaymentHistoryModal(studentName, regNumber, totalFormatted, historyList, globalReceiptUrl) {
            document.getElementById('history-modal-subtitle').innerText = `${studentName} (${regNumber})`;
            document.getElementById('history-modal-total').innerText = totalFormatted;
            document.getElementById('history-modal-global-receipt').href = globalReceiptUrl;

            const tbody = document.getElementById('history-modal-table-body');
            tbody.innerHTML = '';

            historyList.forEach(item => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-slate-50 transition';
                tr.innerHTML = `
                    <td class="py-2 px-3 font-mono font-bold text-emerald-700">${item.invoice}</td>
                    <td class="py-2 px-3 text-slate-600">${item.date}</td>
                    <td class="py-2 px-3">
                        <span class="font-bold text-slate-800">${item.type}</span>
                        ${item.notes ? `<div class="text-[10px] text-slate-400">${item.notes}</div>` : ''}
                    </td>
                    <td class="py-2 px-3 text-right font-bold text-emerald-700 font-mono">${item.amount_formatted}</td>
                    <td class="py-2 px-3 text-slate-600">${item.confirmed_by}</td>
                    <td class="py-2 px-3 text-center">
                        <a href="${item.print_url}" target="_blank" class="px-2 py-0.5 rounded bg-blue-600 hover:bg-blue-700 text-white font-bold text-[10px] transition inline-flex items-center gap-1 shadow">
                            <i class="fa-solid fa-print"></i> Kwitansi
                        </a>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            document.getElementById('modal-payment-history').classList.remove('hidden');
        }

        function closePaymentHistoryModal() {
            document.getElementById('modal-payment-history').classList.add('hidden');
        }

        // ================= MODAL UBAH JURUSAN =================
        function openChangeMajorModal(regId, studentName, regNumber, currentMajorId) {
            document.getElementById('form-change-major').action = `/bendahara/administrasi/pembayaran/${regId}/change-major`;
            document.getElementById('change-major-name').innerText = studentName;
            document.getElementById('change-major-number').innerText = regNumber;
            document.getElementById('change-major-select').value = currentMajorId;
            document.getElementById('modal-change-major').classList.remove('hidden');
        }

        function closeChangeMajorModal() {
            document.getElementById('modal-change-major').classList.add('hidden');
        }

        // ================= MODAL EXPENSE =================
        function openExpenseModal() {
            document.getElementById('expense-modal-title').innerText = 'Catat Pengeluaran Kas Baru';
            document.getElementById('form-expense').action = '{{ route('bendahara.administrasi.expense.store') }}';
            document.getElementById('exp-input-title').value = '';
            document.getElementById('exp-input-amount').value = '';
            document.getElementById('exp-input-recipient').value = '';
            document.getElementById('exp-input-desc').value = '';
            document.getElementById('modal-expense').classList.remove('hidden');
        }

        function openEditExpenseModal(id, title, amount, date, category, recipient, desc) {
            document.getElementById('expense-modal-title').innerText = 'Edit Data Pengeluaran';
            document.getElementById('form-expense').action = `/bendahara/administrasi/pengeluaran/${id}/update`;
            document.getElementById('exp-input-title').value = title;
            document.getElementById('exp-input-amount').value = parseInt(amount);
            document.getElementById('exp-input-date').value = date;
            document.getElementById('exp-input-category').value = category;
            document.getElementById('exp-input-recipient').value = recipient;
            document.getElementById('exp-input-desc').value = desc;
            document.getElementById('modal-expense').classList.remove('hidden');
        }

        function closeExpenseModal() {
            document.getElementById('modal-expense').classList.add('hidden');
        }

        // ================= CLIENT-SIDE PAGINATION & FILTER TABLE =================
        let administrasiPageSize = -1;
        let administrasiCurrentPage = 1;
        let administrasiFilteredRows = [];

        function changeAdministrasiPageSize(val) {
            administrasiPageSize = parseInt(val);
            administrasiCurrentPage = 1;
            initAdministrasiPagination();
        }

        function filterAdministrasiTable() {
            administrasiCurrentPage = 1;
            initAdministrasiPagination();
        }

        function initAdministrasiPagination() {
            const table = document.getElementById('table-administrasi');
            if (!table) return;

            const allRows = Array.from(table.querySelectorAll('tbody tr.administrasi-row'));
            const search = (document.getElementById('search-administrasi')?.value || '').toLowerCase().trim();
            const majorFilter = (document.getElementById('administrasi-major-filter')?.value || '').toLowerCase().trim();

            administrasiFilteredRows = allRows.filter(row => {
                const text = row.innerText.toLowerCase();
                const matchSearch = !search || text.includes(search);
                const matchMajor = !majorFilter || text.includes(majorFilter);
                return matchSearch && matchMajor;
            });

            const total = administrasiFilteredRows.length;
            const pageSize = administrasiPageSize === -1 ? total : administrasiPageSize;
            const totalPages = Math.max(1, Math.ceil(total / (pageSize || 1)));

            if (administrasiCurrentPage > totalPages) administrasiCurrentPage = totalPages;

            allRows.forEach(r => r.style.display = 'none');

            const start = (administrasiCurrentPage - 1) * pageSize;
            const end = administrasiPageSize === -1 ? total : start + pageSize;

            administrasiFilteredRows.forEach((row, idx) => {
                if (idx >= start && idx < end) {
                    row.style.display = '';
                    const noCell = row.querySelector('.administrasi-row-no');
                    if (noCell) noCell.innerText = idx + 1;
                }
            });

            // Update info counter
            const info = document.getElementById('administrasi-table-info');
            if (info) {
                if (total === 0) {
                    info.innerText = 'Tidak ada data yang sesuai filter pencarian.';
                } else if (administrasiPageSize === -1) {
                    info.innerText = `Menampilkan semua (${total}) calon siswa terdaftar.`;
                } else {
                    info.innerText = `Menampilkan ${start + 1} s/d ${Math.min(end, total)} dari total ${total} calon siswa terdaftar.`;
                }
            }

            // Render pagination buttons
            renderPaginationButtons(totalPages);
        }

        function renderPaginationButtons(totalPages) {
            const container = document.getElementById('administrasi-table-pagination');
            if (!container) return;

            if (administrasiPageSize === -1 || totalPages <= 1) {
                container.innerHTML = '';
                return;
            }

            let html = `
                <button type="button" onclick="gotoAdminPage(${administrasiCurrentPage - 1})" 
                        class="px-2.5 py-1 rounded border text-xs font-bold ${administrasiCurrentPage === 1 ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 text-slate-700 hover:bg-slate-100'}" 
                        ${administrasiCurrentPage === 1 ? 'disabled' : ''}>Prev</button>
            `;

            for (let p = 1; p <= totalPages; p++) {
                if (totalPages > 7 && Math.abs(p - administrasiCurrentPage) > 2 && p !== 1 && p !== totalPages) {
                    if (p === 2 || p === totalPages - 1) html += `<span class="px-1 text-slate-400">...</span>`;
                    continue;
                }
                html += `
                    <button type="button" onclick="gotoAdminPage(${p})" 
                            class="px-3 py-1 rounded border text-xs font-bold ${p === administrasiCurrentPage ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs' : 'border-slate-300 text-slate-700 hover:bg-slate-100'}">${p}</button>
                `;
            }

            html += `
                <button type="button" onclick="gotoAdminPage(${administrasiCurrentPage + 1})" 
                        class="px-2.5 py-1 rounded border text-xs font-bold ${administrasiCurrentPage === totalPages ? 'border-slate-200 text-slate-300 cursor-not-allowed' : 'border-slate-300 text-slate-700 hover:bg-slate-100'}" 
                        ${administrasiCurrentPage === totalPages ? 'disabled' : ''}>Next</button>
            `;

            container.innerHTML = html;
        }

        function gotoAdminPage(p) {
            administrasiCurrentPage = p;
            initAdministrasiPagination();
        }

        // ================= COPY & CSV UTILITIES =================
        function copyTable(tableId) {
            const table = document.getElementById(tableId);
            if (!table) return;

            let text = '';
            const rows = table.querySelectorAll('tr');
            rows.forEach(r => {
                if (r.style.display !== 'none') {
                    const cells = r.querySelectorAll('th, td');
                    const rowText = Array.from(cells).map(c => c.innerText.replace(/\s+/g, ' ').trim()).join('\t');
                    text += rowText + '\n';
                }
            });

            navigator.clipboard.writeText(text).then(() => {
                alert('Tabel berhasil disalin ke clipboard!');
            }).catch(err => {
                console.error(err);
            });
        }

        function exportTableToCSV(tableId, filename) {
            const table = document.getElementById(tableId);
            if (!table) return;

            let csv = [];
            const rows = table.querySelectorAll('tr');
            rows.forEach(r => {
                if (r.style.display !== 'none') {
                    const cells = r.querySelectorAll('th, td');
                    const rowCsv = Array.from(cells).map(c => {
                        let val = c.innerText.replace(/"/g, '""').replace(/\s+/g, ' ').trim();
                        return `"${val}"`;
                    }).join(',');
                    csv.push(rowCsv);
                }
            });

            const csvString = csv.join('\n');
            const blob = new Blob([csvString], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.setAttribute('download', filename);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        document.addEventListener('DOMContentLoaded', () => {
            initAdministrasiPagination();
        });
    </script>

</body>
</html>
