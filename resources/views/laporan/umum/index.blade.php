@extends('layouts.app')

@section('content')
    <div class="row mb-3 align-items-center">
        <div class="col-lg-4 col-md-4 col-sm-12">
            <h5 class="mb-0 fw-semibold">General Report</h5><small class="text-muted">Ciling Dental Clinic</small>
        </div>
        <div class="col-lg-8 col-md-8 col-sm-12 d-flex justify-content-end align-items-center mt-2 mt-md-0 gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group btn-group-sm" role="group" aria-label="Quick Filters"><button type="button"
                        class="btn btn-outline-secondary">Hari Ini</button><button type="button"
                        class="btn btn-outline-secondary">7 Hari</button><button type="button"
                        class="btn btn-outline-secondary">30 Hari</button><button type="button"
                        class="btn btn-primary">Bulanan</button></div>
                <button type="button" class="btn btn-success btn-sm ms-1"><i class="ti ti-file-spreadsheet"></i> Export
                    Excel</button>
            </div>

        </div>
    </div>

    <div class="row">
        <!-- Total Biaya -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <p class="card-subtitle text-uppercase text-purple">Total Biaya</p>
                            <h3 class="fs-6">
                                {{-- Ganti dengan variabel dinamis jika ada, contoh: --}}
                                Rp 0
                            </h3>
                            <small class="text-muted">
                                {{-- Perubahan persentase dibanding bulan kemarin --}}
                                <span class="me-1">
                                    <!--
                                                                                                                                                                                                                            Ganti berikut ini dengan variabel dinamis,
                                                                                                                                                                                                                            contoh: ($persenPerubahan > 0 ? '+' : '') . $persenPerubahan . '%'
                                                                                                                                                                                                                        -->
                                    <span class="text-success">+0%</span>
                                </span>
                                dibanding bulan kemarin
                            </small>
                        </div>
                        <div class="ms-auto">
                            <span class="text-primary display-6">
                                <i class="ti ti-currency-dollar"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress text-bg-light">
                        <div class="progress-bar text-bg-primary" role="progressbar" style="width: 100%; height: 6px;"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Jumlah Total Pasien -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <p class="card-subtitle text-uppercase text-success">Jumlah Total Pasien</p>
                            <h3 class="fs-6">
                                {{-- Ganti dengan variabel dinamis jika ada, contoh: --}}
                                0
                            </h3>
                            <small class="text-muted">
                                {{-- Perubahan persentase dibanding bulan kemarin --}}
                                <span class="me-1">
                                    <span class="text-success">+0%</span>
                                </span>
                                dibanding bulan kemarin
                            </small>
                        </div>
                        <div class="ms-auto">
                            <span class="text-success display-6">
                                <i class="ti ti-users"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress text-bg-light">
                        <div class="progress-bar text-bg-success" role="progressbar" style="width: 100%; height: 6px;"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pasien Baru -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <p class="card-subtitle text-uppercase text-info">Pasien Baru</p>
                            <h3 class="fs-6">
                                {{-- Ganti dengan variabel dinamis jika ada, contoh: --}}
                                0
                            </h3>
                            <small class="text-muted">
                                {{-- Perubahan persentase dibanding bulan kemarin --}}
                                <span class="me-1">
                                    <span class="text-info">+0%</span>
                                </span>
                                dibanding bulan kemarin
                            </small>
                        </div>
                        <div class="ms-auto">
                            <span class="text-info display-6">
                                <i class="ti ti-user-plus"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress text-bg-light">
                        <div class="progress-bar text-bg-info" role="progressbar" style="width: 100%; height: 6px;"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pasien Lama -->
        <div class="col-lg-3 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div>
                            <p class="card-subtitle text-uppercase text-warning">Pasien Lama</p>
                            <h3 class="fs-6">
                                {{-- Ganti dengan variabel dinamis jika ada, contoh: --}}
                                0
                            </h3>
                            <small class="text-muted">
                                {{-- Perubahan persentase dibanding bulan kemarin --}}
                                <span class="me-1">
                                    <span class="text-warning">+0%</span>
                                </span>
                                dibanding bulan kemarin
                            </small>
                        </div>
                        <div class="ms-auto">
                            <span class="text-warning display-6">
                                <i class="ti ti-user-check"></i>
                            </span>
                        </div>
                    </div>
                    <div class="progress text-bg-light">
                        <div class="progress-bar text-bg-warning" role="progressbar" style="width: 100%; height: 6px;"
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Donut Pie Chart</h4>
                    <div id="chart-pie-donut"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card mb-2">
                <div class="p-2 d-flex align-items-stretch h-100">
                    <div class="row w-100">
                        <div class="col-4 col-md-3 d-flex align-items-center pe-0">
                            <img src="../assets/images/products/s1.jpg" class="rounded img-fluid" />
                        </div>
                        <div class="col-8 col-md-9 d-flex align-items-center ps-2">
                            <div>
                                <a href="javascript:void(0)" class="card-title link-primary fw-semibold text-dark">
                                    50% sell on wrist watch
                                </a>
                                <p class="card-subtitle mt-1">
                                    By Daniel Jubile
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-2">
                <div class="p-2 d-flex align-items-stretch h-100">
                    <div class="row w-100">
                        <div class="col-4 col-md-3 d-flex align-items-center pe-0">
                            <img src="../assets/images/products/s1.jpg" class="rounded img-fluid" />
                        </div>
                        <div class="col-8 col-md-9 d-flex align-items-center ps-2">
                            <div>
                                <a href="javascript:void(0)" class="card-title link-primary fw-semibold text-dark">
                                    50% sell on wrist watch
                                </a>
                                <p class="card-subtitle mt-1">
                                    By Daniel Jubile
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="datatables">
            <!-- start Zero Configuration -->
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Data Transaksi Pasien</h4>
                    <div class="table-responsive">
                        <table id="zero_config" class="table table-striped table-bordered text-nowrap align-middle">
                            <thead>
                                <!-- start row -->
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Age</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                                <!-- end row -->
                            </thead>
                            <tbody>
                                <!-- start row -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-6">
                                            <img src="../assets/images/profile/user-4.jpg" width="45"
                                                class="rounded-circle" />
                                            <h6 class="mb-0"> Tiger Nixon</h6>
                                        </div>

                                    </td>
                                    <td>System Architect</td>
                                    <td>Edinburgh</td>
                                    <td>61</td>
                                    <td>2011/04/25</td>
                                    <td>$320,800</td>
                                </tr>
                                <!-- end row -->
                                <!-- start row -->
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-6">
                                            <img src="../assets/images/profile/user-2.jpg" width="45"
                                                class="rounded-circle" />
                                            <h6 class="mb-0"> Garrett Winters</h6>
                                        </div>
                                    </td>
                                    <td>Accountant</td>
                                    <td>Tokyo</td>
                                    <td>63</td>
                                    <td>2011/07/25</td>
                                    <td>$170,750</td>
                                </tr>
                                <!-- end row -->
                                <!-- start row -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
