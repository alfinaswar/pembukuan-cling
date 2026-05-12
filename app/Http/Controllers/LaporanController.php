<?php

namespace App\Http\Controllers;

use App\Models\InsentifKaryawan;
use App\Models\MasterMetodePembayaran;
use App\Models\MasterShift;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    public function indexUmum(Request $request)
    {
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();

        if ($request->filled('FilterTanggal')) {
            $parts = explode(' - ', $request->FilterTanggal);
            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($parts[0]))->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($parts[1]))->endOfDay();
        } else {
            $startDate = \Carbon\Carbon::now()->startOfMonth();
            $endDate = \Carbon\Carbon::now()->endOfMonth();
        }

        // Total Biaya
        $totalBiaya = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->sum('TotalBayar');
        // jumlah total pasien
        $totalPasien = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->count();
        // Pasien Baru & Lama
        $pasienBaru = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->where('JenisPasien', 'Baru')
            ->count();

        $pasienLama = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->where('JenisPasien', 'Lama')
            ->count();

        // Chart Payment
        $paymentChartData = Transaksi::select('MetodePembayaran', 'KodeCabang', DB::raw('SUM(TotalBayar) as jumlah'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->groupBy('MetodePembayaran', 'KodeCabang')
            ->with('getMetodePembayaran') // assuming the relation name is getMetodePembayaran
            ->get();

        // ambil label nama metode pembayaran jika relasi ada, kalau tidak fallback ke field MetodePembayaran
        $paymentChartLabels = $paymentChartData->map(function ($item) {
            return $item->getMetodePembayaran->Nama ?? $item->MetodePembayaran ?? '-';
        });

        $paymentChartTotals = $paymentChartData->pluck('jumlah');
        // dd($paymentChartLabels);

        // Transaksi Terbaru
        $transaksiTerbaru = Transaksi::with(['getPerawat'])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->latest()
            ->get();

        // Jenis Perawatan Terbanyak
        $jenisPerawatanTerbanyak = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->with([
                'TransaksiDetail' => function ($q) {
                    $q->select('IdTransaksi', 'JenisPerawatan')
                        ->with('MasterJenisPerawatan');
                }
            ])
            ->get()
            ->pluck('TransaksiDetail')
            ->flatten()
            ->groupBy('JenisPerawatan')
            ->map(function ($items) {
                return [
                    'JenisPerawatan' => $items->first()->MasterJenisPerawatan->Nama ?? '-',
                    'jumlah' => $items->count(),
                ];
            })
            ->sortByDesc('jumlah')
            ->take(5)
            ->values();


        $data = [
            'dokter' => $dokter,
            'perawat' => $perawat,
            'kasir' => $kasir,
            'shift' => $shift,
            'totalBiaya' => $totalBiaya,
            'totalPasien' => $totalPasien,
            'pasienBaru' => $pasienBaru,
            'pasienLama' => $pasienLama,
            'paymentChartLabels' => $paymentChartLabels,
            'paymentChartTotals' => $paymentChartTotals,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'transaksiTerbaru' => $transaksiTerbaru,
            'jenisPerawatanTerbanyak' => $jenisPerawatanTerbanyak,
        ];

        return view('laporan.umum.index', $data);
    }
    public function dataDashboardUmum(Request $request)
    {
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();

        if ($request->filled('FilterTanggal')) {
            $parts = explode(' - ', $request->FilterTanggal);
            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($parts[0]))->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($parts[1]))->endOfDay();
        } else {
            $startDate = \Carbon\Carbon::now()->startOfMonth();
            $endDate = \Carbon\Carbon::now()->endOfMonth();
        }

        // Total Biaya
        $totalBiaya = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->sum('TotalBayar');
        // jumlah total pasien
        $totalPasien = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->count();
        // Pasien Baru & Lama
        $pasienBaru = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->where('JenisPasien', 'Baru')
            ->count();

        $pasienLama = Transaksi::whereBetween('Tanggal', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->where('JenisPasien', 'Lama')
            ->count();

        // Chart Payment
        $paymentChartData = Transaksi::select('MetodePembayaran', 'KodeCabang', DB::raw('SUM(TotalBayar) as jumlah'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->groupBy('MetodePembayaran', 'KodeCabang')
            ->with('getMetodePembayaran') // assuming the relation name is getMetodePembayaran
            ->get();

        // ambil label nama metode pembayaran jika relasi ada, kalau tidak fallback ke field MetodePembayaran
        $paymentChartLabels = $paymentChartData->map(function ($item) {
            return $item->getMetodePembayaran->Nama ?? $item->MetodePembayaran ?? '-';
        });

        $paymentChartTotals = $paymentChartData->pluck('jumlah');

        // Transaksi Terbaru
        $transaksiTerbaru = Transaksi::with(['getPerawat'])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->latest()
            ->get();


        // Jenis Perawatan Terbanyak
        $jenisPerawatanTerbanyak = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->with([
                'TransaksiDetail' => function ($q) {
                    $q->select('IdTransaksi', 'JenisPerawatan')
                        ->with('MasterJenisPerawatan');
                }
            ])
            ->get()
            ->pluck('TransaksiDetail')
            ->flatten()
            ->groupBy('JenisPerawatan')
            ->map(function ($items) {
                return [
                    'JenisPerawatan' => $items->first()->MasterJenisPerawatan->Nama ?? '-',
                    'jumlah' => $items->count(),
                ];
            })
            ->sortByDesc('jumlah')
            ->take(5)
            ->values();


        $data = [
            'dokter' => $dokter,
            'perawat' => $perawat,
            'kasir' => $kasir,
            'shift' => $shift,
            'totalBiaya' => $totalBiaya,
            'totalPasien' => $totalPasien,
            'pasienBaru' => $pasienBaru,
            'pasienLama' => $pasienLama,
            'paymentChartLabels' => $paymentChartLabels,
            'paymentChartTotals' => $paymentChartTotals,
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y'),
            'transaksiTerbaru' => $transaksiTerbaru,
            'jenisPerawatanTerbanyak' => $jenisPerawatanTerbanyak,
        ];

        return view('laporan.umum.index', $data);
    }

    public function indexPerawat(Request $request)
    {
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();
        // dd($OmsetSatuShift);
        return view('laporan.perawat.index', compact('dokter', 'perawat', 'kasir', 'shift'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function dataDashboardPerawat(Request $request)
    {
        // dd($request->all());
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();
        if ($request->filled('perawat') && $request->filled('FilterTanggal')) {
            $parts = explode(' - ', $request->FilterTanggal);

            $startDate = Carbon::createFromFormat('m/d/Y', trim($parts[0]))->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', trim($parts[1]))->endOfDay();

            $perawatId = $request->perawat;
        } elseif ($request->filled('FilterTanggal')) {
            $parts = explode(' - ', $request->FilterTanggal);

            $startDate = Carbon::createFromFormat('m/d/Y', trim($parts[0]))->startOfDay();
            $endDate = Carbon::createFromFormat('m/d/Y', trim($parts[1]))->endOfDay();
        } else {
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }
        $OmsetSatuShift = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->when(isset($perawatId), function ($query) use ($perawatId) {
                return $query->where('IdPerawat', $perawatId);
            })
            ->sum('TotalBayar');
        // dd($OmsetSatuShift);
        $TotalInsentif = InsentifKaryawan::whereBetween('created_at', [$startDate, $endDate])
            ->when(isset($perawatId), function ($query) use ($perawatId) {
                return $query->where('UserId', $perawatId);
            })
            ->sum('Nominal');
        // Total Pasien Dilayani
        $totalPasienDilayani = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->when(isset($perawatId), function ($query) use ($perawatId) {
                return $query->where('IdPerawat', $perawatId);
            })
            ->count();

        $ShiftTotalBiayaKlinik = InsentifKaryawan::with('getTransaksi', 'getUser')
            ->where('JenisRule', 'omzet_shift')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when(isset($perawatId), function ($query) use ($perawatId) {
                return $query->where('UserId', $perawatId);
            })
            ->get();

        $transaksiPeriode = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->when(isset($perawatId), function ($query) use ($perawatId) {
                return $query->where('IdPerawat', $perawatId);
            })
            ->get();

        $groupedTransaksi = $transaksiPeriode->groupBy(function ($trx) {
            return $trx->created_at->format('Y-m-d') . '-' . $trx->Shift . '-' . $trx->IdPerawat;
        });

        $Shift8PasienLama = InsentifKaryawan::with('getTransaksi')
            ->where('UserId', $perawatId)
            ->where('JenisRule', 'pasien_lama')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $pasienBillingMinimal = InsentifKaryawan::with('getTransaksi')
            ->where('UserId', $perawatId)
            ->where('JenisRule', 'transaksi')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $Odontektomi = InsentifKaryawan::where('UserId', $perawatId)
            ->where('JenisRule', 'tindakan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        // Ambil 5 data terakhir pasien baru beserta jumlah pasien baru, nama perawat, dan insentif dari tabel transaksi dan insentif karyawan
        // Hitung jumlah pasien baru per hari di periode yang dipilih untuk perawat terkait
        // Group InsentifKaryawan 'pasien_baru' by tanggal, menghitung jumlah pasien baru dari getTransaksi->JenisPasien == 'Baru'
        $PasienBaru = InsentifKaryawan::with('getTransaksi')
            ->where('UserId', $perawatId)
            ->where('JenisRule', 'pasien_baru')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->format('Y-m-d');
            })
            ->map(function ($group) {
                $jumlahPasienBaru = $group->filter(function ($item) {
                    return ($item->getTransaksi && isset($item->getTransaksi->JenisPasien) && $item->getTransaksi->JenisPasien === 'Baru');
                })->count();
                $perawat_nama = ($group->first() && $group->first()->getUser) ? $group->first()->getUser->name : '-';
                $insentif = ($group->first() && isset($group->first()->Nominal)) ? $group->first()->Nominal : 0;
                return [
                    'tanggal' => $group->first()->created_at->format('Y-m-d'),
                    'jumlah_pasien_baru' => $jumlahPasienBaru,
                    'perawat_nama' => $perawat_nama,
                    'insentif' => $insentif,
                    'items' => $group,
                ];
            });
        // Odontektomi
        $Odontektomi = InsentifKaryawan::with('getTransaksi')
            ->where('UserId', $perawatId)
            ->where('JenisRule', 'tindakan')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        // dd($pasienBillingMinimal);
        $Ringkasan = InsentifKaryawan::selectRaw('
        JenisRule,
        SUM(Nominal) as total_insentif,
        COUNT(*) as total_data
    ')
            ->where('UserId', $perawatId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('JenisRule')
            ->get();
        // dd($Odontektomi);

        // dd($Shift8PasienLama);
        $data = [
            'OmsetSatuShift' => $OmsetSatuShift,
            'TotalInsentif' => $TotalInsentif,
            'totalPasienDilayani' => $totalPasienDilayani,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'perawatId' => isset($perawatId) ? $perawatId : null,
            'ShiftTotalBiayaKlinik' => $ShiftTotalBiayaKlinik,
            'Shift8PasienLama' => $Shift8PasienLama,
            'pasienBillingMinimal' => $pasienBillingMinimal,
            'PasienBaru' => $PasienBaru,
            'Ringkasan' => $Ringkasan,
            'Odontektomi' => $Odontektomi,
        ];

        return view('laporan.perawat.index', compact('dokter', 'perawat', 'kasir', 'shift', 'data'));
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
