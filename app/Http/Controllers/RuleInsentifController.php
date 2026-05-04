<?php

namespace App\Http\Controllers;

use App\Models\RuleInsentif;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RuleInsentifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = RuleInsentif::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('Insentif.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
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
        return view('insentif.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('insentif.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            // Tambahkan aturan validasi lain jika diperlukan
        ]);

        RuleInsentif::create([
            'Nama' => $request->Nama,
            'UserCreate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('rule-insentif')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambahkan data rule insentif: ' . $request->Nama);
        }
        return redirect()->route('Insentif.index')->with('success', 'Rule insentif berhasil ditambahkan');
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
        $ruleInsentif = RuleInsentif::findOrFail($id);
        return view('insentif.edit', compact('ruleInsentif'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $ruleInsentif = RuleInsentif::findOrFail($id);

        $validatedData = $request->validate([
            'Nama' => 'required|string|max:255',
            // Tambahkan aturan validasi lain jika diperlukan
        ]);

        $ruleInsentif->update([
            'Nama' => $request->Nama,
            'UserUpdate' => auth()->user()->name,
        ]);
        if (function_exists('activity')) {
            activity('rule-insentif')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Mengupdate data rule insentif: ' . $request->Nama);
        }

        return redirect()->route('Insentif.index')->with('success', 'Rule insentif berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $ruleInsentif = RuleInsentif::find($id);

        if (!$ruleInsentif) {
            return response()->json(['status' => 404, 'message' => 'Data tidak ditemukan']);
        }
        $ruleInsentif->UserDelete = auth()->user()->name ?? null;
        $ruleInsentif->save();

        if (function_exists('activity')) {
            activity('rule-insentif')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus rule insentif: ' . $ruleInsentif->Nama);
        }

        $ruleInsentif->delete();

        return response()->json(['status' => 200, 'message' => 'Rule insentif berhasil dihapus']);
    }
}
