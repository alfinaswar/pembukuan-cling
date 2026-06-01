<?php

namespace App\Http\Controllers;

use App\Models\MasterShift;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $now = Carbon::now();

        // 📊 Stats Cards Data
        $totalPendapatan = Transaksi::sum('TotalBayar');
        $totalPesanan = Transaksi::count();

        // Pelanggan aktif (unik NamaPasien dengan transaksi 30 hari terakhir)
        $pelangganAktif = Transaksi::where('Tanggal', '>=', $now->copy()->subDays(30))
            ->distinct('NamaPasien')
            ->count('NamaPasien');

        // Produk terjual (jumlah transaksi dengan status completed)
        $produkTerjual = Transaksi::where('TotalBayar', '>', 0)->count();

        // 📈 Data Grafik Penjualan (7 hari terakhir)
        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $chartLabels[] = $date->translatedFormat('D');

            $revenue = Transaksi::whereDate('Tanggal', $date)
                ->sum('TotalBayar');
            $chartData[] = $revenue;
        }

        // 📋 Recent Transactions (5 terbaru)
        $recentTransaksi = Transaksi::with(['getResepsionis', 'getPerawat', 'getDokter'])
            ->latest('Tanggal')
            ->take(5)
            ->get();

        // 📉 Persentase perubahan pendapatan (vs minggu lalu)
        $pendapatanMingguIni = Transaksi::whereBetween('Tanggal', [
            $now->copy()->startOfWeek(),
            $now->copy()->endOfWeek()
        ])->sum('TotalBayar');

        $pendapatanMingguLalu = Transaksi::whereBetween('Tanggal', [
            $now->copy()->subWeek()->startOfWeek(),
            $now->copy()->subWeek()->endOfWeek()
        ])->sum('TotalBayar');

        $persenPerubahan = $pendapatanMingguLalu > 0
            ? round((($pendapatanMingguIni - $pendapatanMingguLalu) / $pendapatanMingguLalu) * 100, 1)
            : 0;
        $listShift = MasterShift::get();
        return view('home', compact(
            'totalPendapatan',
            'totalPesanan',
            'pelangganAktif',
            'produkTerjual',
            'chartLabels',
            'chartData',
            'recentTransaksi',
            'persenPerubahan',
            'listShift'
        ));
    }

    public function updateShift(Request $request)
    {
        $request->validate([
            'shift' => 'required|exists:master_shifts,id',
        ]);

        $user = auth()->user();
        $user->shift = $request->shift;
        $user->save();

        return redirect()->back()->with('success', 'Shift Anda berhasil disimpan.');
    }
}
