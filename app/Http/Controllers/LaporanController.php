<?php

namespace App\Http\Controllers;

use App\Models\InsentifKaryawan;
use App\Models\MasterMetodePembayaran;
use App\Models\MasterShift;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
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
            ->with('getMetodePembayaran')  // assuming the relation name is getMetodePembayaran
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
            ->with('getMetodePembayaran')  // assuming the relation name is getMetodePembayaran
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
            'perawat' => 'required|exists:users,id',
            'shift' => 'nullable|exists:transaksis,Shift',
        ], [
            'FilterTanggal.required' => 'Tanggal periode wajib diisi.',
            'FilterTanggal.regex' => 'Format tanggal tidak sesuai (00/00/0000 - 00/00/0000).',
            'perawat.required' => 'Perawat wajib dipilih.',
            'perawat.exists' => 'Perawat tidak valid.',
            'shift.exists' => 'Shift tidak valid.',
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
        $kodeCabang = auth()->user()->kodeperusahaan;

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
        $countpasienlama = Transaksi::where('JenisPasien', 'Lama')->count();
        $Shift8PasienLama = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'pasien_lama')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d') . '|' . $item->Shift)
            ->map(function ($group) use ($countpasienlama) {
                $first = $group->first();
                return [
                    'created_at' => $first->created_at,
                    'jumlah_pasien_lama' => $countpasienlama . ' Pasien', // atau bisa custom sesuai kebutuhan judul kolom
                    'perawat_nama' => $first->getUser->name ?? '-',
                    'insentif' => $group->sum('Nominal'),
                ];
            })
            ->values();


        // dd($Shift8PasienLama);
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
        // dd($PasienLama);
        // 5i. Ringkasan per JenisRule
        $jenisRuleInfo = [
            'omzet_shift' => ['label' => 'Omset Shift ≥ Rp 6.000.000', 'badge' => 'bg-primary', 'order' => 1],
            'transaksi' => ['label' => 'Transaksi ≥ Rp 1.000.000', 'badge' => 'bg-success', 'order' => 2],
            'pasien_lama' => ['label' => '8 Pasien Lama per Shift', 'badge' => 'bg-info', 'order' => 3],
            'pasien_baru' => ['label' => 'Pasien Baru', 'badge' => 'bg-danger', 'order' => 4],
            'tindakan' => ['label' => 'Tindakan Odontektomi', 'badge' => 'bg-warning text-white', 'order' => 5],
        ];

        // Ambil sum per JenisRule sekali query
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
        $perawat = User::role('Kasir / Resepsionis')->get();
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
        // =============================================
        // 1. VALIDASI
        // =============================================
        $validator = Validator::make($request->all(), [
            'FilterTanggal' => [
                'required',
                'string',
                'regex:/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}\s\-\s[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/'
            ],
            'perawat' => 'required|exists:users,id',
        ], [
            'FilterTanggal.required' => 'Tanggal periode wajib diisi.',
            'FilterTanggal.regex' => 'Format tanggal tidak sesuai (00/00/0000 - 00/00/0000).',
            'perawat.required' => 'Kasir / Resepsionis wajib dipilih.',
            'perawat.exists' => 'Kasir / Resepsionis tidak valid.',
        ]);

        if ($validator->fails()) {
            $errorHtml = collect(['FilterTanggal', 'perawat'])
                ->filter(fn($field) => $validator->errors()->has($field))
                ->map(fn($field) => $validator->errors()->first($field))
                ->implode('<br>');

            return redirect()
                ->route('laporan-resepsionis.index')
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
        $kasirId = $request->perawat;  // nama field di form tetap 'perawat'
        $kodeCabang = auth()->user()->kodeperusahaan;

        // =============================================
        // 3. BASE SCOPE
        //    Resepsionis tidak punya filter shift,
        //    kolom yang dipakai: IdKasir (transaksi) & UserId (insentif)
        //    — sesuaikan nama kolom dengan skema DB kamu
        // =============================================
        $scopeTransaksi = fn($q) => $q
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang)
            ->where('IdResepsionis', $kasirId);  // ← ganti sesuai kolom FK kasir di tabel transaksis

        $scopeInsentif = fn($q) => $q
            ->whereBetween('created_at', [$startDate, $endDate])
            ->where('KodeCabang', $kodeCabang)
            ->where('UserId', $kasirId);

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

        // 5a. Omset & Total Shift (array agar konsisten dengan view)
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
        //     View akses: $row['created_at'], $row['jumlah_pasien_lama'], $row['perawat_nama']
        //     → perlu di-group & di-map agar strukturnya cocok
        $Shift8PasienLama = InsentifKaryawan::with(['getTransaksi', 'getUser'])
            ->where($scopeInsentif)
            ->where('JenisRule', 'pasien_lama')
            ->get()
            ->groupBy(fn($item) => $item->created_at->format('Y-m-d') . '|' . $item->Shift)
            ->map(function ($group) {
                $first = $group->first();
                return [
                    'created_at' => $first->created_at,
                    'jumlah_pasien_lama' => $group->count(),
                    'perawat_nama' => $first->getUser->name ?? '-',
                    'insentif' => $group->sum('Nominal'),
                ];
            })
            ->values();

        // 5f. Ringkasan per JenisRule (hanya rule yang aktif di view resepsionis)
        $jenisRuleInfo = [
            'omzet_shift' => ['label' => 'Shift ≥ Rp 6.000.000 / 2 ≥ Rp 12.000.000', 'badge' => 'bg-primary', 'order' => 1],
            'pasien_lama' => ['label' => 'Shift dengan 8 Pasien Lama', 'badge' => 'bg-info', 'order' => 2],
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
        })->values();

        // =============================================
        // 6. DROPDOWN
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
            'Ringkasan' => $Ringkasan,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'kasirId' => $kasirId,
            'kodeCabang' => $kodeCabang,
        ];

        return view('laporan.resepsionis.index', compact('dokter', 'perawat', 'kasir', 'shift', 'data'));
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

        return view('laporan.dokter.index', compact('TotalPasienBaru', 'TotalBiayaAdmin', 'dataTransaksi', 'TotalPerawatan', 'TotalBiayaPerawatan', 'TotalPasienLama', 'TotalPasien', 'dokter', 'perawat', 'kasir', 'shift'));
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
            'dokter' => 'required|exists:users,id',
            'shift' => 'nullable|exists:transaksis,Shift',
        ], [
            'FilterTanggal.required' => 'Tanggal periode wajib diisi.',
            'FilterTanggal.regex' => 'Format tanggal tidak sesuai (00/00/0000 - 00/00/0000).',
            'dokter.required' => 'Dokter wajib dipilih.',
            'dokter.exists' => 'Dokter tidak valid.',
            'shift.exists' => 'Shift tidak valid.',
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
        $startDate = Carbon::createFromFormat('m/d/Y', trim($startRaw))->startOfDay();
        $endDate = Carbon::createFromFormat('m/d/Y', trim($endRaw))->endOfDay();
        $dokterId = $request->dokter;
        $shiftFilter = $request->filled('shift') ? $request->shift : null;
        $kodeCabang = auth()->user()->kodeperusahaan;

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

        // 5e. Rincian per jenis perawatan (sidebar)
        $RincianJenisPerawatan = TransaksiDetail::with('MasterJenisPerawatan')
            ->selectRaw('JenisPerawatan, COUNT(*) as jumlah, AVG(Biaya) as rata_rata_biaya')
            ->whereNotNull('JenisPerawatan')
            ->whereHas('getTransaksi', $scopeTransaksi)
            ->groupBy('JenisPerawatan')
            ->orderByDesc('jumlah')
            ->limit(10)
            ->get();

        // =============================================
        // 6. DROPDOWN
        // =============================================
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();

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
        ));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
