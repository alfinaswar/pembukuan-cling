@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="datatables">
            <div class="card">
                <div class="card-header bg-teal-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-history me-2"></i>
                        Riwayat Kunjungan Pasien
                    </h5>
                </div>
                <div class="card-body">

                    {{-- 🔹 SEARCH NAMA PASIEN --}}
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted">Cari Nama Pasien</label>
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="ti ti-search text-muted"></i>
                                </span>
                                <input type="text" id="search_nama" class="form-control"
                                    placeholder="Ketik nama pasien...">
                                <button id="btnSearch" class="btn btn-primary">
                                    <i class="ti ti-search me-1"></i> Cari
                                </button>
                                <button id="btnReset" class="btn btn-outline-secondary">
                                    <i class="ti ti-refresh me-1"></i> Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="transaksiKasirTable" class="table table-striped table-bordered align-middle"
                            style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Tanggal</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Pasien</th>
                                    <th>Terakhir Berkunjung</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Layanan</th>
                                    <th>Total Bayar</th>
                                    <th>Petugas</th>
                                    <th>Shift</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if (Session::get('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ Session::get('success') }}',
                iconColor: '#4BCC1F',
                confirmButtonText: 'Oke',
                confirmButtonColor: '#4BCC1F',
            });
        </script>
    @endif

    <script>
        $(function() {
            // Fungsi reload DataTable dengan parameter search
            function reloadTable() {
                $('#transaksiKasirTable').DataTable().ajax.reload();
            }

            // Event klik tombol Search
            $('#btnSearch').on('click', function() {
                reloadTable();
            });

            // Event Enter di input search
            $('#search_nama').on('keypress', function(e) {
                if (e.which === 13) {
                    reloadTable();
                }
            });

            // Event klik tombol Reset
            $('#btnReset').on('click', function() {
                $('#search_nama').val('');
                reloadTable();
            });

            // TOMBOL DELETE
            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Apakah Anda yakin ingin menghapus transaksi ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ route('Transaksi.destroy', ':id') }}'.replace(':id',
                                id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    $('#transaksiKasirTable').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON?.message ??
                                    'Terjadi kesalahan saat menghapus.', 'error');
                            }
                        });
                    }
                });
            });

            // DATATABLES
            const table = $('#transaksiKasirTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                bDestroy: true,
                ajax: {
                    url: "{{ route('Transaksi.index-kunjungan') }}",
                    data: function(d) {
                        d.search_nama = $('#search_nama').val();
                    }
                },
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Memuat...</span>',
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Kode',
                        name: 'Kode'
                    },
                    {
                        data: 'Tanggal',
                        name: 'Tanggal'
                    },
                    {
                        data: 'NamaPasien',
                        name: 'NamaPasien'
                    },
                    {
                        data: 'JenisPasien',
                        name: 'JenisPasien'
                    },
                    {
                        data: 'TerakhirBerkunjung',
                        name: 'TerakhirBerkunjung',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'MetodePembayaran',
                        name: 'MetodePembayaran',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Layanan',
                        name: 'Layanan',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'TotalBayar',
                        name: 'TotalBayar'
                    },
                    {
                        data: 'Petugas',
                        name: 'Petugas',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'Shift',
                        name: 'Shift'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });
    </script>
@endpush
