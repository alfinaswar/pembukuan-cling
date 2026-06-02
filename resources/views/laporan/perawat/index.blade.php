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
            <h3 class="mb-0 fw-semibold">Dashboard Report Insentif Perawat</h3>
            <small class="text-muted">Ringkasan pencapaian dan perhitungan insentif berdasarkan performa</small>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 d-flex justify-content-end align-items-center">
            <div class="input-group me-2" style="max-width: 220px;">
                <input type="text" class="form-control" placeholder="2024-06-04" id="mdate" />
                <span class="input-group-text">
                    <i class="ti ti-calendar fs-5"></i>
                </span>
            </div>
            <a href="" class="btn btn-success" style="background-color: green;">
                <i class="ti ti-file-export"></i> Export
            </a>
        </div>

    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body pb-2">
                    <form id="perawatFilterForm" class="row align-items-end g-2" method="POST"
                        action="{{ route('laporan-perawat.store') }}" style="font-size: 0.925rem;">

                        @csrf
                        @php
                            $user = Auth::user();
                            $perawat_id = old('perawat', request('perawat', $user->id ?? ''));
                            $isSuperadminOrManagement =
                                $user && ($user->hasRole('Superadmin') || $user->hasRole('Management'));
                            $isDisabled = !$isSuperadminOrManagement;
                        @endphp

                        <div class="col-md-4">
                            <label for="perawatSelect" class="form-label mb-1" style="font-size: 0.95em;">Pilih
                                Perawat</label>

                            {{-- Tambahkan class 'select2-readonly-wrapper' jika user bukan Superadmin/Management --}}
                            <div class="input-group mb-2 {{ $isDisabled ? 'select2-readonly-wrapper' : '' }}">
                                <select id="perawatSelect" name="perawat" class="select2 form-control"
                                    style="width:100%; font-size: 0.96em; min-height:36px;"
                                    {{ $isDisabled ? 'tabindex="-1" aria-disabled="true"' : '' }}>
                                    <option value="">Pilih Perawat</option>
                                    @foreach ($perawat as $p)
                                        <option value="{{ $p->id }}"
                                            @if (!$isDisabled) {{-- Jika Superadmin/Management, pilih berdasarkan request atau user id --}}
                        {{ ($user && $user->id == $p->id) || request('perawat') == $p->id ? 'selected' : '' }}
                    @else
                        {{-- Jika bukan, paksa pilih berdasarkan user id (agar tidak bisa diubah) --}}
                        {{ $user && $user->id == $p->id ? 'selected' : '' }} @endif>
                                            {{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div <!-- Pilih Shift -->
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
                                    style="font-size:0.96em; min-height:36px;"
                                    value="{{ old('FilterTanggal', request('FilterTanggal')) }}" autocomplete="off" />
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
                                <li>Rp 50.000 untuk setiap kelipatan Rp 6.000.000</li>

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


        <!-- Pasien dengan Billing Minimal -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm ">
                <div class="card-header ">
                    <div class="d-flex align-items-start">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5" style="color: #162878;">3</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: #162878;">Pasien dengan Billing Minimal</h5>
                            <small style="color: #162878;">Rp 1.000.000 per Transaksi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold" style="color: #162878;">No</th>
                                    <th class="fw-semibold" style="color: #162878;">Tanggal</th>
                                    <th class="fw-semibold" style="color: #162878;">Nama Pasien</th>
                                    <th class="fw-semibold" style="color: #162878;">Total Billing</th>
                                    <th class="fw-semibold" style="color: #162878;">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                @php
                                    // Eloquent Collection: use ->take(5) instead of array_slice
                                    $pasienBillingMinimal = ($data['pasienBillingMinimal'] ?? collect())->take(5);
                                @endphp
                                @forelse ($pasienBillingMinimal as $index => $item)
                                    <tr>
                                        <td style="color: #162878;">{{ $index + 1 }}</td>
                                        <td style="color: #162878;">
                                            {{ isset($item->created_at) ? \Carbon\Carbon::parse($item->created_at)->translatedFormat('d/m/Y') : '-' }}
                                        </td>
                                        <td style="color: #162878;">{{ $item->getTransaksi->NamaPasien ?? '-' }}</td>
                                        <td style="color: #162878;">
                                            Rp
                                            {{ isset($item->getTransaksi->TotalBayar) ? number_format($item->getTransaksi->TotalBayar, 0, ',', '.') : '0' }}
                                        </td>
                                        <td class="fw-semibold" style="color: #162878;">
                                            Rp
                                            {{ isset($item->Nominal) ? number_format($item->Nominal, 0, ',', '.') : '0' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center" style="color: #162878;">Tidak ada data
                                            pasien dengan
                                            billing minimal.</td>
                                    </tr>
                                @endforelse


                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-primary d-flex justify-content-between align-items-center" role="alert"
                        style="background: #f5f6ff; border-color: #d9e0fc; min-height: 90px;">
                        <div class="d-flex align-items-center" style="flex: 1;">
                            <div>
                                <div class="fw-semibold mb-1" style="font-size: 14px; color: #162878;">Perhitungan
                                    Insentif</div>
                                <div style="font-size: 13px; color: #376ede;">
                                    Rp 10.000 untuk setiap pasien dengan<br>
                                    billing minimal Rp 1.000.000
                                </div>
                            </div>
                            <div class="mx-4 flex-shrink-0 d-none d-md-block">
                                <i class="fa fa-arrow-up" style="color:#aac6fd; font-size: 35px;"></i>
                            </div>
                        </div>
                        <div class="text-end ms-2" style="min-width:130px;">
                            <div style="font-size: 13px; color:#376ede;">Total Pasien</div>
                            <div class="fw-bold" style="font-size: 17px; color:#376ede;">
                                {{ count($data['pasienBillingMinimal'] ?? []) }}
                            </div>
                            <div class="mt-1" style="font-size:13px; color: #376ede;">Total Insentif</div>
                            <div class="fw-bold" style="font-size:18px; color: #376ede;">
                                Rp {{ number_format(count($data['pasienBillingMinimal'] ?? []) * 10000, 0, ',', '.') }}
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
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5" style="color: #FF8000;">4</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: #FF8000;">Pasien dengan Jenis Perawatan Odontektomi
                            </h5>
                            {{-- <small class="text-muted">Rp 1.000.000 per Transaksi</small> --}}
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold" style="color: #FF8000;">No</th>
                                    <th class="fw-semibold" style="color: #FF8000;">Tanggal</th>
                                    <th class="fw-semibold" style="color: #FF8000;">Nama</th>
                                    <th class="fw-semibold" style="color: #FF8000;">Perawat</th>
                                    <th class="fw-semibold" style="color: #FF8000;">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                @php
                                    $no = 1;
                                    $totalOdontektomiPasien = 0;
                                    $totalOdontektomiInsentif = 0;
                                @endphp
                                @foreach ($data['Odontektomi'] ?? [] as $item)
                                    <tr>
                                        <td style="color: #FF8000;">{{ $no }}</td>
                                        <td style="color: #FF8000;">
                                            {{ $item->created_at ? \Carbon\Carbon::parse($item->getTransaksi->Tanggal)->format('d/m/Y') : '-' }}
                                        </td>
                                        <td style="color: #FF8000;">
                                            {{ $item->getTransaksi && isset($item->getTransaksi->NamaPasien) ? $item->getTransaksi->NamaPasien : '-' }}
                                        </td>
                                        <td style="color: #FF8000;">
                                            {{ $item->getTransaksi && isset($item->getUser->name) ? $item->getUser->name : '-' }}
                                        </td>

                                        <td class="fw-semibold" style="color: #FF8000;">
                                            {{ $item->Nominal ? 'Rp ' . number_format($item->Nominal, 0, ',', '.') : '-' }}
                                        </td>
                                    </tr>
                                    @php
                                        $no++;
                                        $totalOdontektomiPasien++;
                                        $totalOdontektomiInsentif += $item->Nominal ?? 0;
                                    @endphp
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-primary d-flex justify-content-between align-items-start mt-2" role="alert"
                        style="background: #fff8f1; border-color: #ffe9cb;">
                        <div>
                            <div class="fw-semibold mb-2" style="color: #ff9900;">Perhitungan Insentif</div>
                            <ul class="mb-0 ps-3" style="color: #ff9900; list-style: disc;">
                                <li style="margin-bottom: 0;">
                                    Rp 25.000 untuk setiap pasien<br>
                                    dengan perawatan Odontektomi
                                </li>
                            </ul>
                        </div>
                        <div class="text-end ms-4">
                            @php
                                // Hitung dari atas, bukan dari variabel di bawah tabel
                                $totalOdontektomiPasien = isset($data['Odontektomi']) ? count($data['Odontektomi']) : 0;
                                $totalOdontektomiInsentif = 0;
                                if (isset($data['Odontektomi']) && is_iterable($data['Odontektomi'])) {
                                    foreach ($data['Odontektomi'] as $o) {
                                        $totalOdontektomiInsentif += $o->Nominal ?? 0;
                                    }
                                }
                            @endphp
                            <div class="fw-semibold" style="font-size: 15px; color: #ff9900;">
                                Total Pasien
                                <span class="fw-bold"
                                    style="font-size: 20px; color: #ff9900; margin-left: 8px;">{{ $totalOdontektomiPasien }}</span>
                            </div>
                            <div class="mt-2 fw-semibold" style="color: #ff9900;">Total Insentif</div>
                            <div class="fw-bold" style="font-size: 20px; color: #ff9900;">Rp
                                {{ number_format($totalOdontektomiInsentif, 0, ',', '.') }}</div>
                        </div>
                    </div>



                </div>
            </div>
        </div>


        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5" style="color:#FF2AA0;">5</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color:#FF2AA0;">Pasien Baru
                            </h5>
                            <small class="text-muted" style="color:#FF2AA0 !important;">Berdasarkan Tanggal
                                Kedatangan</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold" style="color:#FF2AA0;">Tanggal</th>
                                    <th class="fw-semibold" style="color:#FF2AA0;">Jumlah</th>
                                    <th class="fw-semibold" style="color:#FF2AA0;">Perawat</th>
                                    <th class="fw-semibold" style="color:#FF2AA0;">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                @if (isset($data['PasienBaru']) && is_iterable($data['PasienBaru']) && count($data['PasienBaru']) > 0)
                                    @foreach (collect($data['PasienBaru'])->take(5) as $index => $pasien)
                                        <tr>
                                            <td style="color:#FF2AA0;">
                                                {{ isset($pasien['tanggal']) ? \Carbon\Carbon::parse($pasien['tanggal'])->translatedFormat('d/m/Y') : '-' }}

                                            </td>

                                            <td style="color:#FF2AA0;">{{ $pasien['jumlah'] ?? '-' }}</td>
                                            <td style="color:#FF2AA0;">{{ $pasien['perawat'] ?? '-' }}</td>
                                            <td style="color:#FF2AA0;">
                                                {{ isset($pasien['insentif']) ? 'Rp ' . number_format($pasien['insentif'], 0, ',', '.') : '-' }}
                                            </td>

                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center" style="color:#FF2AA0;">Tidak ada data</td>
                                    </tr>
                                @endif


                            </tbody>
                        </table>
                    </div>
                    @php
                        // Mengakumulasikan total pasien baru dari array PasienBaru
                        $totalPasienBaru = 0;
                        $totalInsentifPasienBaru = 0;
                        if (isset($data['PasienBaru']) && is_iterable($data['PasienBaru'])) {
                            foreach ($data['PasienBaru'] as $item) {
                                $jumlah = $item['jumlah'] ?? ($item['jumlah_pasien_baru'] ?? 0);
                                $insentif = $item['insentif'] ?? $jumlah * 2000;
                                $totalPasienBaru += $jumlah;
                                $totalInsentifPasienBaru += $insentif;
                            }
                        }
                    @endphp
                    <div class="alert alert-primary d-flex justify-content-between align-items-center" role="alert"
                        style="background: #fdf7fd; border-color: #f2d7ef;">
                        <div class="d-flex flex-column flex-grow-1" style="max-width: 60%;">
                            <div class="fw-semibold mb-2" style="color:#b80080;">Perhitungan Insentif</div>
                            <ul class="mb-0 ps-3" style="color:#b80080; font-size: 15px;">
                                <li>Rp 2.000 untuk setiap pasien baru</li>
                            </ul>
                        </div>
                        <div style="border-left: 2px solid #eed1ec; height: 48px; margin: 0 22px 0 22px;"></div>
                        <div class="text-end ms-2" style="min-width: 160px;">
                            <div class="mb-1" style="color:#b80080; font-size:15px;">Total Pasien Baru <span
                                    class="fw-bold" style="font-size:18px;">{{ $totalPasienBaru }}</span></div>
                            <div style="color:#b80080; font-size: 14px;">Total Insentif</div>
                            <div class="fw-bold" style="color:#b80080; font-size:18px;">
                                Rp {{ number_format($totalInsentifPasienBaru, 0, ',', '.') }}
                            </div>
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
                                    // Ambil Ringkasan dari $data sesuai hasil dari controller (sudah terurut dan lengkap properti)
                                    $ringkasan = $data['Ringkasan'] ?? [];
                                @endphp

                                @foreach ($ringkasan as $item)
                                    <tr>
                                        <td class="py-2 px-0" style="width:40px;">
                                            <span class="badge {{ $item->badge }} me-2"
                                                style="font-size: 1rem; width: 32px; height:32px;display: inline-flex; align-items: center; justify-content: center;">
                                                {{ $item->order }}
                                            </span>
                                        </td>
                                        <td>{{ $item->label }}</td>
                                        <td class="text-end fw-semibold" style="white-space:nowrap">
                                            {{ 'Rp ' . number_format($item->total_insentif ?? 0, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>


                        @php
                            // Hitung total insentif dari ringkasan (totalByJenisRule)
                            $totalInsentifHariIni = 0;
                        @endphp
                        <div class="mt-4 rounded-3 py-3 text-center fw-bold"
                            style="background: linear-gradient(90deg,#665be7 0%,#28c76f 100%); font-size: 1.5rem;color:white;">
                            <div style="font-size:1rem;letter-spacing: 1px;" class="mb-1">
                                TOTAL INSENTIF HARI INI
                            </div>
                            Rp
                            {{ isset($data['TotalInsentif']) ? 'Rp ' . number_format($data['TotalInsentif'], 0, ',', '.') : 'Rp 0' }}
                        </div>

                    </div>
                </div>
            </div>
        </div>

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
