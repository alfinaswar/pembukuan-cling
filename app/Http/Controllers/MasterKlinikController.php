<?php

namespace App\Http\Controllers;

use App\Models\MasterKlinik;
use App\Models\TargetCapaian;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterKlinikController extends Controller
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
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('Klinik.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a href="' . route('Klinik.target-capaian', $encryptedId) . '" class="btn btn-sm btn-info">
                            <i class="fa fa-bullseye"></i>
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i>
                        </button>
                    ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('master.klinik.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.klinik.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'Alamat' => 'required|string',
            'NoTelp' => 'nullable|string|min:11|max:13',
            'Email' => 'nullable|email|max:255',
        ]);

        MasterKlinik::create([
            'Nama' => $request->Nama,
            'Alamat' => $request->Alamat,
            'NoTelp' => $request->NoTelp,
            'Email' => $request->Email,
            'Status' => $request->Status,
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('master-klinik')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambahkan data master klinik: ' . $request->Nama);
        }
        return redirect()->route('Klinik.index')->with('success', 'Klinik berhasil ditambahkan');
    }

    public function storeTarget(Request $request, $id)
    {
        $id = decrypt($id);
        // dd($request->all());
        $validatedData = $request->validate([
            'Tahun' => 'required|numeric|min:2000|max:2100',
            'BesarTarget' => 'required',
        ]);

        $target = new TargetCapaian();
        $target->IdKlinik = $id;
        $target->Tahun = $request->Tahun;
        $target->BesarTarget = $request->BesarTarget;
        $target->UserCreate = auth()->user()->name;
        $target->save();

        if (function_exists('activity')) {
            activity('target-capaian-klinik')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambahkan target capaian klinik tahun ' . $request->Tahun . ' pada klinik ID: ' . $id);
        }

        return redirect()->route('Klinik.target-capaian', encrypt($id))->with('success', 'Target capaian berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(MasterKlinik $masterKlinik)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MasterKlinik $masterKlinik, $id)
    {
        $id = decrypt($id);
        $MasterKlinik = MasterKlinik::Find($id);
        return view('master.klinik.edit', compact('MasterKlinik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MasterKlinik $masterKlinik, $id)
    {
        $id = decrypt($id);
        $masterKlinik = MasterKlinik::findOrFail($id);

        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            'Alamat' => 'required|string',
            'NoTelp' => 'nullable|string|min:11|max:13',
            'Email' => 'nullable|email|max:255',
        ]);

        $masterKlinik->update([
            'Nama' => $request->Nama,
            'Alamat' => $request->Alamat,
            'NoTelp' => $request->NoTelp,
            'Email' => $request->Email,
            'Status' => $request->Status,
            'UserUpdate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('master-klinik')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Mengupdate data master klinik: ' . $request->Nama);
        }

        return redirect()->route('Klinik.index')->with('success', 'Klinik berhasil diupdate');
    }

    public function targetCapaian(Request $request, $id)
    {
        $id = decrypt($id);
        $data = MasterKlinik::with('getTarget')->find($id);
        return view('master.klinik.target-capaian', compact('data'));
    }

    public function buatTarget(Request $request, $id)
    {
        $id = decrypt($id);
        $data = MasterKlinik::with('getTarget')->find($id);
        return view('master.klinik.buat-target', compact('data'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $klinik = MasterKlinik::find($id);

        if (!$klinik) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }
        $klinik->UserDelete = auth()->user()->name ?? null;
        $klinik->save();

        if (function_exists('activity')) {
            activity('master-klinik')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master klinik: ' . $klinik->Nama);
        }

        $klinik->delete();

        return response()->json(['status' => 200, 'message' => 'Master klinik berhasil dihapus']);
    }
}
