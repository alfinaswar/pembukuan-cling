@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Title Header -->
    <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
        <div class="flex-grow-1">
            <h4 class="page-main-title m-0 fw-semibold">
                <i class="ti ti-device-heart-monitor me-2 text-primary"></i>Master Dental Unit
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
                <li class="breadcrumb-item active" aria-current="page">Dental Unit</li>
            </ol>
        </nav>
    </div>

    <!-- Notif sukses via redirect back (tetap tampil jika JS nonaktif / fallback) -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlertBox">
            <i class="fa fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    <!-- Content Card -->
    <div class="row">
        <div class="col-12">
            <div class="datatables">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-list me-2"></i>Data Dental Unit
                        </h4>
                        <button type="button"
                            class="btn btn-primary btn-sm d-flex align-items-center gap-1"
                            data-bs-toggle="modal"
                            data-bs-target="#modalTambahDentalUnit"
                            id="btnTambahDentalUnit">
                            <i class="ti ti-plus"></i> Tambah Dental Unit
                        </button>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- DataTable -->
                        <div class="table-responsive">
                            <table data-tables="basic" class="table table-striped dt-responsive align-middle mb-0"
                                id="dentalUnitTable" width="100%">
                                <thead class="thead-sm text-uppercase fs-xxs">
                                    <tr>
                                        <th style="width:40px;" class="text-center">#</th>
                                        <th>Nama Dental Unit</th>
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

<!-- Modal Bootstrap Tambah Dental Unit -->
<div class="modal fade" id="modalTambahDentalUnit" tabindex="-1" aria-labelledby="modalTambahDentalUnitLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formTambahDentalUnit" method="POST" action="{{ route('DentalUnit.store') }}">
        @csrf
        <input type="hidden" name="KodeCabang" value="{{ $id }}">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTambahDentalUnitLabel"><i class="ti ti-plus"></i> Tambah Dental Unit</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3 text-start">
            <label for="namaDentalUnit" class="form-label">Nama Dental Unit <span class="text-danger">*</span></label>
            <input type="text" id="namaDentalUnit" name="Nama" class="form-control @error('Nama') is-invalid @enderror" required maxlength="255" placeholder="Nama Dental Unit" value="{{ old('Nama') }}">
            @error('Nama')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3 text-start">
            <label for="ketDentalUnit" class="form-label">Keterangan</label>
            <input type="text" id="ketDentalUnit" name="Keterangan" class="form-control @error('Keterangan') is-invalid @enderror" maxlength="255" placeholder="Keterangan" value="{{ old('Keterangan') }}">
            @error('Keterangan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<!-- SweetAlert2 CDN (if not already included in layout) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // SweetAlert2 Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
        });

        // Show success toast from session if alert box exists, then hide box
        var successAlertBox = document.getElementById('successAlertBox');
        if (successAlertBox) {
            Toast.fire({
                icon: 'success',
                title: successAlertBox.textContent.trim()
            });
            setTimeout(function() {
                var alertInstance = bootstrap.Alert.getOrCreateInstance(successAlertBox);
                alertInstance.close();
            }, 300);
        }

        // DataTable init
        var dentalUnitTable = $('#dentalUnitTable').DataTable({
            responsive: true,
            serverSide: true,
            processing: true,
            destroy: true,
            ajax: {
                url: "{{ route('DentalUnit.create', ['id' => $id]) }}",
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
                targets: [0, 3]
            }],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'Nama', name: 'Nama' },
                { data: 'Keterangan', name: 'Keterangan' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });

        // Delete Handler, sesuai response DentalUnitController@destroy
        $('body').on('click', '.btn-delete', function() {
            const id = $(this).data('id');
            const nama = $(this).data('nama');

            Swal.fire({
                title: 'Hapus Data?',
                html: `Anda akan menghapus data:<br><strong class="text-primary">${nama ? nama : 'Dental Unit'}</strong><br>Tindakan ini tidak dapat dibatalkan!`,
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
                        url: "{{ route('DentalUnit.destroy', ':id') }}".replace(':id', id),
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
                                dentalUnitTable.ajax.reload(null, false);
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message || 'Terjadi kesalahan saat menghapus data';
                            Swal.fire('Gagal!', message, 'error');
                        }
                    });
                }
            });
        });

        // Autofocus on modal open
        var modalTambahDentalUnit = document.getElementById('modalTambahDentalUnit');
        if (modalTambahDentalUnit) {
            modalTambahDentalUnit.addEventListener('shown.bs.modal', function () {
                document.getElementById('namaDentalUnit').focus();
            });
        }
    });
</script>
@endpush
