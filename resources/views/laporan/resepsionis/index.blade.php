@extends('layouts.app')

@section('content')
    <style>
        .dashboard-card-bg-primary {
            background: rgba(13, 110, 253, 0.18) !important;
        }

        .dashboard-card-bg-info {
            background: rgba(13, 202, 240, 0.18) !important;
        }

        .dashboard-card-bg-success {
            background: rgba(25, 135, 84, 0.18) !important;
        }

        .dashboard-card-bg-warning {
            background: rgba(255, 193, 7, 0.18) !important;
        }

        .dashboard-card-bg-danger {
            background: rgba(220, 53, 69, 0.18) !important;
        }

        .dashboard-card-bg-secondary {
            background: rgba(108, 117, 125, 0.18) !important;
        }
    </style>
    <style>
        /* Custom for 5 even cols on large and above */
        @media (min-width: 992px) {
            .col-lg-2-4 {
                flex: 0 0 20%;
                max-width: 20%;
            }
        }
    </style>
    <style>
        /* Memblokir semua event klik pada container Select2 */
        .select2-readonly-wrapper .select2-container {
            pointer-events: none;
            cursor: not-allowed;
        }

        /* Mengubah warna background agar terlihat seperti disabled */
        .select2-readonly-wrapper .select2-selection {
            background-color: #f3f3f3 !important;
            border-color: #ced4da !important;
            cursor: not-allowed !important;
        }

        /* Memberikan efek visual pudar sedikit agar jelas tidak bisa diklik */
        .select2-readonly-wrapper .select2-container {
            opacity: 0.75;
        }
    </style>
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h5 class="mb-0 fw-bold">Dashboard Resepsionis</h5>
            <small class="text-muted">Ringkasan pencapaian dan perhitungan insentif berdasarkan performa</small>
        </div>
        {{-- <div class="col-lg-6 col-md-6 col-sm-12 d-flex justify-content-end align-items-center">
            <div class="input-group me-2" style="max-width: 220px;">
                <input type="text" class="form-control" placeholder="2024-06-04" id="mdate" />
                <span class="input-group-text">
                    <i class="ti ti-calendar fs-5"></i>
                </span>
            </div>
            <a href="" class="btn btn-success" style="background-color: green;">
                <i class="ti ti-file-export"></i> Export
            </a>
        </div> --}}
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body pb-2">
                    <form id="perawatFilterForm" class="row align-items-end g-2" method="GET"
                        action="{{ route('laporan-resepsionis.store') }}" style="font-size: 0.925rem;">
                        {{-- @csrf --}}
                        @php
                            $user = Auth::user();
                            $perawat_id = old('perawat', request('perawat', $user->id ?? ''));
                            $isSuperadminOrManagement =
                                $user && ($user->hasRole('Superadmin') || $user->hasRole('Management'));
                            $isDisabled = !$isSuperadminOrManagement;
                        @endphp

                        <div class="col-md-4">
                            <label for="perawatSelect" class="form-label mb-1" style="font-size: 0.95em;">Pilih
                                Kasir / Resepsionis</label>

                            <div class="input-group mb-2 {{ $isDisabled ? 'select2-readonly-wrapper' : '' }}">
                                <select id="perawatSelect" name="perawat" class="select2 form-control"
                                    style="width:100%; font-size: 0.96em; min-height:36px;"
                                    {{ $isDisabled ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                    <option value="">Pilih Kasir / Resepsionis</option>
                                    @php
                                        // Ambil prioritas: old input -> request() -> user id
                                        $selectedKasirId = old('perawat', request('perawat', $user->id ?? ''));
                                    @endphp
                                    @foreach ($kasir as $p)
                                        <option value="{{ $p->id }}"
                                            @if (!$isDisabled) {{ $selectedKasirId == $p->id ? 'selected' : '' }}
                                            @else
                                                {{ $user && $user->id == $p->id ? 'selected' : '' }} @endif>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="col-md-4">
                            <label for="shiftSelect" class="form-label mb-1" style="font-size: 0.95em;">Pilih Shift</label>
                            <div class="input-group mb-2">
                                <select id="shiftSelect" name="shift" class="select2 form-control"
                                    style="width:100%; font-size: 0.96em; min-height:36px;">
                                    <option value="">Pilih Shift</option>
                                    @foreach ($shift as $s)
                                        <option value="{{ $s->id }}"
                                            {{ old('shift', request('shift')) == $s->id ? 'selected' : '' }}>
                                            {{ $s->Nama ?? ($s->name ?? '-') }}
                                            ({{ \Carbon\Carbon::parse($s->JamMulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($s->JamSelesai)->format('H:i') }})
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        </div>
                        <!-- Pilih Periode -->
                        <div class="col-md-4">
                            <label for="periodeInput" class="form-label mb-1" style="font-size: 0.95em;">Pilih
                                Periode</label>
                            <div class="input-group mb-2">
                                <input type="text" id="periodeInput" name="FilterTanggal" class="form-control daterange"
                                    style="font-size:0.96em; min-height:36px;" value="{{ request('FilterTanggal') }}"
                                    autocomplete="off" />
                                <span class="input-group-text" style="font-size: 1em;">
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end mt-1">
                            <button type="submit" class="btn btn-primary btn-sm" style="font-size: 0.96em;">
                                <i class="fa fa-filter"></i> Tampilkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row gx-3 mb-2">
        <!-- Total Shift -->
        <div class="col-12 col-md-6 col-lg-2-4 mb-2" style="flex:1 1 0; min-width: 0;">
            <div class="card shadow-sm border-0 text-center" style="background: #5c27fe; color: #fff; height: 100%;">
                <div class="card-body py-3 px-2 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width:44px; height:44px; background:#4821c8;">
                        <i class="ti ti-calendar fs-3"></i>
                    </div>
                    <div class="flex-grow-1 text-start">
                        <div class="fw-semibold" style="font-size:15px;">Total Shift</div>
                        <div style="font-size:13px; opacity:.8;">Shift</div>
                        <div class="fw-bold mt-1 mb-0" style="font-size:20px;">
                            {{ $data['OmsetSatuShift']['total_shift'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Omset 1 Shift -->
        <div class="col-12 col-md-6 col-lg-2-4 mb-2" style="flex:1 1 0; min-width: 0;">
            <div class="card shadow-sm border-0 text-center" style="background: #07c37f; color: #fff; height: 100%;">
                <div class="card-body py-3 px-2 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width:44px; height:44px; background:#05a46b;">
                        <i class="ti ti-currency-dollar fs-3"></i>
                    </div>
                    <div class="flex-grow-1 text-start">
                        <div class="fw-semibold" style="font-size:15px;">Total Omset / Shift</div>
                        <div style="font-size:13px; opacity:.8;">1 Shift</div>
                        <div class="fw-bold mt-1 mb-0" style="font-size:20px;">
                            {{ isset($data['OmsetSatuShift']['total_omset']) ? 'Rp ' . number_format($data['OmsetSatuShift']['total_omset'], 0, ',', '.') : 'Rp 0' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Insentif Diperoleh -->
        <div class="col-12 col-md-6 col-lg-2-4 mb-2" style="flex:1 1 0; min-width: 0;">
            <div class="card shadow-sm border-0 text-center" style="background: #2196f3; color: #fff; height: 100%;">
                <div class="card-body py-3 px-2 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width:44px; height:44px; background:#1565c0;">
                        <i class="ti ti-gift fs-3"></i>
                    </div>
                    <div class="flex-grow-1 text-start">
                        <div class="fw-semibold" style="font-size:15px;">Total Insentif Diperoleh</div>
                        <div style="font-size:13px; opacity:.8;">Insentif</div>
                        <div class="fw-bold mt-1 mb-0" style="font-size:20px;">
                            {{ isset($data['TotalInsentif']) ? 'Rp ' . number_format($data['TotalInsentif'], 0, ',', '.') : 'Rp 0' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Rata-Rata Omset / Shift -->
        <div class="col-12 col-md-6 col-lg-2-4 mb-2" style="flex:1 1 0; min-width: 0;">
            <div class="card shadow-sm border-0 text-center" style="background: #fcbb3d; color: #fff; height: 100%;">
                <div class="card-body py-3 px-2 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width:44px; height:44px; background:#df9e20;">
                        <i class="ti ti-bar-chart fs-3"></i>
                    </div>
                    <div class="flex-grow-1 text-start">
                        <div class="fw-semibold" style="font-size:15px;">Rata-rata Omset / Shift</div>
                        <div style="font-size:13px; opacity:.8;">Per Shift</div>
                        <div class="fw-bold mt-1 mb-0" style="font-size:20px;">
                            {{ isset($data['OmsetSatuShift']['total_omset']) &&
                            isset($data['OmsetSatuShift']['total_shift']) &&
                            $data['OmsetSatuShift']['total_shift'] > 0
                                ? 'Rp ' .
                                    number_format($data['OmsetSatuShift']['total_omset'] / $data['OmsetSatuShift']['total_shift'], 0, ',', '.')
                                : 'Rp 0' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Pasien Dilayani -->
        <div class="col-12 col-md-6 col-lg-2-4 mb-2" style="flex:1 1 0; min-width: 0;">
            <div class="card shadow-sm border-0 text-center" style="background: #ea4e8b; color: #fff; height: 100%;">
                <div class="card-body py-3 px-2 d-flex align-items-center">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3"
                        style="width:44px; height:44px; background:#b8336a;">
                        <i class="ti ti-users fs-3"></i>
                    </div>
                    <div class="flex-grow-1 text-start">
                        <div class="fw-semibold" style="font-size:15px;">Total Pasien Dilayani</div>
                        <div style="font-size:13px; opacity:.8;">Pasien</div>
                        <div class="fw-bold mt-1 mb-0" style="font-size:20px;">
                            {{ $data['totalPasienDilayani'] ?? 0 }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white"
                    style="background-color: rgba(88, 121, 243, 0.171) !important;">
                    <div class="d-flex align-items-star">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5" style="color: #3a037b;">1</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: #3a037b;">Shift dengan Total Biaya Klinik</h5>
                            <small style="color: #3a037b;">Mencapai minimal Rp 6.000.000</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @php
                        $totalShift = isset($data['ShiftTotalBiayaKlinik']) ? count($data['ShiftTotalBiayaKlinik']) : 0;
                        $totalInsentif = collect($data['ShiftTotalBiayaKlinik'] ?? [])->sum('Nominal');
                    @endphp
                    <div class="text-center mb-3">
                        <div class="fw-semibold mb-2" style="font-size: 16px; color: #3a037b;">
                            Total Insentif Shift
                        </div>
                        <div class="fw-bold" style="font-size:26px; color: #3a037b;">
                            Rp {{ number_format($totalInsentif, 0, ',', '.') }}
                        </div>

                    </div>

                    <div class="alert alert-primary d-flex align-items-start mt-2" role="alert"
                        style="background-color: #f5f6ff; border-color: #d9e0fc; border-radius: 12px;">
                        <div>
                            <div class="fw-semibold mb-2" style="font-size: 14px; color: #3a037b;">
                                Perhitungan Insentif
                            </div>
                            <ul class="mb-0 ps-3" style="font-size: 13px; color: #3a037b; line-height: 1.5;">
                                <li>Rp 100.000 untuk setiap kelipatan Rp 6.000.000</li>

                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>


        <!-- Shift dengan Total 8 Pasien Lama -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-start">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5">2</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: #166534;">Shift dengan Total 8 Pasien Lama</h5>
                            <small class="" style="color: #166534;">dalam 1 Shift</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold" style="color: #166534;">Tanggal</th>
                                    <th class="fw-semibold" style="color: #166534;">Jumlah Pasien Lama</th>
                                    <th class="fw-semibold" style="color: #166534;">Perawat</th>
                                    <th class="fw-semibold" style="color: #166534;">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                @if (isset($data['Shift8PasienLama']) && count($data['Shift8PasienLama']) > 0)
                                    @foreach ($data['Shift8PasienLama'] as $row)
                                        <tr>
                                            <td style="color: #166534;">
                                                {{ \Carbon\Carbon::parse($row['created_at'])->translatedFormat('d/m/Y') }}
                                            </td>

                                            <td style="color: #166534;">{{ $row['jumlah_pasien_lama'] }} Pasien</td>
                                            <td style="color: #166534;">{{ $row['perawat_nama'] }}</td>
                                            <td class="fw-semibold" style="color: #166534;">Rp 30.000</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center" style="color: #166534;">Tidak ada data
                                            shift dengan
                                            minimal 8 pasien lama.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    @php
                        // Akumulasi total shift dan total insentif dari data Shift8PasienLama
                        $totalShiftTercapai =
                            isset($data['Shift8PasienLama']) && is_iterable($data['Shift8PasienLama'])
                                ? count($data['Shift8PasienLama'])
                                : 0;
                        $insentifPerShift = 30000;
                        $totalInsentif = $totalShiftTercapai * $insentifPerShift;
                    @endphp
                    <div class="alert alert-primary d-flex justify-content-between align-items-start" role="alert"
                        style="background: #f5f6ff; border-color: #d9e0fc;">
                        <div>
                            <div class="fw-semibold mb-2" style="font-size: 15px; color: #166534;">Perhitungan Insentif
                            </div>
                            <ul class="mb-0 ps-3" style="font-size: 14px; color: #166534;">
                                <li>Rp {{ number_format($insentifPerShift, 0, ',', '.') }} untuk setiap shift<br> dengan
                                    total 8 pasien lama</li>
                            </ul>
                        </div>
                        <div class="text-end ms-4">
                            <div class="fw-semibold" style="font-size: 15px; color: #166534;">Total Shift Tercapai
                                <span class="fw-bold"
                                    style="font-size: 18px; color:#166534;">{{ $totalShiftTercapai }}</span>
                            </div>
                            <div class="mt-2" style="color: #166534;">Total Insentif</div>
                            <div class="fw-bold" style="font-size:18px; color: #166534;">
                                Rp {{ number_format($totalInsentif, 0, ',', '.') }}
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5" style="color:#e67e22;">3</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color:#e67e22;">Insentif Hari Libur</h5>
                            <small class="text-muted" style="color:#e67e22 !important;">Insentif untuk shift pada hari
                                libur nasional</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold" style="color:#e67e22;">Tanggal</th>
                                    <th class="fw-semibold" style="color:#e67e22;">Nominal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $insentifLibur = $data['InsentifHariLibur'] ?? [];
                                    // dd($insentifLibur);
                                    $totalInsentifLibur = 0;
                                @endphp
                                @if (is_iterable($insentifLibur) && count($insentifLibur) > 0)
                                    @foreach ($insentifLibur as $row)
                                        <tr>
                                            <td style="color:#e67e22;">
                                                {{ isset($row['created_at']) ? \Carbon\Carbon::parse($row['created_at'])->translatedFormat('d/m/Y') : '-' }}
                                            </td>

                                            <td class="fw-semibold" style="color:#e67e22;">
                                                {{ isset($row['Nominal']) ? 'Rp ' . number_format($row['Nominal'], 0, ',', '.') : '-' }}
                                                @php $totalInsentifLibur += $row['Nominal'] ?? 0; @endphp
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="text-center" style="color:#e67e22;">Tidak ada data
                                            insentif hari libur</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-warning d-flex justify-content-between align-items-center" role="alert"
                        style="background: #fff9ed; border-color: #fff3cd;">
                        <div class="fw-semibold" style="color:#e67e22;">Total Insentif Hari Libur</div>
                        <div class="fw-bold" style="color:#e67e22; font-size:18px;">
                            Rp {{ number_format($totalInsentifLibur, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow border-0"
                style="background: linear-gradient(90deg, #665be7 0%, #28c76f 100%); color: white;">
                <div class="card-body p-0">
                    <div class="p-3 pb-2"
                        style="border-radius: 12px 12px 0 0; background: linear-gradient(90deg, #5a58fc 0%, #5ad6ff 100%);">
                        <div class="d-flex align-items-center mb-1">
                            <i class="bx bx-sun fs-4 me-2"></i>
                            <span class="fw-semibold fs-6">Ringkasan Total Insentif</span>
                        </div>
                        <div class="text-white-50" style="font-size: 14px;">
                            Periode: (Harian)
                        </div>
                    </div>
                    <div class="px-4 py-3 bg-white" style="color: #222;">
                        <table class="w-100 align-middle" style="font-size:15px;">
                            <tbody>
                                @php
                                    $jenisRuleInfo = [
                                        'omzet_shift' => [
                                            'label' => 'Shift ≥ Rp 6.000.000 / 2 ≥ Rp 12.000.000',
                                            'badge' => 'bg-primary',
                                            'order' => 1,
                                        ],
                                        'pasien_lama' => [
                                            'label' => 'Shift dengan 8 Pasien Lama',
                                            'badge' => 'bg-info',
                                            'order' => 2,
                                        ],
                                        'insentif_hari_libur' => [
                                            // Tambahan Insentif Hari Libur
                                            'label' => 'Insentif Kehadiran Hari Libur',
                                            'badge' => 'bg-success',
                                            'order' => 3,
                                        ],
                                        'target_tercapai' => [
                                            'label' => 'Target klinik tercapai',
                                            'badge' => 'bg-success',
                                            'order' => 4,
                                        ],
                                        // 'tindakan' => [
                                        //     'label' => 'Perawatan Odontektomi',
                                        //     'badge' => 'bg-warning text-white',
                                        //     'order' => 5,
                                        // ],
                                        // 'pasien_baru' => [
                                        //     'label' => 'Pasien Baru',
                                        //     'badge' => 'bg-danger',
                                        //     'order' => 6,
                                        // ],
                                    ];
                                    $ringkasan = $data['Ringkasan'] ?? [];

                                    // Ambil mapping [JenisRule] => [nominal] untuk quick display
                                    $totalByJenisRule = [];
                                    foreach ($ringkasan as $item) {
                                        $totalByJenisRule[$item->JenisRule] = $item->total_insentif;
                                    }
                                    // Urutan final row output
                                    $rowOrder = array_keys($jenisRuleInfo);
                                @endphp

                                @foreach ($rowOrder as $i => $ruleKey)
                                    <tr>
                                        <td class="py-2 px-0" style="width:40px;">
                                            <span class="badge {{ $jenisRuleInfo[$ruleKey]['badge'] }} me-2"
                                                style="font-size: 1rem; width: 32px; height:32px;display: inline-flex; align-items: center; justify-content: center;">
                                                {{ $jenisRuleInfo[$ruleKey]['order'] }}
                                            </span>
                                        </td>
                                        <td>{{ $jenisRuleInfo[$ruleKey]['label'] }}</td>
                                        <td class="text-end fw-semibold" style="white-space:nowrap">
                                            @php
                                                $nominal = isset($totalByJenisRule[$ruleKey])
                                                    ? $totalByJenisRule[$ruleKey]
                                                    : null;
                                            @endphp
                                            {{ $nominal !== null ? 'Rp ' . number_format($nominal, 0, ',', '.') : 'Rp 0' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @php
                            // Hitung total insentif dari ringkasan (totalByJenisRule)
                            $totalInsentifHariIni = array_sum($totalByJenisRule);
                        @endphp
                        <div class="mt-4 rounded-3 py-3 text-center fw-bold"
                            style="background: linear-gradient(90deg,#665be7 0%,#28c76f 100%); font-size: 1.5rem;color:white;">
                            <div style="font-size:1rem;letter-spacing: 1px;" class="mb-1">
                                TOTAL INSENTIF HARI INI
                            </div>
                            Rp {{ number_format($totalInsentifHariIni, 0, ',', '.') }}
                        </div>

                        {{-- Penjelasan Insentif Hari Libur --}}
                        <div class="mt-3">
                            <div class="fw-semibold" style="font-size: 15px; color: #166534;">Keterangan Insentif Hari
                                Libur:</div>
                            <ul class="mb-0 ps-3" style="font-size: 14px; color: #166534;">
                                <li>Insentif kehadiran diberikan untuk setiap shift resepsionis di hari libur resmi (libur
                                    nasional).</li>
                                <li>Nominal dapat berubah sesuai kebijakan manajemen. Pastikan kehadiran direkap dengan
                                    benar.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">
        <div class="col-12">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-start rounded-3 px-3 py-3" style="background: #f6f7ff;">
                        <span class="me-3 mt-1" style="color: #655be7; font-size: 2rem;">
                            <i class="fa fa-info-circle"></i>
                        </span>
                        <div>
                            <div class="fw-bold" style="color:#655be7;">Catatan:</div>
                            <div class="text-muted">
                                Perhitungan insentif dilakukan otomatis berdasarkan data transaksi dan kehadiran shift
                                resepsionis.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-start rounded-3 px-3 py-3" style="background: #f6f7ff;">
                        <span class="me-3 mt-1" style="color: #655be7; font-size: 2rem;">
                            <i class="fa fa-clock"></i>
                        </span>
                        <div>
                            <div class="fw-bold" style="color:#655be7;">Informasi:</div>
                            <div class="text-muted">
                                Insentif akan dibayarkan setiap akhir bulan berdasarkan rekapitulasi harian.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="row">

    </div>
@endsection
@push('scripts')
    @if (session('fail_message'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                html: `{!! session('fail_message') !!}`,
                confirmButtonColor: '#665be7'
            });
        </script>
    @endif
@endpush
