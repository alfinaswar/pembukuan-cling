@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-stack me-2 text-primary"></i>Master Stok Barang
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)" class="text-decoration-none text-reset">Master</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Stok Barang</li>
                </ol>
            </nav>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-primary">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <span class="avatar bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                    <i class="ti ti-package fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small mb-1">Total Jenis Barang</div>
                                <div id="sum_total_item" class="fw-bold fs-4">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-warning">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <span class="avatar bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                    <i class="ti ti-alert-triangle fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small mb-1">Stok Menipis (< 5)</div>
                                <div id="sum_low_stock" class="fw-bold fs-4 text-warning">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-danger">
                    <div class="card-body py-3">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <span class="avatar bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                                    <i class="ti ti-alert-circle fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="text-muted small mb-1">Stok Habis (0)</div>
                                <div id="sum_zero_stock" class="fw-bold fs-4 text-danger">0</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Stok</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row g-3 mb-4">
                            @if (auth()->user()->hasRole('Superadmin'))
                                <div class="col-md-4">
                                    <label class="form-label small text-muted fw-semibold">Filter Cabang / Klinik</label>
                                    <select id="filter_klinik" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($kliniks as $k)
                                            <option value="{{ $k->Kode }}">{{ $k->Nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <label class="form-label small text-muted fw-semibold">Filter Kategori</label>
                                <select id="filter_kategori" class="form-select form-select-sm">
                                    <option value="">Semua Kategori</option>
                                    @foreach($kategoris as $k)
                                        <option value="{{ $k->id }}">{{ $k->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="btnResetFilter" class="btn btn-outline-secondary btn-sm w-100">
                                    <i class="ti ti-refresh me-1"></i> Reset Filter
                                </button>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table class="table table-striped dt-responsive align-middle mb-0" id="stokTable" width="100%">
                                <thead class="thead-sm text-uppercase fs-xxs">
                                    <tr>
                                        <th style="width:40px;" class="text-center">#</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Stok Saat Ini</th>
                                        <th>Cabang</th>
                                        <th style="width:140px;" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // ==========================================
            // DATATABLES CONFIGURATION
            // ==========================================
            const table = $('#stokTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                ajax: {
                    url: "{{ route('Stok.index') }}",
                    type: 'GET',
                    data: function(d) {
                        d.klinik = $('#filter_klinik').val();
                        d.kategori = $('#filter_kategori').val();
                    }
                },
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Memuat...</span>',
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [
                    { className: 'text-center', targets: [0, 4, 6] }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'KodeBarang', name: 'barang.KodeBarang' },
                    { data: 'NamaBarang', name: 'barang.NamaBarang' },
                    { data: 'Kategori', name: 'barang.kategori.Nama' },
                    { data: 'StokAkhir', name: 'StokAkhir', orderable: true },
                    { data: 'KodeKlinik', name: 'KodeKlinik' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                drawCallback: function(settings) {
                    // Update Summary Cards setiap kali tabel di-draw (filter/pagination)
                    const json = settings.json;
                    if (json && json.summary) {
                        const s = json.summary;
                        $('#sum_total_item').text(new Intl.NumberFormat('id-ID').format(s.total_item));
                        $('#sum_low_stock').text(new Intl.NumberFormat('id-ID').format(s.low_stock));
                        $('#sum_zero_stock').text(new Intl.NumberFormat('id-ID').format(s.zero_stock));
                    }
                }
            });

            // ==========================================
            // FILTER HANDLERS
            // ==========================================
            $('#filter_klinik, #filter_kategori').on('change', function() {
                table.ajax.reload();
            });

            $('#btnResetFilter').on('click', function() {
                $('#filter_klinik').val('');
                $('#filter_kategori').val('');
                table.ajax.reload();
            });
        });
    </script>
@endpush
