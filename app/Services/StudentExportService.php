<?php

namespace App\Services;

use App\Models\Registration;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudentExportService
{
    /**
     * Generate and download styled Excel (.xlsx) file containing all student registration data.
     */
    public function exportAll(string $status = null, $majorId = null, string $search = null): StreamedResponse
    {
        $query = Registration::with([
            'user', 
            'major', 
            'majorChoice2', 
            'biodata', 
            'parent', 
            'school', 
            'academicYear', 
            'verifier'
        ])->latest();

        if ($status) {
            $query->where('status', $status);
        }

        if ($majorId) {
            $query->where('major_id', $majorId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('registration_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('nisn', 'like', "%{$search}%");
                  });
            });
        }

        $registrations = $query->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Calon Siswa');

        // Disable gridline hiding
        $sheet->setShowGridLines(true);

        // KOP DOKUMEN RESMI DI BAGIAN ATAS
        $sheet->setCellValue('A1', 'DATA LENGKAP PENDAFTARAN CALON SISWA BARU (SPMB)');
        $sheet->setCellValue('A2', 'SMK KARYA NUGRAHA BOYOLALI - TAHUN AJARAN 2026/2027');
        $sheet->setCellValue('A3', 'Waktu Unduh: ' . date('d F Y, H:i:s') . ' WIB | Total Siswa: ' . $registrations->count() . ' Siswa');

        $sheet->mergeCells('A1:BC1');
        $sheet->mergeCells('A2:BC2');
        $sheet->mergeCells('A3:BC3');

        $sheet->getStyle('A1')->getFont()->setSize(16)->setBold(true)->getColor()->setRGB('004D40');
        $sheet->getStyle('A2')->getFont()->setSize(12)->setBold(true)->getColor()->setRGB('00796B');
        $sheet->getStyle('A3')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('616161');

        // DAFTAR HEADER KOLOM LENGKAP
        $headers = [
            'No',
            'No. Pendaftar',
            'Tanggal Daftar',
            'Username Akun',
            'Password Akun',
            'NISN',
            'NIK Siswa',
            'No. Kartu Keluarga',
            'Nama Lengkap Siswa',
            'L/P',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'No. HP / WhatsApp',
            'Email',
            'Alamat Jalan / RT / RW',
            'Desa / Kelurahan',
            'Kecamatan',
            'Kabupaten / Kota',
            'Provinsi',
            'Kode Pos',
            'Anak Ke',
            'Jumlah Saudara',
            'Tinggi Badan (cm)',
            'Berat Badan (kg)',
            'Riwayat Penyakit',
            'Asal Sekolah (SMP/MTs)',
            'NPSN Sekolah Asal',
            'Jenis Sekolah',
            'Status Sekolah',
            'Tahun Kelulusan',
            'Nilai Rata-rata Rapor',
            'Nama Ayah',
            'Status Ayah',
            'NIK Ayah',
            'Pekerjaan Ayah',
            'Pendidikan Ayah',
            'Penghasilan Ayah',
            'No. HP Ayah',
            'Nama Ibu',
            'Status Ibu',
            'NIK Ibu',
            'Pekerjaan Ibu',
            'Pendidikan Ibu',
            'Penghasilan Ibu',
            'No. HP Ibu',
            'Nama Wali',
            'Hubungan Wali',
            'Pekerjaan Wali',
            'No. HP Wali',
            'Alamat Wali',
            'Pilihan Jurusan 1',
            'Kelas Industri (Jurusan 1)',
            'Pilihan Jurusan 2',
            'Jalur Pendaftaran',
            'Rekomendasi Oleh',
            'Status Pendaftaran',
            'Catatan Verifikasi',
        ];

        $headerRow = 5;
        $colIndex = 1;
        foreach ($headers as $headerText) {
            $sheet->setCellValueExplicit([$colIndex, $headerRow], $headerText, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $colIndex++;
        }

        // STYLING HEADER
        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $headerRange = "A{$headerRow}:{$lastCol}{$headerRow}";

        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '00A65A'], // Hijau KNBI
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '008548'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(32);

        // ISI DATA SISWA KE ROWS
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($registrations as $r) {
            $user = $r->user;
            $bio = $r->biodata;
            $parent = $r->parent;
            $school = $r->school;

            $statusText = match ($r->status) {
                'accepted' => 'DITERIMA',
                'verified' => 'TERVERIFIKASI',
                'rejected' => 'DITOLAK',
                default => 'PENDING',
            };

            $rowData = [
                $no++,
                (string) $r->registration_number,
                $r->created_at ? $r->created_at->format('d/m/Y H:i') : '-',
                (string) ($user->username ?? $user->phone ?? '-'),
                (string) ($user->raw_password ?? '-'),
                (string) ($user->nisn ?? $bio->nisn ?? '-'),
                (string) ($bio->nik ?? '-'),
                (string) ($bio->no_kk ?? '-'),
                $bio->nama_lengkap ?? $user->name ?? '-',
                $bio->jenis_kelamin ?? 'L',
                $bio->tempat_lahir ?? '-',
                $bio->tanggal_lahir ? date('d/m/Y', strtotime($bio->tanggal_lahir)) : '-',
                $bio->agama ?? 'Islam',
                (string) ($bio->no_hp ?? $user->phone ?? '-'),
                $bio->email ?? $user->email ?? '-',
                $bio->alamat_jalan ?? '-',
                $bio->desa ?? '-',
                $bio->kecamatan ?? '-',
                $bio->kabupaten ?? 'Boyolali',
                $bio->provinsi ?? 'Jawa Tengah',
                (string) ($bio->kode_pos ?? '-'),
                $bio->anak_ke ?? '-',
                $bio->jumlah_saudara ?? '-',
                $bio->tinggi_badan ?? '-',
                $bio->berat_badan ?? '-',
                $bio->riwayat_penyakit ?? 'Tidak Ada',
                $school->asal_sekolah ?? '-',
                (string) ($school->npsn_sekolah ?? '-'),
                $school->jenis_sekolah ?? 'SMP',
                $school->status_sekolah ?? 'Negeri',
                (string) ($school->tahun_lulus ?? '-'),
                $school->nilai_rata_raport ? number_format($school->nilai_rata_raport, 2) : '-',
                $parent->nama_ayah ?? '-',
                $parent->status_ayah ?? 'Hidup',
                (string) ($parent->nik_ayah ?? '-'),
                $parent->pekerjaan_ayah ?? '-',
                $parent->pendidikan_ayah ?? '-',
                $parent->penghasilan_ayah ?? '-',
                (string) ($parent->no_hp_ayah ?? '-'),
                $parent->nama_ibu ?? '-',
                $parent->status_ibu ?? 'Hidup',
                (string) ($parent->nik_ibu ?? '-'),
                $parent->pekerjaan_ibu ?? '-',
                $parent->pendidikan_ibu ?? '-',
                $parent->penghasilan_ibu ?? '-',
                (string) ($parent->no_hp_ibu ?? '-'),
                $parent->nama_wali ?? '-',
                $parent->hubungan_wali ?? '-',
                $parent->pekerjaan_wali ?? '-',
                (string) ($parent->no_hp_wali ?? '-'),
                $parent->alamat_wali ?? '-',
                $r->major->name ?? '-',
                $r->major->industry_class ?? '-',
                $r->majorChoice2->name ?? '-',
                str_replace('_', ' ', strtoupper($r->registration_type ?? 'siswa_baru')),
                $r->recommendation_by ?? '-',
                $statusText,
                $r->verification_notes ?? '-',
            ];

            $cIdx = 1;
            foreach ($rowData as $val) {
                // Gunakan Explicit string untuk nomor/id agar tidak diubah excel ke format scientific
                $sheet->setCellValueExplicit([$cIdx, $currentRow], (string) $val, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $cIdx++;
            }

            // Zebra styling
            if ($currentRow % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F9FAFB');
            }

            $sheet->getRowDimension($currentRow)->setRowHeight(20);
            $currentRow++;
        }

        // BORDER TABEL DATA
        $lastRow = $currentRow - 1;
        if ($lastRow >= $headerRow + 1) {
            $sheet->getStyle("A" . ($headerRow + 1) . ":{$lastCol}{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E5E7EB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Center align untuk No, Tanggal, No Pendaftar, Gender, Status
            $sheet->getStyle("A" . ($headerRow + 1) . ":A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B" . ($headerRow + 1) . ":B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C" . ($headerRow + 1) . ":C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J" . ($headerRow + 1) . ":J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("BE" . ($headerRow + 1) . ":BE{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        // AUTO SIZE UNTUK SELURUH KOLOM
        for ($col = 1; $col <= count($headers); $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // FREEZE PANE AGAR HEADER SELALU TERLIHAT SAAT SCROLL
        $sheet->freezePane('C6');

        $fileName = 'Data_Calon_Siswa_SPMB_SMK_KNBI_' . date('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
