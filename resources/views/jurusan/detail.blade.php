<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $major->name }} - SPMB SMK Karya Nugraha Boyolali</title>
    
    <!-- Meta SEO & Open Graph -->
    <meta name="description" content="{{ $major->tagline ?? $major->description }}">
    <meta property="og:title" content="{{ $major->name }} - SPMB SMK Karya Nugraha Boyolali">
    <meta property="og:description" content="{{ $major->description }}">
    
    <!-- Google Fonts: Plus Jakarta Sans & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN & FontAwesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    },
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-blue-600 selection:text-white min-h-screen flex flex-col">

    <!-- ================= 1. TOP ANNOUNCEMENT BAR ================= -->
    <div class="bg-gradient-to-r from-blue-900 via-slate-900 to-indigo-950 text-white text-[11px] py-2 px-4 shadow-sm border-b border-white/10">
        <div class="max-w-7xl mx-auto flex flex-wrap justify-between items-center gap-2">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-extrabold border border-emerald-400/30">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    SPMB {{ $academicYear->name ?? '2027/2028' }} BUKA
                </span>
                <span class="hidden sm:inline text-slate-300">|</span>
                <span class="text-slate-300">Pusat Keunggulan (SMK PK) &bull; Terakreditasi "A" &bull; NPSN: 20308458</span>
            </div>
            <div class="flex items-center gap-4 text-slate-300">
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['hotline_panitia'] ?? '085290944090') }}" target="_blank" class="hover:text-emerald-300 transition flex items-center gap-1 font-semibold">
                    <i class="fa-brands fa-whatsapp text-emerald-400"></i>
                    <span>Hotline: {{ $settings['hotline_panitia'] ?? '0852-9094-4090' }}</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ================= 2. NAVBAR RESMI ================= -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                
                <!-- BRAND LOGO -->
                <a href="{{ route('home') }}" class="flex items-center gap-3.5 group">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center p-1.5 shadow-xs group-hover:scale-105 transition-transform">
                        @if(!empty($settings['school_logo']))
                            <img src="{{ asset($settings['school_logo']) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        @else
                            <i class="fa-solid fa-graduation-cap text-2xl text-blue-600"></i>
                        @endif
                    </div>
                    <div>
                        <div class="font-black text-base tracking-tight text-slate-900 group-hover:text-blue-600 transition-colors">
                            SMK KARYA NUGRAHA
                        </div>
                        <div class="text-[11px] font-bold tracking-wider text-slate-500 uppercase">
                            BOYOLALI &bull; SPMB ONLINE
                        </div>
                    </div>
                </a>

                <!-- DESKTOP NAV -->
                <nav class="hidden md:flex items-center gap-6 text-xs font-bold text-slate-600">
                    <a href="{{ route('home') }}" class="hover:text-blue-600 transition flex items-center gap-1">
                        <i class="fa-solid fa-house"></i> Beranda
                    </a>
                    
                    <!-- DROPDOWN JURUSAN LAINNYA -->
                    <div class="relative group">
                        <button type="button" class="text-blue-600 flex items-center gap-1.5 py-2 hover:text-blue-700 transition">
                            <i class="fa-solid fa-list-check"></i>
                            <span>Pilih Jurusan</span>
                            <i class="fa-solid fa-chevron-down text-[10px] group-hover:rotate-180 transition-transform"></i>
                        </button>
                        <div class="absolute left-0 top-full mt-1 w-72 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 hidden group-hover:block z-50 animate-in fade-in zoom-in duration-150">
                            <div class="px-3 py-1.5 text-[10px] font-black uppercase tracking-wider text-slate-400">
                                7 Program Keahlian Unggulan
                            </div>
                            @foreach($allMajors as $otherMajor)
                                <a href="{{ route('jurusan.detail', $otherMajor->slug ?: $otherMajor->id) }}" class="flex items-center gap-2.5 px-3.5 py-2 hover:bg-blue-50 hover:text-blue-600 transition text-xs font-bold text-slate-700">
                                    <span class="w-6 h-6 rounded-md bg-blue-100 text-blue-700 flex items-center justify-center text-[10px] font-black">
                                        {{ $otherMajor->code }}
                                    </span>
                                    <div class="truncate">
                                        <div class="truncate">{{ $otherMajor->name }}</div>
                                        @if($otherMajor->industry_class)
                                            <div class="text-[10px] text-amber-600 font-semibold">{{ $otherMajor->industry_class }}</div>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <a href="{{ route('home') }}#alur" class="hover:text-blue-600 transition">Alur Pendaftaran</a>
                    <a href="{{ route('home') }}#syarat" class="hover:text-blue-600 transition">Syarat Berkas</a>
                    <a href="{{ route('home') }}#kontak" class="hover:text-blue-600 transition">Kontak Panitia</a>
                </nav>

                <!-- RIGHT CTA BUTTONS -->
                <div class="hidden sm:flex items-center gap-3">
                    <a href="{{ route('home') }}#login-box" class="px-3.5 py-2 text-xs font-bold text-slate-700 hover:text-blue-600 bg-slate-100 hover:bg-blue-50 rounded-xl transition border border-slate-200">
                        Masuk Akun
                    </a>
                    <a href="{{ route('daftar') }}?major_id={{ $major->id }}" class="px-4 py-2.5 text-xs font-bold text-white bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 rounded-xl shadow-md shadow-blue-500/20 hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-file-signature"></i> Daftar Sekarang
                    </a>
                </div>

                <!-- MOBILE MENU BUTTON -->
                <button type="button" onclick="document.getElementById('mobile-nav').classList.toggle('hidden')" class="md:hidden p-2 text-slate-600 hover:text-blue-600">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

            </div>

            <!-- MOBILE NAV DROPDOWN -->
            <div id="mobile-nav" class="hidden md:hidden py-4 border-t border-slate-100 space-y-2 text-xs font-bold">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 text-slate-700">Beranda</a>
                <div class="px-3 py-1 text-[10px] font-black uppercase text-slate-400">Pilihan Jurusan Lain:</div>
                <div class="grid grid-cols-2 gap-1.5 px-3 pb-2">
                    @foreach($allMajors as $otherMajor)
                        <a href="{{ route('jurusan.detail', $otherMajor->slug ?: $otherMajor->id) }}" class="p-2 bg-slate-50 rounded-lg text-slate-800 hover:bg-blue-50 hover:text-blue-600 truncate">
                            <span class="font-black text-blue-600">{{ $otherMajor->code }}</span>: {{ $otherMajor->name }}
                        </a>
                    @endforeach
                </div>
                <div class="pt-2 flex gap-2">
                    <a href="{{ route('daftar') }}?major_id={{ $major->id }}" class="flex-1 text-center py-2.5 bg-blue-600 text-white rounded-xl shadow">Daftar Sekarang</a>
                    <a href="{{ route('home') }}#login-box" class="flex-1 text-center py-2.5 bg-slate-100 text-slate-700 rounded-xl">Masuk Akun</a>
                </div>
            </div>

        </div>
    </header>

    <!-- ================= 3. BREADCRUMB & HERO BANNER JURUSAN ================= -->
    <section class="relative bg-gradient-to-b from-slate-900 via-slate-900 to-indigo-950 text-white overflow-hidden py-12 md:py-16">
        
        <!-- DECORATIVE BACKGROUND BLUR & GRID -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#1e293b15_1px,transparent_1px),linear-gradient(to_bottom,#1e293b15_1px,transparent_1px)] bg-[size:32px_32px]"></div>
        <div class="absolute -top-32 -right-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-32 -left-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- BREADCRUMB -->
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-6">
                <a href="{{ route('home') }}" class="hover:text-white transition flex items-center gap-1">
                    <i class="fa-solid fa-house text-[10px]"></i> Beranda
                </a>
                <span>/</span>
                <span class="text-slate-400">Program Keahlian</span>
                <span>/</span>
                <span class="text-blue-400 font-bold">{{ $major->code }}</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                
                <!-- HERO LEFT: COPYWRITING & BADGES -->
                <div class="lg:col-span-7 space-y-5">
                    
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="px-3.5 py-1 rounded-xl text-xs font-black bg-blue-500/20 text-blue-300 border border-blue-400/30 uppercase tracking-wider">
                            Kode: {{ $major->code }}
                        </span>
                        @if($major->industry_class)
                            <span class="px-3.5 py-1 rounded-xl text-xs font-black bg-amber-500/20 text-amber-300 border border-amber-400/30 flex items-center gap-1.5 shadow-sm">
                                <i class="fa-solid fa-handshake text-amber-400"></i> Mitra: {{ $major->industry_class }}
                            </span>
                        @endif
                        <span class="px-3 py-1 rounded-xl text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 flex items-center gap-1">
                            <i class="fa-solid fa-circle-check text-emerald-400 text-[10px]"></i> Status: Pendaftaran Terbuka
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white leading-tight tracking-tight">
                        {{ $major->name }}
                    </h1>

                    <p class="text-base sm:text-lg text-slate-300 font-medium leading-relaxed">
                        {{ $major->tagline ?? $major->description }}
                    </p>

                    <!-- QUICK METRICS PILLS -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-3 text-center backdrop-blur-xs">
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Daya Tampung</div>
                            <div class="text-lg font-black text-white mt-0.5">{{ $major->quota }} Siswa</div>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-3 text-center backdrop-blur-xs">
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Sisa Kuota</div>
                            <div class="text-lg font-black text-emerald-400 mt-0.5">{{ $major->remaining_quota }} Kursi</div>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-3 text-center backdrop-blur-xs">
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Standar</div>
                            <div class="text-lg font-black text-amber-400 mt-0.5">Kelas Industri</div>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-2xl p-3 text-center backdrop-blur-xs">
                            <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Penyaluran</div>
                            <div class="text-lg font-black text-blue-400 mt-0.5">BKK Mitra</div>
                        </div>
                    </div>

                    <!-- CTA BUTTONS -->
                    <div class="flex flex-wrap items-center gap-3.5 pt-3">
                        <a href="{{ route('daftar') }}?major_id={{ $major->id }}" class="px-7 py-3.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-cyan-600 hover:from-blue-500 hover:to-indigo-500 text-white rounded-2xl font-black text-sm shadow-xl shadow-blue-600/30 hover:shadow-2xl transition transform hover:-translate-y-1 flex items-center gap-2.5">
                            <i class="fa-solid fa-file-pen text-base"></i> Daftar ke Jurusan Ini
                        </a>
                        <a href="#galeri-video" class="px-5 py-3.5 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-bold text-sm border border-white/20 transition flex items-center gap-2">
                            <i class="fa-solid fa-play text-xs text-blue-400"></i> Video & Foto Praktikum
                        </a>
                    </div>

                </div>

                <!-- HERO RIGHT: BANNER CARD / HIGHLIGHT PREVIEW -->
                <div class="lg:col-span-5">
                    <div class="relative bg-gradient-to-tr from-slate-800 to-slate-800/80 p-3 rounded-3xl border border-white/15 shadow-2xl shadow-black/50 overflow-hidden group">
                        @if(!empty($major->banner_image))
                            <img src="{{ asset($major->banner_image) }}" alt="Banner {{ $major->name }}" class="w-full h-72 sm:h-80 object-cover rounded-2xl group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-72 sm:h-80 bg-gradient-to-br from-blue-900/60 via-slate-800 to-indigo-950 rounded-2xl flex flex-col items-center justify-center p-6 text-center border border-white/10">
                                <div class="w-20 h-20 rounded-2xl bg-blue-600/20 border border-blue-400/30 flex items-center justify-center text-3xl text-blue-400 mb-4 shadow-inner">
                                    <i class="fa-solid {{ $major->icon ?: 'fa-graduation-cap' }}"></i>
                                </div>
                                <h3 class="text-xl font-black text-white">{{ $major->name }}</h3>
                                <p class="text-xs text-slate-400 mt-2 line-clamp-3 leading-relaxed">
                                    {{ $major->description }}
                                </p>
                            </div>
                        @endif

                        <div class="absolute bottom-6 left-6 right-6 bg-slate-900/90 backdrop-blur-md p-3.5 rounded-xl border border-white/20 flex items-center justify-between text-xs font-bold text-white shadow-lg">
                            <div class="flex items-center gap-2.5">
                                <i class="fa-solid fa-award text-amber-400 text-lg"></i>
                                <div>
                                    <div class="font-black">SMK Pusat Keunggulan</div>
                                    <div class="text-[10px] text-slate-400 font-normal">Kurikulum Link & Match Industri</div>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded bg-blue-600 text-white text-[11px] font-black">
                                FOKUS PK
                            </span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </section>

    <!-- ================= 4. VIDEO PROFIL & PRAKTIKUM JURUSAN ================= -->
    <section id="galeri-video" class="py-16 md:py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-video text-blue-600"></i> Video Profil Jurusan
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
                    Saksikan Praktikum & Suasana Belajar
                </h2>
                <p class="mt-2 text-sm sm:text-base text-slate-600">
                    Lihat langsung bagaimana siswa-siswi {{ $major->name }} mempraktikkan keahlian mereka menggunakan fasilitas berstandar industri modern.
                </p>
            </div>

            <!-- VIDEO CONTAINER -->
            <div class="max-w-4xl mx-auto">
                <div class="relative bg-slate-950 rounded-3xl p-2 sm:p-3 shadow-2xl border border-slate-200 overflow-hidden">
                    
                    @if(!empty($major->video_embed_url))
                        <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-slate-900">
                            <iframe class="w-full h-full" 
                                    src="{{ $major->video_embed_url }}" 
                                    title="Video Profil {{ $major->name }}" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                                    allowfullscreen>
                            </iframe>
                        </div>
                    @else
                        <!-- FALLBACK JIKA VIDEO BELUM DI-UPLOAD ADMIN -->
                        <div class="relative w-full aspect-video rounded-2xl overflow-hidden bg-gradient-to-br from-slate-900 to-indigo-950 flex flex-col items-center justify-center p-6 text-center text-white">
                            <div class="w-16 h-16 rounded-full bg-blue-600/30 border border-blue-400/40 flex items-center justify-center text-blue-400 text-2xl mb-3 shadow-lg">
                                <i class="fa-solid fa-play ml-1"></i>
                            </div>
                            <h4 class="text-lg font-black text-white">Video Jurusan Siap Ditayangkan</h4>
                            <p class="text-xs text-slate-400 mt-1 max-w-md">
                                Video profil resmi jurusan {{ $major->name }} dapat ditambahkan dan diperbarui langsung melalui panel Administrator SPMB.
                            </p>
                        </div>
                    @endif

                    <!-- VIDEO FOOTER CAPTION -->
                    <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-white">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600/20 border border-blue-500/30 flex items-center justify-center text-blue-400 text-base">
                                <i class="fa-solid fa-chalkboard-user"></i>
                            </div>
                            <div>
                                <div class="text-xs text-slate-400 font-bold uppercase tracking-wider">Kepala Program Keahlian:</div>
                                <div class="text-sm font-bold text-white">{{ $major->head_of_major ?? 'Tim Guru Produktif ' . $major->code }}</div>
                            </div>
                        </div>
                        <a href="{{ route('daftar') }}?major_id={{ $major->id }}" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl text-xs font-bold transition flex items-center justify-center gap-2 shadow-md">
                            <i class="fa-solid fa-user-plus"></i> Gabung Jurusan Ini
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </section>

    <!-- ================= 5. KEUNGGULAN-KEUNGGULAN JURUSAN ================= -->
    <section class="py-16 md:py-20 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-14">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-star text-emerald-600"></i> Mengapa Memilih Jurusan Ini
                </span>
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
                    Keunggulan Utama Program Keahlian
                </h2>
                <p class="mt-2 text-sm sm:text-base text-slate-600">
                    Nilai tambah dan keuntungan eksklusif yang akan Anda dapatkan selama menempuh pendidikan di {{ $major->name }}.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($major->effective_advantages as $adv)
                    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs hover:shadow-xl hover:border-blue-400 hover:-translate-y-1 transition duration-300 flex flex-col justify-between">
                        <div>
                            <div class="w-12 h-12 rounded-xl bg-blue-50 border border-blue-200 flex items-center justify-center text-blue-600 text-xl mb-4">
                                <i class="fa-solid {{ $adv['icon'] ?? 'fa-award' }}"></i>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 leading-snug">
                                {{ $adv['title'] ?? 'Keunggulan Jurusan' }}
                            </h3>
                            <p class="text-xs text-slate-600 mt-2.5 leading-relaxed">
                                {{ $adv['description'] ?? '-' }}
                            </p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-slate-100 flex items-center gap-1 text-[11px] font-bold text-blue-600">
                            <i class="fa-solid fa-check-circle text-emerald-500"></i> Standar Kejuruan PK
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ================= 6. GALERI FOTO PRAKTIKUM SISWA ================= -->
    <section class="py-16 md:py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-12">
                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-indigo-50 text-indigo-700 border border-indigo-200 uppercase tracking-wider mb-2">
                        <i class="fa-solid fa-camera text-indigo-600"></i> Dokumentasi Nyata
                    </span>
                    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">
                        Foto-Foto Praktikum & Fasilitas Siswa
                    </h2>
                    <p class="mt-2 text-sm text-slate-600 max-w-2xl">
                        Dokumentasi kegiatan belajar mengajar, praktik mesin, laboratorium komputer, dan teaching factory di {{ $major->name }}.
                    </p>
                </div>
                <div class="text-xs font-bold text-slate-500">
                    Total: <strong class="text-slate-900">{{ $major->gallery->count() }} Foto Dokumentasi</strong>
                </div>
            </div>

            @if($major->gallery->count() > 0)
                <!-- GRID FOTO PRAKTIKUM DENGAN MODAL ZOOM -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($major->gallery as $item)
                        <div class="group relative bg-slate-100 rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-xl transition duration-300">
                            <div class="w-full h-64 overflow-hidden bg-slate-200">
                                <img src="{{ asset($item->image_path) }}" 
                                     alt="{{ $item->title }}" 
                                     onclick="openImageModal('{{ asset($item->image_path) }}', '{{ addslashes($item->title) }}')"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 cursor-pointer">
                            </div>
                            <div class="p-4 bg-white">
                                <h4 class="text-sm font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                    {{ $item->title ?: 'Praktikum Siswa ' . $major->code }}
                                </h4>
                                @if($item->description)
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2">
                                        {{ $item->description }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- SHOWCASE JIKA BELUM ADA FOTO DIUNGGAH OLEH ADMIN -->
                <div class="p-10 rounded-3xl bg-gradient-to-br from-slate-50 to-blue-50/50 border-2 border-dashed border-blue-200 text-center max-w-3xl mx-auto">
                    <div class="w-16 h-16 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center text-3xl mx-auto mb-4">
                        <i class="fa-solid fa-images"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900">Galeri Foto Praktikum Siswa</h3>
                    <p class="text-xs text-slate-600 mt-2 max-w-lg mx-auto leading-relaxed">
                        Foto-foto praktikum bengkel dan laboratorium untuk jurusan <strong>{{ $major->name }}</strong> dapat diunggah secara mudah dan langsung melalui menu Administrator SPMB.
                    </p>
                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <span class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-wrench text-blue-600"></i> Alat Praktik Lengkap
                        </span>
                        <span class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-shield-halved text-emerald-600"></i> Standar K3 Industri
                        </span>
                        <span class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-2xs">
                            <i class="fa-solid fa-user-tie text-indigo-600"></i> Instruktur Tersertifikasi
                        </span>
                    </div>
                </div>
            @endif

        </div>
    </section>

    <!-- ================= 7. KOMPETENSI KEAHLIAN & PROSPEK KARIR ================= -->
    <section class="py-16 md:py-20 bg-slate-50 border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-start">
                
                <!-- KOLOM KIRI: KOMPETENSI YANG DIPELAJARI -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-graduation-cap"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">Kompetensi yang Dipelajari</h3>
                            <p class="text-xs text-slate-500">Keterampilan teknis (hard skills) yang dikuasai siswa</p>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach($major->effective_competencies as $comp)
                            <div class="flex items-start gap-3 p-3 rounded-xl bg-slate-50 hover:bg-emerald-50/50 border border-slate-100 hover:border-emerald-200 transition">
                                <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs flex-shrink-0 mt-0.5">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                                <span class="text-xs sm:text-sm font-semibold text-slate-800 leading-snug">
                                    {{ $comp }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- KOLOM KANAN: PROSPEK KARIR & PEKERJAAN MASA DEPAN -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 border border-blue-200 flex items-center justify-center text-xl">
                            <i class="fa-solid fa-briefcase"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">Peluang & Prospek Karir</h3>
                            <p class="text-xs text-slate-500">Profesi dan bidang pekerjaan setelah lulus dari jurusan ini</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($major->effective_career_opportunities as $career)
                            <div class="flex items-center gap-2.5 p-3 rounded-xl bg-slate-50 hover:bg-blue-50/60 border border-slate-100 hover:border-blue-200 transition">
                                <span class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center text-xs flex-shrink-0">
                                    <i class="fa-solid fa-user-check"></i>
                                </span>
                                <span class="text-xs font-bold text-slate-800">
                                    {{ $career }}
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <!-- FASILITAS KHUSUS BENGKEL / LAB -->
                    <div class="mt-6 pt-5 border-t border-slate-100">
                        <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <i class="fa-solid fa-flask-vial text-indigo-600"></i> Fasilitas Praktik & Bengkel:
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($major->effective_facilities as $fac)
                                <span class="px-3 py-1 rounded-lg bg-slate-100 border border-slate-200 text-xs font-semibold text-slate-700">
                                    &bull; {{ $fac }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- ================= 8. JELAJAHI JURUSAN LAINNYA ================= -->
    <section class="py-16 md:py-20 bg-white border-b border-slate-200/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="text-center max-w-3xl mx-auto mb-12">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-slate-100 text-slate-700 border border-slate-200 uppercase tracking-wider mb-2">
                    <i class="fa-solid fa-compass text-blue-600"></i> Pilihan Menarik Lainnya
                </span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
                    Jelajahi Program Keahlian Lain di SMK KNBI
                </h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($allMajors as $other)
                    <div class="bg-slate-50 hover:bg-white rounded-2xl p-5 border border-slate-200/80 hover:border-blue-400 hover:shadow-lg transition duration-300 flex flex-col justify-between group">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-blue-100 text-blue-700 border border-blue-200">
                                    {{ $other->code }}
                                </span>
                                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                    Kuota: {{ $other->quota }}
                                </span>
                            </div>

                            <h4 class="text-base font-bold text-slate-900 group-hover:text-blue-600 transition-colors">
                                {{ $other->name }}
                            </h4>

                            @if($other->industry_class)
                                <div class="mt-1.5 text-xs text-amber-700 font-semibold flex items-center gap-1">
                                    <i class="fa-solid fa-handshake"></i> {{ $other->industry_class }}
                                </div>
                            @endif

                            <p class="mt-2 text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                {{ $other->description }}
                            </p>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-200/60 flex items-center justify-between">
                            <span class="text-[11px] font-medium text-slate-500">Status: Terbuka</span>
                            <a href="{{ route('jurusan.detail', $other->slug ?: $other->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                                Lihat Detail <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    <!-- ================= 9. BOTTOM CTA BANNER ================= -->
    <section class="py-16 bg-gradient-to-r from-blue-900 via-indigo-900 to-slate-900 text-white relative overflow-hidden">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10 space-y-6">
            
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-xs font-bold text-blue-200">
                <i class="fa-solid fa-graduation-cap text-amber-400"></i> SPMB SMK KARYA NUGRAHA BOYOLALI {{ $academicYear->name ?? '2027/2028' }}
            </div>

            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight">
                Siap Menjadi Ahli di Bidang <span class="text-cyan-400">{{ $major->code }}</span>?
            </h2>

            <p class="text-base text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Pendaftaran siswa baru dapat dilakukan secara online kapan saja dan di mana saja. Segera amankan kuota Anda sekarang sebelum batas penerimaan terpenuhi.
            </p>

            <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                <a href="{{ route('daftar') }}?major_id={{ $major->id }}" class="px-8 py-4 bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white font-black text-sm rounded-2xl shadow-xl shadow-cyan-500/20 hover:shadow-2xl transition transform hover:-translate-y-1 flex items-center gap-2.5">
                    <i class="fa-solid fa-file-signature text-base"></i> Isi Formulir Pendaftaran Sekarang
                </a>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['hotline_panitia'] ?? '085290944090') }}?text=Halo%20Panitia%20SPMB,%20saya%20ingin%20bertanya%20mengenai%20jurusan%20{{ urlencode($major->name) }}" target="_blank" class="px-6 py-4 bg-white/10 hover:bg-white/20 text-white font-bold text-sm rounded-2xl border border-white/20 transition flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-400 text-lg"></i> Konsultasi via WhatsApp
                </a>
            </div>

        </div>
    </section>

    <!-- ================= 10. FOOTER ================= -->
    <footer class="mt-auto bg-slate-950 text-slate-400 text-xs py-8 border-t border-slate-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div>
                <p class="font-bold text-white text-sm">SMK KARYA NUGRAHA BOYOLALI</p>
                <p class="mt-0.5 text-slate-500">{{ $settings['school_address'] ?? 'Jl. Tentara Pelajar No. 1, Boyolali, Jawa Tengah 57314' }}</p>
            </div>
            <div class="text-slate-500">
                &copy; {{ date('Y') }} SPMB KNBI. Hak Cipta Dilindungi Undang-Undang.
            </div>
        </div>
    </footer>

    <!-- MODAL LIGHTBOX FOTO ZOOM -->
    <div id="image-modal" class="fixed inset-0 z-50 bg-black/90 backdrop-blur-md hidden items-center justify-center p-4">
        <button type="button" onclick="closeImageModal()" class="absolute top-5 right-5 text-white/80 hover:text-white text-3xl font-bold cursor-pointer">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div class="max-w-4xl max-h-[90vh] flex flex-col items-center">
            <img id="modal-image-src" src="" alt="" class="max-w-full max-h-[80vh] object-contain rounded-2xl shadow-2xl">
            <p id="modal-image-caption" class="text-white text-sm font-bold mt-3 text-center"></p>
        </div>
    </div>

    <script>
        function openImageModal(src, caption) {
            document.getElementById('modal-image-src').src = src;
            document.getElementById('modal-image-caption').textContent = caption;
            document.getElementById('image-modal').classList.remove('hidden');
            document.getElementById('image-modal').classList.add('flex');
        }

        function closeImageModal() {
            document.getElementById('image-modal').classList.add('hidden');
            document.getElementById('image-modal').classList.remove('flex');
        }
    </script>

</body>
</html>
