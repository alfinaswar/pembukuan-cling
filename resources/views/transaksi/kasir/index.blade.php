@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="datatables">
            <div class="card">
                <div class="card-header bg-teal-primary text-white d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-semibold">
                        <i class="ti ti-currency-dollar me-2"></i>
                        Kasir - Daftar Transaksi
                    </h5>
                    <a href="{{ route('Transaksi.create') }}" class="btn btn-primary">
                        <i class="ti ti-plus"></i> Transaksi Baru
                    </a>
                </div>
                <div class="card-body">

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
                                    <th>Metode Pembayaran</th>
                                    <th>Layanan</th>
                                    <th>Total Bayar</th>
                                    <th>Dokter</th>
                                    <th>Perawat</th>
                                    <th>Resepsionis</th>
                                    <th>Shift</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan diisi oleh DataTables -->
                            </tbody>
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
            $('#transaksiKasirTable').DataTable({
                responsive: true,
                serverSide: true,
                processing: true,
                bDestroy: true,
                ajax: {
                    url: "{{ route('Transaksi.index') }}",
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
                        data: 'MetodePembayaran',
                        name: 'MetodePembayaran'
                    },
                    {
                        data: 'Layanan',
                        name: 'Layanan'
                    },
                    {
                        data: 'TotalBayar',
                        name: 'TotalBayar'
                    },

                    {
                        data: 'Dokter',
                        name: 'Dokter'
                    },
                    {
                        data: 'Perawat',
                        name: 'Perawat'
                    },
                    {
                        data: 'Resepsionis',
                        name: 'Resepsionis'
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
