<?php

namespace App\Services;

use App\Models\InsentifKaryawan;
use App\Models\MasterHariLibur;
use App\Models\MasterKlinik;
use App\Models\RuleInsentif;
use App\Models\TargetCapaian;
use App\Models\Transaksi;
use App\Models\User;
use Carbon\Carbon;

class InsentifService
{
    /**
     * Hapus insentif lama dari transaksi ini sebelum menghitung ulang
     */
    public function hapusSebelumProses($transaksi)
    {
        InsentifKaryawan::where('IdTransaksi', $transaksi->id)
            ->where('JenisRule', '!=', 'target_tercapai')
            ->delete();
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

            // A. Rule: Insentif Hari Libur (1x per SHIFT)
            if ($rule->JenisRule == 'insentif_hari_libur') {
                $userId = $this->getUserByRole($transaksi, $rule->Role);
                if (!$userId)
                    continue;

                $isHoliday = $this->isHariLibur($transaksi->Tanggal);
                if (!$isHoliday)
                    continue;

                // Cek insentif hari libur sudah didapatkan untuk user pada SHIFT yang sama
                $sudahDapatHariIniQuery = InsentifKaryawan::where('UserId', $userId)
                    ->where('Role', $rule->Role)
                    ->where('JenisRule', $rule->JenisRule)
                    ->whereDate('Tanggal', $tanggal)
                    ->where('Shift', $shift)
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
                $kodeKlinik = MasterKlinik::where('Kode', $transaksi->KodeCabang)->value('id');
                $targetBulanan = TargetCapaian::where('Tahun', $tanggalCarbon->year)
                    ->where('Bulan', $tanggalCarbon->month)
                    ->where('IdKlinik', $kodeKlinik)
                    ->first();

                if (!$targetBulanan)
                    continue;

                $threshold = (float) $targetBulanan->BesarTarget;

                // Hitung akumulasi omzet bulanan cabang
                $omzetBulanan = Transaksi::where('KodeCabang', $kodeCabang)
                    ->whereYear('Tanggal', $tanggalCarbon->year)
                    ->whereMonth('Tanggal', $tanggalCarbon->month)
                    ->sum('TotalBayar');

                $metricValue = $omzetBulanan;

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

                // Ambil semua karyawan dengan role ini di cabang tersebut (tetap dapat semua)
                $targetUserIds = $this->getUserIdsByRoleAndCabang($rule->Role, $kodeCabang, $tanggalCarbon->year, $tanggalCarbon->month);

                if (empty($targetUserIds)) {
                    $singleUser = $this->getUserByRole($transaksi, $rule->Role);
                    if ($singleUser) {
                        $targetUserIds = [$singleUser];
                    }
                }

                foreach ($targetUserIds as $targetUserId) {
                    $sudahDapatBulanIniQuery = InsentifKaryawan::where('UserId', $targetUserId)
                        ->where('Role', $rule->Role)
                        ->where('JenisRule', $rule->JenisRule)
                        ->whereYear('Tanggal', $tanggalCarbon->year)
                        ->whereMonth('Tanggal', $tanggalCarbon->month)
                        ->where('KodeCabang', $kodeCabang);

                    // Filter dental unit tetap dipertahankan
                    if ($dentalUnit) {
                        $sudahDapatBulanIniQuery->where('DentalUnit', $dentalUnit);
                    }

                    if ($sudahDapatBulanIniQuery->exists())
                        continue;

                    InsentifKaryawan::create([
                        'IdTransaksi' => $transaksi->id,
                        'Tanggal' => $transaksi->Tanggal,
                        'UserId' => $targetUserId,
                        'Role' => $rule->Role,
                        'Nominal' => $rule->Nominal,
                        'JenisRule' => $rule->JenisRule,
                        'Keterangan' => $rule->Keterangan,
                        'Shift' => $shift,
                        'KodeCabang' => $kodeCabang,
                        'DentalUnit' => $dentalUnit,
                        'UserCreate' => auth()->check() ? auth()->user()->name : 'System',
                    ]);
                }

                continue;
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
                'UserCreate' => auth()->check() ? auth()->user()->name : 'System',
            ]);
        }
    }

    // =====================================================
    // 🔥 HELPER: Ambil semua user sesuai role & cabang
    // =====================================================
    public function getUserIdsByRoleAndCabang($role, $kodeCabang, $tahun = null, $bulan = null)
    {
        $roleName = match ((int) $role) {
            2 => 'Dokter',
            3 => 'Kasir / Resepsionis',
            4 => 'Perawat',
            default => null
        };

        if (!$roleName) {
            return [];
        }

        // 1. Karyawan dengan role terkait yang terdaftar di cabang tersebut
        $userIds = User::role($roleName)
            ->where('kodeperusahaan', $kodeCabang)
            ->pluck('id')
            ->toArray();

        // 2. Karyawan yang punya riwayat transaksi di cabang tersebut pada periode terkait
        $columnRole = match ((int) $role) {
            2 => 'IdDokter',
            3 => 'IdResepsionis',
            4 => 'IdPerawat',
            default => null
        };

        if ($columnRole && $tahun && $bulan) {
            $transaksiUserIds = Transaksi::where('KodeCabang', $kodeCabang)
                ->whereYear('Tanggal', $tahun)
                ->whereMonth('Tanggal', $bulan)
                ->whereNotNull($columnRole)
                ->distinct()
                ->pluck($columnRole)
                ->toArray();

            $activeTransUserIds = User::whereIn('id', $transaksiUserIds)->pluck('id')->toArray();
            $userIds = array_merge($userIds, $activeTransUserIds);
        }

        return array_values(array_unique(array_filter($userIds)));
    }

    // =====================================================
    // 🔥 HITUNG MANUAL INSENTIF TARGET BULANAN
    // =====================================================
    public function hitungTargetBulanan($tahun, $bulan, $kodeCabang = null)
    {
        $rules = RuleInsentif::where('Status', 1)
            ->where('JenisRule', 'target_tercapai')
            ->get();

        if ($rules->isEmpty()) {
            return [
                'status' => false,
                'message' => 'Tidak ada rule insentif target_tercapai yang aktif.',
                'created_count' => 0,
            ];
        }

        $clinicsQuery = MasterKlinik::query();
        if ($kodeCabang) {
            $clinicsQuery->where('Kode', $kodeCabang);
        }
        $clinics = $clinicsQuery->get();

        $createdCount = 0;
        $processedClinics = [];

        foreach ($clinics as $klinik) {
            $targetBulanan = TargetCapaian::where('Tahun', $tahun)
                ->where('Bulan', $bulan)
                ->where('IdKlinik', $klinik->id)
                ->first();

            if (!$targetBulanan) {
                continue;
            }

            $threshold = (float) $targetBulanan->BesarTarget;

            // Omzet akumulasi sebulan klinik
            $omzetBulan = Transaksi::where('KodeCabang', $klinik->Kode)
                ->whereYear('Tanggal', $tahun)
                ->whereMonth('Tanggal', $bulan)
                ->sum('TotalBayar');

            $latestTransaksiCabang = Transaksi::where('KodeCabang', $klinik->Kode)
                ->whereYear('Tanggal', $tahun)
                ->whereMonth('Tanggal', $bulan)
                ->orderByDesc('Tanggal')
                ->orderByDesc('id')
                ->first();

            if (!$latestTransaksiCabang) {
                continue;
            }

            foreach ($rules as $rule) {
                $isValid = match ($rule->Operator) {
                    '>=' => $omzetBulan >= $threshold,
                    '<=' => $omzetBulan <= $threshold,
                    '=' => $omzetBulan == $threshold,
                    '>' => $omzetBulan > $threshold,
                    '<' => $omzetBulan < $threshold,
                    default => false,
                };

                if (!$isValid) {
                    continue;
                }

                $targetUserIds = $this->getUserIdsByRoleAndCabang($rule->Role, $klinik->Kode, $tahun, $bulan);

                foreach ($targetUserIds as $targetUserId) {
                    $columnRole = match ((int) $rule->Role) {
                        2 => 'IdDokter',
                        3 => 'IdResepsionis',
                        4 => 'IdPerawat',
                        default => null
                    };

                    $userLastTransaksi = null;
                    if ($columnRole) {
                        $userLastTransaksi = Transaksi::where('KodeCabang', $klinik->Kode)
                            ->whereYear('Tanggal', $tahun)
                            ->whereMonth('Tanggal', $bulan)
                            ->where($columnRole, $targetUserId)
                            ->orderByDesc('Tanggal')
                            ->orderByDesc('id')
                            ->first();
                    }

                    $refTransaksi = $userLastTransaksi ?: $latestTransaksiCabang;
                    $dentalUnit = $refTransaksi->DentalUnit ?? null;

                    $sudahDapatQuery = InsentifKaryawan::where('UserId', $targetUserId)
                        ->where('Role', $rule->Role)
                        ->where('JenisRule', $rule->JenisRule)
                        ->whereYear('Tanggal', $tahun)
                        ->whereMonth('Tanggal', $bulan)
                        ->where('KodeCabang', $klinik->Kode);

                    // Filter dental unit tetap dipertahankan
                    if ($dentalUnit) {
                        $sudahDapatQuery->where('DentalUnit', $dentalUnit);
                    }

                    if ($sudahDapatQuery->exists()) {
                        continue;
                    }

                    InsentifKaryawan::create([
                        'IdTransaksi' => $refTransaksi->id,
                        'Tanggal' => $refTransaksi->Tanggal,
                        'UserId' => $targetUserId,
                        'Role' => $rule->Role,
                        'Nominal' => $rule->Nominal,
                        'JenisRule' => $rule->JenisRule,
                        'Keterangan' => $rule->Keterangan,
                        'Shift' => $refTransaksi->Shift,
                        'KodeCabang' => $klinik->Kode,
                        'DentalUnit' => $dentalUnit,
                        'UserCreate' => auth()->check() ? auth()->user()->name : 'System',
                    ]);

                    $createdCount++;
                }

                $processedClinics[] = $klinik->Nama;
            }
        }

        $clinicList = !empty($processedClinics) ? implode(', ', array_unique($processedClinics)) : '-';

        return [
            'status' => true,
            'message' => "Berhasil memproses insentif target bulanan. Total {$createdCount} insentif baru berhasil ditambahkan untuk cabang yang mencapai target ({$clinicList}).",
            'created_count' => $createdCount,
            'clinics' => array_unique($processedClinics),
        ];
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
