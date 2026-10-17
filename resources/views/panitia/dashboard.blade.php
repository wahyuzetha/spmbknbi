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
                    <a href="{{ route('panitia.dashboard', ['view' => 'dashboard', 'tab' => 'profile']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Profil Sekolah
                    </a>
                    <a href="{{ route('panitia.dashboard', ['view' => 'jurusan']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'jurusan' ? 'text-emerald-400 font-bold' : '' }}">
                        • Jurusan & Kuota
                    </a>
                    <a href="{{ route('panitia.dashboard', ['view' => 'dashboard', 'tab' => 'alamat']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Alamat Sekolah
                    </a>
                    <a href="{{ route('panitia.dashboard', ['view' => 'dashboard', 'tab' => 'contact']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Kontak & Medsos
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
                    <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-900">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
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
                        <span>Data Pembayaran</span>
                        <i class="fa-solid fa-circle-chevron-right group-hover:translate-x-1 transition-transform"></i>
                    </div>
                </a>

            </div>

            <!-- ================= VIEW 1: DASHBOARD UTAMA (TABS PROFILE SEKOLAH) ================= -->
            @if($currentView === 'dashboard')
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <!-- TAB NAVIGATION HEADER (PERSIS GAMBAR USER) -->
                    <div class="flex items-center gap-1 border-b border-slate-200 px-4 pt-3 bg-slate-50/50 overflow-x-auto text-xs font-bold">
                        <button type="button" onclick="switchProfileTabPanitia('profile')" id="tab-btn-profile-p" 
                                class="px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $profileTab === 'profile' ? 'border-[#00a884] text-[#00a884] bg-white font-extrabold' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
                            <i class="fa-solid fa-house"></i> Profile
                        </button>
                        <button type="button" onclick="switchProfileTabPanitia('alamat')" id="tab-btn-alamat-p" 
                                class="px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $profileTab === 'alamat' ? 'border-[#00a884] text-[#00a884] bg-white font-extrabold' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
                            <i class="fa-regular fa-building"></i> Alamat Sekolah
                        </button>
                        <button type="button" onclick="switchProfileTabPanitia('contact')" id="tab-btn-contact-p" 
                                class="px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $profileTab === 'contact' ? 'border-[#00a884] text-[#00a884] bg-white font-extrabold' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
                            <i class="fa-solid fa-phone"></i> Contact
                        </button>
                        <button type="button" onclick="switchProfileTabPanitia('kepsek')" id="tab-btn-kepsek-p" 
                                class="px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $profileTab === 'kepsek' ? 'border-[#00a884] text-[#00a884] bg-white font-extrabold' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
                            <i class="fa-solid fa-user"></i> Data Kepala Sekolah
                        </button>
                    </div>

                    <!-- FORM SIMPAN PENGATURAN SEKOLAH -->
                    <form action="{{ route('panitia.settings.update') }}" method="POST" class="p-6 sm:p-8 space-y-5">
                        @csrf

                        <!-- TAB 1: PROFILE (PERSIS GAMBAR USER) -->
                        <div id="tab-content-profile-p" class="{{ $profileTab === 'profile' ? '' : 'hidden' }} space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">NPSN</label>
                                <div class="sm:col-span-9">
                                    <input type="text" name="school_npsn" value="{{ $settings['school_npsn'] ?? '20308458' }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:bg-white focus:border-teal-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">Nama Sekolah</label>
                                <div class="sm:col-span-9">
                                    <input type="text" name="school_name" value="{{ $settings['school_name'] ?? 'SMK KARYA NUGRAHA BOYOLALI' }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:bg-white focus:border-teal-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">Jenjang Sekolah</label>
                                <div class="sm:col-span-9">
                                    <select name="school_level" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:bg-white focus:border-teal-500">
                                        <option value="SMK" selected>SMK</option>
                                        <option value="SMA">SMA</option>
                                        <option value="SMP">SMP</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">Status Sekolah</label>
                                <div class="sm:col-span-9">
                                    <select name="school_status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold focus:bg-white focus:border-teal-500">
                                        <option value="Swasta" selected>Swasta</option>
                                        <option value="Negeri">Negeri</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: ALAMAT SEKOLAH -->
                        <div id="tab-content-alamat-p" class="{{ $profileTab === 'alamat' ? '' : 'hidden' }} space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">Alamat Lengkap</label>
                                <div class="sm:col-span-9">
                                    <textarea name="school_address" rows="3" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">{{ $settings['school_address'] ?? 'Jl. Tentara Pelajar No. 1, Boyolali, Jawa Tengah' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: CONTACT -->
                        <div id="tab-content-contact-p" class="{{ $profileTab === 'contact' ? '' : 'hidden' }} space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">No. WhatsApp / HP</label>
                                <div class="sm:col-span-9">
                                    <input type="text" name="school_phone" value="{{ $settings['school_phone'] ?? '085290944090' }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">Email Resmi</label>
                                <div class="sm:col-span-9">
                                    <input type="email" name="school_email" value="{{ $settings['school_email'] ?? 'smkknbi@gmail.com' }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                                </div>
                            </div>
                        </div>

                        <!-- TAB 4: DATA KEPALA SEKOLAH -->
                        <div id="tab-content-kepsek-p" class="{{ $profileTab === 'kepsek' ? '' : 'hidden' }} space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">Nama Kepala Sekolah</label>
                                <div class="sm:col-span-9">
                                    <input type="text" name="school_principal_name" value="{{ $settings['school_principal_name'] ?? 'Drs. H. Mulyadi, M.Pd.' }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">NIP / NUPTK</label>
                                <div class="sm:col-span-9">
                                    <input type="text" name="school_principal_nip" value="{{ $settings['school_principal_nip'] ?? '19680512 199403 1 007' }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                                </div>
                            </div>
                        </div>

                        <!-- TOMBOL AKSI -->
                        <div class="pt-4 border-t border-slate-100 flex justify-end gap-2.5">
                            <button type="submit" class="px-5 py-2.5 bg-[#00a65a] hover:bg-[#008f4c] text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-1.5">
                                <i class="fa-solid fa-floppy-disk"></i> Save Changes
                            </button>
                            <button type="reset" class="px-5 py-2.5 bg-[#6c757d] hover:bg-[#5a6268] text-white font-bold text-xs rounded-lg transition">
                                Reset
                            </button>
                        </div>

                    </form>

                </div>
            @endif

            <!-- ================= VIEW 2: VERIFIKASI BERKAS PENDAFTAR ================= -->
            @if(in_array($currentView, ['verifikasi', 'pendaftar']))
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Meja Verifikasi Berkas Calon Siswa Baru</h2>
                            <p class="text-xs text-slate-500">Periksa dan tetapkan status keabsahan dokumen pendaftar</p>
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

                    <!-- TABEL ANTREAN -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-y border-slate-200 font-extrabold uppercase text-[11px] text-slate-600">
                                    <th class="py-3 px-3">Calon Siswa</th>
                                    <th class="py-3 px-3">Jurusan</th>
                                    <th class="py-3 px-3">Berkas Terunggah</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3 text-right">Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($registrations as $reg)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-slate-900">{{ $reg->user->name ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-500 font-mono">
                                                {{ $reg->registration_number }} &bull; NISN: {{ $reg->user->nisn ?? '-' }}
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
                                            <button type="button" onclick="openPanitiaVerifyModal('{{ $reg->id }}', '{{ $reg->registration_number }}', '{{ addslashes($reg->user->name ?? '') }}', '{{ $reg->status }}', '{{ addslashes($reg->verification_notes ?? '') }}')"
                                                    class="px-3 py-1.5 rounded bg-[#00a65a] hover:bg-[#008f4c] text-white text-[11px] font-bold">
                                                Verifikasi
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6 text-center text-slate-400">Tidak ada data verifikasi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
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
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Data Administrasi Pembayaran SPMB</h2>
                            <p class="text-xs text-slate-500">Pengecekan bukti dan status pelunasan pendaftaran</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-y border-slate-200 font-extrabold uppercase text-[11px] text-slate-600">
                                    <th class="py-3 px-3">Invoice</th>
                                    <th class="py-3 px-3">Calon Siswa</th>
                                    <th class="py-3 px-3">Nominal</th>
                                    <th class="py-3 px-3">Metode</th>
                                    <th class="py-3 px-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($payments as $pay)
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-3 font-mono font-bold text-blue-600">{{ $pay->invoice_number }}</td>
                                        <td class="py-3 px-3 font-bold">{{ $pay->registration->user->name ?? '-' }}</td>
                                        <td class="py-3 px-3 font-bold">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-3 uppercase">{{ $pay->payment_method }}</td>
                                        <td class="py-3 px-3 font-bold text-emerald-600">{{ strtoupper($pay->status) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6 text-center text-slate-400">Belum ada transaksi pembayaran.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </main>

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
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-verify-p').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-bold rounded-lg">Simpan</button>
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
            document.getElementById('modal-verify-p').classList.remove('hidden');
        }
    </script>

</body>
</html>
