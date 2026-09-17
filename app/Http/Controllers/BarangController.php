<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\MasterKlinik;
use App\Models\Stok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Barang::with('kategori')->select('barang.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('KategoriNama', function ($row) {
                    return $row->kategori
                        ? '<span class="badge bg-light text-dark border">' . e($row->kategori->Nama) . '</span>'
                        : '-';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex justify-content-center gap-1">';
                    $btn .= '<a href="' . route('Barang.edit', $row->id) . '" class="btn btn-warning btn-sm px-3" title="Edit"><i class="ti ti-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm px-3 btn-delete" data-id="' . $row->id . '" data-nama="' . e($row->NamaBarang) . '" title="Hapus"><i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['KategoriNama', 'action'])
                ->make(true);
        }

        return view('master.barang.index');
    }

    public function create()
    {
        $kategoris = KategoriBarang::orderBy('Nama', 'asc')->get();
        return view('master.barang.create', compact('kategoris'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'NamaBarang' => 'required|string|max:255',
            'KategoriBarangId' => 'required|exists:kategori_barangs,id',
        ]);

        DB::transaction(function () use ($request) {
            $barang = Barang::create([
                'KodeBarang' => strtoupper($request->KodeBarang),
                'NamaBarang' => $request->NamaBarang,
                'KategoriBarangId' => $request->KategoriBarangId,
                'UserCreate' => Auth::check() ? Auth::user()->name : 'System',
            ]);

            // Ambil semua klinik aktif dari MasterKlinik
            $kodeKliniks = $this->getAllKodeKlinik();

            foreach ($kodeKliniks as $kodeKlinik) {
                Stok::create([
                    'KodeKlinik' => $kodeKlinik,
                    'BarangId' => $barang->id,
                    'StokAkhir' => 0,
                    'UserCreate' => Auth::check() ? Auth::user()->name : 'System',
                ]);
            }
        });

        return redirect()->route('Barang.index')->with('success', 'Data Barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $barang = Barang::findOrFail($id);
        $kategoris = KategoriBarang::orderBy('Nama', 'asc')->get();
        return view('master.barang.edit', compact('barang', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'NamaBarang' => 'required|string|max:255',
            'KategoriBarangId' => 'required|exists:kategori_barangs,id',
        ]);

        $barang = Barang::findOrFail($id);
        $barang->update([
            'KodeBarang' => strtoupper($request->KodeBarang),
            'NamaBarang' => $request->NamaBarang,
            'KategoriBarangId' => $request->KategoriBarangId,
            'UserUpdate' => Auth::check() ? Auth::user()->name : 'System',
        ]);

        return redirect()->route('Barang.index')->with('success', 'Data Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $barang = Barang::findOrFail($id);
            $barang->update(['UserDelete' => Auth::check() ? Auth::user()->name : 'System']);
            $barang->delete(); // Cascade akan hapus record stok juga

            return response()->json(['status' => 200, 'message' => 'Data Barang berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 500, 'message' => 'Gagal menghapus data.'], 500);
        }
    }

    /**
     * Ambil semua KodeKlinik aktif dari MasterKlinik
     */
    private function getAllKodeKlinik()
    {
        return MasterKlinik::pluck('Kode')
            ->toArray();
    }
}
