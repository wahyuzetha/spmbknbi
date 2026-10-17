<?php

namespace App\Http\Controllers\Panitia;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\StudentDocument;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama verifikasi berkas panitia SPMB.
     */
    public function index(Request $request)
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $majors = Major::where('is_active', true)->withCount('registrations')->get();

        // 1. Statistik 4 Kartu Metrik Utama (Sesuai Gambar Referensi)
        $totalRegistrations = Registration::count();
        $acceptedCount = Registration::where('status', 'accepted')->count();
        $pendingTestCount = Registration::where('status', 'pending')->count();
        $paidCount = Payment::where('status', 'confirmed')->orWhere('status', 'success')->count();

        $totalQueue = $pendingTestCount;
        $totalVerified = Registration::where('status', 'verified')->count();
        $totalAccepted = $acceptedCount;
        $totalNeedsFix = Registration::where('status', 'rejected')->count();

        // 2. Query Pendaftar
        $query = Registration::with(['user', 'major', 'biodata', 'parents', 'school', 'documents', 'verifier'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default filter: utamakan yang pending jika tidak ada filter
            $query->orderByRaw("CASE status WHEN 'pending' THEN 1 WHEN 'verified' THEN 2 WHEN 'accepted' THEN 3 ELSE 4 END");
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

        $registrations = $query->paginate(12)->withQueryString();

        // 3. Data Pembayaran
        $payments = Payment::with('registration.user')->latest()->paginate(10);

        // 4. Pengaturan Sistem
        $settings = SystemSetting::pluck('value', 'key');

        $currentView = $request->get('view', 'dashboard');
        $profileTab = $request->get('tab', 'profile');

        return view('panitia.dashboard', compact(
            'academicYear',
            'majors',
            'totalRegistrations',
            'acceptedCount',
            'pendingTestCount',
            'paidCount',
            'totalQueue',
            'totalVerified',
            'totalAccepted',
            'totalNeedsFix',
            'registrations',
            'payments',
            'settings',
            'currentView',
            'profileTab'
        ));
    }

    /**
     * Verifikasi pendaftaran calon siswa oleh Panitia.
     */
    public function verifyRegistration(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,accepted,rejected,pending',
            'verification_notes' => 'nullable|string|max:500',
        ]);

        $registration = Registration::findOrFail($id);
        $registration->update([
            'status' => $request->status,
            'verification_notes' => $request->verification_notes,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $statusLabel = match ($request->status) {
            'verified' => 'TERVERIFIKASI',
            'accepted' => 'DITERIMA',
            'rejected' => 'PERLU PERBAIKAN / DITOLAK',
            default => 'MENUNGGU VERIFIKASI',
        };

        return back()->with('success', "Pendaftar {$registration->registration_number} ({$registration->user->name}) telah diubah statusnya menjadi {$statusLabel}.");
    }

    /**
     * Verifikasi individual berkas dokumen pendaftar.
     */
    public function updateDocumentStatus(Request $request, $documentId)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected,pending',
            'notes' => 'nullable|string|max:255',
        ]);

        $document = StudentDocument::findOrFail($documentId);
        $document->update([
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return back()->with('success', "Status dokumen " . ucwords(str_replace('_', ' ', $document->document_type)) . " diperbarui.");
    }

    /**
     * Update Pengaturan Profil Sekolah oleh Panitia.
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
}
