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

class JenisPerawatanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithTitle
{
    protected $data;
    protected $filterInfo;
    protected $totalBiayaAdmin;

    // ✅ Tambah parameter $totalBiayaAdmin
    public function __construct($data, $filterInfo = [], $totalBiayaAdmin = 0)
    {
        $this->data = $data;
        $this->filterInfo = $filterInfo;
        $this->totalBiayaAdmin = $totalBiayaAdmin;
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
            ['Nama Perawatan', 'Jumlah Terjual', 'Total Revenue'],
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

                // ===== 1. HEADER LAPORAN (4 baris) =====
                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue('A1', 'LAPORAN JENIS PERAWATAN');
                $sheet->mergeCells('A1:C1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E79']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                $sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d/m/Y H:i') . ' WIB');
                $sheet->mergeCells('A2:C2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EEF4']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Filter info
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
                $filterText = 'Filter: ' . implode(' | ', $parts);
                $sheet->setCellValue('A3', $filterText);
                $sheet->mergeCells('A3:C3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '333333']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F7FA']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->getRowDimension(4)->setRowHeight(8);

                // ===== 2. HEADER KOLOM (row 5) =====
                $headerRow = 5;
                $sheet->getStyle('A' . $headerRow . ':C' . $headerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E75B6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1F4E79']]],
                ]);
                $sheet->getRowDimension($headerRow)->setRowHeight(30);

                // ===== 3. DATA ROWS =====
                $lastDataRow = $sheet->getHighestRow();
                $dataStartRow = $headerRow + 1;

                if ($lastDataRow >= $dataStartRow) {
                    for ($r = $dataStartRow; $r <= $lastDataRow; $r++) {
                        $bgColor = ($r % 2 == 0) ? 'F2F7FB' : 'FFFFFF';
                        $sheet->getStyle('A' . $r . ':C' . $r)->applyFromArray([
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']]],
                        ]);
                    }

                    $sheet->getStyle('C' . $dataStartRow . ':C' . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0');

                    $sheet->getStyle('B' . $dataStartRow . ':C' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                }

                // ===== 4. FOOTER — 3 BARIS (Subtotal, Admin, Grand Total) =====
                $totalTerjual = 0;
                $totalRevenue = 0;
                foreach ($this->data as $row) {
                    $totalTerjual += $row['jumlah_terjual'] ?? 0;
                    $totalRevenue += $row['total_revenue'] ?? 0;
                }
                $grandTotal = $totalRevenue + $this->totalBiayaAdmin;

                // Baris Subtotal Revenue
                $subRow = $lastDataRow + 1;
                $sheet->setCellValue('A' . $subRow, 'Subtotal Revenue');
                $sheet->mergeCells('A' . $subRow . ':B' . $subRow);
                $sheet->setCellValue('C' . $subRow, $totalRevenue);
                $sheet->getStyle('A' . $subRow . ':C' . $subRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '0D6EFD']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E7F1FF']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1F4E79']]],
                ]);
                $sheet->getStyle('A' . $subRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C' . $subRow)->getNumberFormat()->setFormatCode('"Rp"#,##0');

                // Baris Total Biaya Admin
                $adminRow = $subRow + 1;
                $sheet->setCellValue('A' . $adminRow, 'Total Biaya Admin');
                $sheet->mergeCells('A' . $adminRow . ':B' . $adminRow);
                $sheet->setCellValue('C' . $adminRow, $this->totalBiayaAdmin);
                $sheet->getStyle('A' . $adminRow . ':C' . $adminRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'F59E0B']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF8E1']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1F4E79']]],
                ]);
                $sheet->getStyle('A' . $adminRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C' . $adminRow)->getNumberFormat()->setFormatCode('"Rp"#,##0');

                // Baris Grand Total
                $grandRow = $adminRow + 1;
                $sheet->setCellValue('A' . $grandRow, 'GRAND TOTAL');
                $sheet->mergeCells('A' . $grandRow . ':B' . $grandRow);
                $sheet->setCellValue('C' . $grandRow, $grandTotal);
                $sheet->getStyle('A' . $grandRow . ':C' . $grandRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '198754']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '14532D']]],
                ]);
                $sheet->getStyle('A' . $grandRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                $sheet->getStyle('C' . $grandRow)->getNumberFormat()->setFormatCode('"Rp"#,##0');
                $sheet->getRowDimension($grandRow)->setRowHeight(30);

                // ===== 5. FREEZE & LAYOUT =====
                $sheet->freezePane('A' . ($headerRow + 1));
                $sheet->getColumnDimension('A')->setWidth(40);
                $sheet->getColumnDimension('B')->setWidth(15);
                $sheet->getColumnDimension('C')->setWidth(22);
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
            },
        ];
    }
}
