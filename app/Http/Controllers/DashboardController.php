<?php

namespace App\Http\Controllers;

use App\Models\MasterShift;
use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function kirimPencarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');

        // ── Base query builder (reusable closure) ──────────────────────────────
        $baseQuery = function () use ($request, $tanggal) {
            return Transaksi::whereDate('Tanggal', $tanggal)
                ->when($request->shift, fn($q) => $q->where('Shift', $request->shift))
                ->when($request->kasir, fn($q) => $q->where('IdResepsionis', $request->kasir))
                ->when($request->perawat, fn($q) => $q->where('IdPerawat', $request->perawat));
        };

        // ── 1. Jumlah Shift aktif pada tanggal tersebut ────────────────────────
        //    (distinct shift yang punya transaksi)
        $jumlahShift = Transaksi::whereDate('Tanggal', $tanggal)
            ->when($request->shift, fn($q) => $q->where('Shift', $request->shift))
            ->distinct('Shift')
            ->count('Shift');

        // Total pendapatan minimal per shift (sum semua billing)
        $totalPendapatan = $baseQuery()->sum('TotalBiaya'); // sesuaikan nama kolom

        // ── 2. Total Pasien Lama dalam filter ─────────────────────────────────
        //    Pasien lama = pasien yang sudah pernah bertransaksi sebelum tanggal ini
        $totalPasienLama = $baseQuery()
            ->whereHas('pasien', function ($q) use ($tanggal) {
                $q->whereHas('transaksi', function ($q2) use ($tanggal) {
                    $q2->whereDate('Tanggal', '<', $tanggal);
                });
            })
            ->count();

        // ── 3. Total Pasien dengan Billing >= Rp 1.000.000 ────────────────────
        $totalPasienBillingBesar = $baseQuery()
            ->where('TotalBiaya', '>=', 1000000) // sesuaikan nama kolom
            ->count();

        // ── 4. Total Pasien Baru ───────────────────────────────────────────────
        //    Pasien baru = belum pernah transaksi sebelum tanggal ini
        $totalPasienBaru = $baseQuery()
            ->whereHas('pasien', function ($q) use ($tanggal) {
                $q->whereDoesntHave('transaksi', function ($q2) use ($tanggal) {
                    $q2->whereDate('Tanggal', '<', $tanggal);
                });
            })
            ->count();

        // ── 5. Total Pasien Operasi OD ────────────────────────────────────────
        //    Sesuaikan kondisi dengan field yang menandai operasi OD di sistem kamu
        $totalOperasiOD = $baseQuery()
            ->where('JenisPerawatan', 'like', '%OD%') // sesuaikan kolom / relasi
            ->count();

        // ── Data pendukung (resepsionis & perawat bertugas) ───────────────────
        // Ambil resepsionis yang aktif pada filter ini
        $resepsionisIds = $baseQuery()->distinct()->pluck('IdResepsionis');
        $perawatIds = $baseQuery()->distinct()->pluck('IdPerawat');

        $resepsionisAktif = \App\Models\User::whereIn('id', $resepsionisIds)->get(['id', 'name']);
        $perawatAktif = \App\Models\User::whereIn('id', $perawatIds)->get(['id', 'name']);

        // Shift aktif detail
        $shiftAktif = \App\Models\MasterShift::whereIn(
            'id',
            Transaksi::whereDate('Tanggal', $tanggal)
                ->when($request->shift, fn($q) => $q->where('Shift', $request->shift))
                ->distinct('Shift')
                ->pluck('Shift')
        )->get();

        // ── Kembalikan JSON ───────────────────────────────────────────────────
        return response()->json([
            // Card 1
            'jumlahShift' => $jumlahShift,
            'totalPendapatan' => $totalPendapatan,

            // Card 2
            'totalPasienLama' => $totalPasienLama,

            // Card 3
            'totalPasienBillingBesar' => $totalPasienBillingBesar,

            // Card 4
            'totalPasienBaru' => $totalPasienBaru,

            // Card 5
            'totalOperasiOD' => $totalOperasiOD,

            // Pendukung
            'resepsionisAktif' => $resepsionisAktif,
            'perawatAktif' => $perawatAktif,
            'shiftAktif' => $shiftAktif,

            // Meta
            'tanggal' => $tanggal,
            'updatedAt' => now()->format('d M Y H:i'),
        ]);
    }
}
