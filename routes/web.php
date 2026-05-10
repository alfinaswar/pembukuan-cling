<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MasterJenisPerawatanController;
use App\Http\Controllers\MasterKlinikController;
use App\Http\Controllers\MasterMetodePembayaranController;
use App\Http\Controllers\MasterShiftController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuleInsentifController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
 * |--------------------------------------------------------------------------
 * | Web Routes
 * |--------------------------------------------------------------------------
 * |
 * | Here is where you can register web routes for your application. These
 * | routes are loaded by the RouteServiceProvider and all of them will
 * | be assigned to the "web" middleware group. Make something great!
 * |
 */

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);

    Route::prefix('master/jenis-perawatan')->group(function () {
        Route::get('/', [MasterJenisPerawatanController::class, 'index'])->name('JenisPerawatan.index');
        Route::get('/create', [MasterJenisPerawatanController::class, 'create'])->name('JenisPerawatan.create');
        Route::post('/store', [MasterJenisPerawatanController::class, 'store'])->name('JenisPerawatan.store');
        Route::get('/edit/{id}', [MasterJenisPerawatanController::class, 'edit'])->name('JenisPerawatan.edit');
        Route::put('/update/{id}', [MasterJenisPerawatanController::class, 'update'])->name('JenisPerawatan.update');
        Route::get('/show/{id}', [MasterJenisPerawatanController::class, 'show'])->name('JenisPerawatan.show');
        Route::delete('/delete/{id}', [MasterJenisPerawatanController::class, 'destroy'])->name('JenisPerawatan.destroy');
    });
    Route::prefix('master/klinik')->group(function () {
        Route::get('/', [MasterKlinikController::class, 'index'])->name('Klinik.index');
        Route::get('/create', [MasterKlinikController::class, 'create'])->name('Klinik.create');
        Route::post('/store', [MasterKlinikController::class, 'store'])->name('Klinik.store');
        Route::get('/edit/{id}', [MasterKlinikController::class, 'edit'])->name('Klinik.edit');
        Route::put('/update/{id}', [MasterKlinikController::class, 'update'])->name('Klinik.update');
        Route::get('/show/{id}', [MasterKlinikController::class, 'show'])->name('Klinik.show');
        Route::delete('/delete/{id}', [MasterKlinikController::class, 'destroy'])->name('Klinik.destroy');
    });
    Route::prefix('master/metode-pembayaran')->group(function () {
        Route::get('/', [MasterMetodePembayaranController::class, 'index'])->name('MetodePembayaran.index');
        Route::get('/create', [MasterMetodePembayaranController::class, 'create'])->name('MetodePembayaran.create');
        Route::post('/store', [MasterMetodePembayaranController::class, 'store'])->name('MetodePembayaran.store');
        Route::get('/edit/{id}', [MasterMetodePembayaranController::class, 'edit'])->name('MetodePembayaran.edit');
        Route::put('/update/{id}', [MasterMetodePembayaranController::class, 'update'])->name('MetodePembayaran.update');
        Route::get('/show/{id}', [MasterMetodePembayaranController::class, 'show'])->name('MetodePembayaran.show');
        Route::delete('/delete/{id}', [MasterMetodePembayaranController::class, 'destroy'])->name('MetodePembayaran.destroy');
    });
    Route::prefix('master/shift')->group(function () {
        Route::get('/', [MasterShiftController::class, 'index'])->name('MasterShift.index');
        Route::get('/create', [MasterShiftController::class, 'create'])->name('MasterShift.create');
        Route::post('/store', [MasterShiftController::class, 'store'])->name('MasterShift.store');
        Route::get('/edit/{id}', [MasterShiftController::class, 'edit'])->name('MasterShift.edit');
        Route::put('/update/{id}', [MasterShiftController::class, 'update'])->name('MasterShift.update');
        Route::get('/show/{id}', [MasterShiftController::class, 'show'])->name('MasterShift.show');
        Route::delete('/delete/{id}', [MasterShiftController::class, 'destroy'])->name('MasterShift.destroy');
    });
    Route::prefix('transaksi/kasir')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('Transaksi.index');
        Route::get('/create', [TransaksiController::class, 'create'])->name('Transaksi.create');
        Route::post('/store', [TransaksiController::class, 'store'])->name('Transaksi.store');
        Route::get('/edit/{id}', [TransaksiController::class, 'edit'])->name('Transaksi.edit');
        Route::put('/update/{id}', [TransaksiController::class, 'update'])->name('Transaksi.update');
        Route::get('/show/{id}', [TransaksiController::class, 'show'])->name('Transaksi.show');
        Route::delete('/delete/{id}', [TransaksiController::class, 'destroy'])->name('Transaksi.destroy');
    });
    Route::prefix('insentif/')->group(function () {
        Route::get('/', [RuleInsentifController::class, 'index'])->name('Insentif.index');
        Route::get('/ketentuan', [RuleInsentifController::class, 'aturan'])->name('Insentif.aturan');
        Route::get('/create/{id}', [RuleInsentifController::class, 'create'])->name('Insentif.create');
        Route::post('/store', [RuleInsentifController::class, 'store'])->name('Insentif.store');
        Route::get('/edit/{id}', [RuleInsentifController::class, 'edit'])->name('Insentif.edit');
        Route::put('/update/{id}', [RuleInsentifController::class, 'update'])->name('Insentif.update');
        Route::get('/show/{id}', [RuleInsentifController::class, 'show'])->name('Insentif.show');
        Route::delete('/delete/{id}', [RuleInsentifController::class, 'destroy'])->name('Insentif.destroy');
    });
    Route::prefix('laporan')->group(function () {
        Route::get('/umum', [LaporanController::class, 'indexUmum'])->name('laporan-umum.index');
        Route::get('/perawat', [LaporanController::class, 'indexPerawat'])->name('laporan-perawat.index');
    });
    Route::get('/dashboard/kirim-pencarian', [DashboardController::class, 'kirimPencarian'])->name('dashboard.kirim-pencarian');
});
