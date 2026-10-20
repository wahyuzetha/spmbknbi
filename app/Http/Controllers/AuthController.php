<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\StudentBiodata;
use App\Models\StudentParent;
use App\Models\StudentSchool;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Tampilkan formulir pendaftaran calon siswa baru.
     */
    public function showRegisterForm()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $majors = Major::where('is_active', true)->get();

        return view('auth.daftar', compact('academicYear', 'majors'));
    }

    /**
     * Proses pendaftaran calon siswa baru.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            // Pilihan Jurusan
            'major_id' => 'required|exists:jurusan,id',
            'jenis_pendaftaran' => 'nullable|string',
            'rekomendasi' => 'nullable|string',

            // Data Akun & Siswa
            'nama_siswa' => 'required|string|max:255',
            'nohp' => 'required|string|max:20|unique:siswa,phone',
            'nisn' => 'required|string|max:12|unique:siswa,nisn',
            'email' => 'required|email|max:255|unique:siswa,email',
            'password' => 'required|string|min:6',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|string|max:50',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',

            // Alamat Domisili
            'kabupaten' => 'required|string|max:100',
            'kecamatan' => 'required|string|max:100',
            'desa' => 'required|string|max:100',
            'alamat_jalan' => 'required|string|max:255',

            // Data Orang Tua
            'nama_ayah' => 'required|string|max:255',
            'pekerjaan_ayah' => 'required|string|max:100',
            'no_hp_ayah' => 'nullable|string|max:20',
            'nama_ibu' => 'required|string|max:255',
            'pekerjaan_ibu' => 'required|string|max:100',
            'no_hp_ibu' => 'nullable|string|max:20',

            // Asal Sekolah
            'asal_sekolah' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // 1. Buat Akun Siswa Baru di tabel 'siswa'
            $user = Siswa::create([
                'name' => $validated['nama_siswa'],
                'phone' => $validated['nohp'],
                'nisn' => $validated['nisn'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'raw_password' => $validated['password'],
                'role' => 'murid',
                'is_active' => true,
            ]);

            // 2. Buat Pendaftaran
            $academicYear = AcademicYear::where('is_active', true)->first();
            $registrationNumber = Registration::generateNumber($validated['major_id'], null, $academicYear);

            $registration = Registration::create([
                'registration_number' => $registrationNumber,
                'user_id' => $user->id,
                'academic_year_id' => $academicYear ? $academicYear->id : 1,
                'major_id' => $validated['major_id'],
                'registration_type' => $validated['jenis_pendaftaran'] ?? 'siswa_baru',
                'recommendation_by' => $validated['rekomendasi'] ?? null,
                'status' => 'pending',
            ]);

            // 3. Simpan Biodata Siswa
            StudentBiodata::create([
                'registration_id' => $registration->id,
                'nama_lengkap' => $validated['nama_siswa'],
                'nik' => $request->input('nik', null),
                'no_kk' => $request->input('no_kk', null),
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'agama' => $validated['agama'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'no_hp' => $validated['nohp'],
                'email' => $validated['email'],
                'alamat_jalan' => $validated['alamat_jalan'],
                'desa' => $validated['desa'],
                'kecamatan' => $validated['kecamatan'],
                'kabupaten' => $validated['kabupaten'],
                'provinsi' => $request->input('provinsi', 'Jawa Tengah'),
            ]);

            // 4. Simpan Data Orang Tua
            StudentParent::create([
                'registration_id' => $registration->id,
                'nama_ayah' => $validated['nama_ayah'],
                'pekerjaan_ayah' => $validated['pekerjaan_ayah'],
                'no_hp_ayah' => $validated['no_hp_ayah'] ?? $validated['nohp'],
                'status_ayah' => 'hidup',
                'nama_ibu' => $validated['nama_ibu'],
                'pekerjaan_ibu' => $validated['pekerjaan_ibu'],
                'no_hp_ibu' => $validated['no_hp_ibu'] ?? null,
                'status_ibu' => 'hidup',
            ]);

            // 5. Simpan Data Sekolah Asal
            StudentSchool::create([
                'registration_id' => $registration->id,
                'asal_sekolah' => $validated['asal_sekolah'],
                'jenis_sekolah' => 'SMP',
                'tahun_lulus' => date('Y'),
            ]);

            DB::commit();

            // Jika permintaan AJAX / Fetch (untuk menampilkan pop-up konfirmasi pendaftaran)
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pendaftaran berhasil disimpan!',
                    'data' => [
                        'name' => $user->name,
                        'username' => $user->phone, // atau $user->nisn
                        'nisn' => $user->nisn,
                        'phone' => $user->phone,
                        'raw_password' => $validated['password'],
                        'registration_number' => $registrationNumber,
                    ],
                ]);
            }

            // Jika form submit standar, simpan data ke flash session untuk membuka modal konfirmasi
            return redirect()->route('daftar')->with([
                'show_confirmation_modal' => true,
                'registered_name' => $user->name,
                'registered_username' => $user->nisn ?: $user->phone,
                'registered_password' => $validated['password'],
                'registered_number' => $registrationNumber,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem: '.$e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Gagal mendaftar: '.$e->getMessage());
        }
    }

    /**
     * Tampilkan halaman login utama.
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login (Mendukung No. HP, NISN, atau Email).
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login_identity' => 'required|string',
            'password' => 'required|string',
        ]);

        $identity = $credentials['login_identity'];
        $password = $credentials['password'];

        // Cari user pada tabel petugas (admin/panitia) atau tabel siswa (calon siswa)
        $user = User::where('email', $identity)
            ->orWhere('username', $identity)
            ->orWhere('phone', $identity)
            ->first()
            ?? Siswa::where('email', $identity)
                ->orWhere('nisn', $identity)
                ->orWhere('phone', $identity)
                ->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username / No. HP / NISN atau Kata Sandi salah.',
                ], 422);
            }

            throw ValidationException::withMessages([
                'login_identity' => ['Data akun atau kata sandi yang Anda masukkan tidak sesuai.'],
            ]);
        }

        if (! $user->is_active) {
            return back()->with('error', 'Akun Anda sedang dinonaktifkan. Silakan hubungi Panitia SPMB.');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        $request->session()->put('auth_model_type', $user instanceof Siswa ? 'siswa' : 'petugas');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'redirect_url' => $this->getRedirectUrl($user),
            ]);
        }

        return $this->redirectBasedOnRole($user);
    }

    /**
     * Logout pengguna dari sesi.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda telah berhasil keluar.');
    }

    /**
     * Helper redirect berdasarkan role.
     */
    private function redirectBasedOnRole($user)
    {
        return redirect()->intended($this->getRedirectUrl($user));
    }

    private function getRedirectUrl($user): string
    {
        return match ($user->role) {
            'admin' => route('admin.dashboard', [], false),
            'panitia' => route('panitia.dashboard', [], false),
            'bendahara' => route('bendahara.dashboard', [], false),
            default => route('murid.dashboard', [], false),
        };
    }
}
