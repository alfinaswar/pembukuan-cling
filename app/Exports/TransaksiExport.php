<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithEvents, WithTitle
{
    protected $data;
    protected $filterInfo;
    protected $rowNumber = 0;

    public function __construct($data, $filterInfo = [])
    {
        $this->data = $data;
        $this->filterInfo = $filterInfo;
    }

    public function title(): string
    {
        return 'Laporan Transaksi';
    }

    public function collection()
    {
        return $this->data;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 18,  // Tanggal
            'C' => 18,  // Kode Transaksi
            'D' => 24,  // Nama Cabang
            'E' => 24,  // Nama Pasien
            'F' => 14,  // Jenis Pasien
            'G' => 38,  // Layanan
            'H' => 18,  // Total Bayar
            'I' => 12,  // Shift
            'J' => 28,  // Metode Bayar
            'K' => 20,  // Dokter
            'L' => 20,  // Perawat
            'M' => 20,  // Resepsionis
        ];
    }

    public function headings(): array
    {
        return [
            [
                'No',
                'Tanggal',
                'Kode Transaksi',
                'Nama Cabang',
                'Nama Pasien',
                'Jenis Pasien',
                'Layanan',
                'Total Bayar',
                'Shift',
                'Metode Bayar',
                'Dokter',
                'Perawat',
                'Resepsionis',
            ],
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        // ===== Format Tanggal =====
        $tanggalRaw = $row->Tanggal ?? $row->tanggal ?? $row->created_at ?? '';
        $tanggal = $tanggalRaw ? \Carbon\Carbon::parse($tanggalRaw)->format('d/m/Y H:i') : '-';

        $kodeTransaksi = $row->Kode ?? '-';
        $namaPasien = $row->NamaPasien ?? '-';
        $jenisPasien = $row->JenisPasien ?? '-';
        $shift = $row->getShift->Nama ?? $row->Shift ?? '-';

        // Metode Pembayaran
        if (isset($row->getMetodePembayaran) && $row->getMetodePembayaran->count()) {
            $metodeList = [];
            foreach ($row->getMetodePembayaran as $p) {
                $namaMetode = $p->getMetodeBayar->Nama ?? '-';
                $nominal = 'Rp ' . number_format($p->Nominal ?? 0, 0, ',', '.');
                $metodeList[] = $namaMetode . ' (' . $nominal . ')';
            }
            $metodeBayar = implode(', ', $metodeList);
        } else {
            $metodeBayar = '-';
        }

        $dokter = $row->getDokter->name ?? $row->getDokter->Nama ?? '-';
        $perawat = $row->getPerawat->name ?? $row->getPerawat->Nama ?? '-';
        $resepsionis = $row->getResepsionis->name ?? $row->getResepsionis->Nama ?? '-';
        $totalBayar = (float) ($row->total ?? $row->TotalBayar ?? 0);

        // Layanan (ringkas per transaksi dalam 1 baris)
        $layananItems = [];
        if (isset($row->TransaksiDetail) && $row->TransaksiDetail->count()) {
            foreach ($row->TransaksiDetail as $detail) {
                $layananNama = optional($detail->MasterJenisPerawatan)->Nama
                    ?? $detail->nama_layanan
                    ?? $detail->Nama
                    ?? '-';
                $harga = (float) ($detail->Biaya ?? $detail->Harga ?? 0);
                $qty = (int) ($detail->Qty ?? $detail->Jumlah ?? 1);

                $itemStr = $layananNama;
                if ($qty > 1) {
                    $itemStr .= ' (x' . $qty . ')';
                }
                $itemStr .= ' [Rp ' . number_format($harga * $qty, 0, ',', '.') . ']';
                $layananItems[] = $itemStr;
            }
        }
        $layanan = !empty($layananItems) ? implode('; ', $layananItems) : '-';

        return [
            $this->rowNumber,
            $tanggal,
            $kodeTransaksi,
            $row->getCabang->Nama ?? $row->KodeCabang ?? '-',
            $namaPasien,
            $jenisPasien,
            $layanan,
            $totalBayar,
            $shift,
            $metodeBayar,
            $dokter,
            $perawat,
            $resepsionis,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ===== 1. HEADER LAPORAN (4 baris di atas) =====
                $sheet->insertNewRowBefore(1, 4);

                // Row 1: Judul
                $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI');
                $sheet->mergeCells('A1:M1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E79'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(30);

                // Row 2: Tanggal cetak
                $sheet->setCellValue('A2', 'Tanggal Cetak: ' . date('d/m/Y H:i') . ' WIB');
                $sheet->mergeCells('A2:M2');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['italic' => true, 'size' => 10, 'color' => ['rgb' => '555555']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8EEF4'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Row 3: Filter info
                $filterText = 'Filter: ';
                $parts = [];
                if (!empty($this->filterInfo['klinik'])) {
                    $parts[] = 'Klinik [' . $this->filterInfo['klinik'] . ']';
                } else {
                    $parts[] = 'Klinik [Semua]';
                }
                if (!empty($this->filterInfo['tanggal_mulai']) && !empty($this->filterInfo['tanggal_akhir'])) {
                    $parts[] = 'Periode [' . \Carbon\Carbon::parse($this->filterInfo['tanggal_mulai'])->format('d/m/Y')
                        . ' - ' . \Carbon\Carbon::parse($this->filterInfo['tanggal_akhir'])->format('d/m/Y') . ']';
                }
                $filterText .= implode(' | ', $parts);

                $sheet->setCellValue('A3', $filterText);
                $sheet->mergeCells('A3:M3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '333333']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F7FA'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Row 4: Spacer
                $sheet->getRowDimension(4)->setRowHeight(8);

                // ===== 2. STYLE HEADER KOLOM (Row 5) =====
                $headerRow = 5;
                $sheet->getStyle('A' . $headerRow . ':M' . $headerRow)->applyFromArray([
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

                // ===== 3. STYLE DATA ROWS (Range-based, Instant) =====
                $lastDataRow = $sheet->getHighestRow();
                $dataStartRow = $headerRow + 1; // row 6

                if ($lastDataRow >= $dataStartRow) {
                    // Border & Vertical Alignment sekaligus untuk seluruh tabel data
                    $sheet->getStyle('A' . $dataStartRow . ':M' . $lastDataRow)->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D0D7DE']],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    ]);

                    // Format Rupiah Kolom G (Total Bayar)
                    $sheet->getStyle('H' . $dataStartRow . ':H' . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0');

                    // Right align untuk Total Bayar
                    $sheet->getStyle('H' . $dataStartRow . ':H' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Center align untuk No, Tanggal, Kode Transaksi, Jenis Pasien, Shift
                    $sheet->getStyle('A' . $dataStartRow . ':C' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $dataStartRow . ':E' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('H' . $dataStartRow . ':H' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ===== 4. FOOTER TOTAL =====
                $footerRow = $lastDataRow + 1;

                // Footer Row 1: Total Jumlah Transaksi
                $sheet->setCellValue('A' . $footerRow, 'TOTAL JUMLAH TRANSAKSI');
                $sheet->mergeCells('A' . $footerRow . ':G' . $footerRow);
                $sheet->setCellValue('H' . $footerRow, ($lastDataRow >= $dataStartRow ? ($lastDataRow - $dataStartRow + 1) : 0) . ' Transaksi');
                $sheet->mergeCells('H' . $footerRow . ':M' . $footerRow);
                $sheet->getStyle('A' . $footerRow . ':M' . $footerRow)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F4E79']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D6E4F0'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1F4E79']],
                    ],
                ]);

                // Footer Row 2: Akumulasi Total Bayar
                $footerRow2 = $footerRow + 1;
                $sheet->setCellValue('A' . $footerRow2, 'AKUMULASI TOTAL BAYAR');
                $sheet->mergeCells('A' . $footerRow2 . ':G' . $footerRow2);
                if ($lastDataRow >= $dataStartRow) {
                    $sheet->setCellValue('H' . $footerRow2, '=SUM(H' . $dataStartRow . ':H' . $lastDataRow . ')');
                } else {
                    $sheet->setCellValue('H' . $footerRow2, 0);
                }
                $sheet->mergeCells('H' . $footerRow2 . ':M' . $footerRow2);
                $sheet->getStyle('A' . $footerRow2 . ':M' . $footerRow2)->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F7A3A'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '14532D']],
                    ],
                ]);
                $sheet->getStyle('H' . $footerRow2)->getNumberFormat()->setFormatCode('"Rp"#,##0');

                // ===== 5. FREEZE PANE =====
                $sheet->freezePane('A' . ($headerRow + 1));
            },
        ];
    }
}
