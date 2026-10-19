<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Aplikasi SPMB | SMK Karya Nugraha Boyolali</title>

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
<body class="min-h-screen bg-gradient-to-tr from-slate-900 via-blue-950 to-indigo-900 flex items-center justify-center p-4">

    <div class="max-w-md w-full">
        
        <div class="mb-4 text-center">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-300 hover:text-white bg-white/10 px-3.5 py-1.5 rounded-full transition">
                <i class="fa-solid fa-arrow-left"></i> Beranda Utama
            </a>
        </div>

        <div class="bg-white rounded-3xl p-7 sm:p-8 shadow-2xl border border-slate-100">
            
            <!-- LOGO SEKOLAH -->
            <div class="w-20 h-20 mx-auto mb-3 flex items-center justify-center overflow-hidden">
                @if(!empty($schoolLogo))
                    <img src="{{ asset($schoolLogo) }}" alt="Logo Sekolah" class="max-w-full max-h-full object-contain">
                @else
                    <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-md">
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

            <!-- TABS PILIHAN ROLE (2 PORTAL: CALON SISWA & PANITIA SPMB) -->
            <div class="flex items-center p-1 bg-slate-100 rounded-xl mb-5 text-xs font-bold">
                <button type="button" onclick="setLoginType('murid')" id="tab-type-murid" class="flex-1 py-2 rounded-lg bg-white text-blue-700 shadow transition text-center">
                    <i class="fa-solid fa-user-graduate mr-1"></i> Calon Siswa
                </button>
                <button type="button" onclick="setLoginType('panitia')" id="tab-type-panitia" class="flex-1 py-2 rounded-lg text-slate-600 hover:text-slate-900 transition text-center">
                    <i class="fa-solid fa-users-gear mr-1"></i> Panitia SPMB
                </button>
            </div>

            <div class="text-center mb-5">
                <h2 class="text-xl font-extrabold text-slate-900" id="login-title">Portal Masuk SPMB</h2>
                <p class="text-xs text-slate-500 mt-0.5 font-medium" id="login-sub">SMK KARYA NUGRAHA BOYOLALI</p>
            </div>

            @if(session('error'))
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-semibold">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- NOTIFIKASI KHUSUS PANITIA / PETUGAS SPMB -->
            <div id="panitia-account-info" class="hidden mb-4 p-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs">
                <div class="flex items-start gap-2">
                    <i class="fa-solid fa-circle-info text-amber-600 mt-0.5 text-sm"></i>
                    <div>
                        <span class="font-bold">Ketentuan Login Petugas:</span>
                        <p class="text-[11px] text-amber-800 mt-0.5">Portal ini digunakan untuk login petugas <strong>Panitia</strong>, <strong>Bendahara</strong>, dan <strong>Administrator</strong> SPMB menggunakan akun resmi yang telah didaftarkan.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5" id="label-login-identity">
                        Username / No. HP / NISN / Email
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="login_identity" id="input-login-identity" value="{{ old('login_identity') }}" placeholder="Masukkan No. HP / NISN" required autofocus
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kata Sandi (Password)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="input-login-pwd" placeholder="Masukkan kata sandi" required
                               class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="inline-flex items-center gap-1.5 text-slate-600 font-medium cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded text-blue-600">
                        <span>Ingat saya</span>
                    </label>
                    <a href="https://wa.me/6285290944090" target="_blank" class="text-blue-600 hover:underline font-bold">Lupa Sandi?</a>
                </div>

                <button type="submit" class="w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg shadow-blue-600/25 transition flex items-center justify-center gap-2 text-sm uppercase tracking-wider">
                    <span>Masuk Aplikasi</span> <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

            <div class="mt-6 pt-5 border-t border-slate-100 text-center">
                <p class="text-xs text-slate-500 font-medium">Belum memiliki akun pendaftaran?</p>
                <a href="{{ route('daftar') }}" class="inline-block mt-2 text-sm font-bold text-blue-600 hover:underline">
                    Daftar Sebagai Siswa Baru &rarr;
                </a>
            </div>

        </div>

    </div>

    <script>
        let selectedLoginType = 'murid';

        function setLoginType(type) {
            selectedLoginType = type;
            const tabMurid = document.getElementById('tab-type-murid');
            const tabPanitia = document.getElementById('tab-type-panitia');
            const labelIdentity = document.getElementById('label-login-identity');
            const inputIdentity = document.getElementById('input-login-identity');
            const title = document.getElementById('login-title');
            const panitiaInfo = document.getElementById('panitia-account-info');

            if (type === 'panitia') {
                tabMurid.className = 'flex-1 py-2 rounded-lg text-slate-600 hover:text-slate-900 transition text-center';
                tabPanitia.className = 'flex-1 py-2 rounded-lg bg-white text-indigo-700 shadow transition text-center';
                title.innerText = 'Portal Panitia SPMB';
                labelIdentity.innerText = 'Username / Email Petugas *';
                inputIdentity.placeholder = 'Masukkan username atau email petugas';
                if (panitiaInfo) panitiaInfo.classList.remove('hidden');
            } else {
                tabPanitia.className = 'flex-1 py-2 rounded-lg text-slate-600 hover:text-slate-900 transition text-center';
                tabMurid.className = 'flex-1 py-2 rounded-lg bg-white text-blue-700 shadow transition text-center';
                title.innerText = 'Portal Masuk Siswa';
                labelIdentity.innerText = 'Username / No. HP / NISN *';
                inputIdentity.placeholder = 'Contoh: 085861512412 / NISN';
                if (panitiaInfo) panitiaInfo.classList.add('hidden');
            }
        }
    </script>

</body>
</html>
