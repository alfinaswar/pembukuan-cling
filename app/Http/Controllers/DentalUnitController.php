<?php

namespace App\Http\Controllers;

use App\Models\DentalUnit;
use App\Models\MasterKlinik;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DentalUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterKlinik::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = $row->Kode;
                    return '
                        <a href="' . route('DentalUnit.create', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="ti ti-device-heart-monitor"></i>
                        </a>
                    ';
                })


                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.dental-unit.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id,Request $request)
    {

        if ($request->ajax()) {
            $data = DentalUnit::where('KodeCabang',$id)->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('DentalUnit.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '" data-nama="' . e($row->Nama) . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    ';
                })



                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.dental-unit.create',compact('id'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'Keterangan' => 'nullable|string|max:255',
        ], [
            'Nama.required' => 'Nama Dental Unit wajib diisi.',
        ]);

        DentalUnit::create([
            'Nama' => $validatedData['Nama'],
            'Keterangan' => $validatedData['Keterangan'] ?? null,
            'KodeCabang' => $request->input('KodeCabang') ?? null,
            'UserCreate' => auth()->user()->name ?? null,
        ]);
        return redirect()->back()->with('success', 'Dental Unit berhasil ditambahkan.');

    }

    /**
     * Display the specified resource.
     */
    public function show(DentalUnit $dentalUnit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $dentalUnit = DentalUnit::findOrFail($id);
        return view('master.dental-unit.edit', compact('dentalUnit'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $dentalUnit = DentalUnit::findOrFail($id);

        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'Keterangan' => 'nullable|string|max:255',
        ], [
            'Nama.required' => 'Nama Dental Unit wajib diisi.',
        ]);

        $dentalUnit->update([
            'Nama' => $validatedData['Nama'],
            'Keterangan' => $validatedData['Keterangan'] ?? null,
            'UserUpdate' => auth()->user()->name ?? null,
        ]);
        return redirect()->route('DentalUnit.create', ['id' => $dentalUnit->KodeCabang])->with('success', 'Dental Unit berhasil diperbarui.');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $dentalUnit = DentalUnit::find($id);

        if (!$dentalUnit) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }

        $dentalUnit->UserDelete = auth()->user()->name ?? null;
        $dentalUnit->save();

        if (function_exists('activity')) {
            activity('master-dentalunit')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus dental unit: ' . $dentalUnit->Nama);
        }

        $dentalUnit->delete();

        return response()->json(['status' => 200, 'message' => 'Dental Unit berhasil dihapus']);
    }
}
