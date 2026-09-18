@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-history me-2 text-primary"></i>Riwayat Mutasi Stok
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('Stok.index') }}" class="text-decoration-none text-reset">Stok Barang</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Riwayat Mutasi</li>
                </ol>
            </nav>
        </div>

        <!-- Info Card (Muncul jika ada filter dari URL) -->
        <div id="infoCard" class="card shadow-sm border-0 mb-4 bg-primary bg-opacity-10 border border-primary border-opacity-25" style="display: none;">
            <div class="card-body py-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar bg-primary text-white rounded-circle p-2">
                        <i class="ti ti-info-circle fs-4"></i>
                    </span>
                    <div>
                        <h6 class="mb-0 fw-bold text-primary">Sedang melihat riwayat untuk:</h6>
                        <p class="mb-0 text-muted small" id="infoText">-</p>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="resetUrlFilter()">
                    <i class="ti ti-x me-1"></i> Tutup
                </button>
            </div>
        </div>

        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3">
                        <h5 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Log Transaksi Stok</h5>
                    </div>
                    <div class="card-body">
                        <!-- Filter Section -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-semibold">Barang</label>
                                <select id="filter_barang" class="form-select form-select-sm select2-filter select2">
                                    <option value="">Semua Barang</option>
                                    @foreach($barangs as $b)
                                        <option value="{{ $b->id }}">{{ $b->KodeBarang }} - {{ $b->NamaBarang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small text-muted fw-semibold">Cabang / Klinik</label>
                                <select id="filter_klinik" class="form-select form-select-sm">
                                    <option value="">Semua Cabang</option>
                                    @foreach($kliniks as $k)
                                        <option value="{{ $k->Kode }}">{{ $k->Nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted fw-semibold">Jenis Mutasi</label>
                                <select id="filter_jenis" class="form-select form-select-sm">
                                    <option value="">Semua Jenis</option>
                                    <option value="masuk">Barang Masuk</option>
                                    <option value="penyesuaian">Penyesuaian</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted fw-semibold">Tanggal Mulai</label>
                                <input type="date" id="filter_tgl_mulai" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small text-muted fw-semibold">Tanggal Akhir</label>
                                <input type="date" id="filter_tgl_akhir" class="form-control form-control-sm">
                            </div>

                            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                                <button id="btnResetFilter" class="btn btn-outline-secondary btn-sm">
                                    <i class="ti ti-refresh me-1"></i> Reset
                                </button>
                                <button id="btnApplyFilter" class="btn btn-primary btn-sm">
                                    <i class="ti ti-filter me-1"></i> Terapkan Filter
                                </button>
                            </div>
                        </div>

                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table class="table table-hover dt-responsive align-middle mb-0" id="riwayatTable" width="100%">
                                <thead class="thead-sm text-uppercase fs-xxs bg-light">
                                    <tr>
                                        <th style="width:40px;" class="text-center">#</th>
                                        <th>Tanggal & Waktu</th>
                                        <th>Barang</th>
                                        <th>Cabang</th>
                                        <th class="text-center">Jenis</th>
                                        <th class="text-center">Jumlah</th>
                                        <th class="text-center">Perubahan Stok</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
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


            const urlParams = new URLSearchParams(window.location.search);
            const barangId = urlParams.get('barang_id');
            const klinik = urlParams.get('klinik');

            if (barangId) {
                $('#filter_barang').val(barangId).trigger('change');
            }
            if (klinik) {
                $('#filter_klinik').val(klinik);
            }

            // Tampilkan Info Card jika ada filter dari URL
            if (barangId || klinik) {
                $('#infoCard').show();
                let infoText = [];
                if (barangId) {
                    const selectedText = $('#filter_barang option:selected').text();
                    infoText.push('<strong>Barang:</strong> ' + selectedText);
                }
                if (klinik) {
                    const selectedText = $('#filter_klinik option:selected').text();
                    infoText.push('<strong>Cabang:</strong> ' + selectedText);
                }
                $('#infoText').html(infoText.join(' &nbsp;|&nbsp; '));
            }

            // Fungsi untuk menghapus query string dari URL
            window.resetUrlFilter = function() {
                window.history.pushState({}, document.title, window.location.pathname);
                $('#infoCard').hide();
                $('#filter_barang').val('').trigger('change');
                $('#filter_klinik').val('');
                $('#riwayatTable').DataTable().ajax.reload();
            }

            // DataTables Config
            const table = $('#riwayatTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                ajax: {
                    url: "{{ route('Stok.riwayat') }}",
                    type: 'GET',
                    data: function(d) {
                        d.barang_id = $('#filter_barang').val();
                        d.klinik = $('#filter_klinik').val();
                        d.jenis_mutasi = $('#filter_jenis').val();
                        d.tanggal_mulai = $('#filter_tgl_mulai').val();
                        d.tanggal_akhir = $('#filter_tgl_akhir').val();
                    }
                },
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [
                    { className: 'text-center', targets: [0, 4, 5, 6] }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'Tanggal', name: 'created_at' },
                    { data: 'NamaBarang', name: 'barang.NamaBarang' },
                    { data: 'KodeKlinik', name: 'KodeKlinik' },
                    { data: 'JenisMutasi', name: 'JenisMutasi' },
                    { data: 'Jumlah', name: 'Jumlah' },
                    { data: 'PerubahanStok', name: 'StokSebelum' },
                    { data: 'Keterangan', name: 'Keterangan' },
                    { data: 'UserCreate', name: 'UserCreate' }
                ],
                order: [[1, 'desc']] // Default sort by tanggal terbaru
            });

            // Event Listeners untuk Filter
            $('#btnApplyFilter').on('click', function() {
                table.ajax.reload();
            });

            $('#btnResetFilter').on('click', function() {
                $('#filter_barang').val('').trigger('change');
                $('#filter_klinik').val('');
                $('#filter_jenis').val('');
                $('#filter_tgl_mulai').val('');
                $('#filter_tgl_akhir').val('');
                table.ajax.reload();
            });
        });
    </script>
@endpush
