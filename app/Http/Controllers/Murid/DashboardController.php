<?php

namespace App\Http\Controllers\Murid;

use App\Http\Controllers\Controller;
use App\Models\StudentDocument;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard calon siswa.
     */
    public function index()
    {
        $user = Auth::user();
        $registration = $user->registration()
            ->with(['academicYear', 'major', 'secondChoiceMajor', 'biodata', 'parents', 'school', 'documents', 'payments'])
            ->first();

        // Hitung kelengkapan berkas yang diunggah
        $requiredDocs = ['kartu_keluarga', 'akta_kelahiran', 'ijazah_skl'];
        $uploadedDocs = $registration ? $registration->documents->pluck('document_type')->toArray() : [];
        $completedRequiredCount = count(array_intersect($requiredDocs, $uploadedDocs));
        $progressPercentage = round(($completedRequiredCount / count($requiredDocs)) * 100);

        $settings = SystemSetting::pluck('value', 'key');

        return view('murid.dashboard', compact('user', 'registration', 'progressPercentage', 'settings', 'uploadedDocs'));
    }

    /**
     * Upload dokumen persyaratan pendaftaran.
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'document_type' => 'required|in:kartu_keluarga,akta_kelahiran,ijazah_skl,raport,pas_foto,kartu_kip_pkh,lainnya',
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:3072', // Max 3MB
        ]);

        $user = Auth::user();
        $registration = $user->registration;

        if (! $registration) {
            return back()->with('error', 'Data pendaftaran tidak ditemukan.');
        }

        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();
        $fileSize = round($file->getSize() / 1024); // KB
        $mimeType = $file->getClientMimeType();

        // Simpan ke storage public
        $path = $file->store('documents/' . $registration->registration_number, 'public');

        // Update or Create data dokumen
        StudentDocument::updateOrCreate(
            [
                'registration_id' => $registration->id,
                'document_type' => $request->input('document_type'),
            ],
            [
                'file_path' => $path,
                'original_filename' => $originalFilename,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'status' => 'pending', // Reset status jika re-upload
                'notes' => null,
            ]
        );

        return back()->with('success', 'Dokumen ' . ucwords(str_replace('_', ' ', $request->input('document_type'))) . ' berhasil diunggah.');
    }

    /**
     * Cetak Kartu Bukti Pendaftaran Siswa Baru (Siap Cetak / Print).
     */
    public function printCard()
    {
        $user = Auth::user();
        $registration = $user->registration()
            ->with(['academicYear', 'major', 'biodata', 'parents', 'parent', 'school', 'documents'])
            ->firstOrFail();

        // Catat waktu cetak
        if (! $registration->printed_card_at) {
            $registration->update(['printed_card_at' => now()]);
        }

        $settings = SystemSetting::pluck('value', 'key');

        return view('murid.cetak-kartu', compact('user', 'registration', 'settings'));
    }
}
