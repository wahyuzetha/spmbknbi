<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Manager SPMB | MySQL spmbknbi</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        mono: ['"JetBrains Mono"', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 h-screen flex flex-col overflow-hidden">

    <!-- ================= 1. TOPBAR ================= -->
    <header class="bg-[#1a2226] text-white h-14 flex items-center justify-between px-5 border-b border-black/30 flex-shrink-0 z-30">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-sm shadow">
                <i class="fa-solid fa-database"></i>
            </div>
            <div>
                <h1 class="text-sm font-extrabold text-white flex items-center gap-2">
                    Database Manager Browser
                    <span class="text-[10px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 font-mono border border-emerald-500/30">spmbknbi</span>
                </h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span class="text-[11px] text-slate-300 font-mono bg-[#222d32] px-3 py-1 rounded-md border border-white/10 hidden sm:inline-flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                127.0.0.1:3306 (MySQL)
            </span>
            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-lg transition flex items-center gap-1.5 shadow">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    </header>

    <!-- ================= 2. MAIN WORKSPACE ================= -->
    <div class="flex-1 flex overflow-hidden">

        <!-- SIDEBAR DAFTAR TABEL -->
        <aside class="w-72 bg-[#222d32] text-[#b8c7ce] flex flex-col flex-shrink-0 border-r border-black/20">
            <div class="p-3 border-b border-black/20 bg-[#1a2226]">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 text-xs">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" id="filter-table-input" onkeyup="filterTableList()" placeholder="Cari nama tabel..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-[#2c3b41] border border-black/30 rounded-md text-xs text-white focus:outline-none focus:border-emerald-500">
                </div>
                <div class="flex items-center justify-between text-[11px] text-slate-400 mt-2 px-1">
                    <span>Total: <strong>{{ count($tables) }} Tabel</strong></span>
                    <a href="{{ route('admin.database') }}" class="hover:text-white transition" title="Refresh Tabel"><i class="fa-solid fa-rotate-right"></i></a>
                </div>
            </div>

            <nav class="flex-1 overflow-y-auto custom-scroll p-2 space-y-1 text-xs" id="table-list-container">
                @foreach($tables as $t)
                    @php
                        $isSelected = ($t['name'] === $selectedTable && !request()->filled('sql'));
                    @endphp
                    <a href="{{ route('admin.database', ['table' => $t['name']]) }}" 
                       data-table-name="{{ strtolower($t['name']) }}"
                       class="table-list-item flex items-center justify-between px-3 py-2 rounded-lg transition {{ $isSelected ? 'bg-[#00a65a] text-white font-bold shadow' : 'hover:bg-[#1e282c] hover:text-white text-slate-300' }}">
                        <div class="flex items-center gap-2.5 truncate">
                            <i class="fa-solid fa-table text-xs text-slate-400 {{ $isSelected ? 'text-white' : '' }}"></i>
                            <span class="truncate font-mono">{{ $t['name'] }}</span>
                        </div>
                        <span class="text-[10px] px-1.5 py-0.5 rounded {{ $isSelected ? 'bg-black/20 text-white' : 'bg-black/30 text-slate-400' }} font-bold">
                            {{ $t['rows'] }}
                        </span>
                    </a>
                @endforeach
            </nav>
        </aside>

        <!-- AREA KONTEN TABEL & QUERY -->
        <main class="flex-1 flex flex-col bg-slate-50 overflow-hidden">

            <!-- TAB HEADER: DATA TABEL / SQL QUERY -->
            <div class="bg-white border-b border-slate-200 px-6 pt-3 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center gap-4 text-xs font-bold">
                    <button type="button" onclick="switchMainTab('data')" id="tab-btn-data" 
                            class="pb-3 border-b-2 border-emerald-600 text-emerald-700 font-extrabold flex items-center gap-2">
                        <i class="fa-solid fa-table-list"></i>
                        <span>Isi Tabel: <span class="font-mono text-slate-900 font-black text-sm">{{ $selectedTable }}</span></span>
                    </button>
                    <button type="button" onclick="switchMainTab('sql')" id="tab-btn-sql" 
                            class="pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-terminal"></i>
                        <span>Query SQL Langsung</span>
                    </button>
                </div>

                <div class="flex items-center gap-2 pb-2">
                    <button type="button" onclick="copyTable('active-data-table')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded border border-slate-300 transition">Copy</button>
                    <button type="button" onclick="exportTableToCSV('active-data-table', '{{ $selectedTable }}.csv')" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded border border-slate-300 transition">CSV</button>
                    <button type="button" onclick="window.print()" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded border border-slate-300 transition">Print</button>
                </div>
            </div>

            <!-- CONTAINER KONTEN -->
            <div class="flex-1 overflow-y-auto custom-scroll p-6 space-y-5">

                <!-- ================= TAB 1: DATA TABEL ================= -->
                <div id="section-data-tab" class="{{ request()->filled('sql') ? 'hidden' : '' }} space-y-4">
                    
                    <!-- TOOLBAR ATAS: INFO JUMLAH DATA & PENCARIAN -->
                    <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-xs text-slate-600">
                            <span class="font-bold text-slate-900">Total Baris:</span>
                            <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-extrabold font-mono text-xs">{{ number_format($totalRows) }} records</span>
                            <span class="text-slate-400">&bull;</span>
                            <span>Menampilkan 20 baris per halaman</span>
                        </div>

                        <!-- SEARCH BOX REALTIME / SERVER-SIDE -->
                        <form action="{{ route('admin.database') }}" method="GET" class="flex items-center gap-2">
                            <input type="hidden" name="table" value="{{ $selectedTable }}">
                            <div class="relative">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dalam tabel {{ $selectedTable }}..."
                                       class="px-3 py-1.5 pl-8 bg-slate-50 border border-slate-300 rounded-lg text-xs w-64 focus:bg-white focus:outline-none focus:border-emerald-500">
                                <span class="absolute inset-y-0 left-0 pl-2.5 flex items-center text-slate-400 text-xs">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </span>
                            </div>
                            <button type="submit" class="px-3 py-1.5 bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-bold rounded-lg transition">
                                Saring
                            </button>
                            @if(request()->filled('search'))
                                <a href="{{ route('admin.database', ['table' => $selectedTable]) }}" class="px-2 py-1.5 text-slate-500 hover:text-rose-600 text-xs" title="Reset Pencarian">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            @endif
                        </form>
                    </div>

                    <!-- TABEL DATA ROWS -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="overflow-x-auto custom-scroll">
                            <table id="active-data-table" class="w-full text-left text-xs border-collapse font-sans">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-200 font-extrabold text-slate-700">
                                        <th class="py-3 px-3 w-10 text-center font-mono">#</th>
                                        @foreach($columns as $col)
                                            <th class="py-3 px-3 whitespace-nowrap">
                                                <div class="flex items-center gap-1 font-mono font-bold text-slate-800">
                                                    @if($col['key'] === 'PRI')
                                                        <i class="fa-solid fa-key text-amber-500 text-[10px]" title="Primary Key"></i>
                                                    @endif
                                                    <span>{{ $col['field'] }}</span>
                                                </div>
                                                <div class="text-[9px] text-slate-400 font-normal font-mono">{{ $col['type'] }}</div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-medium">
                                    @if($rows && count($rows) > 0)
                                        @foreach($rows as $idx => $r)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="py-2.5 px-3 text-center text-slate-400 font-mono text-[11px]">{{ $rows->firstItem() + $idx }}</td>
                                                @foreach($columns as $col)
                                                    @php
                                                        $val = $r->{$col['field']} ?? null;
                                                    @endphp
                                                    <td class="py-2.5 px-3 whitespace-nowrap text-slate-700 max-w-xs truncate" title="{{ is_null($val) ? 'NULL' : $val }}">
                                                        @if(is_null($val))
                                                            <span class="text-slate-300 italic font-mono text-[10px]">NULL</span>
                                                        @elseif(strlen($val) > 40 && str_starts_with($val, '$2y$'))
                                                            <span class="font-mono text-[10px] text-purple-600 bg-purple-50 px-1 rounded" title="{{ $val }}">•••••••• (Hashed)</span>
                                                        @elseif($val === '1' && ($col['field'] === 'is_active' || str_ends_with($col['field'], '_status')))
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">1 (Aktif)</span>
                                                        @elseif($val === '0' && ($col['field'] === 'is_active' || str_ends_with($col['field'], '_status')))
                                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">0</span>
                                                        @else
                                                            <span class="font-mono text-[11px]">{{ $val }}</span>
                                                        @endif
                                                    </td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="{{ count($columns) + 1 }}" class="py-10 text-center text-slate-400">
                                                <i class="fa-regular fa-folder-open text-3xl mb-2 text-slate-300 block"></i>
                                                Tabel <strong>{{ $selectedTable }}</strong> kosong atau tidak ada data yang cocok.
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- PAGINASI -->
                        @if($rows && $rows->hasPages())
                            <div class="p-3 bg-slate-50 border-t border-slate-200">
                                {{ $rows->links() }}
                            </div>
                        @endif
                    </div>

                    <!-- STRUKTUR KOLOM TABEL -->
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-code text-emerald-600"></i> Struktur Kolom Tabel: <span class="font-mono text-slate-900">{{ $selectedTable }}</span>
                            </h3>
                            <span class="text-xs text-slate-400">{{ count($columns) }} Kolom</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 text-[11px]">
                                        <th class="py-2 px-3">Kolom</th>
                                        <th class="py-2 px-3">Tipe Data</th>
                                        <th class="py-2 px-3">Boleh Null?</th>
                                        <th class="py-2 px-3">Key</th>
                                        <th class="py-2 px-3">Default</th>
                                        <th class="py-2 px-3">Extra</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 font-mono text-[11px]">
                                    @foreach($columns as $c)
                                        <tr class="hover:bg-slate-50">
                                            <td class="py-2 px-3 font-bold text-slate-800">{{ $c['field'] }}</td>
                                            <td class="py-2 px-3 text-blue-600">{{ $c['type'] }}</td>
                                            <td class="py-2 px-3">{{ $c['null'] ? 'YES' : 'NO' }}</td>
                                            <td class="py-2 px-3 font-bold text-amber-600">{{ $c['key'] ?: '-' }}</td>
                                            <td class="py-2 px-3 text-slate-500">{{ $c['default'] ?? 'NULL' }}</td>
                                            <td class="py-2 px-3 text-slate-400">{{ $c['extra'] ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

                <!-- ================= TAB 2: QUERY SQL LANGSUNG ================= -->
                <div id="section-sql-tab" class="{{ request()->filled('sql') ? '' : 'hidden' }} space-y-4">
                    
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-terminal text-emerald-600"></i> Jalankan Query SQL (MySQL)
                            </h3>
                            <div class="flex items-center gap-1.5 text-xs">
                                <span class="text-slate-400 font-medium">Contoh Snippet:</span>
                                <button type="button" onclick="setQuery('SELECT * FROM users LIMIT 10;')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 font-mono text-[10px] text-blue-700">users</button>
                                <button type="button" onclick="setQuery('SELECT * FROM majors;')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 font-mono text-[10px] text-blue-700">majors</button>
                                <button type="button" onclick="setQuery('SELECT * FROM registrations ORDER BY id DESC LIMIT 10;')" class="px-2 py-0.5 rounded bg-slate-100 hover:bg-slate-200 font-mono text-[10px] text-blue-700">registrations</button>
                            </div>
                        </div>

                        <form action="{{ route('admin.database') }}" method="GET" class="space-y-3">
                            <input type="hidden" name="table" value="{{ $selectedTable }}">
                            <textarea name="sql" id="sql-input-area" rows="4" placeholder="Ketik query SQL di sini, contoh: SELECT * FROM users;" 
                                      class="w-full p-3 bg-slate-900 text-emerald-400 font-mono text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">{{ $customSql ?? "SELECT * FROM {$selectedTable} LIMIT 25;" }}</textarea>
                            <div class="flex justify-end gap-2">
                                <button type="reset" class="px-4 py-2 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg hover:bg-slate-200">Reset</button>
                                <button type="submit" class="px-5 py-2 bg-[#00a65a] hover:bg-[#008f4c] text-white text-xs font-bold rounded-lg shadow flex items-center gap-1.5">
                                    <i class="fa-solid fa-play text-[10px]"></i> Eksekusi SQL
                                </button>
                            </div>
                        </form>
                    </div>

                    @if($queryError)
                        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-mono">
                            <div class="font-bold mb-1 flex items-center gap-1.5 text-rose-700">
                                <i class="fa-solid fa-triangle-exclamation"></i> SQL Error:
                            </div>
                            <div>{{ $queryError }}</div>
                        </div>
                    @endif

                    @if($customResults !== null && !$queryError)
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-5 space-y-3">
                            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                                <h4 class="text-xs font-bold text-slate-800">
                                    Hasil Query ({{ count($customResults) }} baris ditemukan)
                                </h4>
                            </div>
                            <div class="overflow-x-auto custom-scroll">
                                @if(count($customResults) > 0)
                                    @php
                                        $firstRow = (array) $customResults[0];
                                        $customCols = array_keys($firstRow);
                                    @endphp
                                    <table class="w-full text-left text-xs border-collapse font-mono">
                                        <thead>
                                            <tr class="bg-slate-100 border-b border-slate-200 font-bold text-slate-700 text-[11px]">
                                                @foreach($customCols as $c)
                                                    <th class="py-2.5 px-3 whitespace-nowrap">{{ $c }}</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 font-medium">
                                            @foreach($customResults as $resRow)
                                                <tr class="hover:bg-slate-50">
                                                    @foreach($customCols as $c)
                                                        @php $cellVal = ((array)$resRow)[$c] ?? null; @endphp
                                                        <td class="py-2 px-3 whitespace-nowrap text-slate-700 max-w-xs truncate" title="{{ is_null($cellVal) ? 'NULL' : $cellVal }}">
                                                            {{ is_null($cellVal) ? 'NULL' : $cellVal }}
                                                        </td>
                                                    @endforeach
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="p-6 text-center text-slate-400">
                                        Query berhasil dijalankan, 0 baris dikembalikan.
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                </div>

            </div>

        </main>

    </div>

    <!-- SCRIPT UTILITIES -->
    <script>
        function switchMainTab(tab) {
            const dataTab = document.getElementById('section-data-tab');
            const sqlTab = document.getElementById('section-sql-tab');
            const btnData = document.getElementById('tab-btn-data');
            const btnSql = document.getElementById('tab-btn-sql');

            if (tab === 'data') {
                dataTab.classList.remove('hidden');
                sqlTab.classList.add('hidden');
                btnData.className = 'pb-3 border-b-2 border-emerald-600 text-emerald-700 font-extrabold flex items-center gap-2';
                btnSql.className = 'pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2';
            } else {
                dataTab.classList.add('hidden');
                sqlTab.classList.remove('hidden');
                btnData.className = 'pb-3 border-b-2 border-transparent text-slate-500 hover:text-slate-800 flex items-center gap-2';
                btnSql.className = 'pb-3 border-b-2 border-emerald-600 text-emerald-700 font-extrabold flex items-center gap-2';
            }
        }

        function filterTableList() {
            const filter = document.getElementById('filter-table-input').value.toLowerCase();
            const items = document.querySelectorAll('.table-list-item');
            items.forEach(el => {
                const name = el.getAttribute('data-table-name');
                if (name.indexOf(filter) > -1) {
                    el.style.display = 'flex';
                } else {
                    el.style.display = 'none';
                }
            });
        }

        function setQuery(sql) {
            document.getElementById('sql-input-area').value = sql;
            switchMainTab('sql');
        }

        function copyTable(tableId) {
            const table = document.getElementById(tableId);
            if (!table) return;
            let text = "";
            for (let r of table.rows) {
                let row = [];
                for (let c of r.cells) {
                    row.push(c.innerText.trim());
                }
                text += row.join("\t") + "\n";
            }
            navigator.clipboard.writeText(text).then(() => {
                alert("Data tabel berhasil disalin ke clipboard!");
            });
        }

        function exportTableToCSV(tableId, filename) {
            const table = document.getElementById(tableId);
            if (!table) return;
            let csv = [];
            for (let r of table.rows) {
                let row = [];
                for (let c of r.cells) {
                    row.push('"' + c.innerText.replace(/"/g, '""').trim() + '"');
                }
                csv.push(row.join(","));
            }
            const csvFile = new Blob([csv.join("\n")], { type: "text/csv" });
            const downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>

</body>
</html>
