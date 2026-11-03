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

class PasienExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithEvents, WithTitle
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
        return 'Laporan Pasien';
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            [
                'No',
                'Tanggal Transaksi',
                'No. Transaksi',
                'Nama Pasien',
                'Tipe Pasien',
                'Dental Unit',
                'Nama Dokter',
                'Nama Perawat',
                'Nama Resepsionis',
                'Nama Cabang',
                'Jenis Perawatan',
                'Total Biaya Transaksi',
            ],
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $tanggalRaw = $row->Tanggal ?? $row->tanggal ?? $row->created_at ?? '';
        $tanggal = $tanggalRaw ? \Carbon\Carbon::parse($tanggalRaw)->format('d/m/Y') : '-';

        $tipePasien = '-';
        if ($row->JenisPasien === 'Baru') {
            $tipePasien = 'Pasien Baru';
        } elseif ($row->JenisPasien === 'Lama') {
            $tipePasien = 'Pasien Lama';
        } elseif (!empty($row->JenisPasien)) {
            $tipePasien = $row->JenisPasien;
        }

        $perawatanItems = [];
        if (isset($row->TransaksiDetail) && $row->TransaksiDetail->count()) {
            foreach ($row->TransaksiDetail as $detail) {
                $nama = optional($detail->MasterJenisPerawatan)->Nama ?? $detail->JenisPerawatan ?? null;
                if ($nama) {
                    $perawatanItems[] = $nama;
                }
            }
        }
        $jenisPerawatan = !empty($perawatanItems) ? implode(', ', $perawatanItems) : '-';

        $dentalUnit = $row->getDentalUnit?->Nama ?? $row->DentalUnit ?? '-';
        $dokter = $row->getDokter?->name ?? $row->getDokter?->Nama ?? '-';
        $perawat = $row->getPerawat?->name ?? $row->getPerawat?->Nama ?? '-';
        $resepsionis = $row->getResepsionis?->name ?? $row->getResepsionis?->Nama ?? '-';
        $cabang = $row->getCabang?->Nama ?? $row->KodeCabang ?? '-';
        $totalBayar = (float) ($row->TotalBayar ?? $row->total ?? 0);

        return [
            $this->rowNumber,
            $tanggal,
            $row->Kode ?? '-',
            $row->NamaPasien ?? '-',
            $tipePasien,
            $dentalUnit,
            $dokter,
            $perawat,
            $resepsionis,
            $cabang,
            $jenisPerawatan,
            $totalBayar,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Insert 4 baris di atas untuk header laporan
                $sheet->insertNewRowBefore(1, 4);

                // Row 1: Judul
                $sheet->setCellValue('A1', 'LAPORAN DATA PASIEN & PENDAPATAN');
                $sheet->mergeCells('A1:L1');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1F4E79'],
                    ],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(32);

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
                $filterParts = [];
                $filterParts[] = 'Klinik: ' . ($this->filterInfo['klinik'] ?? 'Semua');
                $filterParts[] = 'Tipe Pasien: ' . ($this->filterInfo['tipe_pasien'] ?? 'Semua');
                if (!empty($this->filterInfo['periode_label'])) {
                    $filterParts[] = 'Periode: ' . $this->filterInfo['periode_label'];
                }
                $filterText = 'Filter: ' . implode(' | ', $filterParts);

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

                $sheet->getRowDimension(4)->setRowHeight(8);

                // Row 5: Column Headers
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
                $sheet->getRowDimension($headerRow)->setRowHeight(28);

                $lastDataRow = $sheet->getHighestRow();
                $dataStartRow = $headerRow + 1;

                if ($lastDataRow >= $dataStartRow) {
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

                    // Format Rupiah untuk Kolom L (Total Biaya Transaksi)
                    $sheet->getStyle('L' . $dataStartRow . ':L' . $lastDataRow)
                        ->getNumberFormat()
                        ->setFormatCode('"Rp"#,##0');

                    $sheet->getStyle('L' . $dataStartRow . ':L' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Center kolom No, Tanggal, No Transaksi, Tipe Pasien
                    $sheet->getStyle('A' . $dataStartRow . ':C' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle('E' . $dataStartRow . ':E' . $lastDataRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Row Total Akumulasi
                    $totalRow = $lastDataRow + 1;
                    $sheet->setCellValue('A' . $totalRow, 'TOTAL PENDAPATAN');
                    $sheet->mergeCells('A' . $totalRow . ':K' . $totalRow);
                    $sheet->setCellValue('L' . $totalRow, '=SUM(L' . $dataStartRow . ':L' . $lastDataRow . ')');

                    $sheet->getStyle('A' . $totalRow . ':L' . $totalRow)->applyFromArray([
                        'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '1F4E79'],
                        ],
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '0D2840']],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('L' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('L' . $totalRow)->getNumberFormat()->setFormatCode('"Rp"#,##0');
                    $sheet->getRowDimension($totalRow)->setRowHeight(26);
                }
            },
        ];
    }
}
