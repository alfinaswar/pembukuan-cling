<?php

namespace App\Services;

use App\Models\Insentif;
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
        // dd($kodeCabang);

        // 🔹 Hitung data
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

        $tindakans = $transaksi->TransaksiDetail->pluck('JenisPerawatan')->toArray();

        // 🔹 Context
        $context = [
            'omzet_shift' => $totalShift,
            'pasien_lama' => $pasienLama,
            'pasien_baru' => $pasienBaru,
            'transaksi' => $totalTransaksi,
            'tindakan' => $tindakans
        ];

        // 🔥 Ambil rule
        $rules = RuleInsentif::where('Status', 1)
            ->where('KodeCabang', $kodeCabang)
            ->get();

        foreach ($rules as $rule) {

            $value = $context[$rule->JenisRule] ?? null;
            $isValid = false;

            // 🔹 Tindakan (array)
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

            if ($isValid) {

                $userId = $this->getUserByRole($transaksi, $rule->Role);

                if (!$userId)
                    continue;

                // ❗ Hindari duplicate
                $exists = InsentifKaryawan::where('IdTransaksi', $transaksi->id)
                    ->where('UserId', $userId)
                    ->where('JenisRule', $rule->JenisRule)
                    ->exists();

                if ($exists)
                    continue;

                InsentifKaryawan::create([
                    'IdTransaksi' => $transaksi->id,
                    'UserId' => $userId,
                    'Role' => $rule->Role,
                    'Nominal' => $rule->Nominal,
                    'JenisRule' => $rule->JenisRule,
                    'Keterangan' => $rule->Keterangan,
                    'KodeCabang' => $kodeCabang,
                    'UserCreate' => auth()->user()->name,
                ]);
            }
        }
    }

    private function getUserByRole($transaksi, $role)
    {
        return match ($role) {
            '5' => $transaksi->IdResepsionis,
            '4' => $transaksi->IdPerawat,
            '3' => $transaksi->IdDokter,
            default => null
        };
    }
}
