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
            ],
        ];
    }

    public function map($row): array
    {
        return [
            $row['nama_perawatan'] ?? '-',
            $row['jumlah_terjual'] ?? 0,
            $row['total_revenue'] ?? 0,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ===== 1. INSERT HEADER LAPORAN =====
                // Insert 4 rows before data
                $sheet->insertNewRowBefore(1, 4);

                // Row 1: Judul
                $sheet->setCellValue('A1', 'LAPORAN JENIS PERAWATAN');
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E79'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Row 2: Tanggal Cetak
                $sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d/m/Y H:i') . ' WIB');
                $sheet->mergeCells('A2:C2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8EEF4'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Row 3: Filter Info
                $filterText = 'Filter: ';
                $parts = [];
                if (!empty($this->filterInfo['klinik'])) {
                    $parts[] = 'Klinik [' . $this->filterInfo['klinik'] . ']';
                } else {
                    $parts[] = 'Klinik [Semua]';
                }
                if (!empty($this->filterInfo['jenis_perawatan'])) {
                    $parts[] = 'Jenis Perawatan [' . $this->filterInfo['jenis_perawatan'] . ']';
                } else {
                    $parts[] = 'Jenis Perawatan [Semua]';
                }
                if (!empty($this->filterInfo['tanggal_mulai']) && !empty($this->filterInfo['tanggal_akhir'])) {
                    $parts[] = 'Periode [' . \Carbon\Carbon::parse($this->filterInfo['tanggal_mulai'])->format('d/m/Y')
                        . ' - ' . \Carbon\Carbon::parse($this->filterInfo['tanggal_akhir'])->format('d/m/Y') . ']';
                }
                $filterText .= implode(' | ', $parts);

                $sheet->setCellValue('A3', $filterText);
                $sheet->mergeCells('A3:C3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '333333']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F7FA'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Row 4: empty spacer
                $sheet->getRowDimension(4)->setRowHeight(8);

                // ===== 2. STYLE HEADER KOLOM (row 5) =====
                $headerRow = 5;
                $sheet->getStyle('A' . $headerRow . ':C' . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '2E75B6'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1F4E79']],
                    ],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(30);

                // ===== 3. STYLE DATA ROWS =====
                $lastDataRow = $sheet->getHighestRow();
                $dataStartRow = $headerRow + 1;

                // Alternating row colors
                if ($lastDataRow >= $dataStartRow) {
                    for ($r = $dataStartRow; $r <= $lastDataRow; $r++) {
                        $bgColor = ($r % 2 == 0) ? 'F2F7FB' : 'FFFFFF';
                        $sheet->getStyle('A' . $r . ':C' . $r)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $bgColor],
                            ],
                            'borders' => [
                                'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']],
                            ],
                            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        ]);
                    }

                    // Format Rupiah untuk kolom total_revenue (kolom C)
                    $sheet->getStyle('C' . $dataStartRow . ':C' . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0');

                    // Alignment kanan untuk angka di kolom B dan C
                    $sheet->getStyle('B' . $dataStartRow . ':C' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Center untuk nama perawatan bisa tetap kiri (default)
                }

                // ===== 4. FOOTER: TOTAL =====
                $footerRow = $lastDataRow + 1;
                $sheet->insertNewRowBefore($footerRow, 1);

                $totalTerjual = 0;
                $totalRevenue = 0;
                foreach ($this->data as $row) {
                    $totalTerjual += $row['jumlah_terjual'] ?? 0;
                    $totalRevenue += $row['total_revenue'] ?? 0;
                }

                $sheet->setCellValue('A' . $footerRow, 'TOTAL');
                $sheet->mergeCells('A' . $footerRow . ':A' . $footerRow);
                $sheet->setCellValue('B' . $footerRow, $totalTerjual);
                $sheet->setCellValue('C' . $footerRow, $totalRevenue);

                $sheet->getStyle('A' . $footerRow . ':C' . $footerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '1F4E79']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D6E4F0'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1F4E79']],
                    ],
                ]);
                $sheet->getStyle('C' . $footerRow)->getNumberFormat()->setFormatCode('"Rp"#,##0');

                // ===== 5. FREEZE PANE =====
                $sheet->freezePane('A' . ($headerRow + 1));
            },
        ];
    }
}
