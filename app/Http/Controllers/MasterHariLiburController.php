<?php

namespace App\Http\Controllers;

use App\Models\MasterHariLibur;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterHariLiburController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterHariLibur::orderBy('TanggalLibur', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('MasterHariLibur.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.hari-libur.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.hari-libur.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $validatedData = $request->validate([
            'NamaHari' => 'required|string|max:255',
            'TanggalLibur' => 'required|date|unique:master_hari_liburs,TanggalLibur',
            'Keterangan' => 'nullable|string|max:255',
        ], [
            'NamaHari.required' => 'Nama Hari wajib diisi.',
            'TanggalLibur.required' => 'Tanggal Libur wajib diisi.',
            'TanggalLibur.date' => 'Tanggal Libur harus berupa tanggal yang valid.',
            'TanggalLibur.unique' => 'Tanggal Libur sudah terdaftar.',
        ]);

        MasterHariLibur::create([
            'NamaHari' => $validatedData['NamaHari'],
            'TanggalLibur' => $validatedData['TanggalLibur'],
            'Keterangan' => $validatedData['Keterangan'] ?? null,
        ]);
        return redirect()->route('MasterHariLibur.index')->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterHariLibur $masterHariLibur)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $masterHariLibur = MasterHariLibur::findOrFail($id);
        return view('master.hari-libur.edit', compact('masterHariLibur'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $masterHariLibur = MasterHariLibur::findOrFail($id);

        $validatedData = $request->validate([
            'NamaHari' => 'required|string|max:255',
            'TanggalLibur' => 'required|date|unique:master_hari_liburs,TanggalLibur,' . $masterHariLibur->id,
            'Keterangan' => 'nullable|string|max:255',
        ], [
            'NamaHari.required' => 'Nama Hari wajib diisi.',
            'TanggalLibur.required' => 'Tanggal Libur wajib diisi.',
            'TanggalLibur.date' => 'Tanggal Libur harus berupa tanggal yang valid.',
            'TanggalLibur.unique' => 'Tanggal Libur sudah terdaftar.',
        ]);
        $masterHariLibur->update([
            'NamaHari' => $validatedData['NamaHari'],
            'TanggalLibur' => $validatedData['TanggalLibur'],
            'Keterangan' => $validatedData['Keterangan'] ?? null,
        ]);
        return redirect()->route('MasterHariLibur.index')->with('success', 'Hari libur berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $libur = MasterHariLibur::find($id);

        if (!$libur) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }
        $libur->UserDelete = auth()->user()->name ?? null;
        $libur->save();

        if (function_exists('activity')) {
            activity('master-shift')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master shift: ' . $libur->Nama);
        }

        $libur->delete();

        return response()->json(['status' => 200, 'message' => 'Master shift berhasil dihapus']);
    }
}
