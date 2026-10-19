<?php

namespace App\Http\Controllers\Bendahara;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Expense;
use App\Models\Major;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\StudentSchool;
use App\Models\SystemSetting;
use App\Services\PaymentExportService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman utama dashboard & rekapitulasi pembayaran Bendahara SPMB.
     */
    public function index(Request $request)
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $majors = Major::where('is_active', true)->withCount('registrations')->get();

        // 1. Data Pembayaran Administrasi (Dengan Grouping Global Siswa & Multi-Transaksi)
        $allPayments = Payment::with(['registration.user', 'registration.major', 'confirmedBy'])
            ->latest('payment_date')
            ->latest('id')
            ->get();

        // Mengelompokkan pembayaran berdasarkan pendaftar (registration_id)
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
                'payments' => $items,
            ];
        })->values();

        // Metrik Keuangan Kas Masuk Administrasi SPMB
        $totalKasMasuk = (float) Payment::where('status', 'confirmed')->sum('amount');
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

        // 2. Data Pengeluaran Kas SPMB & Rekonsiliasi Saldo Kas
        $allExpenses = Expense::with('creator')->latest('expense_date')->latest('id')->get();
        $totalPengeluaran = (float) Expense::sum('amount');
        $saldoKas = (float) ($totalKasMasuk - $totalPengeluaran);

        // 3. Pengaturan Sistem & Logo Sekolah
        $settings = SystemSetting::pluck('value', 'key');
        $schoolLogo = $settings['school_logo'] ?? null;

        $currentView = $request->get('view', 'administrasi');

        return view('bendahara.dashboard', compact(
            'academicYear',
            'majors',
            'allPayments',
            'groupedPayments',
            'totalKasMasuk',
            'totalSiswaBayar',
            'totalTransaksi',
            'multiTransactionCount',
            'candidatesForPayment',
            'allExpenses',
            'totalPengeluaran',
            'saldoKas',
            'settings',
            'schoolLogo',
            'currentView'
        ));
    }

    /**
     * Input transaksi pembayaran tunai baru oleh Bendahara SPMB.
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
            'notes' => $validated['notes'] ?: 'Pembayaran tunai diterima langsung oleh Bendahara SPMB',
            'confirmed_by' => auth()->id(),
            'confirmed_at' => now(),
        ]);

        $studentName = $registration->user->name ?? 'Calon Siswa';
        $nominalFormatted = 'Rp ' . number_format($payment->amount, 0, ',', '.');

        return redirect()->route('bendahara.dashboard', ['view' => 'administrasi'])
            ->with('success', "Pembayaran tunai {$nominalFormatted} atas nama {$studentName} ({$registration->registration_number}) berhasil disimpan dan otomatis terverifikasi oleh Bendahara!")
            ->with('last_payment_id', $payment->id);
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

    /**
     * Export Excel data administrasi pembayaran kasir / bendahara SPMB.
     */
    public function exportPaymentsExcel(Request $request, PaymentExportService $exportService)
    {
        return $exportService->exportExcel($request->get('search'));
    }

    /**
     * Ubah jurusan calon siswa dari meja administrasi bendahara.
     */
    public function updateStudentMajor(Request $request, int $registrationId)
    {
        $validated = $request->validate([
            'major_id' => 'required|exists:jurusan,id',
        ]);

        $registration = Registration::with(['user', 'major', 'documents'])->findOrFail($registrationId);
        $newMajor = Major::findOrFail($validated['major_id']);
        $oldNumber = $registration->registration_number;

        $newNumber = $registration->updateMajorWithNumberChange($newMajor);

        $studentName = $registration->user->name ?? 'Calon Siswa';
        $industryInfo = !empty($newMajor->industry_class) ? " ({$newMajor->industry_class})" : '';

        $numberChangeInfo = ($oldNumber !== $newNumber)
            ? ", dan nomor pendaftaran otomatis diperbarui dari {$oldNumber} menjadi {$newNumber}"
            : "";

        return back()->with('success', "Pilihan jurusan calon siswa {$studentName} berhasil diperbarui menjadi {$newMajor->name}{$industryInfo}{$numberChangeInfo}!");
    }

    /**
     * Simpan data pengeluaran kas SPMB.
     */
    public function storeExpense(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:100',
            'expense_date' => 'required|date',
            'category' => 'nullable|string|max:100',
            'recipient' => 'nullable|string|max:255',
            'description' => 'required|string',
        ]);

        $expense = Expense::create([
            'expense_code' => Expense::generateExpenseCode(),
            'title' => $validated['title'],
            'category' => !empty($validated['category']) ? $validated['category'] : 'Operasional SPMB',
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'recipient' => !empty($validated['recipient']) ? $validated['recipient'] : 'Panitia / Vendor',
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('bendahara.dashboard', ['view' => 'pengeluaran'])
            ->with('success', "Pengeluaran kas SPMB sebesar Rp " . number_format($expense->amount, 0, ',', '.') . " untuk '{$expense->title}' berhasil dicatat.");
    }

    /**
     * Update data pengeluaran kas SPMB.
     */
    public function updateExpense(Request $request, int $id)
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:100',
            'expense_date' => 'required|date',
            'category' => 'nullable|string|max:100',
            'recipient' => 'nullable|string|max:255',
            'description' => 'required|string',
        ]);

        $expense->update([
            'title' => $validated['title'],
            'category' => !empty($validated['category']) ? $validated['category'] : $expense->category,
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'description' => $validated['description'],
            'recipient' => !empty($validated['recipient']) ? $validated['recipient'] : $expense->recipient,
        ]);

        return redirect()
            ->route('bendahara.dashboard', ['view' => 'pengeluaran'])
            ->with('success', "Data pengeluaran '{$expense->title}' berhasil diperbarui.");
    }

    /**
     * Hapus data pengeluaran kas SPMB.
     */
    public function deleteExpense(int $id)
    {
        $expense = Expense::findOrFail($id);
        $title = $expense->title;
        $expense->delete();

        return redirect()
            ->route('bendahara.dashboard', ['view' => 'pengeluaran'])
            ->with('success', "Catatan pengeluaran '{$title}' telah dihapus.");
    }
}
