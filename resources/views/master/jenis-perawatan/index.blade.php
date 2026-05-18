@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <!-- Title -->
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">Master Jenis Perawatan</h4>
            </div>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">
                            Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)" class="text-decoration-none text-reset">
                            Master
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        Jenis Perawatan
                    </li>
                </ol>
            </nav>
        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="datatables">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center bg-white">
                            <h4 class="card-title mb-0">Data Jenis Perawatan</h4>
                            <a href="{{ route('JenisPerawatan.create') }}" class="btn btn-primary btn-sm ms-auto">Tambah
                                Jenis Perawatan</a>
                        </div>

                        <div class="card-body">
                            <table id="zero_config" class="table table-striped table-bordered text-nowrap align-middle">
                                <thead class="thead-sm text-uppercase fs-xxs">
                                    <tr>
                                        <th style="width:5%; text-align:center;">#</th>
                                        <th>Kode</th>
                                        <th>Nama</th>
                                        <th>Tarif</th>


                                        <th style="width:90px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
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
        $(document).ready(function() {

            // ✅ Delete Handler
            $('body').on('click', '.btn-delete', function() {
                var id = $(this).data('id');
                // Ambil nama dari sel tabel berdasarkan baris button
                var name = $(this).closest('tr').find('td:eq(2)').text() || 'data terpilih';

                Swal.fire({
                    title: 'Hapus Data?',
                    text: "Apakah Anda yakin ingin menghapus: " + name + "?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('JenisPerawatan.destroy', ':id') }}".replace(
                                ':id', id),
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire('Dihapus!', response.message, 'success');
                                    $('#zero_config').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat menghapus.', 'error');
                            }
                        });
                    }
                });
            });

            $('#zero_config').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                bDestroy: true,
                ajax: {
                    url: "{{ route('JenisPerawatan.index') }}",
                    type: 'GET'
                },

                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: 'Kode',
                        name: 'Kode'
                    },
                    {
                        data: 'Nama',
                        name: 'Nama'
                    },
                    {
                        data: 'Tarif',
                        name: 'Tarif',
                        render: function(data) {
                            // ✅ Format Rupiah tanpa mengubah style table
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(data);
                        }
                    },


                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    }
                ],
                order: [
                    [1, 'asc']
                ]
            });
        });
    </script>
@endpush
