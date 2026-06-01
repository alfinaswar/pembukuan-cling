@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-calendar-event me-2 text-primary"></i>Master Hari Libur
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)" class="text-decoration-none text-reset">Master</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Hari Libur</li>
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
                                <i class="ti ti-list me-2"></i>Data Hari Libur
                            </h4>
                            <a href="{{ route('MasterHariLibur.create') }}"
                                class="btn btn-primary btn-sm d-flex align-items-center gap-1">
                                <i class="ti ti-plus"></i> Tambah Hari Libur
                            </a>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- DataTable -->
                            <div class="table-responsive">
                                <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0"
                                    id="hariLiburTable" width="100%">
                                    <thead class="thead-sm text-uppercase fs-xxs">
                                        <tr>
                                            <th style="width:40px;" class="text-center">#</th>
                                            <th>Nama Hari</th>
                                            <th>Tanggal Libur</th>
                                            <th>Keterangan</th>
                                            <th style="width:90px;" class="text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Data dimuat via DataTables --}}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });

            // Show success toast from session
            @if (session('success'))
                Toast.fire({
                    icon: 'success',
                    title: '{{ session('success') }}'
                });
            @endif

            // Delete Handler
            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Data?',
                    html: `Anda akan menghapus data:<br><strong class="text-primary">${nama ? nama : 'Hari Libur'}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('MasterHariLibur.destroy', ':id') }}".replace(
                                ':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Menghapus...',
                                    allowOutsideClick: false,
                                    didOpen: () => Swal.showLoading()
                                });
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: response.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });
                                    $('#hariLiburTable').DataTable().ajax.reload(null,
                                        false);
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                const message = xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menghapus data';
                                Swal.fire('Gagal!', message, 'error');
                            }
                        });
                    }
                });
            });

            // DataTables Init
            $('#hariLiburTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                destroy: true,
                ajax: {
                    url: "{{ route('MasterHariLibur.index') }}",
                    type: 'GET'
                },
                language: {
                    processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Memuat...</span>',
                    paginate: {
                        next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                        previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
                    },
                    url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
                },
                columnDefs: [{
                    className: 'text-center',
                    targets: [0, 4]
                }],
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'NamaHari',
                        name: 'NamaHari'
                    },
                    {
                        data: 'TanggalLibur',
                        name: 'TanggalLibur'
                    },
                    {
                        data: 'Keterangan',
                        name: 'Keterangan'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
            });
        });
    </script>
@endpush
