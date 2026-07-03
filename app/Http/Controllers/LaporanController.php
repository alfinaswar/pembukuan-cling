<?php

namespace App\Http\Controllers;

use App\Exports\JenisPerawatanExport;
use App\Exports\TransactionExport;
use App\Exports\TransaksiExport;
use App\Models\InsentifKaryawan;
use App\Models\MasterJenisPerawatan;
use App\Models\MasterJenisPerawatans;
use App\Models\MasterKlinik;
use App\Models\MasterMetodePembayaran;
use App\Models\MasterShift;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;


class LaporanController extends Controller
{
    public function indexUmum(Request $request)
    {

        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();
        $cabang = MasterKlinik::get();

        if ($request->filled('FilterTanggal')) {
            $parts = explode(' - ', $request->FilterTanggal);
            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($parts[0]))->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($parts[1]))->endOfDay();
        } else {
            $startDate = \Carbon\Carbon::now()->startOfMonth();
            $endDate = \Carbon\Carbon::now()->endOfMonth();
        }

        // Total Biaya
        $totalBiaya = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->sum('TotalBayar');
        // jumlah total pasien
        $totalPasien = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->count();
        // Pasien Baru & Lama
        $pasienBaru = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->where('JenisPasien', 'Baru')
            ->count();

        $pasienLama = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->where('JenisPasien', 'Lama')
            ->count();

        // Chart Payment
        $paymentChartData = Transaksi::with('getMetodePembayaran')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->get();

        // Ambil data label dan total pembayaran dari relasi getMetodePembayaran
        $metodePembayaranTotals = [];

        foreach ($paymentChartData as $transaksi) {
            foreach ($transaksi->getMetodePembayaran as $pembayaran) {
                $nama = $pembayaran->getMetodeBayar->Nama ?? ($pembayaran->MetodePembayaran ?? '-');
                if (!isset($metodePembayaranTotals[$nama])) {
                    $metodePembayaranTotals[$nama] = 0;
                }
                $metodePembayaranTotals[$nama] += (float) $pembayaran->Nominal;
            }
        }

        $paymentChartLabels = array_keys($metodePembayaranTotals);
        $paymentChartTotals = array_values($metodePembayaranTotals);

        // Transaksi Terbaru
        $transaksiTerbaru = Transaksi::with(['getPerawat'])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        // Jenis Perawatan Terbanyak
        $jenisPerawatanTerbanyak = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
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
                    'JenisPerawatan' => $items->first()->MasterJenisPerawatan->Nama ?? '-',
                    'jumlah' => $items->count(),
                    'jenis_perawatan_id' => $items->first()->JenisPerawatan, // tambahkan ID sebagai tiebreaker
                ];
            })
            ->sort(function ($a, $b) {
                if ($b['jumlah'] === $a['jumlah']) {
                    return strcmp($a['JenisPerawatan'], $b['JenisPerawatan']);
                }
                return $b['jumlah'] <=> $a['jumlah'];
            })
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
            'cabang' => $cabang,
        ];

        return view('laporan.umum.index', $data);
    }

    public function dataDashboardUmum(Request $request)
    {
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();
        $cabang = MasterKlinik::get();

        $kodeCabang = $request->filled('FilterCabang') && $request->FilterCabang
            ? $request->FilterCabang
            : auth()->user()->kodeperusahaan;

        if ($request->filled('FilterTanggal')) {
            $parts = explode(' - ', $request->FilterTanggal);
            $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($parts[0]))->startOfDay();
            $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($parts[1]))->endOfDay();
        } else {
            $startDate = \Carbon\Carbon::now()->startOfMonth();
            $endDate = \Carbon\Carbon::now()->endOfMonth();
        }

        // Total Biaya
        $totalBiaya = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang)
            ->sum('TotalBayar');
        $totalPasien = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang)
            ->count();
        $pasienBaru = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang)
            ->where('JenisPasien', 'Baru')
            ->count();

        $pasienLama = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang)
            ->where('JenisPasien', 'Lama')
            ->count();

        // PERBAIKAN DI SINI: Ganti auth()->user()->kodeperusahaan menjadi $kodeCabang
        $paymentChartData = Transaksi::with('getMetodePembayaran')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang) // ✅ SUDAH BENAR
            ->get();

        // Ambil data label dan total pembayaran dari relasi getMetodePembayaran
        $metodePembayaranTotals = [];

        foreach ($paymentChartData as $transaksi) {
            foreach ($transaksi->getMetodePembayaran as $pembayaran) {
                $nama = $pembayaran->getMetodeBayar->Nama ?? ($pembayaran->MetodePembayaran ?? '-');
                if (!isset($metodePembayaranTotals[$nama])) {
                    $metodePembayaranTotals[$nama] = 0;
                }
                $metodePembayaranTotals[$nama] += (float) $pembayaran->Nominal;
            }
        }

        $paymentChartLabels = array_keys($metodePembayaranTotals);
        $paymentChartTotals = array_values($metodePembayaranTotals);


        $transaksiTerbaru = Transaksi::with(['getPerawat'])
            ->where('KodeCabang', $kodeCabang)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();

        // Jenis Perawatan Terbanyak
        $jenisPerawatanTerbanyak = Transaksi::whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang)
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
            'cabang' => $cabang,
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
        // =============================================
        // 1. VALIDASI
        // =============================================
        $validator = Validator::make($request->all(), [
            'FilterTanggal' => [
                'required',
                'string',
                'regex:/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s\-\s[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/'
            ],
            'perawat' => 'required|exists:transaksis,IdPerawat',
            'shift' => 'nullable|exists:transaksis,Shift',
        ], [
            'FilterTanggal.required' => 'Tanggal periode wajib diisi.',
            'FilterTanggal.regex' => 'Format tanggal tidak sesuai (00/00/0000 - 00/00/0000).',
            'perawat.required' => 'Perawat wajib dipilih.',
            'perawat.exists' => 'Perawat tidak bertugas di tanggal / shift tersebut.',
            'shift.exists' => 'Tidak Bertugas di Shit Tersebut.',
        ]);

        if ($validator->fails()) {
            $errorHtml = collect(['FilterTanggal', 'perawat', 'shift'])
                ->filter(fn($field) => $validator->errors()->has($field))
                ->map(fn($field) => $validator->errors()->first($field))
                ->implode('<br>');

            return redirect()
                ->route('laporan-perawat.index')
                ->withErrors($validator)
                ->withInput()
                ->with('fail_message', $errorHtml ?: 'Terjadi kesalahan validasi.');
        }

        // =============================================
        // 2. PARSE PARAMETER
        // =============================================
        [$startRaw, $endRaw] = explode(' - ', $request->FilterTanggal);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startRaw))->startOfDay();
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endRaw))->endOfDay();
        $perawatId = $request->perawat;
        $shiftFilter = $request->filled('shift') ? $request->shift : null;
        $user = auth()->user();
        if ($user->hasRole('Superadmin') || $user->hasRole('Management')) {
            $perawatUser = User::find($request->perawat);
            $kodeCabang = $perawatUser ? $perawatUser->kodeperusahaan : null;
        } else {
            $kodeCabang = $user->kodeperusahaan;
        }

        // =============================================
        // 3. BASE SCOPE — dua closure reusable
        //    Semua query InsentifKaryawan pakai $scopeInsentif
        //    Semua query Transaksi pakai $scopeTransaksi
        // =============================================
        $scopeTransaksi = function ($q) use ($startDate, $endDate, $perawatId, $shiftFilter, $kodeCabang) {
            $q
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('KodeCabang', $kodeCabang)
                ->where('IdPerawat', $perawatId)
                ->when($shiftFilter, fn($qq) => $qq->where('Shift', $shiftFilter));
        };

        $scopeInsentif = function ($q) use ($startDate, $endDate, $perawatId, $shiftFilter, $kodeCabang) {
            $q
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('KodeCabang', $kodeCabang)
                ->where('UserId', $perawatId)
                ->when($shiftFilter, fn($qq) => $qq->where('Shift', $shiftFilter));
        };

        // =============================================
        // 4. VALIDASI DATA KOSONG
        // =============================================
        $adaTransaksi = Transaksi::where($scopeTransaksi)->exists();
        if (!$adaTransaksi) {
            return redirect()->back()->withInput()->with(
                'error',
                'Data tidak ditemukan untuk filter yang dipilih.'
            );
        }

        // =============================================
        // 5. QUERY — masing-masing fokus, mudah dibaca
        // =============================================

        // 5a. Omset & Total Shift
        $omsetRow = Transaksi::where($scopeTransaksi)
            ->selectRaw('
            COUNT(DISTINCT CONCAT(DATE(created_at), "-", Shift)) as total_shift,
            SUM(TotalBayar) as total_omset
        ')
            ->first();

        $OmsetSatuShift = [
            'total_shift' => $omsetRow->total_shift ?? 0,
            'total_omset' => $omsetRow->total_omset ?? 0,
        ];

        // 5b. Total pasien (count transaksi)
        $totalPasienDilayani = Transaksi::where($scopeTransaksi)->count();

        // 5c. Total insentif semua rule
        $TotalInsentif = InsentifKaryawan::where($scopeInsentif)->sum('Nominal');

        // 5d. Omzet per shift (rule: omzet_shift)
        $ShiftTotalBiayaKlinik = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'omzet_shift')
            ->get();

        // 5e. 8 pasien lama per shift (rule: pasien_lama)
        // Dihitung group per tanggal+shift, struktur data konsisten dengan tabel di view (tanggal, jumlah, perawat, insentif)
        $countpasienlama = Transaksi::where($scopeTransaksi)->where('JenisPasien', 'Lama')->count();

        $Shift8PasienLama = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'pasien_lama')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d') . '|' . $item->Shift)
            ->map(function ($group) use ($countpasienlama) {
                $first = $group->first();
                return [
                    'created_at' => $first->created_at,
                    'jumlah_pasien_lama' => $countpasienlama . ' Pasien',
                    'perawat_nama' => $first->getUser->name ?? '-',
                    'insentif' => $group->sum('Nominal'),
                ];
            })
            ->values();

        // 5f. Billing minimal 1jt (rule: transaksi, TotalBayar >= 1jt)
        $pasienBillingMinimal = InsentifKaryawan::with('getTransaksi')
            ->where($scopeInsentif)
            ->where('JenisRule', 'transaksi')
            ->whereHas('getTransaksi', fn($q) => $q->where('TotalBayar', '>=', 1_000_000))
            ->orderByDesc('created_at')
            ->get();

        // 5g. Odontektomi (rule: tindakan)
        $Odontektomi = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'tindakan')
            ->get();

        // 5h. Pasien baru (rule: pasien_baru) — grouped per tanggal+shift
        $PasienBaru = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'pasien_baru')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d') . '|' . $item->Shift)
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'tanggal' => $first->created_at->format('Y-m-d'),
                    'jumlah' => $group->count(),
                    'perawat' => $first->getUser->name ?? '-',
                    'insentif' => $group->sum('Nominal'),
                ];
            })
            ->values();

        // 5h. Pasien lama (rule: pasien_lama) — grouped per tanggal+shift
        $countpasienlama = Transaksi::where('JenisPasien', 'Lama')->count();
        $PasienLama = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'pasien_lama')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d') . '|' . $item->Shift)
            ->map(function ($group) use ($countpasienlama) {
                $first = $group->first();
                return [
                    'tanggal' => $first->created_at->format('Y-m-d'),
                    'jumlah' => $countpasienlama,
                    'perawat' => $first->getUser->name ?? '-',
                    'insentif' => $group->sum('Nominal'),
                ];
            })
            ->values();

        // 5j. Insentif Hari Libur (rule: insentif_hari_libur)
        // Kumpulkan seluruh insentif dengan JenisRule 'insentif_hari_libur' dengan filter yang sama
        $InsentifHariLibur = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'insentif_hari_libur')
            ->get();

        // ================================
        // 5i. Ringkasan per JenisRule (+ insentif_hari_libur)
        // ================================
        $jenisRuleInfo = [
            'omzet_shift' => ['label' => 'Omset Shift ≥ Rp 6.000.000', 'badge' => 'bg-primary', 'order' => 1],
            'transaksi' => ['label' => 'Transaksi ≥ Rp 1.000.000', 'badge' => 'bg-success', 'order' => 2],
            'pasien_lama' => ['label' => '8 Pasien Lama per Shift', 'badge' => 'bg-info', 'order' => 3],
            'pasien_baru' => ['label' => 'Pasien Baru', 'badge' => 'bg-danger', 'order' => 4],
            'tindakan' => ['label' => 'Tindakan Odontektomi', 'badge' => 'bg-warning text-white', 'order' => 5],
            'insentif_hari_libur' => ['label' => 'Insentif Hari Libur', 'badge' => 'bg-warning text-white', 'order' => 6],
            'target_tercapai' => ['label' => 'Target klinik tercapai', 'badge' => 'bg-success text-white', 'order' => 7],

        ];

        // Ambil sum per JenisRule sekali query termasuk insentif_hari_libur
        $ringkasanDb = InsentifKaryawan::where($scopeInsentif)
            ->selectRaw('JenisRule, SUM(Nominal) as total_insentif, COUNT(*) as total_data')
            ->groupBy('JenisRule')
            ->get()
            ->keyBy('JenisRule');  // key = JenisRule untuk akses O(1)

        $Ringkasan = collect($jenisRuleInfo)->map(function ($info, $key) use ($ringkasanDb) {
            $db = $ringkasanDb->get($key);
            return (object) [
                'JenisRule' => $key,
                'label' => $info['label'],
                'badge' => $info['badge'],
                'order' => $info['order'],
                'total_insentif' => $db->total_insentif ?? 0,
                'total_data' => $db->total_data ?? 0,
            ];
        })->values();

        // =============================================
        // 6. DROPDOWN (untuk form filter)
        // =============================================
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();

        // =============================================
        // 7. KIRIM KE VIEW
        // =============================================
        $data = [
            'OmsetSatuShift' => $OmsetSatuShift,
            'TotalInsentif' => $TotalInsentif,
            'totalPasienDilayani' => $totalPasienDilayani,
            'ShiftTotalBiayaKlinik' => $ShiftTotalBiayaKlinik,
            'Shift8PasienLama' => $Shift8PasienLama,
            'pasienBillingMinimal' => $pasienBillingMinimal,
            'Odontektomi' => $Odontektomi,
            'PasienBaru' => $PasienBaru,
            'PasienLama' => $PasienLama,
            'InsentifHariLibur' => $InsentifHariLibur,
            'Ringkasan' => $Ringkasan,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'perawatId' => $perawatId,
            'shiftFilter' => $shiftFilter,
            'kodeCabang' => $kodeCabang,
        ];

        return view('laporan.perawat.index', compact('dokter', 'perawat', 'kasir', 'shift', 'data'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function indexResepsionis(Request $request)
    {
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();
        // dd($OmsetSatuShift);
        return view('laporan.resepsionis.index', compact('dokter', 'perawat', 'kasir', 'shift'));
    }

    /**
     * Display the specified resource.
     */
    public function dataDashboardResepsionis(Request $request)
    {
        // 1. VALIDASI
        $validator = Validator::make($request->all(), [
            'FilterTanggal' => [
                'required',
                'string',
                'regex:/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s\-\s[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/'
            ],
            'perawat' => 'required|exists:transaksis,IdResepsionis',
            'shift' => 'nullable|exists:transaksis,Shift',
        ], [
            'FilterTanggal.required' => 'Tanggal periode wajib diisi.',
            'FilterTanggal.regex' => 'Format tanggal tidak sesuai (00/00/0000 - 00/00/0000).',
            'perawat.required' => 'Kasir / Resepsionis wajib dipilih.',
            'shift.exists' => 'Shift tidak valid / tidak ditemukan.',
            'perawat.exists' => 'Kasir tidak bertugas di tanggal / shift tersebut',
        ]);
        if ($validator->fails()) {
            $errorHtml = collect(['FilterTanggal', 'perawat', 'shift'])
                ->filter(fn($field) => $validator->errors()->has($field))
                ->map(fn($field) => $validator->errors()->first($field))
                ->implode('<br>');

            return redirect()
                ->route('laporan-resepsionis.index')
                ->withErrors($validator)
                ->withInput()
                ->with('fail_message', $errorHtml ?: 'Terjadi kesalahan validasi.');
        }

        // 2. PARSE PARAMETER
        [$startRaw, $endRaw] = explode(' - ', $request->FilterTanggal);
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startRaw))->startOfDay();
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endRaw))->endOfDay();

        $kasirId = $request->perawat;
        $shiftFilter = $request->filled('shift') ? $request->shift : null;

        $user = auth()->user();
        if ($user->hasRole('Superadmin') || $user->hasRole('Management')) {
            $perawatUser = User::find($request->perawat);
            $kodeCabang = $perawatUser ? $perawatUser->kodeperusahaan : null;
        } else {
            $kodeCabang = $user->kodeperusahaan;
        }

        // 3. BASE SCOPE (Reusable Closures)
        $scopeTransaksi = function ($q) use ($startDate, $endDate, $kodeCabang, $kasirId, $shiftFilter) {
            $q
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('KodeCabang', $kodeCabang)
                ->where('IdResepsionis', $kasirId)
                ->when($shiftFilter, fn($qq) => $qq->where('Shift', $shiftFilter));
        };

        $scopeInsentif = function ($q) use ($startDate, $endDate, $kodeCabang, $kasirId, $shiftFilter) {
            $q
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('KodeCabang', $kodeCabang)
                ->where('UserId', $kasirId)
                ->when($shiftFilter, fn($qq) => $qq->where('Shift', $shiftFilter));
        };

        // 4. VALIDASI DATA KOSONG
        if (!Transaksi::where($scopeTransaksi)->exists()) {
            return redirect()->back()->withInput()->with(
                'error',
                'Data tidak ditemukan untuk filter yang dipilih.'
            );
        }

        // 5. QUERY

        // 5a. Omset & Total Shift
        $omsetRow = Transaksi::where($scopeTransaksi)
            ->selectRaw('
            COUNT(DISTINCT CONCAT(DATE(created_at), "-", Shift)) as total_shift,
            SUM(TotalBayar) as total_omset
        ')
            ->first();

        $OmsetSatuShift = [
            'total_shift' => $omsetRow->total_shift ?? 0,
            'total_omset' => $omsetRow->total_omset ?? 0,
        ];

        // 5b. Total pasien
        $totalPasienDilayani = Transaksi::where($scopeTransaksi)->count();

        // 5c. Total insentif semua rule
        $TotalInsentif = InsentifKaryawan::where($scopeInsentif)->sum('Nominal');

        // 5d. Omzet per shift (rule: omzet_shift)
        $ShiftTotalBiayaKlinik = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'omzet_shift')
            ->get();

        // 5e. 8 pasien lama per shift (rule: pasien_lama)
        $countpasienlama = Transaksi::where($scopeTransaksi)->where('JenisPasien', 'Lama')->count();

        $Shift8PasienLama = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'pasien_lama')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d') . '|' . $item->Shift)
            ->map(function ($group) use ($countpasienlama) {
                $first = $group->first();
                return [
                    'created_at' => $first->created_at,
                    'jumlah_pasien_lama' => $countpasienlama,
                    'perawat_nama' => $first->getUser->name ?? '-',
                    'shift' => $first->Shift ?? null,
                    'insentif' => $group->sum('Nominal'),
                ];
            })
            ->values();

        // 5f. Insentif Hari Libur (rule: hari_libur)
        $InsentifHariLibur = InsentifKaryawan::where($scopeInsentif)
            ->where('JenisRule', 'insentif_hari_libur')
            ->get();
        // dd($InsentifHariLibur);
        // 5g. Ringkasan per JenisRule
        $jenisRuleInfo = [
            'omzet_shift' => ['label' => 'Shift ≥ Rp 6.000.000 / 2 ≥ Rp 12.000.000', 'badge' => 'bg-primary', 'order' => 1],
            'pasien_lama' => ['label' => 'Shift dengan 8 Pasien Lama', 'badge' => 'bg-info', 'order' => 2],
            'insentif_hari_libur' => ['label' => 'Insentif Hari Libur', 'badge' => 'bg-warning', 'order' => 3],
            'target_tercapai' => ['label' => 'Target Tercapai', 'badge' => 'bg-success', 'order' => 4],

        ];

        $ringkasanDb = InsentifKaryawan::where($scopeInsentif)
            ->selectRaw('JenisRule, SUM(Nominal) as total_insentif, COUNT(*) as total_data')
            ->groupBy('JenisRule')
            ->get()
            ->keyBy('JenisRule');

        $Ringkasan = collect($jenisRuleInfo)->map(function ($info, $key) use ($ringkasanDb) {
            $db = $ringkasanDb->get($key);
            return (object) [
                'JenisRule' => $key,
                'label' => $info['label'],
                'badge' => $info['badge'],
                'order' => $info['order'],
                'total_insentif' => $db->total_insentif ?? 0,
                'total_data' => $db->total_data ?? 0,
            ];
        })->sortBy('order')->values();

        // 6. DROPDOWN (Data untuk Filter)
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();
        // dd($InsentifHariLibur);
        // 7. KIRIM KE VIEW
        $data = [
            'OmsetSatuShift' => $OmsetSatuShift,
            'TotalInsentif' => $TotalInsentif,
            'totalPasienDilayani' => $totalPasienDilayani,
            'ShiftTotalBiayaKlinik' => $ShiftTotalBiayaKlinik,
            'Shift8PasienLama' => $Shift8PasienLama,
            'InsentifHariLibur' => $InsentifHariLibur,  // <<-- DITAMBAHKAN
            'Ringkasan' => $Ringkasan,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'kasirId' => $kasirId,
            'kodeCabang' => $kodeCabang,
            'shiftFilter' => $shiftFilter,
        ];
        $data_req = $request->all();
        // dd($data_req);
        return view('laporan.resepsionis.index', compact('dokter', 'perawat', 'kasir', 'shift', 'data', 'data_req'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function indexDokter(Request $request)
    {
        // dd(123);
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();

        if ($request->filled('dokter') && $request->filled('FilterTanggal')) {
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

        $TotalPasienBaru = Transaksi::where('JenisPasien', 'Baru')->where('KodeCabang', auth()->user()->kodeperusahaan);
        $TotalPasienLama = Transaksi::where('JenisPasien', 'Lama')->where('KodeCabang', auth()->user()->kodeperusahaan);

        if ($request->filled('dokter')) {
            $TotalPasienBaru = $TotalPasienBaru->where('IdDokter', $request->dokter);
            $TotalPasienLama = $TotalPasienLama->where('IdDokter', $request->dokter);
        }
        if ($request->filled('shift')) {
            $TotalPasienBaru = $TotalPasienBaru->where('Shift', $request->shift);
            $TotalPasienLama = $TotalPasienLama->where('Shift', $request->shift);
        }
        $TotalPasienBaru = $TotalPasienBaru->count();
        $TotalPasienLama = $TotalPasienLama->count();
        $TotalPasien = $TotalPasienBaru + $TotalPasienLama;

        $TotalPerawatan = TransaksiDetail::query();
        if ($request->filled('KodeCabang') || $request->filled('dokter') || $request->filled('shift') || (isset($startDate) && isset($endDate))) {
            $TotalPerawatan = $TotalPerawatan->whereHas('getTransaksi', function ($query) use ($request, $startDate, $endDate) {
                $query->where('KodeCabang', auth()->user()->kodeperusahaan);
                if ($request->filled('dokter')) {
                    $query->where('IdDokter', $request->dokter);
                }
                if ($request->filled('shift')) {
                    $query->where('Shift', $request->shift);
                }
                if (isset($startDate) && isset($endDate)) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                }
            });
        }
        $TotalPerawatan = $TotalPerawatan->whereNotNull('JenisPerawatan')->count();

        // Correct usage: just build the builder, don't call ->query() on a builder
        $TotalBiayaPerawatan = Transaksi::where('KodeCabang', auth()->user()->kodeperusahaan);

        if ($request->filled('dokter')) {
            $TotalBiayaPerawatan = $TotalBiayaPerawatan->where('IdDokter', $request->dokter);
        }
        if ($request->filled('shift')) {
            $TotalBiayaPerawatan = $TotalBiayaPerawatan->where('Shift', $request->shift);
        }
        if (isset($startDate) && isset($endDate)) {
            $TotalBiayaPerawatan = $TotalBiayaPerawatan->whereBetween('created_at', [$startDate, $endDate]);
        }
        // Hitung Biaya Admin
        $TotalBiayaAdmin = Transaksi::where('KodeCabang', auth()->user()->kodeperusahaan);

        if ($request->filled('dokter')) {
            $TotalBiayaAdmin = $TotalBiayaAdmin->where('IdDokter', $request->dokter);
        }
        if ($request->filled('shift')) {
            $TotalBiayaAdmin = $TotalBiayaAdmin->where('Shift', $request->shift);
        }
        if (isset($startDate) && isset($endDate)) {
            $TotalBiayaAdmin = $TotalBiayaAdmin->whereBetween('created_at', [$startDate, $endDate]);
        }

        $TotalBiayaAdmin = $TotalBiayaAdmin->sum('BiayaAdmin');

        $TotalBiayaPerawatan = $TotalBiayaPerawatan->sum('TotalBayar');
        // dd($TotalBiayaPerawatan);

        $dataTransaksi = Transaksi::with('TransaksiDetail')
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->when($request->filled('dokter'), function ($query) use ($request) {
                $query->where('IdDokter', $request->dokter);
            })
            ->when($request->filled('shift'), function ($query) use ($request) {
                $query->where('Shift', $request->shift);
            })
            ->get();
        // dd($dataTransaksi);
        $klinik = MasterKlinik::get();
        return view('laporan.dokter.index', compact('klinik', 'TotalPasienBaru', 'TotalBiayaAdmin', 'dataTransaksi', 'TotalPerawatan', 'TotalBiayaPerawatan', 'TotalPasienLama', 'TotalPasien', 'dokter', 'perawat', 'kasir', 'shift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function dataDashboardDokter(Request $request)
    {
        // =============================================
        // 1. VALIDASI
        // =============================================
        $validator = Validator::make($request->all(), [
            'FilterTanggal' => [
                'required',
                'string',
                'regex:/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s\-\s[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/'
            ],
            'dokter' => 'required',
            'shift' => 'nullable|exists:transaksis,Shift',
        ], [
            'FilterTanggal.required' => 'Tanggal periode wajib diisi.',
            'FilterTanggal.regex' => 'Format tanggal tidak sesuai (00/00/0000 - 00/00/0000).',
            'dokter.required' => 'Dokter wajib dipilih.',
            'dokter.exists' => 'Dokter tidak valid.',
            'shift.exists' => 'Tidak Bertugas di Shit Tersebut.',
        ]);
        if ($validator->fails()) {
            $errorHtml = collect(['FilterTanggal', 'dokter', 'shift'])
                ->filter(fn($field) => $validator->errors()->has($field))
                ->map(fn($field) => $validator->errors()->first($field))
                ->implode('<br>');

            return redirect()
                ->route('laporan-dokter.index')
                ->withErrors($validator)
                ->withInput()
                ->with('fail_message', $errorHtml ?: 'Terjadi kesalahan validasi.');
        }

        // =============================================
        // 2. PARSE PARAMETER
        // =============================================
        [$startRaw, $endRaw] = explode(' - ', $request->FilterTanggal);
        $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($startRaw))->startOfDay()->format('Y-m-d H:i:s');
        $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($endRaw))->endOfDay()->format('Y-m-d H:i:s');
        // dd($endDate);
        $dokterId = $request->dokter;
        $shiftFilter = $request->filled('shift') ? $request->shift : null;
        $kodeCabang = $request->KodeCabang;
        // dd($startDate);
        // =============================================
        // 3. BASE SCOPE — satu closure reusable
        //    untuk semua query ke tabel Transaksi
        // =============================================
        $scopeTransaksi = fn($q) => $q
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang)
            ->where('IdDokter', $dokterId)
            ->when($shiftFilter, fn($qq) => $qq->where('Shift', $shiftFilter));

        // =============================================
        // 4. VALIDASI DATA KOSONG
        // =============================================
        if (!Transaksi::where($scopeTransaksi)->exists()) {
            return redirect()->back()->withInput()->with(
                'error',
                'Data tidak ditemukan untuk filter yang dipilih.'
            );
        }

        // =============================================
        // 5. QUERY
        // =============================================

        // 5a. Pasien Baru & Lama
        $TotalPasienBaru = Transaksi::where($scopeTransaksi)
            ->where('JenisPasien', 'Baru')
            ->count();

        $TotalPasienLama = Transaksi::where($scopeTransaksi)
            ->where('JenisPasien', 'Lama')
            ->count();

        $TotalPasien = $TotalPasienBaru + $TotalPasienLama;

        // 5b. Total Perawatan (via TransaksiDetail → relasi ke Transaksi)
        $TotalPerawatan = TransaksiDetail::whereNotNull('JenisPerawatan')
            ->whereHas('getTransaksi', $scopeTransaksi)
            ->count();

        // 5c. Total Biaya Perawatan & Admin
        $TotalBiayaPerawatan = Transaksi::where($scopeTransaksi)->sum('TotalBayar');
        $TotalBiayaAdmin = Transaksi::where($scopeTransaksi)->sum('BiayaAdmin');

        // 5d. Detail transaksi untuk tabel
        $dataTransaksi = Transaksi::with([
            'TransaksiDetail.MasterJenisPerawatan',
            'getPerawat',
            'getResepsionis',
        ])
            ->where($scopeTransaksi)
            ->orderByDesc('created_at')
            ->get();
        // dd($dataTransaksi);
        // 5e. Rincian per jenis perawatan (sidebar)
        $RincianJenisPerawatan = TransaksiDetail::with('MasterJenisPerawatan')
            ->selectRaw('JenisPerawatan, COUNT(*) as jumlah, AVG(Biaya) as rata_rata_biaya')
            ->whereNotNull('JenisPerawatan')
            ->whereHas('getTransaksi', $scopeTransaksi)
            ->groupBy('JenisPerawatan')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        $NamaDokter = User::where('id', $dokterId)->first();
        setlocale(LC_TIME, 'id_ID.utf8');  // Pastikan locale Bahasa Indonesia tersedia di server
        $Hari = \Carbon\Carbon::parse($startDate)->translatedFormat('l, d F Y');

        // Ambil perawat yang bertugas pada shift dan tanggal tersebut (via relasi pada transaksi)
        $PerawatBertugas = Transaksi::with('getPerawat')
            ->where($scopeTransaksi)
            ->whereNotNull('IdPerawat')
            ->get()
            ->pluck('getPerawat')
            ->filter()  // hilangkan null jika ada transaksi tanpa perawat
            ->unique('id')
            ->values();
        // Ambil resepsionis yang terkait dengan transaksi pada filter saat ini
        $ResepsionisBertugas = Transaksi::with('getResepsionis')
            ->where($scopeTransaksi)
            ->whereNotNull('IdResepsionis')
            ->get()
            ->pluck('getResepsionis')
            ->filter()
            ->unique('id')
            ->values();

        // =============================================
        // 6. DROPDOWN
        // =============================================
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();
        $klinik = MasterKlinik::get();
        // dd(123);
        // =============================================
        // 7. KIRIM KE VIEW
        // =============================================
        return view('laporan.dokter.index', compact(
            'TotalPasienBaru',
            'TotalPasienLama',
            'TotalPasien',
            'TotalPerawatan',
            'TotalBiayaPerawatan',
            'TotalBiayaAdmin',
            'dataTransaksi',
            'RincianJenisPerawatan',
            'dokter',
            'perawat',
            'kasir',
            'shift',
            'NamaDokter',
            'Hari',
            'PerawatBertugas',
            'ResepsionisBertugas',
            'klinik'
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function downloadExcel(Request $request)
    {
        $validated = $request->validate([
            'FilterTanggal' => 'required|string',
            'dokter' => 'nullable|integer',
            'shift' => 'nullable|integer',
        ]);

        $filename = 'Transaksi_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new TransactionExport($validated),
            $filename
        );
    }

    public function billingMinimalPerawat(Request $request)
    {
        $validated = $request->validate([
            'FilterTanggal' => 'required|string',
            'perawat' => 'nullable|integer',
            'shift' => 'nullable|integer',
        ]);
        // Pisahkan FilterTanggal menjadi $startRaw dan $endRaw, lalu parsing ke Carbon
        [$startRaw, $endRaw] = explode(' - ', $validated['FilterTanggal']);
        $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($startRaw))->startOfDay();
        $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($endRaw))->endOfDay();


        $perawatId = $validated['perawat'] ?? null;
        $shift = $validated['shift'] ?? null;
        $user = auth()->user();
        if ($user->hasRole('Superadmin') || $user->hasRole('Management')) {
            $perawatUser = User::find($perawatId);
            $kodeCabang = $perawatUser ? $perawatUser->kodeperusahaan : null;
        } else {
            $kodeCabang = $user->kodeperusahaan;
        }

        // Query billing minimal per perawat, disimpan di $billingByPerawat
        $billingByPerawat = InsentifKaryawan::with('getTransaksi')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('JenisRule', 'transaksi')
            ->when($kodeCabang, fn($q) => $q->where('KodeCabang', $kodeCabang))
            ->when($perawatId, fn($q) => $q->where('UserId', $perawatId))
            ->when($shift, fn($q) => $q->where('Shift', $shift))
            ->whereHas('getTransaksi', fn($q) => $q->where('TotalBayar', '>=', 1_000_000))
            ->orderByDesc('created_at')
            ->get();
        // dd($billingByPerawat);

        return view('laporan.perawat.billing_minimal', [
            'billingByPerawat' => $billingByPerawat,
            // Format tanggal Indonesia: "dd/mm/yyyy - dd/mm/yyyy"
            'FilterTanggal' => \Carbon\Carbon::createFromFormat('m/d/Y', trim($startRaw))->format('d/m/Y') . ' - ' . \Carbon\Carbon::createFromFormat('m/d/Y', trim($endRaw))->format('d/m/Y'),

            'shift' => $billingByPerawat->first()?->Shift ?? null,

        ]);
    }

    public function indexTransaksi(Request $request)
    {
        $klinik = MasterKlinik::get();
        return view('laporan.transaksi.index', compact('klinik'));
    }

    public function preview(Request $request)
    {
        $user = auth()->user();
        $klinikId = $request->klinik_id;
        // dd($klinikId);
        if (!$user->hasRole('Superadmin') && !$user->hasRole('Management')) {
            $klinikId = $user->kodeperusahaan;
        }

        $query = Transaksi::with([
                'getCabang',
                'TransaksiDetail',
                'getMetodePembayaran.getMetodeBayar',
                'getShift',
                'getDokter',
                'getPerawat',
                'getResepsionis',
            ])
            ->when($klinikId, function ($q) use ($klinikId) {
                $q->where('KodeCabang', $klinikId);
            })
            ->when($request->tanggal_mulai, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->tanggal_mulai);
            })
            ->when($request->tanggal_akhir, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->tanggal_akhir);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $query->values()->map(function ($item, $idx) {
                $metodePembayaran = '-';
                if (isset($item->getMetodePembayaran) && $item->getMetodePembayaran->count()) {
                    $pembayaranList = [];
                    foreach ($item->getMetodePembayaran as $p) {
                        $pembayaranList[] = ($p->getMetodeBayar->Nama ?? '-') . ': Rp ' . number_format($p->Nominal ?? 0, 0, ',', '.');
                    }
                    $metodePembayaran = implode('; ', $pembayaranList);
                }
                $layananOut = '-';
                if (isset($item->TransaksiDetail) && $item->TransaksiDetail->count()) {
                    $rekap = [];
                    foreach ($item->TransaksiDetail as $detail) {
                        $layananNama = optional($detail->MasterJenisPerawatan)->Nama;
                        if ($layananNama) {
                            if (!isset($rekap[$layananNama])) {
                                $rekap[$layananNama] = [
                                    'nama' => $layananNama,
                                    'harga' => 0,
                                    'count' => 0
                                ];
                            }
                            $rekap[$layananNama]['harga'] += (int) ($detail->Biaya ?? 0);
                            $rekap[$layananNama]['count'] += 1;
                        }
                    }
                    $layananStrs = [];
                    foreach ($rekap as $itemLayanan) {
                        $str = $itemLayanan['nama'];
                        if ($itemLayanan['count'] > 1) {
                            $str .= ' x' . $itemLayanan['count'];
                        }
                        $str .= ' [Rp ' . number_format($itemLayanan['harga'], 0, ',', '.') . ']';
                        $layananStrs[] = $str;
                    }
                    $layananOut = implode('; ', $layananStrs);
                }

                return [
                    'no' => $idx + 1,
                    'kode' => $item->Kode ?? '-',
                    'tanggal' => $item->Tanggal ?? $item->tanggal ?? $item->created_at,
                    'nama_pasien' => $item->pelanggan->nama ?? $item->NamaPasien ?? '-',
                    'jenis_pasien' => $item->JenisPasien ?? '-',
                    'metode_pembayaran' => $metodePembayaran,
                    'layanan' => $layananOut,
                    'total_bayar' => $item->total ?? $item->TotalBayar ?? 0,
                    // Pakai 'dd' (Dokter) atau 'dt' (Perawat) sebagai petugas
                    'petugas' =>
                        // Tampilkan dokter, perawat, dan resepsionis dengan label dan <br>
                        (isset($item->getDokter) && $item->getDokter ? 'Dokter: ' . $item->getDokter->name . '<br>' : '') .
                        (isset($item->getPerawat) && $item->getPerawat ? 'Perawat: ' . $item->getPerawat->name . '<br>' : '') .
                        (isset($item->getResepsionis) && $item->getResepsionis ? 'Resepsionis: ' . $item->getResepsionis->name : '')
                        ?: '-',

                    'shift' => $item->getShift?->Nama ?? '-',
                    'aksi' => null
                ];

            })
        ]);
    }

    public function downloadTransaksi(Request $request)
    {
        $request->validate([
            'format' => 'required|in:excel,pdf'
        ]);

        $user = auth()->user();
        $klinikId = $request->klinik_id;
        if (!$user->hasRole('Superadmin') && !$user->hasRole('Management')) {
            $klinikId = $user->kodeperusahaan;
        }

        $query = Transaksi::with([

            'getCabang',
            'TransaksiDetail.MasterJenisPerawatan',
            'getMetodePembayaran.getMetodeBayar',
            'getShift',
            'getDokter',
            'getPerawat',
            'getResepsionis',
        ])
            ->when($klinikId, function ($q) use ($klinikId) {
                $q->where('KodeCabang', $klinikId);
            })
            ->when($request->tanggal_mulai, function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->tanggal_mulai);
            })
            ->when($request->tanggal_akhir, function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->tanggal_akhir);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil nama klinik untuk filter info
        $klinikNama = 'Semua';
        if ($klinikId) {
            $klinikNama = MasterKlinik::where('Kode', $klinikId)->value('Nama') ?? $klinikId;
        }

        $filterInfo = [
            'klinik' => $klinikNama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
        ];

        return Excel::download(
            new TransaksiExport($query, $filterInfo),
            'Laporan-Transaksi-' . date('Y-m-d') . '.xlsx'
        );
    }
    public function indexJenisPerawatan(Request $request)
    {
        $klinik = MasterKlinik::get();
        $jenisPerawatan = MasterJenisPerawatan::get();
        return view('laporan.jenis-perawatan.index', compact('klinik','jenisPerawatan'));
    }
    public function previewJenisPerawatan(Request $request)
{
    $user = auth()->user();
    $klinikId = $request->klinik_id;
    if (!$user->hasRole('Superadmin') && !$user->hasRole('Management')) {
        $klinikId = $user->kodeperusahaan;
    }

    // Cast ke array (saat pilih 1 item)
    $jenisPerawatanIds = $request->jenis_perawatan;
    if (is_string($jenisPerawatanIds)) {
        $jenisPerawatanIds = [$jenisPerawatanIds];
    }
    $jenisPerawatanIds = $jenisPerawatanIds ?: [];
    $query =TransaksiDetail::select(
            'transaksi_details.JenisPerawatan',
            \DB::raw('COUNT(*) as jumlah_penjualan'),
            \DB::raw('COALESCE(SUM(transaksi_details.Biaya), 0) as total_revenue')
        )
        ->join('transaksis', 'transaksis.id', '=', 'transaksi_details.IdTransaksi')
        ->when($klinikId, function ($q) use ($klinikId) {
            $q->where('transaksis.KodeCabang', $klinikId);
        })
        ->when($request->tanggal_mulai, function ($q) use ($request) {
            $q->whereDate('transaksis.Tanggal', '>=', $request->tanggal_mulai);
        })
        ->when($request->tanggal_akhir, function ($q) use ($request) {
            $q->whereDate('transaksis.Tanggal', '<=', $request->tanggal_akhir);
        })
        ->when(!empty($jenisPerawatanIds), function ($q) use ($jenisPerawatanIds) {
            $q->whereIn('transaksi_details.JenisPerawatan', $jenisPerawatanIds);
        })
        ->groupBy('transaksi_details.JenisPerawatan')
        ->orderByDesc('total_revenue')
        ->get();
    $masterJenisPerawatan = MasterJenisPerawatan::whereIn('id', $query->pluck('JenisPerawatan'))->get()->keyBy('id');

    return response()->json([
        'success' => true,
        'data' => $query->map(function ($item) use ($masterJenisPerawatan) {
            return [
                'id'              => $item->JenisPerawatan,
                'nama_perawatan'  => $masterJenisPerawatan[$item->JenisPerawatan]->Nama ?? '-',
                'jumlah_terjual'  => (int) $item->jumlah_penjualan,
                'total_revenue'   => (float) $item->total_revenue,
            ];
        }),
    ]);
}

    public function downloadJenisPerawatan(Request $request)
    {
        $user = auth()->user();
        $klinikId = $request->klinik_id;
        if (!$user->hasRole('Superadmin') && !$user->hasRole('Management')) {
            $klinikId = $user->kodeperusahaan;
        }

        // Cast ke array (saat pilih 1 item)
        $jenisPerawatanIds = $request->jenis_perawatan;
        if (is_string($jenisPerawatanIds)) {
            $jenisPerawatanIds = [$jenisPerawatanIds];
        }
        $jenisPerawatanIds = $jenisPerawatanIds ?: [];

        $query = TransaksiDetail::select(
                'transaksi_details.JenisPerawatan',
                \DB::raw('COUNT(*) as jumlah_penjualan'),
                \DB::raw('COALESCE(SUM(transaksi_details.Biaya), 0) as total_revenue')
            )
            ->join('transaksis', 'transaksis.id', '=', 'transaksi_details.IdTransaksi')
            ->when($klinikId, function ($q) use ($klinikId) {
                $q->where('transaksis.KodeCabang', $klinikId);
            })
            ->when($request->tanggal_mulai, function ($q) use ($request) {
                $q->whereDate('transaksis.Tanggal', '>=', $request->tanggal_mulai);
            })
            ->when($request->tanggal_akhir, function ($q) use ($request) {
                $q->whereDate('transaksis.Tanggal', '<=', $request->tanggal_akhir);
            })
            ->when(!empty($jenisPerawatanIds), function ($q) use ($jenisPerawatanIds) {
                $q->whereIn('transaksi_details.JenisPerawatan', $jenisPerawatanIds);
            })
            ->groupBy('transaksi_details.JenisPerawatan')
            ->orderByDesc('total_revenue')
            ->get();

        $masterJenisPerawatan =MasterJenisPerawatan::whereIn('id', $query->pluck('JenisPerawatan'))->get()->keyBy('id');

        // Info untuk Excel header
        $klinikNama = $klinikId
            ? (MasterKlinik::where('Kode', $klinikId)->value('Nama') ?? $klinikId)
            : 'Semua';

        $jenisPerawatanNama = 'Semua';
        if (!empty($jenisPerawatanIds)) {
            $jenisPerawatanNama = MasterJenisPerawatan::whereIn('id', $jenisPerawatanIds)
                ->pluck('Nama')->implode(', ');
        }

        $filterInfo = [
            'klinik' => $klinikNama,
            'jenis_perawatan' => $jenisPerawatanNama,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_akhir' => $request->tanggal_akhir,
        ];

        // Sesuaikan structure data untuk export
        $data = $query->map(function ($item) use ($masterJenisPerawatan) {
            return [
                'id'              => $item->JenisPerawatan,
                'nama_perawatan'  => $masterJenisPerawatan[$item->JenisPerawatan]->Nama ?? '-',
                'jumlah_terjual'  => (int) $item->jumlah_penjualan,
                'total_revenue'   => (float) $item->total_revenue,
            ];
        });

        return Excel::download(
            new JenisPerawatanExport($data, $filterInfo),
            'Laporan-Jenis-Perawatan-' . date('Y-m-d') . '.xlsx'
        );
    }
}
