<?php

namespace App\Services;

use App\Models\InsentifKaryawan;
use App\Models\MasterHariLibur;
use App\Models\MasterKlinik;
use App\Models\RuleInsentif;
use App\Models\TargetCapaian;
use App\Models\Transaksi;
use Carbon\Carbon;

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
        $dentalUnit = $transaksi->DentalUnit ?? null; // 🔥 Ambil Dental Unit dari transaksi
        $tanggalCarbon = Carbon::parse($tanggal);

        // =====================================================
        // 1. HITUNG DATA SHIFT (Akumulasi Real-time)
        // =====================================================

        // Buat base query agar bisa di-clone untuk efisiensi
        $baseQuery = Transaksi::whereDate('Tanggal', $tanggal)
            ->where('Shift', $shift)
            ->where('KodeCabang', $kodeCabang);

        // 🔥 Jika Dental Unit ada, pisahkan akumulasi berdasarkan Dental Unit
        if ($dentalUnit) {
            $baseQuery->where('DentalUnit', $dentalUnit);
        }

        $totalShift = (clone $baseQuery)->sum('TotalBayar');

        $pasienLama = (clone $baseQuery)
            ->where('JenisPasien', 'Lama')
            ->count();
 
        $pasienBaru = (clone $baseQuery)
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
            'omzet_shift' => $totalShift,
            'pasien_lama' => $pasienLama,
            'pasien_baru' => $pasienBaru,
            'transaksi' => $totalTransaksi,
            'tindakan' => $tindakans,
        ];

        $rules = RuleInsentif::where('Status', 1)->orderByDesc('Nilai')->get();

        foreach ($rules as $rule) {
            $value = $context[$rule->JenisRule] ?? null;

            // =================================================
            // 🔥 HANDLE RULE KHUSUS
            // =================================================

            // A. Rule: Insentif Hari Libur (1x per HARI)
            if ($rule->JenisRule == 'insentif_hari_libur') {
                $userId = $this->getUserByRole($transaksi, $rule->Role);
                if (!$userId)
                    continue;

                $isHoliday = $this->isHariLibur($transaksi->Tanggal);
                if (!$isHoliday)
                    continue;

                $sudahDapatHariIniQuery = InsentifKaryawan::where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->whereDate('Tanggal', $tanggal)
                    ->where('KodeCabang', $kodeCabang);

                if ($dentalUnit) {
                    $sudahDapatHariIniQuery->where('DentalUnit', $dentalUnit);
                }

                if ($sudahDapatHariIniQuery->exists())
                    continue;

                $isValid = true;
                $finalNominal = $rule->Nominal;
            }
            // B. Rule: Target Tercapai (1x per BULAN)
            elseif ($rule->JenisRule == 'target_tercapai') {
                $userId = $this->getUserByRole($transaksi, $rule->Role);
                if (!$userId)
                    continue;

                $sudahDapatBulanIniQuery = InsentifKaryawan::where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->whereYear('Tanggal', $tanggalCarbon->year)
                    ->whereMonth('Tanggal', $tanggalCarbon->month)
                    ->where('KodeCabang', $kodeCabang);

                if ($dentalUnit) {
                    $sudahDapatBulanIniQuery->where('DentalUnit', $dentalUnit);
                }

                if ($sudahDapatBulanIniQuery->exists())
                    continue;

                $kodeKlinik = MasterKlinik::where('Kode', $transaksi->KodeCabang)->value('id');
                $targetBulanan = TargetCapaian::where('Tahun', $tanggalCarbon->year)
                    ->where('Bulan', $tanggalCarbon->month)
                    ->where('IdKlinik', $kodeKlinik)
                    ->first();

                if (!$targetBulanan)
                    continue;

                $threshold = $targetBulanan->BesarTarget;
                $metricKey = $rule->KondisiTambahan ?? 'omzet_shift';
                $metricValue = $context[$metricKey] ?? 0;

                switch ($rule->Operator) {
                    case '>=':
                        $isValid = $metricValue >= $threshold;
                        break;
                    case '<=':
                        $isValid = $metricValue <= $threshold;
                        break;
                    case '=':
                        $isValid = $metricValue == $threshold;
                        break;
                    case '>':
                        $isValid = $metricValue > $threshold;
                        break;
                    case '<':
                        $isValid = $metricValue < $threshold;
                        break;
                    default:
                        $isValid = false;
                }

                if (!$isValid)
                    continue;
                $finalNominal = $rule->Nominal;
            }
            // =================================================
            // 🔥 HANDLE RULE REGULER
            // =================================================
            else {
                if ($value === null)
                    continue;

                if ($rule->JenisRule == 'pasien_baru' && $transaksi->JenisPasien !== 'Baru')
                    continue;
                if ($rule->JenisRule == 'pasien_lama' && $transaksi->JenisPasien !== 'Lama')
                    continue;

                $userId = $this->getUserByRole($transaksi, $rule->Role);
                if (!$userId)
                    continue;

                $isValid = false;
                $finalNominal = $rule->Nominal;

                // C. Rule Tindakan
                if ($rule->JenisRule == 'tindakan') {
                    foreach ($value as $tindakan) {
                        if (str_contains(strtolower($tindakan), strtolower($rule->Nilai))) {
                            $isValid = true;
                            break;
                        }
                    }
                }
                // D. Rule Kelipatan (Misal: Kelipatan 6 Juta)
                elseif ($rule->Operator == 'kelipatan') {
                    $threshold = $rule->Nilai;
                    $totalKelipatanTercapai = floor($totalShift / $threshold);

                    if ($totalKelipatanTercapai >= 1) {
                        $isValid = true;

                        // 🔥 Cek duplikasi kelipatan PER Dental Unit
                        $sudahDiberikanQuery = InsentifKaryawan::where('UserId', $userId)
                            ->where('Role', $rule->Role)
                            ->where('JenisRule', $rule->JenisRule)
                            ->where('Shift', $shift)
                            ->whereDate('Tanggal', $tanggal)
                            ->where('KodeCabang', $kodeCabang);

                        if ($dentalUnit) {
                            $sudahDiberikanQuery->where('DentalUnit', $dentalUnit);
                        }

                        $sudahDiberikan = $sudahDiberikanQuery->count();
                        $kelipatanBaru = $totalKelipatanTercapai - $sudahDiberikan;

                        if ($kelipatanBaru > 0) {
                            $finalNominal = $rule->Nominal;
                        } else {
                            continue;
                        }
                    }
                }
                // E. Rule Operator Biasa
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
                        default:
                            $isValid = false;
                    }
                }

                if (!$isValid)
                    continue;
            }

            // =================================================
            // 3. CEK DUPLICATE & SIMPAN
            // =================================================
            if (
                $rule->BerlakuPer == 'shift' &&
                $rule->Operator != 'kelipatan' &&
                $rule->JenisRule != 'target_tercapai' &&
                $rule->JenisRule != 'insentif_hari_libur'
            ) {
                $existsQuery = InsentifKaryawan::where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->where('Shift', $shift)
                    ->whereDate('Tanggal', $tanggal)
                    ->where('KodeCabang', $kodeCabang);

                // 🔥 Pastikan cek duplikasi juga memisahkan berdasarkan Dental Unit
                if ($dentalUnit) {
                    $existsQuery->where('DentalUnit', $dentalUnit);
                }

                if ($existsQuery->exists())
                    continue;
            }

            // Simpan ke Database
            InsentifKaryawan::create([
                'IdTransaksi' => $transaksi->id,
                'Tanggal' => $transaksi->Tanggal,
                'UserId' => $userId,
                'Role' => $rule->Role,
                'Nominal' => $finalNominal,
                'JenisRule' => $rule->JenisRule,
                'Keterangan' => $rule->Keterangan,
                'Shift' => $shift,
                'KodeCabang' => $kodeCabang,
                'DentalUnit' => $dentalUnit, // 🔥 Simpan info Dental Unit ke riwayat insentif
                'UserCreate' => auth()->user()->name,
            ]);
        }
    }

    // =====================================================
    // 🔥 HELPER: Cek Hari Libur dengan MasterHariLibur
    // =====================================================
    private function isHariLibur($tanggal)
    {
        return MasterHariLibur::whereDate('TanggalLibur', $tanggal)->exists();
    }

    // =====================================================
    // MAPPING ROLE
    // =====================================================
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
