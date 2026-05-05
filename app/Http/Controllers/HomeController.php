<?php

namespace App\Http\Controllers;

use App\Models\MasterShift;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dokter = User::role('Dokter')->get();
        $perawat = User::role('Perawat')->get();
        $kasir = User::role('Kasir / Resepsionis')->get();
        $shift = MasterShift::get();

        $roleId = auth()->user()->roles->first()->id ?? null;
        if ($roleId == 5) { // Kasir / Resepsionis
            return view('dashboard.kasir', compact('dokter', 'perawat', 'kasir', 'shift'));
        } elseif ($roleId == 3) { // Dokter
            return view('dashboard.dokter', compact('dokter', 'perawat', 'kasir', 'shift'));
        } elseif ($roleId == 4) { // Perawat
            return view('dashboard.perawat', compact('dokter', 'perawat', 'kasir', 'shift'));
        } else {
            return view('dashboard.umum', compact('dokter', 'perawat', 'kasir', 'shift'));
        }
    }
}
