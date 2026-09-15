<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class JenisPerawatanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithTitle
{
    protected $data;
    protected $filterInfo;

    public function __construct($data, $filterInfo = [])
    {
        $this->data = $data;
        $this->filterInfo = $filterInfo;
    }

    public function title(): string
    {
        return 'Laporan Jenis Perawatan';
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return [
            [
                'Nama Perawatan',
                'Jumlah Terjual',
                'Total Revenue',
                'Biaya Admin',      // ✅ BARU
                'Grand Total',      // ✅ BARU
            ],
        ];
    }

    public function map($row): array
    {
        return [
            $row['nama_perawatan'] ?? '-',
            $row['jumlah_terjual'] ?? 0,
            $row['total_revenue'] ?? 0,
            $row['total_admin'] ?? 0,     // ✅ BARU
            $row['grand_total'] ?? 0,     // ✅ BARU
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ===== 1. INSERT HEADER LAPORAN =====
                $sheet->insertNewRowBefore(1, 4);

                // Row 1: Judul
                $sheet->setCellValue('A1', 'LAPORAN JENIS PERAWATAN');
                $sheet->mergeCells('A1:E1'); // ✅ E1 karena sekarang 5 kolom
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E79'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Row 2: Tanggal Cetak
                $sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d/m/Y H:i') . ' WIB');
                $sheet->mergeCells('A2:E2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8EEF4'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Row 3: Filter Info
                $filterText = 'Filter: ';
                $parts = [];
                $parts[] = !empty($this->filterInfo['klinik']) && $this->filterInfo['klinik'] !== 'Semua'
                    ? 'Klinik [' . $this->filterInfo['klinik'] . ']'
                    : 'Klinik [Semua]';

                $parts[] = !empty($this->filterInfo['jenis_perawatan']) && $this->filterInfo['jenis_perawatan'] !== 'Semua'
                    ? 'Jenis Perawatan [' . $this->filterInfo['jenis_perawatan'] . ']'
                    : 'Jenis Perawatan [Semua]';

                if (!empty($this->filterInfo['tanggal_mulai']) && !empty($this->filterInfo['tanggal_akhir'])) {
                    $parts[] = 'Periode [' . \Carbon\Carbon::parse($this->filterInfo['tanggal_mulai'])->format('d/m/Y')
                        . ' - ' . \Carbon\Carbon::parse($this->filterInfo['tanggal_akhir'])->format('d/m/Y') . ']';
                }
                $filterText .= implode(' | ', $parts);

                $sheet->setCellValue('A3', $filterText);
                $sheet->mergeCells('A3:E3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '333333']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F7FA'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);

                // Row 4: Spacer
                $sheet->getRowDimension(4)->setRowHeight(8);

                // ===== 2. STYLE HEADER KOLOM (row 5) =====
                $headerRow = 5;
                $sheet->getStyle('A' . $headerRow . ':E' . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2E75B6'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '1F4E79'],
                        ],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(30);

                // ===== 3. STYLE DATA ROWS =====
                $lastDataRow = $sheet->getHighestRow();
                $dataStartRow = $headerRow + 1;

                if ($lastDataRow >= $dataStartRow) {
                    for ($r = $dataStartRow; $r <= $lastDataRow; $r++) {
                        $bgColor = ($r % 2 == 0) ? 'F2F7FB' : 'FFFFFF';
                        $sheet->getStyle('A' . $r . ':E' . $r)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $bgColor],
                            ],
                            'borders' => [
                                'allBorders' => [
                                    'borderStyle' => Border::BORDER_THIN,
                                    'color' => ['rgb' => 'D0D7DE'],
                                ],
                            ],
                            'alignment' => [
                                'vertical' => Alignment::VERTICAL_CENTER,
                                'wrapText' => true,
                            ],
                        ]);
                    }

                    // ✅ Format Rupiah untuk kolom C, D, E (revenue, admin, grand total)
                    $sheet->getStyle('C' . $dataStartRow . ':E' . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0');

                    // ✅ Alignment kanan untuk semua angka (B, C, D, E)
                    $sheet->getStyle('B' . $dataStartRow . ':E' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // ✅ Warna khusus untuk kolom Biaya Admin (oranye)
                    $sheet->getStyle('D' . $dataStartRow . ':D' . $lastDataRow)->applyFromArray([
                        'font' => ['color' => ['rgb' => 'D97706']],
                    ]);

                    // ✅ Warna khusus untuk kolom Grand Total (hijau tebal)
                    $sheet->getStyle('E' . $dataStartRow . ':E' . $lastDataRow)->applyFromArray([
                        'font' => ['bold' => true, 'color' => ['rgb' => '15803D']],
                    ]);
                }

                // ===== 4. FOOTER: TOTAL =====
                $footerRow = $lastDataRow + 1;
                $sheet->insertNewRowBefore($footerRow, 1);

                $totalTerjual = 0;
                $totalRevenue = 0;
                $totalAdmin = 0;
                $grandTotal = 0;

                foreach ($this->data as $row) {
                    $totalTerjual += $row['jumlah_terjual'] ?? 0;
                    $totalRevenue += $row['total_revenue'] ?? 0;
                    $totalAdmin += $row['total_admin'] ?? 0;
                    $grandTotal += $row['grand_total'] ?? 0;
                }

                $sheet->setCellValue('A' . $footerRow, 'TOTAL');
                $sheet->mergeCells('A' . $footerRow . ':A' . $footerRow);
                $sheet->setCellValue('B' . $footerRow, $totalTerjual);
                $sheet->setCellValue('C' . $footerRow, $totalRevenue);
                $sheet->setCellValue('D' . $footerRow, $totalAdmin);
                $sheet->setCellValue('E' . $footerRow, $grandTotal);

                $sheet->getStyle('A' . $footerRow . ':E' . $footerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F4E79']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D6E4F0'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '1F4E79'],
                        ],
                    ],
                ]);

                // ✅ Label TOTAL di kolom A center
                $sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // ✅ Format Rupiah untuk kolom C, D, E di footer
                $sheet->getStyle('C' . $footerRow . ':E' . $footerRow)
                    ->getNumberFormat()
                    ->setFormatCode('"Rp"#,##0');

                // ✅ Grand Total footer pakai highlight
                $sheet->getStyle('E' . $footerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '15803D']],
                ]);

                // ===== 5. FREEZE PANE =====
                $sheet->freezePane('A' . ($headerRow + 1));

                // ===== 6. SET COLUMN WIDTHS =====
                $sheet->getColumnDimension('A')->setWidth(40); // Nama Perawatan
                $sheet->getColumnDimension('B')->setWidth(15); // Jumlah Terjual
                $sheet->getColumnDimension('C')->setWidth(20); // Total Revenue
                $sheet->getColumnDimension('D')->setWidth(20); // Biaya Admin
                $sheet->getColumnDimension('E')->setWidth(22); // Grand Total

                // ===== 7. PRINT SETTINGS =====
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
}
