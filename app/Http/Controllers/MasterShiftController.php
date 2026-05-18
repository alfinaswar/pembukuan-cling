<?php

namespace App\Http\Controllers;

use App\Models\MasterShift;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterShiftController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterShift::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('MasterShift.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
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
        return view('master.shift.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.shift.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'JamMulai' => 'required|date_format:H:i',
            'JamSelesai' => 'required|date_format:H:i',
        ]);

        if (
            strtotime($request->JamSelesai) < strtotime($request->JamMulai)
        ) {
            return back()
                ->withErrors(['JamSelesai' => 'Jam selesai tidak boleh lebih kecil dari jam mulai.'])
                ->withInput();
        }

        MasterShift::create([
            'Nama' => $request->Nama,
            'JamMulai' => $request->JamMulai,
            'JamSelesai' => $request->JamSelesai,
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('master-shift')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambahkan data master shift: ' . $request->Nama);
        }
        return redirect()->route('MasterShift.index')->with('success', 'Shift berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Optional: implement if needed
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $MasterShift = MasterShift::findOrFail($id);
        return view('master.shift.edit', compact('MasterShift'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $masterShift = MasterShift::findOrFail($id);

        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'JamMulai' => 'required|date_format:H:i',
            'JamSelesai' => 'required|date_format:H:i',
        ]);
        if (
            strtotime($request->JamSelesai) < strtotime($request->JamMulai)
        ) {
            return back()
                ->withErrors(['JamSelesai' => 'Jam selesai tidak boleh lebih kecil dari jam mulai.'])
                ->withInput();
        }
        $masterShift->update([
            'Nama' => $request->Nama,
            'JamMulai' => $request->JamMulai,
            'JamSelesai' => $request->JamSelesai,
            'UserUpdate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('master-shift')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Mengupdate data master shift: ' . $request->Nama);
        }

        return redirect()->route('MasterShift.index')->with('success', 'Shift berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $masterShift = MasterShift::find($id);

        if (!$masterShift) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }
        $masterShift->UserDelete = auth()->user()->name ?? null;
        $masterShift->save();

        if (function_exists('activity')) {
            activity('master-shift')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master shift: ' . $masterShift->Nama);
        }

        $masterShift->delete();

        return response()->json(['status' => 200, 'message' => 'Master shift berhasil dihapus']);
    }
}
