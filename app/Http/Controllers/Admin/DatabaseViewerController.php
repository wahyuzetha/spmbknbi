<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DatabaseViewerController extends Controller
{
    /**
     * Tampilkan Database Manager SPMB di Browser.
     */
    public function index(Request $request)
    {
        // 1. Ambil seluruh daftar tabel beserta metadata dari MySQL / SQLite
        $driver = DB::getDriverName();
        $tables = [];

        if ($driver === 'mysql') {
            $tableStatusRaw = DB::select('SHOW TABLE STATUS');
            foreach ($tableStatusRaw as $t) {
                $exactRows = (int) $t->Rows;
                try {
                    $exactRows = DB::table($t->Name)->count();
                } catch (\Throwable $e) {
                    // Gunakan fallback dari SHOW TABLE STATUS jika gagal
                }

                $tables[] = [
                    'name' => $t->Name,
                    'rows' => $exactRows,
                    'engine' => $t->Engine,
                    'size' => round(($t->Data_length + $t->Index_length) / 1024, 2).' KB',
                    'collation' => $t->Collation,
                ];
            }
        } else {
            foreach (Schema::getTables() as $t) {
                $tName = is_array($t) ? ($t['name'] ?? '') : ($t->name ?? '');
                if (! $tName) {
                    continue;
                }
                $tables[] = [
                    'name' => $tName,
                    'rows' => DB::table($tName)->count(),
                    'engine' => strtoupper($driver),
                    'size' => isset($t['size']) ? round($t['size'] / 1024, 2).' KB' : '-',
                    'collation' => $t['collation'] ?? '-',
                ];
            }
        }

        // 2. Tabel terpilih (default: 'pendaftaran' atau 'siswa')
        $availableTableNames = array_column($tables, 'name');
        $defaultTable = in_array('pendaftaran', $availableTableNames)
            ? 'pendaftaran'
            : (in_array('siswa', $availableTableNames) ? 'siswa' : ($availableTableNames[0] ?? ''));
        $selectedTable = $request->input('table', $defaultTable);

        $columns = [];
        $rows = null;
        $totalRows = 0;
        $queryError = null;
        $customResults = null;
        $customSql = $request->input('sql');

        // 3. Eksekusi Custom SQL jika diminta
        if ($request->filled('sql')) {
            try {
                $sqlTrim = trim($request->input('sql'));
                $customResults = DB::select($sqlTrim);
            } catch (\Exception $e) {
                $queryError = $e->getMessage();
            }
        }

        // 4. Ambil struktur dan isi tabel terpilih
        if ($selectedTable && in_array($selectedTable, $availableTableNames)) {
            if ($driver === 'mysql') {
                $columnsRaw = DB::select("SHOW FULL COLUMNS FROM `{$selectedTable}`");
                foreach ($columnsRaw as $col) {
                    $columns[] = [
                        'field' => $col->Field,
                        'type' => $col->Type,
                        'null' => $col->Null === 'YES',
                        'key' => $col->Key,
                        'default' => $col->Default,
                        'extra' => $col->Extra,
                    ];
                }
            } else {
                foreach (Schema::getColumns($selectedTable) as $col) {
                    $columns[] = [
                        'field' => $col['name'],
                        'type' => $col['type'],
                        'null' => (bool) ($col['nullable'] ?? false),
                        'key' => ($col['auto_increment'] ?? false) ? 'PRI' : '',
                        'default' => $col['default'] ?? null,
                        'extra' => ($col['auto_increment'] ?? false) ? 'auto_increment' : '',
                    ];
                }
            }

            $query = DB::table($selectedTable);

            // Filter pencarian
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($columns, $search) {
                    foreach ($columns as $idx => $c) {
                        if ($idx === 0) {
                            $q->where($c['field'], 'LIKE', "%{$search}%");
                        } else {
                            $q->orWhere($c['field'], 'LIKE', "%{$search}%");
                        }
                    }
                });
            }

            $totalRows = $query->count();
            $rows = $query->paginate(20)->withQueryString();
        }

        // 5. Ringkasan data untuk fitur Reset Data Siswa & Pembayaran (Pergantian Tahun Ajaran Baru)
        $resetSummary = [
            'siswa' => Schema::hasTable('siswa') ? DB::table('siswa')->count() : 0,
            'pendaftaran' => Schema::hasTable('pendaftaran') ? DB::table('pendaftaran')->count() : 0,
            'biodata_siswa' => Schema::hasTable('biodata_siswa') ? DB::table('biodata_siswa')->count() : 0,
            'orang_tua_siswa' => Schema::hasTable('orang_tua_siswa') ? DB::table('orang_tua_siswa')->count() : 0,
            'asal_sekolah_siswa' => Schema::hasTable('asal_sekolah_siswa') ? DB::table('asal_sekolah_siswa')->count() : 0,
            'dokumen_siswa' => Schema::hasTable('dokumen_siswa') ? DB::table('dokumen_siswa')->count() : 0,
            'pembayaran' => Schema::hasTable('pembayaran') ? DB::table('pembayaran')->count() : 0,
            'pengeluaran' => Schema::hasTable('pengeluaran') ? DB::table('pengeluaran')->count() : 0,
        ];

        $academicYears = Schema::hasTable('tahun_ajaran')
            ? AcademicYear::orderBy('name', 'desc')->get()
            : collect();
        $activeAcademicYear = $academicYears->firstWhere('is_active', true);

        return view('admin.database', compact(
            'tables',
            'selectedTable',
            'columns',
            'rows',
            'totalRows',
            'customResults',
            'customSql',
            'queryError',
            'resetSummary',
            'academicYears',
            'activeAcademicYear'
        ));
    }

    /**
     * Reset Data Siswa dan/atau Data Pembayaran ke kondisi default awal/kosong (untuk pergantian Tahun Ajaran Baru).
     */
    public function resetData(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'reset_scope' => 'required|in:all,students_only,payments_only',
            'confirm_text' => 'required|string',
            'new_academic_year' => 'nullable|string|max:50',
        ]);

        if (strtoupper(trim($validated['confirm_text'])) !== 'RESET') {
            return back()->with('error', 'Konfirmasi gagal: Ketik kata RESET dengan benar untuk mengosongkan data.');
        }

        $scope = $validated['reset_scope'];
        $studentTables = [
            'dokumen_siswa',
            'asal_sekolah_siswa',
            'orang_tua_siswa',
            'biodata_siswa',
            'pendaftaran',
            'siswa',
        ];
        $paymentTables = [
            'pembayaran',
            'pengeluaran',
        ];

        $tablesToReset = match ($scope) {
            'all' => array_merge($paymentTables, $studentTables),
            'students_only' => $studentTables,
            'payments_only' => $paymentTables,
        };

        try {
            // Hapus file fisik dokumen siswa & bukti pembayaran jika ikut di-reset
            if (in_array('dokumen_siswa', $tablesToReset, true) && Schema::hasTable('dokumen_siswa')) {
                $docPaths = DB::table('dokumen_siswa')->pluck('file_path')->filter()->all();
                if (! empty($docPaths)) {
                    Storage::disk('public')->delete($docPaths);
                }
            }

            if (in_array('pembayaran', $tablesToReset, true) && Schema::hasTable('pembayaran')) {
                $proofPaths = DB::table('pembayaran')->pluck('proof_file')->filter()->all();
                if (! empty($proofPaths)) {
                    Storage::disk('public')->delete($proofPaths);
                }
            }

            // Kosongkan tabel dan kembalikan AUTO_INCREMENT ke 1 (default awal/kosong)
            Schema::disableForeignKeyConstraints();
            $clearedTables = [];
            foreach ($tablesToReset as $tableName) {
                if (Schema::hasTable($tableName)) {
                    DB::table($tableName)->truncate();
                    $clearedTables[] = $tableName;
                }
            }
            Schema::enableForeignKeyConstraints();

            // Jika Admin juga mengisi pergantian Tahun Ajaran Baru sekaligus
            $yearMessage = '';
            $newYearName = trim((string) ($validated['new_academic_year'] ?? ''));
            if ($newYearName !== '' && Schema::hasTable('tahun_ajaran')) {
                AcademicYear::query()->update(['is_active' => false]);
                $academicYear = AcademicYear::updateOrCreate(
                    ['name' => $newYearName],
                    [
                        'is_active' => true,
                        'registration_start' => now()->startOfYear()->toDateString(),
                        'registration_end' => now()->endOfYear()->toDateString(),
                        'description' => 'Tahun Ajaran Aktif SPMB '.$newYearName,
                    ]
                );
                SystemSetting::set('academic_year', $academicYear->name, 'spmb');
                $yearMessage = " Tahun Ajaran Aktif telah diperbarui ke {$academicYear->name}.";
            }

            SystemSetting::clearCache();

            $scopeLabel = match ($scope) {
                'all' => 'Seluruh Data Calon Siswa & Data Pembayaran/Keuangan',
                'students_only' => 'Seluruh Data Calon Siswa & Berkas Pendaftaran',
                'payments_only' => 'Seluruh Data Pembayaran & Pengeluaran Kas',
            };

            return redirect()
                ->route('admin.database', ['table' => $request->input('current_table', 'pendaftaran')])
                ->with('success', "Reset berhasil! {$scopeLabel} (".implode(', ', $clearedTables).") telah dikembalikan ke kondisi default awal/kosong (0 data).{$yearMessage}");
        } catch (\Throwable $e) {
            Schema::enableForeignKeyConstraints();

            return back()->with('error', 'Terjadi kesalahan saat mereset data: '.$e->getMessage());
        }
    }
}
