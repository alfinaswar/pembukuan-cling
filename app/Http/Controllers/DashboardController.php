<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\MasterKlinik;
use App\Models\MasterShift;
use App\Models\Stok;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function kirimPencarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');

        // ── Base query builder (reusable closure) ──────────────────────────────
        $baseQuery = function () use ($request, $tanggal) {
            return Transaksi::whereDate('Tanggal', $tanggal)
                ->when($request->shift, fn($q) => $q->where('Shift', $request->shift))
                ->when($request->kasir, fn($q) => $q->where('IdResepsionis', $request->kasir))
                ->when($request->perawat, fn($q) => $q->where('IdPerawat', $request->perawat));
        };

        // ── 1. Jumlah Shift aktif pada tanggal tersebut ────────────────────────
        //    (distinct shift yang punya transaksi)
        $jumlahShift = Transaksi::whereDate('Tanggal', $tanggal)
            ->when($request->shift, fn($q) => $q->where('Shift', $request->shift))
            ->distinct('Shift')
            ->count('Shift');

        // Total pendapatan minimal per shift (sum semua billing)
        $totalPendapatan = $baseQuery()->sum('TotalBiaya');  // sesuaikan nama kolom

        // ── 2. Total Pasien Lama dalam filter ─────────────────────────────────
        //    Pasien lama = pasien yang sudah pernah bertransaksi sebelum tanggal ini
        $totalPasienLama = $baseQuery()
            ->whereHas('pasien', function ($q) use ($tanggal) {
                $q->whereHas('transaksi', function ($q2) use ($tanggal) {
                    $q2->whereDate('Tanggal', '<', $tanggal);
                });
            })
            ->count();

        // ── 3. Total Pasien dengan Billing >= Rp 1.000.000 ────────────────────
        $totalPasienBillingBesar = $baseQuery()
            ->where('TotalBiaya', '>=', 1000000)  // sesuaikan nama kolom
            ->count();

        // ── 4. Total Pasien Baru ───────────────────────────────────────────────
        //    Pasien baru = belum pernah transaksi sebelum tanggal ini
        $totalPasienBaru = $baseQuery()
            ->whereHas('pasien', function ($q) use ($tanggal) {
                $q->whereDoesntHave('transaksi', function ($q2) use ($tanggal) {
                    $q2->whereDate('Tanggal', '<', $tanggal);
                });
            })
            ->count();

        // ── 5. Total Pasien Operasi OD ────────────────────────────────────────
        //    Sesuaikan kondisi dengan field yang menandai operasi OD di sistem kamu
        $totalOperasiOD = $baseQuery()
            ->where('JenisPerawatan', 'like', '%OD%')  // sesuaikan kolom / relasi
            ->count();

        // ── Data pendukung (resepsionis & perawat bertugas) ───────────────────
        // Ambil resepsionis yang aktif pada filter ini
        $resepsionisIds = $baseQuery()->distinct()->pluck('IdResepsionis');
        $perawatIds = $baseQuery()->distinct()->pluck('IdPerawat');

        $resepsionisAktif = User::whereIn('id', $resepsionisIds)->get(['id', 'name']);
        $perawatAktif = User::whereIn('id', $perawatIds)->get(['id', 'name']);

        // Shift aktif detail
        $shiftAktif = MasterShift::whereIn(
            'id',
            Transaksi::whereDate('Tanggal', $tanggal)
                ->when($request->shift, fn($q) => $q->where('Shift', $request->shift))
                ->distinct('Shift')
                ->pluck('Shift')
        )->get();

        // ── Kembalikan JSON ───────────────────────────────────────────────────
        return response()->json([
            // Card 1
            'jumlahShift' => $jumlahShift,
            'totalPendapatan' => $totalPendapatan,
            // Card 2
            'totalPasienLama' => $totalPasienLama,
            // Card 3
            'totalPasienBillingBesar' => $totalPasienBillingBesar,
            // Card 4
            'totalPasienBaru' => $totalPasienBaru,
            // Card 5
            'totalOperasiOD' => $totalOperasiOD,
            // Pendukung
            'resepsionisAktif' => $resepsionisAktif,
            'perawatAktif' => $perawatAktif,
            'shiftAktif' => $shiftAktif,
            // Meta
            'tanggal' => $tanggal,
            'updatedAt' => now()->format('d M Y H:i'),
        ]);
    }

    public function Stok(Request $request)
    {
        // 1. Handle Filters
        $tanggalMulai = $request->input('tanggal_mulai', now()->startOfMonth()->format('Y-m-d'));
        $tanggalAkhir = $request->input('tanggal_akhir', now()->format('Y-m-d'));

        // Jika kosong (Superadmin pilih "Semua"), biarkan null. Jika user biasa, paksa pakai cabang user.
        $isSuperadmin = auth()->user()->hasRole('Superadmin');  // Sesuaikan dengan nama role kamu
        $kodeKlinik = $request->input('klinik');

        if (!$isSuperadmin || empty($kodeKlinik)) {
            $kodeKlinik = auth()->user()->kodeperusahaan ?? '';
        }

        // 2. Ambil ID Kategori Behel
        $kategoriBehel = KategoriBarang::where('id', '1')->first();
        $kategoriBehelId = $kategoriBehel ? $kategoriBehel->id : null;
        // 3. Ambil Daftar Jenis Behel (Barang)
        $behelTypesQuery = Barang::query();
        if ($kategoriBehelId) {
            $behelTypesQuery->where('KategoriBarangId', $kategoriBehelId);
        }
        $behelTypes = $behelTypesQuery->orderBy('NamaBarang', 'asc')->get();
        $behelIds = $behelTypes->pluck('id');
        // dd($behelTypes);

        // ==========================================
        // REUSABLE FILTER: Agar Summary & Table Konsisten
        // ==========================================
        $filterBehelTransaction = function ($query) use ($kategoriBehelId) {
            if ($kategoriBehelId) {
                // PRIORITAS: Filter berdasarkan Kategori Behel
                $query
                    ->whereNotNull('Barang')
                    ->where('Barang', '!=', '[]')
                    ->where('Barang', '!=', 'null');
            } else {
                // FALLBACK: Jika kategori tidak ditemukan, cek kolom Barang tidak kosong
                $query
                    ->whereNotNull('Barang')
                    ->where('Barang', '!=', '[]')
                    ->where('Barang', '!=', 'null');
            }
        };
        // dd($query);
        // 4. Hitung Summary Cards (Menggunakan Filter yang Sama)
        $stokQuery = Stok::whereIn('BarangId', $behelIds);
        if (!empty($kodeKlinik)) {
            $stokQuery->where('KodeKlinik', $kodeKlinik);
        }

        $trxBaseQuery = Transaksi::query();
        if (!empty($kodeKlinik)) {
            $trxBaseQuery->where('KodeCabang', $kodeKlinik);
        }

        $totalTerpakai = (clone $trxBaseQuery)
            ->whereBetween('Tanggal', [$tanggalMulai, $tanggalAkhir])
            ->whereHas('TransaksiDetail.MasterJenisPerawatan', $filterBehelTransaction)
            ->count();

        $transaksiHariIni = (clone $trxBaseQuery)
            ->whereDate('Tanggal', today())
            ->whereHas('TransaksiDetail.MasterJenisPerawatan', $filterBehelTransaction)
            ->count();

        $lastTransaksiTime = (clone $trxBaseQuery)
            ->whereDate('Tanggal', today())
            ->whereHas('TransaksiDetail.MasterJenisPerawatan', $filterBehelTransaction)
            ->latest()
            ->value('created_at');

        $totalTransaksiBulanIni = (clone $trxBaseQuery)
            ->whereMonth('Tanggal', now()->month)
            ->whereYear('Tanggal', now()->year)
            ->whereHas('TransaksiDetail.MasterJenisPerawatan', $filterBehelTransaction)
            ->count();
        $totalStock = $stokQuery->sum('StokAkhir') + $totalTerpakai;
        $sisaStock = $totalStock - $totalTerpakai;
        // 5. Hitung Stok Per Jenis Behel
        $stockPerType = [];
        $maxStockBase = 50;  // Angka dasar untuk persentase

        foreach ($behelTypes as $type) {
            $q = Stok::where('BarangId', $type->id);
            if (!empty($kodeKlinik)) {
                $q->where('KodeKlinik', $kodeKlinik);
            }

            $stok = $q->sum('StokAkhir');
            $percentage = $maxStockBase > 0 ? min(100, max(0, ($stok / $maxStockBase) * 100)) : 0;

            $stockPerType[] = [
                'barang' => $type,
                'stok' => $stok,
                'percentage' => round($percentage),
                'isNegative' => $stok < 0
            ];
        }

        // 6. 🔥 AMBIL DATA TRANSAKSI (DISESUAIKAN & DIRAPIKAN)
        $transactions = Transaksi::with([
            'getDokter',
            'getPerawat',
            'getResepsionis',
            'TransaksiDetail.MasterJenisPerawatan'
        ])
            ->when(!empty($kodeKlinik), function ($q) use ($kodeKlinik) {
                $q->where('KodeCabang', $kodeKlinik);
            })
            ->whereBetween('Tanggal', [$tanggalMulai, $tanggalAkhir])
            // Gunakan closure yang sama agar data tabel match dengan summary cards
            ->whereHas('TransaksiDetail.MasterJenisPerawatan', $filterBehelTransaction)
            ->latest('Tanggal')
            ->paginate(10);

        // 7. Data untuk Filter Dropdown
        $kliniks = MasterKlinik::orderBy('Nama', 'asc')->get();

        // 8. Return View
        return view('dashboard.stok.index', compact(
            'totalStock',
            'totalTerpakai',
            'sisaStock',
            'transaksiHariIni',
            'lastTransaksiTime',
            'totalTransaksiBulanIni',
            'stockPerType',
            'transactions',
            'kliniks',
            'tanggalMulai',
            'tanggalAkhir',
            'kodeKlinik',
            'behelTypes',
            'isSuperadmin'  // Kirim ini ke view untuk handle opsi "Semua Cabang"
        ));
    }
}
