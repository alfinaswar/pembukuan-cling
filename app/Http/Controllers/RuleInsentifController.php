<?php

namespace App\Http\Controllers;

use App\Models\RuleInsentif;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RuleInsentifController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $role = Role::with('getRuleInsentif')->get();
        // dd($role);
        return view('insentif.index', compact('role'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($id)
    {
        $role = Role::where('id', $id)->first();
        return view('insentif.create', compact('role'));
    }

    public function aturan(Request $request)
    {
        $roles = Role::orderBy('id', 'DESC')->paginate(5);
        return view('insentif.daftar-role', compact('roles'))
            ->with('i', ($request->input('page', 1) - 1) * 5);
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
        $insentif = RuleInsentif::with('getRole')->findOrFail($id);
        return view('insentif.edit', compact('insentif'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $ruleInsentif = RuleInsentif::findOrFail($id);

        $validatedData = $request->validate([
            'Role' => 'required|string|max:255',
            'JenisRule' => 'required|string|max:255',
            'Operator' => 'required|string|max:10',
            'Nilai' => 'required|string|max:50',
            'TipeNominal' => 'required|string|max:50',
            'Nominal' => 'required|string|max:50',
            'BerlakuPer' => 'required|string|max:50',
            'Keterangan' => 'nullable|string|max:255',
        ]);

        // Buang titik pada Nilai dan Nominal
        $nilai = str_replace('.', '', $request->Nilai);
        $nominal = str_replace('.', '', $request->Nominal);

        $ruleInsentif->update([
            'Role' => $request->Role,
            'JenisRule' => $request->JenisRule,
            'Operator' => $request->Operator,
            'Nilai' => $nilai,
            'TipeNominal' => $request->TipeNominal,
            'Nominal' => $nominal,
            'BerlakuPer' => $request->BerlakuPer,
            'Keterangan' => $request->Keterangan,
            'UserUpdate' => auth()->user()->name,
        ]);

        if (function_exists('activity')) {
            activity('rule-insentif')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Mengupdate data rule insentif: ' . $request->JenisRule . ' - ' . $request->Keterangan);
        }

        return redirect()->route('Insentif.index')->with('success', 'Rule insentif berhasil diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
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
