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
            ? round((($totalBiaya - $totalBiayaKemarin) / $totalBiayaKemarin) * 100, 1)
            : 0;

        // Total Pasien
        $totalPasien = $qCurrent()->count();
        $totalPasienKemarin = $qPrev()->count();
        $totalPasienPersen = $totalPasienKemarin > 0
            ? round((($totalPasien - $totalPasienKemarin) / $totalPasienKemarin) * 100, 1)
            : 0;

        // Pasien Baru
        $pasienBaru = $qCurrent()->where('JenisPasien', 'Baru')->count();
        $pasienBaruKemarin = $qPrev()->where('JenisPasien', 'Baru')->count();
        $totalPasienBaruPersen = $pasienBaruKemarin > 0
            ? round((($pasienBaru - $pasienBaruKemarin) / $pasienBaruKemarin) * 100, 1)
            : 0;

        // Pasien Lama
        $pasienLama = $qCurrent()->where('JenisPasien', 'Lama')->count();
        $pasienLamaKemarin = $qPrev()->where('JenisPasien', 'Lama')->count();
        $totalPasienLamaPersen = $pasienLamaKemarin > 0
            ? round((($pasienLama - $pasienLamaKemarin) / $pasienLamaKemarin) * 100, 1)
            : 0;

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

        $transaksiTerbaru = $qCurrent()
            ->with('getCabang')
            ->orderBy('created_at', 'desc')
            ->get();

        $jenisPerawatanTerbanyak = $qCurrent()
            ->with([
                'TransaksiDetail' => function ($q) {
                    $q
                        ->select('IdTransaksi', 'JenisPerawatan')
                        ->with('MasterJenisPerawatan');
                }
            ])
            ->get()
            ->pluck('TransaksiDetail')
            ->flatten()
            ->groupBy('JenisPerawatan')
            ->map(function ($items) {
                return [
                    'JenisPerawatan' => $items->first()->MasterJenisPerawatan->Nama,
                    'jumlah' => $items->count(),
                ];
            })
            ->sortByDesc('jumlah')
            ->take(2)
            ->values();
        // dd($jenisPerawatanTerbanyak);
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
            'transaksiTerbaru' => $transaksiTerbaru,
            'jenisPerawatanTerbanyak' => $jenisPerawatanTerbanyak,
        ];

        // Kalau AJAX → kembalikan JSON
        if ($request->ajax()) {
            return response()->json($data);
        }

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
        $Shift8PasienLama = [];

        foreach ($groupedTransaksi as $group) {
            $pasienLama = $group->where('IdStatusPasien', 2);

            if ($pasienLama->count() >= 8) {
                $firstTrx = $group->first();
                $Shift8PasienLama[] = [
                    'tanggal' => $firstTrx->created_at->format('d/m/Y'),
                    'shift_id' => $firstTrx->Shift,
                    'perawat_id' => $firstTrx->IdPerawat,
                    'perawat_nama' => optional($firstTrx->getPerawat)->name,
                    'jumlah_pasien_lama' => $pasienLama->count(),
                ];
            }
        }

        $pasienBillingMinimal = [];
        // dd($transaksiPeriode);
        foreach ($transaksiPeriode as $trx) {
            if ($trx->TotalBayar >= 1000000) {
                $pasienBillingMinimal[] = [
                    'tanggal' => $trx->created_at,
                    'nama_pasien' => $trx->NamaPasien,
                    'perawat_nama' => $trx->getPerawat->name,
                    'total_billing' => $trx->TotalBayar,
                ];
                if (count($pasienBillingMinimal) >= 5) {
                    break;
                }
            }
        }

        // Ambil 5 data terakhir pasien baru beserta jumlah pasien baru, nama perawat, dan insentif dari tabel transaksi dan insentif karyawan
        $PasienBaru = Transaksi::where('JenisPasien', 'Baru')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->when(isset($perawatId), function ($query) use ($perawatId) {
                return $query->where('IdPerawat', $perawatId);
            })
            ->with([
                'getPerawat',
                'getInsentif' => function ($q) {
                    $q->where('JenisRule', 'pasien_baru');
                }
            ])
            ->selectRaw('Date(created_at) as tanggal, IdPerawat, COUNT(*) as jumlah')
            ->groupBy('Tanggal', 'IdPerawat')
            ->orderByDesc('Tanggal')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'tanggal' => \Carbon\Carbon::parse($item->tanggal)->format('d M Y'),
                    'jumlah_pasien_baru' => $item->jumlah . ' Pasien',
                    'perawat_nama' => optional($item->getPerawat)->name,
                    'insentif' => 'Rp ' . number_format(optional($item->getInsentif)->sum('Nominal'), 0, ',', '.'),
                ];
            })
            ->sortBy('Tanggal')
            ->values();



        // dd($PasienBaru);

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
