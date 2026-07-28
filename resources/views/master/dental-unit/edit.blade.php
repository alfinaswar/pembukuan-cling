@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Title Header -->
    <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
        <div class="flex-grow-1">
            <h4 class="page-main-title m-0 fw-semibold">
                <i class="ti ti-device-heart-monitor me-2 text-primary"></i>Edit Dental Unit
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
                <li class="breadcrumb-item">
                    <a href="{{ route('DentalUnit.create', ['id' => $dentalUnit->KodeCabang]) }}" class="text-decoration-none text-reset">Dental Unit</a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">Edit</li>
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

    <!-- Edit Dental Unit Card -->
    <div class="row">
        <div class="col-lg-12 col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0 fw-semibold">
                        <i class="ti ti-edit me-2"></i>Edit Dental Unit
                    </h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('DentalUnit.update', $dentalUnit->id) }}">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="KodeCabang" value="{{ $dentalUnit->KodeCabang }}">
                        <div class="mb-3 text-start">
                            <label for="namaDentalUnit" class="form-label">Nama Dental Unit <span class="text-danger">*</span></label>
                            <input type="text" id="namaDentalUnit" name="Nama" class="form-control @error('Nama') is-invalid @enderror" required maxlength="255" placeholder="Nama Dental Unit" value="{{ old('Nama', $dentalUnit->Nama) }}">
                            @error('Nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3 text-start">
                            <label for="ketDentalUnit" class="form-label">Keterangan</label>
                            <input type="text" id="ketDentalUnit" name="Keterangan" class="form-control @error('Keterangan') is-invalid @enderror" maxlength="255" placeholder="Keterangan" value="{{ old('Keterangan', $dentalUnit->Keterangan) }}">
                            @error('Keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('DentalUnit.create', ['id' => $dentalUnit->KodeCabang]) }}" class="btn btn-secondary btn-sm">Kembali</a>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </div>
                    </form>
                </div>
            </div>
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
    });
</script>
@endpush
