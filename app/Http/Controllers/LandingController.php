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
        $settings = SystemSetting::pluck('value', 'key');

        $stats = [
            'total_majors' => $majors->count(),
            'total_quota' => $majors->sum('quota'),
            'total_registrants' => Registration::count(),
            'total_accepted' => Registration::where('status', 'accepted')->count(),
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
        $settings = SystemSetting::pluck('value', 'key');

        return view('jurusan.detail', compact('major', 'allMajors', 'academicYear', 'settings'));
    }
}
