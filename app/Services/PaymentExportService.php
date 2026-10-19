<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\SystemSetting;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentExportService
{
    /**
     * Generate and download styled Excel (.xlsx) file containing payment administration data.
     */
    public function exportExcel(string $search = null): StreamedResponse
    {
        $allPayments = Payment::with([
            'registration.user',
            'registration.major',
            'confirmedBy',
        ])
        ->latest('payment_date')
        ->latest('id')
        ->get();

        // Mengelompokkan per calon siswa (registration_id) sesuai tabel administrasi global
        $grouped = $allPayments->groupBy('registration_id')->map(function ($items, $registrationId) {
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

        if ($search) {
            $s = strtolower($search);
            $grouped = $grouped->filter(function ($item) use ($s) {
                $regNo = strtolower($item->registration->registration_number ?? '');
                $name = strtolower($item->registration->user->name ?? '');
                $major = strtolower($item->registration->major->name ?? '');
                return str_contains($regNo, $s) || str_contains($name, $s) || str_contains($major, $s);
            })->values();
        }

        $schoolName = SystemSetting::get('school_name', 'SMK KARYA NUGRAHA BOYOLALI');
        $totalKas = $grouped->sum('total_amount');
        $totalSiswa = $grouped->count();
        $totalTransaksi = $allPayments->count();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Rekap Pembayaran SPMB');
        $sheet->setShowGridLines(true);

        // KOP / JUDUL DOKUMEN RESMI
        $sheet->setCellValue('A1', 'REKAPITULASI GLOBAL ADMINISTRASI PEMBAYARAN SPMB');
        $sheet->setCellValue('A2', strtoupper($schoolName));
        $sheet->setCellValue('A3', 'Waktu Unduh: ' . date('d F Y, H:i') . ' WIB | Total Siswa Bayar: ' . $totalSiswa . ' Siswa | Total Kas Masuk: Rp ' . number_format($totalKas, 0, ',', '.'));

        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');

        $sheet->getStyle('A1')->getFont()->setSize(14)->setBold(true)->getColor()->setRGB('004D40');
        $sheet->getStyle('A2')->getFont()->setSize(11)->setBold(true)->getColor()->setRGB('00796B');
        $sheet->getStyle('A3')->getFont()->setSize(9)->setItalic(true)->getColor()->setRGB('4B5563');

        // RINGKASAN METRIK KEUANGAN (ROW 5)
        $sheet->setCellValue('A5', 'RINGKASAN:');
        $sheet->setCellValue('B5', 'Total Pemasukan Kas: Rp ' . number_format($totalKas, 0, ',', '.'));
        $sheet->setCellValue('D5', 'Jumlah Calon Siswa: ' . $totalSiswa . ' Siswa');
        $sheet->setCellValue('F5', 'Jumlah Transaksi Kasir: ' . $totalTransaksi . ' Transaksi');
        $sheet->mergeCells('B5:C5');
        $sheet->mergeCells('D5:E5');
        $sheet->mergeCells('F5:H5');

        $sheet->getStyle('A5:H5')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E293B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(5)->setRowHeight(22);

        // DAFTAR HEADER KOLOM TABEL
        // Sesuai permintaan pengguna: Kontak (No HP) dan nama SMP dihilangkan
        $headers = [
            'No',
            'No. Pendaftar',
            'Nama Calon Siswa',
            'Pilihan Jurusan',
            'Kelas Industri',
            'Total Pembayaran (Rp)',
            'Transaksi',
            'Tgl Terakhir Bayar',
            'Petugas Penerima / Kasir',
            'Status Pembayaran',
        ];

        $headerRow = 7;
        $colIndex = 1;
        foreach ($headers as $headerText) {
            $sheet->setCellValueExplicit([$colIndex, $headerRow], $headerText, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $colIndex++;
        }

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
                'startColor' => ['rgb' => '008548'], // Hijau Tua Elegan KNBI
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '006030'],
                ],
            ],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(28);

        // ISI DATA KE BARIS
        $currentRow = $headerRow + 1;
        $no = 1;

        foreach ($grouped as $item) {
            $reg = $item->registration;
            $userSiswa = $reg?->user;
            $major = $reg?->major;

            $dateFormatted = $item->last_payment_date ? date('d/m/Y', strtotime($item->last_payment_date)) : '-';
            $petugas = $item->last_confirmed_by->name ?? 'Bendahara SPMB';
            $txInfo = $item->is_multi ? $item->transaction_count . 'x Transaksi' : '1x Setoran';

            $sheet->setCellValueExplicit([1, $currentRow], (string) $no++, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC);
            $sheet->setCellValueExplicit([2, $currentRow], (string) ($reg->registration_number ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([3, $currentRow], (string) strtoupper($userSiswa->name ?? 'Calon Siswa'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([4, $currentRow], (string) ($major->name ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([5, $currentRow], (string) ($major->industry_class ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            
            // Nominal sebagai angka murni dengan Number Format Rupiah
            $sheet->setCellValue([6, $currentRow], (float) $item->total_amount);
            $sheet->getStyle("F{$currentRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');

            $sheet->setCellValueExplicit([7, $currentRow], (string) $txInfo, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([8, $currentRow], (string) $dateFormatted, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([9, $currentRow], (string) $petugas, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([10, $currentRow], 'TERVERIFIKASI', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            // Zebra striping
            if ($currentRow % 2 === 0) {
                $sheet->getStyle("A{$currentRow}:{$lastCol}{$currentRow}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F8FAFC');
            }

            $sheet->getRowDimension($currentRow)->setRowHeight(22);
            $currentRow++;
        }

        $lastDataRow = $currentRow - 1;

        // BORDER DATA & FORMAT CELL ALIGNMENT
        if ($lastDataRow >= $headerRow + 1) {
            $sheet->getStyle("A" . ($headerRow + 1) . ":{$lastCol}{$lastDataRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CBD5E1'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Alignment spesifik
            $sheet->getStyle("A" . ($headerRow + 1) . ":A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B" . ($headerRow + 1) . ":B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E" . ($headerRow + 1) . ":E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F" . ($headerRow + 1) . ":F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle("G" . ($headerRow + 1) . ":G{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H" . ($headerRow + 1) . ":H{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J" . ($headerRow + 1) . ":J{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Warna hijau terang pada status
            $sheet->getStyle("J" . ($headerRow + 1) . ":J{$lastDataRow}")->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '166534']],
            ]);
        }

        // BARIS TOTAL DI PALING BAWAH
        $totalRow = $currentRow;
        $sheet->setCellValue("A{$totalRow}", 'TOTAL KESELURUHAN');
        $sheet->mergeCells("A{$totalRow}:E{$totalRow}");
        $sheet->setCellValue("F{$totalRow}", "=SUM(F" . ($headerRow + 1) . ":F{$lastDataRow})");
        $sheet->getStyle("F{$totalRow}")->getNumberFormat()->setFormatCode('"Rp" #,##0');
        $sheet->setCellValue("G{$totalRow}", "{$totalSiswa} Siswa");
        $sheet->mergeCells("G{$totalRow}:J{$totalRow}");

        $sheet->getStyle("A{$totalRow}:{$lastCol}{$totalRow}")->applyFromArray([
            'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '0F172A']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            'borders' => [
                'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
                'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '0F172A']],
                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']],
            ],
        ]);
        $sheet->getStyle("A{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("F{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle("G{$totalRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($totalRow)->setRowHeight(26);

        // AUTO-SIZE SEMUA KOLOM
        for ($col = 1; $col <= count($headers); $col++) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // FREEZE PANE AGAR HEADER TETAP TERLIHAT SAAT SCROLL
        $sheet->freezePane('C8');

        $fileName = 'Rekap_Administrasi_Pembayaran_SPMB_KNBI_' . date('Ymd_His') . '.xlsx';

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
