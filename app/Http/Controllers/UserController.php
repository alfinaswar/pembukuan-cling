<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MasterKlinik;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Yajra\DataTables\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = User::latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('roles', function ($row) {
                    // Show roles as comma separated string, or a badge for each role if needed
                    $roles = $row->getRoleNames();
                    if ($roles->count() > 0) {
                        return $roles->map(function ($role) {
                            return '<span class="badge bg-primary">' . e($role) . '</span>';
                        })->implode(' ');
                    }
                    return '-';
                })
                ->addColumn('action', function ($row) {
                    $encryptedId = encrypt($row->id);
                    return '
                        <a href="' . route('users.edit', $encryptedId) . '" class="btn btn-sm btn-warning">
                            <i class="fa fa-edit"></i> Edit
                        </a>
                        <button class="btn btn-sm btn-danger btn-delete" data-id="' . $encryptedId . '">
                            <i class="fa fa-trash"></i> Hapus
                        </button>
                    ';
                })
                ->rawColumns(['roles', 'action'])
                ->make(true);
        }

        return view('users.index');
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        $perusahaan = MasterKlinik::get();
        return view('users.create', compact('roles', 'perusahaan'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'required|array',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Hash::make($request->password),
        ]);
        $user->syncRoles($request->roles);

        if (function_exists('activity')) {
            activity('user')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menambahkan user: ' . $request->name);
        }
        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    /**
     * Display the specified user.
     */
    public function show($id)
    {
        $id = decrypt($id);
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('users.show', compact('user', 'roles'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        $id = decrypt($id);
        $user = User::findOrFail($id);
        $roles = Role::all();
        $userRole = $user->roles->pluck('name')->toArray();

        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $id = decrypt($id);
        $user = User::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|array',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = \Hash::make($request->password);
        }

        $user->save();
        $user->syncRoles($request->roles);

        if (function_exists('activity')) {
            activity('user')
                ->causedBy(auth()->user()->id)
                ->withProperties(['ip' => request()->ip()])
                ->log('Mengupdate user: ' . $request->name);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy($id)
    {
        $id = decrypt($id);
        $user = User::find($id);

        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'User tidak ditemukan']);
        }

        if (function_exists('activity')) {
            activity('user')
                ->causedBy(auth()->user()->id ?? null)
                ->withProperties(['ip' => request()->ip()])
                ->log('Menghapus user: ' . $user->name);
        }

        $user->delete();

        return response()->json(['status' => 200, 'message' => 'User berhasil dihapus']);
    }
}
