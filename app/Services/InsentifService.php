<?php

namespace App\Services;

use App\Models\InsentifKaryawan;
use App\Models\RuleInsentif;
use App\Models\Transaksi;

class InsentifService
{
    public function proses($transaksi)
    {
        $tanggal = $transaksi->Tanggal;
        $shift = $transaksi->Shift;
        $kodeCabang = $transaksi->KodeCabang;

        // =====================================================
        // HITUNG DATA SHIFT
        // =====================================================

        $totalShift = Transaksi::whereDate('Tanggal', $tanggal)
            ->where('Shift', $shift)
            ->where('KodeCabang', $kodeCabang)
            ->sum('TotalBayar');

        $pasienLama = Transaksi::whereDate('Tanggal', $tanggal)
            ->where('Shift', $shift)
            ->where('KodeCabang', $kodeCabang)
            ->where('JenisPasien', 'Lama')
            ->count();

        $pasienBaru = Transaksi::whereDate('Tanggal', $tanggal)
            ->where('Shift', $shift)
            ->where('KodeCabang', $kodeCabang)
            ->where('JenisPasien', 'Baru')
            ->count();

        $totalTransaksi = $transaksi->TotalBayar;

        // =====================================================
        // AMBIL NAMA TINDAKAN
        // =====================================================

        $tindakans = $transaksi->TransaksiDetail()
            ->with('MasterJenisPerawatan')
            ->get()
            ->pluck('MasterJenisPerawatan.Nama')
            ->toArray();

        // =====================================================
        // CONTEXT RULE
        // =====================================================

        $context = [
            'omzet_shift' => $totalShift,
            'pasien_lama' => $pasienLama,
            'pasien_baru' => $pasienBaru,
            'transaksi' => $totalTransaksi,
            'tindakan' => $tindakans,
        ];

        // =====================================================
        // AMBIL RULE AKTIF
        // =====================================================

        $rules = RuleInsentif::where('Status', 1)
            ->where('KodeCabang', $kodeCabang)
            ->orderByDesc('Nilai')
            ->get();

        foreach ($rules as $rule) {

            $value = $context[$rule->JenisRule] ?? null;

            if ($value === null) {
                continue;
            }

            $isValid = false;

            // =================================================
            // RULE TINDAKAN
            // =================================================

            if ($rule->JenisRule == 'tindakan') {

                $isValid = in_array($rule->Nilai, $value);

            } else {

                switch ($rule->Operator) {

                    case '>=':
                        $isValid = $value >= $rule->Nilai;
                        break;

                    case '<=':
                        $isValid = $value <= $rule->Nilai;
                        break;

                    case '=':
                        $isValid = $value == $rule->Nilai;
                        break;
                }
            }

            // =================================================
            // JIKA TIDAK VALID
            // =================================================

            if (!$isValid) {
                continue;
            }

            // =================================================
            // USER BERDASARKAN ROLE
            // =================================================

            $userId = $this->getUserByRole($transaksi, $rule->Role);

            if (!$userId) {
                continue;
            }

            // =================================================
            // CEK DUPLICATE
            // =================================================

            if ($rule->BerlakuPer == 'shift') {

                $exists = InsentifKaryawan::where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->where('Shift', $shift)
                    ->whereDate('created_at', date('Y-m-d', strtotime($tanggal)))
                    ->exists();

            } else {

                $exists = InsentifKaryawan::where('IdTransaksi', $transaksi->id)
                    ->where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->exists();
            }

            if ($exists) {
                continue;
            }

            // =================================================
            // SIMPAN INSENTIF
            // =================================================

            InsentifKaryawan::create([
                'IdTransaksi' => $transaksi->id,
                'UserId' => $userId,
                'Role' => $rule->Role,
                'Nominal' => $rule->Nominal,
                'JenisRule' => $rule->JenisRule,
                'Keterangan' => $rule->Keterangan,
                'Shift' => $shift,
                'KodeCabang' => $kodeCabang,
                'UserCreate' => auth()->user()->name,
            ]);
        }
    }

    // =====================================================
    // MAPPING ROLE
    // =====================================================

    private function getUserByRole($transaksi, $role)
    {
        return match ((int) $role) {

            // RESEPSIONIS
            5 => $transaksi->IdResepsionis,

            // PERAWAT
            4 => $transaksi->IdPerawat,

            // DOKTER
            3 => $transaksi->IdDokter,

            default => null
        };
    }
}
