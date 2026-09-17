<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class KategoriBarangController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = KategoriBarang::select(['id', 'Nama', 'created_at']);

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex justify-content-center gap-1">';
                    $btn .= '<a href="' . route('KategoriBarang.edit', $row->id) . '" class="btn btn-warning btn-sm px-3" title="Edit">';
                    $btn .= '<i class="ti ti-edit"></i></a>';
                    $btn .= '<button type="button" class="btn btn-danger btn-sm px-3 btn-delete" data-id="' . $row->id . '" data-nama="' . e($row->Nama) . '" title="Hapus">';
                    $btn .= '<i class="ti ti-trash"></i></button>';
                    $btn .= '</div>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('master.kategori-barang.index');
    }

    public function create()
    {
        return view('master.kategori-barang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
        ]);

        KategoriBarang::create([
            'Nama' => $request->Nama,
            'UserCreate' => Auth::check() ? Auth::user()->name : 'System',
        ]);

        return redirect()->route('KategoriBarang.index')->with('success', 'Data Kategori Barang berhasil ditambahkan.');
    }

    public function show($id)
    {
        $kategori = KategoriBarang::findOrFail($id);
        return view('master.kategori-barang.show', compact('kategori'));
    }

    public function edit($id)
    {
        $kategori = KategoriBarang::findOrFail($id);
        return view('master.kategori-barang.edit', compact('kategori'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Nama' => 'required|string|max:255',
        ]);

        $kategori = KategoriBarang::findOrFail($id);
        $kategori->update([
            'Nama' => $request->Nama,
            'UserUpdate' => Auth::check() ? Auth::user()->name : 'System',
        ]);

        return redirect()->route('KategoriBarang.index')->with('success', 'Data Kategori Barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        try {
            $kategori = KategoriBarang::findOrFail($id);
            $kategori->update([
                'UserDelete' => Auth::check() ? Auth::user()->name : 'System'
            ]);
            $kategori->delete(); // Soft delete akan aktif karena ada $table->softDeletes()

            return response()->json([
                'status' => 200,
                'message' => 'Data Kategori Barang berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Terjadi kesalahan saat menghapus data.'
            ], 500);
        }
    }
}
