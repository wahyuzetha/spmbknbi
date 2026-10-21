<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DatabaseViewerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Bendahara\DashboardController as BendaharaDashboardController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Murid\DashboardController as MuridDashboardController;
use App\Http\Controllers\Panitia\DashboardController as PanitiaDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Landing Page & Detail Jurusan
Route::get('/', [LandingController::class, 'index'])->name('home');
Route::get('/jurusan/{slug}', [LandingController::class, 'majorDetail'])->name('jurusan.detail');

// 2. Authentication & Registration Routes
Route::get('/daftar', [AuthController::class, 'showRegisterForm'])->name('daftar');
Route::post('/daftar', [AuthController::class, 'register'])->name('daftar.store');

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 3. Portal Murid / Calon Siswa (Protected by Auth & Role: murid)
Route::middleware(['auth', 'role:murid'])->prefix('murid')->name('murid.')->group(function () {
    Route::get('/dashboard', [MuridDashboardController::class, 'index'])->name('dashboard');
    Route::post('/upload-document', [MuridDashboardController::class, 'uploadDocument'])->name('upload.document');
    Route::get('/cetak-kartu', [MuridDashboardController::class, 'printCard'])->name('print.card');
    Route::get('/pembayaran/{id}/kwitansi', [MuridDashboardController::class, 'printPaymentReceipt'])->name('payment.receipt');
    Route::get('/pembayaran/rekap', [MuridDashboardController::class, 'printGlobalReceipt'])->name('payment.globalReceipt');
});

// Shortcut redirect /dashboard based on role
Route::middleware('auth')->get('/dashboard', function () {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->isBendahara()) {
        return redirect()->route('bendahara.dashboard');
    }
    if ($user->isPanitia()) {
        return redirect()->route('panitia.dashboard');
    }

    return redirect()->route('murid.dashboard');
})->name('dashboard');

// 4. Portal Admin SPMB (Protected by Auth & Role: admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/registrations/{id}/status', [AdminDashboardController::class, 'updateStatus'])->name('registrations.status');
    Route::post('/panitia/store', [AdminDashboardController::class, 'storePanitia'])->name('panitia.store');
    Route::get('/export-pendaftar', [AdminDashboardController::class, 'export'])->name('export');
    Route::post('/settings/update', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
    Route::post('/majors/{id}/quota', [AdminDashboardController::class, 'updateMajorQuota'])->name('majors.quota');

    // CRUD Data Jurusan (Sesuai Gambar Referensi User)
    Route::post('/sekolah/jurusan/store', [AdminDashboardController::class, 'storeMajor'])->name('sekolah.jurusan.store');
    Route::post('/sekolah/jurusan/{id}/update', [AdminDashboardController::class, 'updateMajor'])->name('sekolah.jurusan.update');
    Route::post('/sekolah/jurusan/{id}/industry-class', [AdminDashboardController::class, 'updateIndustryClass'])->name('sekolah.jurusan.updateIndustryClass');
    Route::post('/sekolah/jurusan/{id}/delete', [AdminDashboardController::class, 'deleteMajor'])->name('sekolah.jurusan.delete');
    Route::post('/sekolah/jurusan/{id}/content', [AdminDashboardController::class, 'updateMajorContent'])->name('sekolah.jurusan.updateContent');
    Route::post('/sekolah/jurusan/{id}/gallery/upload', [AdminDashboardController::class, 'uploadMajorGallery'])->name('sekolah.jurusan.uploadGallery');
    Route::post('/sekolah/jurusan/gallery/{galleryId}/delete', [AdminDashboardController::class, 'deleteMajorGallery'])->name('sekolah.jurusan.deleteGallery');

    // CRUD Data Pengguna (Sesuai Gambar Referensi User)
    Route::post('/sekolah/pengguna/store', [AdminDashboardController::class, 'storeUser'])->name('sekolah.pengguna.store');
    Route::post('/sekolah/pengguna/{id}/update', [AdminDashboardController::class, 'updateUser'])->name('sekolah.pengguna.update');
    Route::post('/sekolah/pengguna/{id}/delete', [AdminDashboardController::class, 'deleteUser'])->name('sekolah.pengguna.delete');
    Route::post('/sekolah/pengguna/bulk-delete', [AdminDashboardController::class, 'bulkDeleteUser'])->name('sekolah.pengguna.bulkDelete');

    // FITUR MENU DATA SPMB: DATA CALON SISWA (Export Excel, Cetak All, Cetak Kartu, CRUD)
    Route::get('/spmb/calon-siswa/export-excel', [AdminDashboardController::class, 'exportExcel'])->name('spmb.exportExcel');
    Route::get('/spmb/calon-siswa/cetak-all', [AdminDashboardController::class, 'printAllStudents'])->name('spmb.printAll');
    Route::get('/spmb/calon-siswa/{id}/cetak-kartu', [AdminDashboardController::class, 'printStudentCard'])->name('spmb.printCard');
    Route::post('/spmb/calon-siswa/store', [AdminDashboardController::class, 'storeStudentManual'])->name('spmb.student.store');
    Route::post('/spmb/calon-siswa/{id}/update', [AdminDashboardController::class, 'updateStudentManual'])->name('spmb.student.update');
    Route::post('/spmb/calon-siswa/{id}/delete', [AdminDashboardController::class, 'deleteStudentManual'])->name('spmb.student.delete');
    Route::post('/spmb/calon-siswa/bulk-delete', [AdminDashboardController::class, 'bulkDeleteStudents'])->name('spmb.student.bulkDelete');

    // FITUR MENU PENGATURAN SPMB & SEKOLAH TERPADU (Tahun Ajaran, Logo, Hotline, TTD Ketua, Kop Surat)
    Route::post('/settings/spmb/update', [AdminDashboardController::class, 'updateSpmbSettings'])->name('settings.spmb.update');
    Route::post('/academic-years/store', [AdminDashboardController::class, 'storeAcademicYear'])->name('academicYears.store');
    Route::post('/academic-years/{id}/activate', [AdminDashboardController::class, 'activateAcademicYear'])->name('academicYears.activate');
    Route::post('/academic-years/{id}/delete', [AdminDashboardController::class, 'deleteAcademicYear'])->name('academicYears.delete');

    // FITUR ADMINISTRASI & PEMBAYARAN TUNAI SPMB OLEH BENDAHARA
    Route::post('/administrasi/pembayaran/store', [AdminDashboardController::class, 'storePayment'])->name('administrasi.payment.store');
    Route::get('/administrasi/pembayaran/{id}/kwitansi', [AdminDashboardController::class, 'printPaymentReceipt'])->name('administrasi.payment.receipt');
    Route::get('/administrasi/pembayaran/rekap/{registrationId}', [AdminDashboardController::class, 'printGlobalReceipt'])->name('administrasi.payment.globalReceipt');
    Route::get('/administrasi/pembayaran/export-excel', [AdminDashboardController::class, 'exportPaymentsExcel'])->name('administrasi.payment.exportExcel');
    Route::post('/administrasi/pembayaran/{registrationId}/change-major', [AdminDashboardController::class, 'updateStudentMajor'])->name('administrasi.payment.updateMajor');

    // FITUR PENGELUARAN KAS SPMB (UANG CALON SISWA PENDAFTAR)
    Route::post('/administrasi/pengeluaran/store', [AdminDashboardController::class, 'storeExpense'])->name('administrasi.expense.store');
    Route::post('/administrasi/pengeluaran/{id}/update', [AdminDashboardController::class, 'updateExpense'])->name('administrasi.expense.update');
    Route::post('/administrasi/pengeluaran/{id}/delete', [AdminDashboardController::class, 'deleteExpense'])->name('administrasi.expense.delete');
});

// 5. Portal Panitia SPMB (Protected by Auth & Role: panitia)
Route::middleware(['auth', 'role:panitia'])->prefix('panitia')->name('panitia.')->group(function () {
    Route::get('/dashboard', [PanitiaDashboardController::class, 'index'])->name('dashboard');
    Route::post('/verify/{id}', [PanitiaDashboardController::class, 'verifyRegistration'])->name('verify');
    Route::post('/document/{id}/status', [PanitiaDashboardController::class, 'updateDocumentStatus'])->name('document.status');
    Route::post('/settings/update', [PanitiaDashboardController::class, 'updateSettings'])->name('settings.update');

    // Export & Cetak untuk Panitia
    Route::get('/spmb/calon-siswa/export-excel', [PanitiaDashboardController::class, 'exportExcel'])->name('spmb.exportExcel');
    Route::get('/spmb/calon-siswa/cetak-all', [PanitiaDashboardController::class, 'printAllStudents'])->name('spmb.printAll');
    Route::get('/spmb/calon-siswa/{id}/cetak-kartu', [PanitiaDashboardController::class, 'printStudentCard'])->name('spmb.printCard');
    Route::post('/spmb/calon-siswa/{id}/update', [AdminDashboardController::class, 'updateStudentManual'])->name('spmb.student.update');
    Route::get('/administrasi/pembayaran/export-excel', [AdminDashboardController::class, 'exportPaymentsExcel'])->name('administrasi.payment.exportExcel');
    Route::post('/administrasi/pembayaran/{registrationId}/change-major', [AdminDashboardController::class, 'updateStudentMajor'])->name('administrasi.payment.updateMajor');
});

// 6. Portal Bendahara SPMB (Protected by Auth & Role: bendahara)
Route::middleware(['auth', 'role:bendahara'])->prefix('bendahara')->name('bendahara.')->group(function () {
    Route::get('/dashboard', [BendaharaDashboardController::class, 'index'])->name('dashboard');
    Route::post('/administrasi/pembayaran/store', [BendaharaDashboardController::class, 'storePayment'])->name('administrasi.payment.store');
    Route::get('/administrasi/pembayaran/{id}/kwitansi', [BendaharaDashboardController::class, 'printPaymentReceipt'])->name('administrasi.payment.receipt');
    Route::get('/administrasi/pembayaran/rekap/{registrationId}', [BendaharaDashboardController::class, 'printGlobalReceipt'])->name('administrasi.payment.globalReceipt');
    Route::get('/administrasi/pembayaran/export-excel', [BendaharaDashboardController::class, 'exportPaymentsExcel'])->name('administrasi.payment.exportExcel');
    Route::post('/administrasi/pembayaran/{registrationId}/change-major', [BendaharaDashboardController::class, 'updateStudentMajor'])->name('administrasi.payment.updateMajor');
    Route::post('/administrasi/pengeluaran/store', [BendaharaDashboardController::class, 'storeExpense'])->name('administrasi.expense.store');
    Route::post('/administrasi/pengeluaran/{id}/update', [BendaharaDashboardController::class, 'updateExpense'])->name('administrasi.expense.update');
    Route::post('/administrasi/pengeluaran/{id}/delete', [BendaharaDashboardController::class, 'deleteExpense'])->name('administrasi.expense.delete');
});

// 7. Database Viewer Browser (Langsung Dibuka di Browser)
Route::get('/admin/database', [DatabaseViewerController::class, 'index'])->name('admin.database');
Route::get('/database', [DatabaseViewerController::class, 'index'])->name('database.viewer');
