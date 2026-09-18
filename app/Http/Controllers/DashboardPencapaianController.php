<?php

namespace App\Http\Controllers;

use App\Models\MasterKlinik;
use App\Models\TargetCapaian;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardPencapaianController extends Controller
{
    /**
     * Dashboard Pencapaian Klinik - Superadmin
     */
    public function index(Request $request)
    {
        // Default: bulan & tahun saat ini
        $bulan = $request->input('bulan', now()->month);
        $tahun = $request->input('tahun', now()->year);

        // Validasi
        $bulan = max(1, min(12, (int) $bulan));
        $tahun = max(2020, min(2100, (int) $tahun));

        // Semua klinik aktif
        $klinikList = MasterKlinik::where('is_active', true)
            ->orderBy('Nama')
            ->get();

        // ====== 1. TARGET PER KLINIK ======
        $targetPerKlinik = TargetCapaian::where('Tahun', $tahun)
            ->where('Bulan', $bulan)
            ->pluck('BesarTarget', 'IdKlinik')
            ->toArray();

        // ====== 2. PENCAPAIAN PER KLINIK (dari transaksis) ======
        $startDate = Carbon::createFromDate($tahun, $bulan, 1)->startOfMonth()->format('Y-m-d H:i:s');
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth()->format('Y-m-d H:i:s');

        $pencapaianPerKlinik = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->groupBy('KodeCabang')
            ->selectRaw('KodeCabang, SUM(TotalBayar) as total')
            ->pluck('total', 'KodeCabang')
            ->toArray();

        // ====== 3. HITUNG TOTAL & RINGKASAN ======
        $totalTarget = 0;
        $totalPencapaian = 0;
        $dataPerKlinik = [];
        $jumlahKlinikAktif = $klinikList->count();

        foreach ($klinikList as $klinik) {
            $target = (float) ($targetPerKlinik[$klinik->Kode] ?? 0);
            $pencapaian = (float) ($pencapaianPerKlinik[$klinik->Kode] ?? 0);
            $persentase = $target > 0 ? round(($pencapaian / $target) * 100, 1) : 0;
            $sisa = max(0, $target - $pencapaian);

            $totalTarget += $target;
            $totalPencapaian += $pencapaian;

            $dataPerKlinik[] = [
                'kode' => $klinik->Kode,
                'nama' => $klinik->Nama,
                'target' => $target,
                'pencapaian' => $pencapaian,
                'persentase' => $persentase,
                'sisa' => $sisa,
            ];
        }

        // Sortir: persentase tertinggi di atas
        usort($dataPerKlinik, fn($a, $b) => $b['persentase'] <=> $a['persentase']);

        // Persentase total
        $persentaseTotal = $totalTarget > 0 ? round(($totalPencapaian / $totalTarget) * 100, 1) : 0;
        $sisaTotal = max(0, $totalTarget - $totalPencapaian);

        // ====== 4. DATA UNTUK CHART ======
        $chartLabels = array_column($dataPerKlinik, 'nama');
        $chartTarget = array_column($dataPerKlinik, 'target');
        $chartCapaian = array_column($dataPerKlinik, 'pencapaian');

        // ====== 5. BULAN & TAHUN UNTUK DROPDOWN ======
        $daftarBulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];
        $daftarTahun = range(now()->year - 2, now()->year + 1);

        // Timestamp update
        $lastUpdate = now()->format('d F Y H:i');

        return view('dashboard.pencapaian.index', compact(
            'bulan',
            'tahun',
            'daftarBulan',
            'daftarTahun',
            'totalTarget',
            'totalPencapaian',
            'persentaseTotal',
            'sisaTotal',
            'dataPerKlinik',
            'chartLabels',
            'chartTarget',
            'chartCapaian',
            'jumlahKlinikAktif',
            'lastUpdate'
        ));
    }
}
