<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DentalUnitController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\MasterHariLiburController;
use App\Http\Controllers\MasterJenisPerawatanController;
use App\Http\Controllers\MasterKlinikController;
use App\Http\Controllers\MasterMetodePembayaranController;
use App\Http\Controllers\MasterShiftController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RuleInsentifController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
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
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::post('/home/update-shift', [HomeController::class, 'updateShift'])->name('home.update-shift');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/session/ping', function () {
        return response()->json(['status' => 'ok']);
    })->name('session.ping');

    // Logout manual via AJAX (opsional, bisa juga pakai route logout bawaan Breeze/Jetstream)
    Route::post('/session/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return response()->json(['status' => 'logged_out']);
    })->name('session.logout');
    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::get('/profile/edit', [UserController::class, 'editProfile'])->name('profile.edit');
    Route::put('/profile/update', [UserController::class, 'updateProfile'])->name('profile.update');
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

        //
        Route::get('/target-capaian/{id}', [MasterKlinikController::class, 'targetCapaian'])->name('Klinik.target-capaian');
        Route::delete('/delete-target/{id}', [MasterKlinikController::class, 'deleteTarget'])->name('Klinik.delete-target');
        Route::get('/tambah-target/{id}', [MasterKlinikController::class, 'buatTarget'])->name('Klinik.tambah-target');
        Route::post('/store/{id}', [MasterKlinikController::class, 'storeTarget'])->name('Klinik.simpan-target');
        Route::get('/show/{id}', [MasterKlinikController::class, 'showTarget'])->name('Klinik.show');
        Route::get('/edit-target/{id}', [MasterKlinikController::class, 'editTarget'])->name('Klinik.edit-target');
        Route::put('/update-target/{id}', [MasterKlinikController::class, 'updateTarget'])->name('Klinik.update-target');
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
    Route::prefix('master/hari-libur')->group(function () {
        Route::get('/', [MasterHariLiburController::class, 'index'])->name('MasterHariLibur.index');
        Route::get('/create', [MasterHariLiburController::class, 'create'])->name('MasterHariLibur.create');
        Route::post('/store', [MasterHariLiburController::class, 'store'])->name('MasterHariLibur.store');
        Route::get('/edit/{id}', [MasterHariLiburController::class, 'edit'])->name('MasterHariLibur.edit');
        Route::put('/update/{id}', [MasterHariLiburController::class, 'update'])->name('MasterHariLibur.update');
        Route::get('/show/{id}', [MasterHariLiburController::class, 'show'])->name('MasterHariLibur.show');
        Route::delete('/delete/{id}', [MasterHariLiburController::class, 'destroy'])->name('MasterHariLibur.destroy');
    });
    Route::prefix('master/dental-unit')->group(function () {
        Route::get('/', [DentalUnitController::class, 'index'])->name('DentalUnit.index');
        Route::get('/create/{id}', [DentalUnitController::class, 'create'])->name('DentalUnit.create');
        Route::post('/store', [DentalUnitController::class, 'store'])->name('DentalUnit.store');
        Route::get('/edit/{id}', [DentalUnitController::class, 'edit'])->name('DentalUnit.edit');
        Route::put('/update/{id}', [DentalUnitController::class, 'update'])->name('DentalUnit.update');
        Route::get('/show/{id}', [DentalUnitController::class, 'show'])->name('DentalUnit.show');
        Route::delete('/delete/{id}', [DentalUnitController::class, 'destroy'])->name('DentalUnit.destroy');
    });
    Route::prefix('transaksi/kasir')->group(function () {
        Route::get('/', [TransaksiController::class, 'index'])->name('Transaksi.index');
        Route::get('/riwayat-kunjungan', [TransaksiController::class, 'indexKunjungan'])->name('Transaksi.index-kunjungan');
        Route::get('/create', [TransaksiController::class, 'create'])->name('Transaksi.create');
        Route::post('/store', [TransaksiController::class, 'store'])->name('Transaksi.store');
        Route::get('/edit/{id}', [TransaksiController::class, 'edit'])->name('Transaksi.edit');
        Route::put('/update/{id}', [TransaksiController::class, 'update'])->name('Transaksi.update');
        Route::get('/show/{id}', [TransaksiController::class, 'show'])->name('Transaksi.show');
        Route::delete('/delete/{id}', [TransaksiController::class, 'destroy'])->name('Transaksi.destroy');
        Route::get('/summary', [TransaksiController::class, 'summary'])->name('Transaksi.summary');
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
        Route::post('/data-umum', [LaporanController::class, 'dataDashboardUmum'])->name('laporan-umum.store');

        Route::get('/perawat', [LaporanController::class, 'indexPerawat'])->name('laporan-perawat.index');
        Route::get('/cari-data-perawat', [LaporanController::class, 'dataDashboardPerawat'])->name('laporan-perawat.store');

        Route::get('/resepsionis', [LaporanController::class, 'indexResepsionis'])->name('laporan-resepsionis.index');
        Route::get('/cari-data-resepsionis', [LaporanController::class, 'dataDashboardResepsionis'])->name('laporan-resepsionis.store');

        Route::get('/dokter', [LaporanController::class, 'indexDokter'])->name('laporan-dokter.index');
        Route::get('/cari-data-dokter', [LaporanController::class, 'dataDashboardDokter'])->name('laporan-dokter.store');
        Route::get('/dokter/export-excel', [LaporanController::class, 'downloadExcel'])->name('laporan-dokter.download-excel');

        Route::get('/perawat/billing-minimal', [LaporanController::class, 'billingMinimalPerawat'])->name('laporan-perawat.billing-minimal');

        Route::get('/transaksi', [LaporanController::class, 'indexTransaksi'])->name('laporan-transaksi.index');
        Route::post('/laporan-transaksi/preview', [LaporanController::class, 'preview'])->name('laporan-transaksi.preview');
        Route::post('/download-data-transaksi', [LaporanController::class, 'downloadTransaksi'])->name('laporan-transaksi.store');

        Route::get('/jenis-perawatan', [LaporanController::class, 'indexJenisPerawatan'])->name('laporan-jenis-perawatan.index');
        Route::post('/laporan-jenis-perawatan/preview', [LaporanController::class, 'previewJenisPerawatan'])->name('laporan-jenis-perawatan.preview');
        Route::post('/laporan-jenis-perawatan/download', [LaporanController::class, 'downloadJenisPerawatan'])->name('laporan-jenis-perawatan.download');
    });
    Route::get('/dashboard/kirim-pencarian', [DashboardController::class, 'kirimPencarian'])->name('dashboard.kirim-pencarian');
});
