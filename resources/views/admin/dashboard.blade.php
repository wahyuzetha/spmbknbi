<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin SPMB | SMK Karya Nugraha Boyolali</title>

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
                            sidebarHeader: '#1a2226',
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
            <a href="{{ route('admin.dashboard', ['view' => 'dashboard']) }}" 
               class="flex items-center gap-3 px-3.5 py-3 rounded-lg transition {{ $currentView === 'dashboard' ? 'bg-[#00a65a] text-white shadow-md font-bold' : 'hover:bg-[#1e282c] hover:text-white' }}">
                <i class="fa-solid fa-gauge text-sm w-4 text-center"></i>
                <span class="flex-1">Dashboard</span>
            </a>

            <!-- MENU 2: SEKOLAH (DROPDOWN - BERISI DATA JURUSAN & DATA PENGGUNA) -->
            <div class="space-y-1">
                <button type="button" onclick="toggleDropdown('menu-sekolah', 'arrow-sekolah')" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition {{ in_array($currentView, ['sekolah', 'jurusan', 'pengguna']) ? 'text-white font-bold bg-[#1e282c]/80' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-desktop text-sm w-4 text-center"></i>
                        <span>Sekolah</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="arrow-sekolah"></i>
                </button>
                <div id="menu-sekolah" class="pl-8 pr-2 py-1 space-y-1 text-[11px] {{ in_array($currentView, ['sekolah', 'jurusan', 'pengguna']) ? '' : 'hidden' }}">
                    <a href="{{ route('admin.dashboard', ['view' => 'dashboard', 'tab' => 'profile']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Profil Sekolah
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'jurusan']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'jurusan' ? 'text-emerald-400 font-bold bg-[#1a2226]' : '' }}">
                        • Data Jurusan
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'pengguna']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'pengguna' ? 'text-emerald-400 font-bold bg-[#1a2226]' : '' }}">
                        • Data Pengguna
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'dashboard', 'tab' => 'alamat']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Alamat Sekolah
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'dashboard', 'tab' => 'contact']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Contact Sekolah
                    </a>
                </div>
            </div>

            <!-- MENU 3: DATA SPMB (DROPDOWN) -->
            <div class="space-y-1">
                <button type="button" onclick="toggleDropdown('menu-spmb', 'arrow-spmb')" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition {{ in_array($currentView, ['calon_siswa', 'pendaftar', 'verifikasi', 'diterima']) ? 'text-white font-bold bg-[#1e282c]/80' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-address-card text-sm w-4 text-center"></i>
                        <span>Data SPMB</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="arrow-spmb"></i>
                </button>
                <div id="menu-spmb" class="pl-8 pr-2 py-1 space-y-1 text-[11px] {{ in_array($currentView, ['calon_siswa', 'pendaftar', 'verifikasi', 'diterima']) ? '' : 'hidden' }}">
                    <a href="{{ route('admin.dashboard', ['view' => 'calon_siswa']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ in_array($currentView, ['calon_siswa', 'pendaftar']) ? 'text-emerald-400 font-bold bg-[#1a2226]' : '' }}">
                        • Calon Siswa
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'verifikasi']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'verifikasi' ? 'text-emerald-400 font-bold' : '' }}">
                        • Verifikasi Berkas
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'calon_siswa', 'status' => 'accepted']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Siswa Diterima
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'verifikasi', 'status' => 'pending']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Belum Ujian / Test
                    </a>
                </div>
            </div>

            <!-- MENU 4: ADMINISTRASI (DROPDOWN) -->
            <div class="space-y-1">
                <button type="button" onclick="toggleDropdown('menu-adminitrasi', 'arrow-administrasi')" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition {{ $currentView === 'administrasi' ? 'text-white font-bold bg-[#1e282c]/80' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-regular fa-credit-card text-sm w-4 text-center"></i>
                        <span>Administrasi</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200" id="arrow-administrasi"></i>
                </button>
                <div id="menu-adminitrasi" class="pl-8 pr-2 py-1 space-y-1 text-[11px] {{ $currentView === 'administrasi' ? '' : 'hidden' }}">
                    <a href="{{ route('admin.dashboard', ['view' => 'administrasi']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'administrasi' ? 'text-emerald-400 font-bold' : '' }}">
                        • Pembayaran Pendaftaran
                    </a>
                </div>
            </div>

            <!-- MENU 5: PENGATURAN (DROPDOWN) -->
            <div class="space-y-1">
                <button type="button" onclick="toggleDropdown('menu-pengaturan', 'arrow-pengaturan')" 
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition {{ in_array($currentView, ['pengaturan', 'pengguna']) ? 'text-white font-bold bg-[#1e282c]/80' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-sliders text-sm w-4 text-center"></i>
                        <span>Pengaturan</span>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] transition-transform duration-200 {{ in_array($currentView, ['pengaturan', 'pengguna']) ? 'rotate-180' : '' }}" id="arrow-pengaturan"></i>
                </button>
                <div id="menu-pengaturan" class="pl-8 pr-2 py-1 space-y-1 text-[11px] {{ in_array($currentView, ['pengaturan', 'pengguna']) ? '' : 'hidden' }}">
                    <a href="{{ route('admin.dashboard', ['view' => 'pengaturan']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'pengaturan' ? 'text-emerald-400 font-bold bg-[#1a2226]' : '' }}">
                        • Pengaturan SPMB & Dokumen
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'pengguna']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition {{ $currentView === 'pengguna' ? 'text-emerald-400 font-bold bg-[#1a2226]' : '' }}">
                        • Manajemen Akun Pengguna
                    </a>
                    <a href="{{ route('admin.dashboard', ['view' => 'dashboard', 'tab' => 'profile']) }}" class="block py-1.5 px-2 rounded hover:text-white hover:bg-[#1a2226] transition">
                        • Pengaturan Profil Sekolah
                    </a>
                </div>
            </div>

            <!-- MENU 6: DATABASE BROWSER -->
            <a href="{{ route('admin.database') }}" target="_blank" 
               class="flex items-center justify-between px-3.5 py-2.5 rounded-lg hover:bg-[#1e282c] hover:text-white transition text-slate-300">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-database text-sm w-4 text-center text-emerald-400"></i>
                    <span>Database MySQL</span>
                </div>
                <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Browser</span>
            </a>

        </nav>

        <!-- SIDEBAR FOOTER: USER & LOGOUT -->
        <div class="p-3 bg-[#1a2226] border-t border-black/20 flex items-center justify-between">
            <div class="flex items-center gap-2 overflow-hidden">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="truncate">
                    <div class="text-xs font-bold text-white truncate">{{ auth()->user()->name }}</div>
                    <div class="text-[10px] text-slate-400 truncate">Administrator</div>
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
                <a href="{{ route('admin.database') }}" target="_blank" class="text-xs font-bold text-slate-700 bg-slate-100 hover:bg-emerald-50 hover:text-emerald-700 px-3 py-1.5 rounded-lg border border-slate-200 transition flex items-center gap-1.5 shadow-sm" title="Buka Database MySQL di Browser">
                    <i class="fa-solid fa-database text-emerald-600"></i> Buka Database
                </a>
                <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1 rounded-full border border-emerald-200">
                    TP {{ $academicYear->name ?? '2027/2028' }}
                </span>
            </div>
        </header>

        <!-- CONTAINER KONTEN -->
        <main class="p-6 sm:p-8 space-y-6">

            <!-- NOTIFIKASI SUKSES / ERROR -->
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

            @if($errors->any())
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-medium space-y-1.5 shadow-sm">
                    <div class="flex items-center gap-2 font-bold text-amber-800 text-sm">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600"></i>
                        <span>Perhatian: Gagal memproses data</span>
                    </div>
                    <ul class="list-disc list-inside pl-4 space-y-0.5 text-amber-800 font-semibold">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- ================= VIEW A: MENU SEKOLAH -> DATA JURUSAN (PERSIS GAMBAR 2) ================= -->
            @if($currentView === 'jurusan')
                
                <!-- HEADER BANNER DATA JURUSAN -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <div class="flex items-center gap-2 text-slate-800 text-xl font-bold">
                        <i class="fa-solid fa-list-ul text-slate-700"></i>
                        <h1>Data Jurusan</h1>
                    </div>
                    <button type="button" onclick="openAddMajorModal()" class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-1.5">
                        <i class="fa-solid fa-pen-to-square"></i> Tambah Data
                    </button>
                </div>

                <!-- CARD DATA JURUSAN (PERSIS GAMBAR 2) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <!-- TEAL BANNER CARD HEADER -->
                    <div class="bg-[#00a65a] px-5 py-3 text-white font-bold text-base">
                        Data jurusan
                    </div>

                    <div class="p-5 space-y-4">
                        
                        <!-- TOOLBAR: COPY, CSV, EXCEL, PRINT & LIVE SEARCH -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="copyTable('table-jurusan')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition">Copy</button>
                                <button type="button" onclick="exportTableToCSV('table-jurusan', 'data_jurusan.csv')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition">CSV</button>
                                <button type="button" onclick="exportTableToCSV('table-jurusan', 'data_jurusan.xlsx')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition">Excel</button>
                                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition">Print</button>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="font-bold text-slate-600">Search:</label>
                                <input type="text" id="search-jurusan" onkeyup="filterTable('search-jurusan', 'table-jurusan')" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded text-xs focus:bg-white focus:outline-none focus:border-teal-500 w-48 sm:w-60">
                            </div>
                        </div>

                        <!-- TABEL DATA JURUSAN (PERSIS URUTAN & KOLOM GAMBAR 2) -->
                        <div class="overflow-x-auto">
                            <table id="table-jurusan" class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="border-b-2 border-slate-200 font-bold text-slate-700 text-xs">
                                        <th class="py-3 px-3 w-12 text-center">#</th>
                                        <th class="py-3 px-3">Kode jurusan</th>
                                        <th class="py-3 px-3">Nama jurusan</th>
                                        <th class="py-3 px-3">Kelas Industri</th>
                                        <th class="py-3 px-3 text-center">Kuota</th>
                                        <th class="py-3 px-3 text-center">Status</th>
                                        <th class="py-3 px-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @forelse($majors as $idx => $m)
                                        <tr class="hover:bg-slate-50/70 transition">
                                            <td class="py-3.5 px-3 text-center text-slate-500 font-semibold">{{ $idx + 1 }}</td>
                                            <td class="py-3.5 px-3 font-semibold text-slate-800">{{ $m->code }}</td>
                                            <td class="py-3.5 px-3 font-semibold text-slate-800">
                                                {{ $m->name }}
                                            </td>
                                            <td class="py-3.5 px-3">
                                                <div class="inline-flex items-center gap-1.5 flex-wrap">
                                                    @if(!empty($m->industry_class))
                                                        <span class="px-2.5 py-1 rounded bg-teal-50 border border-teal-200 text-teal-800 font-bold text-[11px] flex items-center gap-1">
                                                            <i class="fa-solid fa-industry text-teal-600"></i>
                                                            {{ $m->industry_class }}
                                                        </span>
                                                    @else
                                                        <span class="text-slate-400 italic text-[11px]">- Non Kelas Industri -</span>
                                                    @endif
                                                    <button type="button" 
                                                            onclick="openEditIndustryClassModal('{{ $m->id }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->industry_class ?? '') }}')" 
                                                            title="Edit khusus Kelas Industri untuk jurusan ini"
                                                            class="px-2 py-0.5 bg-slate-100 hover:bg-teal-50 hover:text-teal-700 text-slate-600 rounded text-[10px] font-bold border border-slate-200 hover:border-teal-300 transition flex items-center gap-1 cursor-pointer">
                                                        <i class="fa-solid fa-pen text-[9px]"></i> Edit Kelas
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-3 text-center font-semibold text-slate-800">{{ $m->quota }}</td>
                                            <td class="py-3.5 px-3 text-center">
                                                <span class="px-2.5 py-1 rounded bg-[#00a65a] text-white font-bold text-[10px]">
                                                    {{ $m->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-3 text-center">
                                                <div class="inline-flex items-center gap-1.5">
                                                    <form action="{{ route('admin.sekolah.jurusan.delete', $m->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan {{ $m->name }}?')">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1.5 bg-[#f5365c] hover:bg-rose-700 text-white rounded font-bold text-xs transition">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                    <button type="button" onclick="openEditMajorModal('{{ $m->id }}', '{{ addslashes($m->code) }}', '{{ addslashes($m->name) }}', '{{ addslashes($m->industry_class ?? '') }}', '{{ $m->quota }}', '{{ $m->is_active ? '1' : '0' }}')" 
                                                            class="px-3 py-1.5 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded font-bold text-xs transition flex items-center gap-1">
                                                        <i class="fa-solid fa-pen-to-square text-[10px]"></i> Edit
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data jurusan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- TABEL FOOTER (SHOWING ENTRIES & PAGINATION PERSIS GAMBAR 2) -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 text-xs text-slate-600">
                            <div>
                                Showing 1 to {{ $majors->count() }} of {{ $majors->count() }} entries
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" class="px-3 py-1 border border-slate-300 rounded text-slate-400 cursor-not-allowed">Previous</button>
                                <span class="px-3 py-1 border border-teal-500 bg-teal-50 text-teal-700 font-bold rounded">1</span>
                                <button type="button" class="px-3 py-1 border border-slate-300 rounded text-slate-400 cursor-not-allowed">Next</button>
                            </div>
                        </div>

                    </div>

                </div>
            @endif

            <!-- ================= VIEW B: MENU SEKOLAH -> DATA PENGGUNA (PERSIS GAMBAR 1) ================= -->
            @if($currentView === 'pengguna')
                
                <!-- HEADER BANNER DATA PENGGUNA -->
                <div class="flex items-center justify-between pb-2 border-b border-slate-200">
                    <div class="flex items-center gap-2 text-slate-800 text-xl font-bold">
                        <i class="fa-solid fa-list-ul text-slate-700"></i>
                        <h1>Data Pengguna</h1>
                    </div>
                    
                    <!-- TOMBOL MERAH HAPUS DATA (BULK DELETE DENGAN CHECKBOX PERSIS GAMBAR 1) -->
                    <button type="button" onclick="submitBulkDeleteUsers()" class="px-4 py-2 bg-[#f5365c] hover:bg-rose-700 text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-1.5">
                        <i class="fa-solid fa-trash-can"></i> Hapus Data
                    </button>
                </div>

                <!-- DUA KOLOM: KIRI (TABEL DATA PENGGUNA) & KANAN (FORM TAMBAH DATA PENGGUNA) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- KOLOM KIRI (7/12): TABEL DATA PENGGUNA -->
                    <div class="lg:col-span-8 bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        
                        <!-- CARD HEADER TEAL -->
                        <div class="bg-[#00a65a] px-5 py-3 text-white font-bold text-base">
                            Data Pengguna
                        </div>

                        <div class="p-5 space-y-4">
                            
                            <!-- TOOLBAR: COPY, CSV, EXCEL, PRINT & LIVE SEARCH -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="copyTable('table-pengguna')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition">Copy</button>
                                    <button type="button" onclick="exportTableToCSV('table-pengguna', 'data_pengguna.csv')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition">CSV</button>
                                    <button type="button" onclick="exportTableToCSV('table-pengguna', 'data_pengguna.xlsx')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition">Excel</button>
                                    <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition">Print</button>
                                </div>
                                <div class="flex items-center gap-2">
                                    <label class="font-bold text-slate-600">Search:</label>
                                    <input type="text" id="search-pengguna" onkeyup="filterTable('search-pengguna', 'table-pengguna')" class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded text-xs focus:bg-white focus:outline-none focus:border-teal-500 w-36 sm:w-48">
                                </div>
                            </div>

                            <!-- TABEL FORM DENGAN CHECKBOXES (BULK DELETE FORM) -->
                            <form id="form-bulk-delete" action="{{ route('admin.sekolah.pengguna.bulkDelete') }}" method="POST">
                                @csrf
                                <div class="overflow-x-auto">
                                    <table id="table-pengguna" class="w-full text-left text-xs border-collapse">
                                        <thead>
                                            <tr class="border-b-2 border-slate-200 font-bold text-slate-700 text-xs">
                                                <th class="py-3 px-3 w-8 text-center">
                                                    <input type="checkbox" id="check-all-users" onclick="toggleSelectAllUsers(this)" class="w-4 h-4 rounded text-teal-600">
                                                </th>
                                                <th class="py-3 px-3 w-10 text-center">No</th>
                                                <th class="py-3 px-3">Nama Pengguna</th>
                                                <th class="py-3 px-3">Akses</th>
                                                <th class="py-3 px-3">Username</th>
                                                <th class="py-3 px-3 text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 font-medium">
                                            @forelse($staffUsers as $idx => $u)
                                                <tr class="hover:bg-slate-50/70 transition">
                                                    <td class="py-3 px-3 text-center">
                                                        <input type="checkbox" name="ids[]" value="{{ $u->id }}" class="user-checkbox w-4 h-4 rounded text-teal-600">
                                                    </td>
                                                    <td class="py-3 px-3 text-center text-slate-500">{{ $idx + 1 }}</td>
                                                    <td class="py-3 px-3 font-bold text-slate-800">{{ $u->name }}</td>
                                                    <td class="py-3 px-3">
                                                        <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ $u->role === 'admin' ? 'bg-purple-100 text-purple-700' : 'bg-emerald-100 text-emerald-800' }}">
                                                            {{ $u->role === 'admin' ? 'Administrator' : 'Panitia SPMB' }}
                                                        </span>
                                                    </td>
                                                    <td class="py-3 px-3 font-semibold text-slate-600 font-mono">{{ $u->username ?? $u->phone }}</td>
                                                    <td class="py-3 px-3 text-center">
                                                        <div class="flex items-center justify-center gap-1.5">
                                                            <button type="button" onclick="editUserCard('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->username ?? '') }}', '{{ $u->role }}')" 
                                                                    class="p-1.5 w-7 h-7 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded font-bold text-xs inline-flex items-center justify-center transition shadow-sm" title="Edit Data & Ganti Password">
                                                                <i class="fa-solid fa-pen-to-square"></i>
                                                            </button>
                                                            @if($u->id !== auth()->id())
                                                                <button type="button" onclick="confirmDeleteUser('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ addslashes($u->username ?? '') }}')" 
                                                                        class="p-1.5 w-7 h-7 bg-[#f5365c] hover:bg-rose-700 text-white rounded font-bold text-xs inline-flex items-center justify-center transition shadow-sm" title="Hapus Pengguna">
                                                                    <i class="fa-solid fa-trash-can"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="py-6 text-center text-slate-400">Belum ada data staf pengguna.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </form>

                            <!-- FORM TERSEMBUNYI UNTUK HAPUS PENGGUNA SATUAN -->
                            <form id="form-delete-single-user" action="" method="POST" class="hidden">
                                @csrf
                            </form>

                            <!-- TABEL FOOTER -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-3 text-xs text-slate-600">
                                <div>
                                    Showing 1 to {{ $staffUsers->count() }} of {{ $staffUsers->count() }} entries
                                </div>
                                <div class="flex items-center gap-1">
                                    <button type="button" class="px-3 py-1 border border-slate-300 rounded text-slate-400 cursor-not-allowed">Previous</button>
                                    <span class="px-3 py-1 border border-teal-500 bg-teal-50 text-teal-700 font-bold rounded">1</span>
                                    <button type="button" class="px-3 py-1 border border-slate-300 rounded text-slate-400 cursor-not-allowed">Next</button>
                                </div>
                            </div>

                        </div>

                    </div>

                    <!-- KOLOM KANAN (5/12): CARD TAMBAH / EDIT DATA PENGGUNA (PERSIS GAMBAR 1) -->
                    <div class="lg:col-span-4 bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                        
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <h2 class="text-base font-bold text-slate-800" id="card-user-title">
                                Tambah Data Pengguna
                            </h2>
                            <span id="user-mode-badge" class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold">Mode Tambah</span>
                        </div>

                        <form id="form-user-action" action="{{ route('admin.sekolah.pengguna.store') }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama</label>
                                <input type="text" name="name" id="user-input-name" required placeholder="Contoh: smkknbi"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium focus:border-teal-500 focus:outline-none">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Username</label>
                                <input type="text" name="username" id="user-input-username" required placeholder="Contoh: smkknbi"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium focus:border-teal-500 focus:outline-none">
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-semibold text-slate-700" id="label-pwd">Password *</label>
                                    <button type="button" onclick="togglePasswordVisibility()" class="text-[11px] text-blue-600 hover:text-blue-800 font-semibold flex items-center gap-1">
                                        <i class="fa-solid fa-eye" id="eye-icon"></i> <span id="eye-text">Lihat</span>
                                    </button>
                                </div>
                                <input type="password" name="password" id="user-input-password" required placeholder="Kata sandi akun"
                                       class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium focus:border-teal-500 focus:outline-none">
                                <p class="text-[10px] text-slate-400 mt-1" id="pwd-help-text">Minimal 4 karakter. Berikan password ini kepada panitia terkait untuk login.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Level Akses</label>
                                <select name="role" id="user-input-role" required class="w-full px-3.5 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-medium focus:border-teal-500 focus:outline-none">
                                    <option value="panitia" selected>Panitia SPMB</option>
                                    <option value="admin">Administrator SPMB</option>
                                </select>
                            </div>

                            <!-- INFORMASI KEAMANAN & HAK AKSES PANITIA -->
                            <div class="p-3 bg-slate-50 border border-slate-200 rounded-xl text-slate-600 text-[11px] space-y-1">
                                <div class="font-bold text-slate-700 flex items-center gap-1.5">
                                    <i class="fa-solid fa-shield-halved text-emerald-600"></i> Ketentuan Akses Panitia:
                                </div>
                                <p>Panitia SPMB tidak dapat mendaftar sendiri dari luar. Panitia SPMB hanya dapat login jika akun (Username & Password) telah dibuat dan diberikan resmi oleh Administrator melalui menu ini.</p>
                            </div>

                            <!-- TOMBOL SIMPAN & CANCEL (PERSIS GAMBAR 1) -->
                            <div class="pt-3 flex items-center gap-2">
                                <button type="submit" class="px-5 py-2.5 bg-[#00a65a] hover:bg-[#008f4c] text-white font-bold text-xs rounded-lg shadow transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-check"></i> Simpan
                                </button>
                                <button type="button" onclick="resetUserCard()" class="px-5 py-2.5 bg-[#6c757d] hover:bg-[#5a6268] text-white font-bold text-xs rounded-lg transition flex items-center gap-1.5">
                                    <i class="fa-solid fa-xmark"></i> Cancel
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            @endif

            <!-- ================= VIEW C: DASHBOARD UTAMA (4 METRIC CARDS & TABS PROFIL SEKOLAH) ================= -->
            @if($currentView === 'dashboard')
                
                <!-- BANNER HEADER ATAS (PERSIS GAMBAR USER) -->
                <div class="bg-white rounded-xl p-5 border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-slate-800 text-xl font-black">
                            <span class="text-emerald-600"><i class="fa-solid fa-table-cells-large"></i></span>
                            <h1>Dashboard</h1>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Selamat Datang, <strong>{{ auth()->user()->name }}</strong> di APLIKASI SPMB ONLINE SMK KARYA NUGRAHA BOYOLALI.
                        </p>
                    </div>

                    <!-- BREADCRUMB -->
                    <div class="text-xs font-bold text-slate-500 bg-slate-50 px-3.5 py-1.5 rounded-lg border border-slate-200 self-start sm:self-auto flex items-center gap-1.5">
                        <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600"><i class="fa-solid fa-house"></i> Home</a>
                        <span>/</span>
                        <span class="text-emerald-700">Dashboard</span>
                    </div>
                </div>

                <!-- 4 KARTU METRIK UTAMA -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    
                    <!-- KARTU 1: SELURUH PENDAFTAR -->
                    <a href="{{ route('admin.dashboard', ['view' => 'pendaftar']) }}" 
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

                    <!-- KARTU 2: SISWA DITERIMA -->
                    <a href="{{ route('admin.dashboard', ['view' => 'pendaftar', 'status' => 'accepted']) }}" 
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

                    <!-- KARTU 3: BELUM UJIAN/TEST -->
                    <a href="{{ route('admin.dashboard', ['view' => 'verifikasi', 'status' => 'pending']) }}" 
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

                    <!-- KARTU 4: SUDAH MEMBAYAR -->
                    <a href="{{ route('admin.dashboard', ['view' => 'administrasi']) }}" 
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

                <!-- CARD PROFIL SEKOLAH (PERSIS GAMBAR REFERENSI SEBELUMNYA) -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <div class="flex items-center gap-1 border-b border-slate-200 px-4 pt-3 bg-slate-50/50 overflow-x-auto text-xs font-bold">
                        <button type="button" onclick="switchProfileTab('profile')" id="tab-btn-profile" 
                                class="px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $profileTab === 'profile' ? 'border-[#00a884] text-[#00a884] bg-white font-extrabold' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
                            <i class="fa-solid fa-house"></i> Profile
                        </button>
                        <button type="button" onclick="switchProfileTab('alamat')" id="tab-btn-alamat" 
                                class="px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $profileTab === 'alamat' ? 'border-[#00a884] text-[#00a884] bg-white font-extrabold' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
                            <i class="fa-regular fa-building"></i> Alamat Sekolah
                        </button>
                        <button type="button" onclick="switchProfileTab('contact')" id="tab-btn-contact" 
                                class="px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $profileTab === 'contact' ? 'border-[#00a884] text-[#00a884] bg-white font-extrabold' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
                            <i class="fa-solid fa-phone"></i> Contact
                        </button>
                        <button type="button" onclick="switchProfileTab('kepsek')" id="tab-btn-kepsek" 
                                class="px-4 py-2.5 rounded-t-lg transition flex items-center gap-2 border-b-2 {{ $profileTab === 'kepsek' ? 'border-[#00a884] text-[#00a884] bg-white font-extrabold' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
                            <i class="fa-solid fa-user"></i> Data Kepala Sekolah
                        </button>
                    </div>

                    <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6 sm:p-8 space-y-5">
                        @csrf

                        <!-- TAB 1: PROFILE -->
                        <div id="tab-content-profile" class="{{ $profileTab === 'profile' ? '' : 'hidden' }} space-y-4">
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

                        <!-- TAB 2: ALAMAT -->
                        <div id="tab-content-alamat" class="{{ $profileTab === 'alamat' ? '' : 'hidden' }} space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
                                <label class="sm:col-span-3 text-xs font-bold text-slate-700 sm:text-right">Alamat Lengkap</label>
                                <div class="sm:col-span-9">
                                    <textarea name="school_address" rows="3" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">{{ $settings['school_address'] ?? 'Jl. Tentara Pelajar No. 1, Boyolali, Jawa Tengah' }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: CONTACT -->
                        <div id="tab-content-contact" class="{{ $profileTab === 'contact' ? '' : 'hidden' }} space-y-4">
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

                        <!-- TAB 4: KEPALA SEKOLAH -->
                        <div id="tab-content-kepsek" class="{{ $profileTab === 'kepsek' ? '' : 'hidden' }} space-y-4">
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

                        <!-- ACTION BUTTONS -->
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

            <!-- ================= VIEW D: DATA CALON SISWA (PERSIS CONTOH GAMBAR) ================= -->
            @if(in_array($currentView, ['calon_siswa', 'pendaftar', 'diterima']))
                <div class="space-y-4">
                    
                    <!-- CARD UTAMA DATA CALON SISWA -->
                    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm p-6 sm:p-7 space-y-6">
                        
                        <!-- HEADER CARD DENGAN ICON TOGA HIJAU & 3 TOMBOL UTAMA -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                            
                            <!-- KIRI: ICON TOGA & TITLE -->
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-[#e6f7f0] text-[#00a65a] flex items-center justify-center text-2xl shadow-sm border border-[#c3eedb]">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <h1 class="text-xl font-black text-slate-800 tracking-tight flex items-center gap-2">
                                        Data Calon Siswa
                                    </h1>
                                    <p class="text-xs text-slate-500 font-medium mt-0.5">
                                        Manajemen Pendaftaran Angkatan Terbaru
                                    </p>
                                </div>
                            </div>

                            <!-- KANAN: TOMBOL EXPORT, CETAK ALL, TAMBAH DATA (PERSIS GAMBAR) -->
                            <div class="flex items-center flex-wrap gap-2.5">
                                
                                <!-- 1. Tombol Export Excel Lengkap -->
                                <a href="{{ route('admin.spmb.exportExcel', request()->all()) }}" 
                                   class="px-4 py-2 bg-white hover:bg-emerald-50 text-[#00a65a] border border-[#00a65a] rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm"
                                   title="Unduh Keseluruhan Data Siswa dalam Format Excel (.xlsx)">
                                    <i class="fa-regular fa-file-excel text-sm"></i>
                                    <span>Export</span>
                                </a>

                                <!-- 2. Tombol Cetak All -->
                                <a href="{{ route('admin.spmb.printAll', request()->all()) }}" target="_blank"
                                   class="px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm"
                                   title="Cetak Laporan Rekapitulasi Calon Siswa (Print/PDF)">
                                    <i class="fa-solid fa-print text-sm text-slate-600"></i>
                                    <span>Cetak All</span>
                                </a>

                                <!-- 3. Tombol Tambah Data (+ Modal) -->
                                <button type="button" onclick="openAddStudentModal()" 
                                        class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-600/20">
                                    <i class="fa-solid fa-plus text-xs"></i>
                                    <span>Tambah Data</span>
                                </button>

                            </div>

                        </div>

                        <!-- TOOLBAR DATATABLES (COPY, CSV, EXCEL, PRINT & LIVE SEARCH) -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="copyTable('table-calon-siswa')" class="px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded font-bold text-slate-700 transition shadow-2xs">Copy</button>
                                <button type="button" onclick="exportTableToCSV('table-calon-siswa', 'data_calon_siswa.csv')" class="px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded font-bold text-slate-700 transition shadow-2xs">CSV</button>
                                <a href="{{ route('admin.spmb.exportExcel', request()->all()) }}" class="px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded font-bold text-slate-700 transition shadow-2xs inline-block">Excel</a>
                                <button type="button" onclick="window.print()" class="px-3.5 py-1.5 bg-slate-50 hover:bg-slate-100 border border-slate-300 rounded font-bold text-slate-700 transition shadow-2xs">Print</button>
                                <button type="button" id="btn-bulk-delete-student" onclick="submitBulkDeleteStudents()" class="hidden ml-2 px-3.5 py-1.5 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded font-bold text-rose-700 transition">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus Terpilih (<span id="bulk-student-count">0</span>)
                                </button>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="font-bold text-slate-600">Search:</label>
                                <input type="text" id="search-student-input" onkeyup="filterStudentTable()" placeholder="" 
                                       class="px-3 py-1.5 bg-white border border-slate-300 rounded text-xs focus:outline-none focus:border-teal-500 w-36 sm:w-56 shadow-2xs">
                            </div>
                        </div>

                        <!-- FORM BULK DELETE & TABEL DATA CALON SISWA PERSIS GAMBAR REFERENSI -->
                        <form id="form-bulk-delete-student" action="{{ route('admin.spmb.student.bulkDelete') }}" method="POST">
                            @csrf
                            <div class="overflow-x-auto border-t border-slate-100">
                                <table id="table-calon-siswa" class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-slate-600 text-xs font-bold bg-white">
                                            <th class="py-3 px-3 w-8 text-center">
                                                <input type="checkbox" id="check-all-students" onclick="toggleSelectAllStudents(this)" class="w-4 h-4 rounded text-teal-600">
                                            </th>
                                            <th class="py-3 px-3 w-10 text-center cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center justify-center gap-1">No <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-3 cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center gap-1">NO Pendaftar <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-3 cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center gap-1">Username <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-3 cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center gap-1">Password <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-4 cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center gap-1">Nama <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-2.5 text-center cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center justify-center gap-1">L/P <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-4 cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center gap-1">Asal Sekolah <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-3 text-center cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center justify-center gap-1">No Hp <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-4 cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center gap-1">Jurusan <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-3 text-center cursor-pointer hover:text-slate-900 select-none">
                                                <div class="flex items-center justify-center gap-1">Data <span class="text-[9px] text-slate-400">⇅</span></div>
                                            </th>
                                            <th class="py-3 px-3 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-medium">
                                        @php
                                            $displayStudents = isset($allCalonSiswa) && count($allCalonSiswa) > 0 ? $allCalonSiswa : $registrations;
                                        @endphp
                                        @forelse($displayStudents as $idx => $r)
                                            @php
                                                $u = $r->user;
                                                $bio = $r->biodata;
                                                $school = $r->school;
                                                $phone = $u->phone ?? $bio->no_hp ?? '';
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                                if (str_starts_with($cleanPhone, '0')) {
                                                    $waPhone = '62' . substr($cleanPhone, 1);
                                                } else {
                                                    $waPhone = $cleanPhone;
                                                }
                                                $studentName = $bio->nama_lengkap ?? $u->name ?? '-';
                                                $waText = urlencode("Halo {$studentName}, kami dari Panitia SPMB SMK Karya Nugraha Boyolali menginformasikan pendaftaran Anda dengan No. {$r->registration_number}.");
                                            @endphp
                                            <tr id="student-row-{{ $r->id }}" data-student-id="{{ $r->id }}" class="hover:bg-slate-50/70 transition student-row">
                                                
                                                <!-- Checkbox -->
                                                <td class="py-3.5 px-3 text-center">
                                                    <input type="checkbox" name="ids[]" value="{{ $r->id }}" onchange="updateBulkStudentCount()" class="student-item-cb w-4 h-4 rounded text-teal-600">
                                                </td>

                                                <!-- No -->
                                                <td class="py-3.5 px-3 text-center text-slate-500 font-medium student-row-no">
                                                    {{ $idx + 1 }}
                                                </td>

                                                <!-- NO Pendaftar -->
                                                <td class="py-3.5 px-3 font-mono font-bold text-slate-900">
                                                    {{ $r->registration_number }}
                                                </td>

                                                <!-- Username -->
                                                <td class="py-3.5 px-3 font-mono text-slate-700">
                                                    {{ $u->username ?? $u->phone ?? '-' }}
                                                </td>

                                                <!-- Password -->
                                                <td class="py-3.5 px-3 font-mono text-slate-600">
                                                    {{ $u->raw_password ?? '12345' }}
                                                </td>

                                                <!-- Nama -->
                                                <td class="py-3.5 px-4 font-bold text-slate-900 uppercase">
                                                    {{ $studentName }}
                                                </td>

                                                <!-- L/P Badge Persegi Abu-abu -->
                                                <td class="py-3.5 px-2.5 text-center">
                                                    <span class="inline-block px-2 py-0.5 border border-slate-300 rounded text-[11px] font-bold text-slate-600 bg-slate-50/80">
                                                        {{ $bio->jenis_kelamin ?? 'L' }}
                                                    </span>
                                                </td>

                                                <!-- Asal Sekolah -->
                                                <td class="py-3.5 px-4 text-slate-700 uppercase font-semibold text-[11px]">
                                                    {{ $school->asal_sekolah ?? '-' }}
                                                </td>

                                                <!-- No Hp WhatsApp Clickable -->
                                                <td class="py-3.5 px-3 text-center">
                                                    @if(!empty($waPhone))
                                                        <a href="https://wa.me/{{ $waPhone }}?text={{ $waText }}" target="_blank" 
                                                           class="w-7 h-7 rounded-full bg-[#25D366] hover:bg-[#20ba5a] text-white inline-flex items-center justify-center text-xs transition shadow-sm"
                                                           title="Chat WhatsApp ke {{ $phone }}">
                                                            <i class="fa-brands fa-whatsapp text-sm"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-slate-300">-</span>
                                                    @endif
                                                </td>

                                                <!-- Jurusan -->
                                                <td class="py-3.5 px-4 text-slate-800 font-semibold text-[11px]">
                                                    {{ $r->major->name ?? '-' }}
                                                    @if(!empty($r->major->industry_class))
                                                        <span class="text-slate-500 font-normal">({{ $r->major->industry_class }})</span>
                                                    @endif
                                                </td>

                                                <!-- Data / Status (Bullet Dot Merah Pending / Hijau Diterima, Clickable Sekali Respon) -->
                                                <td class="py-3.5 px-3 text-center" id="status-cell-{{ $r->id }}">
                                                    @if($r->status === 'accepted')
                                                        <button type="button" 
                                                                onclick="promptChangeStudentStatus('{{ $r->id }}', '{{ addslashes($studentName) }}', '{{ $r->registration_number }}', 'accepted')"
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition cursor-pointer shadow-2xs hover:shadow active:scale-95 group"
                                                                title="Klik untuk mengubah status (Diterima -> Pending)">
                                                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 
                                                            <span>Diterima</span>
                                                        </button>
                                                    @elseif($r->status === 'verified')
                                                        <button type="button" 
                                                                onclick="promptChangeStudentStatus('{{ $r->id }}', '{{ addslashes($studentName) }}', '{{ $r->registration_number }}', 'verified')"
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 transition cursor-pointer shadow-2xs hover:shadow active:scale-95"
                                                                title="Klik untuk verifikasi calon siswa ini">
                                                            <span class="w-2 h-2 rounded-full bg-indigo-500"></span> 
                                                            <span>Terverifikasi</span>
                                                        </button>
                                                    @elseif($r->status === 'rejected')
                                                        <button type="button" 
                                                                onclick="promptChangeStudentStatus('{{ $r->id }}', '{{ addslashes($studentName) }}', '{{ $r->registration_number }}', 'rejected')"
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition cursor-pointer shadow-2xs hover:shadow active:scale-95"
                                                                title="Klik untuk mengubah status pendaftaran">
                                                            <span class="w-2 h-2 rounded-full bg-rose-500"></span> 
                                                            <span>Ditolak</span>
                                                        </button>
                                                    @else
                                                        <!-- Pending Merah Bulat Persis Screenshot -->
                                                        <button type="button" 
                                                                onclick="promptChangeStudentStatus('{{ $r->id }}', '{{ addslashes($studentName) }}', '{{ $r->registration_number }}', 'pending')"
                                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50/90 hover:bg-rose-100 text-rose-600 border border-rose-200 transition cursor-pointer shadow-2xs hover:shadow active:scale-95 group"
                                                                title="Klik untuk verifikasi calon siswa ini (Pending -> Diterima)">
                                                            <span class="w-2 h-2 rounded-full bg-rose-500 shadow-sm animate-pulse"></span> 
                                                            <span>Pending</span>
                                                        </button>
                                                    @endif
                                                </td>

                                                <!-- Action (Edit Pensil & Cetak Printer) -->
                                                <td class="py-3.5 px-3 text-center">
                                                    <div class="flex items-center justify-center gap-1.5">
                                                        <!-- Tombol Edit Siswa -->
                                                        <button type="button" 
                                                                onclick="openEditStudentModal('{{ $r->id }}', '{{ addslashes($studentName) }}', '{{ $u->username ?? $u->phone }}', '{{ $phone }}', '{{ $bio->jenis_kelamin ?? 'L' }}', '{{ addslashes($school->asal_sekolah ?? '') }}', '{{ $r->major_id }}')"
                                                                class="w-7 h-7 rounded bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs"
                                                                title="Edit Data Calon Siswa">
                                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                                        </button>

                                                        <!-- Tombol Cetak Kartu Individual -->
                                                        <a href="{{ route('admin.spmb.printCard', $r->id) }}" target="_blank"
                                                           class="w-7 h-7 rounded bg-white hover:bg-slate-100 border border-slate-300 text-slate-700 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs"
                                                           title="Cetak Kartu Tanda Bukti Pendaftaran">
                                                            <i class="fa-solid fa-print text-[11px]"></i>
                                                        </a>

                                                        <!-- Tombol Hapus Satuan -->
                                                        <button type="button" 
                                                                onclick="confirmDeleteStudent('{{ $r->id }}', '{{ addslashes($studentName) }}', '{{ $r->registration_number }}')"
                                                                class="w-7 h-7 rounded bg-white hover:bg-rose-50 border border-rose-200 text-rose-600 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs"
                                                                title="Hapus Data Calon Siswa">
                                                            <i class="fa-solid fa-trash-can text-[11px]"></i>
                                                        </button>
                                                    </div>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="12" class="py-10 text-center text-slate-400">
                                                    <i class="fa-solid fa-inbox text-3xl mb-2 text-slate-300"></i>
                                                    <p>Tidak ada data calon siswa terdaftar.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </form>

                        <!-- FOOTER TABEL (SHOWING ENTRIES & PAGINATION PERSIS GAMBAR REFERENSI) -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-4 text-xs text-slate-600 border-t border-slate-200">
                            <div id="student-table-info" class="text-slate-600 font-medium">
                                Showing 1 to {{ min(10, count($displayStudents)) }} of {{ count($displayStudents) }} entries
                            </div>
                            <div id="student-table-pagination" class="flex items-center gap-1 select-none">
                                <span class="text-slate-400 cursor-not-allowed select-none px-2.5 py-1 text-xs">Previous</span>
                                <button type="button" class="border border-slate-300 bg-gradient-to-b from-white to-slate-200 text-slate-800 font-bold px-3 py-1 rounded text-xs shadow-xs mx-0.5">1</button>
                                <span class="text-slate-400 cursor-not-allowed select-none px-2.5 py-1 text-xs">Next</span>
                            </div>
                        </div>

                    </div>

                </div>

                <!-- FORM TERSEMBUNYI UNTUK HAPUS SISWA SATUAN -->
                <form id="form-delete-single-student" action="" method="POST" class="hidden">
                    @csrf
                </form>

                <!-- ================= MODAL TAMBAH CALON SISWA (+ MODAL) ================= -->
                <div id="modal-add-student" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 {{ $errors->any() && old('nama_siswa') ? '' : 'hidden' }}">
                    <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh]">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2 text-slate-900 font-bold text-base">
                                <i class="fa-solid fa-user-plus text-[#00a65a]"></i>
                                <h3>Tambah Data Calon Siswa Baru</h3>
                            </div>
                            <button type="button" onclick="closeAddStudentModal()" class="text-slate-400 hover:text-slate-700">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <form action="{{ route('admin.spmb.student.store') }}" method="POST" autocomplete="off" class="space-y-4 text-xs font-medium text-slate-700">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="font-bold text-slate-800">Nama Lengkap Siswa *</label>
                                    <input type="text" name="nama_siswa" required value="{{ old('nama_siswa') }}" placeholder="Contoh: MUHAMMAD AKSA ANGGARA" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">No. HP / WhatsApp Siswa *</label>
                                    <input type="text" name="phone" required value="{{ old('phone') }}" placeholder="085876098129" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">NISN Siswa (Opsional)</label>
                                    <input type="text" name="nisn" value="{{ old('nisn') }}" autocomplete="off" placeholder="Nomor Induk Siswa Nasional" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">Username Akun (Opsional)</label>
                                    <input type="text" name="username" value="{{ old('username') }}" autocomplete="off" placeholder="Bisa no. HP atau NISN" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">Password Akun *</label>
                                    <input type="text" name="password" required value="{{ old('password', '12345') }}" autocomplete="new-password" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">Jenis Kelamin *</label>
                                    <select name="gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-bold">
                                        <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki (L)</option>
                                        <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan (P)</option>
                                    </select>
                                </div>
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="font-bold text-slate-800">Asal Sekolah (SMP/MTs) *</label>
                                    <input type="text" name="asal_sekolah" required value="{{ old('asal_sekolah') }}" placeholder="Contoh: SMP NEGERI 3 MOJOSONGO" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg uppercase focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="font-bold text-slate-800">Kompetensi Keahlian (Pilihan Jurusan) *</label>
                                    <select name="major_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-semibold">
                                        @foreach($majors as $m)
                                            <option value="{{ $m->id }}" {{ old('major_id') == $m->id ? 'selected' : '' }}>{{ $m->name }} {{ !empty($m->industry_class) ? '('.$m->industry_class.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">Status Awal</label>
                                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-semibold">
                                        <option value="pending" {{ old('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="verified" {{ old('status') === 'verified' ? 'selected' : '' }}>Terverifikasi</option>
                                        <option value="accepted" {{ old('status') === 'accepted' ? 'selected' : '' }}>Diterima</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                                <button type="button" onclick="closeAddStudentModal()" class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 font-bold text-slate-600 transition">Batal</button>
                                <button type="submit" class="px-5 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-lg font-bold transition flex items-center gap-1.5 shadow">
                                    <i class="fa-solid fa-save"></i> Simpan Pendaftaran
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ================= MODAL EDIT CALON SISWA ================= -->
                <div id="modal-edit-student" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
                    <div class="bg-white rounded-2xl max-w-xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh]">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2 text-slate-900 font-bold text-base">
                                <i class="fa-solid fa-pen-to-square text-[#00a65a]"></i>
                                <h3>Edit Data Calon Siswa</h3>
                            </div>
                            <button type="button" onclick="closeEditStudentModal()" class="text-slate-400 hover:text-slate-700">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>

                        <form id="form-edit-student-action" action="" method="POST" class="space-y-4 text-xs font-medium text-slate-700">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="font-bold text-slate-800">Nama Lengkap Siswa *</label>
                                    <input type="text" id="edit-student-name" name="nama_siswa" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">No. HP / WhatsApp Siswa *</label>
                                    <input type="text" id="edit-student-phone" name="phone" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">Username Akun</label>
                                    <input type="text" id="edit-student-username" name="username" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">Password Baru (Opsional)</label>
                                    <input type="text" id="edit-student-password" name="password" placeholder="Kosongkan jika tak diubah" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg font-mono focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="font-bold text-slate-800">Jenis Kelamin *</label>
                                    <select id="edit-student-gender" name="gender" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-bold">
                                        <option value="L">Laki-laki (L)</option>
                                        <option value="P">Perempuan (P)</option>
                                    </select>
                                </div>
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="font-bold text-slate-800">Asal Sekolah (SMP/MTs) *</label>
                                    <input type="text" id="edit-student-school" name="asal_sekolah" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg uppercase focus:bg-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1 sm:col-span-2">
                                    <label class="font-bold text-slate-800">Kompetensi Keahlian (Pilihan Jurusan) *</label>
                                    <select id="edit-student-major" name="major_id" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg focus:bg-white focus:outline-none focus:border-teal-500 font-semibold">
                                        @foreach($majors as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }} {{ !empty($m->industry_class) ? '('.$m->industry_class.')' : '' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-4 border-t border-slate-100">
                                <button type="button" onclick="closeEditStudentModal()" class="px-4 py-2 border border-slate-300 rounded-lg hover:bg-slate-50 font-bold text-slate-600 transition">Batal</button>
                                <button type="submit" class="px-5 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-lg font-bold transition flex items-center gap-1.5 shadow">
                                    <i class="fa-solid fa-save"></i> Perbarui Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ================= MODAL KONFIRMASI STATUS CEPAT (SEKALI RESPON) ================= -->
                <div id="modal-quick-status" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
                    <div class="bg-white rounded-2xl max-w-md w-full p-6 space-y-4 shadow-2xl border border-slate-200 transform transition-all animate-in fade-in zoom-in-95 duration-150">
                        <!-- Header -->
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div id="quick-status-icon-container" class="w-10 h-10 rounded-full flex items-center justify-center text-lg bg-emerald-100 text-[#00a65a]">
                                    <i class="fa-solid fa-user-check"></i>
                                </div>
                                <div>
                                    <h3 id="quick-status-title" class="font-bold text-slate-900 text-sm">Verifikasi Calon Siswa</h3>
                                    <p class="text-[11px] text-slate-500">Konfirmasi perubahan status pendaftaran</p>
                                </div>
                            </div>
                            <button type="button" onclick="closeQuickStatusModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 inline-flex items-center justify-center transition">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </button>
                        </div>

                        <!-- Info Card -->
                        <div class="p-3.5 rounded-xl bg-slate-50/80 border border-slate-200/80 space-y-2 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Nama Calon Siswa:</span>
                                <span id="quick-status-student-name" class="font-bold text-slate-900 uppercase"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">No. Pendaftar:</span>
                                <span id="quick-status-reg-num" class="font-mono font-bold text-slate-700"></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Status Saat Ini:</span>
                                <span id="quick-status-current-badge"></span>
                            </div>
                        </div>

                        <!-- Pertanyaan Konfirmasi -->
                        <div class="text-center py-1">
                            <p id="quick-status-question" class="text-xs text-slate-700 leading-relaxed font-medium"></p>
                        </div>

                        <!-- Tombol Aksi Langsung (Sekali Respon) -->
                        <div id="quick-status-actions" class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                            <!-- Tombol dirender secara dinamis oleh JavaScript -->
                        </div>
                    </div>
                </div>

            @endif

            <!-- ================= VIEW E: MEJA VERIFIKASI BERKAS ================= -->
            @if($currentView === 'verifikasi')
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div>
                            <h2 class="text-base font-extrabold text-slate-900">Meja Verifikasi Kelengkapan Dokumen</h2>
                            <p class="text-xs text-slate-500">Pengecekan berkas persyaratan calon siswa baru</p>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-y border-slate-200 font-extrabold uppercase text-[11px] text-slate-600">
                                    <th class="py-3 px-3">Calon Siswa</th>
                                    <th class="py-3 px-3">Jurusan</th>
                                    <th class="py-3 px-3">Dokumen Terunggah</th>
                                    <th class="py-3 px-3">Status</th>
                                    <th class="py-3 px-3 text-right">Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                @forelse($registrations as $reg)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="py-3 px-3">
                                            <div class="font-bold text-slate-900">{{ $reg->user->name ?? '-' }}</div>
                                            <div class="text-[10px] text-slate-500 font-mono">{{ $reg->registration_number }} &bull; NISN: {{ $reg->user->nisn ?? '-' }}</div>
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
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $reg->status === 'verified' ? 'bg-indigo-100 text-indigo-800' : ($reg->status === 'accepted' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800') }}">
                                                {{ strtoupper($reg->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            <button type="button" onclick="openStatusModal('{{ $reg->id }}', '{{ $reg->registration_number }}', '{{ addslashes($reg->user->name ?? '') }}', '{{ $reg->status }}', '{{ addslashes($reg->verification_notes ?? '') }}')"
                                                    class="px-3 py-1.5 rounded bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold">
                                                Periksa
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6 text-center text-slate-400">Tidak ada data verifikasi.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <!-- ================= VIEW G: PENGATURAN SPMB & DOKUMEN SEKOLAH TERPADU ================= -->
            @if($currentView === 'pengaturan')
                
                <!-- HEADER UTAMA PENGATURAN -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0">
                            <i class="fa-solid fa-sliders"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-black text-slate-900">Pengaturan SPMB & Dokumen Resmi</h1>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Sistem Terpadu
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Kelola tahun ajaran aktif, logo sekolah, nomor hotline panitia, tanda tangan digital ketua panitia, serta format kop surat resmi dalam satu menu.
                            </p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <a href="{{ route('admin.spmb.printAll') }}" target="_blank" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-lg border border-slate-300 transition flex items-center gap-2 shadow-xs">
                            <i class="fa-solid fa-file-invoice text-emerald-600"></i> Pratinjau Rekapitulasi
                        </a>
                        @if(!empty($registrations->first()))
                            <a href="{{ route('admin.spmb.printCard', $registrations->first()->id) }}" target="_blank" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg transition flex items-center gap-2 shadow-sm">
                                <i class="fa-solid fa-id-card"></i> Pratinjau Kartu Peserta
                            </a>
                        @endif
                    </div>
                </div>

                <!-- ================= 1. MANAJEMEN TAHUN AJARAN (ACADEMIC YEAR) ================= -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-700 to-teal-800 px-6 py-4 text-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-calendar-days"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold">1. Pergantian & Pengaturan Tahun Ajaran SPMB</h2>
                                <p class="text-xs text-emerald-100">Atur tahun ajaran aktif yang menjadi acuan pendaftaran siswa baru</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs bg-white/20 text-white px-3 py-1 rounded-full font-bold">
                                Aktif Saat Ini: <strong>{{ $academicYear->name ?? 'Belum Ada' }}</strong>
                            </span>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        
                        <!-- BARIS STATUS & FORM TAMBAH TAHUN AJARAN -->
                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                            
                            <!-- KIRI (5/12): KARTU STATUS TAHUN AJARAN AKTIF -->
                            <div class="lg:col-span-5 bg-gradient-to-br from-emerald-50 to-teal-50/50 border border-emerald-200 rounded-xl p-5 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="px-2.5 py-1 rounded-md bg-emerald-600 text-white text-[11px] font-extrabold uppercase tracking-wider">
                                        TAHUN AJARAN AKTIF
                                    </span>
                                    <i class="fa-solid fa-circle-check text-emerald-600 text-xl"></i>
                                </div>
                                <div>
                                    <div class="text-3xl font-black text-slate-900 tracking-tight">
                                        {{ $academicYear->name ?? 'Belum Diatur' }}
                                    </div>
                                    <div class="text-xs text-slate-600 mt-1 flex items-center gap-1.5 font-medium">
                                        <i class="fa-regular fa-clock text-emerald-600"></i>
                                        <span>Periode: 
                                            {{ $academicYear && $academicYear->start_date ? date('d M Y', strtotime($academicYear->start_date)) : '1 Jan' }} s/d 
                                            {{ $academicYear && $academicYear->end_date ? date('d M Y', strtotime($academicYear->end_date)) : '31 Des' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="pt-3 border-t border-emerald-200/80 text-xs text-slate-600 flex items-center justify-between">
                                    <span>Pendaftar di TP ini:</span>
                                    <span class="font-extrabold text-slate-900 text-sm bg-white px-2.5 py-0.5 rounded border border-emerald-200">
                                        {{ $academicYear ? $academicYear->registrations()->count() : 0 }} Siswa
                                    </span>
                                </div>
                            </div>

                            <!-- KANAN (7/12): FORM TAMBAH TAHUN AJARAN BARU -->
                            <div class="lg:col-span-7 bg-slate-50 border border-slate-200 rounded-xl p-5">
                                <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider mb-3 flex items-center gap-2">
                                    <i class="fa-solid fa-plus-circle text-emerald-600"></i> Tambah Tahun Ajaran Baru
                                </h3>
                                <form action="{{ route('admin.academicYears.store') }}" method="POST" class="space-y-3.5 text-xs">
                                    @csrf
                                    <div>
                                        <label class="block font-bold text-slate-700 mb-1">Nama Tahun Ajaran *</label>
                                        <input type="text" name="name" required placeholder="Contoh: 2027/2028 atau 2028/2029" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500 font-bold">
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Tanggal Buka Pendaftaran</label>
                                            <input type="date" name="start_date" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-700 mb-1">Tanggal Tutup Pendaftaran</label>
                                            <input type="date" name="end_date" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg focus:outline-none focus:border-teal-500">
                                        </div>
                                    </div>
                                    <div class="flex items-center justify-between pt-1">
                                        <label class="flex items-center gap-2 font-semibold text-slate-700 cursor-pointer">
                                            <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-500">
                                            <span>Langsung jadikan Tahun Ajaran Aktif</span>
                                        </label>
                                        <button type="submit" class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-lg font-bold transition flex items-center gap-1.5 shadow-sm">
                                            <i class="fa-solid fa-save"></i> Simpan
                                        </button>
                                    </div>
                                </form>
                            </div>

                        </div>

                        <!-- TABEL RIWAYAT SEMUA TAHUN AJARAN -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden">
                            <div class="bg-slate-100/70 px-4 py-2.5 border-b border-slate-200 font-bold text-xs text-slate-700 flex items-center justify-between">
                                <span>Daftar Tahun Ajaran Tercatat</span>
                                <span class="text-[11px] text-slate-500 font-normal">Klik tombol "Aktifkan" untuk mengganti tahun ajaran aktif</span>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs border-collapse">
                                    <thead>
                                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold">
                                            <th class="py-2.5 px-4 w-12 text-center">No</th>
                                            <th class="py-2.5 px-4">Tahun Ajaran</th>
                                            <th class="py-2.5 px-4">Periode Pendaftaran</th>
                                            <th class="py-2.5 px-4 text-center">Jumlah Pendaftar</th>
                                            <th class="py-2.5 px-4 text-center">Status</th>
                                            <th class="py-2.5 px-4 text-center">Aksi / Pergantian</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 font-medium">
                                        @forelse($allAcademicYears as $idx => $ay)
                                            <tr class="hover:bg-slate-50/80 transition {{ $ay->is_active ? 'bg-emerald-50/40' : '' }}">
                                                <td class="py-3 px-4 text-center text-slate-500">{{ $idx + 1 }}</td>
                                                <td class="py-3 px-4 font-bold text-slate-900 text-sm">
                                                    {{ $ay->name }}
                                                </td>
                                                <td class="py-3 px-4 text-slate-600">
                                                    {{ $ay->start_date ? date('d M Y', strtotime($ay->start_date)) : '-' }} s/d {{ $ay->end_date ? date('d M Y', strtotime($ay->end_date)) : '-' }}
                                                </td>
                                                <td class="py-3 px-4 text-center font-bold text-slate-800">
                                                    <span class="px-2 py-0.5 rounded bg-slate-100 border border-slate-200 font-mono">
                                                        {{ $ay->registrations_count ?? 0 }} Siswa
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    @if($ay->is_active)
                                                        <span class="px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-extrabold text-[11px] border border-emerald-300 inline-flex items-center gap-1">
                                                            <i class="fa-solid fa-circle text-[8px] text-emerald-600 animate-pulse"></i> AKTIF
                                                        </span>
                                                    @else
                                                        <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-500 font-semibold text-[11px]">
                                                            Nonaktif
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <div class="inline-flex items-center gap-2">
                                                        @if(!$ay->is_active)
                                                            <form action="{{ route('admin.academicYears.activate', $ay->id) }}" method="POST">
                                                                @csrf
                                                                <button type="submit" onclick="return confirm('Alihkan tahun ajaran aktif ke {{ $ay->name }}?')" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg text-xs transition shadow-xs flex items-center gap-1">
                                                                    <i class="fa-solid fa-arrows-rotate"></i> Jadikan Aktif
                                                                </button>
                                                            </form>
                                                            @if(($ay->registrations_count ?? 0) === 0)
                                                                <form action="{{ route('admin.academicYears.delete', $ay->id) }}" method="POST">
                                                                    @csrf
                                                                    <button type="submit" onclick="return confirm('Hapus tahun ajaran {{ $ay->name }}?')" class="px-2.5 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg text-xs transition border border-rose-200" title="Hapus Tahun Ajaran">
                                                                        <i class="fa-solid fa-trash-can"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        @else
                                                            <span class="text-xs font-bold text-emerald-700 flex items-center gap-1">
                                                                <i class="fa-solid fa-check"></i> Sedang Digunakan
                                                            </span>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="py-6 text-center text-slate-400">Belum ada data tahun ajaran tersimpan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ================= FORM PENGATURAN TERPADU (LOGO, HOTLINE, TTD, KOP SURAT) ================= -->
                <form action="{{ route('admin.settings.spmb.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- ================= 2. LOGO SEKOLAH & IDENTITAS ================= -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-teal-700 to-cyan-800 px-6 py-4 text-white flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-school"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold">2. Logo Sekolah & Identitas Lembaga</h2>
                                <p class="text-xs text-teal-100">Logo ini otomatis tercetak pada kop surat, kartu peserta, dan dokumen resmi</p>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                                
                                <!-- PREVIEW LOGO -->
                                <div class="md:col-span-4 flex flex-col items-center justify-center p-6 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl text-center">
                                    <div class="w-28 h-28 rounded-2xl bg-white border border-slate-200 shadow-sm p-2 flex items-center justify-center overflow-hidden mb-3">
                                        @if(!empty($settings['school_logo']))
                                            <img src="{{ asset($settings['school_logo']) }}" alt="Logo Sekolah" class="max-w-full max-h-full object-contain">
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
                                    <span class="text-xs font-bold text-slate-800">
                                        {{ !empty($settings['school_logo']) ? 'Logo Kustom Terpasang' : 'Logo Default KNBI' }}
                                    </span>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Format disarankan: PNG transparan (Maks. 2MB)</p>

                                    @if(!empty($settings['school_logo']))
                                        <label class="mt-3 inline-flex items-center gap-1.5 text-xs text-rose-600 font-bold cursor-pointer hover:underline">
                                            <input type="checkbox" name="delete_logo" value="1" class="w-3.5 h-3.5 rounded text-rose-600">
                                            <span>Hapus Logo (Reset Default)</span>
                                        </label>
                                    @endif
                                </div>

                                <!-- FORM UPLOAD & TEKS IDENTITAS -->
                                <div class="md:col-span-8 space-y-4 text-xs">
                                    <div>
                                        <label class="block font-bold text-slate-800 mb-1">Ganti File Logo Sekolah</label>
                                        <input type="file" name="school_logo" accept="image/png,image/jpeg,image/webp,image/svg+xml" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer">
                                        <span class="text-[11px] text-slate-500 mt-1 block">Pilih file logo sekolah baru jika ingin mengganti logo di seluruh sistem & cetakan.</span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block font-bold text-slate-800 mb-1">Nama Resmi Sekolah</label>
                                            <input type="text" name="school_name" value="{{ $settings['school_name'] ?? 'SMK KARYA NUGRAHA BOYOLALI' }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-bold focus:bg-white focus:outline-none focus:border-teal-500">
                                        </div>
                                        <div>
                                            <label class="block font-bold text-slate-800 mb-1">Nomor Pokok Sekolah Nasional (NPSN)</label>
                                            <input type="text" name="school_npsn" value="{{ $settings['school_npsn'] ?? '20308458' }}" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-mono font-bold focus:bg-white focus:outline-none focus:border-teal-500">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <!-- ================= 3. HOTLINE PANITIA & TTD KETUA PANITIA ================= -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                        
                        <!-- KIRI: NOMOR HOTLINE PANITIA SPMB -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden h-full">
                            <div class="bg-gradient-to-r from-emerald-600 to-green-700 px-6 py-4 text-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-base">
                                        <i class="fa-solid fa-headset"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-bold">3. Nomor Hotline Panitia SPMB</h2>
                                        <p class="text-[11px] text-emerald-100">Kontak bantuan calon siswa & orang tua</p>
                                    </div>
                                </div>
                                <i class="fa-brands fa-whatsapp text-2xl text-emerald-200"></i>
                            </div>

                            <div class="p-6 space-y-4 text-xs">
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Nomor WhatsApp / Hotline *</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-emerald-600 font-bold">
                                            <i class="fa-brands fa-whatsapp text-base"></i>
                                        </div>
                                        <input type="text" name="hotline_panitia" value="{{ $settings['hotline_panitia'] ?? '085876098129' }}" required placeholder="Contoh: 085876098129" class="w-full pl-9 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-mono font-bold focus:bg-white focus:outline-none focus:border-emerald-500">
                                    </div>
                                    <p class="text-[11px] text-slate-500 mt-1">Nomor ini tampil di form pendaftaran siswa, halaman bantuan, dan kartu pendaftaran.</p>
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Nama Layanan / Petugas PIC</label>
                                    <input type="text" name="hotline_panitia_name" value="{{ $settings['hotline_panitia_name'] ?? 'Pusat Informasi SPMB SMK KNBI' }}" placeholder="Contoh: Admin SPMB KNBI" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-emerald-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Jam Operasional Layanan</label>
                                    <input type="text" name="hotline_panitia_hours" value="{{ $settings['hotline_panitia_hours'] ?? 'Senin - Sabtu: 07.30 - 15.00 WIB' }}" placeholder="Contoh: Setiap Hari: 08.00 - 16.00 WIB" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-emerald-500">
                                </div>

                                @php
                                    $rawWa = preg_replace('/[^0-9]/', '', $settings['hotline_panitia'] ?? '085876098129');
                                    if(str_starts_with($rawWa, '0')) {
                                        $rawWa = '62' . substr($rawWa, 1);
                                    }
                                @endphp
                                <div class="pt-2">
                                    <a href="https://wa.me/{{ $rawWa }}?text=Halo%20Panitia%20SPMB%20SMK%20Karya%20Nugraha%20Boyolali" target="_blank" class="w-full py-2 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-300 rounded-lg font-bold flex items-center justify-center gap-2 transition">
                                        <i class="fa-brands fa-whatsapp text-emerald-600 text-sm"></i> Uji Coba Hubungi Hotline WhatsApp
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- KANAN: TANDA TANGAN (TTD) DIGITAL KETUA PANITIA -->
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden h-full">
                            <div class="bg-gradient-to-r from-indigo-700 to-blue-800 px-6 py-4 text-white flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-base">
                                        <i class="fa-solid fa-signature"></i>
                                    </div>
                                    <div>
                                        <h2 class="text-sm font-bold">4. Tanda Tangan (TTD) Ketua Panitia</h2>
                                        <p class="text-[11px] text-indigo-100">Otentikasi dokumen cetak kartu & bukti terima</p>
                                    </div>
                                </div>
                                <i class="fa-solid fa-stamp text-2xl text-indigo-200"></i>
                            </div>

                            <div class="p-6 space-y-4 text-xs">
                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">Nama Lengkap Ketua Panitia SPMB *</label>
                                    <input type="text" name="chairman_name" value="{{ $settings['chairman_name'] ?? 'H. Bambang Wahyudi, S.Pd., M.Kom.' }}" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-bold focus:bg-white focus:outline-none focus:border-indigo-500">
                                </div>

                                <div>
                                    <label class="block font-bold text-slate-800 mb-1">NIP / NUPTK Ketua Panitia</label>
                                    <input type="text" name="chairman_nip" value="{{ $settings['chairman_nip'] ?? '19780514 200501 1 008' }}" placeholder="Boleh dikosongkan jika tidak ada" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-xs font-mono font-semibold focus:bg-white focus:outline-none focus:border-indigo-500">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center pt-2">
                                    <div class="sm:col-span-5 bg-slate-50 border border-slate-200 rounded-xl p-3 flex flex-col items-center justify-center text-center">
                                        <div class="w-24 h-16 bg-white border border-slate-200 rounded-lg flex items-center justify-center overflow-hidden mb-1.5 p-1">
                                            @if(!empty($settings['chairman_signature']))
                                                <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD Ketua" class="max-w-full max-h-full object-contain">
                                            @else
                                                <div class="text-[10px] text-slate-400 italic font-mono">[ Belum Ada TTD ]</div>
                                            @endif
                                        </div>
                                        <span class="text-[10px] font-bold text-slate-700">Preview TTD Digital</span>
                                        @if(!empty($settings['chairman_signature']))
                                            <label class="mt-1 text-[10px] text-rose-600 font-bold cursor-pointer hover:underline flex items-center gap-1">
                                                <input type="checkbox" name="delete_signature" value="1" class="w-3 h-3 text-rose-600">
                                                <span>Hapus TTD</span>
                                            </label>
                                        @endif
                                    </div>
                                    <div class="sm:col-span-7 space-y-1.5">
                                        <label class="block font-bold text-slate-800">Upload Gambar TTD (PNG Transparan)</label>
                                        <input type="file" name="chairman_signature" accept="image/png,image/jpeg,image/webp" class="w-full px-2.5 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs file:mr-2 file:py-1 file:px-2.5 file:rounded file:border-0 file:text-[11px] file:font-bold file:bg-indigo-600 file:text-white hover:file:bg-indigo-700 cursor-pointer">
                                        <p class="text-[10px] text-slate-500">Disarankan latar belakang transparan (.png) agar menyatu dengan dokumen cetak.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ================= 4. KOP SURAT RESMI & LIVE PREVIEW ================= -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-6 py-4 text-white flex items-center gap-3">
                            <div class="w-9 h-9 rounded-lg bg-white/20 flex items-center justify-center text-lg">
                                <i class="fa-solid fa-file-lines"></i>
                            </div>
                            <div>
                                <h2 class="text-base font-bold">5. Format Kop Surat Resmi & Simulasi Cetak A4</h2>
                                <p class="text-xs text-slate-300">Format kop surat standar kedinasan yang digunakan pada cetak rekapitulasi, surat pengumuman, dan kartu peserta</p>
                            </div>
                        </div>

                        <div class="p-6 space-y-6">
                            
                            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                                
                                <!-- FORM UPLOAD GAMBAR KOP SURAT (5/12) -->
                                <div class="lg:col-span-5 space-y-4 text-xs">
                                    <div class="p-5 bg-slate-50 border border-slate-200 rounded-xl space-y-4">
                                        <div class="flex items-center justify-between">
                                            <span class="font-bold text-slate-800 text-sm flex items-center gap-2">
                                                <i class="fa-regular fa-image text-emerald-600"></i> Gambar Kop Surat
                                            </span>
                                            @if(!empty($settings['cop_image_banner']))
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800 text-[10px] font-extrabold border border-emerald-200">
                                                    Aktif
                                                </span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-600 text-[10px] font-bold">
                                                    Belum Diunggah
                                                </span>
                                            @endif
                                        </div>

                                        <p class="text-[11px] text-slate-600 leading-relaxed">
                                            Unggah file gambar kop surat resmi sekolah (Format <b>PNG, JPG, atau WebP</b>). Gambar ini akan otomatis dicetak sebagai kepala surat pada seluruh dokumen cetak rekapitulasi siswa, kartu pendaftaran, dan laporan resmi.
                                        </p>

                                        <!-- PREVIEW MINI GAMBAR KOP AKTIF -->
                                        @if(!empty($settings['cop_image_banner']))
                                            <div class="p-3 bg-white border border-slate-200 rounded-lg space-y-2">
                                                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Gambar Kop Surat Saat Ini:</span>
                                                <div class="max-h-24 overflow-hidden rounded border border-slate-100 flex items-center justify-center bg-slate-50 p-1">
                                                    <img src="{{ asset($settings['cop_image_banner']) }}" alt="Kop Surat" class="max-w-full max-h-20 object-contain">
                                                </div>
                                                <label class="inline-flex items-center gap-1.5 text-xs text-rose-600 font-bold cursor-pointer hover:underline pt-1">
                                                    <input type="checkbox" name="delete_cop_banner" value="1" class="w-3.5 h-3.5 rounded text-rose-600">
                                                    <span>Hapus Gambar Kop Surat</span>
                                                </label>
                                            </div>
                                        @endif

                                        <div>
                                            <label class="block font-bold text-slate-800 mb-1">Pilih File Gambar Kop Surat Baru</label>
                                            <input type="file" name="cop_image_banner" accept="image/png,image/jpeg,image/webp" class="w-full px-3 py-2 bg-white border border-slate-300 rounded-lg text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-bold file:bg-slate-800 file:text-white hover:file:bg-slate-900 cursor-pointer">
                                            <span class="text-[10px] text-slate-500 block mt-1">Disarankan gambar beresolusi tinggi dengan rasio horizontal (Maks. 3MB).</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- LIVE PREVIEW KERTAS A4 DOKUMEN CETAK (7/12) -->
                                <div class="lg:col-span-7 bg-slate-100 p-4 sm:p-6 rounded-2xl border border-slate-300">
                                    <div class="flex items-center justify-between mb-3 text-xs">
                                        <span class="font-extrabold text-slate-700 flex items-center gap-1.5">
                                            <i class="fa-solid fa-eye text-emerald-600"></i> Pratinjau Langsung (Simulasi Lembar Cetak A4)
                                        </span>
                                        <span class="text-[11px] bg-white px-2 py-0.5 rounded border border-slate-200 text-slate-500 font-mono">
                                            Skala 100%
                                        </span>
                                    </div>

                                    <!-- KERTAS SIMULASI A4 -->
                                    <div class="bg-white p-6 rounded-xl shadow-lg border border-slate-200 text-slate-900 select-none font-serif text-[11px]">
                                        
                                        <!-- KOP SURAT GAMBAR RESMI -->
                                        @if(!empty($settings['cop_image_banner']))
                                            <div class="mb-4 pb-2 border-b-2 border-slate-900">
                                                <img src="{{ asset($settings['cop_image_banner']) }}" alt="Banner Kop Surat" class="w-full h-auto object-contain max-h-32 mx-auto">
                                            </div>
                                        @else
                                            <div class="w-full py-8 border-2 border-dashed border-slate-300 rounded-xl text-center p-4 bg-slate-50/70 mb-4 flex flex-col items-center justify-center">
                                                <i class="fa-regular fa-image text-3xl text-slate-300 mb-2"></i>
                                                <p class="text-xs font-bold text-slate-600 font-sans">Belum Ada Gambar Kop Surat</p>
                                                <p class="text-[10px] text-slate-400 font-sans mt-0.5">Unggah gambar file kop surat resmi sekolah di sebelah kiri agar tampil di sini.</p>
                                            </div>
                                        @endif

                                        <!-- CONTOH KONTEN RESMI SPMB -->
                                        <div class="text-center my-3 font-sans">
                                            <h3 class="text-xs font-black uppercase tracking-wider underline">SURAT BUKTI KELULUSAN & PENDAFTARAN SPMB</h3>
                                            <p class="text-[10px] text-slate-500 mt-0.5">Nomor: 421.5/SPMB-KNBI/{{ date('Y') }}/001</p>
                                        </div>

                                        <p class="text-[10px] font-sans leading-relaxed text-slate-700 mb-3">
                                            Panitia Penerimaan Peserta Didik Baru (SPMB) SMK Karya Nugraha Boyolali Tahun Ajaran <strong>{{ $academicYear->name ?? '2026/2027' }}</strong> menyatakan bahwa pendaftar berikut telah memenuhi persyaratan verifikasi berkas dan registrasi:
                                        </p>

                                        <!-- CONTOH TABEL MINI -->
                                        <table class="w-full text-left font-sans text-[10px] border border-slate-300 mb-4">
                                            <tr class="bg-slate-50 border-b border-slate-300">
                                                <td class="py-1 px-2 font-bold w-28 text-slate-600">No. Registrasi</td>
                                                <td class="py-1 px-2 font-mono font-bold text-emerald-700">REG-{{ date('Y') }}-0001</td>
                                            </tr>
                                            <tr class="border-b border-slate-200">
                                                <td class="py-1 px-2 font-bold text-slate-600">Nama Siswa</td>
                                                <td class="py-1 px-2 font-bold uppercase">MUHAMMAD AKSA ANGGARA</td>
                                            </tr>
                                            <tr>
                                                <td class="py-1 px-2 font-bold text-slate-600">Pilihan Jurusan</td>
                                                <td class="py-1 px-2 font-bold text-slate-800">Teknik Kendaraan Ringan (Kelas Industri Daihatsu)</td>
                                            </tr>
                                        </table>

                                        <!-- BLOK TANDA TANGAN KETUA PANITIA -->
                                        <div class="flex justify-end pt-2 font-sans">
                                            <div class="w-48 text-center text-[10px] space-y-1">
                                                <p>Boyolali, {{ date('d F Y') }}</p>
                                                <p class="font-bold">Ketua Panitia SPMB,</p>
                                                <div class="h-14 flex items-center justify-center">
                                                    @if(!empty($settings['chairman_signature']))
                                                        <img src="{{ asset($settings['chairman_signature']) }}" alt="TTD" class="max-h-12 object-contain">
                                                    @else
                                                        <div class="h-10 border-b border-dashed border-slate-400 w-28 mx-auto flex items-end justify-center pb-0.5 text-[8px] text-slate-400 font-mono">
                                                            ( TTD Digital )
                                                        </div>
                                                    @endif
                                                </div>
                                                <p class="font-bold underline uppercase text-slate-900 leading-tight">
                                                    {{ $settings['chairman_name'] ?? 'H. Bambang Wahyudi, S.Pd., M.Kom.' }}
                                                </p>
                                                <p class="text-[9px] text-slate-500 font-mono leading-tight">
                                                    NIP. {{ $settings['chairman_nip'] ?? '19780514 200501 1 008' }}
                                                </p>
                                            </div>
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <!-- TOMBOL SIMPAN PENGATURAN TERPADU -->
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-200">
                                <a href="{{ route('admin.dashboard') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold text-xs transition">
                                    Kembali ke Dashboard
                                </a>
                                <button type="submit" class="px-7 py-3 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-xl font-black text-xs transition flex items-center gap-2 shadow-lg transform hover:-translate-y-0.5">
                                    <i class="fa-solid fa-cloud-arrow-up text-base"></i> Simpan Seluruh Pengaturan SPMB
                                </button>
                            </div>

                        </div>
                    </div>

                </form>

            @endif

            <!-- ================= VIEW F: ADMINISTRASI PEMBAYARAN KASIR / BENDAHARA SPMB ================= -->
            @if($currentView === 'administrasi')
                
                <!-- HEADER UTAMA ADMINISTRASI -->
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center text-2xl flex-shrink-0">
                            <i class="fa-solid fa-cash-register"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h1 class="text-xl font-black text-slate-900">Administrasi Pembayaran & Kasir SPMB</h1>
                                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    Petugas Bendahara
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Pencatatan kas masuk pembayaran tunai pendaftaran calon siswa di loket bendahara sekolah secara real-time. Otomatis terverifikasi dan digabung (global) jika terdapat lebih dari satu transaksi tanpa menghilangkan history pembayaran sebelumnya.
                            </p>
                        </div>
                    </div>
                    <div>
                        <button type="button" onclick="openInputPaymentModal()" 
                                class="px-5 py-3 bg-[#00a65a] hover:bg-[#008f4c] text-white rounded-xl font-black text-xs transition flex items-center gap-2 shadow-lg transform hover:-translate-y-0.5 cursor-pointer">
                            <i class="fa-solid fa-plus-circle text-base"></i> + Input Pembayaran Tunai
                        </button>
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK KEUANGAN & PEMBAYARAN -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    
                    <!-- KARTU 1: TOTAL KAS MASUK -->
                    <div class="rounded-xl p-5 text-white shadow-lg relative overflow-hidden"
                         style="background: linear-gradient(135deg, #00a65a 0%, #00733e 100%);">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-100">TOTAL KAS MASUK (TERVERIFIKASI)</div>
                                <div class="text-2xl sm:text-3xl font-black mt-1 font-mono">
                                    Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20 text-[11px] font-semibold text-emerald-100 flex items-center justify-between">
                            <span>Total Seluruh Uang Masuk Kas SPMB</span>
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                    </div>

                    <!-- KARTU 2: TOTAL SISWA TELAH MEMBAYAR -->
                    <div class="rounded-xl p-5 text-white shadow-lg relative overflow-hidden"
                         style="background: linear-gradient(135deg, #0073b7 0%, #005689 100%);">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-user-check"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-extrabold uppercase tracking-widest text-sky-100">SISWA TELAH MEMBAYAR</div>
                                <div class="text-3xl font-black mt-1">
                                    {{ $totalSiswaBayar }} <span class="text-sm font-semibold">Siswa</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20 text-[11px] font-semibold text-sky-100 flex items-center justify-between">
                            <span>Tercatat Pada Sistem Kasir Bendahara</span>
                            <i class="fa-solid fa-users"></i>
                        </div>
                    </div>

                    <!-- KARTU 3: TOTAL TRANSAKSI & ANGSURAN -->
                    <div class="rounded-xl p-5 text-white shadow-lg relative overflow-hidden"
                         style="background: linear-gradient(135deg, #605ca8 0%, #484482 100%);">
                        <div class="flex items-center justify-between">
                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center text-2xl">
                                <i class="fa-solid fa-layer-group"></i>
                            </div>
                            <div class="text-right">
                                <div class="text-[10px] font-extrabold uppercase tracking-widest text-purple-100">FREKUENSI TRANSAKSI MASUK</div>
                                <div class="text-3xl font-black mt-1">
                                    {{ $totalTransaksi }} <span class="text-sm font-semibold">Transaksi</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 pt-2 border-t border-white/20 text-[11px] font-semibold text-purple-100 flex items-center justify-between">
                            <span>{{ $multiTransactionCount }} Siswa multi-transaksi / cicil (digabung)</span>
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                    </div>

                </div>

                <!-- CARD TABEL REKAPITULASI PEMBAYARAN GLOBAL -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                    
                    <!-- HEADER TABEL BANNER TEAL -->
                    <div class="bg-[#00a65a] px-6 py-3.5 text-white font-bold text-base flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Rekapitulasi Global Pembayaran Calon Siswa (1 Baris per Siswa)</span>
                        </div>
                        <span class="text-xs bg-white/20 text-white px-3 py-1 rounded-full font-bold self-start sm:self-auto">
                            Otomatis Di-Global jika > 1 Kali Transaksi
                        </span>
                    </div>

                    <div class="p-5 space-y-4">
                        
                        <!-- TOOLBAR: EXPORT, PRINT & LIVE SEARCH -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <button type="button" onclick="copyTable('table-administrasi')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition flex items-center gap-1">
                                    <i class="fa-regular fa-copy"></i> Copy
                                </button>
                                <button type="button" onclick="exportTableToCSV('table-administrasi', 'administrasi_pembayaran_spmb.csv')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition flex items-center gap-1">
                                    <i class="fa-solid fa-file-csv"></i> CSV
                                </button>
                                <button type="button" onclick="exportTableToCSV('table-administrasi', 'administrasi_pembayaran_spmb.xlsx')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition flex items-center gap-1">
                                    <i class="fa-solid fa-file-excel text-emerald-600"></i> Excel
                                </button>
                                <button type="button" onclick="window.print()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded font-semibold text-slate-700 transition flex items-center gap-1">
                                    <i class="fa-solid fa-print"></i> Print
                                </button>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="font-bold text-slate-600">Pencarian Data:</label>
                                <input type="text" id="search-administrasi" onkeyup="filterTable('search-administrasi', 'table-administrasi')" 
                                       placeholder="Cari No. Pendaftar / Nama Siswa..." 
                                       class="px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-teal-500 w-56 sm:w-72">
                            </div>
                        </div>

                        <!-- TABEL ADMINISTRASI PEMBAYARAN GLOBAL -->
                        <div class="overflow-x-auto">
                            <table id="table-administrasi" class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b-2 border-slate-200 font-bold text-slate-700 text-xs">
                                        <th class="py-3 px-3 w-10 text-center">#</th>
                                        <th class="py-3 px-3">No. Pendaftar</th>
                                        <th class="py-3 px-3">Calon Siswa & Kontak</th>
                                        <th class="py-3 px-3">Pilihan Jurusan</th>
                                        <th class="py-3 px-3 text-right">Total Pembayaran (Global)</th>
                                        <th class="py-3 px-3 text-center">Transaksi & Status</th>
                                        <th class="py-3 px-3">Tgl Terakhir & Bendahara</th>
                                        <th class="py-3 px-3 text-center w-36">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @forelse($groupedPayments as $idx => $item)
                                        @php
                                            $reg = $item->registration;
                                            $userSiswa = $reg?->user;
                                            $major = $reg?->major;
                                            $school = $reg?->school;
                                            
                                            // Serialize history pembayaran untuk modal popup tanpa menghilangkan transaksi sebelumnya
                                            $historyList = $item->payments->map(function($p) {
                                                return [
                                                    'id' => $p->id,
                                                    'invoice_number' => $p->invoice_number,
                                                    'payment_date' => $p->payment_date ? \Carbon\Carbon::parse($p->payment_date)->format('d/m/Y') : \Carbon\Carbon::parse($p->created_at)->format('d/m/Y'),
                                                    'payment_type' => $p->payment_type,
                                                    'amount' => $p->amount,
                                                    'amount_formatted' => 'Rp ' . number_format($p->amount, 0, ',', '.'),
                                                    'notes' => $p->notes ?: '-',
                                                    'confirmed_by' => $p->confirmedBy->name ?? 'Bendahara SPMB',
                                                    'print_url' => route('admin.administrasi.payment.receipt', $p->id),
                                                ];
                                            });
                                        @endphp
                                        <tr class="hover:bg-slate-50/80 transition">
                                            <td class="py-3.5 px-3 text-center text-slate-500 font-semibold">{{ $idx + 1 }}</td>
                                            
                                            <!-- NO PENDAFTAR -->
                                            <td class="py-3.5 px-3">
                                                <span class="font-mono font-black text-slate-900 bg-slate-100 px-2.5 py-1 rounded border border-slate-200">
                                                    {{ $reg->registration_number ?? '-' }}
                                                </span>
                                            </td>

                                            <!-- NAMA CALON SISWA & ASAL SEKOLAH -->
                                            <td class="py-3.5 px-3">
                                                <div class="font-bold text-slate-900 text-xs uppercase">
                                                    {{ $userSiswa->name ?? 'Calon Siswa' }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 flex items-center gap-1.5 mt-0.5">
                                                    <span><i class="fa-solid fa-phone text-slate-400 text-[10px]"></i> {{ $userSiswa->phone ?? '-' }}</span>
                                                    <span>•</span>
                                                    <span>{{ $school->asal_sekolah ?? '-' }}</span>
                                                </div>
                                            </td>

                                            <!-- JURUSAN & KELAS INDUSTRI -->
                                            <td class="py-3.5 px-3">
                                                <div class="font-bold text-slate-800">
                                                    {{ $major->name ?? '-' }}
                                                </div>
                                                @if(!empty($major->industry_class))
                                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded bg-teal-50 border border-teal-200 text-teal-800 font-bold text-[10px]">
                                                        <i class="fa-solid fa-industry text-teal-600"></i>
                                                        {{ $major->industry_class }}
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- TOTAL PEMBAYARAN GLOBAL -->
                                            <td class="py-3.5 px-3 text-right">
                                                <div class="font-mono font-black text-emerald-700 text-sm">
                                                    Rp {{ number_format($item->total_amount, 0, ',', '.') }}
                                                </div>
                                                @if($item->is_multi)
                                                    <div class="text-[10px] text-emerald-600 font-bold">
                                                        Total {{ $item->transaction_count }}x Setoran Tunai
                                                    </div>
                                                @endif
                                            </td>

                                            <!-- TRANSAKSI & STATUS VERIFIKASI -->
                                            <td class="py-3.5 px-3 text-center">
                                                <div class="flex flex-col items-center gap-1">
                                                    @if($item->is_multi)
                                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-800 font-black text-[10px] border border-purple-200 shadow-2xs">
                                                            <i class="fa-solid fa-layer-group"></i> {{ $item->transaction_count }}x Transaksi (Global)
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 font-bold text-[10px] border border-slate-200">
                                                            1x Pembayaran
                                                        </span>
                                                    @endif

                                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-emerald-100 text-emerald-800 font-bold text-[10px] border border-emerald-200">
                                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Terverifikasi
                                                    </span>
                                                </div>
                                            </td>

                                            <!-- TGL TERAKHIR & PETUGAS BENDAHARA -->
                                            <td class="py-3.5 px-3">
                                                <div class="text-slate-700 font-medium">
                                                    {{ \Carbon\Carbon::parse($item->last_payment_date)->translatedFormat('d F Y') }}
                                                </div>
                                                <div class="text-[11px] text-slate-500 flex items-center gap-1 mt-0.5">
                                                    <i class="fa-solid fa-user-tie text-slate-400"></i>
                                                    <span>{{ $item->last_confirmed_by->name ?? 'Bendahara' }}</span>
                                                </div>
                                            </td>

                                            <!-- ACTION BUTTONS -->
                                            <td class="py-3.5 px-3 text-center">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    
                                                    <!-- TOMBOL RIWAYAT LENGKAP TRANSAKSI -->
                                                    <button type="button" 
                                                            onclick='openPaymentHistoryModal("{{ addslashes($userSiswa->name ?? 'Calon Siswa') }}", "{{ $reg->registration_number }}", "Rp {{ number_format($item->total_amount, 0, ',', '.') }}", {{ json_encode($historyList) }}, "{{ route('admin.administrasi.payment.globalReceipt', $item->registration_id) }}")'
                                                            class="p-1.5 w-7 h-7 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg border border-indigo-200 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs cursor-pointer"
                                                            title="Lihat Rincian History Transaksi Siswa Ini">
                                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                                    </button>

                                                    <!-- TOMBOL INPUT TAMBAH PEMBAYARAN SISWA INI (CICILAN / BERIKUTNYA) -->
                                                    <button type="button" 
                                                            onclick="openInputPaymentModal('{{ $item->registration_id }}', '{{ addslashes($userSiswa->name ?? '') }}', '{{ $reg->registration_number }}')"
                                                            class="p-1.5 w-7 h-7 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 rounded-lg border border-emerald-200 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs cursor-pointer"
                                                            title="+ Tambah Pembayaran Kasir untuk Siswa Ini">
                                                        <i class="fa-solid fa-plus"></i>
                                                    </button>

                                                    <!-- TOMBOL CETAK KWITANSI REKAPITULASI (GLOBAL) -->
                                                    <a href="{{ route('admin.administrasi.payment.globalReceipt', $item->registration_id) }}" target="_blank"
                                                       class="p-1.5 w-7 h-7 bg-slate-50 hover:bg-slate-100 text-slate-700 rounded-lg border border-slate-300 font-bold text-xs inline-flex items-center justify-center transition shadow-2xs"
                                                       title="Cetak Kwitansi Rekapitulasi Global">
                                                        <i class="fa-solid fa-print"></i>
                                                    </a>

                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="py-12 text-center text-slate-400">
                                                <i class="fa-solid fa-cash-register text-4xl mb-3 text-slate-300"></i>
                                                <p class="font-bold text-slate-600">Belum ada transaksi pembayaran administrasi yang diinput.</p>
                                                <p class="text-xs text-slate-400 mt-1">Klik tombol <strong>"+ Input Pembayaran Tunai"</strong> di atas untuk menginput pembayaran baru.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- FOOTER KETERANGAN ATURAN PENGGABUNGAN GLOBAL -->
                        <div class="mt-4 pt-3 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-slate-500">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span>Menampilkan <strong>{{ $groupedPayments->count() }}</strong> data siswa terdaftar yang melakukan pembayaran.</span>
                            </div>
                            <div class="text-[11px] text-slate-400 font-medium">
                                * Transaksi berulang dari siswa yang sama otomatis di-global menjadi 1 baris akumulatif tanpa menghapus riwayat sebelumnya.
                            </div>
                        </div>

                    </div>

                </div>

            @endif

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

                    <form action="{{ route('admin.administrasi.payment.store') }}" method="POST" autocomplete="off" class="space-y-4 text-xs font-medium text-slate-700">
                        @csrf
                        
                        <!-- PENCARIAN & PEMILIHAN CALON SISWA -->
                        <div class="space-y-1.5">
                            <label class="font-bold text-slate-800 flex items-center justify-between">
                                <span>Pilih Calon Siswa (Cari Nama / No. Pendaftar) *</span>
                                <span class="text-[11px] text-emerald-600 font-semibold" id="student-count-indicator">{{ count($candidatesForPayment ?? []) }} Calon Siswa</span>
                            </label>
                            
                            <!-- INPUT FILTER LIVE DROPDOWN -->
                            <div class="relative">
                                <input type="text" id="picker-search-input" onkeyup="filterCandidateDropdown()" 
                                       placeholder="Ketik nama siswa atau nomor pendaftar untuk mencari..." 
                                       class="w-full pl-8 pr-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-medium focus:bg-white focus:outline-none focus:border-teal-500 mb-1.5">
                                <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                            </div>

                            <select name="registration_id" id="payment-student-select" required onchange="onPaymentStudentSelected(this)"
                                    class="w-full px-3 py-2.5 bg-white border border-slate-300 rounded-lg text-xs font-bold text-slate-800 focus:outline-none focus:border-teal-500">
                                <option value="" disabled selected>-- Pilih Calon Siswa --</option>
                                @foreach($candidatesForPayment ?? [] as $c)
                                    <option value="{{ $c->id }}" 
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

                        <!-- GRID 2 KOLOM: TANGGAL & BESARAN NOMINAL -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            
                            <!-- TANGGAL PEMBAYARAN -->
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Tanggal Pembayaran *</label>
                                <input type="date" name="payment_date" id="payment-input-date" required value="{{ date('Y-m-d') }}" 
                                       class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-teal-500">
                            </div>

                            <!-- JENIS / KATEGORI PEMBAYARAN -->
                            <div class="space-y-1">
                                <label class="font-bold text-slate-800">Kategori / Jenis Biaya *</label>
                                <select name="payment_type" id="payment-input-type" required class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-teal-500">
                                    <option value="Biaya Formulir Pendaftaran" selected>Biaya Formulir Pendaftaran</option>
                                    <option value="Daftar Ulang (Tahap 1)">Daftar Ulang (Tahap 1)</option>
                                    <option value="Daftar Ulang (Tahap 2)">Daftar Ulang (Tahap 2)</option>
                                    <option value="Biaya Seragam & Atribut">Biaya Seragam & Atribut</option>
                                    <option value="Biaya Praktik & Asuransi">Biaya Praktik & Asuransi</option>
                                    <option value="Infaq / Uang Gedung">Infaq / Uang Gedung</option>
                                    <option value="Pembayaran Biaya Lainnya">Pembayaran Biaya Lainnya</option>
                                </select>
                            </div>

                        </div>

                        <!-- INPUT BESARAN NOMINAL DENGAN QUICK BUTTONS -->
                        <div class="space-y-2">
                            <label class="font-bold text-slate-800 flex items-center justify-between">
                                <span>Besaran Nominal Pembayaran (Rp) *</span>
                                <span class="text-[11px] text-slate-500 font-mono" id="nominal-preview">Rp 0</span>
                            </label>
                            <input type="number" name="amount" id="payment-input-amount" required min="1000" step="1000" placeholder="Contoh: 150000"
                                   oninput="updateNominalPreview(this.value)"
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-lg text-sm font-black font-mono text-emerald-800 focus:bg-white focus:outline-none focus:border-teal-500">
                            
                            <!-- QUICK AMOUNT CHIPS -->
                            <div class="flex items-center gap-1.5 flex-wrap pt-1">
                                <span class="text-[10px] text-slate-400 font-semibold mr-1">Nominal Cepat:</span>
                                <button type="button" onclick="setQuickAmount(50000)" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono font-bold text-[11px] border border-slate-200 transition">50rb</button>
                                <button type="button" onclick="setQuickAmount(100000)" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono font-bold text-[11px] border border-slate-200 transition">100rb</button>
                                <button type="button" onclick="setQuickAmount(150000)" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono font-bold text-[11px] border border-slate-200 transition">150rb</button>
                                <button type="button" onclick="setQuickAmount(250000)" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono font-bold text-[11px] border border-slate-200 transition">250rb</button>
                                <button type="button" onclick="setQuickAmount(500000)" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono font-bold text-[11px] border border-slate-200 transition">500rb</button>
                                <button type="button" onclick="setQuickAmount(1000000)" class="px-2 py-1 rounded bg-slate-100 hover:bg-slate-200 text-slate-700 font-mono font-bold text-[11px] border border-slate-200 transition">1 Juta</button>
                            </div>
                        </div>

                        <!-- CATATAN / KETERANGAN -->
                        <div class="space-y-1">
                            <label class="font-bold text-slate-800">Catatan / Keterangan (Opsional)</label>
                            <input type="text" name="notes" placeholder="Contoh: Pembayaran tunai lunas di loket bendahara SPMB"
                                   class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs focus:bg-white focus:outline-none focus:border-teal-500">
                        </div>

                        <!-- NOTICE VERIFIKASI OTOMATIS OLEH BENDAHARA -->
                        <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-900 text-[11px] space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-emerald-800">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i> Otomatis Terverifikasi & Tersinkronisasi:
                            </div>
                            <p class="leading-relaxed">
                                Pembayaran tunai ini akan langsung <strong>otomatis terverifikasi</strong> oleh sistem atas nama Petugas Bendahara yang sedang login: <strong>{{ auth()->user()->name }}</strong>. Calon siswa dapat langsung melihat nominal dan kwitansi pembayaran di akun portal siswa mereka.
                            </p>
                        </div>

                        <!-- TOMBOL SUBMIT & BATAL -->
                        <div class="pt-3 flex items-center justify-end gap-2.5 border-t border-slate-100">
                            <button type="button" onclick="closeInputPaymentModal()" class="px-4 py-2.5 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs transition">
                                Batal
                            </button>
                            <button type="submit" class="px-6 py-2.5 bg-[#00a65a] hover:bg-[#008f4c] text-white font-black text-xs rounded-lg shadow-md transition flex items-center gap-2">
                                <i class="fa-solid fa-check-double"></i> Simpan & Otomatis Verifikasi
                            </button>
                        </div>

                    </form>

                </div>
            </div>

            <!-- ================= MODAL RINCIAN RIWAYAT PEMBAYARAN SISWA (HISTORY TRANSPARAN) ================= -->
            <div id="modal-payment-history" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4 hidden">
                <div class="bg-white rounded-2xl max-w-2xl w-full p-6 space-y-4 shadow-2xl border border-slate-200 overflow-y-auto max-h-[90vh] animate-in fade-in duration-150">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2 text-slate-900 font-bold text-base">
                                <i class="fa-solid fa-clock-rotate-left text-indigo-600"></i>
                                <h3>Riwayat Lengkap Pembayaran Calon Siswa</h3>
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">Daftar seluruh transaksi yang pernah dilakukan siswa ini</p>
                        </div>
                        <button type="button" onclick="closePaymentHistoryModal()" class="text-slate-400 hover:text-slate-700">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- RINGKASAN SISWA -->
                    <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                        <div>
                            <div class="text-[11px] text-indigo-700 font-bold uppercase">Calon Siswa</div>
                            <div class="text-base font-black text-slate-900 uppercase" id="hist-student-name">-</div>
                            <div class="text-[11px] text-slate-600 font-mono">No. Pendaftar: <strong id="hist-reg-number">-</strong></div>
                        </div>
                        <div class="text-left sm:text-right">
                            <div class="text-[11px] text-slate-500 font-bold uppercase">Total Akumulasi Pembayaran</div>
                            <div class="text-xl font-black text-emerald-700 font-mono" id="hist-total-amount">Rp 0</div>
                        </div>
                    </div>

                    <!-- TABEL RIWAYAT TRANSAKSI SATUAN -->
                    <div class="overflow-x-auto border border-slate-200 rounded-xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-700">
                                    <th class="py-2.5 px-3 text-center w-8">#</th>
                                    <th class="py-2.5 px-3">Tanggal</th>
                                    <th class="py-2.5 px-3">No. Invoice</th>
                                    <th class="py-2.5 px-3">Kategori</th>
                                    <th class="py-2.5 px-3 text-right">Nominal</th>
                                    <th class="py-2.5 px-3">Bendahara</th>
                                    <th class="py-2.5 px-3 text-center w-24">Kwitansi</th>
                                </tr>
                            </thead>
                            <tbody id="hist-tbody" class="divide-y divide-slate-100 font-medium">
                                <!-- Dynamic Rendered by Javascript -->
                            </tbody>
                        </table>
                    </div>

                    <!-- FOOTER ACTIONS -->
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 text-xs">
                        <button type="button" onclick="closePaymentHistoryModal()" class="px-4 py-2 rounded-lg border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold transition">
                            Tutup
                        </button>
                        <a id="hist-global-receipt-btn" href="#" target="_blank" class="px-5 py-2.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-black shadow transition flex items-center gap-2">
                            <i class="fa-solid fa-print"></i> Cetak Kwitansi Rekapitulasi (Global)
                        </a>
                    </div>

                </div>
            </div>

        </main>

    </div>

    <!-- ================= MODAL TAMBAH / EDIT DATA JURUSAN ================= -->
    <div id="modal-major" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100 animate-in fade-in duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-extrabold text-slate-900" id="modal-major-title">Tambah Data Jurusan</h3>
                <button type="button" onclick="closeMajorModal()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-major-action" action="{{ route('admin.sekolah.jurusan.store') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Jurusan *</label>
                    <input type="text" name="code" id="input-major-code" required placeholder="Contoh: TKR, TSM, TKJ Axioo" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Jurusan (Lengkap) *</label>
                    <input type="text" name="name" id="input-major-name" required placeholder="Contoh: Teknik Kendaraan Ringan" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kelas Industri (Opsional)</label>
                    <input type="text" name="industry_class" id="input-major-industry-class" placeholder="Contoh: Kelas Daihatsu, Axioo, Kelas Telkom, Kelas Laksana, dll" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                    <p class="text-[10px] text-slate-400 mt-1">Jika diisi, otomatis muncul di samping nama jurusan pada data calon siswa & cetak kartu.</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kuota Siswa *</label>
                        <input type="number" name="quota" id="input-major-quota" required value="100" min="1" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Status</label>
                        <select name="is_active" id="input-major-status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-semibold">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeMajorModal()" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-bold rounded-lg shadow transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL KHUSUS EDIT KELAS INDUSTRI ================= -->
    <div id="modal-industry-class" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100 animate-in fade-in duration-150">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-teal-50 flex items-center justify-center text-[#00a65a]">
                        <i class="fa-solid fa-industry"></i>
                    </div>
                    <h3 class="text-sm font-extrabold text-slate-900">Edit Kelas Industri Jurusan</h3>
                </div>
                <button type="button" onclick="closeIndustryClassModal()" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-industry-class-action" action="" method="POST" class="space-y-4 text-xs font-medium text-slate-700">
                @csrf
                <div>
                    <label class="block text-slate-500 font-bold mb-1">Nama Jurusan Terkait</label>
                    <div id="industry-modal-major-name" class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-slate-900 font-bold text-xs"></div>
                </div>
                <div>
                    <label class="block text-slate-800 font-bold mb-1">Nama / Mitra Kelas Industri *</label>
                    <input type="text" name="industry_class" id="input-industry-class-modal" placeholder="Contoh: Kelas Daihatsu, Kelas Telkom, Axioo, Kelas Laksana, dll" class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-lg text-xs font-semibold focus:bg-white focus:outline-none focus:border-teal-500">
                    <p class="text-[11px] text-slate-500 mt-1.5 leading-relaxed">
                        <i class="fa-solid fa-circle-info text-teal-600"></i> Kosongkan jika jurusan ini tidak memiliki kelas industri. Seluruh data jurusan pada <strong>Calon Siswa</strong>, <strong>Cetak Kartu F4</strong>, dan <strong>Export Excel</strong> otomatis terupdate seketika.
                    </p>
                </div>
                <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeIndustryClassModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-lg transition">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-bold rounded-lg shadow transition flex items-center gap-1.5">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ================= MODAL STATUS PENDAFTAR ================= -->
    <div id="modal-status" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full p-6 border border-slate-100">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                <h3 class="text-sm font-extrabold text-slate-900">Ubah Status Pendaftaran</h3>
                <button type="button" onclick="document.getElementById('modal-status').classList.add('hidden')" class="text-slate-400 hover:text-slate-700">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <form id="form-update-status" action="" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Calon Siswa</label>
                    <div id="modal-student-name" class="p-2.5 bg-slate-50 rounded-lg text-xs font-bold text-slate-800"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Status *</label>
                    <select name="status" id="modal-status-select" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold">
                        <option value="pending">Pending</option>
                        <option value="verified">Terverifikasi</option>
                        <option value="accepted">Diterima</option>
                        <option value="rejected">Ditolak / Perbaikan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Catatan</label>
                    <textarea name="verification_notes" id="modal-notes" rows="2" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-status').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-bold rounded-lg">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT LOGIC JAVASCRIPT LENGKAP -->
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

        function switchProfileTab(tab) {
            const tabs = ['profile', 'alamat', 'contact', 'kepsek'];
            tabs.forEach(t => {
                const btn = document.getElementById(`tab-btn-${t}`);
                const content = document.getElementById(`tab-content-${t}`);
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

        // Live Table Search Filter
        function filterTable(inputId, tableId) {
            const input = document.getElementById(inputId);
            const filter = input.value.toLowerCase();
            const table = document.getElementById(tableId);
            const tr = table.getElementsByTagName("tr");

            for (let i = 1; i < tr.length; i++) {
                let text = tr[i].textContent || tr[i].innerText;
                if (text.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }
        }

        // Copy Table to Clipboard
        function copyTable(tableId) {
            const table = document.getElementById(tableId);
            let text = "";
            for (let r of table.rows) {
                let row = [];
                for (let c of r.cells) {
                    row.push(c.innerText.trim());
                }
                text += row.join("\t") + "\n";
            }
            navigator.clipboard.writeText(text).then(() => {
                alert("Data tabel berhasil disalin ke clipboard!");
            });
        }

        // Export Table to CSV
        function exportTableToCSV(tableId, filename) {
            const table = document.getElementById(tableId);
            let csv = [];
            for (let r of table.rows) {
                let row = [];
                for (let c of r.cells) {
                    row.push('"' + c.innerText.replace(/"/g, '""').trim() + '"');
                }
                csv.push(row.join(","));
            }
            const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
            const downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }

        // Modal Data Jurusan
        function openAddMajorModal() {
            document.getElementById('modal-major-title').innerText = 'Tambah Data Jurusan';
            document.getElementById('form-major-action').action = "{{ route('admin.sekolah.jurusan.store') }}";
            document.getElementById('input-major-code').value = '';
            document.getElementById('input-major-name').value = '';
            document.getElementById('input-major-industry-class').value = '';
            document.getElementById('input-major-quota').value = '100';
            document.getElementById('input-major-status').value = '1';
            document.getElementById('modal-major').classList.remove('hidden');
        }

        function openEditMajorModal(id, code, name, industryClass, quota, status) {
            document.getElementById('modal-major-title').innerText = 'Edit Data Jurusan';
            document.getElementById('form-major-action').action = `/admin/sekolah/jurusan/${id}/update`;
            document.getElementById('input-major-code').value = code;
            document.getElementById('input-major-name').value = name;
            document.getElementById('input-major-industry-class').value = industryClass || '';
            document.getElementById('input-major-quota').value = quota;
            document.getElementById('input-major-status').value = status;
            document.getElementById('modal-major').classList.remove('hidden');
        }

        function closeMajorModal() {
            document.getElementById('modal-major').classList.add('hidden');
        }

        // Modal Khusus Edit Kelas Industri
        function openEditIndustryClassModal(id, majorName, currentIndustry) {
            document.getElementById('form-industry-class-action').action = `/admin/sekolah/jurusan/${id}/industry-class`;
            document.getElementById('industry-modal-major-name').innerText = majorName;
            document.getElementById('input-industry-class-modal').value = currentIndustry || '';
            document.getElementById('modal-industry-class').classList.remove('hidden');
        }

        function closeIndustryClassModal() {
            document.getElementById('modal-industry-class').classList.add('hidden');
        }

        // Data Pengguna (Edit, Password Update, Single Delete & Bulk Delete)
        function editUserCard(id, name, username, role) {
            document.getElementById('card-user-title').innerText = 'Edit Data & Password';
            const badge = document.getElementById('user-mode-badge');
            if (badge) {
                badge.innerText = 'Mode Edit';
                badge.className = 'px-2 py-0.5 rounded bg-blue-100 text-blue-800 text-[10px] font-bold';
            }
            document.getElementById('form-user-action').action = `/admin/sekolah/pengguna/${id}/update`;
            document.getElementById('user-input-name').value = name;
            document.getElementById('user-input-username').value = username;
            document.getElementById('user-input-role').value = role;
            
            const pwdInput = document.getElementById('user-input-password');
            pwdInput.required = false;
            pwdInput.value = '';
            pwdInput.placeholder = 'Ketik password baru jika ingin diubah';
            
            document.getElementById('label-pwd').innerText = 'Password Baru (Opsional)';
            document.getElementById('pwd-help-text').innerText = 'Kosongkan jika tidak ingin mengubah password akun ini. Masukkan password baru untuk menggantinya.';
            
            // Scroll ke form di layar mobile
            document.getElementById('form-user-action').scrollIntoView({ behavior: 'smooth', block: 'center' });
        }

        function resetUserCard() {
            document.getElementById('card-user-title').innerText = 'Tambah Data Pengguna';
            const badge = document.getElementById('user-mode-badge');
            if (badge) {
                badge.innerText = 'Mode Tambah';
                badge.className = 'px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 text-[10px] font-bold';
            }
            document.getElementById('form-user-action').action = "{{ route('admin.sekolah.pengguna.store') }}";
            document.getElementById('user-input-name').value = '';
            document.getElementById('user-input-username').value = '';
            
            const pwdInput = document.getElementById('user-input-password');
            pwdInput.value = '';
            pwdInput.required = true;
            pwdInput.placeholder = 'Kata sandi akun';
            
            document.getElementById('label-pwd').innerText = 'Password *';
            document.getElementById('user-input-role').value = 'panitia';
            document.getElementById('pwd-help-text').innerText = 'Minimal 4 karakter. Berikan password ini kepada panitia terkait untuk login.';
        }

        function confirmDeleteUser(id, name, username) {
            if (confirm(`Apakah Anda yakin ingin menghapus akun '${name}' (${username})? Akun ini tidak akan bisa login lagi.`)) {
                const form = document.getElementById('form-delete-single-user');
                form.action = `/admin/sekolah/pengguna/${id}/delete`;
                form.submit();
            }
        }

        function togglePasswordVisibility() {
            const pwdInput = document.getElementById('user-input-password');
            const eyeIcon = document.getElementById('eye-icon');
            const eyeText = document.getElementById('eye-text');
            if (pwdInput.type === 'password') {
                pwdInput.type = 'text';
                eyeIcon.className = 'fa-solid fa-eye-slash';
                eyeText.innerText = 'Sembunyikan';
            } else {
                pwdInput.type = 'password';
                eyeIcon.className = 'fa-solid fa-eye';
                eyeText.innerText = 'Lihat';
            }
        }

        function toggleSelectAllUsers(source) {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(cb => cb.checked = source.checked);
        }

        function submitBulkDeleteUsers() {
            const checked = document.querySelectorAll('.user-checkbox:checked');
            if (checked.length === 0) {
                alert('Silakan centang data pengguna yang ingin dihapus terlebih dahulu.');
                return;
            }
            if (confirm(`Apakah Anda yakin ingin menghapus ${checked.length} data pengguna terpilih?`)) {
                document.getElementById('form-bulk-delete').submit();
            }
        }

        function openStatusModal(id, regNum, name, status, notes) {
            const form = document.getElementById('form-update-status');
            form.action = `/admin/registrations/${id}/status`;
            document.getElementById('modal-student-name').innerText = `${name} (${regNum})`;
            document.getElementById('modal-status-select').value = status;
            document.getElementById('modal-notes').value = notes || '';
            document.getElementById('modal-status').classList.remove('hidden');
        }

        // ================= JAVASCRIPT FITUR DATA CALON SISWA =================
        function openAddStudentModal() {
            document.getElementById('modal-add-student').classList.remove('hidden');
        }

        function closeAddStudentModal() {
            document.getElementById('modal-add-student').classList.add('hidden');
        }

        function openEditStudentModal(id, name, username, phone, gender, school, majorId) {
            document.getElementById('form-edit-student-action').action = `/admin/spmb/calon-siswa/${id}/update`;
            document.getElementById('edit-student-name').value = name;
            document.getElementById('edit-student-username').value = username;
            document.getElementById('edit-student-phone').value = phone;
            document.getElementById('edit-student-gender').value = gender;
            document.getElementById('edit-student-school').value = school;
            document.getElementById('edit-student-major').value = majorId;
            document.getElementById('modal-edit-student').classList.remove('hidden');
        }

        function closeEditStudentModal() {
            document.getElementById('modal-edit-student').classList.add('hidden');
        }

        function confirmDeleteStudent(id, name, regNum) {
            if (confirm(`Apakah Anda yakin ingin menghapus data calon siswa '${name}' (${regNum})? Seluruh data pendaftaran dan biodata akan dihapus.`)) {
                const form = document.getElementById('form-delete-single-student');
                form.action = `/admin/spmb/calon-siswa/${id}/delete`;
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

        // ================= TOAST NOTIFICATION SYSTEM =================
        function showToastNotification(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const toast = document.createElement('div');
            toast.className = `pointer-events-auto flex items-start gap-3 p-3.5 rounded-xl shadow-xl border text-xs font-semibold transform transition-all duration-300 translate-x-full ${
                type === 'success' 
                    ? 'bg-emerald-900/95 text-white border-emerald-700 shadow-emerald-950/20' 
                    : 'bg-rose-900/95 text-white border-rose-700 shadow-rose-950/20'
            }`;

            const icon = type === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-rose-400';
            toast.innerHTML = `
                <i class="fa-solid ${icon} text-base mt-0.5"></i>
                <div class="flex-1 leading-snug">${message}</div>
                <button type="button" onclick="this.parentElement.remove()" class="text-white/60 hover:text-white ml-1">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            `;

            container.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full');
                toast.classList.add('translate-x-0');
            });

            setTimeout(() => {
                toast.classList.remove('translate-x-0');
                toast.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // ================= QUICK STATUS MODAL (SEKALI RESPON LANGSUNG) =================
        let activeQuickStudentId = null;
        let activeQuickStudentName = '';
        let activeQuickRegNum = '';

        function promptChangeStudentStatus(id, name, regNum, currentStatus) {
            activeQuickStudentId = id;
            activeQuickStudentName = name;
            activeQuickRegNum = regNum;

            document.getElementById('quick-status-student-name').innerText = name;
            document.getElementById('quick-status-reg-num').innerText = regNum;

            const iconContainer = document.getElementById('quick-status-icon-container');
            const titleEl = document.getElementById('quick-status-title');
            const badgeContainer = document.getElementById('quick-status-current-badge');
            const questionEl = document.getElementById('quick-status-question');
            const actionsEl = document.getElementById('quick-status-actions');

            if (currentStatus === 'pending') {
                titleEl.innerText = 'Verifikasi Status Pendaftaran';
                iconContainer.className = 'w-10 h-10 rounded-full flex items-center justify-center text-lg bg-emerald-100 text-[#00a65a]';
                iconContainer.innerHTML = '<i class="fa-solid fa-user-check"></i>';
                badgeContainer.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-50 text-rose-600 border border-rose-200"><span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span> Pending</span>';
                questionEl.innerHTML = `Calon siswa ini saat ini berstatus <span class="text-rose-600 font-bold">Pending</span>.<br>Apakah Anda ingin memverifikasi calon siswa ini menjadi <b>DITERIMA</b> atau <b>TIDAK</b>?`;
                actionsEl.innerHTML = `
                    <button type="button" onclick="closeQuickStatusModal()" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200 transition">Batal</button>
                    <button type="button" id="btn-quick-reject" onclick="submitQuickStatus('rejected')" class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-xmark"></i> <span>Tidak / Tolak</span>
                    </button>
                    <button type="button" id="btn-quick-submit" onclick="submitQuickStatus('accepted')" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#00a65a] hover:bg-[#008f4c] shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> <span>Diterima</span>
                    </button>
                `;
            } else if (currentStatus === 'accepted') {
                titleEl.innerText = 'Ubah Status Pendaftaran';
                iconContainer.className = 'w-10 h-10 rounded-full flex items-center justify-center text-lg bg-rose-100 text-rose-600';
                iconContainer.innerHTML = '<i class="fa-solid fa-rotate-left"></i>';
                badgeContainer.innerHTML = '<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Diterima</span>';
                questionEl.innerHTML = `Calon siswa ini sudah berstatus <span class="text-emerald-600 font-bold">Diterima</span>.<br>Apakah Anda ingin mengembalikan statusnya menjadi <b>PENDING</b>?`;
                actionsEl.innerHTML = `
                    <button type="button" onclick="closeQuickStatusModal()" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200 transition">Batal</button>
                    <button type="button" id="btn-quick-submit" onclick="submitQuickStatus('pending')" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md shadow-rose-600/20 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-rotate-left"></i> <span>Kembalikan ke Pending</span>
                    </button>
                `;
            } else {
                titleEl.innerText = 'Ubah Status Pendaftaran';
                iconContainer.className = 'w-10 h-10 rounded-full flex items-center justify-center text-lg bg-amber-100 text-amber-600';
                iconContainer.innerHTML = '<i class="fa-solid fa-circle-question"></i>';
                badgeContainer.innerHTML = `<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700 border border-slate-200">${currentStatus}</span>`;
                questionEl.innerHTML = `Pilih status baru untuk calon siswa <b>${name}</b>:`;
                actionsEl.innerHTML = `
                    <button type="button" onclick="closeQuickStatusModal()" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100 border border-slate-200 transition">Batal</button>
                    <button type="button" onclick="submitQuickStatus('pending')" class="px-3.5 py-2 rounded-xl text-xs font-bold text-amber-700 bg-amber-50 hover:bg-amber-100 border border-amber-200 transition">Jadikan Pending</button>
                    <button type="button" id="btn-quick-submit" onclick="submitQuickStatus('accepted')" class="px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#00a65a] hover:bg-[#008f4c] shadow-md shadow-emerald-600/20 transition flex items-center gap-1.5">
                        <i class="fa-solid fa-check"></i> <span>Jadikan Diterima</span>
                    </button>
                `;
            }

            document.getElementById('modal-quick-status').classList.remove('hidden');
        }

        function closeQuickStatusModal() {
            const modal = document.getElementById('modal-quick-status');
            if (modal) modal.classList.add('hidden');
        }

        function submitQuickStatus(newStatus) {
            if (!activeQuickStudentId) return;

            const btn = document.getElementById(newStatus === 'accepted' ? 'btn-quick-submit' : 'btn-quick-reject') || document.getElementById('btn-quick-submit');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>Memproses...</span>';
            }

            fetch(`/admin/registrations/${activeQuickStudentId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    status: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                closeQuickStatusModal();
                if (data.success) {
                    const cell = document.getElementById(`status-cell-${activeQuickStudentId}`);
                    if (cell) {
                        const safeName = activeQuickStudentName.replace(/'/g, "\\'");
                        if (newStatus === 'accepted') {
                            cell.innerHTML = `
                                <button type="button" 
                                        onclick="promptChangeStudentStatus('${activeQuickStudentId}', '${safeName}', '${activeQuickRegNum}', 'accepted')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 transition cursor-pointer shadow-2xs hover:shadow active:scale-95 group"
                                        title="Klik untuk mengubah status (Diterima -> Pending)">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> 
                                    <span>Diterima</span>
                                </button>
                            `;
                        } else if (newStatus === 'pending') {
                            cell.innerHTML = `
                                <button type="button" 
                                        onclick="promptChangeStudentStatus('${activeQuickStudentId}', '${safeName}', '${activeQuickRegNum}', 'pending')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50/90 hover:bg-rose-100 text-rose-600 border border-rose-200 transition cursor-pointer shadow-2xs hover:shadow active:scale-95 group"
                                        title="Klik untuk verifikasi calon siswa ini (Pending -> Diterima)">
                                    <span class="w-2 h-2 rounded-full bg-rose-500 shadow-sm animate-pulse"></span> 
                                    <span>Pending</span>
                                </button>
                            `;
                        } else {
                            cell.innerHTML = `
                                <button type="button" 
                                        onclick="promptChangeStudentStatus('${activeQuickStudentId}', '${safeName}', '${activeQuickRegNum}', '${newStatus}')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 transition cursor-pointer shadow-2xs hover:shadow active:scale-95"
                                        title="Klik untuk mengubah status">
                                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> 
                                    <span>Ditolak</span>
                                </button>
                            `;
                        }
                    }

                    showToastNotification(`Status calon siswa <b>${activeQuickStudentName}</b> berhasil diubah menjadi <b>${newStatus === 'accepted' ? 'Diterima' : (newStatus === 'pending' ? 'Pending' : 'Ditolak')}</b>!`, 'success');
                } else {
                    showToastNotification(data.message || 'Gagal mengubah status pendaftaran.', 'error');
                }
            })
            .catch(err => {
                closeQuickStatusModal();
                showToastNotification('Terjadi kesalahan saat memproses status.', 'error');
            });
        }

        // ================= CLIENT-SIDE PAGINATION (MAKSIMAL 10 DATA PERSIS GAMBAR) =================
        const studentPageSize = 10;
        let studentCurrentPage = 1;
        let studentFilteredRows = [];

        function initStudentPagination() {
            const table = document.getElementById('table-calon-siswa');
            if (!table) return;

            const allRows = Array.from(table.querySelectorAll('tbody tr.student-row'));
            if (allRows.length === 0) return;

            const query = (document.getElementById('search-student-input')?.value || '').toLowerCase().trim();

            studentFilteredRows = allRows.filter(row => {
                if (!query) return true;
                return row.innerText.toLowerCase().includes(query);
            });

            // Sembunyikan seluruh baris terlebih dahulu
            allRows.forEach(row => row.style.display = 'none');

            const total = studentFilteredRows.length;
            const totalPages = Math.ceil(total / studentPageSize) || 1;
            if (studentCurrentPage > totalPages) studentCurrentPage = totalPages;
            if (studentCurrentPage < 1) studentCurrentPage = 1;

            const startIdx = (studentCurrentPage - 1) * studentPageSize;
            const endIdx = Math.min(startIdx + studentPageSize, total);

            // Tampilkan maksimal 10 baris pada halaman aktif
            for (let i = startIdx; i < endIdx; i++) {
                if (studentFilteredRows[i]) {
                    studentFilteredRows[i].style.display = '';
                    const noCell = studentFilteredRows[i].querySelector('.student-row-no');
                    if (noCell) noCell.innerText = i + 1;
                }
            }

            // Info text persis gambar: "Showing 1 to 7 of 7 entries" atau "Showing 1 to 10 of 15 entries"
            const infoEl = document.getElementById('student-table-info');
            if (infoEl) {
                if (total === 0) {
                    infoEl.innerText = 'Showing 0 to 0 of 0 entries';
                } else {
                    const startDisplay = startIdx + 1;
                    const endDisplay = endIdx;
                    if (query && allRows.length !== total) {
                        infoEl.innerText = `Showing ${startDisplay} to ${endDisplay} of ${total} entries (filtered from ${allRows.length} total entries)`;
                    } else {
                        infoEl.innerText = `Showing ${startDisplay} to ${endDisplay} of ${total} entries`;
                    }
                }
            }

            // Tombol Navigasi Persis Screenshot: Previous  [ 1 ]  [ 2 ]  Next
            const paginationEl = document.getElementById('student-table-pagination');
            if (paginationEl) {
                let html = '';

                // Previous Button
                if (studentCurrentPage === 1) {
                    html += `<span class="text-slate-400 cursor-not-allowed select-none px-2.5 py-1 text-xs">Previous</span>`;
                } else {
                    html += `<button type="button" onclick="goToStudentPage(${studentCurrentPage - 1})" class="text-slate-700 hover:text-slate-900 select-none px-2.5 py-1 text-xs font-medium cursor-pointer transition">Previous</button>`;
                }

                // Page Number Boxes
                for (let p = 1; p <= totalPages; p++) {
                    if (p === studentCurrentPage) {
                        html += `<button type="button" class="border border-slate-300 bg-gradient-to-b from-white to-slate-200 text-slate-800 font-bold px-3 py-1 rounded text-xs shadow-xs mx-0.5">${p}</button>`;
                    } else {
                        html += `<button type="button" onclick="goToStudentPage(${p})" class="border border-transparent hover:border-slate-300 hover:bg-slate-100 text-slate-600 px-3 py-1 rounded text-xs mx-0.5 transition cursor-pointer">${p}</button>`;
                    }
                }

                // Next Button
                if (studentCurrentPage === totalPages || total === 0) {
                    html += `<span class="text-slate-400 cursor-not-allowed select-none px-2.5 py-1 text-xs">Next</span>`;
                } else {
                    html += `<button type="button" onclick="goToStudentPage(${studentCurrentPage + 1})" class="text-slate-700 hover:text-slate-900 select-none px-2.5 py-1 text-xs font-medium cursor-pointer transition">Next</button>`;
                }

                paginationEl.innerHTML = html;
            }
        }

        function goToStudentPage(page) {
            studentCurrentPage = page;
            initStudentPagination();
        }

        function filterStudentTable() {
            studentCurrentPage = 1;
            initStudentPagination();
        }

        // ================= JAVASCRIPT FITUR ADMINISTRASI PEMBAYARAN KASIR / BENDAHARA =================
        function openInputPaymentModal(regId = null, name = '', number = '') {
            const modal = document.getElementById('modal-input-payment');
            if (!modal) return;

            const selectEl = document.getElementById('payment-student-select');
            const searchInput = document.getElementById('picker-search-input');
            const amountInput = document.getElementById('payment-input-amount');
            const dateInput = document.getElementById('payment-input-date');
            
            if (searchInput) searchInput.value = '';
            filterCandidateDropdown();

            if (regId && selectEl) {
                selectEl.value = regId;
                onPaymentStudentSelected(selectEl);
            } else if (selectEl && selectEl.options.length > 1) {
                // Jangan paksa jika belum dipilih
                selectEl.selectedIndex = 0;
                document.getElementById('payment-student-preview')?.classList.add('hidden');
            }

            if (amountInput && !regId) {
                amountInput.value = '';
                updateNominalPreview(0);
            }

            modal.classList.remove('hidden');
        }

        function closeInputPaymentModal() {
            const modal = document.getElementById('modal-input-payment');
            if (modal) modal.classList.add('hidden');
        }

        function onPaymentStudentSelected(selectEl) {
            const opt = selectEl.options[selectEl.selectedIndex];
            const preview = document.getElementById('payment-student-preview');
            if (!opt || !opt.value) {
                if (preview) preview.classList.add('hidden');
                return;
            }

            const name = opt.getAttribute('data-name') || '-';
            const number = opt.getAttribute('data-number') || '-';
            const major = opt.getAttribute('data-major') || '-';
            const industry = opt.getAttribute('data-industry') || '';
            const school = opt.getAttribute('data-school') || '-';

            document.getElementById('prev-name').innerText = name;
            document.getElementById('prev-number').innerText = number;
            document.getElementById('prev-major').innerText = major + (industry ? ` (${industry})` : '');
            document.getElementById('prev-school').innerText = school;

            if (preview) preview.classList.remove('hidden');
        }

        function filterCandidateDropdown() {
            const input = document.getElementById('picker-search-input');
            const select = document.getElementById('payment-student-select');
            if (!input || !select) return;

            const filter = input.value.toLowerCase().trim();
            let countVisible = 0;

            for (let i = 0; i < select.options.length; i++) {
                const opt = select.options[i];
                if (!opt.value) continue; // skip placeholder
                
                const text = opt.text.toLowerCase();
                if (text.includes(filter)) {
                    opt.style.display = '';
                    countVisible++;
                } else {
                    opt.style.display = 'none';
                }
            }

            const indicator = document.getElementById('student-count-indicator');
            if (indicator) {
                indicator.innerText = `${countVisible} Calon Siswa Ditemukan`;
            }
        }

        function updateNominalPreview(val) {
            const preview = document.getElementById('nominal-preview');
            if (!preview) return;
            const num = parseInt(val) || 0;
            preview.innerText = 'Rp ' + num.toLocaleString('id-ID');
        }

        function setQuickAmount(val) {
            const input = document.getElementById('payment-input-amount');
            if (input) {
                input.value = val;
                updateNominalPreview(val);
                input.focus();
            }
        }

        function openPaymentHistoryModal(studentName, regNumber, totalFormatted, historyList, globalReceiptUrl) {
            const modal = document.getElementById('modal-payment-history');
            if (!modal) return;

            document.getElementById('hist-student-name').innerText = studentName;
            document.getElementById('hist-reg-number').innerText = regNumber;
            document.getElementById('hist-total-amount').innerText = totalFormatted;

            const btnGlobal = document.getElementById('hist-global-receipt-btn');
            if (btnGlobal && globalReceiptUrl) {
                btnGlobal.href = globalReceiptUrl;
            }

            const tbody = document.getElementById('hist-tbody');
            if (tbody) {
                tbody.innerHTML = '';
                if (!historyList || historyList.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="7" class="py-6 text-center text-slate-400">Belum ada riwayat transaksi pembayaran.</td></tr>`;
                } else {
                    historyList.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.className = 'hover:bg-slate-50 transition';
                        tr.innerHTML = `
                            <td class="py-3 px-3 text-center text-slate-500 font-semibold">${index + 1}</td>
                            <td class="py-3 px-3 font-mono text-slate-700 font-semibold">${item.payment_date}</td>
                            <td class="py-3 px-3 font-mono font-bold text-blue-700">${item.invoice_number}</td>
                            <td class="py-3 px-3 font-bold text-slate-900">${item.payment_type}</td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-emerald-700">${item.amount_formatted}</td>
                            <td class="py-3 px-3 text-slate-600">${item.confirmed_by}</td>
                            <td class="py-3 px-3 text-center">
                                <a href="${item.print_url}" target="_blank" 
                                   class="px-2.5 py-1 bg-white hover:bg-slate-100 border border-slate-300 rounded text-slate-700 text-xs font-bold inline-flex items-center gap-1 shadow-2xs"
                                   title="Cetak Kwitansi Satuan Ini">
                                    <i class="fa-solid fa-print text-emerald-600"></i> Cetak
                                </a>
                            </td>
                        `;
                        tbody.appendChild(tr);
                    });
                }
            }

            modal.classList.remove('hidden');
        }

        function closePaymentHistoryModal() {
            const modal = document.getElementById('modal-payment-history');
            if (modal) modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            initStudentPagination();
        });
    </script>

    <!-- TOAST NOTIFICATION CONTAINER (MODERN FLOATING NOTIFICATIONS) -->
    <div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full"></div>

</body>
</html>
