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
    <div class="row mb-3 align-items-center">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <h5 class="mb-0 fw-semibold">Dashboard Report Insentif Perawat</h5>
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
                        <div class="col-md-6">
                            <label for="perawatSelect" class="form-label mb-1" style="font-size: 0.95em;">Pilih
                                Perawat</label>
                            <div class="input-group mb-2">
                                <select id="perawatSelect" name="perawat" class="select2 form-control"
                                    style="width:100%; font-size: 0.96em; min-height:36px;">
                                    <option value="">Pilih Perawat</option>
                                    @foreach ($perawat as $p)
                                        <option value="{{ $p->id }}"
                                            {{ request('perawat') == $p->id ? 'selected' : '' }}>{{ $p->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Pilih Periode -->
                        <div class="col-md-6">
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
    <div class="row gx-3 mb-4" style="overflow-x: auto; white-space: nowrap;">
        <!-- Total Shift -->
        <div class="col-auto" style="min-width: 220px;">
            <div class="card shadow-sm border-0 text-center" style="background: #5c27fe; color: #fff;">
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
        <div class="col-auto" style="min-width: 260px;">
            <div class="card shadow-sm border-0 text-center" style="background: #07c37f; color: #fff;">
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
        <div class="col-auto" style="min-width: 240px;">
            <div class="card shadow-sm border-0 text-center" style="background: #2196f3; color: #fff;">
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
        <div class="col-auto" style="min-width: 240px;">
            <div class="card shadow-sm border-0 text-center" style="background: #fcbb3d; color: #fff;">
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
        <div class="col-auto" style="min-width: 220px;">
            <div class="card shadow-sm border-0 text-center" style="background: #ea4e8b; color: #fff;">
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
                            <span class="fw-bold fs-5">1</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-primary">Shift dengan Total Biaya Klinik</h5>
                            <small class="text-muted">Mencapai minimal Rp 6.000.000</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold">No</th>
                                    <th class="fw-semibold">Tanggal</th>
                                    <th class="fw-semibold">Total Biaya</th>
                                    <th class="fw-semibold">Perawat</th>
                                    <th class="fw-semibold">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                @forelse($data['ShiftTotalBiayaKlinik'] ?? [] as $index => $shift)
                                    {{-- @dd($shift) --}}
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ \Carbon\Carbon::parse($shift->created_at)->format('d/m/Y') }}</td>
                                        <td>Rp {{ number_format($shift->getTransaksi->TotalBayar ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $shift->getUser->name ?? '-' }}</td>
                                        <td>Rp {{ number_format($shift->Nominal ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada data shift</td>
                                    </tr>
                                @endforelse


                            </tbody>
                        </table>

                    </div>
                    <div class="alert alert-primary d-flex justify-content-between align-items-start mt-2" role="alert"
                        style="background: #f5f6ff; border-color: #d9e0fc;">
                        <div>
                            <div class="fw-semibold text-primary mb-2" style="font-size: 15px;">Perhitungan Insentif</div>
                            <ul class="mb-0 ps-3" style="font-size: 14px; color: #343a40;">
                                <li>Rp 50.000 untuk setiap shift ≥ Rp 6.000.000</li>
                                <li>Rp 100.000 untuk setiap shift ≥ Rp 12.000.000</li>
                            </ul>
                        </div>
                        <div class="text-end ms-4">
                            <div class="fw-semibold text-primary" style="font-size: 15px;">Total Shift Tercapai</div>
                            <div class="fw-bold" style="font-size: 22px; color:#3a37aa;">3</div>
                            <div class="mt-2">Total Insentif</div>
                            <div class="fw-bold text-primary" style="font-size:18px;">Rp 200.000</div>
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
                            <h5 class="mb-0 fw-semibold text-success">Shift dengan Total 8 Pasien Lama</h5>
                            <small class="text-muted">dalam 1 Shift</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold">Tanggal</th>
                                    <th class="fw-semibold">Jumlah Pasien Lama</th>
                                    <th class="fw-semibold">Perawat</th>
                                    <th class="fw-semibold">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                @if (isset($data['Shift8PasienLama']) && count($data['Shift8PasienLama']) > 0)
                                    @foreach ($data['Shift8PasienLama'] as $row)
                                        <tr>
                                            <td>{{ $row['tanggal'] }}</td>
                                            <td>{{ $row['jumlah_pasien_lama'] }} Pasien</td>
                                            <td>{{ $row['perawat_nama'] }}</td>
                                            <td class="text-success fw-semibold">Rp 30.000</td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Tidak ada data shift dengan
                                            minimal 8 pasien lama.</td>
                                    </tr>
                                @endif


                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-primary d-flex justify-content-between align-items-start" role="alert"
                        style="background: #f5f6ff; border-color: #d9e0fc;">
                        <div>
                            <div class="fw-semibold text-primary mb-2" style="font-size: 15px;">Perhitungan Insentif</div>
                            <ul class="mb-0 ps-3" style="font-size: 14px; color: #343a40;">
                                <li>Rp 50.000 untuk setiap shift ≥ Rp 6.000.000</li>
                                <li>Rp 100.000 untuk setiap shift ≥ Rp 12.000.000</li>
                            </ul>
                        </div>
                        <div class="text-end ms-4">
                            <div class="fw-semibold text-primary" style="font-size: 15px;">Total Shift Tercapai</div>
                            <div class="fw-bold" style="font-size: 22px; color:#3a37aa;">3</div>
                            <div class="mt-2">Total Insentif</div>
                            <div class="fw-bold text-primary" style="font-size:18px;">Rp 200.000</div>
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
                            <span class="fw-bold fs-5">3</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-info">Pasien dengan Billing Minimal</h5>
                            <small class="text-muted">Rp 1.000.000 per Transaksi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold">No</th>
                                    <th class="fw-semibold">Tanggal</th>
                                    <th class="fw-semibold">Nama Pasien</th>
                                    <th class="fw-semibold">Total Billing</th>
                                    <th class="fw-semibold">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                @foreach ($data['pasienBillingMinimal'] ?? [] as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            {{ isset($item->tanggal) ? \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') : '-' }}
                                        </td>
                                        <td>{{ $item->nama_pasien ?? '-' }}</td>
                                        <td>Rp
                                            {{ isset($item->total_billing) ? number_format($item->total_billing, 0, ',', '.') : '0' }}
                                        </td>
                                        <td class="text-info fw-semibold">
                                            Rp
                                            {{ isset($item->insentif) ? number_format($item->insentif, 0, ',', '.') : '0' }}
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-primary d-flex justify-content-between align-items-start" role="alert"
                        style="background: #f5f6ff; border-color: #d9e0fc;">
                        <div>
                            <div class="fw-semibold text-primary mb-2" style="font-size: 15px;">Perhitungan Insentif</div>
                            <ul class="mb-0 ps-3" style="font-size: 14px; color: #343a40;">
                                <li>Rp 50.000 untuk setiap shift ≥ Rp 6.000.000</li>
                                <li>Rp 100.000 untuk setiap shift ≥ Rp 12.000.000</li>
                            </ul>
                        </div>
                        <div class="text-end ms-4">
                            <div class="fw-semibold text-primary" style="font-size: 15px;">Total Shift Tercapai</div>
                            <div class="fw-bold" style="font-size: 22px; color:#3a37aa;">3</div>
                            <div class="mt-2">Total Insentif</div>
                            <div class="fw-bold text-primary" style="font-size:18px;">Rp 200.000</div>
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
                            <span class="fw-bold fs-5" style="color: orange;">4</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: orange;">Pasien dengan Jenis Perawatan Odontektomi
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
                                    <th class="fw-semibold" style="color: orange;">No</th>
                                    <th class="fw-semibold" style="color: orange;">Tanggal</th>
                                    <th class="fw-semibold" style="color: orange;">Nama Pasien</th>
                                    <th class="fw-semibold" style="color: orange;">Perawat</th>
                                    <th class="fw-semibold" style="color: orange;">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                <tr>
                                    <td style="color: orange;">1</td>
                                    <td style="color: orange;">21 Mei 2025</td>
                                    <td style="color: orange;">Budi Santoso</td>
                                    <td style="color: orange;">Rp 1.250.000</td>
                                    <td class="fw-semibold" style="color: orange;">Rp 10.000</td>
                                </tr>
                                <tr>
                                    <td style="color: orange;">2</td>
                                    <td style="color: orange;">21 Mei 2025</td>
                                    <td style="color: orange;">Dewi Lestari</td>
                                    <td style="color: orange;">Rp 1.800.000</td>
                                    <td class="fw-semibold" style="color: orange;">Rp 10.000</td>
                                </tr>
                                <tr>
                                    <td style="color: orange;">3</td>
                                    <td style="color: orange;">21 Mei 2025</td>
                                    <td style="color: orange;">Fikri Ramadhan</td>
                                    <td style="color: orange;">Rp 2.000.000</td>
                                    <td class="fw-semibold" style="color: orange;">Rp 10.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-primary d-flex justify-content-between align-items-start" role="alert"
                        style="background: #f5f6ff; border-color: #d9e0fc;">
                        <div>
                            <div class="fw-semibold mb-2" style="font-size: 15px; color: orange;">Perhitungan Insentif
                            </div>
                            <ul class="mb-0 ps-3" style="font-size: 14px; color: orange;">
                                <li>Rp 50.000 untuk setiap shift ≥ Rp 6.000.000</li>
                                <li>Rp 100.000 untuk setiap shift ≥ Rp 12.000.000</li>
                            </ul>
                        </div>
                        <div class="text-end ms-4">
                            <div class="fw-semibold" style="font-size: 15px; color: orange;">Total Shift Tercapai</div>
                            <div class="fw-bold" style="font-size: 22px; color: orange;">3</div>
                            <div class="mt-2" style="color: orange;">Total Insentif</div>
                            <div class="fw-bold" style="font-size:18px; color: orange;">Rp 200.000</div>
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
                            <span class="fw-bold fs-5" style="color: orange;">5</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold" style="color: orange;">Pasien Baru
                            </h5>
                            <small class="text-muted">Berdasarkan Tanggal Kedatangan</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold" style="color: orange;">Tanggal</th>
                                    <th class="fw-semibold" style="color: orange;">Jumlah Pasien baru</th>
                                    <th class="fw-semibold" style="color: orange;">Perawat</th>
                                    <th class="fw-semibold" style="color: orange;">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                @forelse ($data['PasienBaru'] as $index => $pasien)
                                    <tr>
                                        <td style="color: orange;">{{ $pasien['tanggal'] }}</td>
                                        <td style="color: orange;">{{ $pasien['jumlah_pasien_baru'] }}</td>
                                        <td style="color: orange;">{{ $pasien['perawat_nama'] ?? '-' }}</td>
                                        <td style="color: orange;">{{ $pasien['insentif'] }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center" style="color:orange;">Tidak ada data</td>
                                    </tr>
                                @endforelse


                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-primary d-flex justify-content-between align-items-start" role="alert"
                        style="background: #f5f6ff; border-color: #d9e0fc;">
                        <div>
                            <div class="fw-semibold mb-2" style="font-size: 15px; color: orange;">Ringkasan Total Insentif
                            </div>
                            <ul class="mb-0 ps-3" style="font-size: 14px; color: orange;">
                                <li>Rp 50.000 untuk setiap shift ≥ Rp 6.000.000</li>
                                <li>Rp 100.000 untuk setiap shift ≥ Rp 12.000.000</li>
                            </ul>
                        </div>
                        <div class="text-end ms-4">
                            <div class="fw-semibold" style="font-size: 15px; color: orange;">Total Shift Tercapai</div>
                            <div class="fw-bold" style="font-size: 22px; color: orange;">3</div>
                            <div class="mt-2" style="color: orange;">Total Insentif</div>
                            <div class="fw-bold" style="font-size:18px; color: orange;">Rp 200.000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5"></span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-info">Pasien dengan Billing Minimal</h5>
                            <small class="text-muted">Rp 1.000.000 per Transaksi</small>
                        </div>
                    </div>
                </div>
                <div class="card-body pb-2">

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold">No</th>
                                    <th class="fw-semibold">Tanggal</th>
                                    <th class="fw-semibold">Nama Pasien</th>
                                    <th class="fw-semibold">Total Billing</th>
                                    <th class="fw-semibold">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                <tr>
                                    <td>1</td>
                                    <td>21 Mei 2025</td>
                                    <td>Budi Santoso</td>
                                    <td>Rp 1.250.000</td>
                                    <td class="text-info fw-semibold">Rp 10.000</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>21 Mei 2025</td>
                                    <td>Dewi Lestari</td>
                                    <td>Rp 1.800.000</td>
                                    <td class="text-info fw-semibold">Rp 10.000</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>21 Mei 2025</td>
                                    <td>Fikri Ramadhan</td>
                                    <td>Rp 2.000.000</td>
                                    <td class="text-info fw-semibold">Rp 10.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-primary d-flex justify-content-between align-items-start" role="alert"
                        style="background: #f5f6ff; border-color: #d9e0fc;">
                        <div>
                            <div class="fw-semibold text-primary mb-2" style="font-size: 15px;">Perhitungan Insentif</div>
                            <ul class="mb-0 ps-3" style="font-size: 14px; color: #343a40;">
                                <li>Rp 50.000 untuk setiap shift ≥ Rp 6.000.000</li>
                                <li>Rp 100.000 untuk setiap shift ≥ Rp 12.000.000</li>
                            </ul>
                        </div>
                        <div class="text-end ms-4">
                            <div class="fw-semibold text-primary" style="font-size: 15px;">Total Shift Tercapai</div>
                            <div class="fw-bold" style="font-size: 22px; color:#3a37aa;">3</div>
                            <div class="mt-2">Total Insentif</div>
                            <div class="fw-bold text-primary" style="font-size:18px;">Rp 200.000</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
