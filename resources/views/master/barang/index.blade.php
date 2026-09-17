@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold"><i class="ti ti-package me-2 text-primary"></i>Master Barang</h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)" class="text-decoration-none text-reset">Master</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Barang</li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold"><i class="ti ti-list me-2"></i>Data Barang</h4>
                        <a href="{{ route('Barang.create') }}" class="btn btn-primary btn-sm d-flex align-items-center gap-1 px-4">
                            <i class="ti ti-plus"></i> Tambah Barang
                        </a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped dt-responsive align-middle mb-0" id="barangTable" width="100%">
                                <thead class="thead-sm text-uppercase fs-xxs">
                                    <tr>
                                        <th style="width:40px;" class="text-center">#</th>
                                        <th>Kode Barang</th>
                                        <th>Nama Barang</th>
                                        <th>Kategori</th>
                                        <th style="width:120px;" class="text-center">Aksi</th>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });

            @if (session('success'))
                Toast.fire({ icon: 'success', title: '{{ session('success') }}' });
            @endif

            $('body').on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                const nama = $(this).data('nama');

                Swal.fire({
                    title: 'Hapus Data?',
                    html: `Anda akan menghapus data:<br><strong class="text-primary">${nama}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
                    icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus!', cancelButtonText: 'Batal', reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('Barang.destroy', ':id') }}".replace(':id', id),
                            type: 'DELETE',
                            data: { _token: '{{ csrf_token() }}' },
                            beforeSend: () => Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() }),
                            success: function(response) {
                                if (response.status === 200) {
                                    Swal.fire({ icon: 'success', title: 'Berhasil!', text: response.message, timer: 2000, showConfirmButton: false });
                                    $('#barangTable').DataTable().ajax.reload(null, false);
                                } else {
                                    Swal.fire('Gagal!', response.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire('Gagal!', xhr.responseJSON?.message || 'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });

            $('#barangTable').DataTable({
                responsive: true, serverSide: true, processing: true, destroy: true,
                ajax: { url: "{{ route('Barang.index') }}", type: 'GET' },
                language: { url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json" },
                columnDefs: [{ className: 'text-center', targets: [0, 4] }],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'KodeBarang', name: 'KodeBarang' },
                    { data: 'NamaBarang', name: 'NamaBarang' },
                    { data: 'KategoriNama', name: 'kategori.Nama' },
                    { data: 'action', name: 'action', orderable: false, searchable: false }
                ]
            });
        });
    </script>
@endpush
