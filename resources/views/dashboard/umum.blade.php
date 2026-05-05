@extends('layouts.app')

@section('content')
    <div class="container-fluid pt-3">
        <!-- Jquery diperlukan -->
        <form id="filterForm" method="GET" action="{{ route('dashboard.kirim-pencarian') }}">
            <div class="card p-3 mb-3">
                <div class="row align-items-end">
                    <div class="col-md-2 mb-2">
                        <label class="fw-bold text-uppercase small mb-1">Periode Tanggal</label>
                        <input type="date" id="Tanggal" name="tanggal" data-provider="flatpickr" data-date-format="Y-m-d"
                            class="form-control form-control-sm" value="{{ request('tanggal', date('Y-m-d')) }}">

                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="fw-bold text-uppercase small mb-1">Shift</label>
                        <select class="form-control" id="choices-single-no-sorting" name="shift" data-choices
                            data-choices-sorting-false>
                            <option value="">Semua Shift</option>
                            @foreach ($shift as $s)
                                <option value="{{ $s->id }}" {{ request('shift') == $s->id ? 'selected' : '' }}>
                                    {{ $s->Nama }} ({{ $s->JamMulai }} - {{ $s->JamSelesai }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="fw-bold text-uppercase small mb-1">Resepsionis</label>
                        <select class="form-control" id="choices-single-no-sorting" name="kasir" data-choices
                            data-choices-sorting-false>
                            <option value="">Semua Resepsionis</option>
                            @foreach ($kasir as $r)
                                <option value="{{ $r->id }}" {{ request('kasir') == $r->id ? 'selected' : '' }}>
                                    {{ $r->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="fw-bold text-uppercase small mb-1">Perawat</label>
                        <select class="form-control" id="choices-single-no-sorting" name="perawat" data-choices
                            data-choices-sorting-false>
                            <option value="">Semua Perawat</option>
                            @foreach ($perawat as $p)
                                <option value="{{ $p->id }}" {{ request('perawat') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <button type="submit" class="btn btn-success btn-sm me-2" id="btnFilter">
                            <i class="bi bi-funnel"></i> Tampilkan Filter
                        </button>
                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                    </div>
                </div>
            </div>

        </form>

        <div id="dashboard-cards">
            <div class="row g-3 mb-3">
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 position-relative">
                            <span
                                class="position-absolute top-0 end-0 translate-middle badge rounded-circle bg-success fs-6"
                                style="right: 20px; top: 20px; z-index:2;">
                                1
                            </span>
                            <div class="d-flex align-items-start mb-2">
                                <div class="bg-success bg-opacity-10 rounded p-2 d-flex align-items-center justify-content-center"
                                    style="width:48px;height:48px;">
                                    <i class="bi bi-bar-chart-line fs-2 text-success"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark lh-1 fs-6 mb-0">Jumlah Shift</div>
                                    <div class="text-muted small">Total biaya klinik 1 shift<br>mencapai minimal</div>
                                </div>
                            </div>

                            <div class="my-2">
                                <div class="fw-bold display-6 text-success mb-0">Rp 6.000.000</div>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-2 mt-3 mb-2 py-1 px-0 text-center w-100"
                                style="font-size: 1.5rem;">
                                <span class="fw-bold text-success">2</span> <span
                                    class="fs-6 ms-1 text-success">Shift</span>
                            </div>
                            <div class="mt-3">
                                <div class="small fw-semibold text-dark mb-1">Resepsionis Bertugas</div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person fs-5 text-secondary me-2"></i>
                                    <span class="fw-medium text-muted">Rina Amelia</span>
                                </div>
                                <div class="small fw-semibold text-dark mb-1 mt-2">Perawat Bertugas</div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person fs-5 text-secondary me-2"></i>
                                    <span class="fw-medium text-muted">Ns. Aisyah Putri</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 position-relative">
                            <span
                                class="position-absolute top-0 end-0 translate-middle badge rounded-circle bg-success fs-6"
                                style="right: 20px; top: 20px; z-index:2;">
                                1
                            </span>
                            <div class="d-flex align-items-start mb-2">
                                <div class="bg-success bg-opacity-10 rounded p-2 d-flex align-items-center justify-content-center"
                                    style="width:48px;height:48px;">
                                    <i class="bi bi-bar-chart-line fs-2 text-success"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark lh-1 fs-6 mb-0">Total Jumlah</div>
                                    <div class="text-muted small">Pasien Lama dalam 1 shift</div>
                                </div>
                            </div>
                            <div class="my-2">
                                <div class="fw-bold display-6 text-success mb-0">8 Pasien</div>
                            </div>
                            {{-- <div class="bg-success bg-opacity-10 rounded-2 mt-3 mb-2 py-1 px-0 text-center w-100"
                                style="font-size: 1.5rem;">
                                <span class="fw-bold text-success">2</span> <span
                                    class="fs-6 ms-1 text-success">Shift</span>
                            </div> --}}
                            <hr>
                            <div class="mt-3">
                                <div class="small fw-semibold text-dark mb-1">Resepsionis Bertugas</div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person fs-5 text-secondary me-2"></i>
                                    <span class="fw-medium text-muted">Rina Amelia</span>
                                </div>
                                <div class="small fw-semibold text-dark mb-1 mt-2">Perawat Bertugas</div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person fs-5 text-secondary me-2"></i>
                                    <span class="fw-medium text-muted">Ns. Aisyah Putri</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 position-relative">
                            <span
                                class="position-absolute top-0 end-0 translate-middle badge rounded-circle bg-success fs-6"
                                style="right: 20px; top: 20px; z-index:2;">
                                1
                            </span>
                            <div class="d-flex align-items-start mb-2">
                                <div class="bg-success bg-opacity-10 rounded p-2 d-flex align-items-center justify-content-center"
                                    style="width:48px;height:48px;">
                                    <i class="bi bi-bar-chart-line fs-2 text-success"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark lh-1 fs-6 mb-0">Total Pasien</div>
                                    <div class="text-muted small">Billing Lebih dari Rp. 1.000.000</div>
                                </div>
                            </div>
                            <div class="my-2">
                                <div class="fw-bold display-6 text-success mb-0">Rp 6.000.000</div>
                            </div>
                            <div class="bg-success bg-opacity-10 rounded-2 mt-3 mb-2 py-1 px-0 text-center w-100"
                                style="font-size: 1.5rem;">
                                <span class="fw-bold text-success">2</span> <span
                                    class="fs-6 ms-1 text-success">Shift</span>
                            </div>
                            <div class="mt-3">
                                <div class="small fw-semibold text-dark mb-1">Resepsionis Bertugas</div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person fs-5 text-secondary me-2"></i>
                                    <span class="fw-medium text-muted">Rina Amelia</span>
                                </div>
                                <div class="small fw-semibold text-dark mb-1 mt-2">Perawat Bertugas</div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person fs-5 text-secondary me-2"></i>
                                    <span class="fw-medium text-muted">Ns. Aisyah Putri</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-lg-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 position-relative">
                            <span
                                class="position-absolute top-0 end-0 translate-middle badge rounded-circle bg-warning fs-6"
                                style="right: 20px; top: 20px; z-index:2;">
                                4
                            </span>
                            <div class="d-flex align-items-start mb-2">
                                <div class="bg-warning bg-opacity-10 rounded p-2 d-flex align-items-center justify-content-center"
                                    style="width:48px;height:48px;">
                                    <i class="bi bi-person-plus fs-2 text-warning"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark lh-1 fs-6 mb-0">Total Pasien Baru</div>
                                </div>
                            </div>
                            <div class="my-2">
                                <span class="fw-bold display-6 text-warning mb-0" style="font-size:2.5rem;">12</span>
                                <span class="fw-bold text-warning" style="font-size:1.2rem;">Pasien</span>
                            </div>
                            <div class="mt-3">
                                <div class="small fw-semibold text-dark mb-1">Perawat Bertugas</div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person fs-5 text-secondary me-2"></i>
                                    <span class="fw-medium text-muted">Ns. Aisyah Putri</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-lg-6 mb-3">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body p-3 position-relative">
                            <span
                                class="position-absolute top-0 end-0 translate-middle badge rounded-circle bg-danger bg-opacity-25 text-danger fs-6"
                                style="right: 20px; top: 20px; z-index:2;">
                                5
                            </span>
                            <div class="d-flex align-items-start mb-2">
                                <div class="bg-danger bg-opacity-10 rounded p-2 d-flex align-items-center justify-content-center"
                                    style="width:48px;height:48px;">
                                    <i class="bi bi-emoji-smile fs-2 text-danger"></i>
                                </div>
                                <div class="ms-3">
                                    <div class="fw-bold text-dark lh-1 fs-6 mb-0">Total Pasien yang<br>Melakukan Operasi OD
                                    </div>
                                </div>
                            </div>
                            <div class="my-2">
                                <span class="fw-bold display-6 text-danger mb-0" style="font-size:2.5rem;">2</span>
                                <span class="fw-bold text-danger" style="font-size:1.2rem;">Pasien</span>
                            </div>
                            <div class="mt-3">
                                <div class="small fw-semibold text-dark mb-1">Perawat Bertugas</div>
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person fs-5 text-secondary me-2"></i>
                                    <span class="fw-medium text-muted">Ns. Aisyah Putri</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="alert alert-info d-flex align-items-center gap-2" role="alert">
                <i data-lucide="octagon-alert" class="fs-xl"></i>
                <strong>Data di atas adalah <u>akumulasi berdasarkan filter yang dipilih untuk periode dan shift
                        aktif</u>.</strong>
            </div>


        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#filterForm').on('submit', function(e) {
                    e.preventDefault();
                    var $form = $(this);
                    var url = $form.attr('action');
                    var data = $form.serialize();

                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: data,
                        beforeSend: function() {
                            $("#btnFilter").prop("disabled", true).html(
                                '<span class="spinner-border spinner-border-sm"></span> Loading...'
                            );
                        },
                        success: function(response) {
                            console.log(123);

                        },
                        error: function(xhr) {
                            alert('Terjadi kesalahan. Silakan coba lagi.');
                        },
                        complete: function() {
                            $("#btnFilter").prop("disabled", false).html(
                                '<i class="bi bi-funnel"></i> Tampilkan Filter');
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
