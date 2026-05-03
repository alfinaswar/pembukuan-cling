<?php

namespace App\Http\Controllers;

use App\Models\MasterJenisPerawatan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MasterJenisPerawatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * Kolom: Kode, Nama, Tarif, KodeCabang, Status, UserCreate, UserUpdate, UserDelete
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterJenisPerawatan::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('JenisPerawatan.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.jenis-perawatan.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.jenis-perawatan.create');
    }

    /**
     * Store a newly created resource in storage.
     * Kolom: Kode, Nama, Tarif, KodeCabang, Status, UserCreate, UserUpdate, UserDelete
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'Tarif' => 'required|string|min:0',
        ]);

        MasterJenisPerawatan::create([
            'Nama' => $request->Nama,
            'Tarif' => str_replace(['Rp ', '.'], '', $request->Tarif),
            'KodeCabang' => auth()->user()->kodeperusahaan,
            'UserCreate' => auth()->user()->name,
            'UserUpdate' => null,
            'UserDelete' => null,
        ]);

        return redirect()->route('JenisPerawatan.index')->with('success', 'Jenis Perawatan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterJenisPerawatan $masterJenisPerawatan)
    {
        // Optional: implement if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $JenisPerawatan = MasterJenisPerawatan::findOrFail($id);
        return view('master.jenis-perawatan.edit', compact('JenisPerawatan'));
    }

    /**
     * Update the specified resource in storage.
     * Kolom: Kode, Nama, Tarif, KodeCabang, Status, UserUpdate
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $masterJenisPerawatan = MasterJenisPerawatan::findOrFail($id);
        // dd($masterJenisPerawatan);
        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'Tarif' => 'required|string|min:0',
        ]);

        $masterJenisPerawatan->update([
            'Nama' => $request->Nama,
            'Tarif' => str_replace(['Rp ', '.'], '', $request->Tarif),
            'UserUpdate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('master-jenis-perawatan')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Mengubah data jenis perawatan dari : ' . $masterJenisPerawatan->Nama . ' menjadi ' . $request->Nama);
        }
        return redirect()->route('JenisPerawatan.index')->with('success', 'Jenis Perawatan berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     * Kolom: UserDelete
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $jenisPerawatan = MasterJenisPerawatan::find($id);

        if (!$jenisPerawatan) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        $jenisPerawatan->UserDelete = auth()->user()->name ?? null;
        $jenisPerawatan->save();

        if (function_exists('activity')) {
            activity('master-jenis-perawatan')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master jenis perawatan: ' . $jenisPerawatan->Nama);
        }

        $jenisPerawatan->delete();

        return response()->json(['status' => 200, 'message' => 'Master jenis perawatan berhasil dihapus']);
    }
}
