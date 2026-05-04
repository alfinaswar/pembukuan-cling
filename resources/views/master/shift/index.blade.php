@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0">Master Shift</h4>
            </div>
            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Master</a></li>
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Shift Kerja</a></li>
                    <li class="breadcrumb-item active">Data Shift Kerja</li>
                </ol>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-header justify-content-between">
                        <h4 class="card-title">Data Shift Kerja</h4>
                        <a href="{{ route('MasterShift.create') }}" class="btn btn-primary btn-sm">Tambah Shift</a>
                    </div>
                    <div class="card-body">
                        <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0"
                            id="shiftTable">
                            <thead class="thead-sm text-uppercase fs-xxs">
                                <tr>
                                    <th width="5%" style="text-align:center;">#</th>
                                    <th width="40%">Nama</th>
                                    <th width="20%">Jam Mulai</th>
                                    <th width="20%">Jam Selesai</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Data akan dimuat melalui DataTables --}}
                            </tbody>
                        </table>
                    </div>
                    <!-- end card-body-->
                </div>
                <!-- end card-->
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
            $(document).ready(function() {
                $('body').on('click', '.btn-delete', function() {
                    var id = $(this).data('id');
                    Swal.fire({
                        title: 'Hapus Data?',
                        text: "Apakah Anda yakin ingin menghapus shift ini?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route('MasterShift.destroy', ':id') }}'.replace(':id',
                                    id),
                                type: 'DELETE',
                                data: {
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    if (response.status === 200) {
                                        Swal.fire('Dihapus!', response.message, 'success');
                                        $('#shiftTable').DataTable().ajax.reload();
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

                $('#shiftTable').DataTable({
                    responsive: true,
                    serverSide: true,
                    processing: true,
                    bDestroy: true,
                    ajax: {
                        url: "{{ route('MasterShift.index') }}",
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
                            data: 'Nama',
                            name: 'Nama'
                        },
                        {
                            data: 'JamMulai',
                            name: 'JamMulai'
                        },
                        {
                            data: 'JamSelesai',
                            name: 'JamSelesai'
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
