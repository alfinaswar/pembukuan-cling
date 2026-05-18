<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisPerawatan;
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
            $data = Transaksi::with('TransaksiDetail')->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('TotalBayar', function ($row) {
                    $TotalBayar = $row->TotalBayar;
                    return 'Rp ' . number_format($TotalBayar, 0, ',', '.');
                })
                ->addColumn('BiayaAdmin', function ($row) {
                    $BiayaAdmin = $row->BiayaAdmin;
                    return 'Rp ' . number_format($BiayaAdmin, 0, ',', '.');
                })
                ->addColumn('MetodePembayaran', function ($row) {
                    if ($row->getMetodePembayaran) {
                        return $row->getMetodePembayaran->Nama;
                    }
                    return '-';
                })
                ->addColumn('Shift', function ($row) {
                    if ($row->getShift) {
                        return $row->getShift->Nama;
                    }
                    return '-';
                })
                ->addColumn('Perawat', function ($row) {
                    if ($row->getPerawat) {
                        return $row->getPerawat->name;
                    }
                    return '-';
                })
                ->addColumn('Dokter', function ($row) {
                    if ($row->getDokter) {
                        return $row->getDokter->name;
                    }
                    return '-';
                })
                ->addColumn('Resepsionis', function ($row) {
                    if ($row->getResepsionis) {
                        return $row->getResepsionis->name;
                    }
                    return '-';
                })

                ->addColumn('Layanan', function ($row) {
                    if ($row->TransaksiDetail && count($row->TransaksiDetail) > 0) {
                        // Rekap layanan: group by nama, jumlahkan biaya dan hitung count
                        $rekap = [];
                        foreach ($row->TransaksiDetail as $detail) {
                            $nama = optional($detail->MasterJenisPerawatan)->Nama;
                            $biaya = isset($detail->Biaya) ? (int) $detail->Biaya : 0;
                            if ($nama) {
                                if (!isset($rekap[$nama])) {
                                    $rekap[$nama] = [
                                        'nama' => $nama,
                                        'harga' => 0,
                                        'count' => 0,
                                    ];
                                }
                                $rekap[$nama]['harga'] += $biaya;
                                $rekap[$nama]['count'] += 1;
                            }
                        }

                        if (empty($rekap)) {
                            return '-';
                        }

                        $html = '<dl>';
                        foreach ($rekap as $item) {
                            $namaStr = e($item['nama']);
                            // Jika count > 1, tampilkan jumlah
                            if ($item['count'] > 1) {
                                $namaStr .= ' x' . $item['count'];
                            }
                            $hargaStr = 'Rp ' . number_format($item['harga'], 0, ',', '.');
                            $html .= '<dt style="font-weight:500;">' . $namaStr . ':</dt>';
                            $html .= '<dd style="margin-bottom:6px;">' . e($hargaStr) . '</dd>';
                        }
                        $html .= '</dl>';
                        return $html;
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('Transaksi.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action', 'TotalBayar', 'BiayaAdmin', 'Perawat', 'Dokter', 'Resepsionis', 'Layanan'])
                ->make(true);
        }
        return view('transaksi.kasir.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $Perawatan = MasterJenisPerawatan::where('Status', 'Y')
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->get();

        // Ambil shift aktif saat ini
        $shift = MasterShift::whereTime('JamMulai', '<=', now()->format('H:i:s'))
            ->whereTime('JamSelesai', '>=', now()->format('H:i:s'))
            ->first();
        // dd($shift);
        $totalPasienBaru = Transaksi::where('JenisPasien', 'Baru')
            ->where('Shift', optional($shift)->id)
            ->whereDate('created_at', today())
            ->count();

        // Hitung total pasien lama pada shift ini
        $totalPasienLama = Transaksi::where('JenisPasien', 'Lama')
            ->where('Shift', optional($shift)->id)
            ->whereDate('created_at', today())
            ->count();


        $MetodePembayaran = MasterMetodePembayaran::where('Status', 'Y')->get();
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
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
                    if (strtotime($value) < strtotime(date('Y-m-d'))) {
                        $fail('Tidak boleh memilih tanggal ke belakang (backdate).');
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
            'MetodePembayaran' => 'required|exists:master_metode_pembayarans,id',
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
            'MetodePembayaran.required' => 'Pilih metode pembayaran',
            'MetodePembayaran.exists' => 'Metode pembayaran tidak valid',
            'TotalBiaya.required' => 'Total biaya wajib diisi',
            'TotalBiaya.numeric' => 'Total biaya harus angka'
        ]);
        $shiftId = $this->getShift(now());
        $transaksi = Transaksi::create([
            'Tanggal' => $request->Tanggal,
            'NamaPasien' => $request->NamaPasien,
            'JenisPasien' => $request->JenisPasien,
            'MetodePembayaran' => $request->MetodePembayaran,
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
                        'Biaya' => $perawatan['Biaya'],
                        'UserCreate' => auth()->user()->name,
                        'UserUpdate' => null,
                        'UserDelete' => null,
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
        $transaksi = Transaksi::with(['TransaksiDetail', 'getPerawat', 'getDokter', 'getResepsionis'])
            ->findOrFail($id);

        $Perawatan = MasterJenisPerawatan::where('Status', 'Y')
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->get();
        $shift = MasterShift::whereTime('JamMulai', '<=', now()->format('H:i:s'))
            ->whereTime('JamSelesai', '>=', now()->format('H:i:s'))
            ->first();
        $totalPasienBaru = Transaksi::where('JenisPasien', 'Baru')
            ->where('Shift', optional($shift)->id)
            ->whereDate('created_at', today())
            ->count();

        // Hitung total pasien lama pada shift ini
        $totalPasienLama = Transaksi::where('JenisPasien', 'Lama')
            ->where('Shift', optional($shift)->id)
            ->whereDate('created_at', today())
            ->count();

        $MetodePembayaran = MasterMetodePembayaran::where('Status', 'Y')->get();
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();

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
    public function update(Request $request, Transaksi $transaksi)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaksi $transaksi)
    {
        //
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
