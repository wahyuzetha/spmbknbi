<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Major;
use App\Models\Registration;
use App\Models\SystemSetting;

class LandingController extends Controller
{
    public function index()
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $majors = Major::where('is_active', true)->get();
        $settings = collect(SystemSetting::allCached());

        $statusCounts = Registration::selectRaw('status, COUNT(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        $stats = [
            'total_majors' => $majors->count(),
            'total_quota' => $majors->sum('quota'),
            'total_registrants' => (int) $statusCounts->sum(),
            'total_accepted' => (int) ($statusCounts['accepted'] ?? 0),
        ];

        return view('landing', compact('academicYear', 'majors', 'settings', 'stats'));
    }

    /**
     * Halaman Detail Jurusan Lengkap untuk Calon Siswa (Foto Praktikum, Video, & Keunggulan).
     */
    public function majorDetail(string $slug)
    {
        $major = Major::with('gallery')
            ->where('slug', $slug)
            ->orWhere('id', $slug)
            ->orWhere('code', $slug)
            ->firstOrFail();

        $allMajors = Major::where('is_active', true)
            ->where('id', '!=', $major->id)
            ->get();

        $academicYear = AcademicYear::where('is_active', true)->first();
        $settings = collect(SystemSetting::allCached());

        return view('jurusan.detail', compact('major', 'allMajors', 'academicYear', 'settings'));
    }
}
