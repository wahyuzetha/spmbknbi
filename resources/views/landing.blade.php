<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMB KNBI | SMK Karya Nugraha Boyolali - Tahun Pelajaran {{ $academicYear->name ?? '2027/2028' }}</title>
    
    <!-- Meta SEO -->
    <meta name="description" content="Sistem Penerimaan Murid Baru (SPMB) Online SMK Karya Nugraha Boyolali (SMK PK - Pusat Keunggulan). Daftarkan diri Anda sekarang!">
    <meta name="theme-color" content="#0066FF">

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS CDN for instant rendering -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            200: '#bfdbfe',
                            300: '#93c5fd',
                            400: '#60a5fa',
                            500: '#3b82f6',
                            600: '#0066ff',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        },
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(0, 102, 255, 0.08) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(6, 182, 212, 0.08) 0%, transparent 40%);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col antialiased selection:bg-blue-600 selection:text-white hero-gradient">

    <!-- ================= 1. PENGUMUMAN ATAS (ALERT BANNER) ================= -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-blue-600 text-white text-xs md:text-sm py-2 px-4 shadow-sm">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-between gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold bg-white/20 text-white tracking-wide uppercase">
                    <i class="fa-solid fa-bullhorn mr-1.5 animate-pulse text-amber-300"></i> Info Pendaftaran
                </span>
                <span class="font-medium text-slate-100">
                    Penerimaan Siswa Baru Tahun Pelajaran <strong>{{ $academicYear->name ?? '2027/2028' }}</strong> Telah Dibuka!
                </span>
            </div>
            <div class="hidden sm:flex items-center gap-4 text-xs font-semibold">
                <span class="flex items-center gap-1.5 text-blue-100">
                    <i class="fa-regular fa-calendar-check text-amber-300"></i> Batas Akhir: 11 Agustus 2027
                </span>
                <span class="text-white/40">|</span>
                <a href="https://wa.me/6285290944090" target="_blank" class="hover:text-amber-300 transition flex items-center gap-1">
                    <i class="fa-brands fa-whatsapp text-emerald-300"></i> Hotline: 0852-9094-4090
                </a>
                <span class="text-white/40">|</span>
                <button type="button" onclick="openStaffModal()" class="hover:text-amber-300 transition flex items-center gap-1 text-amber-300 font-bold">
                    <i class="fa-solid fa-user-shield"></i> Login Petugas (Admin / Panitia)
                </button>
            </div>
        </div>
    </div>

    <!-- ================= 2. NAVBAR MODERN (STICKY GLASSMORPHISM) ================= -->
    <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-200/80 transition-all shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- BRANDING LOGO & NAMA SEKOLAH -->
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-blue-600 to-cyan-500 p-0.5 shadow-md shadow-blue-500/20 group-hover:scale-105 transition-transform flex items-center justify-center overflow-hidden">
                        @if(!empty($schoolLogo))
                            <img src="{{ asset($schoolLogo) }}" alt="Logo Sekolah" class="w-full h-full object-contain p-1 rounded-[10px] bg-white">
                        @else
                            <div class="w-full h-full bg-white rounded-[10px] flex items-center justify-center text-blue-600 font-extrabold text-xl">
                                <i class="fa-solid fa-graduation-cap"></i>
                            </div>
                        @endif
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-lg font-black tracking-tight bg-gradient-to-r from-blue-700 to-cyan-600 bg-clip-text text-transparent">
                                SPMB KNBI
                            </span>
                            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-md bg-blue-50 text-blue-700 border border-blue-200/60">
                                SMK PK
                            </span>
                        </div>
                        <p class="text-xs font-semibold text-slate-600 tracking-wide uppercase">
                            SMK Karya Nugraha Boyolali
                        </p>
                    </div>
                </a>

                <!-- DESKTOP NAV LINKS -->
                <nav class="hidden md:flex items-center gap-1">
                    <a href="#beranda" class="px-3.5 py-2 text-sm font-semibold text-blue-600 hover:text-blue-700 rounded-lg hover:bg-blue-50/60 transition">
                        Beranda
                    </a>
                    <a href="#jurusan" class="px-3.5 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 rounded-lg hover:bg-slate-50 transition">
                        Jurusan Peminatan
                    </a>
                    <a href="#alur" class="px-3.5 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 rounded-lg hover:bg-slate-50 transition">
                        Alur Pendaftaran
                    </a>
                    <a href="#syarat" class="px-3.5 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 rounded-lg hover:bg-slate-50 transition">
                        Syarat Berkas
                    </a>
                    <a href="#kontak" class="px-3.5 py-2 text-sm font-semibold text-slate-600 hover:text-blue-600 rounded-lg hover:bg-slate-50 transition">
                        Kontak
                    </a>
                </nav>

                <!-- ACTION BUTTONS -->
                <div class="hidden sm:flex items-center gap-2.5">
                    
                    <!-- DROPDOWN LOGIN PETUGAS (ADMIN & PANITIA) -->
                    <div class="relative group">
                        <button type="button" class="px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:text-indigo-600 bg-slate-100 hover:bg-indigo-50 rounded-xl transition border border-slate-200/80 flex items-center gap-1.5">
                            <i class="fa-solid fa-shield-halved text-indigo-600"></i>
                            <span>Login Petugas</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400 group-hover:rotate-180 transition-transform"></i>
                        </button>
                        <div class="absolute right-0 top-full mt-1.5 w-52 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 hidden group-hover:block z-50 animate-in fade-in zoom-in duration-150">
                            <div class="px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-slate-400">
                                Akses Internal Sekolah
                            </div>
                            <a href="javascript:void(0)" onclick="openStaffModal('admin')" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-[10px]">
                                    <i class="fa-solid fa-user-shield"></i>
                                </span>
                                <div>
                                    <div>Administrator</div>
                                    <div class="text-[10px] text-slate-400 font-normal">Super Admin SPMB</div>
                                </div>
                            </a>
                            <a href="javascript:void(0)" onclick="openStaffModal('panitia')" class="flex items-center gap-2.5 px-3.5 py-2 text-xs font-bold text-slate-700 hover:bg-indigo-50 hover:text-indigo-700 transition">
                                <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center text-[10px]">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                </span>
                                <div>
                                    <div>Panitia SPMB</div>
                                    <div class="text-[10px] text-slate-400 font-normal">Verifikator Berkas</div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <a href="#login-box" class="px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 rounded-xl transition border border-slate-200/80 flex items-center gap-1.5">
                        <i class="fa-solid fa-right-to-bracket text-blue-600"></i> Masuk Akun
                    </a>
                    
                    <a href="{{ route('daftar') }}" class="px-4 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl shadow-md shadow-blue-600/25 hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-user-plus"></i> Daftar Sekarang
                    </a>
                </div>

                <!-- MOBILE HAMBURGER -->
                <button type="button" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden p-2 rounded-xl text-slate-600 hover:text-blue-600 hover:bg-slate-100">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
            </div>

            <!-- MOBILE DROPDOWN MENU -->
            <div id="mobile-menu" class="hidden md:hidden py-4 border-t border-slate-100 flex flex-col gap-2">
                <a href="#beranda" class="px-3 py-2 text-sm font-semibold text-blue-600 rounded-lg hover:bg-blue-50">Beranda</a>
                <a href="#jurusan" class="px-3 py-2 text-sm font-semibold text-slate-700 rounded-lg hover:bg-slate-50">Jurusan Peminatan</a>
                <a href="#alur" class="px-3 py-2 text-sm font-semibold text-slate-700 rounded-lg hover:bg-slate-50">Alur Pendaftaran</a>
                <a href="#syarat" class="px-3 py-2 text-sm font-semibold text-slate-700 rounded-lg hover:bg-slate-50">Syarat Berkas</a>
                <a href="#kontak" class="px-3 py-2 text-sm font-semibold text-slate-700 rounded-lg hover:bg-slate-50">Kontak Panitia</a>
                <div class="pt-2 flex flex-col gap-2">
                    <a href="#login-box" class="w-full text-center py-2.5 text-xs font-bold text-slate-700 bg-slate-100 rounded-xl">Masuk Siswa</a>
                    <a href="{{ route('daftar') }}" class="w-full text-center py-2.5 text-xs font-bold text-white bg-blue-600 rounded-xl shadow">Daftar Sekarang</a>
                    <div class="pt-2 border-t border-slate-100 flex flex-col gap-1.5">
                        <button type="button" onclick="openStaffModal('admin')" class="w-full text-left px-3 py-2 text-xs font-bold text-blue-800 bg-blue-50 rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-user-shield text-blue-600"></i> Login Administrator SPMB
                        </button>
                        <button type="button" onclick="openStaffModal('panitia')" class="w-full text-left px-3 py-2 text-xs font-bold text-indigo-800 bg-indigo-50 rounded-xl flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-check text-indigo-600"></i> Login Panitia SPMB
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ================= 3. HERO SECTION ================= -->
    <section id="beranda" class="pt-8 pb-16 md:pt-14 md:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                
                <!-- HERO LEFT: COPYWRITING & CTAs -->
                <div class="lg:col-span-7 space-y-6">
                    
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-50 border border-blue-200 text-blue-700 text-xs font-bold shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-blue-600 animate-ping"></span>
                        <i class="fa-solid fa-award text-amber-500"></i> SMK PUSAT KEUNGGULAN (SMK PK)
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-[1.15] tracking-tight">
                        Siapkan Masa Depan Gemilang di <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-600 bg-clip-text text-transparent">SMK Karya Nugraha</span> Boyolali
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 font-normal leading-relaxed max-w-2xl">
                        Aplikasi resmi <strong>Sistem Penerimaan Murid Baru (SPMB)</strong> Tahun Pelajaran <strong>{{ $academicYear->name ?? '2027/2028' }}</strong>. Nikmati kemudahan pendaftaran secara online, transparan, dan terintegrasi langsung dengan kelas industri nasional.
                    </p>

                    <!-- CTA BUTTONS -->
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        <a href="{{ route('daftar') }}" class="px-7 py-3.5 text-base font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-2xl shadow-xl shadow-blue-600/30 hover:shadow-2xl hover:shadow-blue-600/40 transition transform hover:-translate-y-1 flex items-center gap-3">
                            <i class="fa-solid fa-file-pen text-lg"></i> Isi Formulir Pendaftaran
                        </a>
                        <a href="#alur" class="px-6 py-3.5 text-base font-bold text-slate-700 hover:text-blue-600 bg-white hover:bg-slate-50 border border-slate-200/90 rounded-2xl shadow-sm hover:shadow transition flex items-center gap-2.5">
                            <i class="fa-solid fa-circle-info text-blue-600"></i> Pelajari Alur
                        </a>
                    </div>

                    <!-- HIGHLIGHT STATS -->
                    <div class="grid grid-cols-3 gap-4 pt-6 border-t border-slate-200/80">
                        <div class="bg-white/80 p-3.5 rounded-2xl border border-slate-200/60 shadow-sm">
                            <div class="text-2xl lg:text-3xl font-extrabold text-blue-600">7</div>
                            <div class="text-xs font-semibold text-slate-600 mt-0.5">Program Keahlian</div>
                        </div>
                        <div class="bg-white/80 p-3.5 rounded-2xl border border-slate-200/60 shadow-sm">
                            <div class="text-2xl lg:text-3xl font-extrabold text-indigo-600">500+</div>
                            <div class="text-xs font-semibold text-slate-600 mt-0.5">Total Kuota Siswa</div>
                        </div>
                        <div class="bg-white/80 p-3.5 rounded-2xl border border-slate-200/60 shadow-sm">
                            <div class="text-2xl lg:text-3xl font-extrabold text-emerald-600">100%</div>
                            <div class="text-xs font-semibold text-slate-600 mt-0.5">Siap Kerja Industri</div>
                        </div>
                    </div>

                </div>

                <!-- HERO RIGHT: NEO-GLASSMORPHISM LOGIN BOX (PORTAL RESMI SPMB) -->
                <div class="lg:col-span-5" id="login-box">
                    <div class="glass-card rounded-3xl p-6 sm:p-8 shadow-2xl shadow-blue-900/10 border border-white relative overflow-hidden">
                        
                        <div class="absolute -top-12 -right-12 w-32 h-32 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>

                        <!-- ROLE SWITCHER TABS PADA PORTAL HERO (2 PORTAL: CALON SISWA & PANITIA SPMB) -->
                        <div class="flex items-center justify-center p-1 bg-slate-100/90 rounded-2xl mb-5 text-xs font-bold">
                            <button type="button" onclick="switchLoginRole('murid')" id="tab-murid" class="flex-1 py-2 px-3 rounded-xl bg-white text-blue-700 shadow-sm transition text-center">
                                <i class="fa-solid fa-user-graduate mr-1"></i> Calon Siswa
                            </button>
                            <button type="button" onclick="switchLoginRole('panitia')" id="tab-panitia" class="flex-1 py-2 px-3 rounded-xl text-slate-600 hover:text-slate-900 transition text-center">
                                <i class="fa-solid fa-users-gear mr-1"></i> Panitia SPMB
                            </button>
                        </div>

                        <!-- KEPALA CARD -->
                        <div class="text-center mb-5">
                            <div id="role-icon-box" class="inline-flex p-3 rounded-2xl bg-blue-50 text-blue-600 text-2xl mb-2.5 shadow-inner">
                                <i class="fa-solid fa-id-card-clip" id="role-icon"></i>
                            </div>
                            <h3 class="text-xl font-extrabold text-slate-900" id="role-title">Portal Calon Siswa</h3>
                            <p class="text-xs text-slate-500 mt-0.5 font-medium" id="role-subtitle">
                                Masuk menggunakan No. HP atau NISN pendaftaran Anda
                            </p>
                        </div>

                        <!-- FORM LOGIN INTERAKTIF -->
                        <form action="{{ route('login.post') }}" method="POST" class="space-y-3.5">
                            @csrf
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1" id="label-identity">
                                    No. WhatsApp / NISN *
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400" id="icon-identity">
                                        <i class="fa-solid fa-phone"></i>
                                    </span>
                                    <input type="text" name="login_identity" id="input-identity" placeholder="Contoh: 085861512412" required
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                                    Kata Sandi (Password) *
                                </label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                        <i class="fa-solid fa-lock"></i>
                                    </span>
                                    <input type="password" name="password" id="input-password" placeholder="Masukkan kata sandi" required
                                           class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition">
                                </div>
                            </div>

                            <button type="submit" id="btn-login-submit" class="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-md shadow-blue-600/25 hover:shadow-lg transition flex items-center justify-center gap-2 text-xs sm:text-sm">
                                <span>Masuk Aplikasi</span> <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        </form>

                        <div class="mt-5 pt-4 border-t border-slate-200/70 text-center">
                            <p class="text-xs text-slate-500 font-medium">
                                Belum mendaftar sebagai calon siswa baru?
                            </p>
                            <a href="{{ route('daftar') }}" class="inline-block mt-1 text-xs font-bold text-blue-600 hover:text-blue-700 hover:underline">
                                Buat Akun Pendaftaran Baru &rarr;
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= 4. JURUSAN & KELAS INDUSTRI (7 PROGRAM KEAHLIAN) ================= -->
    <section id="jurusan" class="py-16 bg-white border-y border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-block text-xs font-bold text-blue-600 tracking-wider uppercase mb-2 bg-blue-50 px-3 py-1 rounded-full border border-blue-200/70">
                    Program Pilihan Masa Depan
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">
                    7 Jurusan Unggulan Kelas Industri
                </h2>
                <p class="mt-3 text-base text-slate-600 font-normal">
                    Setiap jurusan di SMK Karya Nugraha Boyolali terhubung langsung dengan industri ternama skala nasional dan kurikulum berbasis kompetensi dunia kerja.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($majors as $major)
                    <div class="bg-slate-50 hover:bg-white rounded-2xl p-6 border border-slate-200/80 hover:border-blue-400 hover:shadow-xl hover:shadow-blue-500/10 transition-all duration-300 group flex flex-col justify-between">
                        <div>
                            <!-- HEADER CARD JURUSAN -->
                            <div class="flex items-center justify-between mb-4">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-blue-100/70 text-blue-700 border border-blue-200">
                                    {{ $major->code }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                                    <i class="fa-solid fa-users text-[10px]"></i> Kuota: {{ $major->quota }}
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                {{ $major->name }}
                            </h3>

                            @if($major->industry_class)
                                <div class="mt-2 inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 border border-amber-200/80 text-amber-800 text-xs font-bold">
                                    <i class="fa-solid fa-handshake text-amber-600"></i> Mitra: {{ $major->industry_class }}
                                </div>
                            @endif

                            <p class="mt-3 text-xs text-slate-600 leading-relaxed font-normal">
                                {{ $major->description }}
                            </p>
                        </div>

                        <div class="mt-5 pt-4 border-t border-slate-200/60 flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500">Status Peminatan:</span>
                            <span class="text-xs font-bold text-blue-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-check text-emerald-500 text-[10px]"></i> Terbuka
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ================= 5. TIMELINE ALUR PENDAFTARAN ================= -->
    <section id="alur" class="py-16 md:py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-block text-xs font-bold text-blue-600 tracking-wider uppercase mb-2 bg-blue-100/60 px-3 py-1 rounded-full">
                    Langkah Mudah
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight sm:text-4xl">
                    Alur Proses Pendaftaran Siswa Baru
                </h2>
                <p class="mt-3 text-base text-slate-600">
                    Ikuti 5 langkah sederhana berikut untuk menyelesaikan proses pendaftaran SPMB Anda.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 lg:gap-6 relative">
                
                <!-- STEP 1 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center relative group hover:-translate-y-1 transition">
                    <div class="w-12 h-12 mx-auto rounded-full bg-blue-600 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-blue-500/20 mb-3">
                        1
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Daftar Akun</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Isi form registrasi awal menggunakan No. WhatsApp & NISN Anda.
                    </p>
                </div>

                <!-- STEP 2 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center relative group hover:-translate-y-1 transition">
                    <div class="w-12 h-12 mx-auto rounded-full bg-indigo-600 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-indigo-500/20 mb-3">
                        2
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Lengkapi Biodata</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Login ke dashboard dan lengkapi data diri, orang tua, serta sekolah asal.
                    </p>
                </div>

                <!-- STEP 3 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center relative group hover:-translate-y-1 transition">
                    <div class="w-12 h-12 mx-auto rounded-full bg-cyan-600 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-cyan-500/20 mb-3">
                        3
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Upload Berkas</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Unggah scan berkas KK, Akta Kelahiran, dan Ijazah/SKL Anda.
                    </p>
                </div>

                <!-- STEP 4 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center relative group hover:-translate-y-1 transition">
                    <div class="w-12 h-12 mx-auto rounded-full bg-amber-500 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-amber-500/20 mb-3">
                        4
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Verifikasi Panitia</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Panitia memvalidasi berkas dan menerbitkan status pendaftaran.
                    </p>
                </div>

                <!-- STEP 5 -->
                <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm text-center relative group hover:-translate-y-1 transition">
                    <div class="w-12 h-12 mx-auto rounded-full bg-emerald-600 text-white font-extrabold text-lg flex items-center justify-center shadow-md shadow-emerald-500/20 mb-3">
                        5
                    </div>
                    <h4 class="font-bold text-slate-900 text-sm">Cetak Bukti</h4>
                    <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                        Unduh & cetak Kartu Bukti Pendaftaran untuk daftar ulang ke sekolah.
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= 6. SYARAT BERKAS & PEMBERITAHUAN WHATSAPP ================= -->
    <section id="syarat" class="py-16 bg-white border-t border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                <!-- DOKUMEN PERSYARATAN -->
                <div class="lg:col-span-6 space-y-6">
                    <div>
                        <span class="text-xs font-bold text-blue-600 uppercase tracking-wider">Persyaratan Utama</span>
                        <h3 class="text-2xl font-extrabold text-slate-900 mt-1">Dokumen yang Perlu Disiapkan</h3>
                        <p class="text-sm text-slate-600 mt-2 font-normal">
                            Pastikan berkas telah dipindai (scan) atau difoto dengan jelas dalam format PDF atau JPG/PNG:
                        </p>
                    </div>

                    <ul class="space-y-3.5">
                        <li class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>
                            <div>
                                <h5 class="text-sm font-bold text-slate-900">Surat Keterangan Lulus (SKL) / Ijazah</h5>
                                <p class="text-xs text-slate-500 mt-0.5">Ijazah SMP/MTs sederajat atau SKL jika ijazah belum terbit.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>
                            <div>
                                <h5 class="text-sm font-bold text-slate-900">Kartu Keluarga (KK) & Akta Kelahiran</h5>
                                <p class="text-xs text-slate-500 mt-0.5">Salinan jelas Kartu Keluarga dan Akta Kelahiran calon siswa.</p>
                            </div>
                        </li>
                        <li class="flex items-start gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                            <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs mt-0.5">
                                <i class="fa-solid fa-check"></i>
                            </span>
                            <div>
                                <h5 class="text-sm font-bold text-slate-900">Scan Buku Raport & Pas Foto</h5>
                                <p class="text-xs text-slate-500 mt-0.5">Raport semester terakhir dan Pas Foto berwarna ukuran 3x4.</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- PEMBERITAHUAN WA PENTING -->
                <div class="lg:col-span-6 flex flex-col justify-center">
                    <div class="bg-amber-50/80 border border-amber-300/80 rounded-2xl p-6 sm:p-7 shadow-sm">
                        <div class="flex items-center gap-3 text-amber-800 font-extrabold text-base mb-3">
                            <i class="fa-solid fa-triangle-exclamation text-amber-600 text-xl"></i>
                            Pemberitahuan Wajib No. WhatsApp
                        </div>
                        <p class="text-xs sm:text-sm text-slate-700 leading-relaxed">
                            Untuk calon siswa baru pada saat mendaftar dan mengisi formulir pendaftaran <strong>diwajibkan menggunakan nomor WhatsApp yang ASLI dan AKTIF</strong> karena Panitia SPMB akan mengirimkan notifikasi kelulusan dan konfirmasi langsung ke nomor tersebut.
                        </p>

                        <div class="mt-4 pt-4 border-t border-amber-200 grid grid-cols-2 gap-3 text-xs font-medium">
                            <div class="bg-white p-3 rounded-xl border border-emerald-300">
                                <span class="text-emerald-700 font-bold block mb-1">
                                    <i class="fa-solid fa-check-circle"></i> Contoh Benar:
                                </span>
                                <code class="text-slate-800 font-bold">085861512412</code>
                            </div>
                            <div class="bg-white p-3 rounded-xl border border-rose-300">
                                <span class="text-rose-700 font-bold block mb-1">
                                    <i class="fa-solid fa-times-circle"></i> Contoh Salah:
                                </span>
                                <code class="text-slate-500 line-through">6285861512412</code>
                            </div>
                        </div>

                        @php
                            $helpdeskWa = preg_replace('/[^0-9]/', '', $globalSettings['hotline_panitia'] ?? '085290944090');
                            if(str_starts_with($helpdeskWa, '0')) {
                                $helpdeskWa = '62' . substr($helpdeskWa, 1);
                            }
                            $hotlineWaText = \App\Models\SystemSetting::formatWaHotlineMessage($globalSettings['wa_hotline_template'] ?? null);
                        @endphp
                        <div class="mt-5">
                            <a href="https://wa.me/{{ $helpdeskWa }}?text={{ urlencode($hotlineWaText) }}" target="_blank" class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow transition text-xs sm:text-sm">
                                <i class="fa-brands fa-whatsapp text-lg"></i> Hubungi WhatsApp Helpdesk Panitia
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- ================= 7. FOOTER ================= -->
    <footer id="kontak" class="bg-slate-900 text-slate-300 pt-12 pb-8 border-t border-slate-800 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 pb-10 border-b border-slate-800">
                
                <!-- KOLOM 1: IDENTITAS -->
                <div>
                    <div class="flex items-center gap-2.5 text-white font-extrabold text-lg mb-3">
                        @if(!empty($schoolLogo))
                            <img src="{{ asset($schoolLogo) }}" alt="Logo" class="w-7 h-7 object-contain rounded bg-white/10 p-0.5">
                        @else
                            <i class="fa-solid fa-school text-blue-400"></i>
                        @endif
                        <span>{{ $globalSettings['school_name'] ?? 'SMK KARYA NUGRAHA BOYOLALI' }}</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed font-normal">
                        Sekolah Menengah Kejuruan Pusat Keunggulan (SMK PK) dengan kurikulum industri, fasilitas modern, dan lulusan yang siap bersaing di tingkat nasional maupun internasional.
                    </p>
                    @php
                        $footerWa = preg_replace('/[^0-9]/', '', $globalSettings['hotline_panitia'] ?? '085290944090');
                        if(str_starts_with($footerWa, '0')) {
                            $footerWa = '62' . substr($footerWa, 1);
                        }
                    @endphp
                    <div class="mt-4 flex items-center gap-3">
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-blue-600 text-white flex items-center justify-center transition text-xs"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-pink-600 text-white flex items-center justify-center transition text-xs"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-red-600 text-white flex items-center justify-center transition text-xs"><i class="fa-brands fa-youtube"></i></a>
                        <a href="https://wa.me/{{ $footerWa }}" target="_blank" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white flex items-center justify-center transition text-xs"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- KOLOM 2: JURUSAN CEPAT -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Jurusan Unggulan</h4>
                    <ul class="space-y-1.5 text-xs text-slate-400 font-medium">
                        <li>• Teknik Kendaraan Ringan (Kelas Daihatsu)</li>
                        <li>• Teknik Body Otomotif (Kelas Laksana)</li>
                        <li>• Teknik Sepeda Motor (Kelas Yamaha)</li>
                        <li>• Teknik Komputer & Jaringan (Kelas Axioo)</li>
                        <li>• Teknik Komputer dan Jaringan (Kelas Telkom)</li>
                        <li>• Layanan Perbankan Syariah (Kelas BSI)</li>
                    </ul>
                </div>

                <!-- KOLOM 3: KONTAK PANITIA -->
                <div>
                    <h4 class="text-sm font-bold text-white uppercase tracking-wider mb-3">Sekretariat SPMB</h4>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot text-blue-400 mt-0.5"></i>
                            <span>Jl. Pandanaran / Tentara Pelajar, Boyolali, Jawa Tengah</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-phone text-emerald-400"></i>
                            <span>WhatsApp: 0852-9094-4090</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="fa-solid fa-envelope text-amber-400"></i>
                            <span>Email: smkknbi@gmail.com</span>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="pt-6 text-center text-xs text-slate-500 font-medium">
                Copyright &copy; {{ date('Y') }} <strong>SMK KARYA NUGRAHA BOYOLALI</strong>. All rights reserved.
            </div>
        </div>
    </footer>

    <!-- ================= MODAL LOGIN KHUSUS PETUGAS (ADMIN & PANITIA) ================= -->
    <div id="modal-staff" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden animate-in fade-in duration-200">
        <div class="bg-white rounded-3xl shadow-2xl max-w-sm w-full p-6 sm:p-7 border border-slate-100 relative">
            
            <button type="button" onclick="closeStaffModal()" class="absolute top-5 right-5 text-slate-400 hover:text-slate-700">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>

            <!-- LOGO & JUDUL -->
            <div class="text-center mb-5">
                <div class="w-14 h-14 mx-auto mb-2 flex items-center justify-center overflow-hidden">
                    @if(!empty($schoolLogo))
                        <img src="{{ asset($schoolLogo) }}" alt="Logo Sekolah" class="max-w-full max-h-full object-contain">
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
                <h3 class="text-base font-extrabold text-slate-900" id="staff-modal-title">Portal Petugas SPMB</h3>
                <p class="text-xs text-slate-500" id="staff-modal-sub">Login Petugas: Panitia, Bendahara & Administrator</p>
            </div>

            <!-- FORM LOGIN PETUGAS -->
            <form action="{{ route('login.post') }}" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Username / Email Petugas</label>
                    <input type="text" name="login_identity" id="modal-staff-identity" placeholder="Masukkan username atau email petugas" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-blue-600">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kata Sandi (Password)</label>
                    <input type="password" name="password" id="modal-staff-pwd" placeholder="Kata sandi akun petugas" required
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:bg-white focus:border-blue-600">
                </div>

                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow transition">
                    Masuk ke Dashboard
                </button>
            </form>

        </div>
    </div>

    <!-- JAVASCRIPT LOGIC UNTUK ROLE SWITCHER & MODAL -->
    <script>
        let currentRole = 'murid';

        function switchLoginRole(role) {
            currentRole = role;
            const tabMurid = document.getElementById('tab-murid');
            const tabPanitia = document.getElementById('tab-panitia');
            const roleTitle = document.getElementById('role-title');
            const roleSubtitle = document.getElementById('role-subtitle');
            const roleIcon = document.getElementById('role-icon');
            const roleIconBox = document.getElementById('role-icon-box');
            const labelIdentity = document.getElementById('label-identity');
            const inputIdentity = document.getElementById('input-identity');

            if (role === 'panitia') {
                tabMurid.className = 'flex-1 py-2 px-3 rounded-xl text-slate-600 hover:text-slate-900 transition text-center';
                tabPanitia.className = 'flex-1 py-2 px-3 rounded-xl bg-white text-indigo-700 shadow-sm transition text-center';
                roleTitle.innerText = 'Portal Panitia SPMB';
                roleSubtitle.innerText = 'Login petugas Panitia, Bendahara & Administrator SPMB';
                roleIcon.className = 'fa-solid fa-users-gear';
                roleIconBox.className = 'inline-flex p-3 rounded-2xl bg-indigo-50 text-indigo-600 text-2xl mb-2.5 shadow-inner';
                labelIdentity.innerText = 'Email / Username Petugas *';
                inputIdentity.placeholder = 'Masukkan username atau email petugas';
            } else {
                tabPanitia.className = 'flex-1 py-2 px-3 rounded-xl text-slate-600 hover:text-slate-900 transition text-center';
                tabMurid.className = 'flex-1 py-2 px-3 rounded-xl bg-white text-blue-700 shadow-sm transition text-center';
                roleTitle.innerText = 'Portal Calon Siswa';
                roleSubtitle.innerText = 'Masuk menggunakan No. HP atau NISN pendaftaran Anda';
                roleIcon.className = 'fa-solid fa-id-card-clip';
                roleIconBox.className = 'inline-flex p-3 rounded-2xl bg-blue-50 text-blue-600 text-2xl mb-2.5 shadow-inner';
                labelIdentity.innerText = 'No. WhatsApp / NISN *';
                inputIdentity.placeholder = 'Contoh: 085861512412';
            }
        }

        function openStaffModal() {
            document.getElementById('modal-staff').classList.remove('hidden');
        }

        function closeStaffModal() {
            document.getElementById('modal-staff').classList.add('hidden');
        }
    </script>

</body>
</html>
