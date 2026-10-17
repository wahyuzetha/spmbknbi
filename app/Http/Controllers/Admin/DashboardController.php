<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Siswa;
use App\Models\StudentBiodata;
use App\Models\StudentParent;
use App\Models\StudentSchool;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\StudentExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard Admin SPMB.
     */
    public function index(Request $request)
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $allAcademicYears = AcademicYear::withCount('registrations')->orderBy('id', 'desc')->get();

        // 1. Statistik 4 Kartu Metrik Utama (Sesuai Gambar Referensi)
        $totalRegistrations = Registration::count();
        $acceptedCount = Registration::where('status', 'accepted')->count();
        $pendingTestCount = Registration::where('status', 'pending')->count();
        $paidCount = Payment::where('status', 'confirmed')->orWhere('status', 'success')->count();

        $verifiedCount = Registration::where('status', 'verified')->count();
        $rejectedCount = Registration::where('status', 'rejected')->count();

        // 2. Statistik & Data Jurusan (7 Jurusan SMK KNBI)
        $majors = Major::withCount('registrations')->get()->map(function ($major) {
            $major->percentage = $major->quota > 0 ? round(($major->registrations_count / $major->quota) * 100) : 0;
            return $major;
        });

        // 3. Query Pendaftar dengan Filter & Pencarian
        $query = Registration::with(['user', 'major', 'biodata', 'school', 'documents', 'verifier'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('nisn', 'like', "%{$search}%");
                  });
            });
        }

        // Calon siswa & pendaftar: paginasi maksimal 10 per halaman sesuai instruksi user
        $allCalonSiswa = (clone $query)->get();
        $registrations = $query->paginate(10)->withQueryString();

        // 4. Data Pengguna Staf (Panitia PPDB & Administrator) untuk Halaman Data Pengguna
        $staffUsers = User::whereIn('role', ['admin', 'panitia'])->latest()->get();

        // 5. Data Pembayaran Administrasi (Dengan Logika Grouping Global Siswa & Multi-Transaksi)
        $allPayments = Payment::with(['registration.user', 'registration.major', 'confirmedBy'])
            ->latest('payment_date')
            ->latest('id')
            ->get();

        // Mengelompokkan pembayaran berdasarkan pendaftar (registration_id)
        // Apabila calon siswa melakukan lebih dari 2 kali transaksi (atau multiple), otomatis di-global menjadi 1 baris
        // tanpa menghilangkan history pembayaran sebelumnya
        $groupedPayments = $allPayments->groupBy('registration_id')->map(function ($items, $registrationId) {
            $first = $items->first();
            $registration = $first->registration;
            $confirmedItems = $items->where('status', 'confirmed');
            $totalAmount = $confirmedItems->sum('amount');
            if ($totalAmount == 0) {
                $totalAmount = $items->sum('amount');
            }
            $transactionCount = $items->count();
            $lastPaymentDate = $items->max('payment_date') ?? $items->max('created_at');
            $lastConfirmedBy = $items->firstWhere('confirmed_by', '!=', null)?->confirmedBy ?? $first->confirmedBy;

            return (object) [
                'registration_id' => $registrationId,
                'registration' => $registration,
                'total_amount' => $totalAmount,
                'transaction_count' => $transactionCount,
                'is_multi' => $transactionCount >= 2,
                'last_payment_date' => $lastPaymentDate,
                'last_confirmed_by' => $lastConfirmedBy,
                'payments' => $items, // seluruh history transaksi tanpa ada yang hilang
            ];
        })->values();

        // Metrik Keuangan Kas Administrasi SPMB
        $totalKasMasuk = Payment::where('status', 'confirmed')->sum('amount');
        $totalSiswaBayar = $groupedPayments->count();
        $totalTransaksi = $allPayments->count();
        $multiTransactionCount = $groupedPayments->where('is_multi', true)->count();

        // Data seluruh calon siswa untuk pencarian cepat dropdown input kasir/bendahara
        $candidatesForPayment = Registration::with(['user', 'major', 'school'])
            ->latest()
            ->get()
            ->map(function ($r) {
                return (object) [
                    'id' => $r->id,
                    'registration_number' => $r->registration_number,
                    'name' => $r->user->name ?? 'Calon Siswa',
                    'phone' => $r->user->phone ?? '-',
                    'school' => $r->school->asal_sekolah ?? '-',
                    'major' => $r->major->name ?? '-',
                    'industry_class' => $r->major->industry_class ?? '',
                ];
            });

        $payments = Payment::with('registration.user')->latest()->paginate(10);

        // 6. Pengaturan Sistem & Profil Sekolah
        $settings = SystemSetting::pluck('value', 'key');

        $currentView = $request->get('view', 'dashboard');
        $profileTab = $request->get('tab', 'profile');

        return view('admin.dashboard', compact(
            'academicYear',
            'allAcademicYears',
            'totalRegistrations',
            'acceptedCount',
            'pendingTestCount',
            'paidCount',
            'verifiedCount',
            'rejectedCount',
            'majors',
            'registrations',
            'allCalonSiswa',
            'staffUsers',
            'payments',
            'allPayments',
            'groupedPayments',
            'totalKasMasuk',
            'totalSiswaBayar',
            'totalTransaksi',
            'multiTransactionCount',
            'candidatesForPayment',
            'settings',
            'currentView',
            'profileTab'
        ));
    }

    /**
     * ==========================================
     * FITUR & AKSI MENU SEKOLAH: DATA JURUSAN
     * ==========================================
     */

    /**
     * Tambah Data Jurusan Baru (Sesuai Gambar 2).
     */
    public function storeMajor(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:jurusan,code',
            'name' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'industry_class' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        Major::create([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'quota' => $validated['quota'],
            'industry_class' => $validated['industry_class'] ?? '',
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Data jurusan {$validated['name']} ({$validated['code']}) berhasil ditambahkan!");
    }

    /**
     * Update Data Jurusan (Sesuai Gambar 2).
     */
    public function updateMajor(Request $request, $id)
    {
        $major = Major::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:jurusan,code,' . $major->id,
            'name' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'industry_class' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $industryClass = !empty($validated['industry_class']) ? trim($validated['industry_class']) : null;

        $major->update([
            'code' => $validated['code'],
            'name' => $validated['name'],
            'quota' => $validated['quota'],
            'industry_class' => $industryClass,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', "Data jurusan '{$major->name}' berhasil diperbarui! Perubahan otomatis diterapkan pada seluruh data calon siswa.");
    }

    /**
     * Update Khusus Kelas Industri Jurusan.
     */
    public function updateIndustryClass(Request $request, $id)
    {
        $major = Major::findOrFail($id);

        $validated = $request->validate([
            'industry_class' => 'nullable|string|max:100',
        ]);

        $industryClass = !empty($validated['industry_class']) ? trim($validated['industry_class']) : null;

        $major->update([
            'industry_class' => $industryClass,
        ]);

        $info = $industryClass ? "'{$industryClass}'" : "(Dikosongkan)";
        return back()->with('success', "Kelas Industri untuk jurusan '{$major->name}' berhasil diperbarui menjadi {$info}! Seluruh data calon siswa otomatis sinkron.");
    }

    /**
     * Hapus Data Jurusan (Sesuai Tombol Hapus pada Gambar 2).
     */
    public function deleteMajor($id)
    {
        $major = Major::findOrFail($id);
        $name = $major->name;
        $major->delete();

        return back()->with('success', "Jurusan {$name} berhasil dihapus.");
    }

    /**
     * Update kuota jurusan cepat.
     */
    public function updateMajorQuota(Request $request, $id)
    {
        $request->validate([
            'quota' => 'required|integer|min:1',
            'industry_class' => 'nullable|string|max:100',
        ]);

        $major = Major::findOrFail($id);
        $major->update([
            'quota' => $request->quota,
            'industry_class' => $request->input('industry_class', $major->industry_class),
        ]);

        return back()->with('success', "Kuota jurusan {$major->name} berhasil diperbarui menjadi {$request->quota} siswa.");
    }

    /**
     * ==========================================
     * FITUR & AKSI MENU SEKOLAH: DATA PENGGUNA
     * ==========================================
     */

    /**
     * Tambah Data Pengguna Baru (Sesuai Formulir Gambar 1).
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:petugas,username',
            'password' => 'required|string|min:4',
            'role' => 'required|string|in:panitia,admin',
        ]);

        $email = $validated['username'] . '@smkknbi.sch.id';
        // Pastikan email unik jika username dipakai
        if (User::where('email', $email)->exists()) {
            $email = $validated['username'] . '_' . time() . '@smkknbi.sch.id';
        }

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $email,
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        $roleLabel = $validated['role'] === 'admin' ? 'Administrator' : 'Panitia SPMB';
        return back()->with('success', "Akun {$roleLabel} '{$validated['name']}' ({$validated['username']}) berhasil dibuat! Akun siap digunakan untuk login Panitia SPMB.");
    }

    /**
     * Update Data Pengguna (Sesuai Tombol Edit Gambar 1).
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:100|unique:petugas,username,' . $user->id,
            'password' => 'nullable|string|min:4',
            'role' => 'required|string|in:panitia,admin',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'role' => $validated['role'],
        ];

        $pwdUpdated = false;
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
            $pwdUpdated = true;
        }

        $user->update($updateData);

        $msg = "Data pengguna {$user->name} berhasil diperbarui!";
        if ($pwdUpdated) {
            $msg .= " Password akun telah berhasil diubah.";
        }

        return back()->with('success', $msg);
    }

    /**
     * Hapus Pengguna Satuan.
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Pengguna {$name} berhasil dihapus.");
    }

    /**
     * Hapus Data Pengguna Massal / Bulk Delete (Sesuai Tombol Merah Hapus Data Gambar 1).
     */
    public function bulkDeleteUser(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Silakan pilih / centang data pengguna yang ingin dihapus terlebih dahulu.');
        }

        // Jangan hapus akun yang sedang login
        $ids = array_filter($ids, fn($id) => $id != auth()->id());

        $count = User::whereIn('id', $ids)->delete();

        return back()->with('success', "{$count} data pengguna terpilih berhasil dihapus!");
    }

    /**
     * Update Profil Sekolah dari tab dashboard.
     */
    public function updateSettings(Request $request)
    {
        $fields = [
            'school_npsn', 'school_name', 'school_level', 'school_status',
            'school_address', 'school_phone', 'school_email', 'school_website',
            'school_principal_name', 'school_principal_nip'
        ];

        foreach ($fields as $field) {
            if ($request->has($field)) {
                SystemSetting::set($field, $request->input($field), 'school_profile');
            }
        }

        return back()->with('success', 'Data Profil Sekolah berhasil disimpan!');
    }

    /**
     * =========================================================================
     * MENU PENGATURAN SPMB & SEKOLAH TERPADU (TAHUN AJARAN, LOGO, HOTLINE, TTD, KOP)
     * =========================================================================
     */

    /**
     * Update Pengaturan Lengkap SPMB & Sekolah (Logo, Hotline, TTD Ketua, Kop Surat, Profil).
     */
    public function updateSpmbSettings(Request $request)
    {
        $request->validate([
            'school_name' => 'nullable|string|max:255',
            'school_npsn' => 'nullable|string|max:50',
            'school_level' => 'nullable|string|max:50',
            'school_status' => 'nullable|string|max:50',
            'school_tagline' => 'nullable|string|max:255',
            'school_address' => 'nullable|string|max:500',
            'school_phone' => 'nullable|string|max:50',
            'school_email' => 'nullable|string|max:100',
            'school_website' => 'nullable|string|max:100',
            
            // Hotline Panitia
            'hotline_panitia' => 'nullable|string|max:50',
            'hotline_panitia_name' => 'nullable|string|max:100',
            'hotline_panitia_hours' => 'nullable|string|max:100',
            
            // TTD & Ketua Panitia
            'chairman_name' => 'nullable|string|max:255',
            'chairman_nip' => 'nullable|string|max:100',
            
            // Kop Surat
            'cop_school_name' => 'nullable|string|max:255',
            'cop_school_status' => 'nullable|string|max:255',
            'cop_school_address' => 'nullable|string|max:500',
            'cop_school_contact' => 'nullable|string|max:255',
            
            // File Uploads
            'school_logo' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'chairman_signature' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
            'cop_image_banner' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        // 1. Simpan Seluruh Pengaturan Teks
        $textFields = [
            'school_name' => 'school_profile',
            'school_npsn' => 'school_profile',
            'school_level' => 'school_profile',
            'school_status' => 'school_profile',
            'school_tagline' => 'school_profile',
            'school_address' => 'school_profile',
            'school_phone' => 'school_profile',
            'school_email' => 'school_profile',
            'school_website' => 'school_profile',
            
            'hotline_panitia' => 'committee',
            'hotline_panitia_name' => 'committee',
            'hotline_panitia_hours' => 'committee',
            
            'chairman_name' => 'committee',
            'chairman_nip' => 'committee',
            
            'cop_school_name' => 'cop_surat',
            'cop_school_status' => 'cop_surat',
            'cop_school_address' => 'cop_surat',
            'cop_school_contact' => 'cop_surat',
        ];

        foreach ($textFields as $field => $group) {
            if ($request->has($field)) {
                SystemSetting::set($field, $request->input($field), $group);
            }
        }

        // 2. Upload / Hapus File Logo Sekolah
        if ($request->boolean('delete_logo') || $request->filled('remove_logo')) {
            $oldLogo = SystemSetting::where('key', 'school_logo')->value('value');
            if ($oldLogo && File::exists(public_path($oldLogo))) {
                File::delete(public_path($oldLogo));
            }
            SystemSetting::set('school_logo', null, 'branding');
        } elseif ($request->hasFile('school_logo')) {
            $dir = public_path('uploads/logo');
            File::ensureDirectoryExists($dir);
            $file = $request->file('school_logo');
            $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            SystemSetting::set('school_logo', 'uploads/logo/' . $filename, 'branding', 'file', 'Logo Sekolah');
        }

        // 3. Upload / Hapus File TTD Ketua Panitia Digital (PNG Transparan)
        if ($request->boolean('delete_signature') || $request->filled('remove_signature')) {
            $oldSig = SystemSetting::where('key', 'chairman_signature')->value('value');
            if ($oldSig && File::exists(public_path($oldSig))) {
                File::delete(public_path($oldSig));
            }
            SystemSetting::set('chairman_signature', null, 'committee');
        } elseif ($request->hasFile('chairman_signature')) {
            $dir = public_path('uploads/signatures');
            File::ensureDirectoryExists($dir);
            $file = $request->file('chairman_signature');
            $filename = 'ttd_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            SystemSetting::set('chairman_signature', 'uploads/signatures/' . $filename, 'committee', 'file', 'TTD Ketua Panitia');
        }

        // 4. Upload / Hapus File Banner Kop Surat Jadi (Opsional)
        if ($request->boolean('delete_cop_banner') || $request->filled('remove_cop_banner')) {
            $oldBanner = SystemSetting::where('key', 'cop_image_banner')->value('value');
            if ($oldBanner && File::exists(public_path($oldBanner))) {
                File::delete(public_path($oldBanner));
            }
            SystemSetting::set('cop_image_banner', null, 'cop_surat');
        } elseif ($request->hasFile('cop_image_banner')) {
            $dir = public_path('uploads/kop');
            File::ensureDirectoryExists($dir);
            $file = $request->file('cop_image_banner');
            $filename = 'kop_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            SystemSetting::set('cop_image_banner', 'uploads/kop/' . $filename, 'cop_surat', 'file', 'Banner Kop Surat Jadi');
        }

        return redirect()->route('admin.dashboard', ['view' => 'pengaturan'])
            ->with('success', 'Pengaturan SPMB, logo sekolah, hotline, TTD ketua panitia, dan kop surat berhasil diperbarui!');
    }

    /**
     * Tambah Tahun Ajaran Baru.
     */
    public function storeAcademicYear(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:tahun_ajaran,name',
            'registration_start' => 'nullable|date',
            'registration_end' => 'nullable|date',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $isActive = !empty($request->is_active);

        if ($isActive) {
            AcademicYear::query()->update(['is_active' => false]);
        }

        $regStart = $validated['registration_start'] ?? $validated['start_date'] ?? null;
        $regEnd = $validated['registration_end'] ?? $validated['end_date'] ?? null;

        $ay = AcademicYear::create([
            'name' => $validated['name'],
            'registration_start' => $regStart,
            'registration_end' => $regEnd,
            'description' => $validated['description'] ?? null,
            'is_active' => $isActive,
        ]);

        return redirect()->route('admin.dashboard', ['view' => 'pengaturan'])
            ->with('success', "Tahun ajaran {$ay->name} berhasil ditambahkan" . ($isActive ? " dan langsung diaktifkan!" : "!"));
    }

    /**
     * Aktifkan Tahun Ajaran (Switch Tahun Ajaran).
     */
    public function activateAcademicYear($id)
    {
        AcademicYear::query()->update(['is_active' => false]);
        $ay = AcademicYear::findOrFail($id);
        $ay->update(['is_active' => true]);

        return redirect()->route('admin.dashboard', ['view' => 'pengaturan'])
            ->with('success', "Tahun Ajaran {$ay->name} sekarang AKTIF sebagai tahun ajaran pendaftaran utama!");
    }

    /**
     * Hapus Tahun Ajaran.
     */
    public function deleteAcademicYear($id)
    {
        $ay = AcademicYear::withCount('registrations')->findOrFail($id);

        if ($ay->is_active) {
            return redirect()->route('admin.dashboard', ['view' => 'pengaturan'])
                ->with('error', "Tahun ajaran aktif ({$ay->name}) tidak dapat dihapus! Silakan aktifkan tahun ajaran lain terlebih dahulu.");
        }

        if ($ay->registrations_count > 0) {
            return redirect()->route('admin.dashboard', ['view' => 'pengaturan'])
                ->with('error', "Tahun ajaran {$ay->name} memiliki {$ay->registrations_count} data pendaftar terdaftar dan tidak dapat dihapus!");
        }

        $ay->delete();

        return redirect()->route('admin.dashboard', ['view' => 'pengaturan'])
            ->with('success', "Tahun ajaran {$ay->name} berhasil dihapus!");
    }

    /**
     * Update status pendaftar.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,verified,accepted,rejected,reserved',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        $registration = Registration::with(['user', 'biodata'])->findOrFail($id);
        $registration->update([
            'status' => $request->status,
            'verification_notes' => $request->verification_notes,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $studentName = $registration->user?->name ?? $registration->biodata?->nama_lengkap ?? 'Calon Siswa';
            return response()->json([
                'success' => true,
                'message' => "Status calon siswa {$studentName} ({$registration->registration_number}) berhasil diubah menjadi " . strtoupper($request->status),
                'status' => $registration->status,
                'status_label' => match ($registration->status) {
                    'accepted' => 'Diterima',
                    'pending' => 'Pending',
                    'verified' => 'Terverifikasi',
                    'rejected' => 'Ditolak',
                    default => ucfirst($registration->status)
                },
                'student_name' => $studentName,
                'accepted_count' => Registration::where('status', 'accepted')->count(),
                'pending_count' => Registration::where('status', 'pending')->count(),
            ]);
        }

        return back()->with('success', "Status pendaftaran {$registration->registration_number} berhasil diperbarui menjadi " . strtoupper($request->status));
    }

    /**
     * Store panitia shortcut.
     */
    public function storePanitia(Request $request)
    {
        return $this->storeUser($request);
    }

    /**
     * Export data.
     */
    public function export(Request $request)
    {
        $registrations = Registration::with(['user', 'major', 'biodata', 'school'])->latest()->get();

        $csvHeader = [
            'No. Pendaftaran',
            'Nama Siswa',
            'NISN',
            'No. HP/WA',
            'Email',
            'Jurusan',
            'Asal Sekolah',
            'Kabupaten',
            'Status',
            'Tanggal Daftar',
        ];

        $callback = function () use ($registrations, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($registrations as $r) {
                fputcsv($file, [
                    $r->registration_number,
                    $r->user->name ?? '-',
                    $r->user->nisn ?? '-',
                    $r->user->phone ?? '-',
                    $r->user->email ?? '-',
                    $r->major->name ?? '-',
                    $r->school->asal_sekolah ?? '-',
                    $r->biodata->kabupaten ?? '-',
                    strtoupper($r->status),
                    $r->created_at->format('Y-m-d H:i'),
                ]);
            }
            fclose($file);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="SPMB_SMK_KNBI_' . date('Ymd_His') . '.csv"',
        ];

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Excel (.xlsx) Lengkap Seluruh Data Calon Siswa (PhpSpreadsheet).
     */
    public function exportExcel(Request $request, StudentExportService $exporter)
    {
        return $exporter->exportAll(
            $request->status,
            $request->major_id,
            $request->search
        );
    }

    /**
     * Cetak All: Laporan Resmi Seluruh Calon Siswa Terdaftar (Siap Print / PDF).
     */
    public function printAllStudents(Request $request)
    {
        $query = Registration::with(['user', 'major', 'biodata', 'school', 'parent'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        $registrations = $query->get();
        $academicYear = AcademicYear::where('is_active', true)->first();
        $settings = SystemSetting::pluck('value', 'key');

        return view('admin.print_all', compact('registrations', 'academicYear', 'settings'));
    }

    /**
     * Cetak Kartu Pendaftaran Calon Siswa Satuan (Siap Print).
     */
    public function printStudentCard($id)
    {
        $registration = Registration::with(['user', 'major', 'majorChoice2', 'biodata', 'school', 'parent', 'academicYear', 'documents'])
            ->findOrFail($id);
        $settings = SystemSetting::pluck('value', 'key');

        return view('admin.print_card', compact('registration', 'settings'));
    }

    /**
     * Tambah Data Calon Siswa Baru secara Manual / Walk-in dari Antarmuka Admin/Panitia.
     */
    public function storeStudentManual(Request $request)
    {
        $validated = $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'username' => 'nullable|string|max:100|unique:siswa,username',
            'password' => 'required|string|min:4',
            'phone' => 'required|string|max:20|unique:siswa,phone',
            'nisn' => 'nullable|string|max:30|unique:siswa,nisn',
            'gender' => 'required|in:L,P',
            'asal_sekolah' => 'required|string|max:255',
            'major_id' => 'required|exists:jurusan,id',
            'status' => 'nullable|in:pending,verified,accepted,rejected',
        ], [
            'username.unique' => 'Username ":input" sudah digunakan oleh calon siswa lain. Silakan pilih username lain atau kosongkan agar otomatis menggunakan nomor HP.',
            'phone.unique' => 'Nomor HP/WhatsApp ":input" sudah terdaftar pada sistem SPMB.',
            'nisn.unique' => 'NISN ":input" sudah terdaftar pada sistem SPMB.',
        ]);

        $rawUsername = !empty($validated['username']) ? trim($validated['username']) : trim($validated['phone']);
        $username = $rawUsername;
        if (empty($validated['username']) && Siswa::where('username', $username)->exists()) {
            $username = $username . '_' . rand(100, 999);
        }

        // Generate email yang terjamin 100% unik
        $cleanPrefix = \Illuminate\Support\Str::slug($username, '');
        if (empty($cleanPrefix)) {
            $cleanPrefix = 'siswa' . time();
        }
        $email = $cleanPrefix . '@siswa.smkknbi.sch.id';
        $counter = 1;
        while (Siswa::where('email', $email)->exists()) {
            $email = $cleanPrefix . $counter . '@siswa.smkknbi.sch.id';
            $counter++;
        }

        DB::beginTransaction();
        try {
            $siswa = Siswa::create([
                'name' => $validated['nama_siswa'],
                'username' => $username,
                'phone' => $validated['phone'],
                'nisn' => !empty($validated['nisn']) ? $validated['nisn'] : null,
                'email' => $email,
                'password' => Hash::make($validated['password']),
                'raw_password' => $validated['password'],
                'role' => 'murid',
                'is_active' => true,
            ]);

            $academicYear = AcademicYear::where('is_active', true)->first();
            $regNumber = Registration::generateNumber($validated['major_id'], $siswa->id, $academicYear);

            $registration = Registration::create([
                'registration_number' => $regNumber,
                'user_id' => $siswa->id,
                'academic_year_id' => $academicYear ? $academicYear->id : 1,
                'major_id' => $validated['major_id'],
                'registration_type' => 'siswa_baru',
                'status' => $validated['status'] ?? 'pending',
            ]);

            StudentBiodata::create([
                'registration_id' => $registration->id,
                'nama_lengkap' => $validated['nama_siswa'],
                'jenis_kelamin' => $validated['gender'],
                'agama' => 'Islam',
                'no_hp' => $validated['phone'],
                'email' => $email,
                'kabupaten' => 'Boyolali',
                'provinsi' => 'Jawa Tengah',
            ]);

            StudentSchool::create([
                'registration_id' => $registration->id,
                'asal_sekolah' => $validated['asal_sekolah'],
                'jenis_sekolah' => str_starts_with(strtoupper($validated['asal_sekolah']), 'MTS') ? 'MTs' : 'SMP',
                'tahun_lulus' => date('Y'),
            ]);

            StudentParent::create([
                'registration_id' => $registration->id,
                'nama_ayah' => $request->input('nama_ayah', 'Orang Tua ' . $validated['nama_siswa']),
                'nama_ibu' => $request->input('nama_ibu', 'Ibu ' . $validated['nama_siswa']),
                'no_hp_ayah' => $validated['phone'],
            ]);

            DB::commit();

            return back()->with('success', "Data Calon Siswa '{$validated['nama_siswa']}' ({$regNumber}) berhasil ditambahkan ke database SPMB!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', "Gagal menambahkan data calon siswa: " . $e->getMessage());
        }
    }

    /**
     * Update Data Calon Siswa dari Modal Edit.
     */
    public function updateStudentManual(Request $request, $id)
    {
        $registration = Registration::with(['user', 'biodata', 'school'])->findOrFail($id);
        $userId = $registration->user_id;

        $validated = $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'username' => 'nullable|string|max:100|unique:siswa,username,' . $userId,
            'password' => 'nullable|string|min:4',
            'phone' => 'required|string|max:20|unique:siswa,phone,' . $userId,
            'gender' => 'required|in:L,P',
            'asal_sekolah' => 'required|string|max:255',
            'major_id' => 'required|exists:jurusan,id',
            'status' => 'nullable|in:pending,verified,accepted,rejected',
        ], [
            'username.unique' => 'Username ":input" sudah digunakan oleh siswa lain.',
            'phone.unique' => 'Nomor HP ":input" sudah terdaftar pada akun lain.',
        ]);

        DB::beginTransaction();
        try {
            $userData = [
                'name' => $validated['nama_siswa'],
                'username' => $validated['username'] ?: $registration->user?->username,
                'phone' => $validated['phone'],
            ];

            if ($request->filled('password')) {
                $userData['password'] = Hash::make($validated['password']);
                $userData['raw_password'] = $validated['password'];
            }

            $registration->user?->update($userData);

            $regData = [
                'major_id' => $validated['major_id'],
            ];
            if (!empty($validated['status'])) {
                $regData['status'] = $validated['status'];
            }
            $registration->update($regData);

            $registration->biodata?->update([
                'nama_lengkap' => $validated['nama_siswa'],
                'jenis_kelamin' => $validated['gender'],
                'no_hp' => $validated['phone'],
            ]);

            $registration->school?->update([
                'asal_sekolah' => $validated['asal_sekolah'],
            ]);

            DB::commit();

            return back()->with('success', "Data calon siswa {$validated['nama_siswa']} ({$registration->registration_number}) berhasil diperbarui!");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', "Gagal memperbarui data calon siswa: " . $e->getMessage());
        }
    }

    /**
     * Hapus Data Calon Siswa Satuan.
     */
    public function deleteStudentManual($id)
    {
        $registration = Registration::with(['user', 'biodata', 'school', 'parent', 'documents', 'payments'])->findOrFail($id);
        $name = $registration->user?->name ?? $registration->registration_number;

        $user = $registration->user;
        $registration->biodata?->delete();
        $registration->school?->delete();
        $registration->parent?->delete();
        $registration->documents()->delete();
        $registration->payments()->delete();
        $registration->delete();

        if ($user) {
            $user->delete();
        }

        return back()->with('success', "Data calon siswa {$name} berhasil dihapus dari sistem.");
    }

    /**
     * Hapus Data Calon Siswa secara Massal (Bulk Delete).
     */
    public function bulkDeleteStudents(Request $request)
    {
        $ids = $request->input('ids', []);
        if (empty($ids)) {
            return back()->with('error', 'Silakan pilih setidaknya satu data calon siswa untuk dihapus.');
        }

        $count = 0;
        foreach ($ids as $id) {
            $registration = Registration::with(['user', 'biodata', 'school', 'parent', 'documents', 'payments'])->find($id);
            if ($registration) {
                $user = $registration->user;
                $registration->biodata?->delete();
                $registration->school?->delete();
                $registration->parent?->delete();
                $registration->documents()->delete();
                $registration->payments()->delete();
                $registration->delete();
                $user?->delete();
                $count++;
            }
        }

        return back()->with('success', "Sebanyak {$count} data calon siswa berhasil dihapus secara massal.");
    }

    /**
     * =========================================================================
     * FITUR ADMINISTRASI PEMBAYARAN KASIR / BENDAHARA SEKOLAH SPMB
     * =========================================================================
     */

    /**
     * Simpan input pembayaran tunai oleh bendahara sekolah.
     * Otomatis terverifikasi atas nama bendahara yang login dan tercatat di akun siswa.
     */
    public function storePayment(Request $request)
    {
        $validated = $request->validate([
            'registration_id' => 'required|exists:pendaftaran,id',
            'amount' => 'required|numeric|min:1000',
            'payment_date' => 'required|date',
            'payment_type' => 'nullable|string|max:150',
            'notes' => 'nullable|string|max:255',
        ]);

        $registration = Registration::with('user')->findOrFail($validated['registration_id']);

        $payment = Payment::create([
            'registration_id' => $registration->id,
            'invoice_number' => Payment::generateInvoiceNumber(),
            'payment_type' => $validated['payment_type'] ?? 'Pembayaran SPMB',
            'amount' => $validated['amount'],
            'payment_date' => $validated['payment_date'],
            'payment_method' => 'tunai',
            'status' => 'confirmed',
            'notes' => $validated['notes'] ?: 'Pembayaran tunai diterima langsung oleh Petugas Bendahara SPMB',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        $studentName = $registration->user->name ?? 'Calon Siswa';
        $nominalFormatted = 'Rp ' . number_format($payment->amount, 0, ',', '.');

        return redirect()->route('admin.dashboard', ['view' => 'administrasi'])
            ->with('success', "Pembayaran tunai {$nominalFormatted} atas nama {$studentName} ({$registration->registration_number}) berhasil disimpan dan otomatis terverifikasi oleh Bendahara!");
    }

    /**
     * Cetak Kwitansi Resmi Pembayaran Satuan.
     */
    public function printPaymentReceipt(int $id)
    {
        $payment = Payment::with(['registration.user', 'registration.major', 'registration.school', 'confirmedBy'])
            ->findOrFail($id);

        $registration = $payment->registration;
        $academicYear = AcademicYear::where('is_active', true)->first();
        $settings = SystemSetting::pluck('value', 'key');

        return view('admin.payment_receipt', compact('payment', 'registration', 'settings', 'academicYear'));
    }

    /**
     * Cetak Rekapitulasi / Kwitansi Global Seluruh Pembayaran Siswa.
     */
    public function printGlobalReceipt(int $registrationId)
    {
        $registration = Registration::with(['user', 'major', 'school', 'payments.confirmedBy'])
            ->findOrFail($registrationId);

        $confirmedPayments = $registration->payments->where('status', 'confirmed')->sortBy('payment_date');
        $totalPaid = $confirmedPayments->sum('amount');
        $academicYear = AcademicYear::where('is_active', true)->first();
        $settings = SystemSetting::pluck('value', 'key');

        return view('admin.payment_global_receipt', compact('registration', 'confirmedPayments', 'totalPaid', 'settings', 'academicYear'));
    }
}

