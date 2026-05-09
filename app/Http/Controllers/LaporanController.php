<?php

namespace App\Http\Controllers;

use App\Models\MasterMetodePembayaran;
use App\Models\MasterShift;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function indexUmum(Request $request)
    {
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();

        // === Parse Filter Tanggal ===
        if ($request->filled('FilterTanggal')) {
            $parts = explode(' - ', $request->FilterTanggal);
            $startDate = Carbon::createFromFormat('d/m/Y', trim($parts[0]))->startOfDay();
            $endDate = Carbon::createFromFormat('d/m/Y', trim($parts[1]))->endOfDay();
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // Periode pembanding: rentang yang sama bulan sebelumnya
        $diffDays = $startDate->diffInDays($endDate);
        $prevEndDate = $startDate->copy()->subDay()->endOfDay();
        $prevStartDate = $prevEndDate->copy()->subDays($diffDays)->startOfDay();

        // === Helper query ===
        $qCurrent = fn() => Transaksi::whereBetween('created_at', [$startDate, $endDate]);
        $qPrev = fn() => Transaksi::whereBetween('created_at', [$prevStartDate, $prevEndDate]);

        // Total Biaya
        $totalBiaya = $qCurrent()->sum('TotalBayar');
        $totalBiayaKemarin = $qPrev()->sum('TotalBayar');
        $totalBiayaPersen = $totalBiayaKemarin > 0
            ? round((($totalBiaya - $totalBiayaKemarin) / $totalBiayaKemarin) * 100, 1) : 0;

        // Total Pasien
        $totalPasien = $qCurrent()->count();
        $totalPasienKemarin = $qPrev()->count();
        $totalPasienPersen = $totalPasienKemarin > 0
            ? round((($totalPasien - $totalPasienKemarin) / $totalPasienKemarin) * 100, 1) : 0;

        // Pasien Baru
        $pasienBaru = $qCurrent()->where('JenisPasien', 'Baru')->count();
        $pasienBaruKemarin = $qPrev()->where('JenisPasien', 'Baru')->count();
        $totalPasienBaruPersen = $pasienBaruKemarin > 0
            ? round((($pasienBaru - $pasienBaruKemarin) / $pasienBaruKemarin) * 100, 1) : 0;

        // Pasien Lama
        $pasienLama = $qCurrent()->where('JenisPasien', 'Lama')->count();
        $pasienLamaKemarin = $qPrev()->where('JenisPasien', 'Lama')->count();
        $totalPasienLamaPersen = $pasienLamaKemarin > 0
            ? round((($pasienLama - $pasienLamaKemarin) / $pasienLamaKemarin) * 100, 1) : 0;

        // Payment Chart
        $paymentTotals = Transaksi::select('MetodePembayaran', DB::raw('SUM(TotalBayar) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('MetodePembayaran')
            ->get();

        $metodes = MasterMetodePembayaran::pluck('Nama', 'id');
        $paymentChartLabels = [];
        $paymentChartTotals = [];
        foreach ($paymentTotals as $row) {
            $paymentChartLabels[] = $metodes[$row->MetodePembayaran] ?? 'Lainnya';
            $paymentChartTotals[] = (float) $row->total;
        }

        // $transaksiTerbaru = $qCurrent()
        //     ->orderBy('created_at', 'desc')
        //     ->get();


        $data = [
            'dokter' => $dokter,
            'perawat' => $perawat,
            'kasir' => $kasir,
            'shift' => $shift,
            'totalBiaya' => $totalBiaya,
            'totalBiayaKemarin' => $totalBiayaKemarin,
            'totalBiayaPersen' => $totalBiayaPersen,
            'totalPasien' => $totalPasien,
            'totalPasienKemarin' => $totalPasienKemarin,
            'totalPasienPersen' => $totalPasienPersen,
            'pasienBaru' => $pasienBaru,
            'pasienBaruKemarin' => $pasienBaruKemarin,
            'totalPasienBaruPersen' => $totalPasienBaruPersen,
            'pasienLama' => $pasienLama,
            'pasienLamaKemarin' => $pasienLamaKemarin,
            'totalPasienLamaPersen' => $totalPasienLamaPersen,
            'paymentChartLabels' => $paymentChartLabels,
            'paymentChartTotals' => $paymentChartTotals,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            // 'transaksiTerbaru' => $transaksiTerbaru,

        ];

        // Kalau AJAX → kembalikan JSON
        if ($request->ajax()) {
            return response()->json($data);
        }

        return view('laporan.umum.index', $data);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
