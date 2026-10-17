<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseViewerController extends Controller
{
    /**
     * Tampilkan Database Manager SPMB di Browser.
     */
    public function index(Request $request)
    {
        // 1. Ambil seluruh daftar tabel beserta metadata dari MySQL
        $tableStatusRaw = DB::select('SHOW TABLE STATUS');
        $tables = [];

        foreach ($tableStatusRaw as $t) {
            $tables[] = [
                'name' => $t->Name,
                'rows' => (int) $t->Rows,
                'engine' => $t->Engine,
                'size' => round(($t->Data_length + $t->Index_length) / 1024, 2) . ' KB',
                'collation' => $t->Collation,
            ];
        }

        // 2. Tabel terpilih (default: 'users')
        $availableTableNames = array_column($tables, 'name');
        $selectedTable = $request->input('table', in_array('users', $availableTableNames) ? 'users' : ($availableTableNames[0] ?? ''));

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

        return view('admin.database', compact(
            'tables',
            'selectedTable',
            'columns',
            'rows',
            'totalRows',
            'customResults',
            'customSql',
            'queryError'
        ));
    }
}
