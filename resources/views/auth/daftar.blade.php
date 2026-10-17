<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran Siswa Baru | SMK Karya Nugraha Boyolali</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome Icons -->
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
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#3b82f6',
                            600: '#0066ff',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #1e3a8a 0%, #0066ff 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="py-6 px-4 sm:px-6 lg:px-8 selection:bg-blue-600 selection:text-white">

    <div class="max-w-4xl mx-auto">

        <!-- TOMBOL KEMBALI -->
        <div class="mb-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs sm:text-sm font-bold text-white/90 hover:text-white bg-white/10 hover:bg-white/20 px-4 py-2 rounded-xl backdrop-blur-md transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

        <!-- CARD FORMULIR UTAMA -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-slate-100">
            
            <!-- HEADER FORM -->
            <div class="p-6 sm:p-8 text-center border-b border-slate-100 bg-slate-50/50">
                <!-- LOGO SEKOLAH -->
                <div class="w-20 h-20 mx-auto mb-3 flex items-center justify-center overflow-hidden">
                    @if(!empty($schoolLogo))
                        <img src="{{ asset($schoolLogo) }}" alt="Logo Sekolah" class="max-w-full max-h-full object-contain">
                    @else
                        <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-md">
                            <!-- Pentagonal Outer Shield -->
                            <polygon points="50,5 95,38 78,92 22,92 5,38" fill="#1e40af" stroke="#ffffff" stroke-width="2"/>
                            <polygon points="50,10 90,40 75,87 25,87 10,40" fill="#2563eb"/>
                            <!-- Inner Circle Gear -->
                            <circle cx="50" cy="50" r="28" fill="#ffffff"/>
                            <circle cx="50" cy="50" r="22" fill="#1d4ed8"/>
                            <!-- Torch / Monas icon inside -->
                            <path d="M50,34 L54,48 L46,48 Z" fill="#facc15"/>
                            <rect x="48" y="48" width="4" height="16" fill="#ffffff"/>
                            <!-- Text Ribbon -->
                            <rect x="18" y="72" width="64" height="12" rx="3" fill="#1e3a8a"/>
                            <text x="50" y="81" font-size="6" font-weight="bold" fill="#ffffff" text-anchor="middle" font-family="sans-serif">SMK KARYA NUGRAHA</text>
                        </svg>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Formulir Pendaftaran Siswa Baru</h1>
                <p class="text-sm font-semibold text-slate-600 mt-1 uppercase tracking-wide">
                    SMK KARYA NUGRAHA BOYOLALI
                </p>

                <div class="inline-flex items-center gap-2 mt-4 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold">
                    <i class="fa-regular fa-calendar-check text-emerald-600"></i>
                    Batas Akhir Pendaftaran: 11 Agustus 2027
                </div>
            </div>

            <!-- ALERT ERROR VALIDASI (JIKA ADA) -->
            @if ($errors->any())
                <div class="m-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-sm">
                    <div class="font-bold mb-1 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> Terdapat kesalahan pengisian data:
                    </div>
                    <ul class="list-disc pl-5 space-y-1 text-xs">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- FORM PENDAFTARAN -->
            <form id="form-pendaftaran" action="{{ route('daftar.store') }}" method="POST" class="p-6 sm:p-10 space-y-8">
                @csrf

                <!-- ================= 1. JURUSAN / PEMINATAN ================= -->
                <div>
                    <div class="flex items-center gap-2 pb-2 mb-4 border-b-2 border-blue-600">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">1</span>
                        <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">Pilihan Jurusan / Peminatan</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                                Jurusan Peminatan Utama *
                            </label>
                            <select name="major_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-900 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-600/20 focus:border-blue-600 transition">
                                <option value="">-- Pilih Jurusan Peminatan --</option>
                                @foreach ($majors as $major)
                                    <option value="{{ $major->id }}" {{ old('major_id') == $major->id ? 'selected' : '' }}>
                                        {{ $major->name }} ({{ $major->industry_class }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ================= 2. DATA PRIBADI SISWA ================= -->
                <div>
                    <div class="flex items-center gap-2 pb-2 mb-4 border-b-2 border-blue-600">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">2</span>
                        <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">Data Pribadi Calon Siswa</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Pendaftaran</label>
                            <select name="jenis_pendaftaran" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                                <option value="siswa_baru" selected>Siswa Baru</option>
                                <option value="pindahan">Pindahan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. HP / WhatsApp Siswa *</label>
                            <input type="text" name="nohp" value="{{ old('nohp') }}" placeholder="Contoh: 085861512412" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Lengkap Siswa *</label>
                            <input type="text" name="nama_siswa" value="{{ old('nama_siswa') }}" placeholder="Sesuai ijazah SMP/MTs" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">NISN (10 Digit) *</label>
                            <input type="text" name="nisn" maxlength="12" value="{{ old('nisn') }}" placeholder="Contoh: 0081234567" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Kelamin *</label>
                            <div class="flex items-center gap-6 h-10">
                                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                                    <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required class="w-4 h-4 text-blue-600"> Laki-laki
                                </label>
                                <label class="inline-flex items-center gap-2 cursor-pointer text-sm font-medium text-slate-700">
                                    <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required class="w-4 h-4 text-blue-600"> Perempuan
                                </label>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Agama *</label>
                            <select name="agama" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                                <option value="">-- Pilih Agama --</option>
                                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                                <option value="Katholik" {{ old('agama') == 'Katholik' ? 'selected' : '' }}>Katholik</option>
                                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                                <option value="Budha" {{ old('agama') == 'Budha' ? 'selected' : '' }}>Budha</option>
                                <option value="Khong Hu Cu" {{ old('agama') == 'Khong Hu Cu' ? 'selected' : '' }}>Khong Hu Cu</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Email Siswa *</label>
                            <input type="email" name="email" value="{{ old('email') }}" placeholder="alamat@email.com" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tempat Lahir *</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Kota / Kabupaten Lahir" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Lahir *</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                    </div>
                </div>

                <!-- ================= 3. DATA ORANG TUA ================= -->
                <div>
                    <div class="flex items-center gap-2 pb-2 mb-4 border-b-2 border-blue-600">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">3</span>
                        <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">Data Orang Tua / Wali</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Ayah Kandung *</label>
                            <input type="text" name="nama_ayah" value="{{ old('nama_ayah') }}" placeholder="Nama Lengkap Ayah" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Ibu Kandung *</label>
                            <input type="text" name="nama_ibu" value="{{ old('nama_ibu') }}" placeholder="Nama Lengkap Ibu" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pekerjaan Ayah *</label>
                            <select name="pekerjaan_ayah" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                                <option value="">-- Pilih Pekerjaan Ayah --</option>
                                <option value="Wiraswasta">Wiraswasta / Pedagang</option>
                                <option value="Petani/Peternak">Petani / Peternak</option>
                                <option value="Pegawai Swasta">Pegawai Swasta</option>
                                <option value="PNS">PNS / ASN</option>
                                <option value="TNI/Polisi">TNI / Polri</option>
                                <option value="Guru/Dosen">Guru / Dosen</option>
                                <option value="Buruh">Buruh Harian Lepas</option>
                                <option value="Pensiunan">Pensiunan</option>
                                <option value="Tidak Bekerja">Tidak Bekerja</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pekerjaan Ibu *</label>
                            <select name="pekerjaan_ibu" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                                <option value="">-- Pilih Pekerjaan Ibu --</option>
                                <option value="Ibu Rumah Tangga">Ibu Rumah Tangga</option>
                                <option value="Wiraswasta">Wiraswasta / Pedagang</option>
                                <option value="Petani/Peternak">Petani / Peternak</option>
                                <option value="Pegawai Swasta">Pegawai Swasta</option>
                                <option value="PNS">PNS / ASN</option>
                                <option value="Guru/Dosen">Guru / Dosen</option>
                                <option value="Buruh">Buruh</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">No. HP / WA Orang Tua</label>
                            <input type="text" name="no_hp_ayah" value="{{ old('no_hp_ayah') }}" placeholder="Contoh: 08123456789"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <!-- ================= 4. ALAMAT RUMAH DOMISILI ================= -->
                <div>
                    <div class="flex items-center gap-2 pb-2 mb-4 border-b-2 border-blue-600">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">4</span>
                        <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">Alamat Rumah Domisili</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kabupaten / Kota *</label>
                            <input type="text" name="kabupaten" value="{{ old('kabupaten', 'Boyolali') }}" placeholder="Contoh: Boyolali" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kecamatan *</label>
                            <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" placeholder="Contoh: Mojosongo / Boyolali" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Desa / Kelurahan *</label>
                            <input type="text" name="desa" value="{{ old('desa') }}" placeholder="Contoh: Kemiri" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Nama Jalan / RT / RW / Dusun *</label>
                            <input type="text" name="alamat_jalan" value="{{ old('alamat_jalan') }}" placeholder="Contoh: RT 02 / RW 05, Dusun Mawar" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>
                    </div>
                </div>

                <!-- ================= 5. ASAL SEKOLAH & KATA SANDI AKUN ================= -->
                <div>
                    <div class="flex items-center gap-2 pb-2 mb-4 border-b-2 border-blue-600">
                        <span class="w-7 h-7 rounded-lg bg-blue-600 text-white font-bold text-xs flex items-center justify-center">5</span>
                        <h2 class="text-base font-extrabold text-slate-900 uppercase tracking-wide">Asal Sekolah & Kata Sandi Akun</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Asal Sekolah (SMP / MTs) *</label>
                            <input type="text" name="asal_sekolah" value="{{ old('asal_sekolah') }}" placeholder="Contoh: SMPN 1 Boyolali" required
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rekomendasi Dari (Jika Ada)</label>
                            <input type="text" name="rekomendasi" value="{{ old('rekomendasi') }}" placeholder="Contoh: Guru SMP / Alumni / Teman"
                                   class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Kata Sandi (Password Akun Login) * <span class="text-rose-600 font-bold">(Mohon Diingat!)</span>
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="input_password" placeholder="Buat kata sandi minimal 6 karakter" required
                                       class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium focus:bg-white focus:border-blue-600 pr-12">
                                <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600">
                                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                                </button>
                            </div>
                            <p class="text-xs text-slate-500 mt-1 font-medium">
                                Kata sandi ini akan Anda gunakan untuk masuk ke aplikasi dan mencetak bukti pendaftaran.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- SUBMIT BUTTON -->
                <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-slate-500 text-center sm:text-left">
                        Pastikan seluruh data yang Anda masukkan sudah benar sebelum disimpan.
                    </p>
                    <button type="submit" id="btn-submit" class="w-full sm:w-auto px-8 py-3.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-2xl shadow-xl shadow-blue-600/30 hover:shadow-2xl transition transform hover:-translate-y-0.5 flex items-center justify-center gap-2 text-sm uppercase tracking-wide">
                        <span>Simpan Pendaftaran</span> <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>

            </form>

        </div>

        <div class="text-center mt-6 text-xs text-blue-100 font-medium pb-8">
            Copyright &copy; {{ date('Y') }} SMK KARYA NUGRAHA BOYOLALI. All rights reserved.
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POPUP: KONFIRMASI PENDAFTARAN (PERSIS GAMBAR REFERENSI USER) -->
    <!-- ========================================================================= -->
    <div id="modal-konfirmasi" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm transition-opacity duration-300 {{ session('show_confirmation_modal') ? '' : 'hidden' }}">
        
        <!-- CARD MODAL -->
        <div class="bg-white rounded-3xl shadow-2xl border border-slate-100 max-w-sm w-full p-6 sm:p-7 relative transform transition-all scale-100 animate-in fade-in zoom-in duration-200">
            
            <!-- LOGO SEKOLAH -->
            <div class="w-20 h-20 mx-auto mb-2 flex items-center justify-center overflow-hidden">
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

            <!-- JUDUL KONFIRMASI -->
            <div class="text-center mb-5">
                <p class="text-xs text-slate-500 font-medium">Konfirmasi Pendaftaran</p>
                <h3 class="text-base font-extrabold text-slate-800 tracking-tight mt-0.5">
                    SMK KARYA NUGRAHA BOYOLALI
                </h3>
            </div>

            <!-- FORM AUTO LOGIN -->
            <form id="form-login-confirm" action="{{ route('login.post') }}" method="POST" class="space-y-3.5">
                @csrf

                <!-- NAMA PENDAFTAR -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pendaftar</label>
                    <input type="text" id="confirm_name" name="name_display" value="{{ session('registered_name') }}" readonly
                           class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none">
                </div>

                <!-- DUA KOLOM: USERNAME & PASSWORD -->
                <div class="grid grid-cols-2 gap-2.5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Username</label>
                        <input type="text" id="confirm_username" name="login_identity" value="{{ session('registered_username') }}" required readonly
                               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                        <input type="text" id="confirm_password" name="password" value="{{ session('registered_password') }}" required
                               placeholder="Kata Sandi"
                               class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-semibold text-slate-800 focus:outline-none focus:border-blue-600">
                    </div>
                </div>

                <!-- ALERT KUNING: PASSWORD (MOHON DIINGAT!) -->
                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200/90 flex items-center gap-2.5 text-amber-900 text-xs font-bold shadow-sm">
                    <span class="text-amber-600 text-base">⚠️</span>
                    <span>PASSWORD (Mohon Diingat!)</span>
                </div>

                <!-- TOMBOL MASUK (KANAN BAWAH) -->
                <div class="flex justify-end pt-3">
                    <button type="submit" id="btn-login-modal" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider flex items-center gap-2 shadow-md shadow-blue-500/25 hover:shadow-lg transition">
                        <i class="fa-solid fa-right-to-bracket"></i> MASUK
                    </button>
                </div>

            </form>

        </div>

    </div>

    <!-- JAVASCRIPT: AJAX FORM & POPUP HANDLING -->
    <script>
        function togglePasswordVisibility() {
            const pwd = document.getElementById('input_password');
            const icon = document.getElementById('eye-icon');
            if (pwd.type === 'password') {
                pwd.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                pwd.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Intercept form submit via Fetch AJAX agar langsung memunculkan pop-up modal konfirmasi
        document.getElementById('form-pendaftaran').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const submitBtn = document.getElementById('btn-submit');
            const originalBtnHtml = submitBtn.innerHTML;

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Menyimpan...';

            const formData = new FormData(form);

            fetch("{{ route('daftar.store') }}", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                },
                body: formData
            })
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;

                if (status === 200 && body.success) {
                    // Isi data ke modal konfirmasi seperti pada gambar user
                    document.getElementById('confirm_name').value = body.data.name;
                    document.getElementById('confirm_username').value = body.data.nisn || body.data.phone;
                    document.getElementById('confirm_password').value = body.data.raw_password;

                    // Tampilkan modal konfirmasi pendaftaran
                    const modal = document.getElementById('modal-konfirmasi');
                    modal.classList.remove('hidden');
                } else {
                    let errMsg = body.message || 'Terjadi kesalahan validasi.';
                    if (body.errors) {
                        errMsg = Object.values(body.errors).flat().join('\n');
                    }
                    alert(errMsg);
                }
            })
            .catch(error => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
                alert('Gagal mengirim pendaftaran: ' + error.message);
            });
        });
    </script>

</body>
</html>
