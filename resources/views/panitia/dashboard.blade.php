<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Panitia | SPMB SMK Karya Nugraha Boyolali</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Chart.js Modern Charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
    </style>
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-800 flex h-screen overflow-hidden">

    <!-- ================= 1. SIDEBAR (PERSIS TAMPILAN GAMBAR USER) ================= -->
    <aside id="sidebar" class="w-64 bg-[#222d32] text-[#b8c7ce] flex flex-col flex-shrink-0 transition-all duration-300 z-30">
        
        <!-- SIDEBAR HEADER: LOGO KNBI & STATUS ONLINE -->
        <div class="h-20 bg-[#1a2226] flex items-center gap-3 px-4 border-b border-black/20">
            <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center overflow-hidden">
                @if(!empty($schoolLogo ?? $settings['school_logo']))
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
                <div class="text-sm font-black text-white tracking-wide">KNBI</div>
                <div class="text-xs text-emerald-400 flex items-center gap-1.5 font-semibold">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Online
                </div>
            </div>
        </div>

        <!-- SIDEBAR NAVIGATION MENU -->
        <nav class="flex-1 overflow-y-auto custom-scroll py-3 px-2 space-y-1.5 text-xs font-semibold">
            
            <!-- MENU 1: DASHBOARD -->
            <a href="{{ route('panitia.dashboard', ['view' => 'dashboard']) }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-lg transition {{ $currentView === 'dashboard' ? 'bg-[#00a65a] text-white shadow-md font-bold' : 'hover:bg-[#1e282c] hover:text-white' }}">
                <i class="fa-solid fa-gauge text-sm w-4 text-center"></i>
                <span class="flex-1">Dashboard</span>
            </a>

            <!-- MENU 2: SEKOLAH (DROPDOWN) -->
            <div class="space-y-1">
                <button type="button" onclick="toggleDropdown('menu-sekolah-panitia', 'arrow-sekolah-panitia')" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition {{ in_array($currentView, ['sekolah', 'jurusan']) ? 'text-white font-bold bg-[#1e282c]/60' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-desktop text-sm w-4 text-center"></i>
                        <span>Sekolah</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="arrow-sekolah-panitia"></i>
                </button>
                <div id="menu-sekolah-panitia" class="pl-8 pr-2 py-1 space-y-1 text-[11px] {{ in_array($currentView, ['sekolah', 'jurusan']) ? '' : 'hidden' }}">
                    <a href="{{ route('panitia.dashboard') }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Peta Sasaran SMP/MTs
                    </a>
                    <a href="{{ route('panitia.dashboard', ['view' => 'jurusan']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'jurusan' ? 'text-emerald-400 font-bold' : '' }}">
                        • Jurusan & Kuota
                    </a>
                </div>
            </div>

            <!-- MENU 3: DATA SPMB (DROPDOWN) -->
            <div class="space-y-1">
                <button type="button" onclick="toggleDropdown('menu-spmb-panitia', 'arrow-spmb-panitia')" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition {{ in_array($currentView, ['pendaftar', 'verifikasi', 'diterima']) ? 'text-white font-bold bg-[#1e282c]/60' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-address-card text-sm w-4 text-center"></i>
                        <span>Data SPMB</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="arrow-spmb-panitia"></i>
                </button>
                <div id="menu-spmb-panitia" class="pl-8 pr-2 py-1 space-y-1 text-[11px] {{ in_array($currentView, ['pendaftar', 'verifikasi', 'diterima']) ? '' : 'hidden' }}">
                    <a href="{{ route('panitia.dashboard', ['view' => 'pendaftar']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'pendaftar' ? 'text-emerald-400 font-bold' : '' }}">
                        • Seluruh Pendaftar
                    </a>
                    <a href="{{ route('panitia.dashboard', ['view' => 'verifikasi']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'verifikasi' ? 'text-emerald-400 font-bold' : '' }}">
                        • Verifikasi Berkas
                    </a>
                    <a href="{{ route('panitia.dashboard', ['view' => 'pendaftar', 'status' => 'accepted']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Siswa Diterima
                    </a>
                    <a href="{{ route('panitia.dashboard', ['view' => 'verifikasi', 'status' => 'pending']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Belum Ujian / Test
                    </a>
                    <a href="{{ route('panitia.spmb.exportExcel') }}" class="block py-1.5 px-2 rounded hover:text-emerald-400 hover:bg-[#1a2226] transition text-emerald-400/90 font-medium">
                        • Unduh / Export Excel
                    </a>
                    <a href="{{ route('panitia.spmb.printAll') }}" target="_blank" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition text-slate-300 font-medium">
                        • Cetak All Rekapitulasi
                    </a>
                </div>
            </div>

            <!-- MENU 4: ADMINISTRASI (DROPDOWN) -->
            <div class="space-y-1">
                <button type="button" onclick="toggleDropdown('menu-admin-panitia', 'arrow-admin-panitia')" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition {{ $currentView === 'administrasi' ? 'text-white font-bold bg-[#1e282c]/60' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-credit-card text-sm w-4 text-center"></i>
                        <span>Administrasi</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="arrow-admin-panitia"></i>
                </button>
                <div id="menu-admin-panitia" class="pl-8 pr-2 py-1 space-y-1 text-[11px] {{ $currentView === 'administrasi' ? '' : 'hidden' }}">
                    <a href="{{ route('panitia.dashboard', ['view' => 'administrasi']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'administrasi' ? 'text-emerald-400 font-bold' : '' }}">
                        • Pembayaran Pendaftaran
                    </a>
                </div>
            </div>

            <!-- MENU 5: PENGATURAN (DROPDOWN) -->
            <div class="space-y-1">
                <button type="button" onclick="toggleDropdown('menu-setting-panitia', 'arrow-setting-panitia')" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-sliders text-sm w-4 text-center"></i>
                        <span>Pengaturan</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="arrow-setting-panitia"></i>
                </button>
                <div id="menu-setting-panitia" class="pl-8 pr-2 py-1 space-y-1 text-[11px] hidden">
                    <a href="{{ route('panitia.dashboard', ['view' => 'dashboard', 'tab' => 'profile']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Profil Sekolah
                    </a>
                </div>
            </div>

        </nav>

        <!-- SIDEBAR FOOTER: USER & LOGOUT -->
        <div class="p-3 bg-[#1a2226] border-t border-black/20 flex items-center justify-between">
            <div class="flex items-center gap-2 overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 text-white flex items-center justify-center font-bold text-xs">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="truncate">
                    <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-slate-400 truncate">Panitia SPMB</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:text-white hover:bg-rose-600 transition" title="Logout">
                    <i class="fa-solid fa-power-off text-sm"></i>
                </button>
            </form>
        </div>

    </aside>

    <!-- ================= 2. MAIN CONTENT WRAPPER ================= -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
        
        <!-- TOPBAR TOGGLE & BREADCRUMB -->
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 sticky top-0 z-20 shadow-sm">
            <div class="flex items-center gap-3">
                <button type="button" onclick="toggleSidebar()" class="p-2 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-lg">
                    <i class="fa-solid fa-bars text-base"></i>
                </button>
                <a href="{{ route('home') }}" target="_blank" class="text-xs font-bold text-slate-500 hover:text-blue-600 flex items-center gap-1.5 transition">
                    <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Website Beranda
                </a>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-indigo-700 bg-indigo-50 px-3 py-1 rounded-full border border-indigo-200">
                    TP {{ $academicYear->name ?? '2027/2028' }}
                </span>
            </div>
        </header>

        <!-- CONTAINER KONTEN -->
        <main class="p-6 sm:p-8 space-y-6">

            <!-- NOTIFIKASI SUKSES -->
            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if(session('last_payment_id'))
                            <a href="{{ route('panitia.administrasi.payment.receipt', session('last_payment_id')) }}" target="_blank"
                               class="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition flex items-center gap-1 shadow-sm">
                                <i class="fa-solid fa-print"></i> Cetak Kwitansi Sekarang
                            </a>
                        @endif
                        <button type="button" onclick="this.closest('div.p-4').remove()" class="text-emerald-700 hover:text-emerald-900">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation text-rose-600 text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-700 hover:text-rose-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-sm">
                    <div class="font-bold">Terjadi Kesalahan:</div>
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- BANNER HEADER ATAS (PERSIS GAMBAR USER) -->
            <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 text-slate-800 text-xl font-black">
                        <span class="text-emerald-600"><i class="fa-solid fa-table-cells-large"></i></span>
                        <h1 class="capitalize">
                            @if($currentView === 'jurusan')
                                Data Jurusan & Kuota
                            @elseif($currentView === 'pendaftar')
                                Data Pendaftar Siswa Baru
                            @elseif($currentView === 'verifikasi')
                                Meja Verifikasi Berkas
                            @elseif($currentView === 'administrasi')
                                Administrasi Pembayaran
                            @else
                                Dashboard Panitia SPMB
                            @endif
                        </h1>
                    </div>
                    <p class="text-xs text-slate-500 mt-1">
                        Selamat Datang, <strong>{{ auth()->user()->name }}</strong> di APLIKASI SPMB ONLINE SMK KARYA NUGRAHA BOYOLALI.
                    </p>
                </div>

                <!-- BREADCRUMB -->
                <div class="text-xs font-bold text-slate-500 bg-slate-50 px-3.5 py-1.5 rounded-lg border border-slate-200 self-start sm:self-auto flex items-center gap-1.5">
                    <a href="{{ route('panitia.dashboard') }}" class="hover:text-blue-600"><i class="fa-solid fa-house"></i> Home</a>
                    <span>/</span>
                    <span class="text-emerald-700 capitalize">{{ $currentView }}</span>
                </div>
            </div>

            <!-- ================= 4 KARTU METRIK UTAMA (PERSIS GAMBAR USER) ================= -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                
                <!-- KARTU 1: SELURUH PENDAFTAR (HIJAU GRADIENT) -->
                <a href="{{ route('panitia.dashboard', ['view' => 'pendaftar']) }}" 
                   class="rounded-xl p-5 text-white shadow-lg relative overflow-hidden transition-all transform hover:-translate-y-1 block group"
                   style="background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%);">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-extrabold uppercase tracking-widest text-white/90">SELURUH PENDAFTAR</div>
                            <div class="text-4xl font-black mt-1">{{ number_format($totalRegistrations) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-2 border-t border-white/20 flex items-center justify-between text-xs font-bold text-white/95">
                        <span>Lihat Semua Pendaftar</span>
                        <i class="fa-solid fa-circle-chevron-right group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

                <!-- KARTU 2: SISWA DITERIMA (BIRU GRADIENT) -->
                <a href="{{ route('panitia.dashboard', ['view' => 'pendaftar', 'status' => 'accepted']) }}" 
                   class="rounded-xl p-5 text-white shadow-lg relative overflow-hidden transition-all transform hover:-translate-y-1 block group"
                   style="background: linear-gradient(135deg, #0073b7 0%, #00c0ef 100%);">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-extrabold uppercase tracking-widest text-white/90">SISWA DITERIMA</div>
                            <div class="text-4xl font-black mt-1">{{ number_format($acceptedCount) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-2 border-t border-white/20 flex items-center justify-between text-xs font-bold text-white/95">
                        <span>Lihat Siswa Lolos</span>
                        <i class="fa-solid fa-circle-chevron-right group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

                <!-- KARTU 3: BELUM UJIAN/TEST (PINK/ROSE GRADIENT) -->
                <a href="{{ route('panitia.dashboard', ['view' => 'verifikasi', 'status' => 'pending']) }}" 
                   class="rounded-xl p-5 text-white shadow-lg relative overflow-hidden transition-all transform hover:-translate-y-1 block group"
                   style="background: linear-gradient(135deg, #f5365c 0%, #f56036 100%);">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-file-pen"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-extrabold uppercase tracking-widest text-white/90">BELUM UJIAN/TEST</div>
                            <div class="text-4xl font-black mt-1">{{ number_format($pendingTestCount) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-2 border-t border-white/20 flex items-center justify-between text-xs font-bold text-white/95">
                        <span>Antrean Verifikasi</span>
                        <i class="fa-solid fa-circle-chevron-right group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

                <!-- KARTU 4: SUDAH MEMBAYAR (ORANGE/GOLD GRADIENT) -->
                <a href="{{ route('panitia.dashboard', ['view' => 'administrasi']) }}" 
                   class="rounded-xl p-5 text-white shadow-lg relative overflow-hidden transition-all transform hover:-translate-y-1 block group"
                   style="background: linear-gradient(135deg, #ff793f 0%, #ffb142 100%);">
                    <div class="flex items-center justify-between">
                        <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl">
                            <i class="fa-solid fa-credit-card"></i>
                        </div>
                        <div class="text-right">
                            <div class="text-[10px] font-extrabold uppercase tracking-widest text-white/90">SUDAH MEMBAYAR</div>
                            <div class="text-4xl font-black mt-1">{{ number_format($paidCount) }}</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-2 border-t border-white/20 flex items-center justify-between text-xs font-bold text-white/95">
                        <span>Jumlah Siswa Bayar</span>
                        <i class="fa-solid fa-circle-chevron-right group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

            </div>

            <!-- ================= GRAFIK STATISTIK SPMB (JURUSAN & ASAL SMP) ================= -->
            @if($currentView === 'dashboard')
                <div class="space-y-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-gradient-to-r from-slate-900 to-slate-800 p-5 rounded-2xl text-white shadow-md border border-slate-700">
                        <div class="flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#00a884] to-emerald-600 flex items-center justify-center text-xl text-white shadow-inner">
                                <i class="fa-solid fa-chart-pie"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-black tracking-tight flex items-center gap-2">
                                    Grafik & Analisis Pendaftar SPMB
                                </h2>
                                <p class="text-xs text-slate-300">
                                    Statistik real-time peminatan kompetensi keahlian dan sebaran asal sekolah pendaftar
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-xs">
                            <span class="px-3 py-1.5 rounded-lg bg-white/10 border border-white/15 text-slate-200 font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-users text-teal-400"></i> {{ $totalRegistrations }} Total Pendaftar
                            </span>
                            <span class="px-3 py-1.5 rounded-lg bg-white/10 border border-white/15 text-slate-200 font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-school text-amber-400"></i> {{ $totalUniqueSchools }} SMP/MTs Terdaftar
                            </span>
                            <span class="px-3 py-1.5 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-check-double text-emerald-400"></i> {{ $paidCount }} Siswa Sudah Bayar
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        <!-- GRAFIK 1: JUMLAH PENDAFTAR BERDASARKAN JURUSAN -->
                        <div class="lg:col-span-5 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-sm">
                                            <i class="fa-solid fa-shapes"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-extrabold text-slate-800">Jumlah Pendaftar per Jurusan</h3>
                                            <p class="text-[11px] text-slate-400 font-medium">Distribusi peminatan kompetensi keahlian</p>
                                        </div>
                                    </div>
                                    <span class="px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-extrabold border border-indigo-100">
                                        {{ count($chartMajorNames) }} Jurusan
                                    </span>
                                </div>
                                <div class="relative h-64 flex items-center justify-center">
                                    <canvas id="chartMajorPendaftarPanitia"></canvas>
                                </div>
                            </div>
                            
                            <!-- Breakdown list jurusan mini -->
                            <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-1 gap-2 max-h-48 overflow-y-auto custom-scroll pr-1">
                                @foreach($majors as $idx => $m)
                                    <div class="flex items-center justify-between text-xs p-1.5 rounded-lg hover:bg-slate-50 transition">
                                        <div class="flex items-center gap-2 truncate">
                                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ ['#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b'][$idx % 7] }};"></span>
                                            <span class="font-bold text-slate-700 truncate" title="{{ $m->name }}">{{ $m->name }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0 font-mono text-[11px]">
                                            <span class="font-black text-slate-900">{{ $m->registrations_count }} <span class="text-slate-400 font-normal">siswa</span></span>
                                            <span class="text-slate-400">/ kuota {{ $m->quota }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- GRAFIK 2: JUMLAH PENDAFTAR ASAL SMP/MTS -->
                        <div class="lg:col-span-7 bg-white rounded-2xl border border-slate-200 shadow-sm p-5 sm:p-6 flex flex-col justify-between">
                            <div>
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 mb-4 border-b border-slate-100">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center font-bold text-sm">
                                            <i class="fa-solid fa-school"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-sm font-extrabold text-slate-800">Jumlah Pendaftar Asal SMP / MTs</h3>
                                            <p class="text-[11px] text-slate-400 font-medium">Asal sekolah pendaftar yang terdaftar di sistem</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-2.5 py-1 rounded-full bg-teal-50 text-teal-800 text-[10px] font-extrabold border border-teal-200">
                                            {{ $totalUniqueSchools }} SMP/MTs Terdaftar
                                        </span>
                                    </div>
                                </div>
                                <div class="relative h-64">
                                    <canvas id="chartSchoolPendaftarPanitia"></canvas>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 bg-slate-50 p-2.5 rounded-xl border border-slate-200/60">
                                <span class="flex items-center gap-1.5 font-medium">
                                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                    Nama sekolah dengan variasi kosakata berbeda otomatis distandarisasi ke 1 format default resmi (SMPN / SMP N / SMP Negeri digabung).
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ================= VIEW 1: PEMETAAN SASARAN SMP/MTS KABUPATEN BOYOLALI REAL-TIME ================= -->
                @include('admin.partials.boyolali_school_map')
            @endif

            <!-- ================= VIEW 2: VERIFIKASI BERKAS PENDAFTAR ================= -->
            @if(in_array($currentView, ['verifikasi', 'pendaftar']))
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Meja Verifikasi Berkas & Data Calon Siswa Baru</h2>
                            <p class="text-xs text-slate-500">Periksa berkas, cetak bukti pendaftaran resmi, dan kelola data pendaftar</p>
                        </div>
                        <div class="flex items-center flex-wrap gap-2">
                            <!-- Tombol Hapus Massal (Muncul Saat Ada yang Dicentang) -->
                            <button type="button" id="btn-bulk-delete-student" onclick="submitBulkDeleteStudents()" 
                                    class="hidden px-3.5 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-2xs cursor-pointer">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                                <span>Hapus Terpilih (<span id="bulk-student-count">0</span>)</span>
                            </button>

                            <!-- Tombol Export Excel Calon Siswa -->
                            <a href="{{ route('panitia.spmb.exportExcel', request()->all()) }}" 
                               class="px-3.5 py-2 bg-white hover:bg-emerald-50 text-[#00a65a] border border-[#00a65a] rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-2xs"
                               title="Unduh Data Calon Siswa Lengkap dalam Format Excel (.xlsx)">
                                <i class="fa-regular fa-file-excel text-sm"></i>
                                <span>Export Excel</span>
                            </a>

                            <!-- Tombol Cetak All Rekapitulasi -->
                            <a href="{{ route('panitia.spmb.printAll', request()->all()) }}" target="_blank"
                               class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-2xs"
                               title="Cetak Laporan Rekapitulasi Calon Siswa (Print / PDF)">
                                <i class="fa-solid fa-print text-sm text-slate-600"></i>
                                <span>Cetak All</span>
                            </a>
                        </div>
                    </div>

                    <!-- SEARCH & FILTER -->
                    <form action="{{ route('panitia.dashboard') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <input type="hidden" name="view" value="{{ $currentView }}">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Nama / No. Reg / NISN..." 
                               class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium">
                        <select name="status" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium">
                            <option value="">Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                            <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Diterima</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Perlu Perbaikan</option>
                        </select>
                        <select name="major_id" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-medium">
                            <option value="">Semua Jurusan</option>
                            @foreach($majors as $m)
                                <option value="{{ $m->id }}" {{ request('major_id') == $m->id ? 'selected' : '' }}>{{ $m->code }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-lg text-xs font-bold transition flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-filter"></i> Saring
                        </button>
                    </form>

                    <!-- FORM BULK DELETE & TABEL ANTREAN -->
                    <form id="form-bulk-delete-student" action="{{ route('panitia.spmb.student.bulkDelete') }}" method="POST">
                        @csrf
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-y border-slate-200 font-extrabold uppercase text-[11px] text-slate-600">
                                        <th class="py-3 px-3 w-8 text-center">
                                            <input type="checkbox" id="check-all-students" onclick="toggleSelectAllStudents(this)" class="w-4 h-4 rounded text-teal-600">
                                        </th>
                                        <th class="py-3 px-3">Calon Siswa</th>
                                        <th class="py-3 px-3">Jurusan</th>
                                        <th class="py-3 px-3">Berkas Terunggah</th>
                                        <th class="py-3 px-3">Status</th>
                                        <th class="py-3 px-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @forelse($registrations as $reg)
                                        @php
                                            $u = $reg->user;
                                            $bio = $reg->biodata;
                                            $school = $reg->school;
                                            $parentModel = $reg->parent ?? $reg->parents;
                                            $phone = $u->phone ?? $bio->no_hp ?? '';
                                            $studentName = $bio->nama_lengkap ?? $u->name ?? '-';
                                            $studentPayload = [
                                                'id' => $reg->id,
                                                'reg_number' => $reg->registration_number,
                                                'name' => $studentName,
                                                'phone' => $phone,
                                                'username' => $u->username ?? $u->phone ?? '',
                                                'email' => $u->email ?? $bio->email ?? '',
                                                'nisn' => $u->nisn ?? '',
                                                'gender' => $bio->jenis_kelamin ?? 'L',
                                                'major_id' => $reg->major_id,
                                                'major_choice_2_id' => $reg->major_choice_2_id ?? '',
                                                'registration_type' => $reg->registration_type ?? 'reguler',
                                                'recommendation_by' => $reg->recommendation_by ?? '',
                                                'status' => $reg->status,

                                                'nik' => $bio->nik ?? '',
                                                'no_kk' => $bio->no_kk ?? '',
                                                'tempat_lahir' => $bio->tempat_lahir ?? '',
                                                'tanggal_lahir' => $bio?->tanggal_lahir ? (\Carbon\Carbon::parse($bio->tanggal_lahir)->format('Y-m-d')) : '',
                                                'agama' => $bio->agama ?? 'Islam',
                                                'anak_ke' => $bio->anak_ke ?? '',
                                                'jumlah_saudara' => $bio->jumlah_saudara ?? '',
                                                'tinggi_badan' => $bio->tinggi_badan ?? '',
                                                'berat_badan' => $bio->berat_badan ?? '',
                                                'riwayat_penyakit' => $bio->riwayat_penyakit ?? '',

                                                'alamat_jalan' => $bio->alamat_jalan ?? '',
                                                'desa' => $bio->desa ?? '',
                                                'kecamatan' => $bio->kecamatan ?? '',
                                                'kabupaten' => $bio->kabupaten ?? '',
                                                'provinsi' => $bio->provinsi ?? '',
                                                'kode_pos' => $bio->kode_pos ?? '',

                                                'asal_sekolah' => $school->asal_sekolah ?? '',
                                                'npsn_sekolah' => $school->npsn_sekolah ?? '',
                                                'jenis_sekolah' => $school->jenis_sekolah ?? 'SMP',
                                                'status_sekolah' => $school->status_sekolah ?? 'Negeri',
                                                'kabupaten_sekolah' => $school->kabupaten_sekolah ?? '',
                                                'tahun_lulus' => $school->tahun_lulus ?? date('Y'),
                                                'no_ijazah' => $school->no_ijazah ?? '',
                                                'nilai_rata_raport' => $school->nilai_rata_raport ?? '',

                                                'nama_ayah' => $parentModel->nama_ayah ?? '',
                                                'status_ayah' => $parentModel->status_ayah ?? 'Masih Hidup',
                                                'nik_ayah' => $parentModel->nik_ayah ?? '',
                                                'pekerjaan_ayah' => $parentModel->pekerjaan_ayah ?? '',
                                                'pendidikan_ayah' => $parentModel->pendidikan_ayah ?? '',
                                                'penghasilan_ayah' => $parentModel->penghasilan_ayah ?? '',
                                                'no_hp_ayah' => $parentModel->no_hp_ayah ?? '',

                                                'nama_ibu' => $parentModel->nama_ibu ?? '',
                                                'status_ibu' => $parentModel->status_ibu ?? 'Masih Hidup',
                                                'nik_ibu' => $parentModel->nik_ibu ?? '',
                                                'pekerjaan_ibu' => $parentModel->pekerjaan_ibu ?? '',
                                                'pendidikan_ibu' => $parentModel->pendidikan_ibu ?? '',
                                                'penghasilan_ibu' => $parentModel->penghasilan_ibu ?? '',
                                                'no_hp_ibu' => $parentModel->no_hp_ibu ?? '',

                                                'nama_wali' => $parentModel->nama_wali ?? '',
                                                'hubungan_wali' => $parentModel->hubungan_wali ?? '',
                                                'pekerjaan_wali' => $parentModel->pekerjaan_wali ?? '',
                                                'no_hp_wali' => $parentModel->no_hp_wali ?? '',
                                                'alamat_wali' => $parentModel->alamat_wali ?? '',
                                            ];
                                        @endphp
                                        <tr class="hover:bg-slate-50 transition">
                                            <td class="py-3 px-3 text-center">
                                                <input type="checkbox" name="ids[]" value="{{ $reg->id }}" onchange="updateBulkStudentCount()" class="student-item-cb w-4 h-4 rounded text-teal-600">
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="font-bold text-slate-900">{{ $studentName }}</div>
                                                <div class="text-[10px] text-slate-500 font-mono">
                                                    {{ $reg->registration_number }} &bull; NISN: {{ $u->nisn ?? '-' }}
                                                </div>
                                            </td>
                                            <td class="py-3 px-3 font-bold text-slate-700">
                                                {{ $reg->major->name ?? '-' }}
                                                @if(!empty($reg->major->industry_class))
                                                    <span class="text-slate-500 font-normal text-[11px]">({{ $reg->major->industry_class }})</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-3">
                                                <div class="flex flex-wrap gap-1">
                                                    @php
                                                        $docMap = ['kartu_keluarga' => 'KK', 'akta_kelahiran' => 'Akta', 'ijazah_skl' => 'Ijazah', 'pas_foto' => 'Foto'];
                                                        $upMap = $reg->documents->pluck('file_path', 'document_type')->toArray();
                                                    @endphp
                                                    @foreach($docMap as $typeKey => $typeLabel)
                                                        @if(isset($upMap[$typeKey]))
                                                            <a href="{{ asset('storage/' . $upMap[$typeKey]) }}" target="_blank" class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold hover:underline">
                                                                ✓ {{ $typeLabel }}
                                                            </a>
                                                        @else
                                                            <span class="px-1.5 py-0.5 rounded bg-slate-100 text-slate-400 text-[10px]">{{ $typeLabel }}</span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="py-3 px-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $reg->status === 'verified' ? 'bg-indigo-100 text-indigo-800' : ($reg->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : ($reg->status === 'rejected' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800')) }}">
                                                    {{ strtoupper($reg->status) }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-3 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    @php
                                                        $rawWa = preg_replace('/[^0-9]/', '', $phone);
                                                        if(str_starts_with($rawWa, '0')) {
                                                            $rawWa = '62' . substr($rawWa, 1);
                                                        }
                                                        $waText = \App\Models\SystemSetting::formatWaMessage($reg, $settings['wa_message_template'] ?? null);
                                                    @endphp
                                                    @if(!empty($rawWa))
                                                        <a href="https://wa.me/{{ $rawWa }}?text={{ urlencode($waText) }}" target="_blank" class="w-7 h-7 rounded bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 text-emerald-700 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs" title="Chat WhatsApp Calon Siswa">
                                                            <i class="fa-brands fa-whatsapp text-xs"></i>
                                                        </a>
                                                    @endif

                                                    <!-- Tombol Edit Calon Siswa (Full Edit Sama dengan Admin) -->
                                                    <button type="button" 
                                                            data-student='@json($studentPayload)'
                                                            onclick="openEditStudentModalFull(this)"
                                                            class="w-7 h-7 rounded bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs cursor-pointer"
                                                            title="Edit Data Lengkap Calon Siswa (Full Biodata)">
                                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                                    </button>

                                                    <!-- Tombol Cetak Bukti Pendaftaran Calon Siswa -->
                                                    <a href="{{ route('panitia.spmb.printCard', $reg->id) }}" target="_blank"
                                                       class="w-7 h-7 rounded bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs cursor-pointer"
                                                       title="Cetak Bukti Pendaftaran Calon Siswa (F4)">
                                                        <i class="fa-solid fa-print text-[11px]"></i>
                                                    </a>

                                                    <!-- Tombol Hapus Data Calon Siswa -->
                                                    <button type="button" 
                                                            onclick="confirmDeleteStudent('{{ $reg->id }}', '{{ addslashes($studentName) }}', '{{ $reg->registration_number }}')"
                                                            class="w-7 h-7 rounded bg-white hover:bg-rose-50 border border-rose-200 text-rose-600 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs cursor-pointer"
                                                            title="Hapus Data Calon Siswa">
                                                        <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                    </button>

                                                    <!-- Tombol Verifikasi Berkas -->
                                                    <button type="button" onclick="openPanitiaVerifyModal('{{ $reg->id }}', '{{ $reg->registration_number }}', '{{ addslashes($studentName) }}', '{{ $reg->status }}', '{{ addslashes($reg->verification_notes ?? '') }}')"
                                                            class="px-3 py-1.5 rounded bg-[#00a65a] hover:bg-[#008f4c] text-white text-[11px] font-bold cursor-pointer">
                                                        Verifikasi
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="py-6 text-center text-slate-400">Tidak ada data verifikasi.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <div>{{ $registrations->links() }}</div>
                </div>
            @endif

            <!-- ================= VIEW 3: JURUSAN & KUOTA ================= -->
            @if($currentView === 'jurusan')
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Program Keahlian & Keterisian Kuota</h2>
                            <p class="text-xs text-slate-500">SMK Karya Nugraha Boyolali</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($majors as $m)
                            <div class="p-4 rounded-xl border border-slate-200 bg-slate-50/50">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-800 font-bold text-xs">{{ $m->code }}</span>
                                    <span class="text-xs font-extrabold text-emerald-600">{{ $m->registrations_count }} / {{ $m->quota }} Siswa</span>
                                </div>
                                <h3 class="text-sm font-extrabold text-slate-900 mb-1">{{ $m->name }}</h3>
                                <div class="text-xs text-indigo-600 font-semibold">{{ $m->industry_class }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- ================= VIEW 4: ADMINISTRASI ================= -->
            @if($currentView === 'administrasi')
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Data Administrasi Pembayaran SPMB</h2>
                            <p class="text-xs text-slate-500">Pengecekan bukti, input pembayaran tunai, dan status pelunasan pendaftaran</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <a href="{{ route('panitia.administrasi.payment.exportExcel') }}" 
                               class="px-3.5 py-2 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl font-bold text-emerald-800 text-xs transition flex items-center gap-1.5 shadow-2xs cursor-pointer" 
                               title="Download Format Excel (.xlsx) Rapi & Resmi">
                                <i class="fa-solid fa-file-excel text-emerald-600 text-sm"></i> Export Excel (.xlsx)
                            </a>
                            <button type="button" onclick="openInputPaymentModal()"
                                    class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-1.5 cursor-pointer">
                                <i class="fa-solid fa-cash-register"></i> + Input Pembayaran Tunai
                            </button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-y border-slate-200 font-extrabold uppercase text-[11px] text-slate-600">
                                    <th class="py-3 px-3">Invoice</th>
                                    <th class="py-3 px-3">No. Pendaftar</th>
                                    <th class="py-3 px-3">Nama Calon Siswa</th>
                                    <th class="py-3 px-3">Pilihan Jurusan</th>
                                    <th class="py-3 px-3 text-right">Nominal</th>
                                    <th class="py-3 px-3">Metode</th>
                                    <th class="py-3 px-3 text-center">Status</th>
                                    <th class="py-3 px-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($payments as $pay)
                                    @php
                                        $pReg = $pay->registration;
                                        $pMajor = $pReg?->major;
                                        $pUser = $pReg?->user;
                                    @endphp
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-3 font-mono font-bold text-blue-600">{{ $pay->invoice_number }}</td>
                                        <td class="py-3 px-3 font-mono font-bold text-slate-800">{{ $pReg?->registration_number ?? '-' }}</td>
                                        <!-- Nama Siswa (Kontak dan Asal SMP tidak ditampilkan) -->
                                        <td class="py-3 px-3 font-bold text-slate-900 uppercase">{{ $pUser?->name ?? '-' }}</td>
                                        <td class="py-3 px-3">
                                            <div class="flex items-center justify-between gap-2">
                                                <div>
                                                    <span class="font-bold text-slate-800">{{ $pMajor?->name ?? '-' }}</span>
                                                    @if(!empty($pMajor?->industry_class))
                                                        <span class="block text-[10px] text-teal-700 font-bold">({{ $pMajor->industry_class }})</span>
                                                    @endif
                                                </div>
                                                @if($pReg)
                                                    <button type="button" 
                                                            onclick="openPanitiaChangeMajorModal('{{ $pReg->id }}', '{{ addslashes($pUser?->name ?? '') }}', '{{ $pReg->registration_number }}', '{{ $pReg->major_id }}', '{{ addslashes($pMajor?->name ?? '') }}')"
                                                            class="px-2 py-0.5 rounded bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-[10px] font-bold flex items-center gap-1 transition cursor-pointer">
                                                        <i class="fa-solid fa-pen-to-square"></i> Ubah
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-3 px-3 font-bold text-right text-emerald-700 font-mono">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-3 uppercase text-slate-600 font-semibold">{{ $pay->payment_method }}</td>
                                        <td class="py-3 px-3 text-center">
                                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px]">
                                                {{ strtoupper($pay->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-center">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <a href="{{ route('panitia.administrasi.payment.receipt', $pay->id) }}" target="_blank"
                                                   class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[10px] inline-flex items-center gap-1 transition shadow-xs"
                                                   title="Cetak Kwitansi Pembayaran">
                                                    <i class="fa-solid fa-print"></i> Kwitansi
                                                </a>
                                                @if($pReg)
                                                    <button type="button" 
                                                            onclick="openPanitiaChangeMajorModal('{{ $pReg->id }}', '{{ addslashes($pUser?->name ?? '') }}', '{{ $pReg->registration_number }}', '{{ $pReg->major_id }}', '{{ addslashes($pMajor?->name ?? '') }}')"
                                                            class="p-1.5 w-7 h-7 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg border border-blue-200 font-bold text-xs inline-flex items-center justify-center transition cursor-pointer"
                                                            title="Ubah Jurusan Calon Siswa Ini">
                                                        <i class="fa-solid fa-graduation-cap"></i>
                                                    </button>
                                                    <button type="button" 
                                                            onclick="confirmDeleteStudent('{{ $pReg->id }}', '{{ addslashes($pUser?->name ?? 'Calon Siswa') }}', '{{ $pReg->registration_number ?? '' }}')"
                                                            class="p-1.5 w-7 h-7 bg-white hover:bg-rose-50 text-rose-600 rounded-lg border border-rose-200 font-bold text-xs inline-flex items-center justify-center transition cursor-pointer"
                                                            title="Hapus Data Calon Siswa Ini">
                                                        <i class="fa-solid fa-trash-can"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="py-6 text-center text-slate-400">Belum ada transaksi pembayaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </main>

    </div>

    <!-- ================= MODAL INPUT PEMBAYARAN TUNAI (PANITIA) ================= -->
    <div id="modal-input-payment" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 {{ $errors->any() && old('payment_date') ? '' : 'hidden' }}">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh]">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2 text-slate-900 font-bold text-base">
                    <i class="fa-solid fa-cash-register text-[#00a65a]"></i>
                    <h3>Input Pembayaran Tunai Calon Siswa</h3>
                </div>
                <button type="button" onclick="closeInputPaymentModal()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('panitia.administrasi.payment.store') }}" method="POST" autocomplete="off" class="space-y-4 text-xs font-medium text-slate-700">
                @csrf

                <div class="space-y-1.5">
                    <label class="font-bold text-slate-800 flex items-center justify-between">
                        <span>Pilih Calon Siswa (Cari Nama / No. Pendaftar) *</span>
                        <span class="text-[11px] text-emerald-600 font-semibold" id="student-count-indicator">Menampilkan Semua Data ({{ count($candidatesForPayment ?? []) }} Calon Siswa)</span>
                    </label>

                    <div class="relative">
                        <input type="text" id="picker-search-input" name="student_search" value="{{ old('student_search') }}" onkeyup="filterCandidateDropdown()" onkeydown="if(event.key==='Enter'){event.preventDefault();}"
                               placeholder="Ketik nama siswa atau nomor pendaftar untuk mencari..."
                               class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:border-teal-500 mb-1.5">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                    </div>

                    <select name="registration_id" id="payment-student-select" required onchange="onPaymentStudentSelected(this)"
                            class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-teal-500">
                        <option value="" disabled {{ old('registration_id') ? '' : 'selected' }}>-- Pilih Calon Siswa Terdaftar (Semua Data) --</option>
                        @foreach($candidatesForPayment ?? [] as $c)
                            <option value="{{ $c->id }}"
                                    {{ (string) old('registration_id') === (string) $c->id ? 'selected' : '' }}
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

                <div class="space-y-1">
                    <label class="font-bold text-slate-800">Tanggal Pembayaran *</label>
                    <input type="date" name="payment_date" id="payment-input-date" required value="{{ old('payment_date', date('Y-m-d')) }}"
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-teal-500">
                </div>

                <div class="space-y-2">
                    <label class="font-bold text-slate-800 flex items-center justify-between">
                        <span>Besaran Nominal Pembayaran (Rp) *</span>
                        <span class="text-[11px] text-slate-500 font-mono" id="nominal-preview">Rp 0</span>
                    </label>
                    <input type="text" inputmode="numeric" name="amount" id="payment-input-amount" required value="{{ old('amount') }}" placeholder="Contoh: 150000 atau 150.000"
                           oninput="updateNominalPreview(this.value)"
                           class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-sm font-bold text-slate-900 focus:outline-none focus:border-teal-500 font-mono">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="text-[10px] text-slate-400 font-semibold mr-1">Nominal Cepat:</span>
                        <button type="button" onclick="setQuickAmount(50000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 50.000</button>
                        <button type="button" onclick="setQuickAmount(100000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 100.000</button>
                        <button type="button" onclick="setQuickAmount(150000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 150.000</button>
                        <button type="button" onclick="setQuickAmount(200000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 200.000</button>
                        <button type="button" onclick="setQuickAmount(500000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 500.000</button>
                        <button type="button" onclick="setQuickAmount(1000000)" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 border border-slate-200 text-[10px] font-bold text-slate-700">Rp 1.000.000</button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="font-bold text-slate-800">Jenis Pembayaran</label>
                        <input type="text" name="payment_type" id="payment-input-type" value="{{ old('payment_type', 'Pembayaran Administrasi SPMB') }}"
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-teal-500">
                    </div>
                    <div class="space-y-1">
                        <label class="font-bold text-slate-800">Catatan / Bukti Tunai</label>
                        <input type="text" name="notes" value="{{ old('notes') }}" placeholder="Contoh: Diterima tunai lunas oleh panitia"
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

    <!-- ================= MODAL VERIFIKASI PANITIA ================= -->
    <div id="modal-verify-p" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-extrabold text-slate-900">Verifikasi Berkas Calon Siswa</h3>
                <button type="button" onclick="document.getElementById('modal-verify-p').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-verify-panitia-action" action="" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Calon Siswa</label>
                    <div id="modal-student-info-p" class="p-2.5 bg-slate-50 rounded-lg text-xs font-bold text-slate-800"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Status Verifikasi *</label>
                    <select name="status" id="modal-status-select-p" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold">
                        <option value="verified">✅ Terverifikasi (Lengkap & Sah)</option>
                        <option value="accepted">🎓 Diterima Menjadi Siswa Baru</option>
                        <option value="rejected">⚠️ Perlu Perbaikan Berkas / Ditolak</option>
                        <option value="pending">⏳ Pending (Menunggu)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Catatan Panitia untuk Siswa</label>
                    <textarea name="verification_notes" id="modal-notes-p" rows="2" placeholder="Catatan keabsahan berkas..." class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs"></textarea>
                </div>
                <div class="flex items-center justify-between gap-2 pt-3 border-t border-slate-100">
                    <a id="modal-print-card-btn-p" href="#" target="_blank" class="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-300 text-xs font-bold rounded-lg transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fa-solid fa-print text-slate-600"></i> Cetak Bukti
                    </a>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="document.getElementById('modal-verify-p').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-200 transition">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-bold rounded-lg shadow transition">Simpan Verifikasi</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT LOGIC JAVASCRIPT -->
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-ml-64');
        }

        function toggleDropdown(menuId, arrowId) {
            const menu = document.getElementById(menuId);
            const arrow = document.getElementById(arrowId);
            menu.classList.toggle('hidden');
            if (arrow) {
                arrow.classList.toggle('rotate-180');
            }
        }

        function switchProfileTabPanitia(tab) {
            const tabs = ['profile', 'alamat', 'contact', 'kepsek'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-btn-${t}-p`);
                const content = document.getElementById(`tab-content-${t}-p`);
                if (btn && content) {
                    if (t === tab) {
                        btn.className = 'px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 border-[#00a884] text-[#00a884] bg-white font-extrabold';
                        content.classList.remove('hidden');
                    } else {
                        btn.className = 'px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 border-transparent text-slate-600 hover:text-slate-900';
                        content.classList.add('hidden');
                    }
                }
            });
        }

        function openPanitiaVerifyModal(id, regNum, name, status, notes) {
            const form = document.getElementById('form-verify-panitia-action');
            form.action = `/panitia/verify/${id}`;
            document.getElementById('modal-student-info-p').innerText = `${name} (${regNum})`;
            document.getElementById('modal-status-select-p').value = status;
            document.getElementById('modal-notes-p').value = notes || '';
            const printBtn = document.getElementById('modal-print-card-btn-p');
            if (printBtn) {
                printBtn.href = `/panitia/spmb/calon-siswa/${id}/cetak-kartu`;
            }
            document.getElementById('modal-verify-p').classList.remove('hidden');
        }

        function openPanitiaChangeMajorModal(regId, studentName, regNumber, currentMajorId, currentMajorName) {
            const modal = document.getElementById('modal-change-major-p');
            const form = document.getElementById('form-change-major-p');
            if (!modal || !form) return;

            form.action = `/panitia/administrasi/pembayaran/${regId}/change-major`;
            const regEl = document.getElementById('cmp-reg-number');
            if (regEl) regEl.innerText = regNumber;
            const nameEl = document.getElementById('cmp-student-name');
            if (nameEl) nameEl.innerText = studentName;
            const curEl = document.getElementById('cmp-current-major');
            if (curEl) curEl.innerText = currentMajorName || '-';

            const select = document.getElementById('cmp-select-major');
            if (select) {
                select.value = currentMajorId;
            }

            modal.classList.remove('hidden');
        }

        function closePanitiaChangeMajorModal() {
            const modal = document.getElementById('modal-change-major-p');
            if (modal) modal.classList.add('hidden');
        }

        function openInputPaymentModal() {
            const modal = document.getElementById('modal-input-payment');
            if (modal) modal.classList.remove('hidden');
        }

        function closeInputPaymentModal() {
            const modal = document.getElementById('modal-input-payment');
            if (modal) modal.classList.add('hidden');
        }

        function setQuickAmount(val) {
            const input = document.getElementById('payment-input-amount');
            if (input) {
                input.value = val;
                updateNominalPreview(val);
            }
        }

        function updateNominalPreview(val) {
            const cleaned = String(val ?? '').replace(/,\d{1,2}$/, '').replace(/[^0-9]/g, '');
            const num = parseInt(cleaned, 10) || 0;
            const preview = document.getElementById('nominal-preview');
            if (preview) {
                preview.innerText = 'Rp ' + num.toLocaleString('id-ID');
            }
        }

        function filterCandidateDropdown() {
            const input = document.getElementById('picker-search-input');
            const filter = (input?.value || '').toLowerCase().trim();
            const select = document.getElementById('payment-student-select');
            if (!select) return;
            const options = select.getElementsByClassName('candidate-option');
            let visibleCount = 0;
            let firstVisibleOpt = null;

            for (let i = 0; i < options.length; i++) {
                const opt = options[i];
                const text = (opt.dataset.number + ' ' + opt.dataset.name + ' ' + opt.dataset.major + ' ' + opt.dataset.school).toLowerCase();
                if (text.includes(filter)) {
                    opt.style.display = '';
                    opt.hidden = false;
                    visibleCount++;
                    if (!firstVisibleOpt) {
                        firstVisibleOpt = opt;
                    }
                } else {
                    opt.style.display = 'none';
                    opt.hidden = true;
                }
            }

            if (filter && firstVisibleOpt) {
                select.value = firstVisibleOpt.value;
                onPaymentStudentSelected(select);
            }

            const indicator = document.getElementById('student-count-indicator');
            if (indicator) {
                indicator.innerText = filter ? `Ditemukan ${visibleCount} Calon Siswa` : `Menampilkan Semua Data (${options.length} Calon Siswa)`;
            }
        }

        function onPaymentStudentSelected(sel) {
            const opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.dataset.number) return;

            document.getElementById('payment-student-preview')?.classList.remove('hidden');
            document.getElementById('prev-name').innerText = opt.dataset.name;
            document.getElementById('prev-number').innerText = opt.dataset.number;
            document.getElementById('prev-major').innerText = opt.dataset.major + (opt.dataset.industry ? ' (' + opt.dataset.industry + ')' : '');
            document.getElementById('prev-school').innerText = opt.dataset.school;
        }

        // ================= JAVASCRIPT FITUR EDIT & HAPUS DATA CALON SISWA PANITIA (FULL ACCESS SAMA DENGAN ADMIN) =================
        function switchEditTab(tabName) {
            document.querySelectorAll('.edit-tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.edit-tab-btn').forEach(btn => {
                btn.classList.remove('border-emerald-600', 'text-emerald-700');
                btn.classList.add('border-transparent', 'text-slate-500');
            });

            const content = document.getElementById(`tab-content-${tabName}`);
            const btn = document.getElementById(`tab-btn-${tabName}`);
            if (content) content.classList.remove('hidden');
            if (btn) {
                btn.classList.remove('border-transparent', 'text-slate-500');
                btn.classList.add('border-emerald-600', 'text-emerald-700');
            }
        }

        function openEditStudentModalFull(element) {
            let data = {};
            if (typeof element === 'string') {
                try { data = JSON.parse(element); } catch(e) {}
            } else if (element && element.getAttribute) {
                try { data = JSON.parse(element.getAttribute('data-student') || '{}'); } catch(e) {}
            } else if (typeof element === 'object') {
                data = element;
            }

            const form = document.getElementById('form-edit-student-action');
            if (form && data.id) {
                form.action = `/panitia/spmb/calon-siswa/${data.id}/update`;
            }

            const badgeReg = document.getElementById('edit-student-badge-reg');
            if (badgeReg) badgeReg.innerText = data.reg_number ? `No. Reg: ${data.reg_number}` : '';

            const setVal = (id, val) => {
                const el = document.getElementById(id);
                if (el) el.value = (val !== null && val !== undefined) ? val : '';
            };

            // Tab 1: Akun & Pendaftaran
            setVal('edit-student-name', data.name);
            setVal('edit-student-nisn', data.nisn);
            setVal('edit-student-phone', data.phone);
            setVal('edit-student-email', data.email);
            setVal('edit-student-username', data.username);
            setVal('edit-student-password', '');
            setVal('edit-student-major', data.major_id);
            setVal('edit-student-major-2', data.major_choice_2_id || '');
            setVal('edit-student-reg-type', data.registration_type || 'reguler');
            setVal('edit-student-recommendation', data.recommendation_by);
            setVal('edit-student-status', data.status || 'pending');

            // Tab 2: Biodata Diri & Alamat
            setVal('edit-student-nik', data.nik);
            setVal('edit-student-no-kk', data.no_kk);
            setVal('edit-student-gender', data.gender || 'L');
            setVal('edit-student-pob', data.tempat_lahir);
            setVal('edit-student-dob', data.tanggal_lahir);
            setVal('edit-student-religion', data.agama || 'Islam');
            setVal('edit-student-child-no', data.anak_ke);
            setVal('edit-student-siblings-count', data.jumlah_saudara);
            setVal('edit-student-height', data.tinggi_badan);
            setVal('edit-student-weight', data.berat_badan);
            setVal('edit-student-disease', data.riwayat_penyakit);
            setVal('edit-student-address-street', data.alamat_jalan);
            setVal('edit-student-village', data.desa);
            setVal('edit-student-district', data.kecamatan);
            setVal('edit-student-city', data.kabupaten);
            setVal('edit-student-province', data.provinsi);
            setVal('edit-student-postal-code', data.kode_pos);

            // Tab 3: Asal Sekolah
            setVal('edit-student-school', data.asal_sekolah);
            setVal('edit-student-school-npsn', data.npsn_sekolah);
            setVal('edit-student-school-type', data.jenis_sekolah || 'SMP');
            setVal('edit-student-school-status', data.status_sekolah || 'Negeri');
            setVal('edit-student-school-city', data.kabupaten_sekolah);
            setVal('edit-student-graduation-year', data.tahun_lulus);
            setVal('edit-student-ijazah-no', data.no_ijazah);
            setVal('edit-student-report-score', data.nilai_rata_raport);

            // Tab 4: Orang Tua & Wali
            setVal('edit-student-father-name', data.nama_ayah);
            setVal('edit-student-father-status', data.status_ayah || 'Masih Hidup');
            setVal('edit-student-father-nik', data.nik_ayah);
            setVal('edit-student-father-job', data.pekerjaan_ayah);
            setVal('edit-student-father-edu', data.pendidikan_ayah);
            setVal('edit-student-father-income', data.penghasilan_ayah);
            setVal('edit-student-father-phone', data.no_hp_ayah);

            setVal('edit-student-mother-name', data.nama_ibu);
            setVal('edit-student-mother-status', data.status_ibu || 'Masih Hidup');
            setVal('edit-student-mother-nik', data.nik_ibu);
            setVal('edit-student-mother-job', data.pekerjaan_ibu);
            setVal('edit-student-mother-edu', data.pendidikan_ibu);
            setVal('edit-student-mother-income', data.penghasilan_ibu);
            setVal('edit-student-mother-phone', data.no_hp_ibu);

            setVal('edit-student-guardian-name', data.nama_wali);
            setVal('edit-student-guardian-relation', data.hubungan_wali);
            setVal('edit-student-guardian-job', data.pekerjaan_wali);
            setVal('edit-student-guardian-phone', data.no_hp_wali);
            setVal('edit-student-guardian-address', data.alamat_wali);

            switchEditTab('akun');
            document.getElementById('modal-edit-student').classList.remove('hidden');
        }

        function openEditStudentModal(id, name, username, phone, gender, school, majorId, status) {
            openEditStudentModalFull({
                id: id,
                name: name,
                username: username,
                phone: phone,
                gender: gender,
                asal_sekolah: school,
                major_id: majorId,
                status: status || 'pending',
            });
        }

        function closeEditStudentModal() {
            document.getElementById('modal-edit-student').classList.add('hidden');
        }

        function confirmDeleteStudent(id, name, regNum) {
            if (confirm(`Apakah Anda yakin ingin menghapus data calon siswa '${name}' (${regNum})? Seluruh data pendaftaran dan biodata akan dihapus.`)) {
                const form = document.getElementById('form-delete-single-student');
                form.action = `/panitia/spmb/calon-siswa/${id}/delete`;
                form.submit();
            }
        }

        function toggleSelectAllStudents(source) {
            const cbs = document.querySelectorAll('.student-item-cb');
            cbs.forEach(cb => cb.checked = source.checked);
            updateBulkStudentCount();
        }

        function updateBulkStudentCount() {
            const count = document.querySelectorAll('.student-item-cb:checked').length;
            const btn = document.getElementById('btn-bulk-delete-student');
            const countLabel = document.getElementById('bulk-student-count');
            if (btn && countLabel) {
                countLabel.innerText = count;
                if (count > 0) {
                    btn.classList.remove('hidden');
                } else {
                    btn.classList.add('hidden');
                }
            }
        }

        function submitBulkDeleteStudents() {
            const checked = document.querySelectorAll('.student-item-cb:checked');
            if (checked.length === 0) {
                alert('Silakan pilih setidaknya satu data siswa untuk dihapus.');
                return;
            }
            if (confirm(`Apakah Anda yakin ingin menghapus ${checked.length} data calon siswa terpilih secara massal?`)) {
                document.getElementById('form-bulk-delete-student').submit();
            }
        }
    </script>

    <!-- ================= MODAL UBAH JURUSAN OLEH PANITIA ================= -->
    <div id="modal-change-major-p" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full border border-slate-200 overflow-hidden transform transition-all">
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 px-6 py-4 text-white flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black">Ubah Pilihan Jurusan Siswa</h3>
                        <p class="text-[11px] text-blue-100">Panitia SPMB</p>
                    </div>
                </div>
                <button type="button" onclick="closePanitiaChangeMajorModal()" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition cursor-pointer">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>

            <form id="form-change-major-p" action="" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="p-3.5 bg-slate-50 border border-slate-200 rounded-xl space-y-1.5 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-semibold">No. Pendaftar:</span>
                        <span id="cmp-reg-number" class="font-mono font-bold text-slate-800 bg-white px-2 py-0.5 rounded border border-slate-200"></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 font-semibold">Nama Siswa:</span>
                        <span id="cmp-student-name" class="font-bold text-slate-900 uppercase"></span>
                    </div>
                    <div class="flex items-center justify-between pt-1.5 border-t border-slate-200">
                        <span class="text-slate-500 font-semibold">Jurusan Saat Ini:</span>
                        <span id="cmp-current-major" class="font-bold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200"></span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-briefcase text-blue-600"></i> Pilih Jurusan Baru *
                    </label>
                    <select name="major_id" id="cmp-select-major" required 
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-bold text-slate-800 focus:bg-white focus:outline-none focus:border-blue-500 transition">
                        <option value="" disabled>-- Pilih Salah Satu Jurusan --</option>
                        @foreach($majors as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->name }} {{ !empty($m->industry_class) ? ' (' . $m->industry_class . ')' : '' }} (Sisa Kuota: {{ $m->remaining_quota }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closePanitiaChangeMajorModal()" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-100 transition cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-black shadow-md flex items-center gap-2 transition cursor-pointer">
                        <i class="fa-solid fa-check"></i> Simpan Jurusan Baru
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FORM TERSEMBUNYI UNTUK HAPUS SISWA SATUAN -->
    <form id="form-delete-single-student" action="" method="POST" class="hidden">
        @csrf
    </form>

    <!-- ================= MODAL EDIT CALON SISWA OLEH PANITIA (FULL ACCESS SAMA DENGAN ADMIN) ================= -->
    <div id="modal-edit-student" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-3 sm:p-4 hidden">
        <div class="bg-white rounded-2xl max-w-4xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 overflow-y-auto max-h-[92vh]">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center text-base">
                        <i class="fa-solid fa-user-pen"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-slate-900">Edit Data Lengkap Calon Siswa</h3>
                            <span id="edit-student-badge-reg" class="font-mono bg-emerald-50 text-emerald-700 px-2 py-0.5 rounded text-[11px] font-bold border border-emerald-200"></span>
                        </div>
                        <p class="text-[11px] text-slate-500">Akses Penuh Panitia SPMB: Ubah data akun, biodata, asal sekolah, dan orang tua/wali siswa.</p>
                    </div>
                </div>
                <button type="button" onclick="closeEditStudentModal()" class="text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Tab Navigasi Kategori Edit -->
            <div class="flex border-b border-slate-200 gap-1 overflow-x-auto text-xs font-bold select-none">
                <button type="button" id="tab-btn-akun" onclick="switchEditTab('akun')" 
                        class="edit-tab-btn px-4 py-2.5 border-b-2 border-emerald-600 text-emerald-700 flex items-center gap-2 transition whitespace-nowrap">
                    <i class="fa-solid fa-user-gear"></i> 1. Akun & Pilihan
                </button>
                <button type="button" id="tab-btn-biodata" onclick="switchEditTab('biodata')" 
                        class="edit-tab-btn px-4 py-2.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 transition whitespace-nowrap">
                    <i class="fa-solid fa-id-card"></i> 2. Biodata & Alamat
                </button>
                <button type="button" id="tab-btn-sekolah" onclick="switchEditTab('sekolah')" 
                        class="edit-tab-btn px-4 py-2.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 transition whitespace-nowrap">
                    <i class="fa-solid fa-school"></i> 3. Asal Sekolah
                </button>
                <button type="button" id="tab-btn-ortu" onclick="switchEditTab('ortu')" 
                        class="edit-tab-btn px-4 py-2.5 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2 transition whitespace-nowrap">
                    <i class="fa-solid fa-people-roof"></i> 4. Orang Tua & Wali
                </button>
            </div>

            <form id="form-edit-student-action" action="" method="POST" class="space-y-4 text-xs font-medium text-slate-700">
                @csrf

                <!-- ================= TAB 1: AKUN & PILIHAN PENDAFTARAN ================= -->
                <div id="tab-content-akun" class="edit-tab-content space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        <div class="space-y-1 sm:col-span-2">
                            <label class="font-bold text-slate-800">Nama Lengkap Siswa *</label>
                            <input type="text" id="edit-student-name" name="nama_siswa" required 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-semibold">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">NISN Siswa</label>
                            <input type="text" id="edit-student-nisn" name="nisn" maxlength="20" placeholder="10 Digit NISN"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">No. HP / WhatsApp Siswa *</label>
                            <input type="text" id="edit-student-phone" name="phone" required 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Email Siswa</label>
                            <input type="email" id="edit-student-email" name="email" placeholder="siswa@gmail.com"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Username Akun</label>
                            <input type="text" id="edit-student-username" name="username" 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Password Baru (Opsional)</label>
                            <input type="text" id="edit-student-password" name="password" placeholder="Kosongkan jika tak diubah" 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Status Pendaftaran *</label>
                            <select id="edit-student-status" name="status" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-bold">
                                <option value="pending">PENDING (Menunggu Verifikasi)</option>
                                <option value="verified">TERVERIFIKASI (Berkas Sah)</option>
                                <option value="accepted">DITERIMA (Lolos Seleksi SPMB)</option>
                                <option value="rejected">DITOLAK / PERLU PERBAIKAN</option>
                            </select>
                        </div>
                        <div class="space-y-1 sm:col-span-2 lg:col-span-3 border-t border-slate-100 pt-3">
                            <label class="font-bold text-slate-800 text-[11px] uppercase tracking-wider text-emerald-800">Pilihan Jurusan & Jalur</label>
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="font-bold text-slate-800">Kompetensi Keahlian (Pilihan Utama) *</label>
                            <select id="edit-student-major" name="major_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-semibold">
                                @foreach($majors as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} {{ !empty($m->industry_class) ? '('.$m->industry_class.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Pilihan Jurusan Ke-2</label>
                            <select id="edit-student-major-2" name="major_choice_2_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                                <option value="">-- Tidak Memilih Jurusan Ke-2 --</option>
                                @foreach($majors as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} {{ !empty($m->industry_class) ? '('.$m->industry_class.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Jalur Pendaftaran</label>
                            <select id="edit-student-reg-type" name="registration_type" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                                <option value="reguler">Reguler</option>
                                <option value="prestasi">Prestasi</option>
                                <option value="afirmasi">Afirmasi (KIP / Kurang Mampu)</option>
                                <option value="zonasi">Domisili / Zonasi</option>
                            </select>
                        </div>
                        <div class="space-y-1 sm:col-span-2">
                            <label class="font-bold text-slate-800">Rekomendasi Oleh (Guru / Alumni / Relasi)</label>
                            <input type="text" id="edit-student-recommendation" name="recommendation_by" placeholder="Contoh: Bpk. Joko (Guru SMPN 1) / Mandiri"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 2: BIODATA PRIBADI & ALAMAT ================= -->
                <div id="tab-content-biodata" class="edit-tab-content space-y-4 hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">NIK Siswa (KTP / KK)</label>
                            <input type="text" id="edit-student-nik" name="nik" maxlength="16" placeholder="16 Digit NIK"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Nomor Kartu Keluarga (KK)</label>
                            <input type="text" id="edit-student-no-kk" name="no_kk" maxlength="16" placeholder="16 Digit No. KK"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Jenis Kelamin *</label>
                            <select id="edit-student-gender" name="gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-bold">
                                <option value="L">Laki-laki (L)</option>
                                <option value="P">Perempuan (P)</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Tempat Lahir</label>
                            <input type="text" id="edit-student-pob" name="tempat_lahir" placeholder="Kota / Kabupaten Kelahiran"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 uppercase">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Tanggal Lahir</label>
                            <input type="date" id="edit-student-dob" name="tanggal_lahir" 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Agama</label>
                            <select id="edit-student-religion" name="agama" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                                <option value="Islam">Islam</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Buddha">Buddha</option>
                                <option value="Konghucu">Konghucu</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Anak Ke-</label>
                            <input type="number" id="edit-student-child-no" name="anak_ke" min="1" max="25" placeholder="1"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Jumlah Saudara</label>
                            <input type="number" id="edit-student-siblings-count" name="jumlah_saudara" min="0" max="25" placeholder="2"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Tinggi & Berat Badan</label>
                            <div class="grid grid-cols-2 gap-2">
                                <input type="number" id="edit-student-height" name="tinggi_badan" placeholder="TB (cm)" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs">
                                <input type="number" id="edit-student-weight" name="berat_badan" placeholder="BB (kg)" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs">
                            </div>
                        </div>
                        <div class="space-y-1 sm:col-span-2 lg:col-span-3">
                            <label class="font-bold text-slate-800">Riwayat Penyakit (Bila Ada)</label>
                            <input type="text" id="edit-student-disease" name="riwayat_penyakit" placeholder="Asma, Alergi, dsb / Kosongkan bila sehat"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <!-- Bagian Alamat -->
                        <div class="space-y-1 sm:col-span-2 lg:col-span-3 border-t border-slate-100 pt-3">
                            <label class="font-bold text-slate-800 text-[11px] uppercase tracking-wider text-emerald-800">Alamat Tempat Tinggal</label>
                        </div>
                        <div class="space-y-1 sm:col-span-2 lg:col-span-3">
                            <label class="font-bold text-slate-800">Alamat Jalan / Dukuh / RT / RW</label>
                            <input type="text" id="edit-student-address-street" name="alamat_jalan" placeholder="Contoh: Jl. Merbabu No. 12 RT 02 / RW 04"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Desa / Kelurahan</label>
                            <input type="text" id="edit-student-village" name="desa" placeholder="Desa / Kelurahan"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Kecamatan</label>
                            <input type="text" id="edit-student-district" name="kecamatan" placeholder="Kecamatan"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Kabupaten / Kota</label>
                            <input type="text" id="edit-student-city" name="kabupaten" placeholder="Kabupaten Boyolali"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Provinsi</label>
                            <input type="text" id="edit-student-province" name="provinsi" placeholder="Jawa Tengah"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Kode Pos</label>
                            <input type="text" id="edit-student-postal-code" name="kode_pos" placeholder="57311"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 3: ASAL SEKOLAH ================= -->
                <div id="tab-content-sekolah" class="edit-tab-content space-y-4 hidden">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                        <div class="space-y-1 sm:col-span-2">
                            <label class="font-bold text-slate-800">Nama Asal Sekolah (SMP / MTs) *</label>
                            <input type="text" id="edit-student-school" name="asal_sekolah" required 
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg uppercase focus:bg-white focus:outline-none focus:border-teal-500 font-semibold">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">NPSN Sekolah Asal</label>
                            <input type="text" id="edit-student-school-npsn" name="npsn_sekolah" maxlength="20" placeholder="8 Digit NPSN"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Jenis Sekolah</label>
                            <select id="edit-student-school-type" name="jenis_sekolah" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                                <option value="SMP">SMP</option>
                                <option value="MTs">MTs</option>
                                <option value="Paket B">Paket B / Setara</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Status Sekolah</label>
                            <select id="edit-student-school-status" name="status_sekolah" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-medium">
                                <option value="Negeri">Negeri</option>
                                <option value="Swasta">Swasta</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Kabupaten Sekolah Asal</label>
                            <input type="text" id="edit-student-school-city" name="kabupaten_sekolah" placeholder="Kabupaten Asal SMP"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Tahun Lulus</label>
                            <input type="text" id="edit-student-graduation-year" name="tahun_lulus" placeholder="{{ date('Y') }}"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Nomor Ijazah / SKL</label>
                            <input type="text" id="edit-student-ijazah-no" name="no_ijazah" placeholder="DN-03/..."
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Nilai Rata-rata Raport</label>
                            <input type="number" step="0.01" min="0" max="100" id="edit-student-report-score" name="nilai_rata_raport" placeholder="85.50"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>
                    </div>
                </div>

                <!-- ================= TAB 4: DATA ORANG TUA & WALI ================= -->
                <div id="tab-content-ortu" class="edit-tab-content space-y-4 hidden">
                    
                    <!-- Bagian Ayah -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <div class="flex items-center gap-2 text-slate-900 font-extrabold text-xs border-b border-slate-200 pb-2">
                            <i class="fa-solid fa-person text-blue-600"></i>
                            <span>Data Ayah Kandung</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="space-y-1 sm:col-span-2">
                                <label class="font-bold text-slate-800">Nama Ayah</label>
                                <input type="text" id="edit-student-father-name" name="nama_ayah" placeholder="Nama Lengkap Ayah"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Status Ayah</label>
                                <select id="edit-student-father-status" name="status_ayah" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500 font-medium">
                                    <option value="Masih Hidup">Masih Hidup</option>
                                    <option value="Meninggal">Meninggal</option>
                                    <option value="Cerai">Cerai</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">NIK Ayah</label>
                                <input type="text" id="edit-student-father-nik" name="nik_ayah" maxlength="16" placeholder="NIK Ayah"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg font-mono focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Pekerjaan Ayah</label>
                                <input type="text" id="edit-student-father-job" name="pekerjaan_ayah" placeholder="Petani / Wiraswasta / PNS"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Pendidikan Ayah</label>
                                <input type="text" id="edit-student-father-edu" name="pendidikan_ayah" placeholder="SMA / S1 / SMP"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Penghasilan Ayah</label>
                                <input type="text" id="edit-student-father-income" name="penghasilan_ayah" placeholder="Rp 2.000.000 - Rp 5.000.000"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">No. HP / WA Ayah</label>
                                <input type="text" id="edit-student-father-phone" name="no_hp_ayah" placeholder="08xxxxxxxxxx"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg font-mono focus:outline-none focus:border-teal-500">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Ibu -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <div class="flex items-center gap-2 text-slate-900 font-extrabold text-xs border-b border-slate-200 pb-2">
                            <i class="fa-solid fa-person-dress text-pink-600"></i>
                            <span>Data Ibu Kandung</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="space-y-1 sm:col-span-2">
                                <label class="font-bold text-slate-800">Nama Ibu</label>
                                <input type="text" id="edit-student-mother-name" name="nama_ibu" placeholder="Nama Lengkap Ibu"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Status Ibu</label>
                                <select id="edit-student-mother-status" name="status_ibu" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500 font-medium">
                                    <option value="Masih Hidup">Masih Hidup</option>
                                    <option value="Meninggal">Meninggal</option>
                                    <option value="Cerai">Cerai</option>
                                </select>
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">NIK Ibu</label>
                                <input type="text" id="edit-student-mother-nik" name="nik_ibu" maxlength="16" placeholder="NIK Ibu"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg font-mono focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Pekerjaan Ibu</label>
                                <input type="text" id="edit-student-mother-job" name="pekerjaan_ibu" placeholder="Ibu Rumah Tangga / Pedagang"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Pendidikan Ibu</label>
                                <input type="text" id="edit-student-mother-edu" name="pendidikan_ibu" placeholder="SMA / S1 / SMP"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Penghasilan Ibu</label>
                                <input type="text" id="edit-student-mother-income" name="penghasilan_ibu" placeholder="Tidak Berpenghasilan / Rp 1.000.000"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">No. HP / WA Ibu</label>
                                <input type="text" id="edit-student-mother-phone" name="no_hp_ibu" placeholder="08xxxxxxxxxx"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg font-mono focus:outline-none focus:border-teal-500">
                            </div>
                        </div>
                    </div>

                    <!-- Bagian Wali -->
                    <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <div class="flex items-center gap-2 text-slate-900 font-extrabold text-xs border-b border-slate-200 pb-2">
                            <i class="fa-solid fa-user-shield text-amber-600"></i>
                            <span>Data Wali Siswa (Opsional / Bila Tinggal Bersama Wali)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <div class="space-y-1 sm:col-span-2">
                                <label class="font-bold text-slate-800">Nama Wali</label>
                                <input type="text" id="edit-student-guardian-name" name="nama_wali" placeholder="Nama Lengkap Wali"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Hubungan Wali</label>
                                <input type="text" id="edit-student-guardian-relation" name="hubungan_wali" placeholder="Paman / Kakek / Saudara"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Pekerjaan Wali</label>
                                <input type="text" id="edit-student-guardian-job" name="pekerjaan_wali" placeholder="Pekerjaan Wali"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">No. HP Wali</label>
                                <input type="text" id="edit-student-guardian-phone" name="no_hp_wali" placeholder="08xxxxxxxxxx"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg font-mono focus:outline-none focus:border-teal-500">
                            </div>
                            <div class="space-y-1 sm:col-span-3">
                                <label class="font-bold text-slate-800">Alamat Wali</label>
                                <input type="text" id="edit-student-guardian-address" name="alamat_wali" placeholder="Alamat tinggal wali"
                                       class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Footer Tombol Aksi Modal -->
                <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                    <span class="text-[11px] text-slate-400 font-normal">
                        <i class="fa-solid fa-circle-info text-emerald-600"></i> Pastikan seluruh data dicek sebelum disimpan.
                    </span>
                    <div class="flex items-center gap-2">
                        <button type="button" onclick="closeEditStudentModal()" 
                                class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 font-bold text-slate-600 transition cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" 
                                class="px-5 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-lg font-bold transition flex items-center gap-1.5 shadow cursor-pointer">
                            <i class="fa-solid fa-save"></i> Simpan Perubahan Lengkap
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- INISIALISASI GRAFIK CHART.JS (JURUSAN & ASAL SMP DENGAN NORMALISASI KOSAKATA) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Grafik Pendaftar per Jurusan
            const ctxMajor = document.getElementById('chartMajorPendaftarPanitia');
            if (ctxMajor) {
                const majorLabels = @json($chartMajorNames ?? []);
                const majorCounts = @json($chartMajorCounts ?? []);
                const colors = [
                    '#3b82f6', '#10b981', '#f59e0b', '#8b5cf6', '#ec4899', '#06b6d4', '#64748b'
                ];

                new Chart(ctxMajor, {
                    type: 'doughnut',
                    data: {
                        labels: majorLabels,
                        datasets: [{
                            data: majorCounts,
                            backgroundColor: colors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const value = context.raw || 0;
                                        const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return ` ${context.label}: ${value} Calon Siswa (${pct}%)`;
                                    }
                                }
                            }
                        },
                        cutout: '62%'
                    }
                });
            }

            // 2. Grafik Pendaftar per Asal SMP/MTs (Normalisasi Kosakata)
            const ctxSchool = document.getElementById('chartSchoolPendaftarPanitia');
            if (ctxSchool) {
                const schoolLabels = @json($chartSchoolNames ?? []);
                const schoolCounts = @json($chartSchoolCounts ?? []);

                new Chart(ctxSchool, {
                    type: 'bar',
                    data: {
                        labels: schoolLabels,
                        datasets: [{
                            label: 'Jumlah Calon Siswa',
                            data: schoolCounts,
                            backgroundColor: 'rgba(0, 168, 132, 0.85)',
                            hoverBackgroundColor: 'rgba(0, 143, 112, 1)',
                            borderRadius: 6,
                            borderSkipped: false,
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0,
                                    font: {
                                        size: 10,
                                        family: 'Plus Jakarta Sans'
                                    }
                                },
                                grid: {
                                    color: '#f1f5f9'
                                }
                            },
                            y: {
                                ticks: {
                                    font: {
                                        size: 10,
                                        family: 'Plus Jakarta Sans',
                                        weight: '600'
                                    },
                                    color: '#1e293b'
                                },
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return ` Pendaftar: ${context.raw} calon siswa`;
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

</body>
</html>
