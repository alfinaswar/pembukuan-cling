<?php

namespace App\Exports;

use App\Models\Transaksi;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TransactionExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithCustomStartCell, WithEvents
{
    protected $filterData;
    protected $exportData = [];
    protected $totalRows = 0;
    // mergeInstructions format:
    // [
    //   ['column'=>'E', 'row_start'=>3, 'row_end'=>4],
    //   ['column'=>'D', 'row_start'=>3, 'row_end'=>4],
    //   ['column'=>'A', 'row_start'=>3, 'row_end'=>4]
    //   ...
    // ]
    protected $mergeInstructions = [];

    public function __construct($filterData)
    {
        $this->filterData = $filterData;
    }

    public function collection()
    {
        $query = Transaksi::with(['TransaksiDetail', 'getDokter', 'getCabang'])
            ->when($this->filterData['dokter'] ?? null, fn($q) => $q->where('IdDokter', $this->filterData['dokter']))
            ->when($this->filterData['shift'] ?? null, fn($q) => $q->where('Shift', $this->filterData['shift']))
            ->when($this->filterData['FilterTanggal'] ?? null, function ($q) {
                $dates = explode(' - ', $this->filterData['FilterTanggal']);
                $startDate = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                $endDate = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                return $q->whereBetween('Tanggal', [$startDate, $endDate]);
            })
            ->orderBy('Tanggal', 'asc')
            ->orderBy('Kode', 'asc')
            ->get();

        $exportData = [];
        $transactionIndex = 0;
        $rowNumber = 3;  // row excel ke-3 adalah baris data pertama

        foreach ($query as $transaksi) {
            $dayName = Carbon::parse($transaksi->Tanggal)->isoFormat('dddd');
            $dateFormatted = Carbon::parse($transaksi->Tanggal)->format('d M Y');
            $branchName = $transaksi->cabang->nama ?? $transaksi->KodeCabang;
            $details = $transaksi->TransaksiDetail;
            $transactionIndex++;

            $countTreatment = $details->count();

            if ($countTreatment > 0) {
                $startMerge = $rowNumber;
                $endMerge = $rowNumber + $countTreatment - 1;

                // Merge kolom Patient Name (E)
                $this->mergeInstructions[] = [
                    'column' => 'E',
                    'row_start' => $startMerge,
                    'row_end' => $endMerge
                ];

                // Merge kolom No. (D)
                $this->mergeInstructions[] = [
                    'column' => 'D',
                    'row_start' => $startMerge,
                    'row_end' => $endMerge
                ];

                // Merge kolom Day (A)
                $this->mergeInstructions[] = [
                    'column' => 'A',
                    'row_start' => $startMerge,
                    'row_end' => $endMerge
                ];

                // Merge kolom Date (B)
                $this->mergeInstructions[] = [
                    'column' => 'B',
                    'row_start' => $startMerge,
                    'row_end' => $endMerge
                ];

                // Merge kolom Branch (C)
                $this->mergeInstructions[] = [
                    'column' => 'C',
                    'row_start' => $startMerge,
                    'row_end' => $endMerge
                ];
            }

            foreach ($details as $detailIndex => $detail) {
                $exportData[] = [
                    'day' => $detailIndex === 0 ? $dayName : '',
                    'date' => $detailIndex === 0 ? $dateFormatted : '',
                    'branch' => $detailIndex === 0 ? $branchName : '',
                    'no' => $detailIndex === 0 ? $transactionIndex : '',
                    'patient_name' => $detailIndex === 0 ? $transaksi->NamaPasien : '',
                    'treatment' => $detail->MasterJenisPerawatan->Nama ?? '',
                    'keterangan' => $detail->Keterangan ?? '', // Add this field
                    'revenue' => $detail->Biaya ?? 0,
                ];
                $rowNumber++;
            }
        }

        // Gabungan/multi transaksi yang day, date, branch nya SAMA => merge
        // Kita cari blok blok yang day, date, branch sama dan merge ke instruksi (untuk kolom A, B, C)
        $rowCount = count($exportData);
        if ($rowCount > 0) {
            $i = 0;  // 0-based
            while ($i < $rowCount) {
                $startRow = $i + 3;  // Excel start row
                $current = $exportData[$i];
                $j = $i + 1;
                while (
                    $j < $rowCount &&
                    $exportData[$j]['day'] === '' &&  // hanya kosong jika sama dalam current design
                    $exportData[$j]['date'] === '' &&
                    $exportData[$j]['branch'] === ''
                ) {
                    $j++;
                }
                $endRow = ($j - 1) + 3;  // inclusive

                if ($endRow > $startRow) {
                    // Merge only once per area to avoid double-merge
                    foreach (['A', 'B', 'C'] as $col) {
                        $alreadyExists = false;
                        foreach ($this->mergeInstructions as $merge) {
                            if ($merge['column'] === $col && $merge['row_start'] == $startRow && $merge['row_end'] == $endRow) {
                                $alreadyExists = true;
                                break;
                            }
                        }
                        if (!$alreadyExists) {
                            $this->mergeInstructions[] = [
                                'column' => $col,
                                'row_start' => $startRow,
                                'row_end' => $endRow
                            ];
                        }
                    }
                }
                $i = $j;
            }
        }

        $this->exportData = $exportData;
        $this->totalRows = count($exportData);

        return collect($exportData);
    }

    public function headings(): array
    {
        return ['DAY', 'DATE', 'BRANCH', 'NO.', 'PATIENT NAME', 'TREATMENT(S)', 'REVENUE'];
    }

    public function map($row): array
    {
        $revenue = !empty($row['revenue']) ? (float) $row['revenue'] : 0;
        $formattedRevenue = number_format($revenue, 0, ',', '.');

        $treatment = $row['treatment'];
        if (!empty($row['keterangan'])) {
            $treatment .= ' - ' . $row['keterangan'];
        }

        return [
            $row['day'],
            $row['date'],
            $row['branch'],
            $row['no'] !== '' ? $row['no'] : '',
            $row['patient_name'],
            $treatment,
            $formattedRevenue,
        ];
    }

    public function startCell(): string
    {
        return 'A2';  // Row 1 = judul, Row 2 = header
    }

    public function styles(Worksheet $sheet)
    {
        return [];  // semua di AfterSheet
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastDataRow = 2 + $this->totalRows;  // row 2 = header, data mulai row 3
                $totalRow = $lastDataRow + 1; // Row for total

                // ═══════════════════════════════════════════════
                // ROW 1 — Judul "Januari 2026"
                // ═══════════════════════════════════════════════
                $sheet->mergeCells('A1:G1');
                $sheet->setCellValue('A1', $this->buildTitle());
                $sheet->getRowDimension(1)->setRowHeight(36);

                $sheet->getStyle('A1:G1')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 26,
                        'name' => 'Arial',
                        'color' => ['rgb' => 'D4610A'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'indent' => 1,
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F2C9B0'],  // salmon/peach
                    ],
                ]);

                // ═══════════════════════════════════════════════
                // ROW 2 — Header tabel
                // ═══════════════════════════════════════════════
                $sheet->getRowDimension(2)->setRowHeight(20);
                $sheet->getStyle('A2:G2')->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'name' => 'Arial',
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'C8C8C8'],  // abu
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '999999'],
                        ],
                    ],
                ]);
                // Add TOTAL row
                $sheet->mergeCells("A{$totalRow}:F{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL REVENUE');
                $sheet->setCellValue("G{$totalRow}", "=SUM(G3:G{$lastDataRow})");

                $sheet->getRowDimension($totalRow)->setRowHeight(20);
                $sheet->getStyle("A{$totalRow}:G{$totalRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 11,
                        'name' => 'Arial',
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E8F4F8'], // Light blue
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_RIGHT,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '999999'],
                        ],
                    ],
                ]);

                // ═══════════════════════════════════════════════
                // ROWS DATA — border tipis, background putih
                // ═══════════════════════════════════════════════
                if ($this->totalRows > 0) {
                    $dataRange = "A3:G{$lastDataRow}";

                    $sheet->getStyle($dataRange)->applyFromArray([
                        'font' => [
                            'size' => 10,
                            'name' => 'Arial',
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => 'FFFFFF'],
                        ],
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'CCCCCC'],
                            ],
                        ],
                        'alignment' => [
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);

                    // Semua kolom style center, left, right seperti biasa
                    $sheet->getStyle("A3:A{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B3:B{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C3:C{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("D3:D{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("E3:E{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F3:F{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                    $sheet->getStyle("G3:G{$lastDataRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle("G{$totalRow}")->getNumberFormat()
                        ->setFormatCode('#,##0');
                    // Merge kolom2 (A, B, C, D, E) sesuai instruksi
                    foreach ($this->mergeInstructions as $merge) {
                        if ($merge['row_start'] !== $merge['row_end']) {
                            $sheet->mergeCells("{$merge['column']}{$merge['row_start']}:{$merge['column']}{$merge['row_end']}");
                        }
                    }

                    // Set row height untuk semua baris data
                    for ($r = 3; $r <= $lastDataRow; $r++) {
                        $sheet->getRowDimension($r)->setRowHeight(16);
                    }
                }

                // COLUMN WIDTHS
                $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(10);  // DAY
                $sheet->getColumnDimension('B')->setAutoSize(false)->setWidth(13);  // DATE
                $sheet->getColumnDimension('C')->setAutoSize(false)->setWidth(10);  // BRANCH
                $sheet->getColumnDimension('D')->setAutoSize(false)->setWidth(5);  // NO.
                $sheet->getColumnDimension('E')->setAutoSize(false)->setWidth(22);  // PATIENT NAME
                $sheet->getColumnDimension('F')->setAutoSize(true);  // TREATMENT(S) — auto
                $sheet->getColumnDimension('G')->setAutoSize(false)->setWidth(13);  // REVENUE

                // Freeze di bawah header
                $sheet->freezePane('A3');
            },
        ];
    }

    /**
     * Generate judul dari FilterTanggal → "Januari 2026"
     */
    private function buildTitle(): string
    {
        if (empty($this->filterData['FilterTanggal'])) {
            return Carbon::now()->locale('id')->isoFormat('MMMM YYYY');
        }

        $dates = explode(' - ', $this->filterData['FilterTanggal']);
        $start = Carbon::createFromFormat('m/d/Y', trim($dates[0]))->locale('id');
        $end = Carbon::createFromFormat('m/d/Y', trim($dates[1]))->locale('id');

        if ($start->format('Y-m') === $end->format('Y-m')) {
            return $start->isoFormat('MMMM YYYY');
        }

        return $start->isoFormat('MMM YYYY') . ' – ' . $end->isoFormat('MMM YYYY');
    }
}
