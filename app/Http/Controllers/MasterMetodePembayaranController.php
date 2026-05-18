<?php

namespace App\Http\Controllers;

use App\Models\MasterMetodePembayaran;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MasterMetodePembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = MasterMetodePembayaran::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('MetodePembayaran.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
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
        return view('master.metode-pembayaran.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master.metode-pembayaran.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
        ]);

        MasterMetodePembayaran::create([
            'Nama' => $request->Nama,
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('master-metode-pembayaran')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambahkan data master metode pembayaran: ' . $request->Nama);
        }
        return redirect()->route('MetodePembayaran.index')->with('success', 'Metode Pembayaran berhasil ditambahkan');
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
        $MasterMetodePembayaran = MasterMetodePembayaran::findOrFail($id);
        return view('master.metode-pembayaran.edit', compact('MasterMetodePembayaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $metodePembayaran = MasterMetodePembayaran::findOrFail($id);

        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
        ]);

        $metodePembayaran->update([
            'Nama' => $request->Nama,
            'UserUpdate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('master-metode-pembayaran')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Mengupdate data master metode pembayaran: ' . $request->Nama);
        }

        return redirect()->route('MetodePembayaran.index')->with('success', 'Metode Pembayaran berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $metodePembayaran = MasterMetodePembayaran::find($id);

        if (!$metodePembayaran) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }
        $metodePembayaran->UserDelete = auth()->user()->name ?? null;
        $metodePembayaran->save();

        if (function_exists('activity')) {
            activity('master-metode-pembayaran')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus master metode pembayaran: ' . $metodePembayaran->Nama);
        }

        $metodePembayaran->delete();

        return response()->json(['status' => 200, 'message' => 'Master metode pembayaran berhasil dihapus']);
    }
}
