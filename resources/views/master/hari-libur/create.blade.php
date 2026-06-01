@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <!-- Page Title Header -->
        <div class="page-title-head d-flex align-items-center flex-wrap gap-2 mb-4">
            <div class="flex-grow-1">
                <h4 class="page-main-title m-0 fw-semibold">
                    <i class="ti ti-plus me-2 text-primary"></i>Tambah Hari Libur
                </h4>
            </div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('home') }}" class="text-decoration-none text-reset">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('MasterHariLibur.index') }}" class="text-decoration-none text-reset">Hari
                            Libur</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Tambah Data</li>
                </ol>
            </nav>
        </div>

        <!-- Form Card -->
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">
                <div class="card shadow-sm border-0">
                    <!-- Card Header -->
                    <div class="card-header bg-white py-3 border-bottom">
                        <h5 class="card-title mb-0 fw-semibold">
                            <i class="ti ti-file-text me-2"></i>Form Data Hari Libur
                        </h5>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body p-4">
                        <form action="{{ route('MasterHariLibur.store') }}" method="POST" id="formMasterHariLibur">
                            @csrf

                            <!-- Info Box -->
                            <div class="alert alert-light border mb-4 d-flex align-items-center gap-2" style="color: #000;">
                                <i class="ti ti-info-circle text-primary"></i>
                                <small class="mb-0" style="color: #000;">Kolom dengan tanda <span
                                        class="text-danger">*</span> wajib diisi.
                                </small>
                            </div>

                            <!-- Nama Hari -->
                            <div class="mb-4">
                                <label for="NamaHari" class="form-label fw-semibold mb-2">
                                    Nama Hari <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-calendar-event text-muted"></i>
                                    </span>
                                    <input type="text" id="NamaHari" name="NamaHari"
                                        class="form-control @error('NamaHari') is-invalid @enderror"
                                        value="{{ old('NamaHari') }}" required
                                        placeholder="Contoh: Idul Fitri, Tahun Baru, dsb" autocomplete="off">
                                </div>
                                @error('NamaHari')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Tanggal Libur -->
                            <div class="mb-4">
                                <label for="TanggalLibur" class="form-label fw-semibold mb-2">
                                    Tanggal Libur <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-calendar text-muted"></i>
                                    </span>
                                    <input type="date" id="TanggalLibur" name="TanggalLibur"
                                        class="form-control @error('TanggalLibur') is-invalid @enderror"
                                        value="{{ old('TanggalLibur') }}" required autocomplete="off">
                                </div>
                                @error('TanggalLibur')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Keterangan -->
                            <div class="mb-4">
                                <label for="Keterangan" class="form-label fw-semibold mb-2">
                                    Keterangan
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="ti ti-info-square-rounded text-muted"></i>
                                    </span>
                                    <input type="text" id="Keterangan" name="Keterangan"
                                        class="form-control @error('Keterangan') is-invalid @enderror"
                                        value="{{ old('Keterangan') }}" placeholder="Opsional: Keterangan tambahan"
                                        autocomplete="off">
                                </div>
                                @error('Keterangan')
                                    <div class="invalid-feedback d-block mt-1">
                                        <i class="ti ti-alert-circle me-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Divider -->
                            <hr class="my-4 text-muted opacity-25">

                            <!-- Actions -->
                            <div class="d-flex justify-content-end gap-2 pt-2">
                                <a href="{{ route('MasterHariLibur.index') }}" class="btn btn-light px-4">
                                    <i class="ti ti-arrow-left me-1"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ti ti-device-floppy me-1"></i>Simpan Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Back Link (Mobile Friendly) -->
                <div class="text-center mt-3 d-lg-none">
                    <a href="{{ route('MasterHariLibur.index') }}" class="text-muted small text-decoration-none">
                        <i class="ti ti-arrow-back-up me-1"></i>Kembali ke daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        /* Fokus input yang lebih halus */
        .form-control:focus,
        .input-group:focus-within {
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.15);
            border-color: #2196f3;
        }

        /* Input group icon styling */
        .input-group-text {
            border-right: none;
            min-width: 45px;
            justify-content: center;
        }

        .input-group .form-control {
            border-left: none;
        }

        /* Smooth transition untuk feedback */
        .invalid-feedback {
            animation: fadeIn 0.2s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus ke field pertama
            document.getElementById('NamaHari')?.focus();
        });
    </script>
@endpush
