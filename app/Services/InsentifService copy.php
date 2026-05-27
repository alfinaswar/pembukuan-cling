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

        $tindakans = $transaksi
            ->TransaksiDetail()
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
            ->orderByDesc('Nilai')
            ->get();

        foreach ($rules as $rule) {
            $value = $context[$rule->JenisRule] ?? null;

            if ($value === null) {
                continue;
            }

            // 🔥 FIX: Validasi JenisPasien untuk rule pasien_baru & pasien_lama
            if ($rule->JenisRule == 'pasien_baru' && $transaksi->JenisPasien !== 'Baru') {
                continue;
            }

            if ($rule->JenisRule == 'pasien_lama' && $transaksi->JenisPasien !== 'Lama') {
                continue;
            }

            $isValid = false;
            $finalNominal = $rule->Nominal;  // Default nominal

            // =================================================
            // RULE TINDAKAN
            // =================================================

            if ($rule->JenisRule == 'tindakan') {
                foreach ($value as $tindakan) {
                    if (str_contains(strtolower($tindakan), strtolower($rule->Nilai))) {
                        $isValid = true;
                        break;
                    }
                }
            }
            // 🔥 NEW: RULE KELOMPOK / KELIPATAN
            elseif ($rule->Operator == 'kelipatan') {
                // Hitung berapa kali kelipatan tercapai (floor division)
                $multiplier = floor($value / $rule->Nilai);

                if ($multiplier >= 1) {
                    $isValid = true;
                    // Nominal akhir = kelipatan × nominal dasar
                    $finalNominal = $multiplier * $rule->Nominal;
                }
            }
            // =================================================
            // RULE OPERATOR BIASA (>=, <=, =)
            // =================================================
            else {
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
                'Nominal' => $finalNominal,  // 🔥 Gunakan nominal hasil kalkulasi
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
            2 => $transaksi->IdDokter,  // DOKTER
            3 => $transaksi->IdResepsionis,  // RESEPSIONIS
            4 => $transaksi->IdPerawat,  // PERAWAT
            default => null
        };
    }
}
