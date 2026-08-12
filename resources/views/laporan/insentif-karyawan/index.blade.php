@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-coin me-2 text-primary"></i>Laporan Insentif Karyawan
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)" class="text-decoration-none text-reset">Laporan</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Insentif Karyawan</li>
                </ol>
            </nav>
        </div>

        <!-- Content Card -->
        <div class="row">
            <div class="col-12">
                <div class="datatables">
                    <div class="card shadow-sm border-0">
                        <!-- Card Header -->
                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                            <h4 class="card-title mb-0 fw-semibold">
                                <i class="ti ti-list me-2"></i>Data Insentif
                            </h4>
                            <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1" id="btnResetFilter">
                                <i class="ti ti-refresh"></i> Reset Filter
                            </button>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- FILTER FORM (Tetap sama) -->
                            <form id="filterForm" class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <label class="form-label fs-xxs text-uppercase fw-semibold text-muted">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-xxs text-uppercase fw-semibold text-muted">Tanggal Selesai</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-xxs text-uppercase fw-semibold text-muted">Karyawan</label>
                                    <select name="user_id" id="user_id" class="form-select form-select-sm">
                                        <option value="">Semua Karyawan</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-xxs text-uppercase fw-semibold text-muted">Shift</label>
                                    <select name="shift" id="shift" class="form-select form-select-sm">
                                        <option value="">Semua Shift</option>
                                        @foreach($shifts as $s)
                                            <option value="{{ $s->id }}">{{ $s->Nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fs-xxs text-uppercase fw-semibold text-muted">Cabang</label>
                                    <select name="kode_cabang" id="kode_cabang" class="form-select form-select-sm">
                                        <option value="">Semua Cabang</option>
                                        @foreach($cabangs as $c)
                                            <option value="{{ $c }}">{{ $c }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" id="btnApplyFilter" class="btn btn-primary btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
                                        <i class="ti ti-search"></i> Terapkan Filter
                                    </button>
                                </div>
                            </form>

                            <!-- SUMMARY CARDS (Tetap sama) -->
                            <div class="row g-3 mb-4" id="summaryCards">
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm bg-soft-success h-100">
                                        <div class="card-body d-flex align-items-center gap-3">
                                            <div class="avatar avatar-md bg-success bg-opacity-10 text-success rounded-circle flex-shrink-0">
                                                <i class="ti ti-coin fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="fs-xxs text-uppercase text-muted fw-semibold mb-1">Total Insentif</p>
                                                <h4 class="mb-0 fw-bold text-success" id="summaryTotalNominal">Rp 0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm bg-soft-primary h-100">
                                        <div class="card-body d-flex align-items-center gap-3">
                                            <div class="avatar avatar-md bg-primary bg-opacity-10 text-primary rounded-circle flex-shrink-0">
                                                <i class="ti ti-file-certificate fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="fs-xxs text-uppercase text-muted fw-semibold mb-1">Total Transaksi</p>
                                                <h4 class="mb-0 fw-bold text-primary" id="summaryTotalRecords">0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card border-0 shadow-sm bg-soft-warning h-100">
                                        <div class="card-body d-flex align-items-center gap-3">
                                            <div class="avatar avatar-md bg-warning bg-opacity-10 text-warning rounded-circle flex-shrink-0">
                                                <i class="ti ti-chart-bar fs-4"></i>
                                            </div>
                                            <div>
                                                <p class="fs-xxs text-uppercase text-muted fw-semibold mb-1">Rata-rata / Transaksi</p>
                                                <h4 class="mb-0 fw-bold text-warning" id="summaryRataRata">Rp 0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0"
                                    id="insentifTable" width="100%">
                                    <thead class="thead-sm text-uppercase fs-xxs">
                                        <tr>
                                            <th style="width:40px;" class="text-center">#</th>
                                            <th>Tanggal</th>
                                            <th>Kode Transaksi</th> <!-- 🔥 TAMBAHAN -->
                                            <th>Nama Karyawan</th>
                                            <th>Role</th>
                                            <th class="text-center">Shift</th>
                                            <th>Cabang</th>
                                            <th>Jenis Rule</th>
                                            <th class="text-end">Nominal</th>
                                            <th style="width:110px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <!-- 🔥 FOOTER TOTAL (colspan disesuaikan jadi 8) -->
                                    <tfoot class="bg-light fw-semibold">
                                        <tr>
                                            <th colspan="8" class="text-end pe-3">TOTAL (Filter Aktif):</th>
                                            <th class="text-end text-success" id="footerTotalNominal">Rp 0</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🔥 MODAL EDIT DATA INSENTIF - hanya Shift yang bisa diedit -->
    <div class="modal fade" id="modalEditInsentif" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-semibold">
                        <i class="ti ti-edit me-2"></i>Edit Shift Insentif
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditInsentif">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="_method" value="PUT">
                        <input type="hidden" id="edit_id" name="id">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Karyawan</label>
                            <input type="text" class="form-control bg-light" id="edit_nama" readonly>
                        </div>

                        <div class="mb-3">
                            <label for="edit_shift" class="form-label fw-semibold">Shift <span class="text-danger">*</span></label>
                            <select class="form-select" name="shift" id="edit_shift" required>
                                <option value="">Pilih Shift</option>
                                @foreach($shifts as $s)
                                    <option value="{{ $s->id }}">{{ $s->Nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .bg-soft-success { background-color: #e8f7ee !important; }
        .bg-soft-primary { background-color: #e7f1ff !important; }
        .bg-soft-warning { background-color: #fff8e1 !important; }
        .avatar { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; }
        #insentifTable tfoot th { font-size: 0.95rem; padding: 0.75rem; border-top: 2px solid #dee2e6; }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function formatRupiah(angka) {
            return 'Rp ' + Number(angka || 0).toLocaleString('id-ID');
        }

        $(document).ready(function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            const table = $('#insentifTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                ajax: {
                    url: "{{ route('laporan-insentif.index') }}",
                    type: 'GET',
                    data: function (d) {
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.user_id = $('#user_id').val();
                        d.shift = $('#shift').val();
                        d.kode_cabang = $('#kode_cabang').val();
                    }
                },
                language: {
                    processing: '<i class="ti ti-loader fa-spin fa-2x text-primary"></i> Memuat data...',
                    paginate: { next: '<i class="ti ti-chevron-right"></i>', previous: '<i class="ti ti-chevron-left"></i>' },
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [
                    { className: 'text-center', targets: [0, 5, 9] }, // Updated targets karena ada kolom baru
                    { className: 'text-end', targets: [8] },
                    {
                        targets: 3, // Nama Karyawan
                        render: function(data) { return data ? `<span class="fw-semibold">${data}</span>` : '-'; }
                    },
                    {
                        targets: 8, // Nominal
                        render: function(data) { return data ? `<span class="fw-bold text-success">${data}</span>` : '-'; }
                    }
                ],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'Tanggal', name: 'getTransaksi.Tanggal' },
                    { data: 'KodeTransaksi', name: 'getTransaksi.KodeTransaksi' }, // 🔥 KOLOM BARU
                    { data: 'UserId', name: 'getUser.name' },
                    { data: 'Role', name: 'Role', orderable: false },
                    { data: 'Shift', name: 'Shift' },
                    { data: 'KodeCabang', name: 'KodeCabang' },
                    { data: 'JenisRule', name: 'JenisRule' },
                    { data: 'Nominal', name: 'Nominal', orderable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ],
                drawCallback: function(settings) {
                    const api = this.api();
                    const json = api.ajax.json();
                    if (json) {
                        $('#summaryTotalNominal').text(formatRupiah(json.totalNominal));
                        $('#summaryTotalRecords').text(json.totalRecords || 0);
                        $('#summaryRataRata').text(formatRupiah(json.rataRata));
                        $('#footerTotalNominal').text(formatRupiah(json.totalNominal));
                    }
                }
            });

            // 🔥 EVENT: Buka Modal Edit (HANYA SHIFT BISA EDIT)
            $('body').on('click', '.btn-edit', function() {
                const id = $(this).data('id');
                const shift = $(this).data('shift');
                const nama = $(this).data('nama');
                // Set form value: Nama readonly, shift (select), id (hidden)
                $('#edit_id').val(id);
                $('#edit_nama').val(nama);
                $('#edit_shift').val(shift);
                const modal = new bootstrap.Modal(document.getElementById('modalEditInsentif'));
                modal.show();
            });

            // 🔥 EVENT: Submit Form Edit via AJAX (Khusus Shift)
            $('#formEditInsentif').on('submit', function(e) {
                e.preventDefault();
                const id = $('#edit_id').val();
                const formData = $(this).serialize();

                Swal.fire({ title: 'Menyimpan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

                $.ajax({
                    url: "{{ route('laporan-insentif.update', ':id') }}".replace(':id', id),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });

                            // Tutup modal
                            const modalEl = document.getElementById('modalEditInsentif');
                            const modal = bootstrap.Modal.getInstance(modalEl);
                            modal.hide();

                            // Reload tabel
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menyimpan data';
                        Swal.fire('Gagal!', message, 'error');
                    }
                });
            });

            // Event Listener Filter & Delete (Tetap sama seperti sebelumnya)
            $('#btnApplyFilter').on('click', function() { table.ajax.reload(null, false); });
            $('#btnResetFilter').on('click', function() { $('#filterForm')[0].reset(); table.ajax.reload(null, false); });
            $('#filterForm').on('keypress', function(e) { if (e.which === 13) { e.preventDefault(); table.ajax.reload(null, false); } });

            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama') || 'Data ini';
                Swal.fire({
                    title: 'Hapus Data?', html: `Anda akan menghapus data insentif:<br><strong class="text-primary">${nama}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('laporan-insentif.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE', data: { _token: '{{ csrf_token() }}' },
                            beforeSend: function() { Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() }); },
                            success: function(response) {
                                if (response.success || response.status === 200) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message || 'Data berhasil dihapus', timer: 2000, showConfirmButton: false });
                                    table.ajax.reload(null, false);
                                } else { Swal.fire('Gagal!', response.message || 'Terjadi kesalahan', 'error'); }
                            },
                            error: function(xhr) { Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data', 'error'); }
                        });
                    }
                });
            });
        });
    </script>
@endpush
