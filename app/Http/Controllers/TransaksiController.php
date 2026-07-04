<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisPerawatan;
use App\Models\MasterKlinik;
use App\Models\MasterMetodePembayaran;
use App\Models\MasterShift;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\InsentifService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $kodeCabang = $user->kodeperusahaan;
            $tanggalMulai = $request->input('tanggal_mulai');
            $tanggalAkhir = $request->input('tanggal_akhir');
            $shiftId = $request->input('shift');

            $data = Transaksi::with('TransaksiDetail')
                ->when(!$tanggalMulai && !$tanggalAkhir, function ($query) {
                    $query->whereDate('created_at', today());
                })
                ->when($tanggalMulai, function ($query) use ($tanggalMulai) {
                    $query->whereDate('created_at', '>=', $tanggalMulai);
                })
                ->when($tanggalAkhir, function ($query) use ($tanggalAkhir) {
                    $query->whereDate('created_at', '<=', $tanggalAkhir);
                })
                ->when($shiftId, function ($query) use ($shiftId) {
                    $query->where('Shift', $shiftId);
                })
                ->when($user->hasRole('Superadmin'), function ($query) use ($request) {
                    // Jika Superadmin, boleh filter kode cabang dari input klinik (dropdown)
                    if ($request->filled('klinik') && $request->input('klinik') != '') {
                        $query->where('KodeCabang', $request->input('klinik'));
                    }
                }, function ($query) use ($kodeCabang) {
                    // Selain Superadmin, pakai dari user login
                    $query->where('KodeCabang', $kodeCabang);
                })
                ->latest();

            $summary = [
                'total_omset' => (clone $data)->sum('TotalBayar'),
                'pasien_baru' => (clone $data)->where('JenisPasien', 'Baru')->count(),
                'pasien_lama' => (clone $data)->where('JenisPasien', 'Lama')->count(),
                'pasien_total' => (clone $data)->count(),
            ];

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('TotalBayar', function ($row) {
                    return 'Rp ' . number_format($row->TotalBayar, 0, ',', '.');
                })
                ->addColumn('MetodePembayaran', function ($row) {
                    if (!$row->getMetodePembayaran || $row->getMetodePembayaran->isEmpty()) {
                        return '-';
                    }
                    $html = '<dl class="mb-0">';
                    foreach ($row->getMetodePembayaran as $pembayaran) {
                        $nama = e($pembayaran->getMetodeBayar->Nama ?? '-');
                        $nominal = number_format($pembayaran->Nominal ?? 0, 0, ',', '.');
                        $html .= '<dt style="font-weight:500;">' . $nama . '</dt>';
                        $html .= '<dd style="margin-bottom:4px;">Rp ' . $nominal . '</dd>';
                    }
                    $html .= '</dl>';
                    return $html;
                })
                ->addColumn('Shift', function ($row) {
                    if (!$row->getShift)
                        return '-';
                    $nama = strtolower($row->getShift->Nama);
                    if ($nama === 'pagi' || $row->getShift->id == 1) {
                        return '<span class="badge bg-warning text-dark"><i class="fa fa-sun me-1"></i>Pagi</span>';
                    } elseif ($nama === 'siang' || $row->getShift->id == 2) {
                        return '<span class="badge bg-primary"><i class="fa fa-moon me-1"></i>Siang</span>';
                    }
                    return '<span class="badge bg-secondary">' . e($row->getShift->Nama) . '</span>';
                })
                ->addColumn('JenisPasien', function ($row) {
                    $jenis = $row->JenisPasien ?? '-';
                    if ($jenis === 'Baru')
                        return '<span class="badge bg-success"><i class="fa fa-user-plus me-1"></i>Baru</span>';
                    if ($jenis === 'Lama')
                        return '<span class="badge bg-info"><i class="fa fa-user-check me-1"></i>Lama</span>';
                    return '-';
                })
                ->addColumn('Layanan', function ($row) {
                    if (!$row->TransaksiDetail || count($row->TransaksiDetail) === 0)
                        return '-';
                    $rekap = [];
                    foreach ($row->TransaksiDetail as $detail) {
                        $nama = optional($detail->MasterJenisPerawatan)->Nama;
                        $biaya = (int) ($detail->Biaya ?? 0);
                        $keterangan = $detail->Keterangan ?? null;
                        if ($nama) {
                            if (!isset($rekap[$nama])) {
                                $rekap[$nama] = [
                                    'nama' => $nama,
                                    'harga' => 0,
                                    'count' => 0,
                                    'keterangan' => []
                                ];
                            }
                            $rekap[$nama]['harga'] += $biaya;
                            $rekap[$nama]['count'] += 1;
                            if ($keterangan && !in_array($keterangan, $rekap[$nama]['keterangan'])) {
                                $rekap[$nama]['keterangan'][] = $keterangan;
                            }
                        }
                    }
                    if (empty($rekap))
                        return '-';
                    $html = '<dl class="mb-0">';
                    foreach ($rekap as $item) {
                        $namaStr = e($item['nama']) . ($item['count'] > 1 ? ' x' . $item['count'] : '');
                        $html .= '<dt style="font-weight:500;">' . $namaStr . ':</dt>';
                        $html .= '<dd style="margin-bottom:4px;">Rp ' . number_format($item['harga'], 0, ',', '.') . '</dd>';
                        if (!empty($item['keterangan'])) {
                            foreach ($item['keterangan'] as $ket) {
                                $html .= '<dd style="margin-bottom:2px;"><small><i class="fa fa-info-circle me-1"></i>' . e($ket) . '</small></dd>';
                            }
                        }
                    }
                    $html .= '</dl>';
                    return $html;
                })
                ->addColumn('Petugas', function ($row) {
                    $dokter = $row->getDokter?->name ?? '-';
                    $perawat = $row->getPerawat?->name ?? '-';
                    $resepsionis = $row->getResepsionis?->name ?? '-';
                    $html = '<dl class="mb-0">';
                    $html .= '<dt style="font-weight:500;">Dokter</dt><dd>' . e($dokter) . '</dd>';
                    $html .= '<dt style="font-weight:500;">Perawat</dt><dd>' . e($perawat) . '</dd>';
                    $html .= '<dt style="font-weight:500;">Resepsionis</dt><dd>' . e($resepsionis) . '</dd>';
                    $html .= '</dl>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    $user = auth()->user();
                    $isKasir = method_exists($user, 'hasRole') && $user->hasRole('Kasir / Resepsionis');
                    $isAdmin = method_exists($user, 'hasRole') && $user->hasRole('Superadmin');

                    $actionButtons = '';

                    // Hanya tampilkan tombol edit dan hapus jika user adalah kasir atau admin
                    if ($isKasir || $isAdmin) {
                        $actionButtons .= '<a href="' . route('Transaksi.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i>
                        </a>';
                        $actionButtons .= '
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i>
                        </button>';
                    }

                    return $actionButtons;
                })
                ->rawColumns(['action', 'TotalBayar', 'Layanan', 'Petugas', 'JenisPasien', 'Shift', 'MetodePembayaran'])
                ->with(['summary' => $summary])
                ->make(true);
        }

        $shift = MasterShift::get();
        $klinik = MasterKlinik::get();
        return view('transaksi.kasir.index', compact('shift', 'klinik'));
    }

    public function indexKunjungan(Request $request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $kodeCabang = $user->kodeperusahaan;
            $searchNama = $request->input('search_nama');

            $data = Transaksi::with('TransaksiDetail')
                ->when(!$user->hasRole('Superadmin'), function ($query) use ($kodeCabang) {
                    $query->where('KodeCabang', $kodeCabang);
                })
                ->when($searchNama, function ($query) use ($searchNama) {
                    $query->where('NamaPasien', 'LIKE', '%' . $searchNama . '%');
                })
                ->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('TotalBayar', function ($row) {
                    return 'Rp ' . number_format($row->TotalBayar, 0, ',', '.');
                })
                ->addColumn('MetodePembayaran', function ($row) {
                    if (!$row->getMetodePembayaran || $row->getMetodePembayaran->isEmpty()) {
                        return '-';
                    }
                    $html = '<dl class="mb-0">';
                    foreach ($row->getMetodePembayaran as $pembayaran) {
                        $nama = e($pembayaran->getMetodeBayar->Nama ?? '-');
                        $nominal = number_format($pembayaran->Nominal ?? 0, 0, ',', '.');
                        $html .= '<dt style="font-weight:500;">' . $nama . '</dt>';
                        $html .= '<dd style="margin-bottom:4px;">Rp ' . $nominal . '</dd>';
                    }
                    $html .= '</dl>';
                    return $html;
                })
                ->addColumn('Shift', function ($row) {
                    if (!$row->getShift)
                        return '-';
                    $nama = strtolower($row->getShift->Nama);
                    if ($nama === 'pagi' || $row->getShift->id == 1) {
                        return '<span class="badge bg-warning text-dark"><i class="fa fa-sun me-1"></i>Pagi</span>';
                    } elseif ($nama === 'siang' || $row->getShift->id == 2) {
                        return '<span class="badge bg-primary"><i class="fa fa-moon me-1"></i>Siang</span>';
                    }
                    return '<span class="badge bg-secondary">' . e($row->getShift->Nama) . '</span>';
                })
                ->addColumn('JenisPasien', function ($row) {
                    $jenis = $row->JenisPasien ?? '-';
                    if ($jenis === 'Baru')
                        return '<span class="badge bg-success"><i class="fa fa-user-plus me-1"></i>Baru</span>';
                    if ($jenis === 'Lama')
                        return '<span class="badge bg-info"><i class="fa fa-user-check me-1"></i>Lama</span>';
                    return '-';
                })
                ->addColumn('TerakhirBerkunjung', function ($row) {
                    $lastVisit = \Carbon\Carbon::parse($row->last_visit);
                    $now = \Carbon\Carbon::parse($row->created_at);

                    $text = $now->diffForHumans($lastVisit);
                    $days = $now->diffInDays($lastVisit);

                    if ($days == 0) {
                        $class = 'bg-success';
                    } elseif ($days == 1) {
                        $class = 'bg-info';
                    } elseif ($days < 7) {
                        $class = 'bg-primary';
                    } elseif ($days < 30) {
                        $class = 'bg-warning text-dark';
                    } elseif ($days < 365) {
                        $class = 'bg-orange';
                    } else {
                        $class = 'bg-danger';
                    }

                    return '<span class="badge ' . $class . '">' . ucfirst($text) . '</span>';
                })
                ->addColumn('Layanan', function ($row) {
                    if (!$row->TransaksiDetail || count($row->TransaksiDetail) === 0)
                        return '-';
                    $rekap = [];
                    foreach ($row->TransaksiDetail as $detail) {
                        $nama = optional($detail->MasterJenisPerawatan)->Nama;
                        $biaya = (int) ($detail->Biaya ?? 0);
                        $keterangan = $detail->Keterangan ?? null;
                        if ($nama) {
                            if (!isset($rekap[$nama])) {
                                $rekap[$nama] = [
                                    'nama' => $nama,
                                    'harga' => 0,
                                    'count' => 0,
                                    'keterangan' => []
                                ];
                            }
                            $rekap[$nama]['harga'] += $biaya;
                            $rekap[$nama]['count'] += 1;
                            if ($keterangan && !in_array($keterangan, $rekap[$nama]['keterangan'])) {
                                $rekap[$nama]['keterangan'][] = $keterangan;
                            }
                        }
                    }
                    if (empty($rekap))
                        return '-';
                    $html = '<dl class="mb-0">';
                    foreach ($rekap as $item) {
                        $namaStr = e($item['nama']) . ($item['count'] > 1 ? ' x' . $item['count'] : '');
                        $html .= '<dt style="font-weight:500;">' . $namaStr . ':</dt>';
                        $html .= '<dd style="margin-bottom:4px;">Rp ' . number_format($item['harga'], 0, ',', '.') . '</dd>';
                        if (!empty($item['keterangan'])) {
                            foreach ($item['keterangan'] as $ket) {
                                $html .= '<dd style="margin-bottom:2px;"><small><i class="fa fa-info-circle me-1"></i>' . e($ket) . '</small></dd>';
                            }
                        }
                    }
                    $html .= '</dl>';
                    return $html;
                })
                ->addColumn('Petugas', function ($row) {
                    $dokter = $row->getDokter?->name ?? '-';
                    $perawat = $row->getPerawat?->name ?? '-';
                    $resepsionis = $row->getResepsionis?->name ?? '-';
                    $html = '<dl class="mb-0">';
                    $html .= '<dt style="font-weight:500;">Dokter</dt><dd>' . e($dokter) . '</dd>';
                    $html .= '<dt style="font-weight:500;">Perawat</dt><dd>' . e($perawat) . '</dd>';
                    $html .= '<dt style="font-weight:500;">Resepsionis</dt><dd>' . e($resepsionis) . '</dd>';
                    $html .= '</dl>';
                    return $html;
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);

                    if (auth()->user()->hasRole('Kasir / Resepsionis') || auth()->user()->hasRole('Superadmin')) {
                        return '
                            <a href="' . route('Transaksi.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                                <i class="fa fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                                <i class="fa fa-trash"></i>
                            </button>
                        ';
                    } else {
                        return '';
                    }
                })
                ->rawColumns(['action', 'TotalBayar', 'Layanan', 'Petugas', 'JenisPasien', 'Shift', 'MetodePembayaran', 'TerakhirBerkunjung'])
                ->make(true);
        }

        return view('transaksi.kasir.history-pembayaran');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $Perawatan = MasterJenisPerawatan::get();

        // Ambil shift aktif saat ini
        $shift = MasterShift::whereTime('JamMulai', '<=', now()->format('H:i:s'))
            ->whereTime('JamSelesai', '>=', now()->format('H:i:s'))
            ->first();
        // dd($shift);
        $kodeCabang = auth()->user()->kodeperusahaan;

        $totalPasienBaru = Transaksi::where('JenisPasien', 'Baru')
            ->where('Shift', optional($shift)->id)
            ->where('KodeCabang', $kodeCabang)
            ->whereDate('created_at', today())
            ->count();

        // Hitung total pasien lama pada shift ini
        $totalPasienLama = Transaksi::where('JenisPasien', 'Lama')
            ->where('Shift', optional($shift)->id)
            ->where('KodeCabang', $kodeCabang)
            ->whereDate('created_at', today())
            ->count();

        $MetodePembayaran = MasterMetodePembayaran::where('Status', 'Y')->get();
        $kodeCabang = auth()->user()->kodeperusahaan;
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->where('KodePerusahaan', $kodeCabang)->get();
        $kasir = User::role('Kasir / Resepsionis')->where('KodePerusahaan', $kodeCabang)->get();

        return view('transaksi.kasir.create', compact('Perawatan', 'MetodePembayaran', 'dokter', 'perawat', 'kasir', 'totalPasienLama', 'totalPasienBaru'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'Tanggal' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $inputDate = strtotime($value);
                    $today = strtotime(date('Y-m-d'));
                    // Mengizinkan backdate, cukup validasi tidak bisa ke depan (future date)
                    if ($inputDate > $today) {
                        $fail('Tidak boleh memilih tanggal ke depan (future date).');
                    }
                }
            ],

            'NamaPasien' => 'required|string|max:255',
            'JenisPasien' => 'required|in:Baru,Lama',
            'JenisPerawatan' => 'required|array|min:1',
            'JenisPerawatan.*.id' => 'required|exists:master_jenis_perawatans,id',
            'JenisPerawatan.*.Biaya' => 'required|numeric|min:0',
            'Dokter' => 'required|exists:users,id',
            'Perawat' => 'required|exists:users,id',
            'Kasir' => 'required|exists:users,id',
            'BiayaAdmin' => 'required|numeric|min:0',
            'MetodePembayaran' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (is_array($value)) {
                        foreach ($value as $item) {
                            if (is_null($item) || $item === '') {
                                $fail('Metode pembayaran tidak boleh kosong.');
                                break;
                            }
                        }
                    }
                }
            ],

            'NominalBayar' => 'required|array',
            'NominalBayar.*' => 'required|numeric|min:0',
            'TotalBiaya' => 'required|numeric|min:0',
        ], [
            'Tanggal.required' => 'Tanggal wajib diisi',
            'Tanggal.date' => 'Format tanggal tidak valid',
            'NamaPasien.required' => 'Nama pasien wajib diisi',
            'JenisPasien.required' => 'Jenis pasien wajib dipilih',
            'JenisPerawatan.required' => 'Minimal 1 perawatan harus dipilih',
            'JenisPerawatan.*.id.required' => 'Silakan pilih jenis perawatan',
            'JenisPerawatan.*.id.exists' => 'Perawatan tidak valid',
            'JenisPerawatan.*.Biaya.required' => 'Biaya perawatan wajib diisi',
            'JenisPerawatan.*.Biaya.numeric' => 'Biaya perawatan harus angka',
            'Dokter.required' => 'Pilih dokter',
            'Dokter.exists' => 'Dokter tidak valid',
            'Perawat.required' => 'Pilih perawat',
            'Perawat.exists' => 'Perawat tidak valid',
            'Kasir.required' => 'Pilih kasir/resepsionis',
            'Kasir.exists' => 'Kasir tidak valid',
            'BiayaAdmin.required' => 'Biaya admin wajib diisi',
            'BiayaAdmin.numeric' => 'Biaya admin harus angka',
            'MetodePembayaran.required' => 'Pilih minimal satu metode pembayaran',
            'MetodePembayaran.array' => 'Metode pembayaran harus berupa array',
            'TotalBiaya.required' => 'Total biaya wajib diisi',
            'TotalBiaya.numeric' => 'Total biaya harus angka'
        ]);
        $shiftId = auth()->user()->shift;
        $transaksi = Transaksi::create([
            'Tanggal' => $request->Tanggal,
            'NamaPasien' => $request->NamaPasien,
            'JenisPasien' => $request->JenisPasien,
            'BiayaAdmin' => $request->BiayaAdmin,
            'TotalBayar' => $request->TotalBiaya,
            'IdResepsionis' => $request->Kasir,
            'IdPerawat' => $request->Perawat,
            'IdDokter' => $request->Dokter,
            'Shift' => $shiftId,
            'UserCreate' => auth()->user()->name,
            'UserUpdate' => null,
            'UserDelete' => null,
            'KodeCabang' => auth()->user()->kodeperusahaan,
        ]);

        // Simpan Detail Perawatan
        if ($request->has('JenisPerawatan') && is_array($request->JenisPerawatan)) {
            foreach ($request->JenisPerawatan as $perawatan) {
                if (
                    isset($perawatan['id'], $perawatan['Biaya']) &&
                    $perawatan['id'] !== null &&
                    $perawatan['Biaya'] !== null
                ) {
                    $transaksi->TransaksiDetail()->create([
                        'IdTransaksi' => $transaksi->id,
                        'JenisPerawatan' => $perawatan['id'],
                        'Keterangan' => $perawatan['Keterangan'],
                        'Biaya' => $perawatan['Biaya'],
                        'UserCreate' => auth()->user()->name,
                        'UserUpdate' => null,
                        'UserDelete' => null,
                    ]);
                }
            }
        }

        if ($request->has('MetodePembayaran') && is_array($request->MetodePembayaran)) {
            foreach ($request->MetodePembayaran as $key => $metode) {
                if ($metode !== null) {
                    $transaksi->getMetodePembayaran()->create([
                        'IdTransaksi' => $transaksi->id,
                        'MetodePembayaran' => $metode,
                        'Nominal' => isset($request->NominalBayar[$key]) ? $request->NominalBayar[$key] : 0,
                    ]);
                }
            }
        }
        app(InsentifService::class)->proses($transaksi);
        return redirect()->route('Transaksi.index')->with('success', 'Transaksi berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaksi $transaksi)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);

        // TAMBAHKAN 'TransaksiPembayaran' di dalam with()
        // (Sesuaikan nama relasi jika berbeda, misal: 'Pembayaran' atau 'TransaksiMetodePembayaran')
        $transaksi = Transaksi::with(['TransaksiDetail', 'getMetodePembayaran', 'getPerawat', 'getDokter', 'getResepsionis'])
            ->findOrFail($id);

        $Perawatan = MasterJenisPerawatan::where('Status', 'Y')->get();
        $shift = MasterShift::whereTime('JamMulai', '<=', now()->format('H:i:s'))
            ->whereTime('JamSelesai', '>=', now()->format('H:i:s'))
            ->first();

        $kodeCabang = auth()->user()->kodeperusahaan;
        $totalPasienBaru = Transaksi::where('JenisPasien', 'Baru')
            ->where('Shift', optional($shift)->id)
            ->where('KodeCabang', $kodeCabang)
            ->whereDate('created_at', today())
            ->count();

        $totalPasienLama = Transaksi::where('JenisPasien', 'Lama')
            ->where('Shift', optional($shift)->id)
            ->where('KodeCabang', $kodeCabang)
            ->whereDate('created_at', today())
            ->count();

        $MetodePembayaran = MasterMetodePembayaran::where('Status', 'Y')->get();
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->where('KodePerusahaan', $transaksi->KodeCabang)->get();
        $kasir = User::role('Kasir / Resepsionis')->where('KodePerusahaan', $transaksi->KodeCabang)->get();


        // dd($transaksi);
        return view('transaksi.kasir.edit', compact(
            'transaksi',
            'Perawatan',
            'MetodePembayaran',
            'dokter',
            'perawat',
            'kasir',
            'totalPasienBaru',
            'totalPasienLama',
            'shift'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // dd($request->all());
        try {
            $decodedId = decrypt($id);
        } catch (\Exception $e) {
            return redirect()->route('Transaksi.index')->with('error', 'ID Transaksi tidak valid.');
        }

        $transaksi = Transaksi::with('TransaksiDetail', 'getMetodePembayaran')->findOrFail($decodedId);
        $validatedData = $request->validate([
            'Tanggal' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $inputDate = strtotime($value);
                    $today = strtotime(date('Y-m-d'));
                    if ($inputDate > $today) {
                        $fail('Tidak boleh memilih tanggal ke depan (future date).');
                    }
                }
            ],

            'NamaPasien' => 'required|string|max:255',
            'JenisPasien' => 'required|in:Baru,Lama',
            'JenisPerawatan' => 'required|array|min:1',
            'JenisPerawatan.*.id' => 'required|exists:master_jenis_perawatans,id',
            'JenisPerawatan.*.Biaya' => 'required|numeric|min:0',
            'Dokter' => 'required|exists:users,id',
            'Perawat' => 'required|exists:users,id',
            'Kasir' => 'required|exists:users,id',
            'BiayaAdmin' => 'required|numeric|min:0',
            'MetodePembayaran' => ['required', 'array', 'min:1'],
            'MetodePembayaran.*' => 'required|integer|exists:master_metode_pembayarans,id', // ensure no null values, and exists in master
            'NominalBayar' => 'required|array',
            'NominalBayar.*' => 'required|numeric|min:0',
            'TotalBiaya' => 'required|numeric|min:0',
        ], [
            'Tanggal.required' => 'Tanggal wajib diisi',
            'Tanggal.date' => 'Format tanggal tidak valid',
            'NamaPasien.required' => 'Nama pasien wajib diisi',
            'JenisPasien.required' => 'Jenis pasien wajib dipilih',
            'JenisPerawatan.required' => 'Minimal 1 perawatan harus dipilih',
            'JenisPerawatan.*.id.required' => 'Silakan pilih jenis perawatan',
            'JenisPerawatan.*.id.exists' => 'Perawatan tidak valid',
            'JenisPerawatan.*.Biaya.required' => 'Biaya perawatan wajib diisi',
            'JenisPerawatan.*.Biaya.numeric' => 'Biaya perawatan harus angka',
            'Dokter.required' => 'Pilih dokter',
            'Dokter.exists' => 'Dokter tidak valid',
            'Perawat.required' => 'Pilih perawat',
            'Perawat.exists' => 'Perawat tidak valid',
            'Kasir.required' => 'Pilih kasir/resepsionis',
            'Kasir.exists' => 'Kasir tidak valid',
            'BiayaAdmin.required' => 'Biaya admin wajib diisi',
            'BiayaAdmin.numeric' => 'Biaya admin harus angka',
            'MetodePembayaran.required' => 'Pilih minimal satu metode pembayaran',
            'MetodePembayaran.array' => 'Metode pembayaran harus berupa array',
            'MetodePembayaran.*.required' => 'Metode pembayaran tidak boleh kosong',
            'MetodePembayaran.*.integer' => 'Metode pembayaran tidak boleh kosong',
            'MetodePembayaran.*.exists' => 'Metode pembayaran tidak valid',
            'NominalBayar.required' => 'Minimal satu nominal pembayaran harus diisi',
            'NominalBayar.array' => 'Nominal pembayaran harus berupa array',
            'NominalBayar.*.required' => 'Nominal pembayaran tidak boleh kosong atau nol',
            'NominalBayar.*.numeric' => 'Nominal pembayaran harus angka',
            'NominalBayar.*.min' => 'Nominal pembayaran harus lebih dari 0',
            'TotalBiaya.required' => 'Total biaya wajib diisi',
            'TotalBiaya.numeric' => 'Total biaya harus angka'
        ]);

        // dd($metodes = is_array($request->MetodePembayaran) ? $request->MetodePembayaran : [$request->MetodePembayaran]);
        // 🔄 Update Header Transaksi
        $transaksi->update([
            'Tanggal' => $request->Tanggal,
            'NamaPasien' => $request->NamaPasien,
            'JenisPasien' => $request->JenisPasien,
            'MetodePembayaran' => $request->MetodePembayaran,
            'BiayaAdmin' => $request->BiayaAdmin,
            'TotalBayar' => $request->TotalBiaya,
            'IdResepsionis' => $request->Kasir,
            'IdPerawat' => $request->Perawat,
            'IdDokter' => $request->Dokter,
            'UserUpdate' => auth()->user()->name,
        ]);
        $transaksi->TransaksiDetail()->delete();

        if ($request->has('JenisPerawatan') && is_array($request->JenisPerawatan)) {
            foreach ($request->JenisPerawatan as $perawatan) {
                if (
                    isset($perawatan['id'], $perawatan['Biaya']) &&
                    $perawatan['id'] !== null &&
                    $perawatan['Biaya'] !== null
                ) {
                    $transaksi->TransaksiDetail()->create([
                        'IdTransaksi' => $transaksi->id,
                        'JenisPerawatan' => $perawatan['id'],
                        'Biaya' => $perawatan['Biaya'],
                        'Keterangan' => $perawatan['Keterangan'],
                        'UserCreate' => auth()->user()->name,
                        'UserUpdate' => null,
                        'UserDelete' => null,
                    ]);
                }
            }
        }
        if ($transaksi->getMetodePembayaran) {
            $transaksi->getMetodePembayaran()->delete();
        }
        if ($request->has('MetodePembayaran') && !empty($request->MetodePembayaran)) {
            $metodes = is_array($request->MetodePembayaran) ? $request->MetodePembayaran : [$request->MetodePembayaran];
            foreach ($metodes as $key => $metode) {
                $transaksi->getMetodePembayaran()->create([
                    'IdTransaksi' => $transaksi->id,
                    'MetodePembayaran' => $metode,
                    'Nominal' => isset($request->NominalBayar[$key]) ? $request->NominalBayar[$key] : 0,
                    'UserCreate' => auth()->user()->name,
                    'UserUpdate' => null,
                    'UserDelete' => null,
                ]);
            }
        }

        app(InsentifService::class)->hapusSebelumProses($transaksi);
        app(InsentifService::class)->proses($transaksi);

        return redirect()->route('Transaksi.index')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        try {
            $transaksi = Transaksi::findOrFail($id);
            // dd($transaksi);
            $transaksi->TransaksiDetail()->delete();
            $transaksi->getMetodePembayaran()->delete();
            $transaksi->delete();

            return response()->json([
                'status' => 200,
                'message' => 'Transaksi berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Gagal menghapus transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getShift($tanggal)
    {
        $jam = Carbon::parse($tanggal)->format('H:i:s');

        $shift = MasterShift::whereTime('JamMulai', '<=', $jam)
            ->whereTime('JamSelesai', '>', $jam)
            ->first();

        return $shift ? $shift->id : null;
    }
}
