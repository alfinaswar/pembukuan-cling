<?php

namespace App\Services;

use App\Models\InsentifKaryawan;
use App\Models\RuleInsentif;
use App\Models\Transaksi;

class InsentifService
{
    /**
     * Hapus insentif lama dari transaksi ini sebelum menghitung ulang
     */
    public function hapusSebelumProses($transaksi)
    {
        InsentifKaryawan::where('IdTransaksi', $transaksi->id)->delete();
    }

    public function proses($transaksi)
    {
        $tanggal = $transaksi->Tanggal;
        $shift = $transaksi->Shift;
        $kodeCabang = $transaksi->KodeCabang;

        // =====================================================
        // 1. HITUNG DATA SHIFT (Akumulasi Real-time)
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
        // 2. AMBIL DATA PENDUKUNG
        // =====================================================
        $tindakans = $transaksi
            ->TransaksiDetail()
            ->with('MasterJenisPerawatan')
            ->get()
            ->pluck('MasterJenisPerawatan.Nama')
            ->toArray();

        $context = [
            'omzet_shift' => $totalShift,  // 🔥 Kunci untuk rule kelipatan
            'pasien_lama' => $pasienLama,
            'pasien_baru' => $pasienBaru,
            'transaksi' => $totalTransaksi,
            'tindakan' => $tindakans,
        ];

        $rules = RuleInsentif::where('Status', 1)->orderByDesc('Nilai')->get();

        foreach ($rules as $rule) {
            $value = $context[$rule->JenisRule] ?? null;
            if ($value === null)
                continue;

            // --- Validasi Konteks ---
            if ($rule->JenisRule == 'pasien_baru' && $transaksi->JenisPasien !== 'Baru')
                continue;
            if ($rule->JenisRule == 'pasien_lama' && $transaksi->JenisPasien !== 'Lama')
                continue;

            // 🔥 FIX: Hitung userId DI SINI (sebelum digunakan di logika kelipatan)
            $userId = $this->getUserByRole($transaksi, $rule->Role);
            if (!$userId)
                continue;

            $isValid = false;
            $finalNominal = $rule->Nominal;

            // =================================================
            // 3. EVALUASI RULE
            // =================================================

            // A. Rule Tindakan (Cek string)
            if ($rule->JenisRule == 'tindakan') {
                foreach ($value as $tindakan) {
                    if (str_contains(strtolower($tindakan), strtolower($rule->Nilai))) {
                        $isValid = true;
                        break;
                    }
                }
            }
            // B. 🔥 Rule Kelipatan (Akumulasi Shift)
            elseif ($rule->Operator == 'kelipatan') {
                $threshold = $rule->Nilai;  // Misal: 6.000.000

                // 1. Hitung total kelipatan yang TERCAPAI saat ini di shift tersebut
                $totalKelipatanTercapai = floor($totalShift / $threshold);

                if ($totalKelipatanTercapai >= 1) {
                    $isValid = true;

                    // 2. Hitung berapa insentif kelipatan yang SUDAH PERNAH dibagikan untuk shift ini
                    // (Query ini aman karena insentif transaksi ini sudah di-delete di hapusSebelumProses)
                    $sudahDiberikan = InsentifKaryawan::where('UserId', $userId)
                        ->where('Role', $rule->Role)
                        ->where('JenisRule', $rule->JenisRule)
                        ->where('Shift', $shift)
                        ->whereDate('created_at', date('Y-m-d', strtotime($tanggal)))
                        ->where('KodeCabang', $kodeCabang)
                        ->count();

                    // 3. Hitung selisih: hanya berikan untuk kelipatan BARU yang belum dibayar
                    $kelipatanBaru = $totalKelipatanTercapai - $sudahDiberikan;

                    if ($kelipatanBaru > 0) {
                        // Nominal = jumlah kelipatan baru × nominal dasar rule
                        $finalNominal = $kelipatanBaru * $rule->Nominal;
                    } else {
                        // Semua kelipatan sudah dibayar, skip transaksi ini untuk rule ini
                        continue;
                    }
                }
            }
            // C. Rule Operator Biasa (>=, <=, =)
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
            // 4. FINALISASI & SIMPAN
            // =================================================
            if (!$isValid)
                continue;

            // Safety check duplicate untuk rule NON-kelipatan (per-shift)
            if ($rule->BerlakuPer == 'shift' && $rule->Operator != 'kelipatan') {
                $exists = InsentifKaryawan::where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->where('Shift', $shift)
                    ->whereDate('created_at', date('Y-m-d', strtotime($tanggal)))
                    ->where('KodeCabang', $kodeCabang)
                    ->exists();
                if ($exists)
                    continue;
            }

            // Simpan ke Database
            InsentifKaryawan::create([
                'IdTransaksi' => $transaksi->id,
                'UserId' => $userId,
                'Role' => $rule->Role,
                'Nominal' => $finalNominal,  // Bisa 1x atau Nx nominal jika ada beberapa kelipatan baru
                'JenisRule' => $rule->JenisRule,
                'Keterangan' => $rule->Keterangan,
                'Shift' => $shift,
                'KodeCabang' => $kodeCabang,
                'UserCreate' => auth()->user()->name,
            ]);
        }
    }

    private function getUserByRole($transaksi, $role)
    {
        return match ((int) $role) {
            2 => $transaksi->IdDokter,
            3 => $transaksi->IdResepsionis,
            4 => $transaksi->IdPerawat,
            default => null
        };
    }
}
