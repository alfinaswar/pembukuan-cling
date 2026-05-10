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
                <div class="card-body">
                    <form class="row align-items-end">
                        <div class="col-md-6">
                            <label for="perawatSelect" class="form-label">Pilih Perawat</label>
                            <select id="perawatSelect" name="perawat" class="select2 form-select">
                                <option value="">-- Semua Perawat --</option>
                                @foreach ($perawat as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <!-- Pilih Periode -->
                        <div class="col-md-6">
                            <label for="periodeInput" class="form-label">Pilih Periode</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control daterange" />

                                <span class="input-group-text">
                                    <i class="ti ti-calendar fs-5"></i>
                                </span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <!-- Total Shift -->
        <div class="col-md-2">
            <div class="card dashboard-card-bg-primary border-0">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div
                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-primary">
                            <i class="ti ti-clock-hour-4 fs-3"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                            <h4 class="mb-0 fs-3">Total Shift</h4>
                            <span>Shift</span>
                        </div>
                        <div class="ms-auto align-self-center">
                            <h2 class="fs-3 mb-0">
                                {{ $summary['total_shift'] ?? 0 }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Omset 1 Shift -->
        <div class="col-md-2">
            <div class="card dashboard-card-bg-info border-0">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div
                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-info">
                            <i class="ti ti-cash fs-3"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                            <h4 class="mb-0 fs-3">Total Omset/Shift</h4>
                            <span>1 Shift</span>
                        </div>
                        <div class="ms-auto align-self-center">
                            <h2 class="fs-3 mb-0">
                                {{ isset($summary['total_omset_per_shift']) ? 'Rp ' . number_format($summary['total_omset_per_shift'], 0, ',', '.') : 'Rp 0' }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Insentif Diperoleh -->
        <div class="col-md-2">
            <div class="card dashboard-card-bg-success border-0">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div
                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-success">
                            <i class="ti ti-gift fs-3"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                            <h4 class="mb-0 fs-3">Insentif Diperoleh</h4>
                            <span>Insentif</span>
                        </div>
                        <div class="ms-auto align-self-center">
                            <h2 class="fs-3 mb-0">
                                {{ isset($summary['total_insentif']) ? 'Rp ' . number_format($summary['total_insentif'], 0, ',', '.') : 'Rp 0' }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Rata-Rata Omset / Shift -->
        <div class="col-md-2">
            <div class="card dashboard-card-bg-warning border-0">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div
                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-warning">
                            <i class="ti ti-chart-bar fs-3"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                            <h4 class="mb-0 fs-3">Rata-Rata Omset</h4>
                            <span>Per Shift</span>
                        </div>
                        <div class="ms-auto align-self-center">
                            <h2 class="fs-3 mb-0">
                                {{ isset($summary['rata_rata_omset_per_shift']) ? 'Rp ' . number_format($summary['rata_rata_omset_per_shift'], 0, ',', '.') : 'Rp 0' }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Pasien Dilayani -->
        <div class="col-md-2">
            <div class="card dashboard-card-bg-danger border-0">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div
                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-danger">
                            <i class="ti ti-users fs-3"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                            <h4 class="mb-0 fs-3">Pasien Dilayani</h4>
                            <span>Total Pasien</span>
                        </div>
                        <div class="ms-auto align-self-center">
                            <h2 class="fs-3 mb-0">
                                {{ $summary['total_pasien'] ?? 0 }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Total Pasien Baru -->
        <div class="col-md-2">
            <div class="card dashboard-card-bg-secondary border-0">
                <div class="card-body">
                    <div class="d-flex flex-row">
                        <div
                            class="round-40 rounded-circle text-white d-flex align-items-center justify-content-center text-bg-secondary">
                            <i class="ti ti-user-plus fs-3"></i>
                        </div>
                        <div class="ms-3 align-self-center">
                            <h4 class="mb-0 fs-3">Pasien Baru</h4>
                            <span>New Pasien</span>
                        </div>
                        <div class="ms-auto align-self-center">
                            <h2 class="fs-3 mb-0">
                                {{ $summary['total_pasien_baru'] ?? 0 }}
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body pb-2">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5">1</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-primary">Shift dengan Total Biaya Klinik</h5>
                            <small class="text-muted">Mencapai minimal Rp 6.000.000</small>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="fw-semibold">No</th>
                                    <th class="fw-semibold">Tanggal</th>
                                    <th class="fw-semibold">Total Biaya (1 Shift)</th>
                                    <th class="fw-semibold">Perawat</th>
                                    <th class="fw-semibold">Insentif</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data dummy untuk tabel --}}
                                <tr>
                                    <td>1</td>
                                    <td>04/06/2024</td>
                                    <td>Rp 6.500.000</td>
                                    <td>Ayu Pratiwi</td>
                                    <td>Rp 200.000</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>03/06/2024</td>
                                    <td>Rp 6.100.000</td>
                                    <td>Rina Astuti</td>
                                    <td>Rp 190.000</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>02/06/2024</td>
                                    <td>Rp 6.800.000</td>
                                    <td>Budi Santoso</td>
                                    <td>Rp 210.000</td>
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

        <!-- Shift dengan Total 8 Pasien Lama -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body pb-2">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5">2</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-success">Shift dengan Total 8 Pasien Lama</h5>
                            <small class="text-muted">dalam 1 Shift</small>
                        </div>
                    </div>
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
                                <tr>
                                    <td>21 Mei 2025</td>
                                    <td>8 Pasien</td>
                                    <td>Siti Rahma</td>
                                    <td class="text-success fw-semibold">Rp 30.000</td>
                                </tr>
                                <tr>
                                    <td>20 Mei 2025</td>
                                    <td>8 Pasien</td>
                                    <td>Dewi Anggraini</td>
                                    <td class="text-success fw-semibold">Rp 30.000</td>
                                </tr>
                                <tr>
                                    <td>19 Mei 2025</td>
                                    <td>8 Pasien</td>
                                    <td>Ahmad Fauzi</td>
                                    <td class="text-success fw-semibold">Rp 30.000</td>
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

        <!-- Pasien dengan Billing Minimal -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body pb-2">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5">3</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-info">Pasien dengan Billing Minimal</h5>
                            <small class="text-muted">Rp 1.000.000 per Transaksi</small>
                        </div>
                    </div>
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body pb-2">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5">3</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-info">Pasien dengan Billing Minimal</h5>
                            <small class="text-muted">Rp 1.000.000 per Transaksi</small>
                        </div>
                    </div>
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body pb-2">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5">3</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-info">Pasien dengan Billing Minimal</h5>
                            <small class="text-muted">Rp 1.000.000 per Transaksi</small>
                        </div>
                    </div>
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
        <div class="col-md-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body pb-2">
                    <div class="d-flex align-items-start mb-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white me-3"
                            style="width:32px; height:32px;">
                            <span class="fw-bold fs-5">3</span>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-semibold text-info">Pasien dengan Billing Minimal</h5>
                            <small class="text-muted">Rp 1.000.000 per Transaksi</small>
                        </div>
                    </div>
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
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between mb-3">

            </div>
        </div>
        <div class="datatables">
            <div class="card">
                <div class="card-header bg-teal-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-file-text me-2"></i>
                        Daftar Transaksi Pasien Terbaru
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tabel-transaksi-terbaru"
                            class="table table-striped table-bordered text-nowrap align-middle dataTable display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Perawatan</th>
                                    <th>Total Biaya</th>
                                    <th>Cara Bayar</th>
                                    <th>Tanggal</th>
                                    <th>Cabang</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transaksiTerbaru as $i => $tr)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>{{ $tr->NamaPasien ?? '-' }}</td>
                                        <td>
                                            @if (!empty($tr->TransaksiDetail) && count($tr->TransaksiDetail) > 0)
                                                <ul class="list-unstyled mb-0">
                                                    @foreach ($tr->TransaksiDetail as $td)
                                                        <li>
                                                            {{ $td->MasterJenisPerawatan->Nama ?? '-' }}:
                                                            <span class="text-secondary">
                                                                {{ 'Rp ' . number_format($td->Biaya ?? 0, 0, ',', '.') }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ 'Rp ' . number_format($tr->TotalBayar ?? 0, 0, ',', '.') }}</td>
                                        <td>{{ $tr->getMetodePembayaran->Nama ?? ($tr->MetodePembayaran ?? '-') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($tr->created_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>{{ $tr->getCabang->Nama ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">Data tidak tersedia.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection
