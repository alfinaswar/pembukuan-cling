<?php

namespace App\Services;

use App\Models\InsentifKaryawan;
use App\Models\MasterHariLibur;
use App\Models\MasterKlinik;
use App\Models\RuleInsentif;
use App\Models\TargetBulanan;  // <-- 🔥 Tambahkan import Model TargetBulanan
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
        $tanggalCarbon = Carbon::parse($tanggal);

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

                $sudahDapatHariIni = InsentifKaryawan::where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->whereDate('created_at', $tanggal)
                    ->where('KodeCabang', $kodeCabang)
                    ->exists();

                if ($sudahDapatHariIni)
                    continue;

                $isValid = true;
                $finalNominal = $rule->Nominal;
            }
            // B. Rule: Target Tercapai (1x per BULAN)
            elseif ($rule->JenisRule == 'target_tercapai') {
                $userId = $this->getUserByRole($transaksi, $rule->Role);
                if (!$userId)
                    continue;

                // 🔥 CEK FREKUENSI: Sudah dapat insentif bulan ini?
                $sudahDapatBulanIni = InsentifKaryawan::where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->whereYear('created_at', $tanggalCarbon->year)
                    ->whereMonth('created_at', $tanggalCarbon->month)
                    ->where('KodeCabang', $kodeCabang)
                    ->exists();

                if ($sudahDapatBulanIni)
                    continue;
                $kodeKlinik = MasterKlinik::where('Kode', $transaksi->KodeCabang)->value('id');
                $targetBulanan = TargetCapaian::where('Tahun', $tanggalCarbon->year)
                    ->where('Bulan', $tanggalCarbon->month)
                    ->where('IdKlinik', $kodeKlinik)
                    ->first();

                // Jika target bulan ini belum di-set di database, skip rule ini
                if (!$targetBulanan)
                    continue;

                // Threshold diambil dari tabel target, BUKAN dari $rule->Nilai
                $threshold = $targetBulanan->BesarTarget;

                // Ambil metric aktual dari context (misal: omzet_shift)
                $metricKey = $rule->KondisiTambahan ?? 'omzet_shift';
                $metricValue = $context[$metricKey] ?? 0;

                // Evaluasi berdasarkan operator menggunakan $threshold
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

                // 🔥 Besar insentif tetap diambil dari rule
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
                // D. Rule Kelipatan
                elseif ($rule->Operator == 'kelipatan') {
                    $threshold = $rule->Nilai;
                    $totalKelipatanTercapai = floor($totalShift / $threshold);

                    if ($totalKelipatanTercapai >= 1) {
                        $isValid = true;

                        $sudahDiberikan = InsentifKaryawan::where('UserId', $userId)
                            ->where('Role', $rule->Role)
                            ->where('JenisRule', $rule->JenisRule)
                            ->where('Shift', $shift)
                            ->whereDate('created_at', date('Y-m-d', strtotime($tanggal)))
                            ->where('KodeCabang', $kodeCabang)
                            ->count();

                        $kelipatanBaru = $totalKelipatanTercapai - $sudahDiberikan;

                        if ($kelipatanBaru > 0) {
                            $finalNominal = $kelipatanBaru * $rule->Nominal;
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
                'Nominal' => $finalNominal,
                'JenisRule' => $rule->JenisRule,
                'Keterangan' => $rule->Keterangan,
                'Shift' => $shift,
                'KodeCabang' => $kodeCabang,
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
