<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisPerawatan;
use App\Models\MasterMetodePembayaran;
use App\Models\MasterShift;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;
use App\Services\InsentifService;
use Illuminate\Support\Carbon;

class TransaksiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $Perawatan = MasterJenisPerawatan::where('Status', 'Y')
            ->where('KodeCabang', auth()->user()->kodeperusahaan)
            ->get();

        $MetodePembayaran = MasterMetodePembayaran::where('Status', 'Y')->get();
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        return view('transaksi.kasir.create', compact('Perawatan', 'MetodePembayaran', 'dokter', 'perawat', 'kasir'));
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
    public function edit(Transaksi $transaksi)
    {
        //
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
