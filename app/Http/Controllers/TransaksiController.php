<?php

namespace App\Http\Controllers;

use App\Models\DentalUnit;
use App\Models\MasterJenisPerawatan;
use App\Models\MasterKlinik;
use App\Models\MasterMetodePembayaran;
use App\Models\MasterShift;
use App\Models\Stok;
use App\Models\StokMutasi;
use App\Models\Transaksi;
use App\Models\User;
use App\Services\InsentifService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use DB;
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
            $dentalUnitId = $request->input('dental_unit'); // 🔥 Ambil parameter dental unit

            $data = Transaksi::with(['TransaksiDetail', 'getDentalUnit'])
                ->when(!$tanggalMulai && !$tanggalAkhir, function ($query) {
                    $query->whereDate('Tanggal', today());
                })
                ->when($tanggalMulai, function ($query) use ($tanggalMulai) {
                    $query->whereDate('Tanggal', '>=', $tanggalMulai);
                })
                ->when($tanggalAkhir, function ($query) use ($tanggalAkhir) {
                    $query->whereDate('Tanggal', '<=', $tanggalAkhir);
                })
                ->when($shiftId, function ($query) use ($shiftId) {
                    $query->where('Shift', $shiftId);
                })
                // 🔥 FILTER DENTAL UNIT
                ->when($dentalUnitId, function ($query) use ($dentalUnitId) {
                    $query->where('DentalUnit', $dentalUnitId);
                })
                ->when($user->hasRole('Superadmin'), function ($query) use ($request) {
                    if ($request->filled('klinik') && $request->input('klinik') != '') {
                        $query->where('KodeCabang', $request->input('klinik'));
                    }
                }, function ($query) use ($kodeCabang) {
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
                ->addColumn('Kode', function ($row) {
                    $kode = $row->Kode ?? '-';
                    if ($row->getDentalUnit) {
                        $dentalUnitName = $row->getDentalUnit->Nama ?? $row->getDentalUnit->name ?? null;
                        if ($dentalUnitName) {
                            $kode .= ' - ' . e($dentalUnitName);
                        } else {
                            $kode .= ' - Dental Unit';
                        }
                    }
                    return $kode;
                })
                ->addColumn('TotalBayar', function ($row) {
                    return 'Rp ' . number_format($row->TotalBayar, 0, ',', '.');
                })
                ->addColumn('MetodePembayaran', function ($row) {
                    if (!$row->getMetodePembayaran || $row->getMetodePembayaran->isEmpty()) return '-';
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
                    if (!$row->getShift) return '-';
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
                    if ($jenis === 'Baru') return '<span class="badge bg-success"><i class="fa fa-user-plus me-1"></i>Baru</span>';
                    if ($jenis === 'Lama') return '<span class="badge bg-info"><i class="fa fa-user-check me-1"></i>Lama</span>';
                    return '-';
                })
                ->addColumn('Layanan', function ($row) {
                    if (!$row->TransaksiDetail || count($row->TransaksiDetail) === 0) return '-';
                    $rekap = [];
                    foreach ($row->TransaksiDetail as $detail) {
                        $nama = optional($detail->MasterJenisPerawatan)->Nama;
                        $biaya = (int) ($detail->Biaya ?? 0);
                        $keterangan = $detail->Keterangan ?? null;
                        if ($nama) {
                            if (!isset($rekap[$nama])) {
                                $rekap[$nama] = ['nama' => $nama, 'harga' => 0, 'count' => 0, 'keterangan' => []];
                            }
                            $rekap[$nama]['harga'] += $biaya;
                            $rekap[$nama]['count'] += 1;
                            if ($keterangan && !in_array($keterangan, $rekap[$nama]['keterangan'])) {
                                $rekap[$nama]['keterangan'][] = $keterangan;
                            }
                        }
                    }
                    if (empty($rekap)) return '-';
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
                    if ($isKasir || $isAdmin) {
                        $actionButtons .= '<a href="' . route('Transaksi.edit', $encryptedId) . '" class="btn btn-sm btn-warning"><i class="fa fa-edit"></i></a>';
                        $actionButtons .= '<button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '"><i class="fa fa-trash"></i></button>';
                    }
                    return $actionButtons;
                })
                ->rawColumns(['action', 'Kode', 'TotalBayar', 'Layanan', 'Petugas', 'JenisPasien', 'Shift', 'MetodePembayaran'])
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
        $dental = DentalUnit::where('KodeCabang',auth()->user()->kodeperusahaan)->get();
        return view('transaksi.kasir.create', compact('dental','Perawatan', 'MetodePembayaran', 'dokter', 'perawat', 'kasir', 'totalPasienLama', 'totalPasienBaru'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. VALIDASI DATA
        $validatedData = $request->validate([
            'Tanggal' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) > strtotime(date('Y-m-d'))) {
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
            'DentalUnit' => 'nullable',
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
            'NominalBayar.*' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $matches = [];
                    if (preg_match('/^NominalBayar\.(\d+)$/', $attribute, $matches)) {
                        $totalNominalBayar = array_sum($request->NominalBayar);
                        $totalBiaya = $request->TotalBiaya;

                        if ($totalNominalBayar > $totalBiaya) {
                            $fail('Akumulasi nominal bayar tidak boleh lebih dari total biaya.');
                        } elseif ($totalNominalBayar < $totalBiaya) {
                            $fail('Akumulasi nominal bayar tidak boleh kurang dari total biaya.');
                        }
                    }
                }
            ],
            'TotalBiaya' => 'required|numeric|min:0',
        ], [
            'Tanggal.required' => 'Tanggal wajib diisi',
            'NamaPasien.required' => 'Nama pasien wajib diisi',
            'JenisPasien.required' => 'Jenis pasien wajib dipilih',
            'JenisPerawatan.required' => 'Minimal 1 perawatan harus dipilih',
            'Dokter.required' => 'Pilih dokter',
            'Perawat.required' => 'Pilih perawat',
            'Kasir.required' => 'Pilih kasir/resepsionis',
            'BiayaAdmin.required' => 'Biaya admin wajib diisi',
            'MetodePembayaran.required' => 'Pilih minimal satu metode pembayaran',
            'TotalBiaya.required' => 'Total biaya wajib diisi',
        ]);

        $shiftId = auth()->user()->shift;
        $kodeCabang = auth()->user()->kodeperusahaan;
        $userName = auth()->user()->name;

        // 🔥 2. MULAI DATABASE TRANSACTION
        DB::beginTransaction();

        try {
            // 3. SIMPAN TRANSAKSI UTAMA
            $transaksi = Transaksi::create([
                'Tanggal' => $request->Tanggal,
                'NamaPasien' => $request->NamaPasien,
                'JenisPasien' => $request->JenisPasien,
                'BiayaAdmin' => $request->BiayaAdmin,
                'TotalBayar' => $request->TotalBiaya,
                'IdResepsionis' => $request->Kasir,
                'IdPerawat' => $request->Perawat,
                'IdDokter' => $request->Dokter,
                'DentalUnit' => $request->DentalUnit,
                'Shift' => $shiftId,
                'UserCreate' => $userName,
                'UserUpdate' => null,
                'UserDelete' => null,
                'KodeCabang' => $kodeCabang,
            ]);

            // 4. SIMPAN DETAIL PERAWATAN
            if ($request->has('JenisPerawatan') && is_array($request->JenisPerawatan)) {
                foreach ($request->JenisPerawatan as $perawatan) {
                    if (isset($perawatan['id'], $perawatan['Biaya']) && $perawatan['id'] !== null && $perawatan['Biaya'] !== null) {
                        $transaksi->TransaksiDetail()->create([
                            'IdTransaksi' => $transaksi->id,
                            'Tanggal' => $transaksi->Tanggal,
                            'JenisPerawatan' => $perawatan['id'],
                            'Keterangan' => $perawatan['Keterangan'] ?? null,
                            'Biaya' => $perawatan['Biaya'],
                            'UserCreate' => $userName,
                            'UserUpdate' => null,
                            'UserDelete' => null,
                        ]);
                    }
                }
            }

            // 5. SIMPAN METODE PEMBAYARAN
            if ($request->has('MetodePembayaran') && is_array($request->MetodePembayaran)) {
                foreach ($request->MetodePembayaran as $key => $metode) {
                    if ($metode !== null) {
                        $transaksi->getMetodePembayaran()->create([
                            'IdTransaksi' => $transaksi->id,
                            'MetodePembayaran' => $metode,
                            'Nominal' => $request->NominalBayar[$key] ?? 0,
                        ]);
                    }
                }
            }

            // 🔥 6. LOGIKA PENGURANGAN STOK OTOMATIS (BERDASARKAN KOLOM 'Barang')
            // Gabungkan jumlah kebutuhan barang jika ada jenis perawatan yang sama lebih dari sekali
            $barangKebutuhan = [];

            foreach ($request->JenisPerawatan as $perawatan) {
                $jenisPerawatanId = $perawatan['id'];
                $masterJp = MasterJenisPerawatan::find($jenisPerawatanId);

                if ($masterJp && !empty($masterJp->Barang)) {
                    // Fix: Pastikan hanya menjalankan json_decode jika benar tipe datanya string
                    if (is_string($masterJp->Barang)) {
                        $barangIds = json_decode($masterJp->Barang, true);
                    } elseif (is_array($masterJp->Barang)) {
                        $barangIds = $masterJp->Barang; // Sudah array, langsung pakai
                    } else {
                        $barangIds = [];
                    }

                    if (is_array($barangIds)) {
                        foreach ($barangIds as $barangId) {
                            // Gabungkan kebutuhan setiap barang
                            if (!isset($barangKebutuhan[$barangId])) {
                                $barangKebutuhan[$barangId] = 1;
                            } else {
                                $barangKebutuhan[$barangId] += 1;
                            }
                        }
                    }
                }
            }

            // Proses pengurangan stok sekaligus mutasi stok dengan jumlah yang memang dibutuhkan (bisa > 1)
            foreach ($barangKebutuhan as $barangId => $qtyDibutuhkan) {
                // 🔒 LOCK FOR UPDATE: Mencegah race condition
                $stok = Stok::where('BarangId', $barangId)
                    ->where('KodeKlinik', $kodeCabang)
                    ->lockForUpdate()
                    ->first();

                $stokTersedia = $stok ? $stok->StokAkhir : 0;

                // Cek apakah stok cukup
                if (!$stok || $stokTersedia < $qtyDibutuhkan) {
                    $namaBarang = ($stok && $stok->barang) ? $stok->barang->NamaBarang : 'ID Barang: ' . $barangId;
                    throw new \Exception("Stok tidak mencukupi untuk barang: <strong>{$namaBarang}</strong>.<br>Stok tersedia: {$stokTersedia}, Dibutuhkan: {$qtyDibutuhkan}.");
                }

                // Hitung stok baru
                $stokSebelum = $stok->StokAkhir;
                $stokSesudah = $stokSebelum - $qtyDibutuhkan;

                // Update tabel stok
                $stok->update([
                    'StokAkhir' => $stokSesudah,
                    'UserUpdate' => $userName
                ]);
                // Catat ke tabel mutasi stok, jumlah disesuaikan
                StokMutasi::create([
                    'KodeKlinik' => $kodeCabang,
                    'BarangId' => $barangId,
                    'JenisMutasi' => 'pemakaian',
                    'Jumlah' => -$qtyDibutuhkan, // Negatif karena berkurang
                    'StokSebelum' => $stokSebelum,
                    'StokSesudah' => $stokSesudah,
                    'Keterangan' => 'Pemakaian untuk Transaksi #' . ($transaksi->Kode ?? $transaksi->id),
                    'UserCreate' => $userName,
                ]);
            }

            // 7. ACTIVITY LOG
            if (function_exists('activity')) {
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($transaksi)
                    ->withProperties([
                        'attributes' => $transaksi->toArray(),
                        'request' => $request->all(),
                    ])
                    ->log('Transaksi berhasil dibuat');
            }

            // 8. PROSES INSENTIF
            app(InsentifService::class)->proses($transaksi);

            // 🔥 9. COMMIT TRANSACTION
            DB::commit();

            return redirect()->route('Transaksi.index')->with('success', 'Transaksi berhasil disimpan dan stok telah diperbarui.');

        } catch (\Exception $e) {
            // 🔥 10. ROLLBACK TRANSACTION JIKA ADA ERROR
            DB::rollBack();

            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }
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
        $dental = DentalUnit::where('KodeCabang', auth()->user()->kodeperusahaan)->get();
        // dd($transaksi);
        $masterShift = MasterShift::get();
        return view('transaksi.kasir.edit', compact(
            'transaksi',
            'Perawatan',
            'MetodePembayaran',
            'dokter',
            'perawat',
            'kasir',
            'totalPasienBaru',
            'totalPasienLama',
            'shift','dental','masterShift'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $decodedId = decrypt($id);
        } catch (\Exception $e) {
            return redirect()->route('Transaksi.index')->with('error', 'ID Transaksi tidak valid.');
        }

        // Ambil data transaksi LAMA beserta detailnya SEBELUM diupdate
        $transaksi = Transaksi::with('TransaksiDetail', 'getMetodePembayaran')->findOrFail($decodedId);
        $oldDetails = $transaksi->TransaksiDetail()->get();

        $validatedData = $request->validate([
            'Tanggal' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) > strtotime(date('Y-m-d'))) {
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
            'MetodePembayaran.*' => 'required|integer|exists:master_metode_pembayarans,id',
            'NominalBayar' => 'required|array',
            'NominalBayar.*' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    $matches = [];
                    if (preg_match('/^NominalBayar\.(\d+)$/', $attribute, $matches)) {
                        $totalNominalBayar = array_sum($request->NominalBayar);
                        $totalBiaya = $request->TotalBiaya;

                        if ($totalNominalBayar > $totalBiaya) {
                            $fail('Akumulasi nominal bayar tidak boleh lebih dari total biaya.');
                        } elseif ($totalNominalBayar < $totalBiaya) {
                            $fail('Akumulasi nominal bayar tidak boleh kurang dari total biaya.');
                        }
                    }
                }
            ],
            'TotalBiaya' => 'required|numeric|min:0',
        ], [
            'Tanggal.required' => 'Tanggal wajib diisi',
            'NamaPasien.required' => 'Nama pasien wajib diisi',
            'JenisPasien.required' => 'Jenis pasien wajib dipilih',
            'JenisPerawatan.required' => 'Minimal 1 perawatan harus dipilih',
            'Dokter.required' => 'Pilih dokter',
            'Perawat.required' => 'Pilih perawat',
            'Kasir.required' => 'Pilih kasir/resepsionis',
            'BiayaAdmin.required' => 'Biaya admin wajib diisi',
            'MetodePembayaran.required' => 'Pilih minimal satu metode pembayaran',
            'TotalBiaya.required' => 'Total biaya wajib diisi',
        ]);

        $kodeCabang = auth()->user()->kodeperusahaan;
        $userName = auth()->user()->name;

        // 🔥 1. MULAI DATABASE TRANSACTION
        DB::beginTransaction();

        try {
            // 🔥 2. REVERSAL STOK (KEMBALIKAN STOK DARI DATA LAMA, JUMLAH disesuaikan jika ada perawatan sama berkali-kali)
            // Hitung jumlah setiap perawatan pada $oldDetails
            $reversalBarangCount = []; // [barangId => jumlah]

            foreach ($oldDetails as $oldDetail) {
                $masterJp = MasterJenisPerawatan::find($oldDetail->JenisPerawatan);
                if ($masterJp && !empty($masterJp->Barang) && is_string($masterJp->Barang)) {
                    $barangIds = json_decode($masterJp->Barang, true);

                    if (is_array($barangIds)) {
                        foreach ($barangIds as $barangId) {
                            if (!isset($reversalBarangCount[$barangId])) {
                                $reversalBarangCount[$barangId] = 0;
                            }
                            $reversalBarangCount[$barangId]++;
                        }
                    }
                }
            }

            // Balikkan stok sesuai jumlah yang dibutuhkan per barang
            foreach ($reversalBarangCount as $barangId => $jumlah) {
                $stok = Stok::where('BarangId', $barangId)
                    ->where('KodeKlinik', $kodeCabang)
                    ->lockForUpdate()
                    ->first();

                if ($stok) {
                    $stokSebelum = $stok->StokAkhir;
                    $stokSesudah = $stokSebelum + $jumlah;

                    $stok->update([
                        'StokAkhir' => $stokSesudah,
                        'UserUpdate' => $userName
                    ]);

                    StokMutasi::create([
                        'KodeKlinik' => $kodeCabang,
                        'BarangId' => $barangId,
                        'JenisMutasi' => 'pembatalan_update',
                        'Jumlah' => $jumlah, // Positif karena stok kembali, sesuai jumlah mutasi per barang
                        'StokSebelum' => $stokSebelum,
                        'StokSesudah' => $stokSesudah,
                        'Keterangan' => 'Reversal stok karena update Transaksi #' . ($transaksi->Kode ?? $transaksi->id),
                        'UserCreate' => $userName,
                    ]);
                }
            }

            // 3. UPDATE HEADER TRANSAKSI
            $transaksi->update([
                'Tanggal' => $request->Tanggal,
                'NamaPasien' => $request->NamaPasien,
                'JenisPasien' => $request->JenisPasien,
                'BiayaAdmin' => $request->BiayaAdmin,
                'DentalUnit' => $request->DentalUnit,
                'TotalBayar' => $request->TotalBiaya,
                'IdResepsionis' => $request->Kasir,
                'IdPerawat' => $request->Perawat,
                'IdDokter' => $request->Dokter,
                'UserUpdate' => $userName,
            ]);

            // 4. HAPUS DETAIL & PEMBAYARAN LAMA
            $transaksi->TransaksiDetail()->delete();
            if ($transaksi->getMetodePembayaran) {
                $transaksi->getMetodePembayaran()->delete();
            }

            // 5. BUAT DETAIL BARU & KURANGI STOK BARU
            // Hitung kebutuhan barang berdasarkan perawatan baru (jika sama bisa lebih dari sekali)
            $pemakaianBarangCount = []; // [barangId => jumlah]

            if ($request->has('JenisPerawatan') && is_array($request->JenisPerawatan)) {
                foreach ($request->JenisPerawatan as $perawatan) {
                    if (isset($perawatan['id'], $perawatan['Biaya']) && $perawatan['id'] !== null && $perawatan['Biaya'] !== null) {

                        // A. Buat Detail Baru
                        $transaksi->TransaksiDetail()->create([
                            'IdTransaksi' => $transaksi->id,
                            'JenisPerawatan' => $perawatan['id'],
                            'Biaya' => $perawatan['Biaya'],
                            'Keterangan' => $perawatan['Keterangan'] ?? null,
                            'UserCreate' => $userName,
                        ]);

                        // B. Hitung kebutuhan barang untuk dikurangi nanti
                        $masterJp = MasterJenisPerawatan::find($perawatan['id']);

                        if ($masterJp && !empty($masterJp->Barang) && is_string($masterJp->Barang)) {
                            $barangIds = json_decode($masterJp->Barang, true);

                            if (is_array($barangIds)) {
                                foreach ($barangIds as $barangId) {
                                    if (!isset($pemakaianBarangCount[$barangId])) {
                                        $pemakaianBarangCount[$barangId] = 0;
                                    }
                                    $pemakaianBarangCount[$barangId]++;
                                }
                            }
                        }
                    }
                }
            }

            // Lakukan pengurangan stok sekaligus pencatatan mutasi berdasarkan kebutuhan barang
            foreach ($pemakaianBarangCount as $barangId => $jumlah) {
                $stok = Stok::where('BarangId', $barangId)
                    ->where('KodeKlinik', $kodeCabang)
                    ->lockForUpdate()
                    ->first();

                $stokTersedia = $stok ? $stok->StokAkhir : 0;
                if (!$stok || $stokTersedia < $jumlah) {
                    $namaBarang = ($stok && $stok->barang) ? $stok->barang->NamaBarang : 'ID Barang: ' . $barangId;
                    throw new \Exception("Gagal Update: Stok tidak mencukupi untuk barang <strong>{$namaBarang}</strong>.<br>Stok tersedia: {$stokTersedia}, Dibutuhkan: {$jumlah}.");
                }

                $stokSebelum = $stok->StokAkhir;
                $stokSesudah = $stokSebelum - $jumlah;

                $stok->update([
                    'StokAkhir' => $stokSesudah,
                    'UserUpdate' => $userName
                ]);

                StokMutasi::create([
                    'KodeKlinik' => $kodeCabang,
                    'BarangId' => $barangId,
                    'JenisMutasi' => 'pemakaian',
                    'Jumlah' => -$jumlah, // Negatif sesuai kebutuhan barang
                    'StokSebelum' => $stokSebelum,
                    'StokSesudah' => $stokSesudah,
                    'Keterangan' => 'Pemakaian untuk Update Transaksi #' . ($transaksi->Kode ?? $transaksi->id),
                    'UserCreate' => $userName,
                ]);
            }

            // 6. BUAT METODE PEMBAYARAN BARU
            if ($request->has('MetodePembayaran') && !empty($request->MetodePembayaran)) {
                $metodes = is_array($request->MetodePembayaran) ? $request->MetodePembayaran : [$request->MetodePembayaran];
                foreach ($metodes as $key => $metode) {
                    $transaksi->getMetodePembayaran()->create([
                        'IdTransaksi' => $transaksi->id,
                        'MetodePembayaran' => $metode,
                        'Nominal' => $request->NominalBayar[$key] ?? 0,
                        'UserCreate' => $userName,
                    ]);
                }
            }

            // 7. ACTIVITY LOG
            if (function_exists('activity')) {
                activity()
                    ->causedBy(auth()->user())
                    ->performedOn($transaksi)
                    ->withProperties([
                        'attributes' => $transaksi->toArray(),
                        'request' => $request->all(),
                    ])
                    ->log('Transaksi berhasil diupdate, Kode: ' . ($transaksi->Kode ?? '-'));
            }

            // 8. PROSES INSENTIF
            app(InsentifService::class)->hapusSebelumProses($transaksi);
            app(InsentifService::class)->proses($transaksi);

            // 🔥 9. COMMIT TRANSACTION
            DB::commit();

            return redirect()->route('Transaksi.index')->with('success', 'Transaksi dan data stok berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors(['global' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $decodedId = decrypt($id);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => 'ID Transaksi tidak valid.'
            ], 400);
        }

        // 2. Mulai Database Transaction
        DB::beginTransaction();

        try {
            // Ambil transaksi BESERTA detailnya SEBELUM dihapus (PENTING untuk reversal stok)
            $transaksi = Transaksi::with('TransaksiDetail')->findOrFail($decodedId);

            $userDelete = auth()->user()->name;
            $kodeCabang = $transaksi->KodeCabang;

            // 🔥 3. REVERSAL STOK (Perhatikan jumlah sesuai pengurangan, bukan sekadar +1)
            $barangKembali = [];

            // Hitung jumlah barang per BarangId
            foreach ($transaksi->TransaksiDetail as $detail) {
                $masterJp = MasterJenisPerawatan::find($detail->JenisPerawatan);

                if ($masterJp && !empty($masterJp->Barang)) {
                    $rawBarang = $masterJp->Barang;

                    // Only decode if it's a string
                    $barangIds = [];
                    if (is_string($rawBarang)) {
                        $decoded = json_decode($rawBarang, true);
                        if (is_array($decoded)) {
                            $barangIds = $decoded;
                        }
                    } elseif (is_array($rawBarang)) {
                        // Defensive: Already array (not expected), use as-is
                        $barangIds = $rawBarang;
                    }

                    if (is_array($barangIds)) {
                        foreach ($barangIds as $barangId) {
                            if (!isset($barangKembali[$barangId])) {
                                $barangKembali[$barangId] = 0;
                            }
                            $barangKembali[$barangId] += 1;
                            // Jika satu detail/pilihan bisa mengurangi lebih dari satu stok, ubah per detail logika di sini
                        }
                    }
                }
            }

            // Eksekusi pengembalian stok sesuai jumlah kemunculannya (bisa lebih dari 1)
            foreach ($barangKembali as $barangId => $jumlahKembali) {
                $stok = Stok::where('BarangId', $barangId)
                    ->where('KodeKlinik', $kodeCabang)
                    ->lockForUpdate()
                    ->first();

                if ($stok) {
                    $stokSebelum = $stok->StokAkhir;
                    $stokSesudah = $stokSebelum + $jumlahKembali;

                    $stok->update([
                        'StokAkhir' => $stokSesudah,
                        'UserUpdate' => $userDelete
                    ]);

                    // Catat di mutasi stok
                    StokMutasi::create([
                        'KodeKlinik' => $kodeCabang,
                        'BarangId' => $barangId,
                        'JenisMutasi' => 'pembatalan_hapus',
                        'Jumlah' => $jumlahKembali, // Positif karena stok kembali
                        'StokSebelum' => $stokSebelum,
                        'StokSesudah' => $stokSesudah,
                        'Keterangan' => 'Reversal stok karena penghapusan Transaksi #' . ($transaksi->Kode ?? $transaksi->id),
                        'UserCreate' => $userDelete,
                    ]);
                }
            }

            // 4. SOFT DELETE RELATED MODELS (Logika asli kamu yang dirapikan)

            // a. Transaksi Detail
            foreach ($transaksi->TransaksiDetail as $detail) {
                $detail->UserDelete = $userDelete;
                $detail->save();
            }
            $transaksi->TransaksiDetail()->delete();

            // b. Insentif (Jika ada)
            if ($transaksi->getInsentif) {
                foreach ($transaksi->getInsentif as $insentif) {
                    $insentif->UserDelete = $userDelete;
                    $insentif->save();
                }
                $transaksi->getInsentif()->delete();
            }

            // c. Metode Pembayaran
            $transaksi->getMetodePembayaran()->delete();

            // 5. SOFT DELETE TRANSAKSI UTAMA
            $transaksi->UserDelete = $userDelete;
            $transaksi->save();
            $transaksi->delete();

            // 6. Commit Transaction
            DB::commit();

            return response()->json([
                'status' => 200,
                'message' => 'Transaksi berhasil dihapus dan stok telah dikembalikan.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

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
     public function getDentalUnits(Request $request)
    {
        $kodeKlinik = $request->input('kode_klinik');

        if (!$kodeKlinik) {
            return response()->json(['data' => []]);
        }
        $units = DentalUnit::where('KodeCabang', $kodeKlinik)
            ->orderBy('Nama', 'asc')
            ->get(['id', 'Nama']);

        return response()->json(['data' => $units]);
    }
}
