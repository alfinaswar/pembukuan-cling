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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithTitle
{
    protected $data;
    protected $filterInfo;
    protected $rowMap = []; // [excel_row => transaksi_index]
    protected $transaksiRowRanges = []; // [transaksi_index => ['start' => x, 'end' => y]]

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

    public function headings(): array
    {
        return [
            [
                'Tanggal',
                'Kode Transaksi',
                'Nama Pasien',
                'Jenis Pasien',
                'Layanan',
                'Harga Layanan',
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
        // ===== Format Tanggal =====
        $tanggalRaw = $row->tanggal ?? $row->created_at ?? '';
        $tanggal = $tanggalRaw ? \Carbon\Carbon::parse($tanggalRaw)->format('d/m/Y H:i') : '-';

        $kodeTransaksi = $row->Kode ?? $row->Kode ?? '-';
        $namaPasien = $row->NamaPasien ?? '-';
        $jenisPasien = $row->JenisPasien ?? '-';
        $shift = $row->getShift->Nama ?? $row->Shift ?? '-';
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

        // 1 transaksi bisa punya banyak layanan (dari TransaksiDetail)
        $layananItems = [];
        if (isset($row->TransaksiDetail) && $row->TransaksiDetail->count()) {
            foreach ($row->TransaksiDetail as $detail) {
                $layananNama = optional($detail->MasterJenisPerawatan)->Nama
                    ?? $detail->nama_layanan
                    ?? $detail->Nama
                    ?? '-';
                $harga = (float) ($detail->Biaya ?? $detail->Harga ?? 0);
                $qty = (int) ($detail->Qty ?? $detail->Jumlah ?? 1);

                // Jika qty > 1, tampilkan sebagai "Nama (x2)"
                if ($qty > 1) {
                    $layananNama .= ' (x' . $qty . ')';
                    $harga = $harga * $qty;
                }

                $layananItems[] = [
                    'nama' => $layananNama,
                    'harga' => $harga,
                ];
            }
        }

        // Jika tidak ada layanan, tetap tampilkan 1 baris
        if (empty($layananItems)) {
            $layananItems[] = ['nama' => '-', 'harga' => 0];
        }

        // Simpan mapping row untuk merging nanti
        $transaksiIndex = count($this->transaksiRowRanges);
        $startRow = count($this->rowMap);
        foreach ($layananItems as $item) {
            $this->rowMap[] = $transaksiIndex;
        }
        $this->transaksiRowRanges[$transaksiIndex] = [
            'start' => $startRow,
            'end' => count($this->rowMap) - 1,
            'total' => $totalBayar,
        ];

        // Return array of rows (1 transaksi bisa jadi multiple rows)
        $rows = [];
        foreach ($layananItems as $item) {
            $rows[] = [
                $tanggal,
                $kodeTransaksi,
                $namaPasien,
                $jenisPasien,
                $item['nama'],
                $item['harga'],
                $totalBayar,
                $shift,
                $metodeBayar,
                $dokter,
                $perawat,
                $resepsionis,
            ];
        }

        return $rows;
    }

    /**
     * Override default behavior: map() returns array of rows (multi-row per record)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ===== 1. INSERT HEADER LAPORAN (geser data ke bawah) =====
                // Insert 4 baris di atas untuk header laporan
                $sheet->insertNewRowBefore(1, 4);

                // Row 1: Judul
                $sheet->setCellValue('A1', 'LAPORAN TRANSAKSI');
                $sheet->mergeCells('A1:L1');
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
                $sheet->mergeCells('A2:L2');
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
                $sheet->mergeCells('A3:L3');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '333333']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F7FA'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);

                // Row 4: Empty spacer
                $sheet->getRowDimension(4)->setRowHeight(8);

                // ===== 2. STYLE HEADER KOLOM (sekarang di row 5) =====
                $headerRow = 5;
                $sheet->getStyle('A' . $headerRow . ':L' . $headerRow)->applyFromArray([
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

                // ===== 3. HITUNG TOTAL & MERGE CELL =====
                $lastDataRow = $sheet->getHighestRow();
                $dataStartRow = $headerRow + 1; // row 6

                // Hitung total
                $totalPasien = 0;
                $akumulasiTotalBayar = 0;
                $transaksiSeen = [];

                foreach ($this->transaksiRowRanges as $range) {
                    $excelStartRow = $dataStartRow + $range['start'];
                    $excelEndRow = $dataStartRow + $range['end'];

                    // Merge kolom A-F, H-L (kecuali G = Total Bayar, E = Layanan, F = Harga Layanan)
                    // Kolom yang di-merge: A(Tanggal), B(Kode), C(Nama Pasien), D(Jenis Pasien),
                    //                       G(Total Bayar), H(Shift), I(Metode Bayar), J(Dokter), K(Perawat), L(Resepsionis)
                    $mergeColumns = ['A', 'B', 'C', 'D', 'G', 'H', 'I', 'J', 'K', 'L'];

                    if ($excelEndRow > $excelStartRow) {
                        foreach ($mergeColumns as $col) {
                            $sheet->mergeCells($col . $excelStartRow . ':' . $col . $excelEndRow);
                            $sheet->getStyle($col . $excelStartRow . ':' . $col . $excelEndRow)
                                ->getAlignment()
                                ->setVertical(Alignment::VERTICAL_CENTER);
                        }
                    }

                    // Hitung total (hitung per transaksi, bukan per baris)
                    if (!in_array($excelStartRow, $transaksiSeen)) {
                        $totalPasien++;
                        $akumulasiTotalBayar += $range['total'];
                        $transaksiSeen[] = $excelStartRow;
                    }
                }

                // ===== 4. STYLE DATA ROWS =====
                if ($lastDataRow >= $dataStartRow) {
                    // Alternating row colors
                    for ($r = $dataStartRow; $r <= $lastDataRow; $r++) {
                        $bgColor = ($r % 2 == 0) ? 'F2F7FB' : 'FFFFFF';
                        $sheet->getStyle('A' . $r . ':L' . $r)->applyFromArray([
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

                    // Format Rupiah kolom F (Harga Layanan) dan G (Total Bayar)
                    $sheet->getStyle('F' . $dataStartRow . ':G' . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0');

                    // Alignment kanan untuk angka
                    $sheet->getStyle('F' . $dataStartRow . ':G' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Center untuk kolom Tanggal, Kode Transaksi
                    $sheet->getStyle('A' . $dataStartRow . ':B' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ===== 5. FOOTER TOTAL =====
                $footerRow = $lastDataRow + 1;
                $sheet->insertNewRowBefore($footerRow, 2);

                // Row footer 1: Total Pasien
                $sheet->setCellValue('A' . $footerRow, 'TOTAL JUMLAH PASIEN');
                $sheet->mergeCells('A' . $footerRow . ':F' . $footerRow);
                $sheet->setCellValue('G' . $footerRow, $totalPasien . ' Pasien');
                $sheet->mergeCells('G' . $footerRow . ':L' . $footerRow);
                $sheet->getStyle('A' . $footerRow . ':L' . $footerRow)->applyFromArray([
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

                // Row footer 2: Akumulasi Total Bayar
                $footerRow2 = $footerRow + 1;
                $sheet->setCellValue('A' . $footerRow2, 'AKUMULASI TOTAL BAYAR');
                $sheet->mergeCells('A' . $footerRow2 . ':F' . $footerRow2);
                $sheet->setCellValue('G' . $footerRow2, $akumulasiTotalBayar);
                $sheet->mergeCells('G' . $footerRow2 . ':L' . $footerRow2);
                $sheet->getStyle('A' . $footerRow2 . ':L' . $footerRow2)->applyFromArray([
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
                $sheet->getStyle('G' . $footerRow2)->getNumberFormat()->setFormatCode('"Rp"#,##0');

                // ===== 6. FREEZE PANE =====
                $sheet->freezePane('A' . ($headerRow + 1));
            },
        ];
    }
}
